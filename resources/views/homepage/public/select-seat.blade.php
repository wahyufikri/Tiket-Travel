@extends('homepage.layouts.main')

@section('title', 'Pilih Kursi')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-8"
     x-data="seatFormComponent()">

    {{-- Sidebar Detail Perjalanan --}}
    <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-2xl shadow-lg border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-2" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-4H5l7-8 7 8h-4v4H9z" />
                </svg>
                Detail Perjalanan
            </h2>
            <span class="bg-red-100 text-red-600 text-xs font-semibold px-3 py-1 rounded-full">
                {{ $origin }} → {{ $destination }}
            </span>
        </div>

        <div class="space-y-2 text-gray-700">
            <p class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-2" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 9h10M5 21h14a2 2 0 002-2v-7H3v7a2 2 0 002 2z" />
                </svg>
                {{ \Carbon\Carbon::parse($trip->departure_date)->isoFormat('dddd, D MMMM Y') }}
            </p>
            <p class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3" />
                </svg>
                {{ $departure_segment }} WIB → {{ $arrival_segment }} WIB
            </p>
            <p class="flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 mr-2" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5" />
                </svg>
                {{ $pax }} Orang
            </p>
        </div>

        {{-- Rute Perjalanan --}}
        <div class="mt-5 border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold text-gray-800 mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500 mr-2" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 20l-5.447-2.724A2 2 0 013 15.382V6.618a2 2 0 011.553-1.894L9 2m6 0l5.447 2.724A2 2 0 0121 6.618v8.764a2 2 0 01-1.553 1.894L15 20" />
                </svg>
                Rute Perjalanan
            </h4>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-center">
                    <span class="text-orange-600 mr-2">🚐</span>
                    <span>{{ $origin }} - {{ $destination }}</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Pilih Kursi --}}
    @php
        $config = explode(',', $trip->vehicle->seat_configuration);
        $groupedSeats = [];
        $seenSeats = [];
        foreach ($seats as $seat) {
            if (in_array($seat->seat_number, $seenSeats)) continue;
            $row = strtoupper(substr($seat->seat_number, 0, 1));
            $groupedSeats[$row][] = $seat;
            $seenSeats[] = $seat->seat_number;
        }
    @endphp

    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
        <h2 class="text-2xl font-bold mb-4 text-red-600 flex items-center gap-2">🚍 Pilih Kursi</h2>

        <form id="seatForm" method="POST" action="{{ route('checkout') }}" @submit.prevent="handleSubmit">
            @csrf

            <div class="space-y-6 mb-6">
                @foreach ($config as $rowConfig)
                    @php
                        [$rowLabel, $count] = explode('=', $rowConfig);
                        $rowLabel = strtoupper(trim($rowLabel));
                        $seatsInRow = $groupedSeats[$rowLabel] ?? [];
                        usort($seatsInRow, fn($a, $b) => intval(substr($a->seat_number, 1)) <=> intval(substr($b->seat_number, 1)));
                    @endphp
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-lg font-bold text-gray-700">{{ $rowLabel }}</span>
                            <div class="flex gap-2">
                                @foreach ($seatsInRow as $seat)
                                    @php $isBooked = in_array($seat->seat_number, $bookedSeats); @endphp
                                    <label class="relative cursor-pointer group">
                                        <input type="checkbox" name="selected_seats[]" class="peer hidden"
                                               value="{{ $seat->seat_number }}" x-model="selectedSeats"
                                               @click="
                                                    if (!selectedSeats.includes('{{ $seat->seat_number }}') && selectedSeats.length >= max) {
                                                        $event.preventDefault();
                                                        alert('Maksimal pilih ' + max + ' kursi');
                                                    }
                                               "
                                               @if ($isBooked) disabled @endif
                                        >
                                        <div class="w-12 h-12 flex items-center justify-center rounded-lg border-2 text-sm font-medium transition-all duration-200
                                            @if ($isBooked)
                                                bg-gray-300 text-gray-500 border-gray-300 cursor-not-allowed
                                            @else
                                                bg-white text-gray-800 border-gray-300 hover:bg-red-100 hover:border-red-500
                                            @endif
                                            peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 shadow-sm group-hover:shadow-md">
                                            {{ $seat->seat_number }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="mt-2 text-xs text-gray-500 italic">
                Keterangan: A adalah barisan kursi paling depan, B barisan kedua, dst.
            </p>
            <p class="text-sm text-gray-500 mb-4">* Maksimal pilih {{ $pax }} kursi</p>

            {{-- Hidden Inputs --}}
            <input type="hidden" name="schedule_id" value="{{ $trip->id }}">
            <input type="hidden" name="pax" value="{{ $pax }}">
            <input type="hidden" name="origin" value="{{ $origin }}">
            <input type="hidden" name="destination" value="{{ $destination }}">
            <input type="hidden" name="price" value="{{ $price }}">
            @foreach ($passengerNames as $name)
                <input type="hidden" name="passenger_names[]" value="{{ $name }}">
            @endforeach

            {{-- Checkbox Terms --}}
            <label class="flex items-center space-x-3 mt-4">
                <input type="checkbox" required class="form-checkbox h-5 w-5 text-red-600">
                <span class="text-gray-700 text-sm select-none">
                    Saya telah membaca dan menyetujui
                    <a href="#" @click.prevent="showTerms = true"
                       class="text-red-600 underline hover:text-red-700 cursor-pointer">
                        Syarat & Ketentuan
                    </a>
                </span>
            </label>

            <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold text-lg hover:bg-red-700 transition-colors mt-4">
                Lanjutkan ke Konfirmasi
            </button>
        </form>
    </div>

    {{-- Modal Syarat & Ketentuan --}}
    <div x-show="showTerms" style="display:none" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
            <button @click="showTerms = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
            <h2 class="text-lg font-bold mb-4">Syarat & Ketentuan</h2>
            <p class="text-sm text-gray-600">
                1. Tiket yang sudah dibeli tidak dapat dikembalikan.<br>
                2. Harap datang 30 menit sebelum keberangkatan.<br>
                3. Penumpang wajib membawa identitas resmi.<br>
                4. Harga dapat berubah sewaktu-waktu tanpa pemberitahuan.<br>
            </p>
            <div class="mt-4 text-right">
                <button @click="showTerms = false" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Tutup</button>
            </div>
        </div>
    </div>

    {{-- Popup Pesanan Sukses --}}
    <div x-show="showSuccess" style="display:none"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="scale-90 opacity-0"
         x-transition:enter-end="scale-100 opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-red-600 text-white p-6 rounded-lg shadow-lg max-w-sm w-full text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M5 13l4 4L19 7" />
            </svg>
            <h2 class="text-lg font-bold mb-2">Pesanan Berhasil!</h2>
            <p class="text-sm text-red-100">Mengalihkan ke halaman berikutnya...</p>
        </div>
    </div>

</div>

<script>
function seatFormComponent() {
    return {
        selectedSeats: [],
        max: {{ $pax }},
        showTerms: false,
        showSuccess: false,
        handleSubmit() {
            if (this.selectedSeats.length !== this.max) {
                alert('Pilih tepat ' + this.max + ' kursi.');
                return;
            }
            this.showSuccess = true;
            setTimeout(() => {
                document.getElementById('seatForm').submit();
            }, 2000);
        }
    }
}
</script>
@endsection
