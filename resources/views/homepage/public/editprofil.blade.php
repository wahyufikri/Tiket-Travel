@extends('homepage.layouts.main')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded p-6">
    <h1 class="text-2xl font-bold text-red-700 mb-4">Perbarui Profil</h1>

    <form action="{{ route('customer.updateProfile') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Nama</label>
            <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">No HP</label>
            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Alamat</label>
            <textarea name="address" class="w-full border rounded px-3 py-2">{{ old('address', $customer->address) }}</textarea>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800">Simpan Perubahan</button>
            <a href="{{ route('customer.profile') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
        </div>
    </form>
</div>
@endsection
