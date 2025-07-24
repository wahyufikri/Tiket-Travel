<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;

class PublicScheduleController extends Controller
{
    // Untuk halaman utama
    public function home()
    {
        // Ambil data unik origin & destination untuk dropdown
        $origins = Route::select('origin')->distinct()->get();
        $destinations = Route::select('destination')->distinct()->get();
        $schedules = collect();

        return view('homepage.public.home', compact('origins', 'destinations','schedules'));
    }



    // Untuk hasil pencarian
    public function search(Request $request)
    {
        $departure = $request->input('depart');
        $arrival = $request->input('arrival');
        $date = $request->input('date');
        $pax = $request->input('pax');
        $origins = Route::select('origin')->distinct()->get();
        $destinations = Route::select('destination')->distinct()->get();

        $schedules = collect();

        if ($departure && $arrival && $date && $pax) {
            $schedules = Schedule::with('route')
                ->whereHas('route', function ($query) use ($departure, $arrival) {
                    $query->where('origin', $departure)
                          ->where('destination', $arrival);
                })
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

        return view('homepage.public.schedule', compact(
            'schedules',
            'departure',
            'arrival',
            'date',
            'pax',
            'origins', 'destinations'
        ));
    }

    
}
