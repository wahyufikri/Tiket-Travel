<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('origin', 'like', '%' . $search . '%')
                ->orWhere('destination', 'like', '%' . $search . '%');
        }

        $allowedSortBy = ['origin', 'destination', 'price'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'created_at';
        $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDirection);

        $routes = $query->paginate(10)->appends([
            'search' => $request->get('search'),
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]);

        return view('dashboard.routes.index', compact('routes', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        return view('dashboard.routes.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'origin' => 'required|max:60',
        'destination' => 'required|max:60',
        'price' => 'required|numeric',
        'duration_minutes' => 'required|integer|min:1', // tambahkan ini
    ]);

    Route::create([
        'origin' => $validated['origin'],
        'destination' => $validated['destination'],
        'price' => $validated['price'],
        'duration_minutes' => $validated['duration_minutes'], // tambahkan ini juga
    ]);

    return redirect()->route('rute.index')->with('success', 'Rute berhasil ditambahkan.');
}


    public function edit($id)
    {
        $routes = Route::findOrFail($id);
        return view('dashboard.routes.edit', compact('routes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
             'origin' => 'required|max:60',
            'destination' => 'required|max:60',
            'price' => 'required|numeric',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $route = Route::findOrFail($id);
        $route->origin = $validated['origin'];
        $route->destination = $validated['destination'];
        $route->price = $validated['price'];
        $route->duration_minutes = $validated['duration_minutes']; // tambahkan ini
        $route->save();

        return redirect()->route('rute.index')->with('success', 'Route berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $route = Route::findOrFail($id);
        $route->delete();

        return redirect()->route('rute.index')->with('success', 'Route berhasil dihapus.');
    }
}
