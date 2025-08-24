<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Stop;
use App\Models\Vehicle;
use App\Services\FonnteService;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['route', 'vehicle', 'driver']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('departure_date', 'like', '%' . $search . '%')
                    ->orWhere('departure_time', 'like', '%' . $search . '%');
            });
        }

        $allowedSortBy = ['departure_date', 'departure_time', 'status', 'available_seats', 'route', 'vehicle', 'driver'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'created_at';
        $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';
        if ($sortBy === 'route') {
            $query->join('routes', 'schedules.route_id', '=', 'routes.id')
                ->orderBy('routes.origin', $sortDirection)
                ->select('schedules.*')
                ->with('route');
        } elseif ($sortBy === 'vehicle') {
            $query->join('vehicles', 'schedules.vehicle_id', '=', 'vehicles.id')
                ->orderBy('vehicles.vehicle_name', $sortDirection)
                ->select('schedules.*')
                ->with('vehicle');
        } elseif ($sortBy === 'driver') {
            $query->join('drivers', 'schedules.driver_id', '=', 'drivers.id')
                ->orderBy('drivers.name', $sortDirection)
                ->select('schedules.*')
                ->with('driver');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }



        $schedules = $query->paginate(10)->appends([
            'search' => $request->get('search'),
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]);

        return view('dashboard.schedules.index', compact('schedules', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $routes = Route::all();

        $vehicles = Vehicle::where('status', 'active')->get();
        $drivers  = Driver::where('status', 'active')->get();

        $routeStopsGrouped = Stop::all()
            ->groupBy('route_id')
            ->map(function ($stops) {
                return $stops->map(function ($stop) {
                    return [
                        'id'        => $stop->id,
                        'stop_name' => $stop->stop_name,
                    ];
                });
            });

        return view('dashboard.schedules.create', compact('routes', 'vehicles', 'drivers', 'routeStopsGrouped'));
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required',
            'vehicle_id' => 'required',
            'driver_id' => 'required',
            'departure_date' => 'required|date',
            'departure_time' => 'required|date_format:H:i',

        ]);

        // Gabungkan tanggal dan waktu keberangkatan
        $departureDateTime = Carbon::createFromFormat('Y-m-d H:i', $validated['departure_date'] . ' ' . $validated['departure_time']);

        // Validasi tidak boleh input waktu lampau
        if ($departureDateTime->lt(Carbon::now())) {
            return back()->withErrors([
                'departure_time' => 'Waktu keberangkatan tidak boleh kurang dari waktu saat ini.'
            ])->withInput();
        }

        ScheduleService::createSchedule($validated);


        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }






    public function edit($id)
    {
        $schedules = Schedule::findOrFail($id);
        $routes = Route::all();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        return view('dashboard.schedules.edit', compact('schedules', 'routes', 'vehicles', 'drivers'));
    }

   public function update(Request $request, $id, FonnteService $fonnte)
{
    $validated = $request->validate([
        'route_id' => 'required|max:60',
        'vehicle_id' => 'required|max:60',
        'driver_id' => 'required|max:60',
        'departure_date' => 'required|date',
        'departure_time' => 'required|date_format:H:i',
        'status' => 'required|in:active,completed,cancelled',
    ]);

    $schedule = Schedule::findOrFail($id);
    $route = Route::findOrFail($validated['route_id']);

    $departure = Carbon::createFromFormat('Y-m-d H:i', $validated['departure_date'].' '.$validated['departure_time']);
    $arrival = $departure->copy()->addMinutes($route->duration_minutes);

    // Update data schedule
    $schedule->route_id = $validated['route_id'];
    $schedule->vehicle_id = $validated['vehicle_id'];
    $schedule->driver_id = $validated['driver_id'];
    $schedule->departure_date = $validated['departure_date'];
    $schedule->departure_time = $validated['departure_time'];
    $schedule->arrival_time = $arrival->format('H:i');
    $schedule->status = $validated['status'];
    $schedule->save();

    // ✅ Kirim WA kalau status = cancelled
    if ($validated['status'] === 'cancelled') {
        $orders = $schedule->orders; // relasi hasMany Order

        foreach ($orders as $order) {
            $phone = $this->normalizePhone($order->customer->phone);
            $message = "Halo {$order->customer->name},
Jadwal keberangkatan Anda pada {$schedule->departure_date} jam {$schedule->departure_time} rute {$route->origin} - {$route->destination} telah DIBATALKAN.
Silakan pilih reschedule atau refund dengan menghubungi contact person AWR. Terimakasih";

            $fonnte->sendMessage($phone, $message);
        }
    }

    // Jika status = completed → update lokasi terakhir driver & kendaraan
    if ($validated['status'] === 'completed') {
        $driver = Driver::findOrFail($validated['driver_id']);
        $driver->current_location = $route->destination;
        $driver->save();

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $vehicle->current_location = $route->destination;
        $vehicle->save();
    }

    return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
}


    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }



    /**
     * Normalisasi nomor HP (0xx jadi 62xx, hapus simbol dll)
     */
    private function normalizePhone($phone)
    {
        // hapus semua karakter non-digit
        $phone = preg_replace('/\D/', '', $phone);

        // ubah 0 di depan jadi 62
        return preg_replace('/^0/', '62', $phone);
    }
}
