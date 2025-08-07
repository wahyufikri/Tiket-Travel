@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Manajemen Pembayaran</h2>
        <a href="/payments/create" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            + Pembayaran
        </a>
    </div>

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

    <div class="mb-4">
        <form action="" method="GET" class="flex items-center max-w-sm space-x-2">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 3a7.5 7.5 0 006.15 13.65z" />
                    </svg>
                </span>
                <input type="text" name="search" placeholder="Cari pembayaran..." value="{{ request('search') }}"
                    class="bg-white w-full pl-10 pr-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Kode Order</th>
                    <th class="px-4 py-2">Total Harga</th> {{-- Tambahan --}}
                    <th class="px-4 py-2">Metode</th>
                    <th class="px-4 py-2">Bukti Bayar</th>
                    <th class="px-4 py-2">Tanggal Bayar</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Verifikator</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $index => $payment)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + $payments->firstItem() }}</td>
                        <td class="px-4 py-2">{{ $payment->order->order_code ?? '-' }}</td>
                        <td class="px-4 py-2">
                Rp {{ number_format($payment->order->total_price ?? 0, 0, ',', '.') }}
            </td>
                        <td class="px-4 py-2">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="px-4 py-2">
                            @if($payment->payment_proof)
                                <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="text-blue-500 hover:underline">Lihat</a>
                            @else
                                <span class="text-gray-500">Tidak ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y H:i') : '-' }}</td>
                        <td class="px-4 py-2">
                            @switch($payment->status)
                                @case('menunggu')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">Menunggu</span>
                                @break
                                @case('terverifikasi')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">Terverifikasi</span>
                                @break
                                @case('ditolak')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">Ditolak</span>
                                @break
                                @default
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">{{ $payment->status }}</span>
                            @endswitch
                        </td>
                        <td class="px-4 py-2">{{ $payment->verifier->name ?? '-' }}</td>
                        <td class="px-4 py-2 flex space-x-2">
                            <a href="/payments/{{ $payment->id }}/edit" class="text-yellow-500 hover:text-yellow-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/payments/{{ $payment->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="fas fa-trash text-red-500 hover:text-red-700"
                                    onclick="return confirm('Yakin akan menghapus pembayaran ini?')">
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 py-4">Tidak ada data pembayaran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection
