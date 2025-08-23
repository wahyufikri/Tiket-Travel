<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MidtransLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('📩 Midtrans Webhook Received', $request->all());

        // Ambil order_id asli dari Midtrans
        $rawOrderId = $request->input('order_id');
        Log::debug("🔍 Raw order_id dari Midtrans: {$rawOrderId}");

        // Cari pola order_id sesuai format kita: TX-YYYYMMDDHHIISS-RANDOM
        if (preg_match('/TX-\d{14}-\d+/', $rawOrderId, $matches)) {
            $orderIdFromMidtrans = $matches[0];
        } else {
            $orderIdFromMidtrans = $rawOrderId;
        }

        Log::debug("🛠 Parsed order_id (dipakai untuk query): {$orderIdFromMidtrans}");

        $transactionStatus = $request->input('transaction_status');

        // Cari order berdasarkan order_code
        $order = Order::where('order_code', $orderIdFromMidtrans)->first();

        if (!$order) {
            Log::warning("⚠️ Order tidak ditemukan untuk order_id: {$orderIdFromMidtrans}");
            return response()->json(['message' => "Order not found"], 200);
        }

        // Simpan payload webhook ke order
        $order->midtrans_response = json_encode($request->all(), JSON_PRETTY_PRINT);

        // Pemetaan status
        $statusMap = [
            'settlement' => ['payment_status' => 'lunas', 'order_status' => 'selesai'],
            'capture'    => ['payment_status' => 'lunas', 'order_status' => 'selesai'],
            'pending'    => ['payment_status' => 'belum', 'order_status' => 'menunggu'],
            'deny'       => ['payment_status' => 'gagal', 'order_status' => 'batal'],
            'cancel'     => ['payment_status' => 'gagal', 'order_status' => 'batal'],
            'expire'     => ['payment_status' => 'gagal', 'order_status' => 'batal'],
        ];

        if (isset($statusMap[$transactionStatus])) {
            $order->fill($statusMap[$transactionStatus]);
            Log::info("✅ Order {$order->order_code} berhasil diupdate → {$transactionStatus}");

            // Kalau pembayaran sukses (settlement/capture), insert ke transactions
            if (in_array($transactionStatus, ['settlement', 'capture'])) {

                // Cek apakah sudah ada transaksi untuk order ini
                $existingTransaction = Transaction::where('order_id', $order->id)->first();

                if (!$existingTransaction) {
                    Transaction::create([
                        'type'             => 'income',
                        'order_id'         => $order->id,
                        'title'            => 'Tiket Website ' . $order->order_code,
                        'amount'           => $order->total_price ?? 0, // pastikan field ini ada di tabel orders
                        'category_id'      => 1, // sesuaikan ID kategori
                        'transaction_date' => now(),
                        'payment_method'   => 'midtrans',
                        'description'      => 'Pembayaran otomatis dari Midtrans',
                    ]);

                    Log::info("💰 Transaction untuk Order {$order->order_code} berhasil dibuat");
                } else {
                    Log::info("ℹ️ Transaction untuk Order {$order->order_code} sudah ada, tidak dibuat lagi");
                }
            }
        } else {
            Log::warning("⚠️ Status {$transactionStatus} tidak dikenali untuk order {$order->order_code}");
        }

        $order->save();

        return response()->json(['message' => 'Webhook processed'], 200);
    }
}
