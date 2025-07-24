@extends('homepage.layouts.main')

@section('title', 'Form Pemesanan')

@section('content')
    <div class="max-w-6xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Detail Perjalanan --}}
        <div class="bg-white p-6 rounded shadow border">
            <h2 class="text-2xl font-bold mb-4">DETAIL PERJALANAN</h2>
            <p class="text-red-600 font-semibold">{{ $trip->origin }} → {{ $trip->destination }}</p>
            <p class="mt-2 text-sm">{{ \Carbon\Carbon::parse($trip->departure_date)->isoFormat('dddd, D MMMM Y') }}</p>
            <p class="text-sm font-medium">{{ $trip->departure_time }} WIB</p>
            <p class="mt-2 text-sm">Jumlah Penumpang: {{ $pax }} Orang</p>

            {{-- Rute Perjalanan --}}
            <div class="mt-4 border-t pt-4">
                <h4 class="text-sm font-semibold mb-2">Rute Perjalanan</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center">
                        <span class="text-orange-600 mr-2">🚐</span>
                        <span>{{ $trip->route->origin }} - {{ $trip->route->destination }}</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Form Pemesanan --}}
        <div class="bg-white p-6 rounded shadow border">
            <h2 class="text-2xl font-bold mb-4">DATA PEMESAN</h2>

            <form action="{{ route('public.processBooking') }}" method="POST">

                @csrf
                <input type="hidden" name="schedule_id" value="{{ $trip->id }}">

                {{-- Data Pemesan --}}
                <div class="mb-4">
                    <input type="text" name="name" placeholder="Nama" required
                        class="w-full border px-3 py-2 rounded">
                </div>
                <div class="mb-4">
                    <input type="text" name="phone" placeholder="Nomor Telepon" required
                        class="w-full border px-3 py-2 rounded">
                </div>
                <div class="mb-4">
                    <input type="email" name="email" placeholder="Email" required
                        class="w-full border px-3 py-2 rounded">
                </div>

                {{-- Checkbox --}}
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_passenger" class="mr-2">
                        Pemesan adalah Penumpang
                    </label>
                </div>

                <h3 class="text-xl font-bold mt-6 mb-2">DATA PENUMPANG</h3>

                @for ($i = 1; $i <= $pax; $i++)
                    <div class="mb-4">
                        <input type="text" name="passenger_names[]" placeholder="Nama Penumpang {{ $i }}"
                            required class="w-full border px-3 py-2 rounded">
                    </div>
                @endfor

                {{-- Tanpa Login --}}
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="guest_checkout" class="mr-2">
                        Tanpa Login
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                    <i class="fas fa-sign-in-alt mr-2"></i> Lanjutkan
                </button>
            </form>
        </div>
    </div>
@endsection
