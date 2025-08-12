<?php

require_once __DIR__ . '/vendor/autoload.php';

\Midtrans\Config::$serverKey = 'Mid-server-PmfVK1hnPwApE8EOwOhJNPDh';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

function testPayment($bank, $amount = 10000) {
    echo "Testing $bank...\n";

    $params = [
        'payment_type' => 'bank_transfer',
        'transaction_details' => [
            'order_id' => strtoupper($bank) . '-TEST-' . uniqid(),
            'gross_amount' => $amount,
        ],
        'bank_transfer' => [
            'bank' => $bank
        ]
    ];

    try {
        $charge = \Midtrans\CoreApi::charge($params);
        echo "SUCCESS: VA Number => " . json_encode($charge->va_numbers ?? $charge) . "\n\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n\n";
    }
}

function testGopay($amount = 10000) {
    echo "Testing GoPay...\n";

    $params = [
        'payment_type' => 'gopay',
        'transaction_details' => [
            'order_id' => 'GOPAY-TEST-' . uniqid(),
            'gross_amount' => $amount,
        ],
        'gopay' => [
            'enable_callback' => false,
            'callback_url' => 'https://example.com/callback'
        ]
    ];

    try {
        $charge = \Midtrans\CoreApi::charge($params);
        echo "SUCCESS: QR Code URL => " . ($charge->actions[0]->url ?? 'No URL') . "\n\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n\n";
    }
}

testPayment('bca');
testPayment('bri');
testPayment('mandiri');
testGopay();
