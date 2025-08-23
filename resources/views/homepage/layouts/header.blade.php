<header class="bg-red-700 text-white shadow"
        x-data="{ open: false, showAuthModal: false, activeTab: 'login' }">
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
    <a href="/" class="text-3xl font-extrabold tracking-tight text-white-700">
        AWR<span class="font-light text-white-800">Travel</span>
    </a>

    <nav class="relative flex items-center justify-between px-6 py-3
                bg-gradient-to-r from-red-700 via-red-600 to-red-500
                bg-opacity-95 backdrop-blur-lg shadow-lg rounded-full border border-red-400">

        <!-- Menu Kiri -->
        <div class="flex items-center space-x-6">
            <!-- Desktop Menu -->
            <ul class="hidden md:flex space-x-8 text-white font-medium">
                <li><a href="/" class="relative group">
                    Beranda
                    <span class="absolute left-0 -bottom-1 w-0 h-[2px] bg-white transition-all group-hover:w-full"></span>
                </a></li>
                
                <li><a href="/cek-reservasi" class="relative group">
                    Cek Reservasi
                    <span class="absolute left-0 -bottom-1 w-0 h-[2px] bg-white transition-all group-hover:w-full"></span>
                </a></li>



                <li><a href="/tentang-kami" class="relative group">
                    Tentang Kami
                    <span class="absolute left-0 -bottom-1 w-0 h-[2px] bg-white transition-all group-hover:w-full"></span>
                </a></li>
            </ul>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="md:hidden" x-data="{ open: false }">
            <button @click="open = !open" class="text-white focus:outline-none">
                <svg x-show="!open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" x-cloak class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Mobile Menu -->
            <ul x-show="open" x-transition
                class="absolute top-16 left-0 w-full
                       bg-gradient-to-b from-red-700 via-red-600 to-red-500
                       bg-opacity-95 backdrop-blur-lg shadow-xl rounded-b-2xl
                       text-white font-medium space-y-4 p-6 z-50">
                <li><a href="/" class="block hover:text-yellow-300 transition">Beranda</a></li>
<li><a href="/" class="block hover:text-yellow-300 transition">Cek Reservasi</a></li>
                <li><a href="/tentang" class="block hover:text-yellow-300 transition">Tentang Kami</a></li>
            </ul>
        </div>

        <!-- Menu Profil -->
        @auth('customer')
    <!-- Tombol Hamburger -->
    <div class="relative ml-4" x-data="{ showProfileMenu: false }">
        <button @click="showProfileMenu = !showProfileMenu"
                class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-red-700 shadow hover:bg-gray-100 transition">
            <!-- Ikon Hamburger -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="showProfileMenu" @click.away="showProfileMenu = false" x-transition
             class="absolute right-0 mt-3 w-44 bg-white text-gray-800 rounded-xl shadow-xl py-2 border border-gray-200">
            <a href="{{ route('customer.profile') }}"
               class="block px-4 py-2 hover:bg-gray-100 rounded-t-lg">Profil</a>
            <form action="{{ route('customer.logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 hover:bg-gray-100 rounded-b-lg">Logout</button>
            </form>
        </div>
    </div>
@else
    <!-- Tombol Login -->
    <button @click="showAuthModal = true; activeTab = 'login'"
            class="ml-4 bg-white text-red-700 px-6 py-2 rounded-full shadow-lg hover:bg-gray-200 transition font-semibold">
        Sign in
    </button>
@endauth

    </nav>
</div>

    {{-- Modal Login/Register --}}
    <!-- Tambahkan state error di Alpine -->
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



</header>
