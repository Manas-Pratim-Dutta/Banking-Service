export function initSidebarMenus() {
    const buttons =
        document.querySelectorAll<HTMLElement>("[data-menu-toggle]");
    const sidebar = document.getElementById('sidebar');

    buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            // Auto-expand sidebar if it's collapsed
            if (sidebar && sidebar.classList.contains('w-20')) {
                // Trigger the toggle button click to handle expansion animations/logic
                document.getElementById('sidebar-toggle')?.click();
            }
            const targetId = btn.getAttribute("data-menu-toggle");
            if (!targetId) return;

            const targetMenu = document.getElementById(targetId);
            if (!targetMenu) return;

            const isClosed = targetMenu.classList.contains("max-h-0");

            buttons.forEach((otherBtn) => {
                if (otherBtn !== btn) {
                    otherBtn.classList.remove("active");

                    const otherTargetId = otherBtn.getAttribute("data-menu-toggle");
                    if (!otherTargetId) return;

                    const otherMenu = document.getElementById(otherTargetId);
                    if (!otherMenu) return;

                    otherMenu.classList.add("max-h-0", "opacity-0");
                    otherMenu.classList.remove("max-h-[500px]", "opacity-100");
                }
            });

            if (isClosed) {
                targetMenu.classList.remove("max-h-0", "opacity-0");
                targetMenu.classList.add("max-h-[500px]", "opacity-100", "ml-10");
                btn.classList.add("active");
            } else {
                targetMenu.classList.add("max-h-0", "opacity-0");
                targetMenu.classList.remove("max-h-[500px]", "opacity-100", "ml-10");
                btn.classList.remove("active");
            }
        });
    });
}

export function initProfileMenu() {
    const container = document.getElementById("profile");
    const menu = document.getElementById("profile-action");

    if (!container || !menu) return;

    container.addEventListener("click", (e) => {
        e.stopPropagation();
        menu.classList.toggle("hidden");
    });

    document.addEventListener("click", () => {
        menu.classList.add("hidden");
    });
}

export function initSidebarToggle() {
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = document.getElementById('toggle-icon');
    const texts = document.querySelectorAll('.sidebar-text');
    const initials = document.querySelector('.sidebar-initials');

    if (!toggleBtn || !sidebar || !toggleIcon) return;

    toggleBtn.addEventListener('click', function () {
        const isCollapsed = sidebar.classList.toggle('w-20');
        sidebar.classList.toggle('w-64');

        if (isCollapsed) {
            toggleIcon.classList.add('rotate-180');
            const openMenus = document.querySelectorAll('[data-submenu]');
            openMenus.forEach(menu => {
                menu.classList.add("max-h-0", "opacity-0");
                menu.classList.remove("max-h-[500px]", "opacity-100", "ml-10");
            });
            const activeBtns = document.querySelectorAll('[data-menu-toggle].active');
            activeBtns.forEach(btn => btn.classList.remove('active'));

        } else {
            toggleIcon.classList.remove('rotate-180');
        }

        texts.forEach(el => {
            if (isCollapsed) {
                el.classList.add('hidden');
                el.classList.remove('opacity-100');
            } else {
                el.classList.remove('hidden');
                setTimeout(() => el.classList.add('opacity-100'), 50);
            }
        });

        if (initials) {
            if (isCollapsed) {
                initials.classList.remove('hidden');
            } else {
                initials.classList.add('hidden');
            }
        }
    });
}
