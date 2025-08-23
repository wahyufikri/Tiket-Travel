<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    // Level log khusus untuk exception tertentu (kosong default)
    protected $levels = [];

    // Exception yang tidak perlu dilaporkan
    protected $dontReport = [];

    // Input yang tidak boleh di-flash saat validation error
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Daftarkan callback untuk reporting exception
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
