@extends('homepage.layouts.main')

@section('title', 'Checkout')

@section('content')

    <div class="max-w-4xl mx-auto py-8">
        <h2 class="text-2xl font-bold mb-4">Detail Pesanan</h2>

        <div class="bg-white p-6 rounded shadow border">
            <p class="text-red-600 font-semibold">{{ $origin }} → {{ $destination }}</p>

            <p class="text-sm">{{ \Carbon\Carbon::parse($trip->departure_date)->isoFormat('dddd, D MMMM Y') }}</p>
            <p class="text-sm">{{ $trip->departure_time }} WIB</p>

            <hr class="my-4">

            <h3 class="font-semibold mb-2">Data Pemesan</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left">No.</th>
                        <th class="text-left">Nama</th>
                        <th class="text-left">Kursi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($selectedSeats as $index => $seat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $passengerNames[$index] ?? '-' }}</td>
                            <td>{{ $seat }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr class="my-4">

            <div class="text-sm">
                <p>Harga Tiket: Rp. {{ number_format($price) }}</p>
<p>Jumlah Penumpang: {{ $pax }}</p>
<p class="font-semibold">Total Harga:
    <span class="text-red-600">Rp. {{ number_format($price * $pax) }}</span>
</p>

            </div>

            <form action="{{ route('checkout.process') }}" method="POST" class="mt-4">

                @csrf
                <!-- Hidden inputs to pass all data -->


                <input type="hidden" name="origin" value="{{ $origin }}">
<input type="hidden" name="destination" value="{{ $destination }}">
<input type="hidden" name="price" value="{{ $price }}">

                <input type="hidden" name="schedule_id" value="{{ $trip->id }}">
                <input type="hidden" name="pax" value="{{ $pax }}">
                @foreach ($selectedSeats as $index => $seat)
                    <input type="hidden" name="passenger_names[]" value="{{ $passengerNames[$index] ?? '-' }}">
                    <input type="hidden" name="selected_seats[]" value="{{ $seat }}">
                @endforeach


                <label class="block mt-2">
                    <input type="checkbox" required> Saya telah membaca Syarat & Ketentuan
                </label>

                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded mt-3">
                    Lanjutkan Pembayaran
                </button>
            </form>
        </div>
    </div>
@endsection
