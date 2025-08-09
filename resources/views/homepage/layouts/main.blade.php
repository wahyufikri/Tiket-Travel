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

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- x-cloak untuk sembunyikan elemen sebelum Alpine inisialisasi --}}
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Animasi fade-in */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    {{-- Custom CSS tambahan tiap halaman --}}
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased fade-in">

    {{-- Header --}}
    @include('homepage.layouts.header')

    {{-- Main Content --}}
    <main class="min-h-screen max-w-7xl mx-auto px-4 py-6">
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
