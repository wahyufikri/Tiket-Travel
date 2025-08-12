<?php
require_once 'vendor/autoload.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'Mid-server-PmfVK1hnPwApE8EOwOhJNPDh';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Data transaksi
$params = [
    'transaction_details' => [
        'order_id' => 'TEST-' . time(),
        'gross_amount' => 10000, // jumlah dalam IDR
    ],
    'customer_details' => [
        'first_name' => 'Budi',
        'last_name' => 'Santoso',
        'email' => 'budi@example.com',
        'phone' => '081234567890',
    ],
];

// Dapatkan Snap Token
try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    echo "Snap Token: " . $snapToken . PHP_EOL;

    // Generate link langsung ke Snap
    echo "Buka link ini di browser:\n";
    echo "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
