<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background: #f8f8f8;
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
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
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
</body>
</html>
