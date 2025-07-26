@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Order</h2>

    <form action="/pemesanan/{{ $order->id }}" method="POST"
        class="bg-white p-6 rounded-lg shadow-md space-y-4">
        @csrf
        @method('PUT')

        <!-- Customer -->
        <div>
            <label for="customer_id" class="block font-semibold">Pelanggan <span class="text-red-500">*</span></label>
            <select name="customer_id" id="customer_id" required
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}"
                        {{ $customer->id == $order->customer_id ? 'selected' : '' }}>
                        {{ $customer->name }} - {{ $customer->email }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Jadwal -->
        <div>
            <label for="schedule_id" class="block font-semibold">Jadwal <span class="text-red-500">*</span></label>
            <select name="schedule_id" id="schedule_id" required
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                @foreach ($schedules as $schedule)
                    <option value="{{ $schedule->id }}"
                        data-price="{{ $schedule->route->price }}"
                        {{ $schedule->id == $order->schedule_id ? 'selected' : '' }}>
                        {{ $schedule->route->origin }} - {{ $schedule->route->destination }} |
                        {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d M Y') }}
                        {{ $schedule->departure_time }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Jumlah Kursi -->
        <div>
            <label for="seat_quantity" class="block font-semibold">Jumlah Kursi <span class="text-red-500">*</span></label>
            <input type="number" name="seat_quantity" id="seat_quantity" min="1"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('seat_quantity', $order->seat_quantity) }}" required>
        </div>

        <!-- Total Harga -->
        <div>
            <label for="total_price" class="block font-semibold">Total Harga <span class="text-red-500">*</span></label>
            <input type="number" name="total_price" id="total_price"
                class="w-full border bg-gray-100 rounded px-3 py-2 mt-1 focus:outline-none"
                value="{{ old('total_price', $order->total_price) }}" readonly>
        </div>

        <!-- Status Pembayaran -->
        <div>
            <label class="block font-semibold">Status Pembayaran <span class="text-red-500">*</span></label>
            <select name="payment_status"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                <option value="belum" {{ $order->payment_status === 'belum' ? 'selected' : '' }}>Belum</option>
                <option value="lunas" {{ $order->payment_status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="gagal" {{ $order->payment_status === 'gagal' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>

        <!-- Status Pemesanan -->
        <div>
            <label class="block font-semibold">Status Pemesanan <span class="text-red-500">*</span></label>
            <select name="order_status"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                <option value="menunggu" {{ $order->order_status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="proses" {{ $order->order_status === 'proses' ? 'selected' : '' }}>Diproses</option>
                <option value="selesai" {{ $order->order_status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="batal" {{ $order->order_status === 'batal' ? 'selected' : '' }}>Batal</option>
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

{{-- Script hitung total harga --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const scheduleSelect = document.getElementById('schedule_id');
        const seatInput = document.getElementById('seat_quantity');
        const totalPriceInput = document.getElementById('total_price');

        function updateTotalPrice() {
            const selectedOption = scheduleSelect.options[scheduleSelect.selectedIndex];
            const pricePerSeat = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const quantity = parseInt(seatInput.value) || 0;

            const total = quantity * pricePerSeat;
            totalPriceInput.value = total;
        }

        scheduleSelect.addEventListener('change', updateTotalPrice);
        seatInput.addEventListener('input', updateTotalPrice);

        // Hitung saat pertama dimuat
        updateTotalPrice();
    });
</script>
@endsection
