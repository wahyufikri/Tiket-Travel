@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Order</h2>

    <form action="/pemesanan/{{ $order->id }}" method="POST"
        class="bg-white p-6 rounded-lg shadow-md space-y-4">
        @csrf
        @method('PUT')

        <!-- Status Pembayaran -->
        <div>
            <label class="block font-semibold">Status Pembayaran <span class="text-red-500">*</span></label>
            <select name="payment_status"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                <option value="belum" {{ $order->payment_status == 'belum' ? 'selected' : '' }}>Belum</option>
                <option value="lunas" {{ $order->payment_status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="gagal" {{ $order->payment_status == 'gagal' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>

        <!-- Status Pemesanan -->
        <div>
            <label class="block font-semibold">Status Pemesanan <span class="text-red-500">*</span></label>
            <select name="order_status"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                <option value="menunggu" {{ $order->order_status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="proses" {{ $order->order_status == 'proses' ? 'selected' : '' }}>Diproses</option>
                <option value="selesai" {{ $order->order_status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="batal" {{ $order->order_status == 'batal' ? 'selected' : '' }}>Batal</option>
            </select>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end space-x-2 mt-6">
            <a href="{{ route('pemesanan.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
            <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
