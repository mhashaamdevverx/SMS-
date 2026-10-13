/**
 * AKM School System - Dynamic Theme & Color Customizer
 * Palette: Deep Indigo (#1E1B4B) + Teal (#14B8A6) + Warm Amber (#F59E0B)
 */

(function () {
    const THEMES = {
        indigo: {
            name: 'Deep Indigo (Official)',
            primary: '#4F46E5',
            primaryHover: '#4338CA',
            primaryPressed: '#3730A3',
            secondary: '#14B8A6',
            secondaryHover: '#0D9488',
            accent: '#F59E0B',
            accentHover: '#D97706',
            sidebarBg: '#1E1B4B',
            sidebarText: '#C7D2FE',
            headerBg: 'rgba(255, 255, 255, 0.88)',
            headerBorder: '#E5E7EB',
            navHover: '#4F46E5',
            accentLight: '#EEF2FF',
            accentText: '#4F46E5',
            badgeBg: '#C7D2FE'
        },
        emerald: {
            name: 'Emerald Teal',
            primary: '#14B8A6',
            primaryHover: '#0D9488',
            primaryPressed: '#0F766E',
            secondary: '#4F46E5',
            secondaryHover: '#4338CA',
            accent: '#F59E0B',
            accentHover: '#D97706',
            sidebarBg: '#1E1B4B',
            sidebarText: '#C7D2FE',
            headerBg: 'rgba(255, 255, 255, 0.88)',
            headerBorder: '#E5E7EB',
            navHover: '#14B8A6',
            accentLight: '#CCFBF1',
            accentText: '#0D9488',
            badgeBg: '#A7F3D0'
        }
    };

    function applyTheme(themeKey) {
        // Enforce the user's official Deep Indigo theme
        const theme = THEMES[themeKey] || THEMES.indigo;
        const root = document.documentElement;

        root.style.setProperty('--primary', theme.primary);
        root.style.setProperty('--primary-hover', theme.primaryHover);
        root.style.setProperty('--primary-pressed', theme.primaryPressed);
        root.style.setProperty('--secondary', theme.secondary);
        root.style.setProperty('--secondary-hover', theme.secondaryHover);
        root.style.setProperty('--accent', theme.accent);
        root.style.setProperty('--accent-hover', theme.accentHover);
        root.style.setProperty('--sidebar-bg', theme.sidebarBg);
        root.style.setProperty('--sidebar-text', theme.sidebarText);

        // Update elements dynamically
        const sidebars = document.querySelectorAll('.student-side, .teacher-side, .admin-side, .sidebar');
        sidebars.forEach(s => s.style.background = theme.sidebarBg);

        const topbars = document.querySelectorAll('.student-top, .teacher-top, .admin-top, .navbar');
        topbars.forEach(t => {
            t.style.background = theme.headerBg;
            t.style.borderColor = theme.headerBorder;
        });

        const activeNavs = document.querySelectorAll('.student-side nav a.is-active, .teacher-side nav a.is-active, .admin-side nav a.is-active, .sidebar-btn.active');
        activeNavs.forEach(a => {
            a.style.background = theme.primary;
            a.style.color = '#FFFFFF';
        });

        localStorage.setItem('akm_app_theme_color', themeKey || 'indigo');
    }

    window.setAkmTheme = function (themeKey) {
        applyTheme(themeKey);
    };

    // Auto-apply saved theme on script load
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('akm_app_theme_color') || 'indigo';
        applyTheme(savedTheme);

        // Setup Mobile Navigation Drawer Toggle
        const mobileToggle = document.getElementById('mobileNavToggle');
        const sidebar = document.querySelector('.student-side, .teacher-side, .admin-side, .sidebar');
        const overlay = document.getElementById('mobileNavOverlay');

        if (mobileToggle && sidebar) {
            mobileToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('mobile-open');
            });
        }

        if (overlay && sidebar) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('mobile-open');
            });
        }
    });

    // Immediate initial call to prevent color flicker
    const saved = localStorage.getItem('akm_app_theme_color') || 'indigo';
    applyTheme(saved);
})();
