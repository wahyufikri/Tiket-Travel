@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto py-10 px-4 relative"
    x-data="{ showModal: @json($errors->any()), showPass: false, showNewPass: false, showConfirm: false }">

    <div class="flex justify-center">
        <div class="w-full max-w-xl">
            @if(session('success'))
                <div class="bg-orange-100 border border-orange-300 text-orange-800 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-2xl p-6">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 mx-auto bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user fa-3x text-gray-700"></i>
                    </div>
                    <h5 class="mt-4 text-lg font-semibold text-gray-800">Profil Pengguna</h5>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold text-gray-500 mb-1">Nama Lengkap</label>
                    <div class="bg-gray-100 p-2 rounded">{{ auth()->user()->name }}</div>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold text-gray-500 mb-1">Email</label>
                    <div class="bg-gray-100 p-2 rounded">{{ auth()->user()->email }}</div>
                </div>

                <div class="mb-6">
                    <label class="block font-semibold text-gray-500 mb-1">Password</label>
                    <div class="bg-gray-100 p-2 rounded">***************</div>
                </div>

                <div class="text-center">
                    <button class="bg-red-600 hover:bg-red-400 text-white font-semibold py-2 px-6 rounded shadow"
                        @click="showModal = true">
                        <i class="fas fa-lock mr-2"></i>Ubah Kata Sandi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div
        class="absolute top-16 left-0 w-full flex justify-center z-50"
        x-show="showModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        x-cloak
        @keydown.escape.window="showModal = false"
    >
        <!-- Modal Box -->
        <div class="bg-white rounded-2xl shadow w-full max-w-md mx-4 p-6 relative"
            @click.away="showModal = false">

            <!-- Close -->
            <button
                type="button"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
                @click="showModal = false"
            >
                <i class="fas fa-times"></i>
            </button>

            <!-- Icon -->
            <div class="text-center mb-4">
                <div class="w-14 h-14 bg-gray-100 mx-auto rounded-full flex items-center justify-center">
                    <i class="fas fa-lock text-gray-500 text-xl"></i>
                </div>
            </div>

            <h5 class="text-lg font-bold text-center text-gray-800 mb-1">Ganti Password</h5>
            <p class="text-center text-sm text-gray-500 mb-4">Pastikan kata sandi yang Anda buat mudah diingat dan aman.</p>

            @if($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-2 text-sm rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profil.updatePassword') }}" method="POST">
                @csrf

                <!-- Old Password -->
                <div class="mb-4">
                    <label class="block font-semibold text-gray-700 mb-1">Kata Sandi Lama</label>
                    <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                        <span class="px-3 bg-gray-100 text-gray-500"><i class="fas fa-key"></i></span>
                        <input :type="showPass ? 'text' : 'password'" name="current_password"  autocomplete="new-password" class="flex-1 px-3 py-2 focus:outline-none appearance-none bg-transparent min-w-0" placeholder="Masukkan kata sandi lama" required>

                    </div>
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <label class="block font-semibold text-gray-700 mb-1">Kata Sandi Baru</label>
                    <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                        <span class="px-3 bg-gray-100 text-gray-500"><i class="fas fa-lock"></i></span>
                        <input :type="showNewPass ? 'text' : 'password'" name="new_password" class="flex-1 px-3 py-2 focus:outline-none appearance-none" placeholder="Masukkan kata sandi baru" required>

                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label class="block font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                        <span class="px-3 bg-gray-100 text-gray-500"><i class="fas fa-check-circle"></i></span>
                        <input :type="showConfirm ? 'text' : 'password'" name="new_password_confirmation" class="flex-1 px-3 py-2 focus:outline-none appearance-none" placeholder="Ulangi kata sandi baru" required>

                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between">
                    <button type="button" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100" @click="showModal = false">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-400 text-white font-semibold px-4 py-2 rounded">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
