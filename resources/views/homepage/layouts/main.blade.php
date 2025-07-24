<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Travel Ticket')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite('resources/css/app.css') <!-- Tailwind -->
    @vite('resources/js/app.js')   <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800 antialiased">

    @include('homepage.layouts.header')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('homepage.layouts.footer')

</body>
</html>
