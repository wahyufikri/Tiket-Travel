@extends('homepage.layouts.main')

@section('content')
<div class="max-w-7xl mx-auto p-4" x-data="{ editMode: false }">
    <h1 class="text-2xl font-bold text-red-700 mb-4">Profil Saya</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Kolom Kiri --}}
        <div class="bg-white shadow rounded p-4 flex flex-col items-center">
            {{-- Foto Profil --}}
            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-4xl">
                <i class="fas fa-user"></i>
            </div>

            {{-- Jika tidak sedang edit --}}
            <template x-if="!editMode">
                <div class="text-center mt-4">
                    <h2 class="font-bold text-lg">{{ Auth::guard('customer')->user()->name }}</h2>
                    <p class="text-gray-600">{{ Auth::guard('customer')->user()->email }}</p>
                    <p class="text-gray-600">{{ Auth::guard('customer')->user()->phone }}</p>
                    <p class="text-gray-600">{{ Auth::guard('customer')->user()->address }}</p>
                </div>
            </template>

            {{-- Jika sedang edit --}}
            <template x-if="editMode">
                <form action="{{ route('customer.updateProfile') }}" method="POST" class="mt-4 w-full">
                    @csrf
                    <div class="mb-2">
                        <label class="block text-sm font-medium">Nama</label>
                        <input type="text" name="name" value="{{ Auth::guard('customer')->user()->name }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ Auth::guard('customer')->user()->email }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium">No HP</label>
                        <input type="text" name="phone" value="{{ Auth::guard('customer')->user()->phone }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800">Simpan</button>
                        <button type="button" @click="editMode = false" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
                    </div>
                </form>
            </template>

            {{-- Tombol Update & Logout --}}
            <div class="mt-4 flex space-x-2" x-show="!editMode">
                <button @click="editMode = true" class="bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800">Perbarui</button>
                <form action="{{ route('customer.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">Logout</button>
                </form>
            </div>

            {{-- Promo --}}
            <div class="mt-6 w-full bg-red-50 border border-red-200 rounded p-3 text-sm">
                <p class="font-bold text-red-700">🎓 Khusus Mahasiswa!</p>
                <ul class="list-disc ml-4 text-gray-700 mt-1">
                    <li>Diskon eksklusif</li>
                    <li>Akses prioritas</li>
                    <li>Promo menarik lainnya</li>
                </ul>
                <a href="#" class="text-blue-600 hover:underline mt-2 block">Klik disini untuk melakukan pendaftaran.</a>
            </div>
        </div>

        {{-- Kolom Kanan Riwayat --}}
        <div class="md:col-span-2 bg-white shadow rounded p-4">
            <div class="flex items-center justify-between border-b pb-2 mb-4">
                <h3 class="font-bold text-lg">Riwayat Transaksi</h3>
                <select class="border rounded px-2 py-1 text-sm">
                    <option>Semua Transaksi</option>
                    <option>Berhasil</option>
                    <option>Dibatalkan</option>
                </select>
            </div>

            @forelse($orders as $order)
                <div class="border rounded p-3 mb-3">
                    <p class="text-red-700 font-bold">{{ $order->route }}</p>
                    <p>{{ $order->passenger_count }} Penumpang</p>
                    <p>{{ \Carbon\Carbon::parse($order->departure_time)->translatedFormat('l, d F Y') }} - {{ \Carbon\Carbon::parse($order->departure_time)->format('H:i') }} WIB</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm font-mono">{{ $order->code }}</span>
                        @if($order->status === 'dibatalkan')
                            <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Dibatalkan</span>
                        @else
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Berhasil</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Belum ada riwayat transaksi.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
