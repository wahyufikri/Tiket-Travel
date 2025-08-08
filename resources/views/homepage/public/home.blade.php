@extends('homepage.layouts.main')

@section('title', 'Beranda')

@section('content')
<section class="bg-red-50 py-8">
    <div class="max-w-6xl mx-auto">
        {{-- Form Pencarian --}}
        <form action="{{ route('public.schedule') }}" method="GET"
              x-data="scheduleForm()" x-init="init()"
              class="bg-white shadow-md rounded-xl p-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            {{-- Dari --}}
            <div>
                <label for="depart" class="block text-sm font-semibold text-gray-700 mb-1">Dari</label>
                <select id="depart" name="depart" required
                        class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    <option value="" disabled {{ request('depart') ? '' : 'selected' }}>Pilih asal</option>
                    @foreach ($origins as $origin)
                        <option value="{{ $origin->stop_name }}" {{ request('depart') == $origin->stop_name ? 'selected' : '' }}>
                            {{ $origin->stop_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tujuan --}}
            <div>
                <label for="arrival" class="block text-sm font-semibold text-gray-700 mb-1">Tujuan</label>
                <select id="arrival" name="arrival" required
                        class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                    <option value="" disabled {{ request('arrival') ? '' : 'selected' }}>Pilih tujuan</option>
                    @foreach ($destinations as $destination)
                        <option value="{{ $destination->stop_name }}" {{ request('arrival') == $destination->stop_name ? 'selected' : '' }}>
                            {{ $destination->stop_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal --}}
            <div>
                <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" required
                       class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            {{-- Penumpang --}}
            <div>
                <label for="pax" class="block text-sm font-semibold text-gray-700 mb-1">Penumpang</label>
                <input type="number" name="pax" id="pax" min="1" value="{{ request('pax', 1) }}"
                       required class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            {{-- Tombol --}}
            <div>
                <button type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-red-700 transition duration-200">
                    Cek Jadwal
                </button>
            </div>
        </form>
    </div>
</section>

{{-- Pesanan Terakhir --}}
<section class="max-w-6xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-3">Pesanan Terakhir</h2>

    @if($orders->isEmpty())
        <div class="bg-gray-100 text-center p-4 rounded">
            Tidak ada pesanan terakhir
        </div>
    @else
        @foreach($orders as $order)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-orange-500">
                {{-- Header Booking --}}
                <div class="flex justify-between items-center border-b border-orange-200 pb-4 mb-4">
                    <h2 class="text-lg font-bold text-orange-600">{{ $order->kode_booking }}</h2>
                    <span class="px-3 py-1 rounded-full text-white text-sm
                        {{ $order->status == 'BOOK' ? 'bg-red-500' : 'bg-green-500' }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>

                {{-- Detail Booking --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Kiri --}}
    <div>
        <p><span class="font-semibold">Kode Pesanan:</span> {{ $order->order_code }}</p>
        <p><span class="font-semibold">Tanggal Pesan:</span>
            {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('l, d F Y H:i') }}
        </p>
        <p><span class="font-semibold">Jumlah Kursi:</span> {{ $order->seat_quantity }}</p>
    </div>

    {{-- Kanan --}}
    <div>
        <p><span class="font-semibold">Total Harga:</span> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        <p><span class="font-semibold">Status Pembayaran:</span> {{ ucfirst($order->payment_status) }}</p>
        <p><span class="font-semibold">Status Pesanan:</span> {{ ucfirst($order->order_status) }}</p>
    </div>
</div>


                {{-- Tombol Bayar --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('checkout.payment', $order->id) }}"
                       class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold">
                        Bayar
                    </a>
                </div>
            </div>
        @endforeach
    @endif
</section>
@endsection
