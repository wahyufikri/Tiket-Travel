@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Tambah Customer Baru</h2>

    <form action="{{ route('pelanggan.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
        @csrf

        <!-- Nama -->
        <div>
            <label for="name" class="block font-semibold">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" maxlength="60"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror"
                placeholder="Masukkan Nama Customer" value="{{ old('name') }}" required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block font-semibold">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('email') border-red-500 @enderror"
                placeholder="Masukkan Email" value="{{ old('email') }}" required>
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nomor Telepon -->
        <div>
            <label for="phone" class="block font-semibold">Nomor Telepon <span class="text-red-500">*</span></label>
            <input type="text" name="phone" id="phone"
                class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('phone') border-red-500 @enderror"
                placeholder="Masukkan Nomor Telepon" value="{{ old('phone') }}" required>
            @error('phone')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kata Sandi -->
        <!-- Kata Sandi (Opsional) -->
<div>
    <label for="password" class="block font-semibold">
        Kata Sandi <span class="text-gray-500 text-sm">(opsional)</span>
    </label>
    <input type="password" name="password" id="password"
        class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500 @error('password') border-red-500 @enderror"
        placeholder="Biarkan kosong jika tidak ingin mengganti sandi">
    @error('password')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Konfirmasi Sandi (Opsional) -->
<div>
    <label for="password_confirmation" class="block font-semibold">
        Konfirmasi Kata Sandi <span class="text-gray-500 text-sm">(opsional)</span>
    </label>
    <input type="password" name="password_confirmation" id="password_confirmation"
        class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
        placeholder="Ulangi sandi jika mengubah">
</div>


        <!-- Tombol -->
        <div class="flex justify-end space-x-2 mt-6">
            <a href="{{ route('pelanggan.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tambah</button>
        </div>
    </form>
</div>
@endsection
