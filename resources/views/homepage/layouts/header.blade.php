<header class="bg-red-700 text-white shadow"
        x-data="{ open: false, showAuthModal: false, activeTab: 'login' }">
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
        <a href="/" class="text-2xl font-bold">AWR<span class="font-light">Travel</span></a>

        <nav class="relative flex items-center space-x-4">
            {{-- Menu Utama --}}
            <div class="relative">
                <button @click="open = !open" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <ul :class="{'block': open, 'hidden': !open}"
                    class="absolute md:static md:flex space-x-4 bg-red-700 md:bg-transparent p-4 md:p-0 rounded md:rounded-none z-10 md:z-auto hidden">
                    <li><a href="/" class="hover:underline">Beranda</a></li>
                    <li><a href="/berita" class="hover:underline">Berita</a></li>
                    <li><a href="/cek-reservasi" class="hover:underline">Cek Reservasi</a></li>
                    <li><a href="/tentang" class="hover:underline">Tentang Kami</a></li>
                </ul>
            </div>

            {{-- Menu Profil jika login --}}
            @auth('customer')
                <div class="relative" x-data="{ showProfileMenu: false }">
                    <button @click="showProfileMenu = !showProfileMenu" class="flex items-center space-x-2">
                        <span>{{ Auth::guard('customer')->user()->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="showProfileMenu" @click.away="showProfileMenu = false"
                         class="absolute right-0 mt-2 w-40 bg-white text-black rounded shadow-lg py-2 z-50">
                        <a href="{{ route('customer.profile') }}" class="block px-4 py-2 hover:bg-gray-100">Profil</a>
                        <form action="{{ route('customer.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Jika belum login, munculkan modal login --}}
                <button @click="showAuthModal = true; activeTab = 'login'"
                        class="bg-white text-red-700 px-4 py-2 rounded hover:bg-gray-200">
                    Login
                </button>
            @endauth
        </nav>
    </div>

    {{-- Modal Login/Register --}}
    <div x-show="showAuthModal" x-cloak
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
            <div class="flex justify-between mb-4 border-b pb-2">
                <button @click="activeTab = 'login'"
                        :class="activeTab === 'login' ? 'font-bold text-red-600' : 'text-gray-500'">Login</button>
                <button @click="activeTab = 'register'"
                        :class="activeTab === 'register' ? 'font-bold text-red-600' : 'text-gray-500'">Register</button>
            </div>

            {{-- Login Form --}}
            <div x-show="activeTab === 'login'">
                <form method="POST" action="{{ route('customer.login') }}">
                    @csrf
                    <input type="email" name="email" placeholder="Email" required
                           class="w-full border px-3 py-2 rounded mb-3 text-black">
                    <input type="password" name="password" placeholder="Password" required
                           class="w-full border px-3 py-2 rounded mb-3 text-black">
                    <button type="submit"
                            class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">Login</button>
                </form>
            </div>

            {{-- Register Form --}}
            <div x-show="activeTab === 'register'">
                <form method="POST" action="{{ route('customer.register') }}">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Lengkap" required
                           class="w-full border px-3 py-2 rounded mb-3 text-black">
                    <input type="text" name="phone" placeholder="Nomor Telepon" required
                           class="w-full border px-3 py-2 rounded mb-3 text-black">
                    <input type="email" name="email" placeholder="Email" required
                           class="w-full border px-3 py-2 rounded mb-3 text-black">
                    <input type="password" name="password" placeholder="Password" required
                           class="w-full border px-3 py-2 rounded mb-3 text-black">
                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Register</button>
                </form>
            </div>

            <button @click="showAuthModal = false" class="mt-4 text-gray-600 hover:underline text-sm">Tutup</button>
        </div>
    </div>
</header>
