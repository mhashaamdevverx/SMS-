/**
 * Excellence Student Portal UI Engine
 * Handles Light/Dark Theme Switch, Sidebar Collapsing, Command Search (Ctrl+K),
 * Notification Bell, Language Toggle, and Dropdown Controls.
 */

(function () {
    'use strict';

    // 1. Theme Management (Light / Dark with localStorage & System preference fallback)
    function getStoredTheme() {
        const stored = localStorage.getItem('akm_portal_theme');
        if (stored) return stored;
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function setTheme(themeName) {
        document.documentElement.setAttribute('data-theme', themeName);
        localStorage.setItem('akm_portal_theme', themeName);
        updateThemeToggleIcons(themeName);
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        const nextTheme = current === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
    }

    function updateThemeToggleIcons(themeName) {
        const themeBtn = document.getElementById('themeToggleBtn');
        if (!themeBtn) return;
        const icon = themeBtn.querySelector('i');
        if (icon) {
            icon.className = themeName === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
            themeBtn.title = themeName === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    }

    // 2. Sidebar Desktop Collapsing (Full 260px / Icon 76px)
    function initSidebarCollapse() {
        const aside = document.querySelector('.student-side');
        const main = document.querySelector('.student-main');
        const collapseBtn = document.getElementById('sidebarCollapseBtn');
        if (!aside || !collapseBtn) return;

        const isCollapsed = localStorage.getItem('akm_sidebar_collapsed') === 'true';
        if (isCollapsed) {
            aside.classList.add('collapsed');
            if (main) main.classList.add('sidebar-collapsed');
        }

        collapseBtn.addEventListener('click', () => {
            aside.classList.toggle('collapsed');
            if (main) main.classList.toggle('sidebar-collapsed');
            const state = aside.classList.contains('collapsed');
            localStorage.setItem('akm_sidebar_collapsed', state ? 'true' : 'false');
        });
    }

    // 3. Mobile Navigation Drawer & Overlay
    function initMobileDrawer() {
        const toggleBtn = document.getElementById('mobileNavToggle');
        const overlay = document.getElementById('mobileNavOverlay');
        const aside = document.querySelector('.student-side');

        if (toggleBtn && aside && overlay) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                aside.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', () => {
                aside.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }
    }

    // 4. Command Search (Ctrl+K or Cmd+K Focus)
    function initCommandSearch() {
        const searchInput = document.getElementById('globalSearchInput');
        const searchBtn = document.getElementById('globalSearchBtn');

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                if (searchInput) searchInput.focus();
            }
        });

        if (searchBtn && searchInput) {
            searchBtn.addEventListener('click', () => searchInput.focus());
        }
    }

    // 5. Language Switcher (EN / Urdu RTL)
    function initLanguageSwitch() {
        const langBtn = document.getElementById('langToggleBtn');
        if (!langBtn) return;

        let currentLang = localStorage.getItem('akm_portal_lang') || 'en';
        applyLang(currentLang);

        langBtn.addEventListener('click', () => {
            currentLang = currentLang === 'en' ? 'ur' : 'en';
            localStorage.setItem('akm_portal_lang', currentLang);
            applyLang(currentLang);
        });

        function applyLang(lang) {
            const langSpan = langBtn.querySelector('.lang-code');
            if (langSpan) langSpan.textContent = lang === 'en' ? 'EN' : 'اردو';
            if (lang === 'ur') {
                document.documentElement.setAttribute('dir', 'rtl');
            } else {
                document.documentElement.removeAttribute('dir');
            }
        }
    }

    // 6. Global Dropdown & Modal Conflict Manager (Prevents popup collisions)
    function closeAllActiveDropdowns() {
        const dropdowns = document.querySelectorAll('#userDropdownMenu, #notifDropdown, #adminNotifDropdown, .profile-dropdown');
        dropdowns.forEach(d => d.style.display = 'none');
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#topProfileUser') && !e.target.closest('#notifBellBtn') && !e.target.closest('#adminNotifBellBtn')) {
            closeAllActiveDropdowns();
        }
    });

    // Observer to lock scroll & close dropdowns when any modal triggers
    let isObserverMutating = false;
    const observer = new MutationObserver(() => {
        if (isObserverMutating) return;
        const openModal = document.querySelector('.modal-backdrop:not([hidden]), .crud-confirm-backdrop:not([hidden]), #student-profile-modal:not([style*="display: none"]), #stdLeaveDetailModal:not([style*="display: none"]), #uploadOutlineModal:not([style*="display: none"])');
        isObserverMutating = true;
        if (openModal) {
            closeAllActiveDropdowns();
            if (!document.body.classList.contains('modal-open')) {
                document.body.classList.add('modal-open');
            }
        } else {
            if (document.body.classList.contains('modal-open')) {
                document.body.classList.remove('modal-open');
            }
        }
        isObserverMutating = false;
    });

    // Initialize scripts on DOMReady
    document.addEventListener('DOMContentLoaded', () => {
        setTheme(getStoredTheme());
        initSidebarCollapse();
        initMobileDrawer();
        initCommandSearch();
        initLanguageSwitch();

        const themeBtn = document.getElementById('themeToggleBtn');
        if (themeBtn) {
            themeBtn.addEventListener('click', toggleTheme);
        }

        observer.observe(document.body, { childList: true, subtree: true, attributes: true });
    });

    // Listen for OS Theme Changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('akm_portal_theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
})();

