@extends('homepage.layouts.main')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-4 grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- KIRI: Detail Pesanan --}}
        <div class="bg-white border rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-orange-600">DETAIL PESANAN</h2>

            <div class="mb-4">
                <p class="text-sm font-medium">Kode Booking</p>
                <p class="text-lg font-semibold text-red-600">{{ $order->order_code }}</p>
            </div>

            <div class="mb-4 grid grid-cols-2 text-sm">
                <div>
                    <p class="font-medium">{{ $trip->origin }}</p>
                    <p>{{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('l, d F Y') }}</p>
                </div>
                <div>
                    <p class="font-medium">{{ $trip->destination }}</p>
                    <p>{{ $trip->departure_time }} WIB</p>
                </div>
            </div>

            <hr class="my-4">

            <h3 class="font-semibold mb-2">DATA PEMESAN</h3>
            <div class="text-sm mb-2">
                <p><strong>Nama Pemesan:</strong> {{ $passengerNames[0] ?? '-' }}</p>
                <p><strong>Kontak:</strong> +62xxxxxxxxxxx</p>
                <p><strong>Email:</strong> email@example.com</p>
            </div>

            <table class="w-full text-sm border mt-2">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="text-left px-2 py-1">No.</th>
                        <th class="text-left px-2 py-1">Nama</th>
                        <th class="text-left px-2 py-1">Kursi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($selectedSeats as $index => $seat)
                        <tr class="border-b">
                            <td class="px-2 py-1">{{ $index + 1 }}</td>
                            <td class="px-2 py-1">{{ $passengerNames[$index] ?? '-' }}</td>
                            <td class="px-2 py-1">{{ $seat }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr class="my-4">

            <div class="text-sm">
                <p>Sub Total: <strong>Rp. {{ number_format($order->total_price) }}</strong></p>
                <p>Diskon Voucher: <strong>-Rp. 0</strong></p>
                <p class="mt-1 font-semibold">Total Harga: <span class="text-red-600">Rp.
                        {{ number_format($order->total_price) }}</span>
                </p>
            </div>
        </div>

        {{-- KANAN: Pembayaran --}}
        <div class="bg-white border rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-orange-600">PEMBAYARAN</h2>

            <p class="text-sm mb-4">Virtual Account</p>

            <div class="space-y-3">
                {{-- BNI VA --}}
                <form action="{{ route('checkout.simulate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="payment_method" value="BNI VA">
                    <button type="submit"
                        class="w-full border p-3 rounded flex justify-between items-center hover:border-red-600 cursor-pointer text-left">
                        <div>
                            <p class="font-semibold">BNI VA</p>
                            <p class="text-xs text-gray-500">Minimal transaksi Rp. 50.000</p>
                        </div>
                        <img src="https://upload.wikimedia.org/wikipedia/id/thumb/0/0d/Bank_BNI_logo.svg/1200px-Bank_BNI_logo.svg.png"
                            alt="BNI" class="w-12">
                    </button>
                </form>

                {{-- BRIVA --}}
                <form action="{{ route('checkout.simulate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="payment_method" value="BRIVA">
                    <button type="submit"
                        class="w-full border p-3 rounded flex justify-between items-center hover:border-red-600 cursor-pointer text-left">
                        <div>
                            <p class="font-semibold">BRIVA</p>
                            <p class="text-xs text-gray-500">Minimal transaksi Rp. 50.000</p>
                        </div>
                        <img src="https://upload.wikimedia.org/wikipedia/id/2/23/Bank_Rakyat_Indonesia.png" alt="BRI"
                            class="w-12">
                    </button>
                </form>

                {{-- Mandiri VA --}}
                <form action="{{ route('checkout.simulate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="payment_method" value="MANDIRI VA">
                    <button type="submit"
                        class="w-full border p-3 rounded flex justify-between items-center hover:border-red-600 cursor-pointer text-left">
                        <div>
                            <p class="font-semibold">MANDIRI VA</p>
                            <p class="text-xs text-gray-500">Minimal transaksi Rp. 50.000</p>
                        </div>
                        <img src="https://upload.wikimedia.org/wikipedia/id/thumb/2/2e/Bank_Mandiri_logo.svg/1200px-Bank_Mandiri_logo.svg.png"
                            alt="Mandiri" class="w-12">
                    </button>
                </form>

                {{-- Permata VA --}}
                <form action="{{ route('checkout.simulate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="payment_method" value="PERMATA VA">
                    <button type="submit"
                        class="w-full border p-3 rounded flex justify-between items-center hover:border-red-600 cursor-pointer text-left">
                        <div>
                            <p class="font-semibold">PERMATA VA</p>
                            <p class="text-xs text-gray-500">Minimal transaksi Rp. 50.000</p>
                        </div>
                        <img src="https://seeklogo.com/images/P/permata-bank-logo-001F576D47-seeklogo.com.png"
                            alt="Permata" class="w-12">
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
