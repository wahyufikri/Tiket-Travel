<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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

public function destroy($id)
    {
        Order::destroy($id);
        return redirect()->route('pemesanan.index')->with('success', 'Auto Schedule dihapus.');
    }

}
