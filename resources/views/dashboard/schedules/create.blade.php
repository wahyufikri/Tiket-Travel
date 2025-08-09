@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Jadwal Baru</h2>

@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <form action="{{ route('jadwal.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4"
            x-data="scheduleForm()">
            @csrf

            <!-- Rute -->
            <div>
                <label for="route" class="block font-semibold">Rute <span class="text-red-500">*</span></label>
                <select name="route_id" id="route"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('route_id') border-red-500 @enderror"
                    required x-model="selectedRoute" @change="updateArrivalTime(); updateStops()">
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
    <label for="departure_date" class="block text-sm font-medium text-gray-700 mb-1">
        <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
        Tanggal Keberangkatan <span class="text-red-500">*</span>
    </label>

    <div class="relative">
        <input
            type="date"
            name="departure_date"
            id="departure_date"
            value="{{ old('departure_date') }}"
            required
            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-gray-700
                   focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition
                   @error('departure_date') border-red-500 focus:ring-red-600 @enderror">
        <i class="fa-regular fa-calendar-days absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
    </div>

    @error('departure_date')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>


            <!-- Waktu -->
            <div class="mb-4" x-data="{ departureTime: '{{ old('departure_time') }}', custom: false }">
    <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-2">
        <i class="fa-regular fa-clock mr-1 text-gray-500"></i>
        Waktu Keberangkatan <span class="text-red-500">*</span>
    </label>

    <!-- Grid Pilihan Jam -->
    <div class="grid grid-cols-4 gap-2">
        @foreach (['05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00'] as $time)
            <button
                type="button"
                @click="departureTime = '{{ $time }}'; custom = false"
                :class="departureTime === '{{ $time }}' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-red-100'"
                class="px-3 py-2 rounded-lg shadow transition font-medium">
                {{ $time }}
            </button>
        @endforeach

        <!-- Pilihan Custom -->
        <button
            type="button"
            @click="custom = true; departureTime = ''"
            :class="custom ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-red-100'"
            class="px-3 py-2 rounded-lg shadow transition font-medium">
            Custom
        </button>
    </div>

    <!-- Input Custom Time -->
    <div x-show="custom" class="mt-3">
        <input type="time"
               id="departure_time"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500
                      @error('departure_time') border-red-500 @enderror"
               x-model="departureTime"
               @change="updateArrivalTime()"
               required>
    </div>

    <!-- Hidden input untuk submit ke server -->
    <input type="hidden" name="departure_time" :value="departureTime">

    @error('departure_time')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>


            <!-- Estimasi Tiba -->


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
                pickupStop: '',
                dropoffStop: '',
                availableStops: [],
                routeDurations: @json($routes->pluck('duration_minutes', 'id')),
                routeStops: @json($routeStopsGrouped), // dari controller: groupBy route_id

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
                },

                updateStops() {
                    this.availableStops = this.routeStops[this.selectedRoute] || [];
                }
            }
        }
    </script>
@endsection
