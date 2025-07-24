<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $allowedSortBy = ['name', 'status'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'created_at';
        $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDirection);

        $drivers = $query->paginate(10)->appends([
            'search' => $request->get('search'),
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]);

        return view('dashboard.drivers.index', compact('drivers', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'active')->get();
        return view('dashboard.drivers.create',compact('vehicles'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|max:60',
        'phone_number' => 'required',
        'address' => 'required',
        'status' => 'required|in:active,inactive',
        'vehicles' => 'nullable|array', // untuk input multiple kendaraan
        'vehicles.*' => 'exists:vehicles,id', // validasi ID kendaraan
    ], [
        'name.required' => 'Nama wajib diisi.',
        'phone_number.required' => 'No Hp wajib diisi.',
        'address.required' => 'Alamat wajib diisi.',
        'phone_number.phone_number' => 'Format phone_number tidak valid.'
    ]);

    DB::transaction(function () use ($validated) {
        $driver = Driver::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['vehicles'])) {
            $driver->vehicles()->attach($validated['vehicles']);
        }
    });

    return redirect()->route('sopir.index')->with('success', 'Sopir berhasil ditambahkan.');
}

    public function edit($id)
    {
        $drivers = Driver::findOrFail($id);
        return view('dashboard.drivers.edit', compact('drivers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:60',
            'phone_number' => 'required',
            'address' => 'required',
            'status' => 'required|in:active,inactive',
            'current_location' => 'nullable|string|max:100',
        ]);

        $driver = Driver::findOrFail($id);
        $driver->name = $validated['name'];
        $driver->phone_number = $validated['phone_number'];
        $driver->address = $validated['address'];
        $driver->status = $validated['status'];
        $driver->current_location = $validated['current_location'] ?? $driver->current_location;

        $driver->save();

        return redirect()->route('sopir.index')->with('success', 'Sopir berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $user = Driver::findOrFail($id);
        $user->delete();

        return redirect()->route('sopir.index')->with('success', 'Driver berhasil dihapus.');
    }
}
