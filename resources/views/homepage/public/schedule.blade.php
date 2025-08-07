@extends('homepage.layouts.main')

@section('title', 'Hasil Pencarian Jadwal')

@section('content')
    <div class="max-w-6xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Form Pencarian Ulang --}}
        <div class="bg-white p-6 rounded shadow border">
            <h3 class="text-xl font-semibold mb-4">Ubah Pencarian</h3>
            <form action="{{ route('public.schedule') }}" method="GET" x-data="{ selectedOrigin: '{{ request('depart') }}' }" class="space-y-4">

                {{-- Dari --}}
<div>
    <label for="depart" class="block text-sm font-medium">Dari</label>
    <select name="depart" id="depart" x-model="selectedOrigin" class="w-full border rounded px-3 py-2" required>
        <option value="" disabled {{ !request('depart') ? 'selected' : '' }}>Pilih asal</option>
        @foreach ($stops as $loc)
            <option value="{{ $loc->stop_name }}"
                {{ request('depart') == $loc->stop_name ? 'selected' : '' }}>
                {{ $loc->stop_name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Ke --}}
<div>
    <label for="arrival" class="block text-sm font-medium">Ke</label>
    <select name="arrival" id="arrival" class="w-full border rounded px-3 py-2" required>
        <option value="" disabled {{ !request('arrival') ? 'selected' : '' }}>Pilih tujuan</option>
        @foreach ($stops as $loc)
            <option value="{{ $loc->stop_name }}"
                {{ request('arrival') == $loc->stop_name ? 'selected' : '' }}>
                {{ $loc->stop_name }}
            </option>
        @endforeach
    </select>
</div>



                {{-- Tanggal --}}
                <div>
                    <label for="date" class="block text-sm font-medium">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}"
                        class="w-full border rounded px-3 py-2" required>
                </div>

                {{-- Penumpang --}}
                <div>
                    <label for="pax" class="block text-sm font-semibold text-gray-700 mb-1">Penumpang</label>
                    @php
                        $maxSeats = $schedules->min('available_seats') ?? 10;
                    @endphp
                    <input type="number" name="pax" id="pax" min="1" max="{{ $maxSeats }}"
                        value="{{ request('pax', 1) }}" required
                        class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500" />

                    @error('pax')
                        <div class="text-xs text-red-600 mt-1 whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Tombol --}}
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                    Cek
                </button>
            </form>

        </div>

        {{-- Hasil Jadwal --}}
        <div>
            <h2 class="text-2xl font-bold mb-6">Jadwal Keberangkatan</h2>

            @if (empty($schedules) || count($schedules) == 0)
                <div class="text-center py-10">
                    <img src="{{ asset('images/notfound.jpg') }}" class="w-52 mx-auto mb-4">
                    <p class="text-gray-600">Maaf, tidak ditemukan jadwal untuk rute dan tanggal yang kamu pilih.</p>
                </div>
            @else
                @foreach ($schedules as $item)
                    @php
                        $stops = collect($item->route->stops ?? []);
                        $originStop = $stops->firstWhere('stop_name', request('depart'));
                        $destinationStop = $stops->firstWhere('stop_name', request('arrival'));
                    @endphp


                    <div class="bg-white shadow rounded p-4 mb-4 border-l-4 border-red-600">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-lg">
                                    {{ $originStop->stop_name ?? 'Asal Tidak Diketahui' }}
                                    →
                                    {{ $destinationStop->stop_name ?? 'Tujuan Tidak Diketahui' }}
                                </h4>
                                <p>{{ $item->departure_date }} | {{ $item->departure_time }}</p>
                                <p>{{ $item->available_seats }} Kursi Tersedia</p>
                                @php
                                    $routeId = $item->route_id;
                                    $originStopModel = $item->route->stops->firstWhere('stop_name', request('depart'));
                                    $destinationStopModel = $item->route->stops->firstWhere(
                                        'stop_name',
                                        request('arrival'),
                                    );

                                    $customPrice = null;
                                    if ($originStopModel && $destinationStopModel) {
                                        $customPrice = \App\Models\StopPrice::where('route_id', $routeId)
                                            ->where('from_stop_id', $originStopModel->id)
                                            ->where('to_stop_id', $destinationStopModel->id)
                                            ->value('price');

                                        // Fallback jika tidak ditemukan dalam urutan biasa
                                        if ($customPrice === null) {
                                            $customPrice = \App\Models\StopPrice::where('route_id', $routeId)
                                                ->where('from_stop_id', $destinationStopModel->id)
                                                ->where('to_stop_id', $originStopModel->id)
                                                ->value('price');
                                        }
                                    }

                                @endphp

                                <p class="text-red-700 font-semibold mt-1">
                                    Rp {{ number_format($customPrice ?? $item->route->price, 0, ',', '.') }}
                                </p>

                            </div>
                            <a href="{{ route('public.booking', [
    'schedule_id' => $item->id,
    'pax' => request('pax'),
    'date' => request('date'),
    'price' => $customPrice ?? $item->route->price,
    'origin' => request('depart'),
    'destination' => request('arrival'),
]) }}"
   class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
    Pilih
</a>

                        </div>
                    </div>
                @endforeach
            @endif
        </div>

    </div>
@endsection
