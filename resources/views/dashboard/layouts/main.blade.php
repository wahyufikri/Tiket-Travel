<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Jika pakai Vite -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Styles tambahan dari child view --}}
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        @include('dashboard.layouts.sidebar')

        <main class="ml-64 w-full p-6">
            @yield('content')
        </main>
    </div>

    {{-- Scripts tambahan dari child view --}}
    @stack('scripts')
</body>
</html>
