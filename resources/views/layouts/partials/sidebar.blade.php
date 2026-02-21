<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-text">Antrian Online</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link logout-link">
                Logout
            </button>
        </form>
    </div>
</aside>
