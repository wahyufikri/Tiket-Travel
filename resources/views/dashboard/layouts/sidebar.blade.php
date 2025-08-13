<!-- resources/views/layouts/sidebar.blade.php -->
<aside x-data="{ open: false, showLogoutConfirm: false }" class="w-64 h-screen bg-[#8B0000] text-white fixed ">

    <a href="{{ url('/dashboard') }}" class="p-6 font-bold text-lg flex flex-col items-center space-y-2">
    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="w-20 h-20 object-contain">
    <span>AWR TRAVEL</span>
</a>


    <nav class="space-y-2 px-4">
    <a href="{{ url('/kendaraan') }}" class="flex items-center gap-2 py-2 px-4 rounded hover:bg-[#a83232]">
        <!-- Truck Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17H6a2 2 0 00-2 2v1h16v-1a2 2 0 00-2-2h-3"></path>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h3v-2a2 2 0 012-2h3v4H3z"></path>
        </svg>
        Kendaraan
    </a>

    <a href="{{ url('/sopir') }}" class="flex items-center gap-2 py-2 px-4 rounded hover:bg-[#a83232]">
        <!-- User Icon (Driver) -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5.121 17.804A4 4 0 015 16V7a4 4 0 014-4h6a4 4 0 014 4v9a4 4 0 01-.121.804M16 21v-2a4 4 0 00-8 0v2"></path>
        </svg>
        Sopir
    </a>

    <div x-data="{ open: false }" class="transition-all">
    <button @click="open = !open"
        class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-[#a83232] cursor-pointer">
        <div class="flex items-center space-x-2">
            <!-- Map Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 20l-5.447-2.724A2 2 0 013 15.382V5.618a2 2 0 011.553-1.947L9 1v19zm6-19l5.447 2.724A2 2 0 0121 5.618v9.764a2 2 0 01-1.553 1.947L15 20V1z"></path>
            </svg>
            <span>Rute</span>
        </div>
        <!-- Panah Dropdown -->
        <svg :class="{ 'rotate-90': open }"
             class="h-4 w-4 transform transition-transform duration-300"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Isi dropdown -->
    <div x-show="open" x-transition x-cloak
         class="ml-4 mt-1 space-y-1 pl-2 border-l border-[#c34f4f]">
        <a href="{{ url('/rute') }}"
           class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 20l-5.447-2.724A2 2 0 013 15.382V5.618a2 2 0 011.553-1.947L9 1v19zm6-19l5.447 2.724A2 2 0 0121 5.618v9.764a2 2 0 01-1.553 1.947L15 20V1z"></path>
            </svg>
            <span>Daftar Rute</span>
        </a>
        <a href="{{ url('/stop') }}"
           class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 20l-5.447-2.724A2 2 0 013 15.382V5.618a2 2 0 011.553-1.947L9 1v19zm6-19l5.447 2.724A2 2 0 0121 5.618v9.764a2 2 0 01-1.553 1.947L15 20V1z"></path>
            </svg>
            <span>Rute Stop</span>
        </a>
    </div>
</div>


    <!-- Jadwal Dropdown -->
<div x-data="{ open: false }" class="transition-all">
    <button @click="open = !open" class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-[#a83232] cursor-pointer">
        <div class="flex items-center space-x-2">
            <!-- Calendar Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 7V3m8 4V3m-9 4h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
            </svg>
            <span>Jadwal</span>
        </div>
        <!-- Panah Dropdown -->
        <svg :class="{ 'rotate-90': open }" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Isi dropdown -->
    <div x-show="open" x-transition x-cloak class="ml-4 mt-1 space-y-1 pl-2 border-l border-[#c34f4f]">
        <a href="{{ url('/jadwal') }}" class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 7V3m8 4V3m-9 4h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
            </svg>
            <span>Jadwal Manual</span>
        </a>
        <a href="{{ url('/auto_schedule') }}" class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 7V3m8 4V3m-9 4h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
            </svg>
            <span>Jadwal Otomatis</span>
        </a>
    </div>
</div>


    <div x-data="{ open: false }" class="transition-all">
    <!-- Tombol utama -->
    <button @click="open = !open" class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-[#a83232] cursor-pointer">
        <div class="flex items-center space-x-2">
            <!-- Icon Folder Keuangan -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
            </svg>
            <span>Transaksi</span>
        </div>
        <!-- Panah -->
        <svg :class="{ 'rotate-90': open }" class="h-4 w-4 transform transition-transform duration-300"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Isi dropdown -->
    <div x-show="open" x-transition x-cloak class="ml-4 mt-1 space-y-1 pl-2 border-l border-[#c34f4f]">
        <a href="{{ url('/pemesanan') }}" class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
            <!-- Clipboard Icon -->
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h6m-6 4h6M5 7h14M9 3h6a2 2 0 012 2v1H7V5a2 2 0 012-2z"></path>
            </svg>
            <span>Pemesanan</span>
        </a>
        <a href="{{ url('/keuangan') }}" class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
            <!-- Money Icon -->
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v2m14 0V7a2 2 0 012-2h2a2 2 0 012 2v2m-4 0h-4m4 0v6m0 0h4m-4 0h-4m-6 0h4m-4 0H5m4 0v-6" />
            </svg>
            <span>Keuangan</span>
        </a>
    </div>
</div>




    <div x-data="{ open: false }" class="transition-all">
        <button @click="open = !open" class="w-full flex justify-between items-center py-2 px-4 rounded hover:bg-[#a83232] cursor-pointer">
            <div class="flex items-center space-x-2">
                <!-- Ikon pengguna -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A9.969 9.969 0 0012 20c2.137 0 4.111-.666 5.722-1.796M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Manajemen</span>
            </div>
            <!-- Panah Dropdown -->
            <svg :class="{ 'rotate-90': open }" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>


        <!-- Isi dropdown -->
        <div x-show="open" x-transition x-cloak class="ml-4 mt-1 space-y-1 pl-2 border-l border-[#c34f4f]">
            <a href="{{ url('/pelanggan') }}" class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
                    <!-- Ikon User -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0121 16.5c0 2.486-1.64 4.5-3.667 4.5H6.667C4.64 21 3 18.986 3 16.5c0-1.61.635-3.068 1.674-4.12L12 14z" />
                    </svg>
                    <span>Pelanggan</span>
                </a>
                @auth
        @if(auth()->user()->role && auth()->user()->role->name === 'superadmin')
            <a href="{{ url('/manajemen-admin') }}" class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
                <!-- Ikon Admin -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Admin</span>
                </a>

                <a href="{{ url('/manajemen-role') }}" class="flex items-center space-x-2 py-1.5 px-4 text-sm rounded hover:bg-[#c34f4f]">
                    <!-- Ikon Role -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83" />
                    </svg>
                    <span>Role</span>
                </a>
                @endif
@endauth
            </div>
        </div>



</nav>


    <!-- Footer -->
    <!-- Footer Dropdown (Alpine.js version) -->
<div class="absolute bottom-4 left-0 w-full px-4">
        <div @click="open = !open" class="py-2 px-4 bg-[#a83232] text-white rounded cursor-pointer">
            <span class="block font-medium">{{ Auth::user()->name ?? 'Pengguna' }}</span>
            <small>{{ Auth::user()->email ?? 'p@gmail.com' }}</small>
        </div>

        <div x-show="open" x-transition x-cloak @click.away="open = false"
             class="mt-2 bg-red text-white rounded shadow-md overflow-hidden">
            <a href="{{ url('/profil') }}" class="block px-4 py-2 text-sm hover:bg-[#a83232]">Profil</a>

            <!-- Trigger Logout -->
            <button id="logoutBtn"
        type="button"
        class="w-full text-left px-4 py-2 text-sm hover:bg-[#a83232]">
    Logout
</button>
        </div>
    </div>
<form id="logoutForm" method="POST" action="{{ url('logoutadmin') }}" class="hidden">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 text-sm">
                        Ya, Logout
                    </button>
                </form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('logoutBtn').addEventListener('click', function () {
        Swal.fire({
            title: 'Logout dari akun?',
            text: "Anda akan keluar dari dashboard.",
            icon: 'warning',
            background: '#1f2937', // gelap
            color: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // merah AWR
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Ya, Logout',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            customClass: {
                popup: 'rounded-xl shadow-lg animate__animated animate__fadeInDown',
                confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                cancelButton: 'px-4 py-2 rounded-lg font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    });
});
</script>


</aside>
