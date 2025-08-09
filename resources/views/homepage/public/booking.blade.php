@extends('homepage.layouts.main')

@section('title', 'Form Pemesanan')

@section('content')
    <div x-data="{
    isPassenger: false,
    pemesanNama: '{{ Auth::guard('customer')->check() ? Auth::guard('customer')->user()->name : '' }}',
    passengerNames: Array({{ $pax }}).fill(''),
    showAuthModal: false,
    activeTab: 'login'
}"
class="max-w-6xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-2 gap-8">


        {{-- Detail Perjalanan --}}
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
            {{ $departure_segment }}  WIB → {{ $arrival_segment }} WIB
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


        {{-- Form Pemesanan --}}
        <div class="bg-white p-6 rounded shadow border">
            <h2 class="text-2xl font-bold mb-4">DATA PEMESAN</h2>

            <form action="{{ route('public.processBooking') }}" method="POST" class="space-y-5 bg-white p-6 rounded-xl shadow-lg border">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ $trip->id }}">
    <input type="hidden" name="origin" value="{{ $origin }}">
    <input type="hidden" name="destination" value="{{ $destination }}">
    <input type="hidden" name="departure_segment" value="{{ $departure_segment }}">
    <input type="hidden" name="arrival_segment" value="{{ $arrival_segment }}">



    {{-- Data Pemesan --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pemesan</label>
        <input type="text" name="name"
            value="{{ Auth::guard('customer')->check() ? Auth::guard('customer')->user()->name : '' }}"
            {{ Auth::guard('customer')->check() ? 'readonly' : '' }} required
            class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
            placeholder="Masukkan nama">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
        <input type="text" name="phone"
            value="{{ Auth::guard('customer')->check() ? Auth::guard('customer')->user()->phone : '' }}"
            {{ Auth::guard('customer')->check() ? 'readonly' : '' }} required
            class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
            placeholder="08xxxxxxxxxx">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
        <input type="email" name="email"
            value="{{ Auth::guard('customer')->check() ? Auth::guard('customer')->user()->email : '' }}"
            {{ Auth::guard('customer')->check() ? 'readonly' : '' }} required
            class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
            placeholder="email@example.com">
    </div>

    {{-- Checkbox --}}
    <div class="flex items-center space-x-2">
        <input type="checkbox" name="is_passenger" class="h-4 w-4 text-blue-600 border-gray-300 rounded"
            x-model="isPassenger"
            @change="if(isPassenger){ passengerNames[0] = pemesanNama } else { passengerNames[0] = '' }">
        <label class="text-sm text-gray-700">Pemesan adalah Penumpang</label>
    </div>

    <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Data Penumpang</h3>

    @for ($i = 1; $i <= $pax; $i++)
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Penumpang {{ $i }}</label>
            <input type="text" name="passenger_names[]" placeholder="Masukkan nama penumpang"
                x-model="passengerNames[{{ $i - 1 }}]" required
                class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
        </div>
    @endfor

    {{-- Tombol --}}
    @guest('customer')
        <button type="button" @click="showAuthModal = true; activeTab = 'login'"
            class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
            Sign In
        </button>
    @else
        <button type="submit"
            class="w-full bg-red-600 text-white py-2 rounded-lg font-semibold hover:bg-red-700 transition">
            Lanjutkan
        </button>
    @endguest
</form>

        </div>

        {{-- Modal Login/Register --}}
        <div x-show="showAuthModal" x-cloak
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 p-4"
     x-data="{
         activeTab: '{{ old('email') ? 'login' : 'register' }}',
         showError: {{ $errors->any() ? 'true' : 'false' }},
         errorMessage: '{{ $errors->first() }}'
     }"
     x-init="
        @if($errors->any())
            showAuthModal = true; // Biar modal langsung terbuka
            setTimeout(() => showError = false, 3000);
        @endif
     "
     x-transition>


    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative">

        <!-- Pop-up Error -->
        <div x-show="showError"
             x-transition
             class="absolute top-4 right-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg text-sm">
            <span x-text="errorMessage"></span>
            <button @click="showError = false" class="ml-2 font-bold">×</button>
        </div>

        <!-- Header Tabs -->
        <div class="flex justify-center bg-gray-100 p-4 border-b">
            <button @click="activeTab = 'login'"
                :class="activeTab === 'login'
                    ? 'text-red-600 font-bold border-b-2 border-red-600'
                    : 'text-gray-500 hover:text-red-500'"
                class="px-4 py-2 focus:outline-none transition">Login</button>
            <button @click="activeTab = 'register'"
                :class="activeTab === 'register'
                    ? 'text-green-600 font-bold border-b-2 border-green-600'
                    : 'text-gray-500 hover:text-green-500'"
                class="px-4 py-2 focus:outline-none transition">Register</button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <!-- Login Form -->
            <div x-show="activeTab === 'login'" x-transition>
                <form method="POST" action="{{ route('customer.login') }}" class="space-y-4">
                    @csrf
                    <input type="email" name="email" placeholder="Email" required
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-red-400 focus:outline-none text-black">
                    <input type="password" name="password" placeholder="Password" required
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-red-400 focus:outline-none text-black">
                    <button type="submit"
                            class="w-full bg-red-600 text-white py-2 rounded-lg shadow-md hover:bg-red-700 transition">Login</button>
                </form>
            </div>

            <!-- Register Form -->
            <div x-show="activeTab === 'register'" x-transition>
                <form method="POST" action="{{ route('customer.register') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Lengkap" required
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none text-black">
                    <input type="text" name="phone" placeholder="Nomor Telepon" required
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none text-black">
                    <input type="email" name="email" placeholder="Email" required
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none text-black">
                    <input type="password" name="password" placeholder="Password" required
                           class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none text-black">
                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2 rounded-lg shadow-md hover:bg-green-700 transition">Register</button>
                </form>
            </div>
        </div>

        <!-- Footer Close -->
        <div class="bg-gray-50 p-4 text-center">
            <button @click="showAuthModal = false"
                    class="text-gray-500 hover:text-red-500 text-sm font-medium transition">
                ✕ Tutup
            </button>
        </div>
    </div>
</div>
    </div>
@endsection
