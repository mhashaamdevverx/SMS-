<!-- =========================================================================
     TOP STICKY NAVBAR (GLASSMORPHISM WITH ADMIN / ROYAL PURPLE THEME)
     ========================================================================= -->
<header class="landing-header" id="landingHeader">
    <div class="roots-header-container">
        <nav class="roots-navbar navbar navbar-expand-lg p-0">
            <!-- Brand: Official School Logo PNG | EXCELLENCE SCHOOL SYSTEM -->
            <a class="roots-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/school_logo.png') }}" alt="Excellence School System" class="roots-brand-logo-img">
                <div class="roots-brand-divider"></div>
                <div class="roots-brand-text">
                    <span class="roots-brand-name">EXCELLENCE</span>
                    <span class="roots-brand-sub">SCHOOL SYSTEM</span>
                </div>
            </a>

            <!-- Mobile Menu Button -->
            <button class="navbar-toggler border-0 shadow-none ms-auto d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRootsContent" aria-controls="navbarRootsContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-1 text-dark"></i>
            </button>

            <!-- Navigation Links (Why Choose Us & Help Desk moved to Footer as requested) -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarRootsContent">
                <div class="roots-nav-menu my-3 my-lg-0">
                    <a class="roots-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    <a class="roots-nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a>
                    <a class="roots-nav-link {{ request()->routeIs('admissions') ? 'active' : '' }}" href="{{ route('admissions') }}">Admissions</a>
                    <a class="roots-nav-link {{ request()->routeIs('academics') ? 'active' : '' }}" href="{{ route('academics') }}">Academics</a>
                    <a class="roots-nav-link {{ request()->routeIs('careers') ? 'active' : '' }}" href="{{ route('careers') }}">Careers</a>
                    <a class="roots-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                </div>

                <!-- ACTION BUTTON: PORTAL LOGIN ONLY -->
                <div class="roots-nav-actions">
                    <a href="{{ route('login') }}" class="btn-roots-apply" id="portalLoginNavBtn" title="Access Student, Teacher & Admin Portals">
                        <span class="btn-icon-lock"><i class="bi bi-shield-lock-fill"></i></span>
                        <span>Portal Login</span>
                        <i class="bi bi-arrow-right-short fs-5 btn-arrow"></i>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>
