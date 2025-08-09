@extends('dashboard.layouts.main') <!-- Pastikan ini sesuai dengan layout kamu -->

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Manajemen Customer</h2>
        <a href="{{ route('pelanggan.create') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            + Customer
        </a>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div id="success-alert"
            class="mb-4 flex items-center justify-between p-4 bg-orange-100 border border-orange-400 text-orange-800 rounded-lg shadow-sm transition-opacity duration-500 ease-in-out">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()"
                class="text-green-600 hover:text-green-800 text-sm">&times;</button>
        </div>

        <script>
            setTimeout(() => {
                const alert = document.getElementById('success-alert');
                if (alert) {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 6000);
        </script>
    @endif

    {{-- Form Pencarian --}}
    <div class="mb-4">
        <form action="{{ route('pelanggan.index') }}" method="GET" class="flex items-center max-w-sm space-x-2">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 3a7.5 7.5 0 006.15 13.65z" />
                    </svg>
                </span>
                <input type="text" name="search" placeholder="Cari nama atau email..."
                    value="{{ request('search') }}"
                    class="bg-white w-full pl-10 pr-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                Cari
            </button>
        </form>
    </div>

    {{-- Tabel Data Customer --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Telepon</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $index => $customer)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                        <td class="px-4 py-2">{{ $customer->name }}</td>
                        <td class="px-4 py-2">{{ $customer->email }}</td>
                        <td class="px-4 py-2">{{ $customer->phone }}</td>
                        <td class="px-4 py-2 flex space-x-2">
                            <a href="{{ route('pelanggan.edit', $customer->id) }}"
                                class="text-yellow-500 hover:text-yellow-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('pelanggan.destroy', $customer->id) }}" method="post" class="d-inline">
                                @method('DELETE')
                                @csrf
                                <button class="fas fa-trash text-red-500 hover:text-red-700"
                                    onclick="return confirm('Yakin akan menghapus customer ini?')"></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data customer</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection
