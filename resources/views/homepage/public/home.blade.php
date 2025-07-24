@extends('homepage.layouts.main')

@section('title', 'Beranda')

@section('content')
    <section class="bg-red-50 py-8">
        <div class="max-w-6xl mx-auto">
            <form action="{{ route('public.schedule') }}" method="GET" x-data="{ selectedOrigin: '{{ request('depart') }}' }"
                class="bg-white shadow-md rounded-xl p-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

                {{-- Origin --}}
                <div>
                    <label for="depart" class="block text-sm font-semibold text-gray-700 mb-1">Dari</label>
                    <select name="depart" id="depart" required x-model="selectedOrigin"
                        class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        <option value="" disabled selected>Pilih asal</option>
                        @foreach ($origins as $origin)
                            <option value="{{ $origin->origin }}">
                                {{ $origin->origin }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Destination --}}
                <div>
                    <label for="arrival" class="block text-sm font-semibold text-gray-700 mb-1">Tujuan</label>
                    <select name="arrival" id="arrival" required
                        class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                        <option value="" disabled selected>Pilih tujuan</option>
                        @foreach ($destinations as $destination)
                            <template x-if="selectedOrigin !== '{{ $destination->destination }}'">
                                <option value="{{ $destination->destination }}"
                                    {{ request('arrival') == $destination->destination ? 'selected' : '' }}>
                                    {{ $destination->destination }}
                                </option>
                            </template>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}" required
                        class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
                </div>

                {{-- Pax --}}
                <div>
                    <label for="pax" class="block text-sm font-semibold text-gray-700 mb-1">Penumpang</label>
                    @php
                        $maxSeats = $schedules->min('available_seats') ?? 10;
                    @endphp
                    <input type="number" name="pax" id="pax" min="1" value="{{ request('pax', 1) }}"
                        required class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" />
                </div>

                {{-- Tombol --}}
                <div>
                    <button type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-red-700 transition duration-200">
                        Cek Jadwal
                    </button>
                </div>
            </form>




        </div>
    </section>

    <section class="max-w-6xl mx-auto mt-10">
        <h2 class="text-xl font-bold mb-3">Pesanan Terakhir</h2>
        <div class="bg-gray-100 text-center p-4 rounded">
            Tidak ada pesanan terakhir
        </div>
    </section>
@endsection
