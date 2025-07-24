@extends('homepage.layouts.main')

@section('title', 'Pilih Kursi')

@section('content')
    <div class="max-w-6xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Sidebar Detail Perjalanan --}}
        <div class="bg-white p-6 rounded shadow border">
            <h2 class="text-2xl font-bold mb-4">DETAIL PERJALANAN</h2>
            <p class="text-red-600 font-semibold">{{ $trip->origin }} → {{ $trip->destination }}</p>
            <p class="mt-2 text-sm">{{ \Carbon\Carbon::parse($trip->departure_date)->isoFormat('dddd, D MMMM Y') }}</p>
            <p class="text-sm font-medium">{{ $trip->departure_time }} WIB</p>
            <p class="mt-2 text-sm">Jumlah Penumpang: {{ $pax }} Orang</p>

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

        {{-- Pilih Kursi --}}
        @php
            $config = explode(',', $trip->vehicle->seat_configuration); // Contoh: A=2,B=3,...
            $groupedSeats = [];

            foreach ($seats as $seat) {
                $row = strtoupper(substr($seat->seat_number, 0, 1));
                $groupedSeats[$row][] = $seat;
            }
        @endphp

        <div class="bg-white p-6 rounded shadow border" x-data="{
            selectedSeats: [],
            max: {{ $pax }}
        }" x-init="console.log('MAX SEATS:', max)">

            <h2 class="text-2xl font-bold mb-4">PILIH KURSI</h2>


            <form id="seatForm" method="POST" action="{{ route('checkout.show') }}"
                @submit.prevent="
        if (selectedSeats.length !== max) {
            alert('Pilih tepat ' + max + ' kursi.');
        } else {
            document.getElementById('seatForm').submit();
        }
    ">


                @csrf

                <div class="space-y-4 mb-6">
                    @foreach ($config as $rowConfig)
                        @php
                            [$rowLabel, $count] = explode('=', $rowConfig);
                            $rowLabel = strtoupper(trim($rowLabel));
                            $seatsInRow = $groupedSeats[$rowLabel] ?? [];

                            // Urutkan seat_number seperti A1, A2, dst.
                            usort(
                                $seatsInRow,
                                fn($a, $b) => intval(substr($a->seat_number, 1)) <=> intval(substr($b->seat_number, 1)),
                            );
                        @endphp

                        <div class="flex items-center space-x-2">
                            <div class="w-12 font-bold text-center">{{ $rowLabel }}</div>

                            @foreach ($seatsInRow as $seat)
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="selected_seats[]" class="peer hidden"
                                        :value="'{{ $seat->seat_number }}'" x-model="selectedSeats"
                                        @click="
        if (!selectedSeats.includes('{{ $seat->seat_number }}') && selectedSeats.length >= max) {
            $event.preventDefault();
            alert('Maksimal pilih ' + max + ' kursi');
        }
    "
                                        @if ($seat->is_booked)
                                    disabled
                            @endif
                            />




                            <div
                                class="w-12 h-12 flex items-center justify-center rounded border text-sm font-medium
                                    @if ($seat->is_booked) bg-gray-300 text-gray-500 cursor-not-allowed
                                    @else
                                        bg-white text-black hover:bg-gray-100 @endif
                                    peer-checked:bg-red-600 peer-checked:text-white">
                                {{ $seat->seat_number }}
                            </div>
                            </label>
                    @endforeach
                </div>
                @endforeach
        </div>
        <p class="mt-2 text-xs text-gray-500 italic">
            Keterangan: A adalah barisan kursi paling depan, B barisan kedua, dan begitu seterusnya.
        </p>

        <p class="text-sm text-gray-500 mb-4">* Maksimal pilih {{ $pax }} kursi</p>

        <input type="hidden" name="schedule_id" value="{{ $trip->id }}">
        <input type="hidden" name="pax" value="{{ $pax }}">
        @foreach ($passengerNames as $name)
    <input type="hidden" name="passenger_names[]" value="{{ $name }}">
@endforeach


        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
            Lanjutkan ke Konfirmasi
        </button>
        </form>
    </div>

    </div>
@endsection
