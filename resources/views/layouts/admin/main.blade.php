<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - BJBO Sedagho')</title>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- 100% Tailwind CSS & Modular Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bjbo-bg text-gray-900 font-sans antialiased min-h-screen flex">
    @include('sweetalert::alert')

    <!-- Admin Sidebar (Desktop & Mobile Drawer) -->
    @include('layouts.admin.sidebar')

    <!-- Main Wrapper Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Navbar -->
        @include('layouts.admin.navbar')

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('layouts.admin.footer')
    </div>

    @include('layouts.admin.script')
</body>
</html>
