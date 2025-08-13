<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $orders = \App\Models\Order::where('payment_status', 'belum')
        ->where('order_status', 'menunggu')
        ->where('expired_at', '<', now())
        ->get();

    foreach ($orders as $order) {
        $order->update([
            'order_status' => 'expired'
        ]);

        // Lepasin kursinya
        \App\Models\Booking::where('order_id', $order->id)->delete();

        $this->info("Order {$order->order_code} expired.");
    }
}

}
