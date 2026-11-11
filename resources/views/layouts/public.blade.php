<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('languages.supported.'.app()->getLocale().'.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Excellence School System - ' . __('Nurturing Minds, Inspiring Leaders'))</title>

    <link rel="icon" type="image/png" href="{{ asset('images/school_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/school_logo.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --primary-light: #6366F1;
            --purple: #8B5CF6;
            --purple-dark: #7C3AED;
            --deep-indigo: #1E1B4B;
            --dark-navy: #0F172A;
            --accent-amber: #F59E0B;
            --accent-green: #10B981;
            --text-dark: #0F172A;
            --text-body: #475569;
            --bg-light: #F8FAFC;
            --border-light: #E2E8F0;
            --font-display: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-body);
            background-color: #FFFFFF;
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-display);
            color: var(--text-dark);
            font-weight: 700;
        }

        /* --- ROOTS-STYLE ULTRA CLEAN NAVBAR --- */
        .landing-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            height: 78px;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .landing-header.scrolled {
            height: 72px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 8px 30px -4px rgba(15, 23, 42, 0.08);
            border-bottom-color: rgba(203, 213, 225, 0.9);
        }

        .roots-header-container {
            width: 100%;
            max-width: 1480px;
            margin: 0 auto;
            padding: 0 36px;
        }

        .roots-navbar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .roots-brand {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
            white-space: nowrap !important;
        }

        .roots-brand-logo-img {
            height: 52px;
            width: auto;
            max-width: 60px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(79, 70, 229, 0.22));
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            flex-shrink: 0;
        }

        .roots-brand:hover .roots-brand-logo-img {
            transform: scale(1.06) translateY(-1px);
            filter: drop-shadow(0 6px 18px rgba(79, 70, 229, 0.35));
        }

        .roots-brand-divider {
            width: 1.5px;
            height: 38px;
            background: linear-gradient(180deg, rgba(203, 213, 225, 0.2) 0%, rgba(203, 213, 225, 0.9) 50%, rgba(203, 213, 225, 0.2) 100%);
            margin: 0 16px;
            flex-shrink: 0;
        }

        .roots-brand-text {
            display: flex;
            flex-direction: column;
            white-space: nowrap !important;
            line-height: 1.15;
        }

        .roots-brand-name {
            font-family: var(--font-display);
            font-size: 16.5px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .roots-brand-sub {
            font-family: var(--font-display);
            font-size: 10.5px;
            font-weight: 700;
            color: #6366F1;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Nav Menu Links with Pill Hover */
        .roots-nav-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
            white-space: nowrap !important;
        }

        .roots-nav-link {
            font-family: var(--font-display);
            font-size: 14.5px;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            white-space: nowrap !important;
            padding: 8px 14px;
            border-radius: 9px;
            position: relative;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .roots-nav-link:hover {
            color: #4F46E5;
            background: rgba(99, 102, 241, 0.08);
            transform: translateY(-1px);
        }

        .roots-nav-link.active {
            color: #4F46E5;
            background: rgba(99, 102, 241, 0.12);
            font-weight: 700;
        }

        /* Right CTA Action: Modern Glowing Portal Login */
        .roots-nav-actions {
            display: flex;
            align-items: center;
            margin-left: 24px;
            flex-shrink: 0;
            white-space: nowrap !important;
        }

        .btn-roots-apply {
            height: 44px;
            padding: 0 22px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: #FFFFFF !important;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.02em;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35);
            white-space: nowrap !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-roots-apply .btn-icon-lock {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .btn-roots-apply .btn-arrow {
            transition: transform 0.25s ease;
        }

        .btn-roots-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.48);
            background: linear-gradient(135deg, #4338CA 0%, #6D28D9 100%);
            color: #FFFFFF !important;
        }

        .btn-roots-apply:hover .btn-arrow {
            transform: translateX(4px);
        }

        /* --- FOOTER --- */
        .landing-footer {
            background: var(--dark-navy);
            color: #94A3B8;
            padding-top: 80px;
            padding-bottom: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-brand-title {
            color: #FFFFFF;
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 18px;
        }

        .footer-col-title {
            color: #FFFFFF;
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #94A3B8;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: #FFFFFF;
            text-decoration: underline;
        }

        .footer-bottom-bar {
            margin-top: 60px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13px;
        }

        .ssl-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 5px 12px;
            border-radius: 99px;
            color: #38BDF8;
            font-weight: 600;
            font-size: 12px;
        }

        /* Page Banner Header */
        .page-banner {
            padding-top: 140px;
            padding-bottom: 70px;
            background: radial-gradient(circle at 10% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.1) 0%, transparent 40%),
                        #FFFFFF;
            border-bottom: 1px solid #F1F5F9;
            position: relative;
        }

        .page-banner-tag {
            display: inline-block;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--purple);
            background: #F5F3FF;
            padding: 6px 14px;
            border-radius: 99px;
            margin-bottom: 12px;
        }

        .page-banner-title {
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0F172A;
            margin-bottom: 14px;
        }

        .page-banner-sub {
            font-size: 17px;
            color: #475569;
            max-width: 680px;
            margin: 0 auto;
        }

        @media (max-width: 1320px) {
            .roots-nav-menu {
                gap: 6px;
            }
            .roots-nav-link {
                padding: 7px 11px;
                font-size: 13.5px;
            }
            .roots-header-container {
                padding: 0 20px;
            }
            .roots-nav-actions {
                margin-left: 14px;
            }
        }

        @media (max-width: 991px) {
            .landing-header {
                height: 72px;
            }
            .roots-navbar .navbar-collapse {
                background: #FFFFFF;
                padding: 20px;
                border-radius: 16px;
                margin-top: 10px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.1);
                border: 1px solid #E2E8F0;
            }
            .roots-nav-menu {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 0;
                width: 100%;
            }
            .roots-nav-link {
                width: 100%;
                padding: 10px 14px;
            }
            .roots-nav-actions {
                margin-left: 0;
                padding-top: 14px;
                border-top: 1px solid #E2E8F0;
                width: 100%;
            }
            .btn-roots-apply {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    @stack('styles')
    @include('partials.locale-head')
</head>
<body>

    @include('partials.public-header')

    <main>
        @yield('content')
    </main>

    @include('partials.public-footer')

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sticky Header scroll effect
        window.addEventListener('scroll', function () {
            const header = document.getElementById('landingHeader');
            if (!header) return;
            if (window.scrollY > 20) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
