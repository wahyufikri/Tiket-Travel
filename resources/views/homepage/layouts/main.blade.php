<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Travel Ticket')</title>

    {{-- Tailwind & Alpine.js --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- x-cloak untuk sembunyikan elemen sebelum Alpine inisialisasi --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- Custom CSS tambahan tiap halaman --}}
    @stack('styles')
</head>
<body class="bg-white text-gray-800 antialiased">

    {{-- Header --}}
    @include('homepage.layouts.header')

    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('homepage.layouts.footer')

    {{-- Stack untuk modal (biar tidak terhalang container) --}}
    @stack('modals')

    {{-- Custom JS tiap halaman --}}
    @stack('scripts')

</body>
</html>
