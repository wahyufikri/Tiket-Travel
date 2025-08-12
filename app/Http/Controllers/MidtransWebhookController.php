<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('🔔 Midtrans Webhook Diterima', [
        'payload' => $request->all()
    ]);
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $notification = new Notification();
        $status = $notification->transaction_status;
        $orderId = $notification->order_id;

        // Cari order berdasarkan order_id
        $order = Order::where('order_code', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update status sesuai status dari Midtrans
        if (in_array($status, ['settlement', 'capture'])) {
            $order->update([
                'payment_status' => 'lunas',
                'order_status' => 'proses',
            ]);
        } elseif ($status === 'pending') {
            $order->update([
                'payment_status' => 'belum',
                'order_status' => 'menunggu',
            ]);
        } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
            $order->update([
                'payment_status' => 'gagal',
                'order_status' => 'batal',
            ]);
        }

        return response()->json(['message' => 'Order status updated']);
    }


    public function checkAllVaChannels()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false; // sandbox
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $banks = ['bri', 'bca', 'bni', 'mandiri'];
        $results = [];

        foreach ($banks as $bank) {
            $params = [
                'payment_type' => 'bank_transfer',
                'transaction_details' => [
                    'order_id' => strtoupper($bank) . '-TEST-' . time(),
                    'gross_amount' => 10000,
                ],
                'bank_transfer' => [
                    'bank' => $bank
                ],
                'customer_details' => [
                    'first_name' => 'Test',
                    'email' => 'test@example.com',
                    'phone' => '08123456789',
                ]
            ];

            try {
                $charge = \Midtrans\CoreApi::charge($params);
                $results[$bank] = [
                    'active' => true,
                    'va_number' => $charge->va_numbers[0]->va_number ?? null
                ];
            } catch (\Exception $e) {
                $results[$bank] = [
                    'active' => false,
                    'error' => $e->getMessage()
                ];
            }

            // jeda 1 detik biar order_id tidak duplikat di Midtrans
            sleep(1);
        }

        return response()->json($results);
    }
}
