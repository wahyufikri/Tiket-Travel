@extends('homepage.layouts.main')

@section('title', 'Hasil Pencarian Jadwal')

@section('content')
    <div class="max-w-6xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Form Pencarian Ulang --}}
        <div x-data="{ openDepart: false, openArrival: false, depart: '{{ request('depart') ?? '' }}' }" class="bg-white p-6 rounded shadow border">
            <h3 class="text-xl font-semibold mb-4">Ubah Pencarian</h3>
            <form action="{{ route('public.schedule') }}" method="GET" x-data="{ selectedOrigin: '{{ request('depart') }}' }"
                class="bg-white shadow-lg rounded-xl p-6 space-y-5 border border-gray-100">

                {{-- Dari --}}
                <div class="flex flex-col mb-4 md:mb-0">
        <label for="depart" class="text-sm font-semibold text-gray-700 mb-1">Dari</label>
        <div class="relative">
          <select id="depart" name="depart" required
              x-model="depart"
              @focus="openDepart = true" @blur="openDepart = false"
              class="appearance-none w-full border border-gray-300 rounded-lg px-4 py-2 pr-10
                     focus:ring-2 focus:ring-red-500 focus:border-red-500
                     transition duration-150 shadow-sm hover:shadow-md cursor-pointer bg-white">
              <option value="" disabled :selected="depart === ''">Pilih asal</option>
              @foreach ($origins->unique('stop_name') as $origin)
                <option value="{{ $origin->stop_name }}">{{ $origin->stop_name }}</option>
              @endforeach
          </select>

          <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 transition-transform duration-200"
                :class="openDepart ? 'rotate-180' : ''">
            ▼
          </span>
        </div>
      </div>

      {{-- Tujuan --}}
      <div class="flex flex-col mb-4 md:mb-0">
        <label for="arrival" class="text-sm font-semibold text-gray-700 mb-1">Tujuan</label>
        <div class="relative">
          <select id="arrival" name="arrival" required
              @focus="openArrival = true" @blur="openArrival = false"
              class="appearance-none w-full border border-gray-300 rounded-lg px-4 py-2 pr-10
                     focus:ring-2 focus:ring-red-500 focus:border-red-500
                     transition duration-150 shadow-sm hover:shadow-md cursor-pointer bg-white">
              <option value="" disabled selected>Pilih tujuan</option>
              @foreach ($destinations->unique('stop_name') as $destination)
                <template x-if="depart !== '{{ $destination->stop_name }}'">
                  <option value="{{ $destination->stop_name }}">{{ $destination->stop_name }}</option>
                </template>
              @endforeach
          </select>

          <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 transition-transform duration-200"
                :class="openArrival ? 'rotate-180' : ''">
            ▼
          </span>
        </div>
      </div>

                {{-- Tanggal --}}
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <div class="relative">
                        <!-- Icon kalender -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>

                        <!-- Input date -->
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm hover:border-red-400 transition"
                            required>
                    </div>
                </div>


                {{-- Penumpang --}}
                <div>
                    <label for="pax" class="block text-sm font-semibold text-gray-700 mb-1">Penumpang</label>
                    @php
                        $maxSeats = $schedules->min('available_seats') ?? 10;
                    @endphp
                    <input type="number" name="pax" id="pax" min="1" max="{{ $maxSeats }}"
                        value="{{ request('pax', 1) }}" required
                        class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500" />

                    @error('pax')
                        <div class="text-xs text-red-600 mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div>
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 rounded-lg font-semibold shadow hover:from-red-600 hover:to-red-700 transition duration-200">
                        Cek Jadwal
                    </button>
                </div>
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
                                @php
                                    $departureSegment = null;
                                    $arrivalSegment = null;

                                    if (
                                        $originStop &&
                                        $destinationStop &&
                                        isset($originStop->travel_minutes) &&
                                        isset($destinationStop->travel_minutes)
                                    ) {
                                        // Jam berangkat segmen
                                        $departureSegment = \Carbon\Carbon::parse($item->departure_time)->addMinutes(
                                            $originStop->travel_minutes,
                                        );

                                        // Jam tiba segmen
                                        $arrivalSegment = \Carbon\Carbon::parse($item->departure_time)->addMinutes(
                                            $destinationStop->travel_minutes,
                                        );
                                    }
                                @endphp

                                @if ($departureSegment && $arrivalSegment)
                                    <p>{{ $item->departure_date }} | {{ $departureSegment->format('H:i') }} -
                                        {{ $arrivalSegment->format('H:i') }}</p>
                                @endif







                                @php
                                    $originStopModel = $item->route->stops->firstWhere('stop_name', request('depart'));
                                    $destinationStopModel = $item->route->stops->firstWhere(
                                        'stop_name',
                                        request('arrival'),
                                    );

                                    // Hitung jumlah kursi kendaraan
                                    $totalSeats = \App\Models\Seat::where('vehicle_id', $item->vehicle_id)->count();

                                    // Hitung kursi yang sudah terpakai di segmen yang overlap
                                    $bookedSeats = \App\Models\Booking::join(
                                        'route_stops as rs_from',
                                        'bookings.from_stop_id',
                                        '=',
                                        'rs_from.id',
                                    )
                                        ->join('route_stops as rs_to', 'bookings.to_stop_id', '=', 'rs_to.id')
                                        ->where('bookings.schedule_id', $item->id)
                                        ->where('rs_from.route_id', $item->route_id)
                                        ->where('rs_to.route_id', $item->route_id)
                                        ->where(function ($query) use ($originStopModel, $destinationStopModel) {
                                            $query
                                                ->where('rs_from.stop_order', '<', $destinationStopModel->stop_order)
                                                ->where('rs_to.stop_order', '>', $originStopModel->stop_order);
                                        })
                                        ->distinct('bookings.seat_id')
                                        ->count('bookings.seat_id');

                                    $availableSeats = $totalSeats - $bookedSeats;
                                @endphp

                                <p>{{ $availableSeats }} Kursi Tersedia</p>

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
                                'departure_segment' => $departureSegment ? $departureSegment->format('H:i') : null,
                                'arrival_segment' => $arrivalSegment ? $arrivalSegment->format('H:i') : null,
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
