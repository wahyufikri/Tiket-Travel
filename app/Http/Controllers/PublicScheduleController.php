<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Schedule;
use App\Models\Stop;
use Illuminate\Http\Request;

class PublicScheduleController extends Controller
{
    // Untuk halaman utama
    public function home(Request $request)
{
    // Ambil semua route_stops yang sudah urut berdasarkan sequence
    $routeStops = Stop::with('route')->orderBy('route_id')->orderBy('stop_order')->get();

    // Ambil data asal yang unik berdasarkan kombinasi nama dan route
    $origins = $routeStops->unique(fn ($item) => $item->route_id . '-' . $item->stop_name);
    $destinations = $routeStops->unique(fn ($item) => $item->route_id . '-' . $item->stop_name);

    // Kosongkan schedules awal, nanti akan terisi saat pencarian
    $schedules = collect();

    return view('homepage.public.home', compact('routeStops', 'origins', 'schedules','destinations'));
}



    // Untuk hasil pencarian
    public function search(Request $request)
{
    $departure = $request->input('depart');
    $arrival = $request->input('arrival');
    $date = $request->input('date');
    $pax = $request->input('pax');

    // Dropdown data
    $origins = Stop::select('stop_name')->distinct()->get();
    $destinations = Stop::select('stop_name')->distinct()->get();

    $schedules = collect();

    if ($departure && $arrival && $date && $pax) {
        // Cari semua stop sesuai nama
        $originStops = Stop::where('stop_name', $departure)->get();
        $destinationStops = Stop::where('stop_name', $arrival)->get();

        // Filter: harus di rute yg sama dan urutan arrival > departure
        $matchingRoutes = [];

        foreach ($originStops as $originStop) {
            foreach ($destinationStops as $destinationStop) {
                if (
                    $originStop->route_id === $destinationStop->route_id &&
                    $originStop->stop_order < $destinationStop->stop_order
                ) {
                    $matchingRoutes[] = $originStop->route_id;
                }
            }
        }

        if (!empty($matchingRoutes)) {
            $schedules = Schedule::with(['route.stops', 'vehicle', 'driver'])

                ->whereIn('route_id', $matchingRoutes)
                ->whereDate('departure_date', $date)
                ->where('available_seats', '>=', 1)
                ->where('status', 'active')
                ->get();

            $minSeats = $schedules->min('available_seats');

            if ($minSeats !== null && $pax > $minSeats) {
                return redirect()->back()->withInput()->withErrors([
                    'pax' => "Kursi yang tersedia hanya $minSeats.",
                ]);
            }
        }
    }

    $stops = Stop::with('route')->orderBy('route_id')->orderBy('stop_order')->get();


    return view('homepage.public.schedule', compact(
        'schedules',
        'departure',
        'arrival',
        'date',
        'pax',
        'origins',
        'destinations',
        'stops'
    ));
}



}
