@extends('dashboard.layouts.main')

@section('content')
<div class="max-w-2xl mx-auto mt-8 bg-white rounded-xl shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-calendar-check text-green-600"></i>
        Edit Auto Schedule
    </h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('auto_schedule.update', $autoSchedule->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Rute --}}
        <div>
            <label class="block font-semibold mb-1 text-gray-700">Rute</label>
            <select name="route_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-green-200">
                @foreach ($routes as $route)
                    <option value="{{ $route->id }}" {{ $route->id == $autoSchedule->route_id ? 'selected' : '' }}>
                        {{ $route->origin }} → {{ $route->destination }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Kendaraan --}}
        <div>
            <label class="block font-semibold mb-1 text-gray-700">Kendaraan</label>
            <select name="vehicle_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-green-200">
                @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ $vehicle->id == $autoSchedule->vehicle_id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sopir --}}
        <div>
            <label class="block font-semibold mb-1 text-gray-700">Sopir</label>
            <select name="driver_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-green-200">
                @foreach ($drivers as $driver)
                    <option value="{{ $driver->id }}" {{ $driver->id == $autoSchedule->driver_id ? 'selected' : '' }}>
                        {{ $driver->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Hari --}}
        <div>
            <label class="block font-semibold mb-2 text-gray-700">Hari</label>
            <div class="grid grid-cols-4 gap-2">
                @foreach ([0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'] as $i => $day)
                    <label class="flex items-center space-x-2 bg-gray-50 rounded-lg p-2 cursor-pointer hover:bg-green-100 transition">
                        <input type="radio" name="weekday" value="{{ $i }}"
                               class="text-green-600 focus:ring-green-400"
                               {{ $autoSchedule->weekday == $i ? 'checked' : '' }}>
                        <span>{{ $day }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Waktu --}}
        <div x-data="{ selectedTime: '{{ $autoSchedule->departure_time }}' }">
            <label class="block font-semibold mb-2 text-gray-700">Jam Keberangkatan</label>

            {{-- Grid tombol jam --}}
            <div class="grid grid-cols-4 gap-2 mb-3">
                @foreach (['05:00','06:00', '07:00', '08:00', '09:00',
                           '10:00', '11:00', '12:00', '13:00',
                           '14:00', '15:00', '16:00', '17:00',
                           '18:00', '19:00', '20:00'] as $time)
                    <button type="button"
                        @click="selectedTime = '{{ $time }}'"
                        :class="selectedTime === '{{ $time }}'
                            ? 'bg-green-600 text-white'
                            : 'bg-gray-100 text-gray-700'"
                        class="px-3 py-2 rounded-lg shadow hover:bg-green-200 transition font-medium">
                        {{ $time }}
                    </button>
                @endforeach
            </div>

            {{-- Input custom --}}
            <input type="time"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-green-200"
                @input="selectedTime = $event.target.value"
                :value="selectedTime">

            {{-- Hidden input untuk dikirim ke server --}}
            <input type="hidden" name="departure_time" :value="selectedTime">

            <p class="mt-2 text-sm text-gray-600" x-show="selectedTime">
                Jam terpilih: <span class="font-semibold text-green-600" x-text="selectedTime"></span>
            </p>
        </div>

        {{-- Status --}}
        <div>
            <label class="block font-semibold mb-1 text-gray-700">Status</label>
            <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-green-200">
                <option value="aktif" {{ $autoSchedule->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ $autoSchedule->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="pt-4">
            <button class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition">
                <i class="fa-solid fa-save mr-2"></i> Update
            </button>
        </div>
    </form>
</div>
@endsection
