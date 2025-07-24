<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function book(Request $request)
    {
        $schedule = Schedule::with('route')->findOrFail($request->schedule_id);
        $pax = $request->pax ?? 1;

        return view('homepage.public.booking', [
            'trip' => $schedule,
            'pax' => $pax,
        ]);
    }

    public function selectSeat(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'passenger_names' => 'required|array',
        ]);

        // Simpan data ke session sementara
        session([
            'customer' => [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_passenger' => $request->has('is_passenger'),
                'guest_checkout' => $request->has('guest_checkout'),
                'passenger_names' => $request->passenger_names,
            ],
            'schedule_id' => $request->schedule_id,
            'pax' => count($request->passenger_names),
        ]);

        return redirect()->route('public.seatSelection', ['schedule_id' => $request->schedule_id]);
    }
    public function showSeatSelection($schedule_id)
    {
        $trip = Schedule::with(['route', 'vehicle'])->findOrFail($schedule_id);
        $seats = Seat::where('schedule_id', $schedule_id)->get();

        // Ambil pax dari session, fallback ke 1 kalau tidak ada
        $pax = session('pax', 1);
        $passengerNames = session('customer.passenger_names', []);


        return view('homepage.public.select-seat', compact('trip', 'seats', 'pax','passengerNames'));
    }

    public function checkout(Request $request)
    {

        $trip = Schedule::with('route', 'vehicle')->findOrFail($request->schedule_id);
        $selectedSeats = $request->selected_seats;
        $pax = $request->pax;
        $passengerNames = $request->input('passenger_names', []);

        // Optional: ambil detail seat yg dipilih dari DB
        $seats = Seat::whereIn('seat_number', $selectedSeats)
            ->where('schedule_id', $trip->id)
            ->get();

        return view('homepage.public.checkout', compact('trip', 'selectedSeats', 'pax', 'seats','passengerNames'));
    }
    public function processPayment(Request $request)
{
    // Validasi atau simpan order ke DB
    // Misal:
    // - Simpan ke tabel bookings
    // - Kurangi kursi tersedia
    // - Kirim email/invoice
    // - Redirect ke halaman sukses

    return redirect('/')->with('success', 'Pemesanan berhasil dikonfirmasi!');
}

}
