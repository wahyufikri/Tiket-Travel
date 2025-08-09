@extends('homepage.layouts.main')

@section('title', 'Checkout')

@section('content')
<div x-data="{ showTerms: false }" class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-extrabold text-gray-900 mb-8">Detail Pesanan</h2>

    <div class="bg-white shadow-md rounded-lg border border-gray-200 p-8">
        <div class="mb-6">
            <p class="text-red-600 font-semibold text-lg">{{ $origin }} → {{ $destination }}</p>
            <p class="text-gray-600 mt-1">
                {{ \Carbon\Carbon::parse($trip->departure_date)->isoFormat('dddd, D MMMM Y') }} &bull; {{ $departure_segment }} → {{ $arrival_segment }} WIB
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
                Total Harga: <span class="text-red-600">Rp {{ number_format($price * $pax, 0, ',', '.') }}</span>
            </p>
        </section>

        <form action="{{ route('checkout.process') }}" method="POST" class="mt-8">
            @csrf

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
                <a href="#" @click.prevent="showTerms = true" class="text-red-600 underline hover:text-red-700 cursor-pointer">
                    Syarat & Ketentuan
                </a>
            </span>
        </label>

            <button type="submit"
                class="w-full mt-6 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg shadow-md transition duration-150">
                Lanjutkan Pembayaran
            </button>
        </form>

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

            <div class="text-gray-700 text-sm leading-relaxed mb-6 space-y-4">
    <p><strong>1. Reservasi dan Pembayaran</strong><br>
    Pemesanan tiket harus dilakukan minimal 2 hari sebelum keberangkatan. Pembayaran harus lunas sebelum jadwal keberangkatan. Pembayaran yang sudah dilakukan bersifat final dan tidak dapat dikembalikan, kecuali pembatalan dilakukan oleh pihak travel.</p>

    <p><strong>2. Pembatalan dan Perubahan Jadwal</strong><br>
    Pembatalan oleh pelanggan harus diinformasikan minimal 24 jam sebelum jadwal keberangkatan. Pembatalan yang dilakukan kurang dari 24 jam sebelum keberangkatan akan dikenakan biaya pembatalan sebesar 50% dari harga tiket. Perubahan jadwal perjalanan dapat dilakukan paling lambat 24 jam sebelum keberangkatan, tergantung ketersediaan kursi.</p>

    <p><strong>3. Dokumen dan Identitas Penumpang</strong><br>
    Penumpang wajib membawa identitas resmi seperti KTP, SIM, atau paspor saat keberangkatan. Penumpang bertanggung jawab atas kelengkapan dokumen dan informasi yang diberikan.</p>

    <p><strong>4. Ketepatan Waktu</strong><br>
    Penumpang wajib hadir di titik keberangkatan paling lambat 30 menit sebelum waktu yang dijadwalkan. Keterlambatan penumpang dapat menyebabkan penumpang ditinggalkan tanpa pengembalian dana.</p>

    <p><strong>5. Bagasi dan Barang Bawaan</strong><br>
    Penumpang diperbolehkan membawa bagasi dengan berat maksimal 20 kg. Barang berbahaya, mudah terbakar, dan barang ilegal dilarang dibawa dalam perjalanan.</p>

    <p><strong>6. Kesehatan dan Keselamatan</strong><br>
    Penumpang diharapkan dalam kondisi sehat saat melakukan perjalanan. Pihak travel tidak bertanggung jawab atas kondisi kesehatan penumpang selama perjalanan.</p>

    <p><strong>7. Force Majeure</strong><br>
    Travel tidak bertanggung jawab atas keterlambatan, pembatalan, atau perubahan jadwal akibat bencana alam, kerusuhan, kecelakaan, atau keadaan darurat lainnya yang berada di luar kendali pihak travel.</p>

    <p><strong>8. Penggunaan Tiket</strong><br>
    Tiket hanya berlaku untuk penumpang yang namanya tertera pada tiket. Tiket tidak dapat dipindahtangankan tanpa persetujuan travel.</p>

    <p><strong>9. Kebijakan Perilaku Penumpang</strong><br>
    Penumpang wajib menjaga ketertiban dan mematuhi peraturan selama perjalanan. Travel berhak menurunkan penumpang yang mengganggu ketertiban atau melanggar aturan.</p>

    <p><strong>10. Lain-lain</strong><br>
    Perubahan syarat dan ketentuan dapat dilakukan sewaktu-waktu oleh pihak travel dengan pemberitahuan terlebih dahulu. Dengan menggunakan layanan travel, penumpang dianggap menyetujui seluruh syarat dan ketentuan ini.</p>
</div>


            <button @click="showTerms = false"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none"
                aria-label="Close modal">
                ✕
            </button>

            <button @click="showTerms = false"
                class="mt-4 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow transition">
                Tutup
            </button>
        </div>
    </div>
</div>
    </div>
</div>
@endsection
