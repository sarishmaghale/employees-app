$(document).ready(function(){

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

    // Set sidebar to collapsed by default on desktop
    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
        header.classList.add('expanded');
        mainContent.classList.add('expanded');
    } else {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    }
    
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

//activate flatpickr
    $(document).ready(function(){
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d"
    });
    });

$('#addTaskModal, #editTaskModal, #newEmployeeModal').on('shown.bs.modal',function(){
    flatpickr(".datepicker", { 
        dateFormat: "Y-m-d"
     });
    const $currentModal = $(this);
    const startDate = $currentModal.find(".task_start_date")[0]?._flatpickr;
    const endDate = $currentModal.find(".task_end_date")[0]?._flatpickr;
    if (startDate && endDate) {
        if (startDate.input.value) {
            endDate.set('minDate', startDate.input.value);
        }
        startDate.set('onChange', function(selectedDates, dateStr) {
            endDate.set('minDate', dateStr);
            if (endDate.selectedDates[0] && endDate.selectedDates[0] < selectedDates[0]) {
                endDate.clear();
            }
        });
    }
});

function showSpinner(btn) {
    if (!btn.dataset.originalText) btn.dataset.originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            
        `;
}
function hideSpinner(target) {
    if (typeof target === 'string') {
        // If target is a form ID, restore all submit buttons inside it
        const $form = $('#' + target);
        if (!$form.length) return;

        $form.find("[type='submit']").each(function() {
            hideSpinner(this); // recursively restore each button
        });
    } else {
        // If target is a single button
        const $btn = $(target);
        $btn.prop('disabled', false);
        if ($btn.data('originalText')) {
            $btn.html($btn.data('originalText'));
        }
  
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}