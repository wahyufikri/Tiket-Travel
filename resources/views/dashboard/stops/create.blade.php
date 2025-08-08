@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Pemberhentian Rute</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('stop.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @csrf

            <!-- Pilih Rute -->
            <div>
                <label for="route_id" class="block font-semibold">Pilih Rute <span class="text-red-500">*</span></label>
                <select name="route_id" id="route_id" required
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Rute --</option>
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                            {{ $route->origin }} - {{ $route->destination }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Urutan -->
            <div>
                <label for="stop_order" class="block font-semibold">Urutan Pemberhentian <span
                        class="text-red-500">*</span></label>
                <input type="number" name="stop_order" id="stop_order" min="1"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('stop_order') }}" required>
            </div>

            <!-- Nama Pemberhentian -->
            <div>
                <label for="stop_name" class="block font-semibold">Nama Pemberhentian <span
                        class="text-red-500">*</span></label>
                <input type="text" name="stop_name" id="stop_name" maxlength="100"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('stop_name') }}" required>
            </div>

            <!-- Waktu Tempuh dari Pemberhentian Sebelumnya -->
            <div>
                <label for="travel_minutes" class="block font-semibold">
                    Waktu Tempuh dari Pemberhentian Sebelumnya (menit) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="travel_minutes" id="travel_minutes" min="0"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('travel_minutes') }}" placeholder="Contoh: 45" required>
            </div>

            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('stop.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tambah</button>
            </div>
        </form>
    </div>
@endsection
