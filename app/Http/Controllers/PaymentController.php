<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }



    public function index()    {
        $payments = Payment::with('order')->paginate(10);
        return view('dashboard.payments.index', compact('payments'));
    }
    public function simulate(Request $request)
{
    $order = Order::findOrFail($request->order_id);

    // Simulasi pembayaran lewat service (misal service ini nge-return true jika sukses, false kalau gagal)
    $isPaid = $this->paymentService->simulatePayment($request->payment_method, $order);

    if ($isPaid) {
        // Buat data payment dengan status terverifikasi
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'payment_proof' => null,
            'paid_at' => now(),
            'verified_by' => null,
            'status' => 'terverifikasi',
        ]);

        // Update status order
        $order->order_status = 'proses';
        $order->payment_status = 'lunas';
        $order->save();

        // Buat transaksi di tabel transactions
        Transaction::create([
            'type' => 'income',
            'order_id' => $order->id,
            'title' => 'Tiket Website',
            'amount' => $order->total_price,
            'category_id' => 1,
            'transaction_date' => now(),
            'payment_method' => $request->payment_method,
        ]);

        // Tampilkan halaman sukses dengan data order dan rute
        return view('homepage.public.final', [
            'order' => $order,
            'origin' => session('origin'),
            'destination' => session('destination'),
            'departure_segment' => session('departure_segment'),
            'arrival_segment' => session('arrival_segment'),
            'phone' => session('customer.customer_phone'),
            // data tambahan kalau perlu
        ]);
    } else {
        // Buat data payment dengan status gagal
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'payment_proof' => null,
            'paid_at' => null,
            'verified_by' => null,
            'status' => 'ditolak',
        ]);

        // Update order status jadi belum lunas / pending
        $order->order_status = 'menunggu';
        $order->payment_status = 'gagal';
        $order->save();

        // Tampilkan halaman gagal dengan pesan error
        return view('homepage.public.failed', [
            'order' => $order,
            'origin' => $order->origin,
            'destination' => $order->destination,
            'errorMessage' => 'Pembayaran gagal, coba lagi.',
            // data tambahan kalau perlu
        ]);
    }
}



    public function success($orderId)
{
    $order = Order::with(['payment', 'schedule.route', 'customer', 'passengers'])->findOrFail($orderId);
    $origin = $request->origin ?? session('origin');
$destination = $request->destination ?? session('destination');
$departure_segment = session('departure_segment');
$arrival_segment = session('arrival_segment');
$phone = session('customer.customer_phone');
$email = session('customer.customer_email');

    // Ambil penumpang pertama untuk ditampilkan
    $passenger = $order->passengers->first();

    return view('homepage.public.final', compact('order', 'passenger','origin', 'destination','departure_segment', 'arrival_segment', 'phone', 'email'));
}

public function testSnap()
{
    // Konfigurasi Midtrans
    Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    Config::$isProduction = false; // Sandbox
    Config::$isSanitized = true;
    Config::$is3ds = true;

    // Data transaksi
    $params = [
        'transaction_details' => [
            'order_id' => 'TEST-' . time(),
            'gross_amount' => 10000, // HARUS integer, tanpa titik/koma
        ],
        'customer_details' => [
            'first_name' => 'Budi',
            'last_name' => 'Setiawan',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
        ],
        'item_details' => [
            [
                'id' => 'item1',
                'price' => 10000,
                'quantity' => 1,
                'name' => 'Kamera Sewa'
            ]
        ],
        'credit_card' => [
            'secure' => true
        ]
    ];

    // Buat Snap Token
    $snapToken = Snap::getSnapToken($params);

    return view('test-snap', compact('snapToken'));
}

}
