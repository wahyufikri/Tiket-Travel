@extends('dashboard.layouts.main') <!-- Ganti sesuai layout utama kamu -->

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Edit Data Admin</h2>

        <form action="/manajemen-admin/{{ $users->id }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @method('PUT')
            @csrf

            <!-- Nama -->
            <div>
                <label for="name" class="block font-semibold">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('name',$users->name) }}" required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block font-semibold">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('email',$users->email) }}" required>
            </div>


            <!-- Role -->
            <div>
                <label for="role" class="block font-semibold">Role <span class="text-red-500">*</span></label>
                <select name="role_id" id="role"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    required>
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                @if (old('role_id',$users->role_id) == $role->id)
                    <option value="{{ $role->id }}" selected>{{ $users->role->name }}</option>
                @else
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endif
                @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
    <label class="block font-semibold mb-1">Status <span class="text-red-500">*</span></label>
    <div class="flex space-x-6">
        <label class="inline-flex items-center">
            <input type="radio" name="status" value="active"
                class="form-radio text-red-500"
                {{ old('status', $users->status ?? '') == 'active' ? 'checked' : '' }}>
            <span class="ml-2">Aktif</span>
        </label>

        <label class="inline-flex items-center">
            <input type="radio" name="status" value="inactive"
                class="form-radio text-red-500"
                {{ old('status', $users->status ?? '') == 'inactive' ? 'checked' : '' }}>
            <span class="ml-2">Tidak Aktif</span>
        </label>
    </div>
</div>


            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('manajemen-admin.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Edit</button>
            </div>
        </form>
    </div>
@endsection
