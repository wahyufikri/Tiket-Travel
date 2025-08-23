<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan - AWR Travel</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 2px 0; font-size: 12px; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AWR Travel</h1>
        <p>Jl. By Pass, Tarok Dipo, Kec. Guguk Panjang, Kota Bukittinggi, Sumatera Barat 26181</p>
        <p>Telp: (021) 12345678 | Email: info@awrtravel.com</p>
    </div>

    <div class="title">
        Laporan Pemesanan {{ ucfirst($filter) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Order</th>
                <th>Rute</th>
                <th>Jumlah Tiket</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
    @forelse($orders as $i => $order)
    <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $order->order_code }}</td>
        <td>{{ $order->booking->fromStop->stop_name }} → {{ $order->booking->toStop->stop_name }}</td>
        <td>{{ $order->seat_quantity }}</td>
        <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
        <td>{{ ucfirst($order->order_status) }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="6" style="text-align: center;">Tidak ada data pemesanan</td>
    </tr>
    @endforelse

    @if(count($orders) > 0)
    <tr>
        <td colspan="4" style="text-align: left; font-weight: bold;">Total</td>
        <td colspan="2" style="font-weight: bold;">
            Rp{{ number_format($orders->sum('total_price'), 0, ',', '.') }}
        </td>
    </tr>
    @endif
</tbody>

    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
    </div>
</body>
</html>
