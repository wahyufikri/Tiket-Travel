<?php

namespace App\Services;

use App\Models\Order;

class PaymentService
{
    public function simulatePayment(string $method, Order $order): bool
    {
        // Contoh logika simulasi pembayaran
        // Misal, jika metode ada dan order valid, kembalikan true
        // if ($method && $order) {
        //     return true; // sukses
        // }
        return false; // gagal
    }
}
