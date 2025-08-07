<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{



    public function index()    {
        $payments = Payment::with('order')->paginate(10);
        return view('dashboard.payments.index', compact('payments'));
    }
    public function simulate(Request $request)
{
    $order = Order::findOrFail($request->order_id);

    // Simpan data pembayaran simulasi
    $payment = Payment::create([
        'order_id' => $order->id,
        'payment_method' => $request->payment_method,
        'payment_proof' => null, // simulasi, tidak ada bukti
        'paid_at' => now(),
        'verified_by' => null, // belum diverifikasi admin
        'status' => 'terverifikasi', // langsung kita anggap sukses
    ]);

    // Update status order
    $order->order_status = 'proses';
    $order->payment_status = 'lunas';
    $order->save();

    // Jika pembayaran terverifikasi, masukkan juga ke tabel transactions
    if ($payment->status === 'terverifikasi') {
        Transaction::create([
            'type' => 'income', // bisa 'income' atau 'expense' sesuai kebutuhan
            'order_id' => $order->id,
            'title' => 'Tiket Website',
            'amount' => $order->total_price, // pastikan field ini ada di Order
            'category_id' => 1, // ID kategori transaksi yang sesuai
            'transaction_date' => now(),
            'payment_method' => 'Website', // pastikan ini ID dari payment method,
        ]);
    }

    return redirect()->route('checkout.success', $order->id);
}


    public function success($orderId)
{
    $order = Order::with(['payment', 'schedule.route', 'customer', 'passengers'])->findOrFail($orderId);
    $origin = $request->origin ?? session('origin');
$destination = $request->destination ?? session('destination');

    // Ambil penumpang pertama untuk ditampilkan
    $passenger = $order->passengers->first();

    return view('homepage.public.final', compact('order', 'passenger','origin', 'destination'));
}

}
