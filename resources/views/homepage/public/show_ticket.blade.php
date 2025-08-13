@extends('homepage.layouts.main')

@section('content')
<div class="container mx-auto py-10">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden">

        {{-- Header Tiket --}}
        <div class="bg-orange-600 text-white px-6 py-4 text-center">
            <h2 class="text-2xl font-bold">Tiket Pemesanan Travel</h2>
            <p class="text-sm">Nomor Pesanan: <span class="font-semibold">{{ $order->order_code ?? $order->id }}</span></p>
        </div>

        {{-- Detail Pemesanan --}}
        <div class="p-6 space-y-4">
            {{-- Penumpang & Kursi --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-1 mb-2">Penumpang & Kursi</h3>
                <table class="w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left">No</th>
                            <th class="border px-3 py-2 text-left">Nama Penumpang</th>
                            <th class="border px-3 py-2 text-left">Nomor Kursi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->passengers as $index => $p)
                            <tr>
                                <td class="border px-3 py-2">{{ $index + 1 }}</td>
                                <td class="border px-3 py-2">{{ $p->name }}</td>
                                <td class="border px-3 py-2">{{ $p->seat_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Rute --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-1 mb-2">Rute & Jadwal</h3>
                <p><span class="font-medium">Rute:</span> {{ $origin }} → {{ $destination }}</p>
                <p><span class="font-medium">Tanggal & Waktu:</span> {{ \Carbon\Carbon::parse($departure_segment)->format('d F Y, H:i') }}</p>
            </div>

            {{-- Harga & Pembayaran --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-1 mb-2">Pembayaran</h3>
                <p><span class="font-medium">Harga Total:</span> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                <p><span class="font-medium">Status:</span>
                    @if($order->payment_status === 'lunas')
                        <span class="text-green-600 font-semibold">Dikonfirmasi</span>
                    @elseif($order->payment_status === 'belum')
                        <span class="text-yellow-600 font-semibold">Menunggu Verifikasi</span>
                    @else
                        <span class="text-red-600 font-semibold">Belum Bayar</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="bg-gray-50 px-6 py-4 flex justify-between">
            <a href="{{ route('orders.downloadTicket', $order->id) }}"
               class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded">
               Download PDF
            </a>
            <a href="{{ url()->previous() }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
               Kembali
            </a>
        </div>
    </div>
</div>
@endsection
