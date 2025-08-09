@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Data Customer</h2>

    <form action="/pelanggan/{{ $customer->id }}" method="POST"
        class="bg-white p-6 rounded-lg shadow-md space-y-4">
        @method('PUT')
        @csrf

        <!-- Nama -->
        <div>
            <label for="name" class="block font-semibold">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" maxlength="60"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('name', $customer->name) }}" required>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block font-semibold">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('email', $customer->email) }}" required>
        </div>

        <!-- Telepon -->
        <div>
            <label for="phone" class="block font-semibold">Telepon <span class="text-red-500">*</span></label>
            <input type="text" name="phone" id="phone" maxlength="15"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                value="{{ old('phone', $customer->phone) }}" required>
        </div>

        <!-- Password (Opsional) -->
        <div>
            <label for="password" class="block font-semibold">Password Baru <span class="text-gray-500 text-sm">(opsional)</span></label>
            <input type="password" name="password" id="password"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                placeholder="Biarkan kosong jika tidak ingin mengganti password">
        </div>

        <!-- Status -->
        <div>
            <label class="block font-semibold mb-1">Status <span class="text-red-500">*</span></label>
            <div class="flex space-x-6">
                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="active"
                        class="form-radio text-red-500"
                        {{ old('status', $customer->status ?? '') == 'active' ? 'checked' : '' }}>
                    <span class="ml-2">Aktif</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="inactive"
                        class="form-radio text-red-500"
                        {{ old('status', $customer->status ?? '') == 'inactive' ? 'checked' : '' }}>
                    <span class="ml-2">Tidak Aktif</span>
                </label>
            </div>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end space-x-2 mt-6">
            <a href="{{ route('pelanggan.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Batal
            </a>
            <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Edit
            </button>
        </div>
    </form>
</div>
@endsection
