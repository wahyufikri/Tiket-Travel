@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Jadwal Baru</h2>


        <form action="{{ route('jadwal.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4"
            x-data="scheduleForm()">
            @csrf

            <!-- Rute -->
            <div>
                <label for="route" class="block font-semibold">Rute <span class="text-red-500">*</span></label>
                <select name="route_id" id="route"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('route_id') border-red-500 @enderror"
                    required x-model="selectedRoute" @change="updateArrivalTime()">
                    <option value="">-- Pilih Rute --</option>
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                            {{ $route->origin }} → {{ $route->destination }}
                        </option>
                    @endforeach
                </select>
                @error('route_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kendaraan -->
            <div>
                <label for="vehicle" class="block font-semibold">Kendaraan <span class="text-red-500">*</span></label>
                <select name="vehicle_id" id="vehicle"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('vehicle_id') border-red-500 @enderror"
                    required x-data
                    x-on:change="
        $el.dataset.capacities
            ? document.getElementById('available_seats').value = $el.dataset.capacities.split(',')[$el.selectedIndex - 1]
            : ''
    "
                    data-capacities="{{ $vehicles->pluck('capacity')->implode(',') }}">
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->vehicle_name }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sopir -->
            <div>
                <label for="driver" class="block font-semibold">Sopir <span class="text-red-500">*</span></label>
                <select name="driver_id" id="driver"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('driver_id') border-red-500 @enderror"
                    required>
                    <option value="">-- Pilih Sopir --</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
                @error('driver_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal -->
            <div class="mb-4">
                <label for="departure_date" class="block font-semibold">
                    Tanggal Keberangkatan <span class="text-red-500">*</span>
                </label>
                <input type="date" name="departure_date" id="departure_date"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('departure_date') border-red-500 @enderror"
                    value="{{ old('departure_date') }}" required>
                @error('departure_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Waktu -->
            <div class="mb-4">
                <label for="departure_time" class="block font-semibold">
                    Waktu Keberangkatan <span class="text-red-500">*</span>
                </label>
                <input type="time" name="departure_time" id="departure_time"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('departure_time') border-red-500 @enderror"
                    x-model="departureTime" @change="updateArrivalTime()" value="{{ old('departure_time') }}" required>
                @error('departure_time')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estimasi Tiba -->
            <div class="mb-4">
                <label class="block font-semibold">
                    Estimasi Waktu Tiba
                </label>
                <p x-text="arrivalTime || '--:--'" class="text-gray-700 mt-1 font-semibold"></p>
            </div>


            <!-- Jumlah Kursi -->
            <!-- Jumlah Kursi -->
            <div class="mb-4">
                <label for="available_seats" class="block font-semibold">
                    Jumlah Kursi Tersedia <span class="text-red-500">*</span>
                </label>
                <input type="number" name="available_seats" id="available_seats" min="1"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('available_seats') border-red-500 @enderror"
                    placeholder="Masukkan jumlah kursi" value="{{ old('available_seats') }}" required>
                @error('available_seats')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            

            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('jadwal.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tambah</button>
            </div>
        </form>
    </div>

    <!-- Alpine.js Script -->
    <script>
        function scheduleForm() {
            return {
                departureTime: '',
                arrivalTime: '',
                selectedRoute: '',
                routeDurations: @json($routes->pluck('duration_minutes', 'id')), // dalam menit

                updateArrivalTime() {
                    if (!this.departureTime || !this.selectedRoute) {
                        this.arrivalTime = '--:--';
                        return;
                    }

                    const duration = parseInt(this.routeDurations[this.selectedRoute] || 0);
                    const [hours, minutes] = this.departureTime.split(':').map(Number);

                    const waktuBerangkat = new Date();
                    waktuBerangkat.setHours(hours);
                    waktuBerangkat.setMinutes(minutes + duration);

                    const jam = waktuBerangkat.getHours().toString().padStart(2, '0');
                    const menit = waktuBerangkat.getMinutes().toString().padStart(2, '0');
                    this.arrivalTime = `${jam}:${menit}`;
                }
            }
        }
    </script>
@endsection
