<header class="bg-red-700 text-white shadow">
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
        <a href="/" class="text-2xl font-bold">AWR<span class="font-light">Travel</span></a>

        <nav x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="md:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <ul :class="{'block': open, 'hidden': !open}" class="absolute md:static md:flex space-x-4 bg-red-700 md:bg-transparent p-4 md:p-0 rounded md:rounded-none z-10 md:z-auto hidden">
                <li><a href="/" class="hover:underline">Beranda</a></li>
                <li><a href="/outlet" class="hover:underline">Outlet</a></li>
                <li><a href="/paket" class="hover:underline">Paket</a></li>
                <li><a href="/berita" class="hover:underline">Berita</a></li>
                <li><a href="/cek-reservasi" class="hover:underline">Cek Reservasi</a></li>
                <li><a href="/tentang" class="hover:underline">Tentang Kami</a></li>
            </ul>
        </nav>
    </div>
</header>
