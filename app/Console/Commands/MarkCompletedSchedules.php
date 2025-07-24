<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarkCompletedSchedules extends Command
{
    protected $signature = 'schedule:mark-completed';
    protected $description = 'Tandai jadwal yang sudah lewat (tanggal dan jam) sebagai completed';

    public function handle()
    {
        $now = Carbon::now();

        // Mengupdate semua jadwal yang waktu keberangkatannya sudah lewat
        $count = Schedule::where(DB::raw("STR_TO_DATE(CONCAT(departure_date, ' ', departure_time), '%Y-%m-%d %H:%i:%s')"), '<', $now)
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        $this->info("Total $count jadwal ditandai sebagai completed.");
    }
}
