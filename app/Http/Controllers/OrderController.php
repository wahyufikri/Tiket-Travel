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
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer','schedule');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $allowedSortBy = ['customer','schedule', 'order_code', 'seat_quantity', 'total_price', 'payment_status','order_status'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'created_at';
        $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDirection);

        $orders = $query->paginate(10)->appends([
            'search' => $request->get('search'),
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]);

        return view('dashboard.orders.index', compact('orders', 'sortBy', 'sortDirection'));
    }

    public function create(Request $request)
{
    $customers = Customer::all();
    $stops = Stop::with('route')->get();
    $stopPrices = StopPrice::with('route', 'fromStop', 'toStop')->get();

    $schedules = Schedule::query()
        ->where('status', 'active') // filter status active
        ->when($request->origin && $request->destination, function ($query) use ($request) {
            $query->whereHas('route.stops', function ($q) use ($request) {
                $q->where('id', $request->origin);
            })->whereHas('route.stops', function ($q) use ($request) {
                $q->where('id', $request->destination);
            });
        })
        ->with('route')
        ->get();

    return view('dashboard.orders.create', compact('customers','schedules','stops','stopPrices'));
}

public function getSeats(Schedule $schedule)
{
    // Ambil kursi yang sudah dibooking di jadwal ini
    $bookedSeatIds = Booking::where('schedule_id', $schedule->id)
        ->pluck('seat_id');

    // Ambil kursi dari kendaraan yang dipakai jadwal ini, kecuali yang sudah dibooking
    $seats = Seat::where('vehicle_id', $schedule->vehicle_id)
        ->whereNotIn('id', $bookedSeatIds)
        ->get();

    return response()->json($seats);
}





    public function store(Request $request)
{
    $validated = $request->validate([
        'customer_id'      => 'required|exists:customers,id',
        'schedule_id'      => 'required|exists:schedules,id',
        'seat_quantity'    => 'required|integer|min:1',
        'payment_status'   => 'required|in:belum,lunas,gagal',
        'order_status'     => 'required|in:menunggu,proses,selesai,batal',
        'selected_seats'   => 'required|array|min:1',
        'selected_seats.*' => 'string',
        'passenger_names'  => 'required|array|min:1',
    ]);

    if (count($validated['selected_seats']) !== count($validated['passenger_names'])) {
        return back()->with('error', 'Jumlah kursi dan jumlah penumpang harus sama.');
    }

    $schedule = Schedule::with('route.stops')->findOrFail($validated['schedule_id']);

    // Ambil origin & destination otomatis
    $originStop = $schedule->route->stops->sortBy('stop_order')->first();
    $destinationStop = $schedule->route->stops->sortByDesc('stop_order')->first();

    $customPrice = StopPrice::where('route_id', $schedule->route_id)
        ->where('from_stop_id', $originStop->id)
        ->where('to_stop_id', $destinationStop->id)
        ->value('price') ?? $schedule->route->price;

    $totalPrice = $customPrice * $validated['seat_quantity'];

    DB::beginTransaction();
    try {
        $orderCode = Str::upper(Str::random(10));

        $order = Order::create([
            'customer_id'    => $validated['customer_id'],
            'schedule_id'    => $schedule->id,
            'seat_quantity'  => $validated['seat_quantity'],
            'total_price'    => $totalPrice,
            'payment_status' => $validated['payment_status'],
            'order_status'   => $validated['order_status'],
            'order_code'     => $orderCode,
        ]);

        foreach ($validated['selected_seats'] as $i => $seatNumber) {
            $seat = Seat::where('vehicle_id', $schedule->vehicle_id)
                        ->where('seat_number', $seatNumber)
                        ->firstOrFail();

            $alreadyBooked = Booking::where('schedule_id', $schedule->id)
                ->where('seat_id', $seat->id)
                ->exists();

            if ($alreadyBooked) {
                throw new \Exception("Kursi {$seatNumber} sudah dibooking.");
            }

            OrderPassenger::create([
                'order_id'    => $order->id,
                'name'        => $validated['passenger_names'][$i] ?? '-',
                'seat_number' => $seatNumber,
            ]);

            Booking::create([
                'order_id'       => $order->id,
                'schedule_id'    => $schedule->id,
                'seat_id'        => $seat->id,
                'from_stop_id'   => $originStop->id,
                'to_stop_id'     => $destinationStop->id,
                'passenger_name' => $validated['passenger_names'][$i] ?? '-',
            ]);
        }

        DB::commit();
        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan dan booking berhasil dibuat.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}




    public function show(Order $order)
{

    $trip = $order->schedule;
    $passengers = $order->passengers;
    $origin = session('origin');
    $destination = session('destination'); // asumsi relasi passengers() di model Order



    return view('homepage.public.show', [
        'order' => $order,
        'trip' => $trip,
        'selectedSeats' => $passengers->pluck('seat_number'),
        'passengerNames' => $passengers->pluck('name'),
        'pax' => $order->seat_quantity,
        'origin' => $origin,
        'destination' => $destination,
        'departure_segment' => session('departure_segment'),
        'arrival_segment' => session('arrival_segment'),
        'phone' => session('customer.customer_phone'),
        'email' => session('customer.customer_email'),
    ]);
}


public function edit($id)
{
    $order = Order::with(['passengers', 'schedule.route.stops'])->findOrFail($id);
    $customers = Customer::all();
    $schedules = Schedule::with('route.stops')->get();

    // Ambil kursi yang sudah dibooking orang lain di schedule ini
    $bookedSeats = Booking::where('schedule_id', $order->schedule_id)
        ->where('order_id', '!=', $order->id) // kecuali order ini sendiri
        ->pluck('seat_id')
        ->toArray();

    // Ambil semua kursi di kendaraan yang dipakai schedule ini
    $seats = Seat::where('vehicle_id', $order->schedule->vehicle_id)->get();

    // Ambil route stops untuk origin & destination
    $stops = $order->schedule->route->stops ?? [];

    return view('dashboard.orders.edit', compact(
        'order',
        'customers',
        'schedules',
        'seats',
        'bookedSeats',
        'stops'
    ));
}


public function update(Request $request, $id)
{
    $request->validate([
        'payment_status' => 'required|in:belum,lunas,gagal',
        'order_status'   => 'required|in:menunggu,proses,selesai,batal',
    ]);

    $order = Order::findOrFail($id);

    $order->payment_status = $request->payment_status;
    $order->order_status   = $request->order_status;

    $order->save();

    return redirect()->route('pemesanan.index')
        ->with('success', 'Status pembayaran dan status pemesanan berhasil diperbarui.');
}



public function destroy($id)
{
    DB::beginTransaction();

    try {
        // Ambil data order beserta penumpang dan jadwal
        $order = Order::with('passengers', 'schedule')->findOrFail($id);

        // Hitung jumlah kursi yang dipesan
        $seatsToRestore = $order->passengers->count();

        // Kembalikan status kursi yang sudah dipesan
        foreach ($order->passengers as $passenger) {
            Seat::where('vehicle_id', $order->schedule->vehicle_id)
                ->where('seat_number', $passenger->seat_number)
                ->update(['is_booked' => 0]);
        }

        // Tambahkan kembali available_seats
        $order->schedule->increment('available_seats', $seatsToRestore);

        // Hapus order (beserta penumpang jika pakai cascade)
        $order->delete();

        DB::commit();

        return redirect()->route('pemesanan.index')->with('success', 'Pesanan berhasil dihapus dan kursi dikembalikan.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('pemesanan.index')->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
    }
}





    public function showTicket(Order $order)
{


    $order->load('passengers');
    $origin = session('origin');
    $destination = session('destination');
    $departure_segment = session('departure_segment');
    $arrival_segment = session('arrival_segment');
    $phone = session('customer.customer_phone');
    $email = session('customer.customer_email');
    return view('homepage.public.show_ticket', compact('order', 'departure_segment', 'arrival_segment', 'phone', 'email','origin', 'destination'));
}

public function downloadTicket(Order $order)
{


    $order->load('passengers');
    $origin = session('origin');
    $destination = session('destination');
    $departure_segment = session('departure_segment');
    $arrival_segment = session('arrival_segment');
    $pdf = FacadePdf::loadView('homepage.public.ticket', compact('order','departure_segment', 'arrival_segment', 'origin', 'destination'))
        ->setPaper('A4', 'portrait');

    return $pdf->download('tiket_' . $order->id . '.pdf');
}




}
