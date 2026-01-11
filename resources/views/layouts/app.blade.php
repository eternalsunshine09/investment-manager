<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Porto Tracking')</title>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Outfit', sans-serif;
        background: #F6F8FD;
    }
    </style>
</head>

<body x-data="{ sidebarOpen: window.innerWidth >= 768 }" class="text-slate-800">

    <div class="toggle-wrapper" :class="{ 'moved': sidebarOpen }">
        <label class="switch">
            <input type="checkbox" x-model="sidebarOpen">
            <span class="slider"></span>
        </label>
    </div>

    <div class="sidebar-overlay md:hidden" :class="{ 'active': sidebarOpen }" @click="sidebarOpen = false"></div>

    @include('components.sidebar')

    <main class="main-content-full pt-20 md:pt-8" :class="{ 'has-sidebar': sidebarOpen }">
        @yield('content')
    </main>

</body>

</html>