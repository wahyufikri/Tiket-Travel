@extends('dashboard.layouts.main') <!-- Ganti sesuai layout utama kamu -->

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Order Baru</h2>
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pemesanan.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @csrf

            <!-- Customer -->
            <div>
                <label for="customer_id" class="block font-semibold">Pelanggan <span class="text-red-500">*</span></label>
                <select name="customer_id" id="customer_id" required
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->email }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jadwal -->
            <div>
                <label for="schedule_id" class="block font-semibold">Jadwal <span class="text-red-500">*</span></label>
                <select name="schedule_id" id="schedule_id" required
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Jadwal --</option>
                    @foreach ($schedules as $schedule)
                        <option value="{{ $schedule->id }}" data-price="{{ $schedule->route->price }}">
                            {{ $schedule->route->origin }} - {{ $schedule->route->destination }} |
                            {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d M Y') }}
                            {{ $schedule->departure_time }}
                        </option>
                    @endforeach

                </select>
            </div>

            <!-- Jumlah Kursi -->
            <div>
                <label for="seat_quantity" class="block font-semibold">Jumlah Kursi <span
                        class="text-red-500">*</span></label>
                <input type="number" name="seat_quantity" id="seat_quantity" min="1"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan jumlah kursi" required>
            </div>

            <!-- Total Harga -->
            <div>
                <label for="total_price" class="block font-semibold">Total Harga <span class="text-red-500">*</span></label>
                <input type="number" name="total_price" id="total_price"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Total harga" required>
            </div>

            <!-- Status Pembayaran -->
            <div>
                <label class="block font-semibold">Status Pembayaran <span class="text-red-500">*</span></label>
                <select name="payment_status"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    required>
                    <option value="belum">Belum</option>
                    <option value="lunas" selected>Lunas</option> <!-- default dipilih -->
                    <option value="gagal">Gagal</option>
                </select>
            </div>

            <!-- Status Pemesanan -->
            <div>
                <label class="block font-semibold">Status Pemesanan <span class="text-red-500">*</span></label>
                <select name="order_status"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    required>
                    <option value="menunggu">Menunggu</option>
                    <option value="proses">Diproses</option>
                    <option value="selesai" selected>Selesai</option> <!-- default dipilih -->
                    <option value="batal">Batal</option>
                </select>
            </div>


            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('pemesanan.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleSelect = document.getElementById('schedule_id');
            const seatInput = document.getElementById('seat_quantity');
            const totalPriceInput = document.getElementById('total_price');

            function updateTotalPrice() {
                const selectedOption = scheduleSelect.options[scheduleSelect.selectedIndex];
                const seatCount = parseInt(seatInput.value) || 0;
                const pricePerSeat = parseFloat(selectedOption.getAttribute('data-price')) || 0;

                const total = seatCount * pricePerSeat;
                totalPriceInput.value = total;
            }

            // Trigger saat memilih jadwal atau mengisi jumlah kursi
            scheduleSelect.addEventListener('change', updateTotalPrice);
            seatInput.addEventListener('input', updateTotalPrice);
        });
    </script>
@endsection
