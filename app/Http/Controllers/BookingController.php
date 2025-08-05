<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPassenger;
use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        if (!Auth::guard('customer')->check()) {
        return redirect()->route('customer.login'); // Halaman login customer
    }
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
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
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



        return view('homepage.public.select-seat', compact('trip', 'seats', 'pax', 'passengerNames'));
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



        return view('homepage.public.checkout', compact('trip', 'selectedSeats', 'pax', 'seats', 'passengerNames'));
    }
    public function process(Request $request)
{
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
        'pax' => 'required|integer|min:1',
        'selected_seats' => 'required|array',
        'passenger_names' => 'required|array',
    ]);

    DB::beginTransaction();

    try {
        $schedule = Schedule::findOrFail($request->schedule_id);

        // Simpan data customer (jika login bisa pakai Auth::id())
        $customer = Customer::firstOrCreate(
            ['email' => session('customer.customer_email')],
            [
                'name'  => session('customer.customer_name'),
                'phone' => session('customer.customer_phone'),
            ]
        );

        $price = $schedule->route->price;
        $total = $price * $request->pax;

        $order = Order::create([
            'customer_id'    => $customer->id,
            'schedule_id'    => $schedule->id,
            'order_code'     => strtoupper(Str::random(10)),
            'seat_quantity'  => $request->pax,
            'total_price'    => $total,
            'payment_status' => 'belum',
            'order_status'   => 'menunggu',
        ]);

        foreach ($request->selected_seats as $index => $seat) {
            OrderPassenger::create([
                'order_id'    => $order->id,
                'name'        => $request->passenger_names[$index] ?? '-',
                'seat_number' => $seat,
            ]);
        }

        DB::commit();

        return redirect()->route('checkout.payment', ['order' => $order->id]);

    } catch (\Exception $e) {
        DB::rollBack();

        return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
    }
}

    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

        // Ubah status ke batal
        $order->update(['order_status' => 'batal']);

        // Hapus data penumpangnya
        $order->passengers()->delete();

        return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
