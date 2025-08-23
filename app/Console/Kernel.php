<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * Tambahkan command custom di sini.
     */
    protected $commands = [
        \App\Console\Commands\GenerateDailySchedule::class,
        \App\Console\Commands\SendTravelNotification::class,
        \App\Console\Commands\MarkCompletedSchedules::class,
        \App\Console\Commands\AutoCancelUnpaidOrders::class,
        \App\Console\Commands\ExpireOrders::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Generate jadwal harian setiap jam 2 pagi
        $schedule->command('schedule:generate-daily')->dailyAt('02:00');
        $schedule->command('inspire')->everyMinute();

        // Menandai schedule yang sudah selesai setiap 15 menit
        $schedule->command('schedule:mark-completed')->everyFifteenMinutes();

        // Membatalkan pesanan yang belum dibayar → cek tiap menit
        $schedule->command('orders:cancel-unpaid')->everyMinute();

        // Kirim notifikasi travel ke pelanggan → cek tiap menit
        $schedule->command('send:travel-notification')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Expire pesanan otomatis → cek tiap menit
        $schedule->command('orders:expire')->everyMinute();
        $schedule->command('inspire')->everyMinute();


        // Tambahan logic langsung (tanpa command terpisah)
        $schedule->call(function () {
            \App\Models\Order::where('payment_status', 'belum')
                ->where('order_status', 'menunggu')
                ->where('expired_at', '<', now())
                ->update(['order_status' => 'batal']);
        })->everyMinute();

        $schedule->call(function () {
        Log::info('🔔 Scheduler jalan di ' . now());
    })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
