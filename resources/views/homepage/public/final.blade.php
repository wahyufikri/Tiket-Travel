{{-- resources/views/checkout/success.blade.php --}}
@extends('homepage.layouts.main')

@section('content')
<div class="max-w-2xl mx-auto mt-12 bg-white rounded-xl shadow-lg p-8">

    {{-- Icon centang --}}
    <div class="flex justify-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    </div>

    {{-- Judul --}}
    <h1 class="text-3xl font-bold text-gray-800 text-center">Pembayaran Berhasil!</h1>
    <p class="text-gray-600 mt-2 text-center">
        Tiket Anda telah dikonfirmasi ✅ Informasi lengkap telah dikirim ke WhatsApp Anda.
    </p>

    {{-- Detail Pemesanan --}}
    <div class="mt-8 border rounded-lg overflow-hidden">
        <div class="bg-gray-100 px-5 py-3 border-b">
            <h2 class="font-semibold text-gray-800">Detail Pemesanan</h2>
        </div>
        <div class="p-5 space-y-4">

            {{-- Penumpang & Kursi --}}
            <div>
                <span class="block font-medium text-gray-700 mb-1">Penumpang & Kursi:</span>
                <ul class="bg-gray-50 border rounded-md divide-y">
                    @foreach ($order->passengers as $p)
                        <li class="px-3 py-2 flex justify-between">
                            <span>{{ $p->name }}</span>
                            <span class="text-sm bg-green-100 text-green-700 px-2 py-0.5 rounded">Kursi {{ $p->seat_number }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Info Rute & Jadwal --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="block font-medium text-gray-700">Rute:</span>
                    <span>{{ $origin }} → {{ $destination }}</span>
                </div>
                <div>
                    <span class="block font-medium text-gray-700">Jadwal:</span>
                    <span>{{ $departure_segment }} → {{ $arrival_segment }}</span>
                </div>
            </div>

            {{-- Nomor Order & Metode Pembayaran --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="block font-medium text-gray-700">Nomor Order:</span>
                    <span>{{ $order->order_code }}</span>
                </div>
                <div>
                    <span class="block font-medium text-gray-700">Metode Pembayaran:</span>
                    <span>{{ $order->payment->payment_method }}</span>
                </div>
            </div>

            {{-- Total Bayar --}}
            <div>
                <span class="block font-medium text-gray-700">Total Bayar:</span>
                <span class="text-lg font-bold text-green-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>

            {{-- Status --}}
            <div>
                <span class="block font-medium text-gray-700">Status:</span>
                @if ($order->payment->status === 'terverifikasi')
                    <span class="text-green-600 font-semibold">✅ Dikonfirmasi</span>
                @elseif($order->payment->status === 'menunggu')
                    <span class="text-yellow-600 font-semibold">⏳ Menunggu Verifikasi</span>
                @else
                    <span class="text-red-600 font-semibold">❌ Ditolak</span>
                @endif
            </div>

            {{-- Nomor WhatsApp --}}
            <div class="text-sm text-gray-600 border-t pt-3">
                Notifikasi WhatsApp dengan e-tiket telah dikirim ke:
                <span class="font-medium">{{ $order->customer->phone }}</span>
            </div>

        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="mt-6 flex flex-wrap justify-center gap-3">
        <a href="{{ route('orders.showTicket', $order->id) }}"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow">
            Lihat Tiket
        </a>
        <a href="{{ route('orders.downloadTicket', $order->id) }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">
            Unduh Tiket
        </a>
        <a href="{{ url('/') }}"
            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded shadow">
            Kembali ke Beranda
        </a>
    </div>

</div>
@endsection
