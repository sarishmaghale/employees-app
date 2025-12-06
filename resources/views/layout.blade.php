<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src={{ asset('js/script.js') }} defer></script>
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
            <a href="#" class="menu-item active" data-title="Dashboard">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>

            <div class="menu-label">Components</div>

            <a href="#" class="menu-item" data-title="Users">
                <i class="fas fa-users"></i>
                <span class="menu-text">Employees</span>
            </a>
            <div class="menu-label">Tasks</div>

            <a href="#" class="menu-item" data-title="Users">
                <i class="fas fa-users"></i>
                <span class="menu-text">Calendar</span>
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
                <div class="user-avatar">AD</div>
                <span class="user-name">Admin User</span>
                <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>

                <div class="user-dropdown">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <button class="dropdown-item danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>


</body>

</html>
