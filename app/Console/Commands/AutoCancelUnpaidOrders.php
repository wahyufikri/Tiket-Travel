<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Booking; // pastikan modelnya ada
use Carbon\Carbon;

class AutoCancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid';
    protected $description = 'Batalkan order yang belum dibayar dalam 1 jam';

    public function handle()
    {
        $limit = Carbon::now()->subHour();

        $orders = Order::where('payment_status', 'belum')
            ->where('order_status', 'menunggu')
            ->where('created_at', '<=', $limit)
            ->get();

        foreach ($orders as $order) {
            // Update status order jadi batal
            $order->update(['order_status' => 'batal']);

            // Hapus penumpang
            $order->passengers()->delete();

            // Hapus data di tabel bookings (kalau punya relasi)
            Booking::where('order_id', $order->id)->delete();
        }

        $this->info("Pembatalan otomatis selesai. Total dibatalkan: " . $orders->count());
    }
}
