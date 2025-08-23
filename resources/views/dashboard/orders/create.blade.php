@extends('dashboard.layouts.main')

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-md"
     x-data="bookingForm()"
     x-init="init()">

    <h2 class="text-2xl font-bold mb-6">Buat Pemesanan Baru</h2>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pemesanan.store') }}" method="POST" @submit="validateForm">
        @csrf

        {{-- Customer --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Customer</label>
            <select name="customer_id" class="w-full border rounded p-2" required>
                <option value="">-- Pilih Customer --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Jadwal --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Jadwal</label>
            <select name="schedule_id" id="schedule_id" class="w-full border rounded p-2" x-model="scheduleId" @change="fetchSeats" required>
                <option value="">-- Pilih Jadwal --</option>
                @foreach($schedules as $schedule)
    @php
        $routeStops = $schedule->route->stops->sortBy('stop_order')->values();
        $originStop = $routeStops->first();
        $destinationStop = $routeStops->last();
        $fullPrice = $stopPrices->first(function($sp) use ($schedule, $originStop, $destinationStop) {
            return $sp->route_id == $schedule->route_id
                && $sp->from_stop_id == $originStop->id
                && $sp->to_stop_id == $destinationStop->id;
        })->price ?? 0;
    @endphp

    {{-- 1. Full route --}}
    <option value="{{ $schedule->id }}-{{ $originStop->id }}-{{ $destinationStop->id }}" data-price="{{ $fullPrice }}">
        {{ $originStop->stop_name }} → {{ $destinationStop->stop_name }} - {{ $schedule->departure_time }}
    </option>

    {{-- 2. Semua segmen antar stop --}}
    @for($i = 0; $i < $routeStops->count() - 1; $i++)
        @php
            $segOrigin = $routeStops[$i];
            $segDest = $routeStops[$i + 1];
            $segPrice = $stopPrices->first(function($sp) use ($schedule, $segOrigin, $segDest) {
                return $sp->route_id == $schedule->route_id
                    && $sp->from_stop_id == $segOrigin->id
                    && $sp->to_stop_id == $segDest->id;
            })->price ?? 0;
        @endphp
        <option value="{{ $schedule->id }}-{{ $segOrigin->id }}-{{ $segDest->id }}" data-price="{{ $segPrice }}">
            {{ $segOrigin->stop_name }} → {{ $segDest->stop_name }} - {{ $schedule->departure_time }}
        </option>
    @endfor
@endforeach


            </select>
        </div>

        {{-- Jumlah Kursi --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Jumlah Kursi</label>
            <input type="number" name="seat_quantity" id="seat_quantity" class="w-full border rounded p-2" min="1" x-model="maxSeats" @input="resetSelection" value="1" required>
        </div>

        {{-- Pilih Kursi --}}
        <div class="mb-6">
            <label class="block font-medium mb-2">Pilih Kursi</label>
            <div class="grid grid-cols-4 gap-3">
                <template x-for="seat in seats" :key="seat.seat_number">
                    <div
                        class="flex items-center justify-center w-12 h-12 rounded-md border text-sm font-semibold cursor-pointer"
                        :class="seat.is_booked
                            ? 'bg-gray-400 text-gray-700 cursor-not-allowed'
                            : (selectedSeats.includes(seat.seat_number) ? 'bg-red-500 text-white' : 'bg-gray-200 hover:bg-blue-200')"
                        @click="toggleSeat(seat)"
                        x-text="seat.seat_number">
                    </div>
                </template>
            </div>
            <small class="text-gray-500">Klik kursi untuk memilih (maksimal sesuai jumlah kursi).</small>
        </div>

        {{-- Hidden Seats --}}
        <template x-for="seat in selectedSeats" :key="seat">
            <input type="hidden" name="selected_seats[]" :value="seat">
        </template>

        {{-- Nama Penumpang --}}
        <div class="mb-6">
            <label class="block font-medium mb-2">Nama Penumpang</label>
            <template x-for="seat in selectedSeats" :key="seat">
                <input type="text" name="passenger_names[]"
                       :placeholder="`Penumpang kursi ${seat}`"
                       class="w-full mb-2 border rounded p-2" required>
            </template>
        </div>

        {{-- Status Pembayaran --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Status Pembayaran</label>
            <select name="payment_status" class="w-full border rounded p-2" required>
                <option value="belum">Belum</option>
                <option value="lunas">Lunas</option>
                <option value="gagal">Gagal</option>
            </select>
        </div>

        {{-- Status Order --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Status Order</label>
            <select name="order_status" class="w-full border rounded p-2" required>
                <option value="menunggu">Menunggu</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
                <option value="batal">Batal</option>
            </select>
        </div>

        {{-- Total Harga --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Total Harga</label>
            <input type="text" name="total_price" id="total_price" class="w-full border rounded p-2 bg-gray-100" x-model="totalPrice" readonly>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
    </form>
</div>

<script>
function bookingForm() {
    return {
        scheduleId: '',
        seats: [],
        selectedSeats: [],
        maxSeats: 1,
        totalPrice: 0,

        init() {
            this.updatePrice();
        },

        fetchSeats() {
            if (!this.scheduleId) return;
            let price = document.querySelector(`#schedule_id option[value="${this.scheduleId}"]`)?.dataset.price || 0;
            this.pricePerSeat = parseInt(price);
            this.updatePrice();

            fetch(`/get-seats/${this.scheduleId}`)
                .then(res => res.json())
                .then(data => {
                    this.seats = data;
                    this.selectedSeats = [];
                });
        },

        toggleSeat(seat) {
            if (seat.is_booked) return;
            if (this.selectedSeats.includes(seat.seat_number)) {
                this.selectedSeats = this.selectedSeats.filter(s => s !== seat.seat_number);
            } else {
                if (this.selectedSeats.length >= this.maxSeats) {
                    alert(`Maksimal hanya ${this.maxSeats} kursi.`);
                    return;
                }
                this.selectedSeats.push(seat.seat_number);
            }
            this.updatePrice();
        },

        resetSelection() {
            this.selectedSeats = [];
            this.updatePrice();
        },

        updatePrice() {
            let price = document.querySelector(`#schedule_id option[value="${this.scheduleId}"]`)?.dataset.price || 0;
            this.totalPrice = (parseInt(price) || 0) * this.selectedSeats.length;
        },

        validateForm(e) {
            if (this.selectedSeats.length === 0) {
                e.preventDefault();
                alert('Silakan pilih kursi terlebih dahulu.');
            }
        }
    }
}
</script>
@endsection
