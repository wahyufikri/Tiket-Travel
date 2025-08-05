<header class="bg-red-700 text-white shadow">
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
        <a href="/" class="text-2xl font-bold">AWR<span class="font-light">Travel</span></a>

        <nav x-data="{ open: false }" class="relative flex items-center space-x-4">
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
                {{-- Jika belum login, tampilkan tombol login --}}
                <a href="{{ route('customer.login') }}"
                   class="bg-white text-red-700 px-4 py-2 rounded hover:bg-gray-200">Login</a>
            @endauth
        </nav>
    </div>
</header>
