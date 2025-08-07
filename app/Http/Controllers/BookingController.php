<?php

namespace App\Http\Controllers;

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

    $pax = session('pax', 1);
    $origin = session('origin');
    $destination = session('destination');
    $price = session('price');
    $departure_date = session('departure_date');
    $passengerNames = session('customer.passenger_names', []);

    // Bisa dd() untuk memastikan nilainya sekarang ada
    // dd($origin, $destination, $price);

    return view('homepage.public.select-seat', compact('trip', 'seats', 'pax', 'origin', 'destination', 'price', 'departure_date','passengerNames'));
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
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'pax' => 'required|integer|min:1',
            'selected_seats' => 'required|array',
            'passenger_names' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $schedule = Schedule::with('route.stops')->findOrFail($request->schedule_id);

            $customer = Customer::firstOrCreate(
                ['email' => session('customer.customer_email')],
                [
                    'name'  => session('customer.customer_name'),
                    'phone' => session('customer.customer_phone'),
                ]
            );

            // Hitung harga dari StopPrice
            $origin = session('origin');
            $destination = session('destination');

            $originStop = $schedule->route->stops->firstWhere('stop_name', $origin);
            $destinationStop = $schedule->route->stops->firstWhere('stop_name', $destination);

            $customPrice = null;
            if ($originStop && $destinationStop) {
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
            }

            $price = $customPrice ?? $schedule->route->price;
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

                Seat::where('vehicle_id', $schedule->vehicle_id)
                    ->where('seat_number', $seat)
                    ->update(['is_booked' => 1]);
            }
            $schedule->decrement('available_seats', count($request->selected_seats));

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
