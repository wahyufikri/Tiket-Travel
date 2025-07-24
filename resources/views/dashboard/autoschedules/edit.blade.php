@extends('dashboard.layouts.main')

@section('content')
<div class="max-w-xl mx-auto mt-8">
    <h2 class="text-xl font-semibold mb-4">Edit Auto Schedule</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('auto_schedule.update', $autoSchedule->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Rute --}}
        <div class="mb-4">
            <label>Rute</label>
            <select name="route_id" class="w-full border p-2 rounded">
                @foreach ($routes as $route)
                    <option value="{{ $route->id }}" {{ $route->id == $autoSchedule->route_id ? 'selected' : '' }}>
                        {{ $route->origin }} → {{ $route->destination }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Kendaraan --}}
        <div class="mb-4">
            <label>Kendaraan</label>
            <select name="vehicle_id" class="w-full border p-2 rounded">
                @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ $vehicle->id == $autoSchedule->vehicle_id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sopir --}}
        <div class="mb-4">
            <label>Sopir</label>
            <select name="driver_id" class="w-full border p-2 rounded">
                @foreach ($drivers as $driver)
                    <option value="{{ $driver->id }}" {{ $driver->id == $autoSchedule->driver_id ? 'selected' : '' }}>
                        {{ $driver->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Hari --}}
        <div class="mb-4">
    <label>Hari</label>
    <select name="weekday" class="w-full border p-2 rounded">
        @foreach ([0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'] as $i => $day)
            <option value="{{ $i }}" {{ $autoSchedule->weekday == $i ? 'selected' : '' }}>
                {{ $day }}
            </option>
        @endforeach
    </select>
</div>


        {{-- Waktu --}}
        <div class="mb-4">
            <label>Jam Keberangkatan</label>
            <input type="time" name="departure_time" value="{{ $autoSchedule->departure_time }}" class="w-full border p-2 rounded">
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label>Status</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="aktif" {{ $autoSchedule->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ $autoSchedule->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
    </form>
</div>
@endsection
