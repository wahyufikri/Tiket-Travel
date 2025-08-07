<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Seat;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_name', 'like', '%' . $search . '%')
                    ->orWhere('license_plate', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        $allowedSortBy = ['vehicle_name', 'license_plate', 'type', 'color', 'capacity', 'year', 'status'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'created_at';
        $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDirection);

        $vehicles = $query->paginate(10)->appends([
            'search' => $request->get('search'),
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]);

        return view('dashboard.vehicles.index', compact('vehicles', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $drivers = Driver::all()->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->name,
                'phone_number' => $d->phone_number,
            ];
        });
        return view('dashboard.vehicles.create', compact('drivers'));
    }


    public function store(Request $request)
{

    $validated = $request->validate([
        'vehicle_name' => 'required|max:60',
        'license_plate' => 'required|unique:vehicles,license_plate',
        'type' => 'required',
        'color' => 'required'   ,
        'capacity' => 'required|integer',
        'year' => 'required|digits:4',
        'status' => 'required|in:active,inactive',
        'seat_configuration' => 'nullable|regex:/^([A-Z]=\d+)(,[A-Z]=\d+)*$/',
        'drivers' => 'nullable|array',
        'drivers.*' => 'exists:drivers,id',
    ], [
        'vehicle_name.required' => 'Nama kendaraan wajib diisi.',
        'license_plate.required' => 'No Plat wajib diisi.',
        'license_plate.unique' => 'No Plat sudah terdaftar.',
        'seat_configuration.regex' => 'Format konfigurasi kursi tidak valid. Contoh: A=3,B=4,C=3',
    ]);

    // ✅ Tambahkan validasi custom di sini
    $seatConfiguration = $validated['seat_configuration'] ?? null;
    $totalSeats = 0;

    if ($seatConfiguration) {
        $rows = explode(',', $seatConfiguration);
        foreach ($rows as $row) {
            if (strpos($row, '=') !== false) {
                [$rowLabel, $count] = explode('=', $row);
                $totalSeats += (int) trim($count);
            }
        }
    }

    if ($totalSeats !== (int) $validated['capacity']) {
        return back()->withInput()->withErrors([
            'seat_configuration' => 'Jumlah total kursi harus sama dengan kapasitas kendaraan (' . $validated['capacity'] . '). Saat ini: ' . $totalSeats,
        ]);
    }

    // Baru lanjut simpan jika valid
    DB::transaction(function () use ($validated, $request, &$vehicle) {
    $vehicle = Vehicle::create([
        'vehicle_name' => $validated['vehicle_name'],
        'license_plate' => $validated['license_plate'],
        'type' => $validated['type'],
        'color' => $validated['color'],
        'capacity' => $validated['capacity'],
        'year' => $validated['year'],
        'status' => $validated['status'],
        'seat_configuration' => $validated['seat_configuration'] ?? null,
    ]);

    if ($request->has('drivers')) {
        $vehicle->drivers()->attach($validated['drivers']);
    }

    // ✅ Tambahkan logic generate seats
    if (!empty($validated['seat_configuration'])) {
        $rows = explode(',', $validated['seat_configuration']);
        foreach ($rows as $row) {
            if (strpos($row, '=') !== false) {
                [$rowLabel, $count] = explode('=', $row);
                $rowLabel = trim($rowLabel);
                $count = (int) trim($count);
                for ($i = 1; $i <= $count; $i++) {
                    Seat::create([
                        'vehicle_id' => $vehicle->id,
                        'seat_number' => $rowLabel . $i, // Contoh: A1, A2
                        'is_booked' => false,
                    ]);
                }
            }
        }
    }
});

    return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan.');
}



    public function edit($id)
    {
        $vehicles = Vehicle::findOrFail($id);
        return view('dashboard.vehicles.edit', compact('vehicles'));
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'vehicle_name' => 'required|max:60',
        'license_plate' => 'required',
        'type' => 'required',
        'color' => 'required',
        'capacity' => 'required|integer',
        'year' => 'required|digits:4',
        'status' => 'required|in:active,inactive',
        'seat_configuration' => 'nullable|regex:/^([A-Z]=\d+)(,[A-Z]=\d+)*$/',
        'current_location' => 'nullable|string|max:100',
    ], [
        'seat_configuration.regex' => 'Format konfigurasi kursi tidak valid. Contoh: A=3,B=4,C=3',
    ]);

    // ✅ Validasi apakah jumlah kursi sesuai kapasitas
    $seatConfiguration = $validated['seat_configuration'] ?? null;
    $totalSeats = 0;

    if ($seatConfiguration) {
        $rows = explode(',', $seatConfiguration);
        foreach ($rows as $row) {
            if (strpos($row, '=') !== false) {
                [$rowLabel, $count] = explode('=', $row);
                $totalSeats += (int) trim($count);
            }
        }
    }

    if ($totalSeats !== (int) $validated['capacity']) {
        return back()->withInput()->withErrors([
            'seat_configuration' => 'Jumlah total kursi harus sama dengan kapasitas kendaraan (' . $validated['capacity'] . '). Saat ini: ' . $totalSeats,
        ]);
    }

    // ✅ Update data jika valid
    $vehicle = Vehicle::findOrFail($id);
    $vehicle->vehicle_name = $validated['vehicle_name'];
    $vehicle->license_plate = $validated['license_plate'];
    $vehicle->type = $validated['type'];
    $vehicle->color = $validated['color'];
    $vehicle->capacity = $validated['capacity'];
    $vehicle->year = $validated['year'];
    $vehicle->status = $validated['status'];
    $vehicle->seat_configuration = $validated['seat_configuration'] ?? null;
    $vehicle->current_location = $validated['current_location'] ?? $vehicle->current_location;

    $vehicle->save();

    return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui.');
}


    public function destroy($id)
    {
        $user = Vehicle::findOrFail($id);
        $user->delete();

        return redirect()->route('kendaraan.index')->with('success', 'Vehicle berhasil dihapus.');
    }
}
