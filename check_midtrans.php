<?php

$serverKey = 'Mid-server-PmfVK1hnPwApE8EOwOhJNPDh'; // ambil dari .env

$apiUrl = 'https://api.sandbox.midtrans.com/v2/charge';

// Data transaksi
$transaction = [
    'payment_type' => 'bank_transfer',
    'transaction_details' => [
        'order_id' => 'ORDER-' . time(),
        'gross_amount' => 10000 // Rp10.000
    ],
    'bank_transfer' => [
        'bank' => 'bca'
    ]
];

// Inisialisasi cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':')
    ],
    CURLOPT_POSTFIELDS => json_encode($transaction)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output hasil
echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
