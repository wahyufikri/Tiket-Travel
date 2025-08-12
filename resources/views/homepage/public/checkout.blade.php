@extends('homepage.layouts.main')

@section('title', 'Checkout')

@section('content')
<div x-data="{ showTerms: false }" class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-extrabold text-gray-900 mb-8">Detail Pesanan</h2>
    <div class="bg-white shadow-md rounded-lg border border-gray-200 p-8">
        <div class="mb-6">
            <p class="text-red-600 font-semibold text-lg">{{ $origin }} → {{ $destination }}</p>
            <p class="text-gray-600 mt-1">
                {{ \Carbon\Carbon::parse($trip->departure_date)->isoFormat('dddd, D MMMM Y') }}
                &bull; {{ $departure_segment }} → {{ $arrival_segment }} WIB
            </p>
        </div>
        <hr class="border-gray-300 mb-6">

        <section>
            <h3 class="text-xl font-semibold mb-4">Data Pemesan & Penumpang</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700 border border-gray-200 rounded-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b border-gray-200">No.</th>
                            <th class="py-3 px-4 border-b border-gray-200">Nama</th>
                            <th class="py-3 px-4 border-b border-gray-200">Kursi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($selectedSeats as $index => $seat)
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="py-3 px-4 border-b border-gray-200">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 border-b border-gray-200">{{ $passengerNames[$index] ?? '-' }}</td>
                                <td class="py-3 px-4 border-b border-gray-200">{{ $seat }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <hr class="border-gray-300 my-6">

        <section class="text-gray-800 text-base space-y-2">
            <p><strong>Harga Tiket:</strong> Rp {{ number_format($price, 0, ',', '.') }}</p>
            <p><strong>Jumlah Penumpang:</strong> {{ $pax }}</p>
            <p class="text-lg font-bold">
                Total Harga:
                <span class="text-red-600">Rp {{ number_format($price * $pax, 0, ',', '.') }}</span>
            </p>
        </section>

        {{-- Form hanya untuk membawa data ke Midtrans via Snap --}}
        <form id="checkout-form">
            <input type="hidden" name="origin" value="{{ $origin }}">
            <input type="hidden" name="destination" value="{{ $destination }}">
            <input type="hidden" name="price" value="{{ $price }}">
            <input type="hidden" name="schedule_id" value="{{ $trip->id }}">
            <input type="hidden" name="pax" value="{{ $pax }}">

            @foreach ($selectedSeats as $index => $seat)
                <input type="hidden" name="passenger_names[]" value="{{ $passengerNames[$index] ?? '-' }}">
                <input type="hidden" name="selected_seats[]" value="{{ $seat }}">
            @endforeach

            <label class="flex items-center space-x-3 mt-4">
                <input type="checkbox" required class="form-checkbox h-5 w-5 text-red-600" id="agreeCheckbox">
                <span class="text-gray-700 text-sm select-none">
                    Saya telah membaca dan menyetujui
                    <a href="#" @click.prevent="showTerms = true"
                       class="text-red-600 underline hover:text-red-700 cursor-pointer">
                        Syarat & Ketentuan
                    </a>
                </span>
            </label>

            <button type="button" id="pay-button"
                class="w-full mt-6 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg shadow-md transition duration-150">
                Lanjutkan Pembayaran
            </button>
        </form>

        @if(!empty($vaNumber))
    <p>Nomor VA BRI: <strong>{{ $vaNumber }}</strong></p>
@endif
        {{-- Tampilkan VA jika tersedia --}}
@if(!empty($vaNumber) && !empty($bank))
<div class="mt-6 p-4 bg-green-50 border border-green-300 rounded-lg">
    <p class="font-semibold text-green-700 mb-1">
        Nomor Virtual Account ({{ strtoupper($bank) }})
    </p>
    <p class="text-lg font-mono mb-3">{{ $vaNumber }}</p>

    {{-- Link ke simulator Midtrans --}}
    @if(app()->environment('local') || app()->environment('sandbox'))
        <a href="https://simulator.sandbox.midtrans.com/{{ strtolower($bank) }}/va/index"
           target="_blank"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow">
           Buka Simulator Pembayaran
        </a>
    @endif

    <p class="text-sm text-gray-600 mt-2">
        Silakan lakukan pembayaran sebelum batas waktu yang ditentukan.
    </p>
</div>
@endif


        {{-- Modal Syarat & Ketentuan --}}
        <div x-show="showTerms"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.away="showTerms = false"
             style="display: none;">
            <div @click.stop class="bg-white rounded-lg shadow-lg max-w-3xl w-full max-h-[80vh] overflow-y-auto p-6 relative">
                <h3 class="text-xl font-bold mb-4">Syarat & Ketentuan</h3>
                <p class="text-sm text-gray-600">
                    {{-- Isi syarat & ketentuan di sini --}}
                </p>
                <button @click="showTerms = false"
                    class="mt-4 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Script Snap Midtrans --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script type="text/javascript">
document.getElementById('pay-button').onclick = function(){
    if (!document.getElementById('agreeCheckbox').checked) {
        alert('Anda harus menyetujui Syarat & Ketentuan terlebih dahulu.');
        return;
    }
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result){
            console.log('success', result);
        },
        onPending: function(result){
            console.log('pending', result);
        },
        onError: function(result){
            console.log('error', result);
        }
    });
};
</script>
@endsection
