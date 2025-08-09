{{-- resources/views/checkout/failed.blade.php --}}
@extends('homepage.layouts.main')

@section('content')
<div class="max-w-2xl mx-auto mt-12 bg-white rounded-xl shadow-lg p-8 text-center">

    {{-- Icon silang --}}
    <div class="flex justify-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </div>

    {{-- Judul --}}
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Pembayaran Gagal!</h1>

    {{-- Pesan --}}
    <p class="text-gray-600 mb-6">
        Maaf, pembayaran Anda gagal diproses. Silakan coba lagi atau hubungi layanan pelanggan jika memerlukan bantuan.
    </p>

    {{-- Tombol aksi --}}
    <div class="flex justify-center gap-4">
        <a href="{{ route('checkout.payment', ['order' => $order->id]) }}"
   class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded shadow">
   Coba Lagi
</a>

        <a href="{{ url('') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded shadow">
           Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
