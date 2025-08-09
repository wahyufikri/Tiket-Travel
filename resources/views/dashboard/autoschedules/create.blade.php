@extends('dashboard.layouts.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tambah Auto Schedule
        </h2>

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-md shadow-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('auto_schedule.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Rute --}}
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Rute</label>
                <select name="route_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->origin }} → {{ $route->destination }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Kendaraan --}}
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Kendaraan</label>
                <select name="vehicle_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sopir --}}
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Sopir</label>
                <select name="driver_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Hari --}}
<div x-data="{
    selectAll: false,
    toggleAll() {
        const checkboxes = document.querySelectorAll('input[name=\'weekday[]\']');
        checkboxes.forEach(cb => cb.checked = this.selectAll);
    }
}">
    <label class="block font-semibold mb-2 text-gray-700">Pilih Hari (boleh lebih dari satu)</label>

    {{-- Checkbox Pilih Semua --}}
    <label class="flex items-center space-x-2 bg-blue-50 rounded-lg p-2 hover:bg-blue-100 transition mb-2">
        <input type="checkbox" x-model="selectAll" @change="toggleAll"
            class="text-blue-600 focus:ring-blue-400 rounded">
        <span class="font-medium text-blue-700">Pilih Semua</span>
    </label>

    {{-- Daftar Hari --}}
    <div class="grid grid-cols-2 gap-2">
        @foreach ([0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'] as $i => $day)
            <label class="flex items-center space-x-2 bg-gray-50 rounded-lg p-2 hover:bg-gray-100 transition">
                <input type="checkbox" name="weekday[]" value="{{ $i }}"
                    class="text-blue-600 focus:ring-blue-400 rounded">
                <span>{{ $day }}</span>
            </label>
        @endforeach
    </div>
</div>


            {{-- Jam Keberangkatan --}}
            {{-- Jam Keberangkatan --}}
<div x-data="{ selectedTime: '' }">
    <label class="block font-semibold mb-1 text-gray-700">Jam Keberangkatan</label>

    {{-- Grid Pilihan Jam --}}
    <div class="grid grid-cols-4 gap-2 mb-3">
        @foreach ([
            '05:00','06:00', '07:00', '08:00', '09:00',
            '10:00', '11:00', '12:00', '13:00',
            '14:00', '15:00', '16:00', '17:00',
            '18:00', '19:00', '20:00'
        ] as $time)
            <button type="button"
                @click="selectedTime = '{{ $time }}'"
                :class="selectedTime === '{{ $time }}'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-700'"
                class="px-3 py-2 rounded-lg shadow hover:bg-blue-200 transition font-medium">
                {{ $time }}
            </button>
        @endforeach
    </div>

    {{-- Input custom --}}
    <input type="time"
           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
           @input="selectedTime = $event.target.value">

    <input type="hidden" name="departure_time" :value="selectedTime">

    <p class="mt-2 text-sm text-gray-600" x-show="selectedTime">
        Jam terpilih: <span class="font-semibold text-blue-600" x-text="selectedTime"></span>
    </p>
</div>



            {{-- Status --}}
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#departure_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 5
        });
    </script>
@endpush
@endsection
