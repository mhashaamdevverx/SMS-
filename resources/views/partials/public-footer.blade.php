<!-- =========================================================================
     FOOTER (IDENTICAL DESIGN LANGUAGE AS ADMIN PORTAL)
     ========================================================================= -->
<footer class="landing-footer">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Col 1: Brand & Status -->
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('images/school_logo.png') }}" alt="Excellence School System" style="height: 48px; width: auto; object-fit: contain; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));">
                    <div class="d-flex flex-column">
                        <span class="footer-brand-title" style="letter-spacing: 0.05em; line-height: 1.2;">EXCELLENCE</span>
                        <span style="font-size: 10.5px; font-weight: 700; color: #818CF8; letter-spacing: 0.15em;">SCHOOL SYSTEM</span>
                    </div>
                </div>
                <p style="font-size: 13.5px; color: #94A3B8; max-width: 320px; line-height: 1.6;">
                    {{ __('Dedicated to intellectual curiosity, moral integrity, STEM innovation, and student success from Early Childhood to Higher Secondary.') }}
                </p>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.12); font-size: 12px; color: #38BDF8;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                    {{ __('Session :session Active', ['session' => $academicSession ?? '2026-2027']) }} &bull; {{ __('FBISE Affiliated') }}
                </div>
            </div>

            <!-- Col 2: Navigation Links (Including Why Choose Us & Help Desk shifted from navbar) -->
            <div class="col-6 col-lg-3">
                <h5 class="footer-col-title">{{ __('Navigation') }}</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}"><i class="bi bi-chevron-right me-1 text-primary"></i> {{ __('Home') }}</a></li>
                    <li><a href="{{ route('about') }}"><i class="bi bi-chevron-right me-1 text-primary"></i> {{ __('About Our Legacy') }}</a></li>
                    <li><a href="{{ route('why.choose.us') }}" class="fw-bold" style="color: #818CF8;"><i class="bi bi-star-fill me-1 text-primary"></i> {{ __('Why Choose Us') }}</a></li>
                    <li><a href="{{ route('academics') }}"><i class="bi bi-chevron-right me-1 text-primary"></i> {{ __('Academic Wings') }}</a></li>
                    <li><a href="{{ route('admissions') }}"><i class="bi bi-chevron-right me-1 text-primary"></i> {{ __('Admissions :session', ['session' => $academicSession ?? '2026-2027']) }}</a></li>
                    <li><a href="{{ route('careers') }}"><i class="bi bi-chevron-right me-1 text-primary"></i> {{ __('Career Openings') }}</a></li>
                    <li><a href="{{ route('contact') }}"><i class="bi bi-chevron-right me-1 text-primary"></i> {{ __('Contact Campus') }}</a></li>
                    <li><a href="{{ route('support') }}" class="fw-bold" style="color: #818CF8;"><i class="bi bi-headset me-1 text-primary"></i> {{ __('Help Desk & Support') }}</a></li>
                </ul>
            </div>

            <!-- Col 3: Academic Wings -->
            <div class="col-6 col-lg-2">
                <h5 class="footer-col-title">{{ __('Academic Wings') }}</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('academics') }}#preschool">{{ __('Early Childhood Wing') }}</a></li>
                    <li><a href="{{ route('academics') }}#primary">{{ __('Primary School Wing') }}</a></li>
                    <li><a href="{{ route('academics') }}#middle">{{ __('Middle School Wing') }}</a></li>
                    <li><a href="{{ route('academics') }}#matric">{{ __('Matriculation (FBISE)') }}</a></li>
                    <li><a href="{{ route('academics') }}#cambridge">{{ __('Cambridge O-Levels') }}</a></li>
                    <li><a href="{{ route('academics') }}#stem">{{ __('STEM & AI Robotics Lab') }}</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact & Portal Access -->
            <div class="col-lg-3">
                <h5 class="footer-col-title">{{ __('Campus & Access') }}</h5>
                <p style="font-size: 13.5px; color: #94A3B8; margin-bottom: 8px;">
                    <i class="bi bi-geo-alt-fill text-primary me-2"></i> {{ __('Sector H-8/4, Educational Enclave, Islamabad') }}
                </p>
                <p style="font-size: 13.5px; color: #94A3B8; margin-bottom: 8px;">
                    <i class="bi bi-telephone-fill text-primary me-2"></i> +92 51 848 2900 / 01
                </p>
                <p style="font-size: 13.5px; color: #94A3B8; margin-bottom: 16px;">
                    <i class="bi bi-envelope-fill text-primary me-2"></i> info@excellenceschool.edu.pk
                </p>
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light w-100 fw-bold py-2" class="btn-roots-apply w-100 justify-content-center">
                    <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('Sign In to Academic Portal') }}
                </a>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <div>
                &copy; {{ date('Y') }} Excellence School System. {{ __('All rights reserved.') }}
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="ssl-badge">
                    <i class="bi bi-shield-check"></i> {{ __('256-Bit SSL Encrypted') }}
                </span>
                <span class="ssl-badge" style="color: #A7F3D0; border-color: rgba(16, 185, 129, 0.3);">
                    <i class="bi bi-patch-check"></i> {{ __('FBISE Recognized') }}
                </span>
            </div>
        </div>
    </div>
</footer>
