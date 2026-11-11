<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('languages.supported.'.app()->getLocale().'.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} - Excellence School System</title>

    <link rel="icon" type="image/png" href="{{ asset('images/school_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/school_logo.png') }}">
    
    <!-- Bootstrap Icons & Google Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">

    @include('partials.locale-head')
</head>
<body>

    <!-- TOP NAVIGATION BAR -->
    <header class="top-navbar">
        <a href="/" class="btn-back-home" title="{{ __('Back') }}">
            <i class="bi bi-arrow-left"></i>
            <span>{{ __('Back') }}</span>
        </a>
        <div class="top-nav-actions">
            <a href="{{ route('support') }}" class="btn-header-support" title="{{ __('Help Desk & Support') }}" aria-label="{{ __('Help & Support') }}">
                <i class="bi bi-question-lg"></i>
            </a>
        </div>
    </header>

    <!-- MAIN LOGIN SHELL -->
    <main class="login-wrapper">
        <div class="login-card-container">

            <!-- LEFT COLUMN: SIGN IN FORM -->
            <div class="form-side">
                
                <!-- BRAND LOGO BADGE (CENTERED) -->
                <a href="/" title="{{ __('Back to Home') }}" style="text-decoration:none;">
                    <div class="badge-logo-outer">
                        <div class="badge-logo-inner">
                            <img src="{{ asset('images/school_logo.png') }}" alt="Excellence School System Logo">
                        </div>
                    </div>
                </a>

                <!-- TITLE & SUBTITLE (CENTERED) -->
                <h1 class="form-heading">Excellence School System</h1>
                <p class="form-subheading">{{ __('Enter your email and password to access the portal') }}</p>

                <!-- SUCCESS ALERT -->
                @if(session('success'))
                    <div class="alert-box alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- ERROR ALERT -->
                @if(session('error'))
                    <div class="alert-box alert-error">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- LOGIN FORM -->
                <form action="/login" method="POST" id="loginForm" novalidate>
                    @csrf

                    <fieldset class="login-role-switch">
                        <legend>{{ __('Sign in as') }}</legend>
                        <label><input type="radio" name="account_type" value="student" @checked(old('account_type', 'student') === 'student')><span><i class="bi bi-mortarboard-fill" aria-hidden="true"></i> {{ __('Student') }}</span></label>
                        <label><input type="radio" name="account_type" value="teacher" @checked(old('account_type') === 'teacher')><span><i class="bi bi-person-badge-fill" aria-hidden="true"></i> {{ __('Teacher') }}</span></label>
                        <label><input type="radio" name="account_type" value="admin" @checked(old('account_type') === 'admin')><span><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> {{ __('Admin') }}</span></label>
                    </fieldset>

                    <!-- EMAIL INPUT -->
                    <div class="form-group">
                        <label for="login-email" class="form-label">{{ __('Email Address') }}</label>
                        <div class="input-pill-wrapper">
                            <i class="bi bi-envelope field-icon"></i>
                            <input 
                                id="login-email" 
                                type="email" 
                                name="email" 
                                class="input-pill" 
                                placeholder="{{ __('Enter Your Email') }}" 
                                value="{{ old('email') }}" 
                                required
                                autocomplete="email"
                            >
                        </div>
                        @error('email')
                            <span class="input-error-msg"><i class="bi bi-x-circle-fill"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <!-- PASSWORD INPUT -->
                    <div class="form-group">
                        <label for="login-password" class="form-label">{{ __('Password') }}</label>
                        <div class="input-pill-wrapper">
                            <i class="bi bi-lock field-icon"></i>
                            <input 
                                id="login-password" 
                                type="password" 
                                name="password" 
                                class="input-pill" 
                                placeholder="{{ __('Enter Your Password') }}" 
                                required
                            >
                            <button type="button" class="btn-toggle-eye" id="togglePasswordBtn" onclick="togglePasswordVisibility(event)" title="{{ __('Toggle password visibility') }}">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="input-error-msg"><i class="bi bi-x-circle-fill"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <!-- OPTIONS ROW -->
                    <div class="form-options-row">
                        <label class="custom-checkbox-label">
                            <input type="checkbox" name="remember" value="1" checked>
                            <span>{{ __('Remember me') }}</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">{{ __('Forgot Password?') }}</a>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" class="btn-primary-submit">
                        <span>{{ __('Sign In to Dashboard') }}</span>
                        <i class="bi bi-arrow-right fs-17"></i>
                    </button>
                </form>

                <!-- FOOTER -->
                <div class="form-footer-note">
                    <span>{{ __("Don't have an account?") }}</span>
                    <a href="/signup">{{ __('Create an Account') }}</a>
                </div>

            </div>

            <!-- RIGHT COLUMN: HERO PHOTO & GLASS CARDS -->
            <div class="hero-side">
                <div class="hero-inner">
                    <div class="hero-overlay"></div>

                    <!-- FLOATING TOP CARD -->
                    <div class="floating-card-top">
                        <div class="rating-badges-row">
                            <div class="badge-academic-rating">
                                <i class="bi bi-star-fill fs-10"></i>
                                <span>{{ __('Top Academic Rating') }}</span>
                            </div>
                            <span class="badge-tier">{{ __('Tier 1') }}</span>
                        </div>
                        <div class="floating-card-title">{{ __("Dean's Honor Roll System") }}</div>
                        <div class="floating-card-metric">
                            <span>{{ __('Graduation Success') }}</span>
                            <span class="metric-val">98.4%</span>
                        </div>
                    </div>

                    <!-- FLOATING BOTTOM QUOTE CARD -->
                    <div class="floating-card-bottom">
                        <div class="quote-icon-circle">
                            <i class="bi bi-quote"></i>
                        </div>
                        <p class="quote-body-text">
                            {{ __('"Outstanding academic support & faculty collaboration across all faculties."') }}
                        </p>
                        <div class="quote-card-footer">
                            <div class="avatar-group-container">
                                <div class="avatar-overlap">
                                    <div class="avatar-circle av-1">SC</div>
                                    <div class="avatar-circle av-2">ED</div>
                                    <div class="avatar-circle av-3"><i class="bi bi-star-fill"></i></div>
                                </div>
                                <span class="avatar-group-label">{{ __('Student Council 2025') }}</span>
                            </div>
                            <div class="badge-award">{{ __('Excellence Award') }}</div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <!-- SCRIPT FOR INTERACTION -->
    <script>
        function togglePasswordVisibility(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            var passInput = document.getElementById('login-password');
            var eyeIcon = document.getElementById('eyeIcon');
            if (!passInput) return;
            var isPass = passInput.type === 'password';
            passInput.type = isPass ? 'text' : 'password';
            if (eyeIcon) {
                if (isPass) {
                    eyeIcon.className = 'bi bi-eye-slash';
                } else {
                    eyeIcon.className = 'bi bi-eye';
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('togglePasswordBtn');
            if (btn) {
                btn.onclick = togglePasswordVisibility;
            }
        });
    </script>
    <script src="{{ asset('js/app.js') }}?v={{ time() }}" defer></script>
</body>
</html>