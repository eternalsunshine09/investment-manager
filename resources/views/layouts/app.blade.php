<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Porto Tracking')</title>

    <!-- Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Page-specific CSS -->
    @stack('styles')

    <style>
    body {
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
    }

    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="bg-[#F8FAFC] text-slate-800">
    @yield('content')

    <!-- Page-specific JS -->
    @stack('scripts')
</body>

</html>