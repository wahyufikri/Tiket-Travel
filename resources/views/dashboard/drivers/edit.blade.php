@extends('dashboard.layouts.main') <!-- Ganti sesuai layout utama kamu -->

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Edit Data Sopir</h2>

        <form action="/sopir/{{ $drivers->id }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @method('PUT')
            @csrf

            <!-- Nama -->
            <div>
                <label for="name" class="block font-semibold">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('name', $drivers->name) }}" required>
            </div>

            <div>
                <label for="phone_number" class="block font-semibold">No HP <span class="text-red-500">*</span></label>
                <input type="text" name="phone_number" id="phone_number" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('phone_number', $drivers->phone_number) }}" required>
            </div>
            <div>
                <label for="address" class="block font-semibold">Alamat <span class="text-red-500">*</span></label>
                <input type="text" name="address" id="address" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('address', $drivers->address) }}" required>
            </div>
            <div class="mb-4">
                <label for="current_location" class="block text-sm font-medium text-gray-700">Lokasi Terkini</label>
                <input type="text" name="current_location" id="current_location"
                    value="{{ old('current_location', $drivers->current_location ?? '') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>




            <!-- Status -->
            <div>
                <label class="block font-semibold mb-1">Status <span class="text-red-500">*</span></label>
                <div class="flex space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="active" class="form-radio text-red-500"
                            {{ old('status', $drivers->status ?? '') == 'active' ? 'checked' : '' }}>
                        <span class="ml-2">Aktif</span>
                    </label>

                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="inactive" class="form-radio text-red-500"
                            {{ old('status', $drivers->status ?? '') == 'inactive' ? 'checked' : '' }}>
                        <span class="ml-2">Tidak Aktif</span>
                    </label>
                </div>
            </div>


            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('sopir.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Edit</button>
            </div>
        </form>
    </div>
@endsection
