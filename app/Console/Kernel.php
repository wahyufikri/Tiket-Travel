<?php
namespace App\Console\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        \App\Console\Commands\GenerateDailySchedule::class, // tambahkan command kamu di sini
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Atur agar command jalan tiap hari jam 2 pagi
        $schedule->command('schedule:generate-daily')->everyMinute();
        // Untuk testing bisa ganti: ->everyMinute();

        $schedule->command('schedule:mark-completed')->daily();

        $schedule->command('orders:cancel-unpaid')->everyMinutes();

        $schedule->command('send:travel-notification')->everyTenMinutes();

        $schedule->command('orders:expire')->everyMinute();
        $schedule->call(function () {
        \App\Models\Order::where('payment_status', 'belum')
            ->where('order_status', 'menunggu')
            ->where('expired_at', '<', now())
            ->update(['order_status' => 'batal']);
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
