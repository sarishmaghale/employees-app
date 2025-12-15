<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-logo">
                <i class="fas fa-layer-group"></i>
                <span class="logo-text">Admin Panel</span>
            </a>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item " data-title="Dashboard">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>

            @if (session('role') === 'admin')
                <div class="menu-label">Components</div>

                <a href="{{ route('employees.index') }}" class="menu-item" data-title="Users">
                    <i class="fas fa-users"></i>
                    <span class="menu-text">Employees</span>
                </a>
            @endif
            <div class="menu-label">Tasks</div>

            <a href="{{ route('calendar.index') }}" class="menu-item" data-title="Users">
                <i class="fas fa-calendar-alt"></i>
                <span class="menu-text">Calendar</span>
            </a>
            <a href="{{ route('tasks.all') }}" class="menu-item" data-title="Users">
                <i class="fas fa-tasks"></i>
                <span class="menu-text">Tasks</span>
            </a>
            <div class="menu-label">Components</div>

            <a href="#" class="menu-item" data-title="Analytics">
                <i class="fas fa-chart-line"></i>
                <span class="menu-text">Add User</span>
            </a>
        </nav>
    </aside>

    <!-- Header -->
    <header class="header" id="header">
        <div class="header-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
        </div>

        <div class="header-right">
            <div class="user-menu" id="userMenu">
                <div class="user-avatar">
                    <img class="img-radius" src="{{ asset('storage/' . session('profile_image')) }}"
                        alt="User-Profile-Image">
                </div>
                <span class="user-name">{{ session('username') }}</span>
                <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>

                <div class="user-dropdown">
                    <a href="{{ route('profile.show') }}" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button class="dropdown-item danger" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @yield('content')
    </div>

    <div id="globalSpinner" class="d-none text-center mt-3">
        <div class="spinner-border text-primary" role="status">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>

    @stack('scripts')
</body>

</html>
