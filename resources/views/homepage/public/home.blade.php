@extends('homepage.layouts.main')

@section('title', 'Beranda')

@section('content')
    <section class="bg-gradient-to-br from-red-50 to-white py-10">
        <div x-data="{ openDepart: false, openArrival: false, depart: '{{ request('depart') ?? '' }}' }" class="max-w-6xl mx-auto px-4">
            {{-- Form Pencarian --}}
            <form action="{{ route('public.schedule') }}" method="GET" x-data="scheduleForm()" x-init="init()"
                class="bg-white shadow-lg rounded-2xl p-6 grid grid-cols-1 md:grid-cols-5 gap-6 border border-red-100">

                {{-- Dari --}}
                <div class="flex flex-col mb-4 md:mb-0">
                    <label for="depart" class="text-sm font-semibold text-gray-700 mb-1">Dari</label>
                    <div class="relative">
                        <select id="depart" name="depart" required x-model="depart" @focus="openDepart = true"
                            @blur="openDepart = false"
                            class="appearance-none w-full border border-gray-300 rounded-lg px-4 py-2 pr-10
                     focus:ring-2 focus:ring-red-500 focus:border-red-500
                     transition duration-150 shadow-sm hover:shadow-md cursor-pointer bg-white">
                            <option value="" disabled :selected="depart === ''">Pilih asal</option>
                            @foreach ($origins->unique('stop_name') as $origin)
                                <option value="{{ $origin->stop_name }}">{{ $origin->stop_name }}</option>
                            @endforeach
                        </select>

                        <span
                            class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 transition-transform duration-200"
                            :class="openDepart ? 'rotate-180' : ''">
                            ▼
                        </span>
                    </div>
                </div>

                {{-- Tujuan --}}
                <div class="flex flex-col mb-4 md:mb-0">
                    <label for="arrival" class="text-sm font-semibold text-gray-700 mb-1">Tujuan</label>
                    <div class="relative">
                        <select id="arrival" name="arrival" required @focus="openArrival = true"
                            @blur="openArrival = false"
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

                        <span
                            class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 transition-transform duration-200"
                            :class="openArrival ? 'rotate-180' : ''">
                            ▼
                        </span>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="flex flex-col mb-4 md:mb-0" x-data="datepicker()" x-init="init()">
                    <label for="date" class="text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <div class="relative">
                        <input type="text" name="date" id="date" x-model="formattedDate" readonly required
                            @click="showDatepicker = !showDatepicker"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10
                     focus:ring-2 focus:ring-red-500 focus:border-red-500
                     transition duration-150 shadow-sm hover:shadow-md cursor-pointer bg-white">

                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-500 pointer-events-none">
                            📅
                        </span>

                        <div x-show="showDatepicker" @click.away="showDatepicker = false"
                            class="absolute z-50 mt-2 bg-white border border-gray-300 rounded-lg shadow-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <button type="button" @click="prevMonth"
                                    class="text-gray-500 hover:text-red-500">⬅</button>
                                <span class="font-semibold text-gray-800" x-text="monthNames[month] + ' ' + year"></span>
                                <button type="button" @click="nextMonth"
                                    class="text-gray-500 hover:text-red-500">➡</button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium">
                                <template x-for="day in dayNames" :key="day">
                                    <div class="text-gray-500" x-text="day"></div>
                                </template>
                                <template x-for="blankday in blankDays" :key="'blank' + blankday">
                                    <div></div>
                                </template>
                                <template x-for="date in noOfDays" :key="date">
                                    <div @click="pickDate(date)" class="p-2 rounded-full cursor-pointer"
                                        :class="isToday(date) ? 'bg-red-500 text-white' :
                                            selectedDate.getDate() === date && selectedDate.getMonth() === month ?
                                            'bg-red-200 text-red-700' :
                                            'hover:bg-red-100'">
                                        <span x-text="date"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Penumpang --}}
                <div class="flex flex-col mb-4 md:mb-0" x-data="{ pax: {{ request('pax', 1) }} }">
                    <label for="pax" class="text-sm font-semibold text-gray-700 mb-1">Penumpang</label>
                    <div class="relative flex items-center">
                        <!-- Tombol Minus -->
                        <button type="button" @click="if (pax > 1) pax--"
                            class="absolute left-0 ml-2 text-gray-500 hover:text-red-500 focus:outline-none">
                            ➖
                        </button>

                        <!-- Input Number -->
                        <input type="number" name="pax" id="pax" min="1" x-model="pax" required
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-10 py-2 text-center
                     focus:ring-2 focus:ring-red-500 focus:border-red-500
                     transition duration-150 shadow-sm hover:shadow-md cursor-pointer">

                        <!-- Tombol Plus -->
                        <button type="button" @click="pax++"
                            class="absolute right-0 mr-2 text-gray-500 hover:text-red-500 focus:outline-none">
                            ➕
                        </button>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex flex-col justify-end">
                    <button type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-red-700 active:scale-95 transition duration-200 shadow-md">
                        Cek Jadwal
                    </button>
                </div>
            </form>
        </div>
    </section>



    {{-- Pesanan Terakhir --}}
    <section class="max-w-6xl mx-auto mt-10" x-data="{ open: null }">
        <h2 class="text-xl font-bold mb-3 text-gray-800">Pesanan Terakhir</h2>

        @if ($orders->isEmpty())
            <div class="bg-gray-100 text-center p-6 rounded-lg shadow-sm text-gray-500">
                Tidak ada pesanan terakhir
            </div>
        @else
            <div class="space-y-6">
                @foreach ($orders as $index => $order)
                    <div class="bg-white rounded-xl shadow-lg border border-orange-200 overflow-hidden transition transform hover:shadow-xl hover:-translate-y-1"
                        x-data="{ expanded: false }" x-init="$el.classList.add('opacity-0', 'translate-y-4');
                        setTimeout(() => $el.classList.remove('opacity-0', 'translate-y-4'), {{ $index * 100 }})">
                        {{-- Header Booking --}}
                        <div class="flex justify-between items-center bg-orange-50 px-6 py-4 cursor-pointer"
                            @click="expanded = !expanded">
                            <div>
                                <h2 class="text-lg font-bold text-orange-600">{{ $order->kode_booking }}</h2>
                                <p class="text-sm text-gray-500">Klik untuk lihat detail</p>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-white text-sm
                            {{ $order->status == 'BOOK' ? 'bg-red-500' : 'bg-green-500' }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </div>

                        {{-- Detail Booking --}}
                        <div class="px-6 py-4 border-t border-orange-100" x-show="expanded" x-transition>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Kiri --}}
                                {{-- Kiri --}}
                                <div class="space-y-2">
                                    <p><span class="font-semibold text-gray-700">Kode Pesanan:</span>
                                        {{ $order->order_code }}</p>
                                    <p><span class="font-semibold text-gray-700">Tanggal Pesan:</span>
                                        {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('l, d F Y H:i') }}
                                    </p>

                                    @if ($order->payment_status === 'belum' && $order->expired_at)
    <p class="text-red-600 font-bold">
        Waktu pembayaran berakhir pada: {{ $order->expired_at->format('H:i') }}
    </p>
@endif


                                    <p><span class="font-semibold text-gray-700">Jumlah Kursi:</span>
                                        {{ $order->seat_quantity }}</p>
                                    <p><span class="font-semibold text-gray-700">Nama Kursi:</span>
                                        @foreach ($order->passengers as $p)
                                            {{ $p->seat_number }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </p>
                                </div>


                                {{-- Kanan --}}
                                <div class="space-y-2">
                                    <p><span class="font-semibold text-gray-700">Total Harga:</span>
                                        <span class="text-green-600 font-bold">Rp
                                            {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    </p>
                                    <p><span class="font-semibold text-gray-700">Status Pembayaran:</span>
                                        <span
                                            class="{{ $order->payment_status === 'belum' ? 'text-red-600' : 'text-green-600' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </p>
                                    <p><span class="font-semibold text-gray-700">Status Pesanan:</span>
                                        {{ ucfirst($order->order_status) }}</p>
                                </div>
                            </div>

                            {{-- Tombol Bayar --}}
                            {{-- Tombol Bayar --}}
                            @if ($order->order_status === 'menunggu' && $order->payment_status === 'belum' && $order->expired_at->isFuture())
    <div class="mt-6 text-center">
        <button onclick="payNow({{ $order->id }})"
           class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
            Bayar Sekarang
        </button>
    </div>

@elseif($order->expired_at->isPast() && $order->payment_status === 'belum')
    <p class="text-red-600 font-semibold mt-4">Waktu pembayaran sudah habis. Silakan pesan ulang.</p>
@endif


                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        function datepicker() {
            return {
                showDatepicker: false,
                selectedDate: new Date(),
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                ],
                dayNames: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                noOfDays: [],
                blankDays: [],
                formattedDate: '',
                init() {
                    this.getNoOfDays();
                    this.formatDate(this.selectedDate);
                },
                formatDate(date) {
                    let day = date.getDate().toString().padStart(2, '0');
                    let month = (date.getMonth() + 1).toString().padStart(2, '0');
                    let year = date.getFullYear();
                    this.formattedDate = `${year}-${month}-${day}`;
                },
                isToday(date) {
                    const today = new Date();
                    const d = new Date(this.year, this.month, date);
                    return today.toDateString() === d.toDateString();
                },
                pickDate(date) {
                    this.selectedDate = new Date(this.year, this.month, date);
                    this.formatDate(this.selectedDate);
                    this.showDatepicker = false;
                },
                prevMonth() {
                    if (this.month === 0) {
                        this.month = 11;
                        this.year--;
                    } else {
                        this.month--;
                    }
                    this.getNoOfDays();
                },
                nextMonth() {
                    if (this.month === 11) {
                        this.month = 0;
                        this.year++;
                    } else {
                        this.month++;
                    }
                    this.getNoOfDays();
                },
                getNoOfDays() {
                    let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    let dayOfWeek = new Date(this.year, this.month, 1).getDay();
                    this.blankDays = Array.from({
                        length: dayOfWeek
                    });
                    this.noOfDays = Array.from({
                        length: daysInMonth
                    }, (_, i) => i + 1);
                }
            }
        }


        function payNow(orderId) {
    fetch(`/payment/snap-token/${orderId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        snap.pay(data.token, {
            onSuccess: function(result) {
                window.location.href = '/'; // ganti sesuai halaman sukses
            },
            onPending: function(result) {
                alert('Pembayaran pending, silakan selesaikan.');
            },
            onError: function(result) {
                alert('Pembayaran gagal.');
            },
            onClose: function() {
                alert('Anda menutup popup pembayaran.');
            }
        });
    })
    .catch(err => console.error(err));
}
    </script>
@endsection
