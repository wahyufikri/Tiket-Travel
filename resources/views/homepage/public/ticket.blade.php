<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tiket Travel</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; background-color: #f9f9f9; }
        .ticket {
            background: #fff;
            border: 2px dashed #FF5733;
            padding: 20px;
            border-radius: 10px;
            width: 600px;
            margin: 30px auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { color: #FF5733; text-align: center; margin-bottom: 5px; }
        .order-code { text-align: center; font-size: 13px; color: #555; margin-bottom: 15px; }
        .section-title { font-weight: bold; font-size: 15px; margin-top: 15px; margin-bottom: 8px; color: #444; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .info p { margin: 4px 0; }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .status-paid { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-unpaid { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>Tiket Pemesanan Travel</h2>
        <div class="order-code">Nomor Pesanan: {{ $order->order_code ?? $order->id }}</div>

        {{-- Penumpang & Kursi --}}
        <div class="section-title">Penumpang & Nomor Kursi</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Penumpang</th>
                    <th>Nomor Kursi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->passengers as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->seat_number }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Rute & Jadwal --}}
        <div class="section-title">Rute & Jadwal</div>
        <div class="info">
            <p><strong>Rute:</strong> {{ $origin }} → {{ $destination }}</p>
            <p><strong>Tanggal & Waktu:</strong> {{ \Carbon\Carbon::parse($departure_segment)->format('d F Y, H:i') }}</p>
        </div>

        {{-- Pembayaran --}}
        <div class="section-title">Pembayaran</div>
        <div class="info">
            <p><strong>Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            <p><strong>Status Pembayaran:</strong>
                @if($order->payment_status === 'lunas')
                    <span class="status-paid">Dikonfirmasi</span>
                @elseif($order->payment_status === 'belum')
                    <span class="status-pending">Menunggu Verifikasi</span>
                @else
                    <span class="status-unpaid">Belum Bayar</span>
                @endif
            </p>
        </div>

        <div class="footer">
            Terima kasih telah memesan di <strong>PT AWR Travel</strong>.<br>
            Tunjukkan tiket ini kepada petugas saat keberangkatan.
        </div>
    </div>
</body>
</html>
