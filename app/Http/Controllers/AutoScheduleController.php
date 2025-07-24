<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AutoSchedule;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Driver;

class AutoScheduleController extends Controller
{
   public function index(Request $request)
{
    $query = AutoSchedule::with(['route', 'vehicle', 'driver']);

    // Pencarian berdasarkan nama rute atau nama driver (bisa ditambah sesuai kebutuhan)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('route', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->orWhereHas('driver', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }

    // Sorting (misalnya berdasarkan jam keberangkatan, status, atau lainnya)
    $allowedSortBy = ['departure_time', 'status', 'weekday'];
    $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'created_at';
    $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';

    $query->orderBy($sortBy, $sortDirection);

    // Pagination dan preserve query string
    $autoSchedules = $query->paginate(10)->appends([
        'search' => $request->get('search'),
        'sort_by' => $sortBy,
        'sort_direction' => $sortDirection,
    ]);

    return view('dashboard.autoschedules.index', compact('autoSchedules', 'sortBy', 'sortDirection'));
}


    public function create()
    {
        return view('dashboard.autoschedules.create', [
            'routes' => Route::all(),
            'vehicles' => Vehicle::all(),
            'drivers' => Driver::all(),
        ]);
    }

    public function store(Request $request)
{
    // dd($request->all());
    $request->validate([
        'route_id' => 'required|exists:routes,id',
        'vehicle_id' => 'required|exists:vehicles,id',
        'driver_id' => 'required|exists:drivers,id',
        'weekday' => 'required|array|min:1',
        'weekday.*' => 'integer|between:0,6',
        'departure_time' => 'required|date_format:H:i',
        'status' => 'required|in:aktif,nonaktif',
    ]);

    foreach ($request->weekday as $day) {
        $day = intval($day); // <<== INI WAJIB AGAR 0 MASUK

        AutoSchedule::create([
            'route_id' => $request->route_id,
            'vehicle_id' => $request->vehicle_id,
            'driver_id' => $request->driver_id,
            'weekday' => $day,
            'departure_time' => $request->departure_time,
            'status' => $request->status,
        ]);
    }

    return redirect()->route('auto_schedule.index')->with('success', 'Auto Schedule berhasil ditambahkan.');
}



    public function edit($id)
    {
        $autoSchedule = AutoSchedule::findOrFail($id);
        return view('dashboard.autoschedules.edit', [
            'autoSchedule' => $autoSchedule,
            'routes' => Route::all(),
            'vehicles' => Vehicle::all(),
            'drivers' => Driver::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'weekday' => 'required|integer|min:0|max:6',
            'departure_time' => 'required|date_format:H:i',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $autoSchedule = AutoSchedule::findOrFail($id);
        $autoSchedule->update($request->all());

        return redirect()->route('auto_schedule.index')->with('success', 'Auto Schedule berhasil diupdate.');
    }

    public function destroy($id)
    {
        AutoSchedule::destroy($id);
        return redirect()->route('auto_schedule.index')->with('success', 'Auto Schedule dihapus.');
    }
}
