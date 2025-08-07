<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Stop;
use App\Models\StopPrice;
use Illuminate\Http\Request;

class StopsController extends Controller
{
    public function index()
    {
        $routeStops = Stop::with('route')->orderBy('route_id')->orderBy('stop_order')->get();
        $stopPrices = StopPrice::with('route', 'fromStop', 'toStop')->get();
$routes = Route::all();

        return view('dashboard.stops.index', compact('routeStops','stopPrices','routes'));
    }

    // Form tambah pemberhentian
    public function create()
    {
        $routes = Route::all();
        return view('dashboard.stops.create', compact('routes'));
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'route_id'   => 'required|exists:routes,id',
            'stop_order' => 'required|integer|min:1',
            'stop_name'  => 'required|string|max:100',
        ]);

        Stop::create($request->only(['route_id', 'stop_order', 'stop_name']));

        return redirect()->route('stop.index')->with('success', 'Pemberhentian berhasil ditambahkan.');
    }

    // Form edit
    public function edit(Stop $routeStop)
    {
        $routes = Route::all();
        return view('dashboard.stops.edit', compact('routeStop', 'routes'));
    }

    // Update data
    public function update(Request $request, Stop $routeStop)
    {
        $request->validate([
            'route_id'   => 'required|exists:routes,id',
            'stop_order' => 'required|integer|min:1',
            'stop_name'  => 'required|string|max:100',
        ]);

        $routeStop->update($request->only(['route_id', 'stop_order', 'stop_name']));

        return redirect()->route('stop.index')->with('success', 'Pemberhentian berhasil diperbarui.');
    }

    // Hapus data
    public function destroy(Stop $routeStop)
    {
        $routeStop->delete();
        return redirect()->route('stop.index')->with('success', 'Pemberhentian berhasil dihapus.');
    }
}
