@extends('dashboard.layouts.main')

@section('content')
    <div class="max-w-xl mx-auto mt-8">
        <h2 class="text-xl font-semibold mb-4">Tambah Auto Schedule</h2>
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form action="{{ route('auto_schedule.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label>Rute</label>
                <select name="route_id" class="w-full border p-2 rounded">
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->origin }} → {{ $route->destination }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label>Kendaraan</label>
                <select name="vehicle_id" class="w-full border p-2 rounded">
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label>Sopir</label>
                <select name="driver_id" class="w-full border p-2 rounded">
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1">Pilih Hari (boleh lebih dari satu)</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ([0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'] as $i => $day)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="weekday[]" value="{{ $i }}">
                            <span>{{ $day }}</span>
                        </label>
                    @endforeach

                </div>
            </div>





            <div class="mb-4">
                <label>Jam Keberangkatan</label>
                <input type="time" name="departure_time" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label>Status</label>
                <select name="status" class="w-full border p-2 rounded">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        </form>
    </div>
@endsection
