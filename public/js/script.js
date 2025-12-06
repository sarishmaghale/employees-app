
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const header = document.getElementById('header');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        menuToggle.addEventListener('click', () => {
            if (window.innerWidth > 768) {
                // Desktop: collapse/expand
                sidebar.classList.toggle('collapsed');
                header.classList.toggle('expanded');
                mainContent.classList.toggle('expanded');
            } else {
                // Mobile: show/hide with overlay
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            }
        });

        // Close sidebar on overlay click (mobile)
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        // Active Menu Item
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                menuItems.forEach(mi => mi.classList.remove('active'));
                this.classList.add('active');

                // Close mobile menu after selection
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            });
        });

        // User Dropdown Toggle
        const userMenu = document.getElementById('userMenu');
        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth > 768) {
                    // Reset mobile states when switching to desktop
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                } else {
                    // Reset desktop states when switching to mobile
                    sidebar.classList.remove('collapsed');
                    header.classList.remove('expanded');
                    mainContent.classList.remove('expanded');
                }
            }, 250);
        });

        // Close mobile sidebar when clicking on a link
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            });
        });
   