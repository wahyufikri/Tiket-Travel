@extends('homepage.layouts.main')

@section('content')
<div class="container mx-auto px-4 py-10">

    <!-- Judul -->
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-red-600">Cek Reservasi</h1>
        <p class="text-gray-600 mt-2">Masukkan kode reservasi untuk melihat detail tiket Anda.</p>
    </div>

    <!-- Grid 2 Kolom -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Card Form -->
        <div class="bg-white rounded-2xl shadow-lg border border-red-200 p-6">
            <h2 class="text-xl font-bold text-red-600 mb-4">Cari Reservasi</h2>
            <form action="{{ route('cek-reservasi') }}" method="GET" class="space-y-4">
                <div>
                    <label for="order_code" class="block text-sm font-semibold text-gray-700">Kode Order</label>
                    <input type="text" name="order_code" id="order_code"
                           value="{{ request('order_code') }}"
                           placeholder="Masukkan kode Pemesanan (contoh: TX-20210815013850-7157)"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition">
                    <i class="fas fa-search mr-2"></i> Cek Reservasi
                </button>
            </form>
        </div>

        <!-- Card Hasil -->
        <div>
            @if(request('order_code'))
                @if($order)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-red-600 mb-4">Detail Reservasi</h2>
                        <div class="space-y-2 text-gray-700">
                            <p><strong>Kode Order:</strong> {{ $order->order_code }}</p>
                            <p><strong>Nama Pemesan:</strong> {{ $order->customer->name }}</p>
                            <p><strong>No. Telepon:</strong> {{ $order->customer->phone }}</p>
                            <p><strong>Rute:</strong> {{ $order->booking->fromStop->stop_name }} → {{ $order->booking->toStop->stop_name }}</p>
                            <p><strong>Jumlah Kursi:</strong> {{ $order->seat_quantity }}</p>
                            <p><strong>Tanggal Keberangkatan:</strong> {{ $order->schedule->departure_date }}</p>
                            <p><strong>Jam Keberangkatan:</strong> {{ \Carbon\Carbon::parse($order->schedule->departure_time)->format('H:i') }}</p>
                            <p><strong>Total Harga:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>

                            <!-- Status -->
                            <p><strong>Status Order:</strong>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($order->order_status == 'menunggu') bg-yellow-100 text-yellow-800
                                    @elseif($order->order_status == 'proses') bg-blue-100 text-blue-800
                                    @elseif($order->order_status == 'selesai') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </p>
                            <p><strong>Status Pembayaran:</strong>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($order->payment_status == 'lunas') bg-green-100 text-green-800
                                    @elseif($order->payment_status == 'belum') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                        </div>

                        <!-- Penumpang -->
                        <div class="mt-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Penumpang:</h3>
                            <ul class="space-y-1 text-gray-600">
                                @foreach($order->passengers as $p)
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-user text-red-500"></i>
                                        {{ $p->name }} <span class="text-gray-500 text-sm">(Kursi: {{ $p->seat_number }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="bg-red-100 text-red-800 border border-red-300 rounded-lg p-6 text-center shadow">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Reservasi dengan kode <strong>{{ request('order_code') }}</strong> tidak ditemukan.
                    </div>
                @endif
            @else
                <div class="bg-gray-50 text-gray-500 border border-gray-200 rounded-lg p-6 text-center shadow">
                    <i class="fas fa-ticket-alt text-red-400 text-lg mr-1"></i>
                    Masukkan kode order untuk melihat detail reservasi Anda.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
