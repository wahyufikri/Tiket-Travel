@extends('dashboard.layouts.main') <!-- Ganti sesuai layout utama kamu -->

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Admin Baru</h2>

        <form action="{{ route('manajemen-admin.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @csrf

            <!-- Nama -->
            <div>
                <label for="name" class="block font-semibold">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan Nama Pengguna" value="{{ old('name') }}" required>
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

            <!-- Kata Sandi -->
            <div>
                <label for="password" class="block font-semibold">Kata Sandi <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan Kata Sandi Baru" value="{{ old('password') }}" required>
            </div>

            <!-- Konfirmasi Sandi -->
            <div>
                <label for="password_confirmation" class="block font-semibold">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan Konfirmasi Kata Sandi" required>
                    @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block font-semibold">Role <span class="text-red-500">*</span></label>
                <select name="role_id" id="role"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    required>
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                    <!-- Tambahkan sesuai kebutuhan -->
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block font-semibold mb-1">Status <span class="text-red-500">*</span></label>
                <div class="flex space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="active" class="form-radio text-red-500" checked>
                        <span class="ml-2">Aktif</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="inactive" class="form-radio text-red-500">
                        <span class="ml-2">Tidak Aktif</span>
                    </label>
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('manajemen-admin.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tambah</button>
            </div>
        </form>
    </div>
@endsection
