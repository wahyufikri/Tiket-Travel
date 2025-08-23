<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - AWR Travel</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #333;
            margin: 40px;
            background-color: #fff;
        }

        /* Header Perusahaan */
        .report-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .company-address {
            font-size: 12px;
            color: #555;
        }

        /* Judul Laporan */
        h2 {
            text-align: center;
            margin: 10px 0;
            text-transform: uppercase;
            font-size: 16px;
        }

        /* Tabel Laporan */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        td {
            vertical-align: middle;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            background: #e8e8e8;
        }

        /* Footer Tanda Tangan */
        .signature {
            margin-top: 40px;
            width: 100%;
        }
        .signature td {
            border: none;
            text-align: center;
            padding-top: 40px;
        }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Kop Laporan -->
    <div class="report-header">
        <div class="company-name">AWR Travel</div>
        <div class="company-address">
            Jl. By Pass, Tarok Dipo, Kec. Guguk Panjang, Kota Bukittinggi, Sumatera Barat 26181, Bukittinggi - Sumatera Barat <br>
            Telp: (0751) 123456 | Email: info@awrtravel.com
        </div>
    </div>

    <!-- Judul -->
    <h2>{{ $title }}</h2>

    <!-- Tabel Transaksi -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Nominal</th>
                <th>Metode</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $i => $t)
                <tr>
                    <td style="text-align: center;">{{ $i+1 }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $t->title }}</td>
                    <td>{{ $t->category->name ?? '-' }}</td>
                    <td class="text-right">Rp{{ number_format($t->amount, 0, ',', '.') }}</td>
                    <td>{{ $t->paymentMethod->name ?? '-' }}</td>
                    <td>{{ $t->description ?? '-' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: left;">Total</td>
                <td style="text-align: right">Rp{{ number_format($transactions->sum('amount'), 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer Tanda Tangan -->
    <table class="signature">
        <tr>
            <td style="text-align: left;">Padang, {{ \Carbon\Carbon::now()->format('d F Y') }}</td>
            <td style="text-align: right;">Pemilik,</td>
        </tr>
        <tr>
            <td></td>
            <td class="name">AWR Travel</td>
        </tr>
    </table>

</body>
</html>
