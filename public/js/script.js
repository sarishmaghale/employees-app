document.addEventListener('DOMContentLoaded', () => {

     const spinner = document.getElementById("globalSpinner");

           document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function() {
            const submitButtons = form.querySelectorAll("[type='submit']");
            submitButtons.forEach(btn => {
                if (!btn.dataset.originalText) btn.dataset.originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span>`;
            });
        });
    });
            
    const sidebar = document.getElementById('sidebar');
    const header = document.getElementById('header');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const menuItems = document.querySelectorAll('.menu-item');
    const userMenu = document.getElementById('userMenu');

    const currentPath = window.location.pathname;

    // Highlight active menu item based on current path
    menuItems.forEach(link => {
        const href = link.getAttribute('href');
        if (!href || href === '#' || href === 'javascript:void(0)') return;

        const linkPath = new URL(link.href, window.location.origin).pathname;

        if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
            link.classList.add('active');

            // If inside a submenu, open it
            const submenu = link.closest('.submenu');
            if (submenu) {
                submenu.classList.add('show');

                // Mark parent toggle as active
                const parentToggle = submenu.previousElementSibling; // assuming toggle button comes before submenu
                if (parentToggle) parentToggle.classList.add('active');
            }
        }
    });

    // Sidebar Toggle
    menuToggle.addEventListener('click', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.toggle('collapsed');
            header.classList.toggle('expanded');
            mainContent.classList.toggle('expanded');
        } else {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        }
    });

    // Close sidebar on overlay click (mobile)
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });

    // Update active on click
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            menuItems.forEach(mi => mi.classList.remove('active'));
            this.classList.add('active');

            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    });

    // User Dropdown Toggle
    userMenu.addEventListener('click', (e) => {
        e.stopPropagation();
        userMenu.classList.toggle('active');
    });

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
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            } else {
                sidebar.classList.remove('collapsed');
                header.classList.remove('expanded');
                mainContent.classList.remove('expanded');
            }
        }, 250);
    });
});
function reenableFormButtons(formId) {
    const form = document.getElementById(formId);
    if (!form) return; // safety check

    const submitButtons = form.querySelectorAll("[type='submit']");
    submitButtons.forEach(btn => {
        btn.disabled = false;
        // Restore original text from data attribute
        if (btn.dataset.originalText) {
            btn.innerHTML = btn.dataset.originalText;
        }
    });
}

