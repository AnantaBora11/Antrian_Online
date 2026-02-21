<header class="topbar">
    <div class="topbar-title">
        @yield('page-title', 'Dashboard')
    </div>
    <div class="topbar-user">
        <span class="user-name">{{ Auth::user()->name }}</span>
        <span class="user-role">{{ ucfirst(Auth::user()->role) }}</span>
    </div>
</header>
