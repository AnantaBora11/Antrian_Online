<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Antrian Online')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <header class="topbar">
        <div class="topbar-left">
            <span class="topbar-brand">Antrian Online</span>
            <span class="topbar-divider">—</span>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-role">{{ ucfirst(Auth::user()->role) }}</span>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </header>

    <main class="admin-content">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
