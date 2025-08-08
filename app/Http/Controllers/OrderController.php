<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\Seat;
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

    public function create()
    {
        $customers = Customer::all();
        $schedules = Schedule::all(); // Assuming you have a Schedule model

        return view('dashboard.orders.create',compact('customers','schedules'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'schedule_id' => 'required|exists:schedules,id',
        'seat_quantity' => 'required|integer|min:1',
        'total_price' => 'required|numeric|min:0',
        'payment_status' => 'required|in:belum,lunas,gagal',
        'order_status' => 'required|in:menunggu,proses,selesai,batal',
    ]);

    // Generate kode order acak 10 huruf kapital
    $orderCode = Str::upper(Str::random(10)); // contoh: GKAHSTLZVW

    // Simpan ke database
    Order::create([
        'customer_id' => $validated['customer_id'],
        'schedule_id' => $validated['schedule_id'],
        'seat_quantity' => $validated['seat_quantity'],
        'total_price' => $validated['total_price'],
        'payment_status' => $validated['payment_status'],
        'order_status' => $validated['order_status'],
        'order_code' => $orderCode,
    ]);

    return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil dibuat.');
}

    public function show(Order $order)
{
    
    $trip = $order->schedule;
    $passengers = $order->passengers; // asumsi relasi passengers() di model Order



    return view('homepage.public.show', [
        'order' => $order,
        'trip' => $trip,
        'selectedSeats' => $passengers->pluck('seat_number'),
        'passengerNames' => $passengers->pluck('name'),
        'pax' => $order->seat_quantity,
    ]);
}


public function edit($id)
{
    $order = Order::findOrFail($id);
    $customers = Customer::all();
    $schedules = Schedule::all();

    return view('dashboard.orders.edit', compact('order', 'customers', 'schedules'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'schedule_id' => 'required|exists:schedules,id',
        'seat_quantity' => 'required|integer|min:1',
        'total_price' => 'required|numeric|min:0',
        'payment_status' => 'nullable|in:belum,lunas,gagal',
        'order_status' => 'nullable|in:menunggu,proses,selesai,batal',
    ]);

    $order = Order::findOrFail($id);

    $order->customer_id = $request->customer_id;
    $order->schedule_id = $request->schedule_id;
    $order->seat_quantity = $request->seat_quantity;
    $order->total_price = $request->total_price;

    // Gunakan data dari form jika ada, jika tidak gunakan default
    $order->payment_status = $request->payment_status ?? 'lunas';
    $order->order_status = $request->order_status ?? 'selesai';

    $order->save();

    return redirect()->route('pemesanan.index')->with('success', 'Data pemesanan berhasil diperbarui.');
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
    return view('homepage.public.show_ticket', compact('order'));
}

public function downloadTicket(Order $order)
{


    $order->load('passengers');
    $pdf = FacadePdf::loadView('homepage.public.ticket', compact('order'))
        ->setPaper('A4', 'portrait');

    return $pdf->download('tiket_' . $order->id . '.pdf');
}




}
