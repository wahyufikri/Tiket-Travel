<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestScheduleCommand extends Command
{
    // Signature command harus sama dengan yang dipanggil di Kernel
    protected $signature = 'test:schedule';

    protected $description = 'Command test untuk scheduler Laravel 12';

    public function handle(): void
    {
        $this->info('✅ TestScheduleCommand jalan!');
    }
}
