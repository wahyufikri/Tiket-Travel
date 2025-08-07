<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Stop;
use App\Models\StopPrice;
use Illuminate\Http\Request;

class StopPriceController extends Controller
{
    public function index()
{
    $routes = Route::all(); // Untuk kebutuhan dropdown
    $routeStops = Stop::with('route')->get(); // Untuk daftar pemberhentian
    $stopPrices = StopPrice::with(['route', 'fromStop', 'toStop'])->get(); // Untuk harga antar titik

    return view('dashboard.stops.index', compact('routes', 'routeStops', 'stopPrices'));
}

    public function create()
    {
        $routes = Route::all();
        return view('dashboard.stops.create', compact('routes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'from_stop_id' => 'required|exists:route_stops,id|different:to_stop_id',
            'to_stop_id' => 'required|exists:route_stops,id|different:from_stop_id',
            'price' => 'required|numeric|min:0',
        ]);

        StopPrice::create($request->only(['route_id', 'from_stop_id', 'to_stop_id', 'price']));

        return redirect()->route('hargapertitik.index')->with('success', 'Harga antar titik berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $stopPrice = StopPrice::findOrFail($id);
        $routes = Route::all();
        $stops = Stop::where('route_id', $stopPrice->route_id)->get();

        return view('dashboard.stops.edit', compact('stopPrice', 'routes', 'stops'));
    }

    public function update(Request $request, $id)
    {
        $stopPrice = StopPrice::findOrFail($id);

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'from_stop_id' => 'required|exists:route_stops,id|different:to_stop_id',
            'to_stop_id' => 'required|exists:route_stops,id|different:from_stop_id',
            'price' => 'required|numeric|min:0',
        ]);

        $stopPrice->update($request->only(['route_id', 'from_stop_id', 'to_stop_id', 'price']));

        return redirect()->route('hargapertitik.index')->with('success', 'Harga antar titik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $stopPrice = StopPrice::findOrFail($id);
        $stopPrice->delete();

        return redirect()->route('hargapertitik.index')->with('success', 'Harga antar titik berhasil dihapus.');
    }
}
