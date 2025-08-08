<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPassenger;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Stop;
use App\Models\StopPrice;
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

    // Simpan ke session
    session([
        'origin' => $request->origin,
        'destination' => $request->destination,
        'price' => $request->price,
        'pax' => $request->pax,
        'departure_date' => $request->date,
    ]);
        return view('homepage.public.booking', [
            'trip' => $schedule,
            'pax' => $pax,
            'origin' => $request->origin,
            'destination' => $request->destination,
        ]);
    }

   public function selectSeat(Request $request)
{
    if (!Auth::guard('customer')->check()) {
        return redirect()->route('customer.login');
    }

    $validated = $request->validate([
        'name' => 'required',
        'phone' => 'required',
        'email' => 'required|email',
        'passenger_names' => 'required|array',
    ]);

    $schedule = Schedule::with('route.stops')->findOrFail($request->schedule_id);

    $originStop = $schedule->route->stops->firstWhere('stop_name', session('origin'));
    $destinationStop = $schedule->route->stops->firstWhere('stop_name', session('destination'));

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
        'schedule_id' => $schedule->id,
        'pax' => count($request->passenger_names),
        'origin' => $originStop ? $originStop->stop_name : null,
        'destination' => $destinationStop ? $destinationStop->stop_name : null,
    ]);

    return redirect()->route('public.seatSelection', ['schedule_id' => $schedule->id]);
}

    public function showSeatSelection($schedule_id)
{
    $trip = Schedule::with(['route', 'vehicle'])->findOrFail($schedule_id);

    $seats = Seat::where('vehicle_id', $trip->vehicle_id)->get();

    // Ambil origin & destination dari session
    $origin = session('origin');
    $destination = session('destination');

    // Cari ID stop di database
    $originStopId = Stop::where('stop_name', $origin)->value('id');
    $destinationStopId = Stop::where('stop_name', $destination)->value('id');

    // Kursi yang sudah dibooking untuk rute yang bentrok
    // Ambil urutan stop untuk origin & destination user
$originStopOrder = Stop::where('route_id', $trip->route_id)
    ->where('stop_name', $origin) // bukan stop_id, tapi stop_name
    ->value('stop_order');

$destinationStopOrder = Stop::where('route_id', $trip->route_id)
    ->where('stop_name', $destination)
    ->value('stop_order');


// Ambil kursi yang bentrok rutenya
$bookedSeats = Booking::join('route_stops as rs_from', 'bookings.from_stop_id', '=', 'rs_from.id')
    ->join('route_stops as rs_to', 'bookings.to_stop_id', '=', 'rs_to.id')
    ->join('seats', 'bookings.seat_id', '=', 'seats.id')
    ->where('schedule_id', $schedule_id)
    ->where('rs_from.route_id', $trip->route_id)
    ->where('rs_to.route_id', $trip->route_id)
    ->where(function ($q) use ($originStopOrder, $destinationStopOrder) {
        $q->where(function ($query) use ($originStopOrder, $destinationStopOrder) {
            $query->where('rs_from.stop_order', '<', $destinationStopOrder)
                  ->where('rs_to.stop_order', '>', $originStopOrder);
        });
    })
    ->pluck('seats.seat_number')
    ->toArray();



    foreach ($seats as $seat) {
        $seat->is_booked = in_array($seat->seat_number, $bookedSeats);
    }

    $pax = session('pax', 1);
    $price = session('price');
    $departure_date = session('departure_date');
    $passengerNames = session('customer.passenger_names', []);

    return view('homepage.public.select-seat', compact(
        'trip', 'seats', 'pax', 'origin', 'destination', 'price', 'departure_date', 'passengerNames', 'bookedSeats'
    ));
}




    public function checkout(Request $request)
{


    if (!$request->has(['schedule_id', 'selected_seats'])) {
        return redirect()->route('public.home')->with('error', 'Data tidak lengkap.');
    }

    $trip = Schedule::with('route.stops', 'vehicle')->findOrFail($request->schedule_id);
    $selectedSeats = $request->selected_seats;
    $pax = $request->pax;
    $passengerNames = $request->input('passenger_names', []);

    $origin = $request->origin ?? session('origin');
    $destination = $request->destination ?? session('destination');



    $stops = $trip->route->stops;

    $originStop = $stops->firstWhere('stop_name', $origin);
    $destinationStop = $stops->firstWhere('stop_name', $destination);

    // ✅ FIX: Hitung harga dari stop, bukan dari request
    $price = session('price');



    return view('homepage.public.checkout', compact(
        'trip',
        'selectedSeats',
        'pax',
        'passengerNames',
        'price',
        'origin',
        'destination'
    ));
}


public function process(Request $request)
{
    // 1. Validasi request
    $request->validate([
        'schedule_id'      => 'required|exists:schedules,id',
        'pax'              => 'required|integer|min:1',
        'selected_seats'   => 'required|array',
        'passenger_names'  => 'required|array',
    ]);

    // 2. Ambil data schedule + route stops
    $schedule = Schedule::with('route.stops')->findOrFail($request->schedule_id);

    // 3. Ambil data customer dari session
    $customer = Customer::firstOrCreate(
        ['email' => session('customer.customer_email')],
        [
            'name'  => session('customer.customer_name'),
            'phone' => session('customer.customer_phone'),
        ]
    );

    // 4. Ambil origin & destination
    $origin = session('origin');
    $destination = session('destination');

    $originStop = $schedule->route->stops->firstWhere('stop_name', $origin);
    $destinationStop = $schedule->route->stops->firstWhere('stop_name', $destination);

    if (!$originStop || !$destinationStop) {
        return back()->with('error', 'Data origin atau destination tidak valid.');
    }

    // 5. Tentukan harga
    $customPrice = StopPrice::where('route_id', $schedule->route_id)
        ->where('from_stop_id', $originStop->id)
        ->where('to_stop_id', $destinationStop->id)
        ->value('price');

    if ($customPrice === null) {
        $customPrice = StopPrice::where('route_id', $schedule->route_id)
            ->where('from_stop_id', $destinationStop->id)
            ->where('to_stop_id', $originStop->id)
            ->value('price');
    }

    $price = $customPrice ?? $schedule->route->price;
    $total = $price * $request->pax;

    // 6. Validasi semua kursi
    $seatsData = [];
    foreach ($request->selected_seats as $index => $seatNumber) {
        $seat = Seat::where('vehicle_id', $schedule->vehicle_id)
            ->where('seat_number', $seatNumber)
            ->first();

        if (!$seat) {
            return back()->with('error', "Kursi {$seatNumber} tidak ditemukan.");
        }

        // Cek apakah kursi ini sudah dibooking di segmen yang overlap
        $alreadyBooked = Booking::join('route_stops as rs_from', 'bookings.from_stop_id', '=', 'rs_from.id')
            ->join('route_stops as rs_to', 'bookings.to_stop_id', '=', 'rs_to.id')
            ->where('bookings.schedule_id', $schedule->id)
            ->where('bookings.seat_id', $seat->id)
            ->where('rs_from.route_id', $schedule->route_id)
            ->where('rs_to.route_id', $schedule->route_id)
            ->where(function ($query) use ($originStop, $destinationStop) {
                // Overlap: booking lama mulai sebelum tujuan baru DAN berakhir setelah asal baru
                $query->where('rs_from.stop_order', '<', $destinationStop->stop_order)
                      ->where('rs_to.stop_order', '>', $originStop->stop_order);
            })
            ->exists();

        if ($alreadyBooked) {
            return back()->with('error', "Kursi {$seatNumber} sudah dibooking di segmen perjalanan ini.");
        }

        $seatsData[] = [
            'seat' => $seat,
            'name' => $request->passenger_names[$index] ?? '-',
        ];
    }

    // 7. Simpan order & booking dalam transaksi
    DB::beginTransaction();
    try {
        // Buat order
        $order = Order::create([
            'customer_id'    => $customer->id,
            'schedule_id'    => $schedule->id,
            'order_code'     => strtoupper(Str::random(10)),
            'seat_quantity'  => $request->pax,
            'total_price'    => $total,
            'payment_status' => 'belum',
            'order_status'   => 'menunggu',
        ]);

        if (!$order || !$order->id) {
            throw new \Exception("Gagal membuat order. Periksa model Order.");
        }

        // Simpan penumpang & booking
        foreach ($seatsData as $data) {
            OrderPassenger::create([
                'order_id'    => $order->id,
                'name'        => $data['name'],
                'seat_number' => $data['seat']->seat_number,
            ]);

            Booking::create([
                'order_id'       => $order->id,
                'schedule_id'    => $schedule->id,
                'seat_id'        => $data['seat']->id,
                'from_stop_id'   => $originStop->id,
                'to_stop_id'     => $destinationStop->id,
                'passenger_name' => $data['name'],
            ]);
        }

        // ⚠️ Tidak decrement available_seats karena per segmen
        DB::commit();

        return redirect()->route('checkout.payment', ['order' => $order->id]);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
    }
}





    public function cancelOrder($id)
    {
        $order = Order::with('passengers')->findOrFail($id);

        $order->update(['order_status' => 'batal']);

        // Ambil seat_number dan vehicle_id untuk unbook kursi
        $schedule = Schedule::find($order->schedule_id);

        foreach ($order->passengers as $passenger) {
            Seat::where('vehicle_id', $schedule->vehicle_id)
                ->where('seat_number', $passenger->seat_number)
                ->update(['is_booked' => 0]);
        }

        $order->passengers()->delete();

        return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan dan kursi dikembalikan.');
    }
}
