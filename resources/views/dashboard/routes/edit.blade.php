@extends('dashboard.layouts.main') <!-- Ganti sesuai layout utama kamu -->

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Edit Data Rute</h2>
         @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form action="/rute/{{ $routes->id }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @method('PUT')
            @csrf

            <!-- Asal -->
            <div>
                <label for="origin" class="block font-semibold">Asal <span class="text-red-500">*</span></label>
                <input type="text" name="origin" id="origin" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('origin', $routes->origin) }}" required>
            </div>

            <!-- Tujuan -->
            <div>
                <label for="destination" class="block font-semibold">Tujuan <span class="text-red-500">*</span></label>
                <input type="text" name="destination" id="destination" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('destination', $routes->destination) }}" required>
            </div>

            <!-- Harga -->

            <!-- Durasi -->
            <div>
                <label for="duration_minutes" class="block font-semibold">Durasi Perjalanan (menit) <span class="text-red-500">*</span></label>
                <input type="number" name="duration_minutes" id="duration_minutes" min="1"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('duration_minutes', $routes->duration_minutes) }}" required>
            </div>

            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('rute.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Edit</button>
            </div>
        </form>
    </div>
@endsection
