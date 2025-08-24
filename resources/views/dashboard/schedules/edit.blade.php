@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h2 class="text-2xl font-bold mb-4">Edit Jadwal</h2>

        <form action="/jadwal/{{ $schedules->id }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @method('PUT')
            @csrf

            <!-- Route -->
            <div>
                <label for="route_id" class="block font-semibold">Rute <span class="text-red-500">*</span></label>
                <select name="route_id" id="route_id" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                    <option value="">-- Pilih Rute --</option>
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $schedules->route_id) == $route->id ? 'selected' : '' }}>
                            {{ $route->origin }} - {{ $route->destination }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Vehicle -->
            <div>
                <label for="vehicle_id" class="block font-semibold">Kendaraan <span class="text-red-500">*</span></label>
                <select name="vehicle_id" id="vehicle_id" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $schedules->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->vehicle_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Driver -->
            <div>
                <label for="driver_id" class="block font-semibold">Sopir <span class="text-red-500">*</span></label>
                <select name="driver_id" id="driver_id" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" required>
                    <option value="">-- Pilih Sopir --</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('driver_id', $schedules->driver_id) == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Departure Date -->
            <div>
                <label for="departure_date" class="block font-semibold">Tanggal Keberangkatan <span class="text-red-500">*</span></label>
                <input type="date" name="departure_date" id="departure_date" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" value="{{ old('departure_date', $schedules->departure_date) }}" required>
            </div>

            <!-- Departure Time -->
           <div x-data="{
        departureTime: '{{ old('departure_time', $schedules->departure_time ? \Carbon\Carbon::parse($schedules->departure_time)->format('H:i') : '') }}',
        customTime: false
    }" class="mb-4">

    <label for="departure_time" class="block font-semibold mb-2 text-gray-700">
        Waktu Keberangkatan <span class="text-red-500">*</span>
    </label>

    <!-- Grid Tombol Jam -->
    <div class="grid grid-cols-4 gap-2 mb-3">
        @foreach(['05:00', '07:00', '09:00', '11:00', '13:00', '15:00', '17:00', '19:00'] as $time)
            <button type="button"
                @click="departureTime = '{{ $time }}'; customTime = false"
                :class="departureTime === '{{ $time }}' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
                class="px-3 py-2 rounded-lg text-sm font-medium transition">
                {{ $time }}
            </button>
        @endforeach

        <!-- Tombol Custom -->
        <button type="button"
            @click="customTime = true; departureTime = ''"
            :class="customTime ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-yellow-100'"
            class="px-3 py-2 rounded-lg text-sm font-medium transition">
            Custom
        </button>
    </div>

    <!-- Input Time untuk Custom -->
    <template x-if="customTime">
        <input type="time"
            name="departure_time"
            id="departure_time"
            x-model="departureTime"
            class="w-full border rounded-lg px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                   @error('departure_time') border-red-500 focus:ring-red-500 @enderror"
            required>
    </template>

    <!-- Hidden input untuk pilihan cepat -->
    <template x-if="!customTime">
        <input type="hidden" name="departure_time" :value="departureTime" required>
    </template>

    @error('departure_time')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>




            <!-- Available Seats -->
            <div>
                <label for="available_seats" class="block font-semibold">Jumlah Kursi <span class="text-red-500">*</span></label>
                <input type="number" name="available_seats" id="available_seats" min="1" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500" value="{{ old('available_seats', $schedules->available_seats) }}" required>
            </div>

           <!-- Status -->
<div>
    <label class="block font-semibold mb-1">Status <span class="text-red-500">*</span></label>
    <div class="flex space-x-6">
        <label class="inline-flex items-center">
            <input type="radio" name="status" value="active"
                class="form-radio text-red-500"
                {{ old('status', $schedules->status) == 'active' ? 'checked' : '' }}>
            <span class="ml-2">Aktif</span>
        </label>

        <label class="inline-flex items-center">
            <input type="radio" name="status" value="completed"
                class="form-radio text-red-500"
                {{ old('status', $schedules->status) == 'completed' ? 'checked' : '' }}>
            <span class="ml-2">Selesai</span>
        </label>

        <label class="inline-flex items-center">
            <input type="radio" name="status" value="cancelled"
                class="form-radio text-red-500"
                {{ old('status', $schedules->status) == 'cancelled' ? 'checked' : '' }}
                onclick="return confirmCancelStatus(this)">
            <span class="ml-2">Dibatalkan</span>
        </label>
    </div>
</div>

            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('jadwal.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>

   @push('scripts')
<script>
    function confirmCancelStatus(radio) {
        Swal.fire({
            title: 'Batalkan Jadwal?',
            text: "Jika dibatalkan, notifikasi WhatsApp akan dikirim ke customer!",
            icon: 'warning',
            background: '#1f2937', // warna gelap
            color: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // merah
            cancelButtonColor: '#6b7280', // abu
            confirmButtonText: '<i class="fas fa-ban"></i> Ya, Batalkan',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            customClass: {
                popup: 'rounded-xl shadow-lg animate__animated animate__shakeX',
                confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                cancelButton: 'px-4 py-2 rounded-lg font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                radio.checked = true; // tetap pilih cancelled
            } else {
                radio.checked = false; // batal, jangan ubah pilihan
            }
        });

        return false; // cegah default behavior
    }
</script>
@endpush
@endsection
