<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ config('languages.supported.'.app()->getLocale().'.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Help Desk & Support Center') }} - Excellence School System</title>

    <link rel="icon" type="image/png" href="{{ asset('images/school_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/school_logo.png') }}">

    <!-- Google Fonts & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #064e3b;
            --primary-hover: #043e2f;
            --primary-dark: #022c22;
            --primary-accent: #0d9488;
            --primary-light: #ecfdf5;
            --primary-mint: #e6f4f1;
            --primary-border: #a7f3d0;
            --emerald-600: #059669;
            --emerald-500: #10b981;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-300: #cbd5e1;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.07), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            --card-shadow-hover: 0 20px 30px -10px rgba(6, 78, 59, 0.12), 0 10px 15px -5px rgba(0, 0, 0, 0.05);
            --radius-lg: 20px;
            --radius-md: 14px;
            --radius-pill: 9999px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f4f7f5;
            color: var(--slate-800);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Header */
        .support-header {
            background: #ffffff;
            border-bottom: 1px solid var(--slate-200);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.94);
        }

        .header-inner {
            max-width: 1240px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-wrap {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid rgba(6, 78, 59, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            box-shadow: 0 4px 10px rgba(6, 78, 59, 0.1);
        }

        .brand-logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .brand-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 19px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary-accent);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-header-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            background: #ffffff;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-pill);
            color: var(--slate-700);
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .btn-header-back:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary-border);
            transform: translateX(-2px);
        }

        .btn-header-signin {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            background: var(--primary);
            border: 1.5px solid var(--primary);
            border-radius: var(--radius-pill);
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(6, 78, 59, 0.25);
        }

        .btn-header-signin:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Hero Banner Section */
        .hero-section {
            background: linear-gradient(135deg, #043e2f 0%, #064e3b 50%, #065f46 100%);
            color: #ffffff;
            padding: 56px 24px 64px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.22) 0%, rgba(6, 78, 59, 0) 70%);
            pointer-events: none;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(167, 243, 208, 0.12) 0%, rgba(6, 78, 59, 0) 70%);
            pointer-events: none;
        }

        .hero-container {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .hero-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: var(--radius-pill);
            font-size: 12.5px;
            font-weight: 700;
            color: #a7f3d0;
            letter-spacing: 0.3px;
            margin-bottom: 20px;
            backdrop-filter: blur(8px);
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 38px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            font-size: 16px;
            color: #d1fae5;
            max-width: 680px;
            margin: 0 auto 32px;
            font-weight: 400;
            line-height: 1.6;
        }

        /* Search Bar */
        .search-box-wrap {
            max-width: 620px;
            margin: 0 auto;
            position: relative;
        }

        .search-input-field {
            width: 100%;
            padding: 16px 20px 16px 52px;
            background: #ffffff;
            border: 2px solid transparent;
            border-radius: var(--radius-pill);
            font-size: 15px;
            font-weight: 500;
            color: var(--slate-800);
            outline: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
            transition: all 0.25s ease;
        }

        .search-input-field:focus {
            border-color: #a7f3d0;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25), 0 0 0 4px rgba(13, 148, 136, 0.25);
        }

        .search-icon-pos {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-accent);
            font-size: 19px;
            pointer-events: none;
        }

        .search-clear-btn {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: #f1f5f9;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            color: var(--slate-600);
            cursor: pointer;
            font-size: 14px;
            transition: background 0.15s;
        }

        .search-clear-btn:hover {
            background: #e2e8f0;
            color: var(--slate-900);
        }

        /* Main Shell */
        .main-content-wrapper {
            max-width: 1240px;
            margin: -28px auto 60px;
            padding: 0 24px;
            position: relative;
            z-index: 5;
            width: 100%;
            flex: 1;
        }

        /* Contact Cards Grid */
        .contact-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 38px;
        }

        .contact-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 24px 20px;
            border: 1px solid var(--slate-200);
            box-shadow: var(--card-shadow);
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .contact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-accent));
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .contact-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
            border-color: #cbd5e1;
        }

        .contact-card:hover::before {
            opacity: 1;
        }

        .card-icon-bubble {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
            transition: all 0.2s ease;
        }

        .contact-card:hover .card-icon-bubble {
            background: var(--primary);
            color: #ffffff;
            transform: scale(1.08);
        }

        .card-label-small {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-accent);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .card-main-title {
            font-size: 16.5px;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .card-description {
            font-size: 13px;
            color: var(--slate-500);
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .card-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 9px 14px;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .card-action-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary-border);
            color: var(--primary-hover);
        }

        /* Two-Column Section */
        .layout-split {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 28px;
            align-items: start;
        }

        /* FAQ Side */
        .faq-panel {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--slate-200);
            padding: 30px;
            box-shadow: var(--card-shadow);
        }

        .section-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--slate-200);
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-badge-count {
            background: var(--primary-light);
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: var(--radius-pill);
            border: 1px solid var(--primary-border);
        }

        /* Category Filter Pills */
        .faq-filter-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .filter-btn {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-pill);
            padding: 7px 16px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .filter-btn:hover {
            background: var(--slate-100);
            color: var(--slate-900);
        }

        .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(6, 78, 59, 0.2);
        }

        /* Accordion List */
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .faq-item {
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            overflow: hidden;
            transition: all 0.25s ease;
            background: #ffffff;
        }

        .faq-item:hover {
            border-color: #cbd5e1;
        }

        .faq-item.active {
            border-color: var(--primary-border);
            box-shadow: 0 4px 12px rgba(6, 78, 59, 0.06);
            background: #fcfffe;
        }

        .faq-question-btn {
            width: 100%;
            text-align: left;
            background: transparent;
            border: none;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            cursor: pointer;
            font-family: inherit;
        }

        .question-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .question-category-tag {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--slate-100);
            color: var(--slate-600);
            white-space: nowrap;
        }

        .faq-item.active .question-category-tag {
            background: var(--primary-light);
            color: var(--primary);
        }

        .question-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--slate-900);
            line-height: 1.4;
        }

        .faq-item.active .question-title {
            color: var(--primary);
        }

        .faq-chevron {
            width: 32px;
            height: 32px;
            min-width: 32px;
            border-radius: 50%;
            background: var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--slate-600);
            font-size: 13px;
            transition: all 0.25s ease;
        }

        .faq-item.active .faq-chevron {
            background: var(--primary);
            color: #ffffff;
            transform: rotate(180deg);
        }

        .faq-answer-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0, 1, 0, 1);
        }

        .faq-item.active .faq-answer-panel {
            max-height: 800px;
            transition: max-height 0.4s ease-in-out;
        }

        .faq-answer-body {
            padding: 0 20px 20px 20px;
            font-size: 14px;
            color: var(--slate-600);
            line-height: 1.65;
            border-top: 1px dashed var(--slate-200);
            margin-top: 4px;
            padding-top: 14px;
        }

        .faq-answer-body p + p {
            margin-top: 10px;
        }

        .faq-answer-body ul, .faq-answer-body ol {
            margin: 10px 0 10px 20px;
        }

        .faq-answer-body li {
            margin-bottom: 6px;
        }

        .highlight-link {
            color: var(--primary);
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .highlight-link:hover {
            color: var(--primary-accent);
        }

        .no-results-box {
            display: none;
            text-align: center;
            padding: 40px 20px;
            color: var(--slate-500);
        }

        .no-results-box i {
            font-size: 40px;
            color: var(--slate-300);
            margin-bottom: 12px;
            display: block;
        }

        /* Form Side */
        .ticket-panel {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--slate-200);
            padding: 30px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 90px;
        }

        .ticket-panel-header {
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--slate-200);
        }

        .ticket-title {
            font-family: 'Outfit', sans-serif;
            font-size: 21px;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }

        .ticket-subtitle {
            font-size: 13.5px;
            color: var(--slate-500);
        }

        /* Alert Box */
        .success-ticket-alert {
            background: #ecfdf5;
            border: 1.5px solid #a7f3d0;
            border-radius: var(--radius-md);
            padding: 18px 20px;
            margin-bottom: 20px;
            color: #064e3b;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .success-icon-wrap {
            width: 36px;
            height: 36px;
            min-width: 36px;
            background: #10b981;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .success-content h4 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .ticket-badge-pill {
            display: inline-block;
            background: #064e3b;
            color: #ffffff;
            font-family: monospace;
            font-size: 12.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            margin: 2px 0 6px;
        }

        .success-content p {
            font-size: 13px;
            line-height: 1.5;
            color: #043e2f;
        }

        .form-row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .form-field-group {
            margin-bottom: 14px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 6px;
        }

        .field-input, .field-select, .field-textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--slate-200);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: inherit;
            color: var(--slate-800);
            background: var(--slate-50);
            outline: none;
            transition: all 0.2s ease;
        }

        .field-input:focus, .field-select:focus, .field-textarea:focus {
            background: #ffffff;
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
        }

        .field-textarea {
            resize: vertical;
            min-height: 110px;
        }

        .field-error-msg {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
            display: block;
        }

        .btn-submit-ticket {
            width: 100%;
            padding: 13px 20px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-pill);
            font-size: 14.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(6, 78, 59, 0.25);
            transition: all 0.2s ease;
            margin-top: 8px;
        }

        .btn-submit-ticket:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(6, 78, 59, 0.32);
        }

        .btn-submit-ticket:active {
            transform: translateY(0);
        }

        .guarantee-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 14px;
            font-size: 12px;
            color: var(--slate-500);
            text-align: center;
        }

        /* Footer */
        .support-footer {
            background: #ffffff;
            border-top: 1px solid var(--slate-200);
            padding: 28px 24px;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13px;
            color: var(--slate-500);
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: var(--slate-600);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .contact-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .layout-split {
                grid-template-columns: 1fr;
            }

            .ticket-panel {
                position: static;
            }
        }

        @media (max-width: 640px) {
            .hero-title {
                font-size: 28px;
            }

            .contact-cards-grid {
                grid-template-columns: 1fr;
            }

            .form-row-grid {
                grid-template-columns: 1fr;
            }

            .header-inner {
                padding: 12px 16px;
            }

            .main-content-wrapper {
                padding: 0 16px;
                margin-top: -16px;
            }

            .faq-panel, .ticket-panel {
                padding: 20px 16px;
            }

            .footer-inner {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- TOP HEADER -->
    <header class="support-header">
        <div class="header-inner">
            <a href="{{ route('login') }}" class="brand-link" title="{{ __('Return to Portal') }}">
                <div class="brand-logo-wrap">
                    <img src="{{ asset('images/school_logo.png') }}" alt="Excellence School System Logo">
                </div>
                <div class="brand-text">
                    <h1>Excellence School System</h1>
                    <span>{{ __('Help Desk & Support Center') }}</span>
                </div>
            </a>

            <div class="header-actions">
                <a href="{{ route('login') }}" class="btn-header-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>{{ __('Back to Sign In') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-pill-badge">
                <i class="bi bi-shield-check"></i>
                <span>{{ __('Official 24/7 Help Desk & Academic Portal Support') }}</span>
            </div>
            <h2 class="hero-title">{{ __('How can our Support Team assist you?') }}</h2>
            <p class="hero-subtitle">
                {{ __('Find quick guidance on student portals, roll number slips, fee challans, exam results, or submit an official inquiry directly to our IT and Academic Administration.') }}
            </p>

            <!-- LIVE SEARCH BAR -->
            <div class="search-box-wrap">
                <i class="bi bi-search search-icon-pos"></i>
                <input 
                    type="text" 
                    id="faqSearchInput" 
                    class="search-input-field" 
                    placeholder="{{ __('Search help topics (e.g., password reset, roll slip, fee voucher, login issue)...') }}"
                    autocomplete="off"
                >
                <button type="button" id="searchClearBtn" class="search-clear-btn" title="{{ __('Clear search') }}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- MAIN SHELL -->
    <main class="main-content-wrapper">

        <!-- DIRECT CONTACT CARDS -->
        <div class="contact-cards-grid">
            
            <!-- Email -->
            <div class="contact-card">
                <div>
                    <div class="card-icon-bubble">
                        <i class="bi bi-envelope-at-fill"></i>
                    </div>
                    <div class="card-label-small">{{ __('24/7 Help Desk Email') }}</div>
                    <div class="card-main-title">support@excellence.edu.pk</div>
                    <div class="card-description">
                        {{ __('Monitored round the clock. Official student and teacher inquiry channel.') }}
                    </div>
                </div>
                <a href="mailto:support@excellence.edu.pk?subject=Portal%20Support%20Request" class="card-action-btn">
                    <span>{{ __('Send Direct Email') }}</span>
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>

            <!-- Helpline -->
            <div class="contact-card">
                <div>
                    <div class="card-icon-bubble">
                        <i class="bi bi-telephone-inbound-fill"></i>
                    </div>
                    <div class="card-label-small">{{ __('Telephone Helpline') }}</div>
                    <div class="card-main-title">+92 (042) 111-222-333</div>
                    <div class="card-description">
                        {{ __('Admissions, Fee Office & General Helpline (Mon–Sat: 8:00 AM – 4:00 PM).') }}
                    </div>
                </div>
                <a href="tel:+92042111222333" class="card-action-btn">
                    <span>{{ __('Call Helpline') }}</span>
                    <i class="bi bi-telephone"></i>
                </a>
            </div>

            <!-- Password Reset -->
            <div class="contact-card">
                <div>
                    <div class="card-icon-bubble">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div class="card-label-small">{{ __('Account Access') }}</div>
                    <div class="card-main-title">{{ __('Password Assistance') }}</div>
                    <div class="card-description">
                        {{ __('Forgot your portal password? Request administrative verification and credential recovery.') }}
                    </div>
                </div>
                <a href="{{ route('password.request') }}" class="card-action-btn">
                    <span>{{ __('Reset Password') }}</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <!-- Campus IT Office -->
            <div class="contact-card">
                <div>
                    <div class="card-icon-bubble">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="card-label-small">{{ __('Main Campus Office') }}</div>
                    <div class="card-main-title">{{ __('IT & Registrar Desk') }}</div>
                    <div class="card-description">
                        {{ __('Room 102, Academic Block A, Excellence School System Main Campus.') }}
                    </div>
                </div>
                <a href="#supportTicketSection" class="card-action-btn">
                    <span>{{ __('Submit Query Below') }}</span>
                    <i class="bi bi-arrow-down"></i>
                </a>
            </div>

        </div>

        <!-- TWO COLUMN LAYOUT: FAQ + TICKET FORM -->
        <div class="layout-split">

            <!-- LEFT COLUMN: INTERACTIVE FAQ KNOWLEDGE BASE -->
            <div class="faq-panel">
                
                <div class="section-header-row">
                    <h3 class="section-title">
                        <i class="bi bi-question-circle-fill" style="color: var(--primary-accent);"></i>
                        <span>{{ __('Frequently Asked Questions') }}</span>
                    </h3>
                    <span class="section-badge-count" id="faqCounter">{{ __('8 Questions') }}</span>
                </div>

                <!-- CATEGORY FILTER BUTTONS -->
                <div class="faq-filter-nav">
                    <button type="button" class="filter-btn active" data-filter="all">{{ __('All Questions') }}</button>
                    <button type="button" class="filter-btn" data-filter="portal">{{ __('Portal Login') }}</button>
                    <button type="button" class="filter-btn" data-filter="student">{{ __('Students & Slips') }}</button>
                    <button type="button" class="filter-btn" data-filter="fees">{{ __('Fees & Challans') }}</button>
                    <button type="button" class="filter-btn" data-filter="teacher">{{ __('Teachers & Staff') }}</button>
                </div>

                <!-- FAQ ITEMS -->
                <div class="faq-list" id="faqAccordionList">

                    <!-- FAQ 1 -->
                    <div class="faq-item" data-category="portal">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Portal Login') }}</span>
                                <span class="question-title">{{ __('How do I sign in to the Student, Teacher, or Admin portal?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('To log in to your designated portal:') }}</p>
                                <ol>
                                    <li>{{ __('Navigate to the') }} <a href="{{ route('login') }}" class="highlight-link">{{ __('Sign In Page') }}</a>.</li>
                                    <li>{{ __('Choose your role switch at the top of the form:') }} <strong>{{ __('Student') }}</strong>, <strong>{{ __('Teacher') }}</strong>, {{ __('or') }} <strong>{{ __('Admin') }}</strong>.</li>
                                    <li>{{ __('Type in your registered email address and portal password.') }}</li>
                                    <li>{{ __('Click') }} <strong>{{ __('Sign In to Dashboard') }}</strong> {{ __('to proceed.') }}</li>
                                </ol>
                                <p>{{ __('Ensure you select the exact role tab corresponding to your account to avoid "Invalid Credentials" notices.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="faq-item" data-category="portal">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Portal Login') }}</span>
                                <span class="question-title">{{ __('I forgot my password or cannot access my account. What should I do?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('For privacy and security, portal passwords are verified and reset through the school administration:') }}</p>
                                <ul>
                                    <li>{{ __('Visit the') }} <a href="{{ route('password.request') }}" class="highlight-link">{{ __('Password Help Page') }}</a>.</li>
                                    <li>{{ __('Submit your registered portal email address.') }}</li>
                                    <li>{{ __('The school administrator will verify your identity against school records and provide your reset credentials securely.') }}</li>
                                </ul>
                                <p>{{ __('Alternatively, you may contact the IT desk directly at') }} <a href="mailto:support@excellence.edu.pk" class="highlight-link">support@excellence.edu.pk</a> {{ __('or visit Academic Block A, Room 102.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="faq-item" data-category="student">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Students & Slips') }}</span>
                                <span class="question-title">{{ __('Where can students view and download Roll Number Slips?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('Students can obtain their official examination roll slips directly through their portal:') }}</p>
                                <ol>
                                    <li>{{ __('Sign in to your') }} <strong>{{ __('Student Portal') }}</strong>.</li>
                                    <li>{{ __('Open the') }} <strong>{{ __('Roll Slips') }}</strong> {{ __('tab from the sidebar menu.') }}</li>
                                    <li>{{ __('Once the examination department issues the slips for your class session, a') }} <strong>{{ __('Download Roll Slip') }}</strong> {{ __('button will appear.') }}</li>
                                    <li>{{ __('The slip includes your registration number, exam schedule, allotted center, and QR/barcode authentication.') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="faq-item" data-category="fees">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Fees & Challans') }}</span>
                                <span class="question-title">{{ __('How do I download my monthly Fee Voucher and record payment?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('To manage school fees:') }}</p>
                                <ul>
                                    <li>{{ __('Log in as a student and navigate to the') }} <strong>{{ __('Fees') }}</strong> {{ __('section.') }}</li>
                                    <li>{{ __('Click') }} <strong>{{ __('Download Fee Voucher') }}</strong> {{ __('to generate a printable bank challan copy.') }}</li>
                                    <li>{{ __('You can submit your payment at designated partner bank branches or upload online payment confirmation for admin review.') }}</li>
                                    <li>{{ __('Once verified by the Accounts Department, your fee status will automatically update to') }} <strong>{{ __('Paid') }}</strong>.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="faq-item" data-category="student">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Students & Slips') }}</span>
                                <span class="question-title">{{ __('Where can I check monthly test session results and attendance?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('Comprehensive academic progress tracking is accessible via the Student Portal:') }}</p>
                                <ul>
                                    <li><strong>{{ __('Results:') }}</strong> {{ __('Go to the') }} <em>{{ __('Results / Test Sessions') }}</em> {{ __('page to view class-wise monthly test scores, exam grades, and download official marks sheets.') }}</li>
                                    <li><strong>{{ __('Attendance:') }}</strong> {{ __('Open the') }} <em>{{ __('Attendance') }}</em> {{ __('menu to see daily presence, total absent days, and monthly attendance percentages.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 6 -->
                    <div class="faq-item" data-category="teacher">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Teachers & Staff') }}</span>
                                <span class="question-title">{{ __('I am a new teacher. How do I get portal access?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('Faculty members can request teacher portal activation:') }}</p>
                                <ol>
                                    <li>{{ __('Click') }} <a href="/signup" class="highlight-link">{{ __('Create an Account') }}</a> {{ __('or visit the Sign Up page.') }}</li>
                                    <li>{{ __('Enter your official teacher profile email, full name, and mobile number.') }}</li>
                                    <li>{{ __('Submit the request. The school management and principal will approve your account and activate your sign-in privileges.') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 7 -->
                    <div class="faq-item" data-category="teacher">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Teachers & Staff') }}</span>
                                <span class="question-title">{{ __('How do teachers punch in/out and submit leave applications?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('Inside the Teacher Portal:') }}</p>
                                <ul>
                                    <li><strong>{{ __('Punch In / Out:') }}</strong> {{ __('Use the dedicated Punch button on your dashboard each morning upon arriving at school.') }}</li>
                                    <li><strong>{{ __('Leave Requests:') }}</strong> {{ __('Go to') }} <em>{{ __('Leaves') }}</em>, {{ __('select your leave dates, reason, and submit for Admin approval.') }}</li>
                                    <li><strong>{{ __('Course Outlines & Marks:') }}</strong> {{ __('Upload syllabi and input student test scores for your assigned subjects directly through the portal.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 8 -->
                    <div class="faq-item" data-category="portal">
                        <button type="button" class="faq-question-btn" aria-expanded="false">
                            <div class="question-meta">
                                <span class="question-category-tag">{{ __('Portal Login') }}</span>
                                <span class="question-title">{{ __('The login page gives an error or gets stuck. What should I check?') }}</span>
                            </div>
                            <div class="faq-chevron">
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer-panel">
                            <div class="faq-answer-body">
                                <p>{{ __('If you encounter any browser or connectivity hiccups:') }}</p>
                                <ul>
                                    <li>{{ __('Ensure your browser is up to date (Google Chrome, Microsoft Edge, Safari, and Firefox are recommended).') }}</li>
                                    <li>{{ __('Clear temporary browser cache and cookies or open an Incognito / Private window.') }}</li>
                                    <li>{{ __('Verify that caps lock is turned off when typing your password.') }}</li>
                                    <li>{{ __('If problem persists, submit the support form on this page so our IT administrator can inspect your account status.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- NO SEARCH RESULTS PLACEHOLDER -->
                <div class="no-results-box" id="noSearchResults">
                    <i class="bi bi-search"></i>
                    <h4>{{ __('No matching answers found') }}</h4>
                    <p>{{ __('Try searching with different keywords or submit an inquiry using the support form.') }}</p>
                </div>

            </div>

            <!-- RIGHT COLUMN: SUBMIT INQUIRY TICKET FORM -->
            <div class="ticket-panel" id="supportTicketSection">
                
                <div class="ticket-panel-header">
                    <h3 class="ticket-title">
                        <i class="bi bi-headset" style="color: var(--primary-accent);"></i>
                        <span>{{ __('Submit a Support Ticket') }}</span>
                    </h3>
                    <p class="ticket-subtitle">
                        {{ __('Need personalized help? Submit a ticket and our IT & Student Support department will reply promptly.') }}
                    </p>
                </div>

                {{-- SUCCESS CONFIRMATION BANNER --}}
                @if(session('ticket_success'))
                    <div class="success-ticket-alert">
                        <div class="success-icon-wrap">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="success-content">
                            <h4>{{ __('Inquiry Successfully Logged!') }}</h4>
                            <div>{{ __('Ticket Tracking ID:') }} <span class="ticket-badge-pill">#{{ session('ticket_success')['ticket_id'] }}</span></div>
                            <p>
                                {{ __('Thank you,') }} <strong>{{ session('ticket_success')['name'] }}</strong>{{ __('. Our IT support desk has received your ticket and an officer will reach out to') }} <strong>{{ session('ticket_success')['email'] }}</strong> {{ __('shortly.') }}
                            </p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: #fee2e2; border: 1.5px solid #fca5a5; border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 16px; color: #991b1b; font-size: 13px;">
                        <strong><i class="bi bi-exclamation-circle-fill"></i> {{ __('Please complete the form properly:') }}</strong>
                        <ul style="margin: 6px 0 0 18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('support.submit') }}" method="POST" id="supportInquiryForm" novalidate>
                    @csrf

                    <div class="form-row-grid">
                        <div class="form-field-group">
                            <label for="supportName" class="field-label">{{ __('Your Full Name') }} <span style="color:#ef4444;">*</span></label>
                            <input 
                                type="text" 
                                id="supportName" 
                                name="name" 
                                class="field-input" 
                                placeholder="{{ __('e.g. Ahmad Ali') }}" 
                                value="{{ old('name') }}"
                                required
                            >
                        </div>

                        <div class="form-field-group">
                            <label for="supportEmail" class="field-label">{{ __('Your Email Address') }} <span style="color:#ef4444;">*</span></label>
                            <input 
                                type="email" 
                                id="supportEmail" 
                                name="email" 
                                class="field-input" 
                                placeholder="{{ __('e.g. student@gmail.com') }}" 
                                value="{{ old('email') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-row-grid">
                        <div class="form-field-group">
                            <label for="supportRole" class="field-label">{{ __('Your Role') }} <span style="color:#ef4444;">*</span></label>
                            <select id="supportRole" name="role" class="field-select" required>
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>{{ __('Select your portal role...') }}</option>
                                <option value="Student" {{ old('role') === 'Student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                                <option value="Teacher" {{ old('role') === 'Teacher' ? 'selected' : '' }}>{{ __('Teacher / Faculty') }}</option>
                                <option value="Parent" {{ old('role') === 'Parent' ? 'selected' : '' }}>{{ __('Parent / Guardian') }}</option>
                                <option value="Admin / Staff" {{ old('role') === 'Admin / Staff' ? 'selected' : '' }}>{{ __('Staff / Other') }}</option>
                            </select>
                        </div>

                        <div class="form-field-group">
                            <label for="supportCategory" class="field-label">{{ __('Issue Category') }} <span style="color:#ef4444;">*</span></label>
                            <select id="supportCategory" name="category" class="field-select" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>{{ __('Select Category...') }}</option>
                                <option value="Login & Password Access" {{ old('category') === 'Login & Password Access' ? 'selected' : '' }}>{{ __('Login & Password Access') }}</option>
                                <option value="Roll Slip & Exam Schedule" {{ old('category') === 'Roll Slip & Exam Schedule' ? 'selected' : '' }}>{{ __('Roll Slip & Exam Schedule') }}</option>
                                <option value="Fee Voucher & Challan" {{ old('category') === 'Fee Voucher & Challan' ? 'selected' : '' }}>{{ __('Fee Voucher & Challan') }}</option>
                                <option value="Test Results & Attendance" {{ old('category') === 'Test Results & Attendance' ? 'selected' : '' }}>{{ __('Test Results & Attendance') }}</option>
                                <option value="Teacher Portal Sign Up" {{ old('category') === 'Teacher Portal Sign Up' ? 'selected' : '' }}>{{ __('Teacher Portal Sign Up') }}</option>
                                <option value="Technical Bug / Other" {{ old('category') === 'Technical Bug / Other' ? 'selected' : '' }}>{{ __('Technical Bug / Other') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field-group">
                        <label for="supportSubject" class="field-label">{{ __('Subject / Issue Summary') }} <span style="color:#ef4444;">*</span></label>
                        <input 
                            type="text" 
                            id="supportSubject" 
                            name="subject" 
                            class="field-input" 
                            placeholder="{{ __('Briefly describe what you need help with...') }}" 
                            value="{{ old('subject') }}"
                            required
                        >
                    </div>

                    <div class="form-field-group">
                        <label for="supportMessage" class="field-label">{{ __('Detailed Description') }} <span style="color:#ef4444;">*</span></label>
                        <textarea 
                            id="supportMessage" 
                            name="message" 
                            class="field-textarea" 
                            placeholder="{{ __('Please provide details (e.g., student registration number, class, error message received)...') }}" 
                            required
                        >{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn-submit-ticket">
                        <i class="bi bi-send-fill"></i>
                        <span>{{ __('Submit Support Ticket') }}</span>
                    </button>

                    <div class="guarantee-note">
                        <i class="bi bi-shield-lock-fill" style="color: var(--primary-accent);"></i>
                        <span>{{ __('Your data is strictly encrypted and shared only with verified school staff.') }}</span>
                    </div>

                </form>

            </div>

        </div>

    </main>

    <!-- FOOTER -->
    <footer class="support-footer">
        <div class="footer-inner">
            <div>
                © {{ date('Y') }} <strong>Excellence School System</strong>. {{ __('All rights reserved.') }} {{ __('24/7 IT Help Desk.') }}
            </div>
            <div class="footer-links">
                <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> {{ __('Sign In') }}</a>
                <a href="/signup"><i class="bi bi-person-plus"></i> {{ __('Teacher Sign Up') }}</a>
                <a href="{{ route('password.request') }}"><i class="bi bi-key"></i> {{ __('Password Help') }}</a>
                <a href="mailto:support@excellence.edu.pk"><i class="bi bi-envelope"></i> {{ __('Email Desk') }}</a>
            </div>
        </div>
    </footer>

    <!-- INTERACTIVE JAVASCRIPT LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Accordion Toggle
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const questionBtn = item.querySelector('.faq-question-btn');
                questionBtn.addEventListener('click', () => {
                    const isOpen = item.classList.contains('active');
                    
                    // Close others if desired, or keep multiple open
                    faqItems.forEach(other => {
                        if (other !== item) {
                            other.classList.remove('active');
                            other.querySelector('.faq-question-btn').setAttribute('aria-expanded', 'false');
                        }
                    });

                    if (isOpen) {
                        item.classList.remove('active');
                        questionBtn.setAttribute('aria-expanded', 'false');
                    } else {
                        item.classList.add('active');
                        questionBtn.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            // Open the first FAQ item by default for immediate feedback
            if (faqItems.length > 0) {
                faqItems[0].classList.add('active');
                faqItems[0].querySelector('.faq-question-btn').setAttribute('aria-expanded', 'true');
            }

            // Category Filter Buttons
            const filterBtns = document.querySelectorAll('.filter-btn');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const category = btn.getAttribute('data-filter');
                    
                    applyFaqFilters();
                });
            });

            // Live Search Functionality
            const searchInput = document.getElementById('faqSearchInput');
            const searchClearBtn = document.getElementById('searchClearBtn');
            const noResultsBox = document.getElementById('noSearchResults');
            const faqCounter = document.getElementById('faqCounter');

            searchInput.addEventListener('input', function () {
                if (this.value.trim().length > 0) {
                    searchClearBtn.style.display = 'flex';
                } else {
                    searchClearBtn.style.display = 'none';
                }
                applyFaqFilters();
            });

            searchClearBtn.addEventListener('click', function () {
                searchInput.value = '';
                searchClearBtn.style.display = 'none';
                searchInput.focus();
                applyFaqFilters();
            });

            function applyFaqFilters() {
                const query = searchInput.value.toLowerCase().trim();
                const activeFilterBtn = document.querySelector('.filter-btn.active');
                const activeCategory = activeFilterBtn ? activeFilterBtn.getAttribute('data-filter') : 'all';

                let visibleCount = 0;

                faqItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    const text = item.innerText.toLowerCase();

                    const matchesCategory = (activeCategory === 'all' || itemCategory === activeCategory);
                    const matchesSearch = (!query || text.includes(query));

                    if (matchesCategory && matchesSearch) {
                        item.style.display = 'block';
                        visibleCount++;
                        // If searching, expand matching items
                        if (query) {
                            item.classList.add('active');
                            item.querySelector('.faq-question-btn').setAttribute('aria-expanded', 'true');
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (faqCounter) {
                    faqCounter.textContent = visibleCount + ' ' + (visibleCount === 1 ? @json(__('Question')) : @json(__('Questions')));
                }

                if (visibleCount === 0) {
                    noResultsBox.style.display = 'block';
                } else {
                    noResultsBox.style.display = 'none';
                }
            }

        });
    </script>
</body>
</html>
