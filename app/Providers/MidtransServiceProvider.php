<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class MidtransServiceProvider extends ServiceProvider
{
    public function register()
    {
        // ambil dari config, fallback ke env kalau perlu
        $serverKey = config('midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        if (empty($serverKey)) {
            Log::warning('Midtrans server key is empty. Check .env and config/midtrans.php');
            return;
        }

        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = filter_var(config('midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);
        \Midtrans\Config::$isSanitized  = filter_var(config('midtrans.is_sanitized', env('MIDTRANS_IS_SANITIZED', true)), FILTER_VALIDATE_BOOLEAN);
        \Midtrans\Config::$is3ds        = filter_var(config('midtrans.is_3ds', env('MIDTRANS_IS_3DS', true)), FILTER_VALIDATE_BOOLEAN);
    }

    public function boot()
    {
        // kosong atau log jika mau
    }
}
