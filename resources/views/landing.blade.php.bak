<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excellence School System - Nurturing Young Minds Through Innovation & Play</title>

    <link rel="icon" type="image/png" href="{{ asset('images/school_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/school_logo.png') }}">

    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
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
            --accent-orange: #F97316;
            --accent-cyan: #06B6D4;
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

        /* --- HERO SECTION (EDUKID / MODERN 3D LAYOUT) --- */
        .hero-section {
            padding-top: 135px;
            padding-bottom: 90px;
            position: relative;
            overflow: hidden;
            background: 
                radial-gradient(850px circle at 15% 15%, rgba(139, 92, 246, 0.09) 0%, transparent 70%),
                radial-gradient(650px circle at 85% 60%, rgba(79, 70, 229, 0.08) 0%, transparent 70%),
                radial-gradient(400px circle at 50% 90%, rgba(244, 114, 182, 0.04) 0%, transparent 60%),
                #FFFFFF;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(148, 163, 184, 0.22) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.6;
            mask-image: linear-gradient(180deg, #FFFFFF 0%, rgba(255,255,255,0.3) 80%, transparent 100%);
            -webkit-mask-image: linear-gradient(180deg, #FFFFFF 0%, rgba(255,255,255,0.3) 80%, transparent 100%);
            pointer-events: none;
        }

        .hero-glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 1;
        }
        .hero-glow-1 {
            width: 360px;
            height: 360px;
            top: 8%;
            right: 4%;
            background: rgba(124, 58, 237, 0.12);
        }
        .hero-glow-2 {
            width: 300px;
            height: 300px;
            bottom: 8%;
            right: 30%;
            background: rgba(79, 70, 229, 0.10);
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 7px 18px;
            background: rgba(238, 242, 255, 0.95);
            border: 1px solid rgba(99, 102, 241, 0.28);
            border-radius: 99px;
            font-size: 13px;
            font-weight: 700;
            color: #4F46E5;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.08);
            backdrop-filter: blur(8px);
        }

        .pulse-dot-live {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulseGreen 2s infinite;
            display: inline-block;
        }

        @keyframes pulseGreen {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Bold Playful Title with Pill Words */
        .hero-playful-title {
            font-size: clamp(38px, 4.8vw, 62px);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            color: #0F172A;
            margin-bottom: 22px;
        }

        .word-highlight-pill {
            display: inline-block;
            padding: 2px 14px;
            border-radius: 12px;
            color: #FFFFFF;
            font-weight: 800;
            transform: rotate(-1.5deg);
            margin: 0 4px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        .word-orange {
            background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
        }

        .word-purple {
            background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%);
            transform: rotate(1.5deg);
        }

        .hero-subtitle {
            font-size: 18px;
            line-height: 1.7;
            color: #475569;
            max-width: 540px;
            margin-bottom: 34px;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 32px;
        }

        .btn-hero-orange {
            background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
            color: #FFFFFF !important;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 16px;
            padding: 14px 34px;
            border-radius: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px -4px rgba(249, 115, 22, 0.45);
            transition: all 0.25s ease;
        }

        .btn-hero-orange:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px -4px rgba(249, 115, 22, 0.55);
            color: #FFFFFF;
        }

        .btn-hero-purple-outline {
            background: #FFFFFF;
            color: #7C3AED !important;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 16px;
            padding: 14px 28px;
            border-radius: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 2px solid #DDD6FE;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.08);
            transition: all 0.2s ease;
        }

        .btn-hero-purple-outline:hover {
            background: #F5F3FF;
            border-color: #8B5CF6;
            transform: translateY(-2px);
        }

        .hero-rating-strip {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        /* 3D Multi-Layer Screen Collage (Right Side) */
        .hero-3d-stack {
            position: relative;
            z-index: 10;
        }

        .main-3d-tablet {
            background: #FFFFFF;
            border-radius: 28px;
            padding: 12px;
            border: 2px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 30px 70px -15px rgba(15, 23, 42, 0.18);
            position: relative;
            transform: perspective(1000px) rotateY(-4deg) rotateX(2deg);
            transition: transform 0.4s ease;
        }

        .main-3d-tablet:hover {
            transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
        }

        .tablet-screen {
            border-radius: 20px;
            overflow: hidden;
            height: 420px;
            position: relative;
        }

        .tablet-screen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Floating Overlap Cards in 3D Style */
        .floating-badge-stem {
            position: absolute;
            top: -24px;
            right: -20px;
            background: #FFFFFF;
            border-radius: 18px;
            padding: 12px 18px;
            border: 1.5px solid #E2E8F0;
            box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.16);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 20;
            animation: floatSlow 5s ease-in-out infinite alternate;
        }

        .floating-badge-success {
            position: absolute;
            bottom: -24px;
            left: -24px;
            background: #FFFFFF;
            border-radius: 18px;
            padding: 12px 18px;
            border: 1.5px solid #E2E8F0;
            box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.16);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 20;
            animation: floatSlow 5s ease-in-out infinite alternate 2.5s;
        }

        @keyframes floatSlow {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-10px); }
        }

        /* --- FOUR PILLARS SECTION --- */
        .pillars-section {
            padding: 90px 0;
            background: #FFFFFF;
        }

        .section-tag-pill {
            display: inline-block;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #7C3AED;
            background: #F5F3FF;
            padding: 6px 16px;
            border-radius: 99px;
            margin-bottom: 14px;
        }

        .section-heading-bold {
            font-size: clamp(30px, 3.6vw, 44px);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0F172A;
            margin-bottom: 14px;
        }

        .section-subheading-clean {
            font-size: 17px;
            color: #64748B;
            max-width: 620px;
            margin: 0 auto;
        }

        .pillar-card {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 24px;
            padding: 32px 28px;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .pillar-card:hover {
            transform: translateY(-8px);
            border-color: #CBD5E1;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.12);
        }

        .pillar-icon-box {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 26px;
            margin-bottom: 22px;
        }

        .pillar-icon-orange { background: #FFF7ED; color: #F97316; }
        .pillar-icon-purple { background: #FAF5FF; color: #7C3AED; }
        .pillar-icon-green  { background: #ECFDF5; color: #10B981; }
        .pillar-icon-amber  { background: #FFFBEB; color: #F59E0B; }

        .pillar-title {
            font-size: 19px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 12px;
        }

        .pillar-desc {
            font-size: 14.5px;
            color: #64748B;
            line-height: 1.65;
            margin-bottom: 20px;
        }

        .pillar-link {
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.2s ease;
        }

        .pillar-card:hover .pillar-link {
            gap: 10px;
        }

        /* --- PROGRAMS TAILORED SECTION --- */
        .programs-section {
            padding: 90px 0;
            background: #F8FAFC;
        }

        .program-card {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .program-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.14);
            border-color: #CBD5E1;
        }

        .program-img-box {
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .program-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .program-card:hover .program-img-box img {
            transform: scale(1.06);
        }

        .program-age-pill {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 5px 14px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 800;
            color: #0F172A;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .program-body {
            padding: 26px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .program-title {
            font-size: 20px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 10px;
        }

        .program-desc {
            font-size: 14px;
            color: #64748B;
            margin-bottom: 18px;
        }

        .program-bullets {
            list-style: none;
            padding: 0;
            margin: 0 0 22px 0;
            flex-grow: 1;
        }

        .program-bullets li {
            font-size: 13.5px;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .program-footer-bar {
            padding-top: 18px;
            border-top: 1px solid #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* --- DEEP BRAND GREEN/INDIGO ACCENT BANNER --- */
        .mentor-banner-section {
            padding: 90px 0;
            background: linear-gradient(135deg, #064E3B 0%, #065F46 50%, #1E1B4B 100%);
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
        }

        .mentor-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 14.5px;
            font-weight: 700;
        }

        /* --- LIFE INSIDE CAMPUS GALLERY --- */
        .campus-gallery-section {
            padding: 90px 0;
            background: #FFFFFF;
        }

        .gallery-photo-card {
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            height: 250px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .gallery-photo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-photo-card:hover img {
            transform: scale(1.08);
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px;
            background: linear-gradient(180deg, transparent 0%, rgba(15, 23, 42, 0.85) 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 14px;
        }

        /* --- TESTIMONIALS SECTION --- */
        .testimonials-section {
            padding: 90px 0;
            background: #F8FAFC;
        }

        .review-card {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 22px;
            padding: 30px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* --- SCHEDULE TOUR CTA BANNER --- */
        .tour-cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
            color: #FFFFFF;
            position: relative;
        }

        .tour-form-card {
            background: #FFFFFF;
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.25);
            color: #0F172A;
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
            .main-3d-tablet {
                transform: none;
                margin-top: 40px;
            }
            .floating-badge-stem,
            .floating-badge-success {
                position: static;
                margin: 12px auto;
                animation: none;
                width: 100%;
                max-width: 320px;
            }
        }
    </style>
</head>
<body>

    <!-- =========================================================================
         TOP STICKY NAVBAR (PRESERVING EXACT DESIGN, BRANDING & COLOR SCHEME)
         ========================================================================= -->
    @include('partials.public-header')

    <!-- =========================================================================
         SECTION 1: HERO (EDUKID 3D PLAYFUL & MODERN STYLE)
         ========================================================================= -->
    <section class="hero-section" id="home">
        <div class="hero-glow-orb hero-glow-1"></div>
        <div class="hero-glow-orb hero-glow-2"></div>

        <div class="container position-relative" style="z-index: 5;">
            <div class="row align-items-center g-5">
                <!-- Left Hero Content -->
                <div class="col-lg-6">
                    <div class="hero-badge-pill">
                        <span class="pulse-dot-live"></span>
                        <span>FULL PROGRAMME {{ $academicSession ?? '2026-2027' }} OPEN NOW</span>
                    </div>

                    <h1 class="hero-playful-title">
                        Nurturing Young Minds Through
                        <span class="word-highlight-pill word-orange">Play,</span>
                        <span class="word-highlight-pill word-purple">Wonder & 3D</span>
                        Interactive Learning.
                    </h1>

                    <p class="hero-subtitle">
                        Where early discovery seamlessly blossoms into academic mastery. Blending Montessori methods, cutting-edge STEM robotics labs, and comprehensive character building from Kindergarten to Matriculation.
                    </p>

                    <div class="hero-cta-group">
                        <a href="{{ route('admissions') }}" class="btn-hero-orange">
                            <span>Enroll Your Child</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#tourSection" class="btn-hero-purple-outline">
                            <i class="bi bi-calendar2-event text-purple"></i>
                            <span>Book Campus Tour</span>
                        </a>
                    </div>

                    <div class="hero-rating-strip">
                        <div class="d-flex align-items-center gap-1 text-warning fs-6">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <strong class="text-dark ms-2" style="font-size: 14px;">4.9 / 5</strong>
                        </div>
                        <span class="text-muted small">&bull; (1,200+ Happy Parent Reviews)</span>
                        <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-3 py-1">
                            <i class="bi bi-check-circle-fill me-1"></i> FBISE & Cambridge
                        </span>
                    </div>
                </div>

                <!-- Right 3D Multi-Layer Screen Showcase -->
                <div class="col-lg-6">
                    <div class="hero-3d-stack">
                        <!-- Floating Badge 1 (Top Right) -->
                        <div class="floating-badge-stem">
                            <div class="rounded-3 p-2 bg-purple-subtle text-purple fs-4">
                                <i class="bi bi-robot"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 13.5px;">Teacher & AI STEM Pro</h6>
                                <small class="text-muted" style="font-size: 11px;">Interactive Learning Modules</small>
                            </div>
                        </div>

                        <!-- 3D Tablet Window Screen -->
                        <div class="main-3d-tablet">
                            <div class="tablet-screen">
                                <img src="{{ asset('images/stem_lab.jpg') }}" alt="STEM Learning Lab" onerror="this.onerror=null; this.src='{{ asset('images/landing_hero.jpg') }}';">
                                <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(180deg, transparent 0%, rgba(15, 23, 42, 0.85) 100%); color: #FFF;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-white"><i class="bi bi-broadcast text-danger me-1"></i> Live Innovation Class</h6>
                                            <small class="text-light opacity-75">Hands-on 3D printing & sensor robotics</small>
                                        </div>
                                        <span class="badge bg-primary px-3 py-1 rounded-pill">Active Lab</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Badge 2 (Bottom Left) -->
                        <div class="floating-badge-success">
                            <div class="rounded-3 p-2 bg-success-subtle text-success fs-4">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 13.5px;">99.8% Board Distinction</h6>
                                <small class="text-muted" style="font-size: 11px;">National Merit Positions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 2: FOUR PILLARS OF HOLISTIC GROWTH
         ========================================================================= -->
    <section class="pillars-section">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-tag-pill">EXCELLENCE AT EVERY STEP</span>
                <h2 class="section-heading-bold">Four Pillars of Holistic Growth</h2>
                <p class="section-subheading-clean">
                    Every child is an explorer. We offer diverse academic and co-curricular pathways tailored to each developmental milestone.
                </p>
            </div>

            <div class="row g-4">
                <!-- Pillar 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box pillar-icon-orange">
                            <i class="bi bi-puzzle-fill"></i>
                        </div>
                        <h4 class="pillar-title">Montessori & STEM</h4>
                        <p class="pillar-desc">
                            Hands-on sensory exploration, coding logic, robotics sets, and practical observation from Early Years.
                        </p>
                        <a href="{{ route('academics') }}#preschool" class="pillar-link text-warning">
                            <span>Learn More</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box pillar-icon-purple">
                            <i class="bi bi-person-heart"></i>
                        </div>
                        <h4 class="pillar-title">Nurturing Educators</h4>
                        <p class="pillar-desc">
                            Compassionate, certified teachers delivering individualized attention with a low 15:1 student-to-teacher ratio.
                        </p>
                        <a href="{{ route('about') }}" class="pillar-link text-purple">
                            <span>Meet Culture</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box pillar-icon-green">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="pillar-title">Safe & Active Sports</h4>
                        <p class="pillar-desc">
                            2-acre safe green campus, modern sports courts, karate, gymnastics, athletics, and physical wellness.
                        </p>
                        <a href="{{ route('why.choose.us') }}" class="pillar-link text-success">
                            <span>Explore Grounds</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pillar 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box pillar-icon-amber">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <h4 class="pillar-title">Arts, Music & Ethics</h4>
                        <p class="pillar-desc">
                            Creative studio work, public speaking, Islamic moral ethics, and performing arts fostering well-rounded character.
                        </p>
                        <a href="{{ route('academics') }}" class="pillar-link text-warning">
                            <span>Discover Arts</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 3: PROGRAMS TAILORED TO EVERY AGE STAGE
         ========================================================================= -->
    <section class="programs-section">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between mb-5 gap-3">
                <div>
                    <span class="section-tag-pill">EARLY YEARS TO MATRICULATION</span>
                    <h2 class="section-heading-bold mb-1">Programs Tailored to Every Age Stage</h2>
                    <p class="text-muted mb-0">Select an academic tier tailored to your child's age, growth stage, and learning ambitions.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('academics') }}#preschool" class="btn btn-sm btn-white border rounded-pill px-3 py-2 fw-bold text-dark shadow-sm bg-white">Pre-School</a>
                    <a href="{{ route('academics') }}#primary" class="btn btn-sm btn-white border rounded-pill px-3 py-2 fw-bold text-dark shadow-sm bg-white">Primary Wing</a>
                    <a href="{{ route('academics') }}#matric" class="btn btn-sm btn-white border rounded-pill px-3 py-2 fw-bold text-dark shadow-sm bg-white">Senior FBISE</a>
                    <a href="{{ route('academics') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm" style="background: #7C3AED;">View All Wings &rarr;</a>
                </div>
            </div>

            <div class="row g-4">
                <!-- Program 1: Pre-School -->
                <div class="col-md-6 col-lg-4">
                    <div class="program-card">
                        <div class="program-img-box">
                            <span class="program-age-pill"><i class="bi bi-balloon-heart text-danger me-1"></i> Ages 3 - 5 Years</span>
                            <img src="{{ asset('images/landing_hero.jpg') }}" alt="Little Sprouts Discovery">
                        </div>
                        <div class="program-body">
                            <h4 class="program-title">Little Sprouts Discovery</h4>
                            <p class="program-desc">Nurturing curiosity and fine motor skills through playful sensory activities and phonics.</p>
                            <ul class="program-bullets">
                                <li><i class="bi bi-check2-circle text-primary"></i> Multi-sensory play & alphabet phonics</li>
                                <li><i class="bi bi-check2-circle text-primary"></i> Social sharing, empathy, and manners</li>
                                <li><i class="bi bi-check2-circle text-primary"></i> Daily outdoor supervised sports play</li>
                            </ul>
                            <div class="program-footer-bar">
                                <div>
                                    <small class="text-muted d-block">Wing Enrollment</small>
                                    <strong class="text-dark">Spring 2026 Open</strong>
                                </div>
                                <a href="{{ route('admissions') }}" class="btn btn-sm btn-warning fw-bold px-3 py-2 rounded-3 text-dark">
                                    Apply Now &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program 2: Primary Wing -->
                <div class="col-md-6 col-lg-4">
                    <div class="program-card">
                        <div class="program-img-box">
                            <span class="program-age-pill"><i class="bi bi-cpu text-primary me-1"></i> Grades 1 - 5</span>
                            <img src="{{ asset('images/stem_lab.jpg') }}" alt="Junior Explorers STEM Hub">
                        </div>
                        <div class="program-body">
                            <h4 class="program-title">Junior Explorers STEM Hub</h4>
                            <p class="program-desc">Building strong foundational literacy, conceptual math, science projects, and coding.</p>
                            <ul class="program-bullets">
                                <li><i class="bi bi-check2-circle text-success"></i> Hands-on weekly science experiments</li>
                                <li><i class="bi bi-check2-circle text-success"></i> Scratch coding & robotics logic</li>
                                <li><i class="bi bi-check2-circle text-success"></i> Bilingual English & Urdu eloquence</li>
                            </ul>
                            <div class="program-footer-bar">
                                <div>
                                    <small class="text-muted d-block">Wing Enrollment</small>
                                    <strong class="text-dark">Seats Available</strong>
                                </div>
                                <a href="{{ route('admissions') }}" class="btn btn-sm btn-primary fw-bold px-3 py-2 rounded-3" style="background: #7C3AED;">
                                    Apply Now &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program 3: Senior Wing -->
                <div class="col-md-6 col-lg-4">
                    <div class="program-card">
                        <div class="program-img-box">
                            <span class="program-age-pill"><i class="bi bi-award text-warning me-1"></i> Grades 6 - 10 / Matric</span>
                            <img src="{{ asset('upload/school_campus.jpg') }}" alt="Master Innovators Senior Wing" onerror="this.onerror=null; this.src='{{ asset('images/landing_hero.jpg') }}';">
                        </div>
                        <div class="program-body">
                            <h4 class="program-title">Master Innovators Senior Wing</h4>
                            <p class="program-desc">Rigorous FBISE board syllabus mastery with structured monthly test diagnostics.</p>
                            <ul class="program-bullets">
                                <li><i class="bi bi-check2-circle text-warning"></i> FBISE Board Distinction track record</li>
                                <li><i class="bi bi-check2-circle text-warning"></i> Fully equipped physics, chem & bio labs</li>
                                <li><i class="bi bi-check2-circle text-warning"></i> Career counseling & Olympiad training</li>
                            </ul>
                            <div class="program-footer-bar">
                                <div>
                                    <small class="text-muted d-block">Wing Enrollment</small>
                                    <strong class="text-dark">Merit Admissions</strong>
                                </div>
                                <a href="{{ route('admissions') }}" class="btn btn-sm btn-success fw-bold px-3 py-2 rounded-3 text-white">
                                    Apply Now &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 4: MODERN DEEP EMERALD / BRAND FEATURE BANNER
         ========================================================================= -->
    <section class="mentor-banner-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge px-3 py-2 rounded-pill bg-light text-dark fw-bold mb-3" style="font-size: 13px;">TOP-TIER ACCREDITATION</span>
                    <h2 class="display-6 fw-bold mb-3" style="letter-spacing: -0.02em;">We Are The Best Mentors In Modern Holistic Education</h2>
                    <p class="lead opacity-90 mb-4" style="font-size: 17px; line-height: 1.7;">
                        Dedicated to creating an inspiring environment where every learner thrives academically, socially, and creatively through compassionate guidance and world-class digital tools.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="mentor-feature-item">
                                <i class="bi bi-patch-check-fill fs-5 text-warning"></i>
                                <span>Dual Curriculum Wings</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mentor-feature-item">
                                <i class="bi bi-cpu-fill fs-5 text-info"></i>
                                <span>Smart AI Classrooms</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mentor-feature-item">
                                <i class="bi bi-trophy-fill fs-5 text-warning"></i>
                                <span>30+ Activity Clubs</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mentor-feature-item">
                                <i class="bi bi-clock-history fs-5 text-success"></i>
                                <span>Safe Day Care Support</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admissions') }}" class="btn btn-warning px-4 py-3 fw-bold rounded-3 shadow text-dark">
                            Schedule Campus Visit <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('about') }}" class="btn btn-outline-light px-4 py-3 fw-bold rounded-3">
                            About Our Legacy
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="p-3 rounded-4" style="background: rgba(255, 255, 255, 0.08); border: 1.5px solid rgba(255, 255, 255, 0.15); box-shadow: 0 25px 50px rgba(0,0,0,0.3);">
                        <img src="{{ asset('images/stem_lab.jpg') }}" alt="Innovation Lab Showcase" class="img-fluid rounded-3 w-100 shadow" style="height: 380px; object-fit: cover;">
                        <div class="mt-3 d-flex justify-content-between align-items-center text-white px-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-warning"><i class="bi bi-shield-lock-fill"></i> ISO 9001 & FBISE Certified</h6>
                                <small class="text-light opacity-75">Recognized nationwide for academic standard</small>
                            </div>
                            <span class="badge bg-success px-3 py-2 rounded-pill">Ranked #1 in Sector</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- (NOTE: Faculty Section is EXCLUDED from landing page as requested by user) -->

    <!-- =========================================================================
         SECTION 5: LIFE INSIDE EXCELLENCE CAMPUS (CAMPUS GALLERY)
         ========================================================================= -->
    <section class="campus-gallery-section">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-tag-pill">CAMPUS LIFE & CULTURE</span>
                <h2 class="section-heading-bold">Life Inside Excellence Campus</h2>
                <p class="section-subheading-clean">
                    From robotic hackathons to sports galas and art exhibitions, explore everyday snapshots of our vibrant community.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-6 col-lg-3">
                    <div class="gallery-photo-card">
                        <img src="{{ asset('images/stem_lab.jpg') }}" alt="STEM Robotics Lab">
                        <div class="gallery-caption">STEM & 3D Prototyping</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="gallery-photo-card">
                        <img src="{{ asset('images/landing_hero.jpg') }}" alt="Main Academic Campus">
                        <div class="gallery-caption">2-Acre Green Campus</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="gallery-photo-card">
                        <img src="{{ asset('upload/school_campus.jpg') }}" alt="Sports & Physical Wellness" onerror="this.onerror=null; this.src='{{ asset('images/landing_hero.jpg') }}';">
                        <div class="gallery-caption">Sports Gala & Athletics</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="gallery-photo-card">
                        <img src="{{ asset('images/stem_lab.jpg') }}" alt="Computer & Coding Hub">
                        <div class="gallery-caption">AI Workstations & Coding</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 6: TESTIMONIALS (WHAT 500+ PARENTS SAY)
         ========================================================================= -->
    <section class="testimonials-section">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-tag-pill">PARENT TESTIMONIALS</span>
                <h2 class="section-heading-bold">What 500+ Parents Say About Excellence</h2>
                <p class="section-subheading-clean">
                    Read genuine experiences from mothers, fathers, and guardians whose children thrive at Excellence School System.
                </p>
            </div>

            <div class="row g-4">
                <!-- Review 1 -->
                <div class="col-md-4">
                    <div class="review-card">
                        <div>
                            <div class="text-warning mb-3">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="text-muted" style="font-size: 15px; line-height: 1.7;">
                                "My daughter joined the Early Years wing two years ago. The personalized care and interactive STEM activities boosted her confidence tremendously!"
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: #F97316;">
                                FA
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Farah Ahmed</h6>
                                <small class="text-muted">Mother of Ayla (Grade 1)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="col-md-4">
                    <div class="review-card">
                        <div>
                            <div class="text-warning mb-3">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="text-muted" style="font-size: 15px; line-height: 1.7;">
                                "The online academic portal is fantastic. We can monitor attendance, monthly test session scores, and teacher notes in real-time from anywhere."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: #7C3AED;">
                                TR
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Tariq Rehman</h6>
                                <small class="text-muted">Father of Bilal (Class 8)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="col-md-4">
                    <div class="review-card">
                        <div>
                            <div class="text-warning mb-3">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="text-muted" style="font-size: 15px; line-height: 1.7;">
                                "Outstanding FBISE board preparation! The faculty goes above and beyond with extra remedial sessions. My son scored 96% in his Matric exams."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: #10B981;">
                                SK
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Dr. Saima Khan</h6>
                                <small class="text-muted">Parent of Hamza (Matric Board)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 7: SCHEDULE CAMPUS TOUR CTA (EDUKID STYLE)
         ========================================================================= -->
    <section class="tour-cta-section" id="tourSection">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge px-3 py-2 rounded-pill bg-white text-dark fw-bold mb-2">CAMPUS VISIT & REGISTRATION</span>
                <h2 class="display-6 fw-bold mb-2">Schedule Your Private Campus Tour Today</h2>
                <p class="lead opacity-90 max-w-600 mx-auto" style="font-size: 16px;">
                    Experience our smart classrooms, STEM robotics labs, and meet the leadership team in person.
                </p>
            </div>

            <!-- Tour Success Alert -->
            @if(session('tour_success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 shadow-sm mb-4 mx-auto max-w-700" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Campus Tour Booked!</h5>
                            <p class="mb-0 text-muted">{{ session('tour_success') }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="tour-form-card mx-auto" style="max-width: 820px;">
                <form action="{{ route('tour.schedule') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark mb-1">Contact Phone</label>
                            <input type="tel" name="parent_phone" class="form-control py-2 rounded-3" placeholder="0300-1234567" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark mb-1">Select Wing</label>
                            <select name="student_wing" class="form-select py-2 rounded-3" required>
                                <option value="" disabled selected>Select Wing</option>
                                <option value="Early Childhood Wing">Early Childhood Wing</option>
                                <option value="Primary School Wing">Primary School Wing</option>
                                <option value="Senior / Matric Wing">Senior / Matric Wing</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark mb-1">Preferred Date</label>
                            <input type="date" name="tour_date" class="form-control py-2 rounded-3" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow" style="background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%);">
                                <i class="bi bi-calendar-check-fill me-2"></i> Book Free Campus Visit & Tour
                            </button>
                            <div class="text-center mt-2 small text-muted">
                                <i class="bi bi-lock-fill text-success me-1"></i> No obligation &bull; Free prospectus on arrival
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         FOOTER (WHY CHOOSE US & HELP DESK SHIFTED HERE AS REQUESTED)
         ========================================================================= -->
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
</body>
</html>
