@extends('layouts.public')

@section('title', 'About Us - Excellence School System')

@section('content')
<!-- Page Banner Header -->
<section class="page-banner text-center">
    <div class="container">
        <span class="page-banner-tag"><i class="bi bi-mortarboard-fill me-1"></i> Our Legacy & Vision</span>
        <h1 class="page-banner-title">Nurturing Intellectual Leaders Since 2012</h1>
        <p class="page-banner-sub">
            Excellence School System combines academic distinction, high-tech STEM labs, and deep-rooted moral values to empower future leaders from Early Childhood to Matriculation.
        </p>
    </div>
</section>

<!-- Vision & Mission Grid -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="pe-lg-4">
                    <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-3" style="font-size: 13px;">CORE PURPOSE</span>
                    <h2 class="fw-bold mb-3" style="color: #0F172A; font-size: 34px;">Empowering Students to Lead in a Fast-Evolving World</h2>
                    <p class="text-muted mb-4" style="font-size: 16px; line-height: 1.8;">
                        Founded with a mission to deliver world-standard education in Pakistan, Excellence School System is accredited with the Federal Board of Intermediate & Secondary Education (FBISE) and Pearson Edexcel. We blend scientific inquiry, coding & robotics, and character development into an integrated curriculum.
                    </p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 border bg-light">
                                <i class="bi bi-compass-fill fs-2 text-primary mb-2 d-block"></i>
                                <h5 class="fw-bold text-dark mb-1">Our Mission</h5>
                                <p class="small text-muted mb-0">To ignite lifelong curiosity, critical thinking, and ethical responsibility in every young learner.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 border bg-light">
                                <i class="bi bi-eye-fill fs-2 text-warning mb-2 d-block"></i>
                                <h5 class="fw-bold text-dark mb-1">Our Vision</h5>
                                <p class="small text-muted mb-0">To stand as the leading innovation-driven school network producing global scholars and ethical citizens.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="{{ asset('images/landing_hero.jpg') }}" alt="Campus Building" class="img-fluid rounded-4 shadow-lg border" style="width: 100%; height: 420px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 m-4 p-3 rounded-3 shadow-lg" style="background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(10px); color: #FFF; max-width: 280px;">
                        <h6 class="fw-bold mb-1 text-warning"><i class="bi bi-building"></i> 2-Acre Modern Campus</h6>
                        <small class="text-light">Sector H-8/4 Educational Enclave, Islamabad</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Milestones Stats (With Smooth Count-Up Number Animations) -->
<section class="py-5 about-stats-section" id="milestonesStatsSection">
    <div class="stats-bg-orb orb-1"></div>
    <div class="stats-bg-orb orb-2"></div>
    <div class="container py-3 position-relative" style="z-index: 5;">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="stat-counter-card p-3">
                    <div class="display-5 fw-bold text-warning mb-1 stat-counter-val" 
                         data-target="{{ $totalStudents ?? 2500 }}" 
                         data-suffix="+" 
                         data-decimals="0">
                        0+
                    </div>
                    <div class="text-uppercase fw-semibold stat-label">Students Enrolled</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-counter-card p-3">
                    <div class="display-5 fw-bold text-warning mb-1 stat-counter-val" 
                         data-target="{{ $totalTeachers ?? 120 }}" 
                         data-suffix="+" 
                         data-decimals="0">
                        0+
                    </div>
                    <div class="text-uppercase fw-semibold stat-label">Certified Faculty</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-counter-card p-3">
                    <div class="display-5 fw-bold text-warning mb-1 stat-counter-val" 
                         data-target="99.8" 
                         data-suffix="%" 
                         data-decimals="1">
                        0.0%
                    </div>
                    <div class="text-uppercase fw-semibold stat-label">Board Distinction</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-counter-card p-3">
                    <div class="display-5 fw-bold text-warning mb-1 stat-counter-val" 
                         data-target="15" 
                         data-suffix="+" 
                         data-decimals="0">
                        0+
                    </div>
                    <div class="text-uppercase fw-semibold stat-label">Years of Excellence</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Campus Pillars -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge px-3 py-2 rounded-pill bg-purple-subtle text-purple fw-bold mb-2" style="background: #FAF5FF; color: #8B5CF6; font-size: 13px;">OUR PILLARS</span>
            <h2 class="fw-bold" style="color: #0F172A; font-size: 34px;">What Defines Excellence</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary d-inline-block mb-3" style="width: 52px; height: 52px; text-align: center;">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">STEM & Digital Innovation</h5>
                    <p class="text-muted small mb-0">From grade 1, students participate in hands-on robotics, coding in Python/Scratch, and practical scientific experiments.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="rounded-3 p-3 bg-success-subtle text-success d-inline-block mb-3" style="width: 52px; height: 52px; text-align: center;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Moral & Ethical Leadership</h5>
                    <p class="text-muted small mb-0">Character building, empathy, Islamic values, and civic duty are weaved throughout daily school life.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning d-inline-block mb-3" style="width: 52px; height: 52px; text-align: center;">
                        <i class="bi bi-trophy-fill fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Academic Preeminence</h5>
                    <p class="text-muted small mb-0">Our students consistently secure top positions in FBISE board exams and national science Olympiads.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('admissions') }}" class="btn btn-primary px-4 py-3 fw-bold rounded-3 shadow" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                Apply for Admission {{ $academicSession ?? '2026-2027' }} <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .about-stats-section {
        background: linear-gradient(135deg, #1E1B4B 0%, #312E81 100%);
        color: #FFFFFF;
        position: relative;
        overflow: hidden;
    }

    .about-stats-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 1px);
        background-size: 28px 28px;
        opacity: 0.35;
        pointer-events: none;
    }

    .stats-bg-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        opacity: 0.3;
    }

    .stats-bg-orb.orb-1 {
        width: 300px;
        height: 300px;
        background: #4F46E5;
        top: -60px;
        left: 5%;
    }

    .stats-bg-orb.orb-2 {
        width: 260px;
        height: 260px;
        background: #F59E0B;
        bottom: -50px;
        right: 8%;
    }

    .stat-counter-card {
        border-radius: 18px;
        transition: transform 0.3s ease, background 0.3s ease;
        position: relative;
    }

    .stat-counter-card:hover {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, 0.05);
    }

    .stat-counter-val {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-feature-settings: "tnum";
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        text-shadow: 0 4px 20px rgba(245, 158, 11, 0.25);
        color: #FBBF24 !important;
        transition: transform 0.2s ease;
    }

    .stat-counter-val.is-counting {
        will-change: contents;
    }

    .stat-label {
        letter-spacing: 0.06em;
        font-size: 13px;
        color: #C7D2FE;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const section = document.getElementById('milestonesStatsSection');
    const counters = document.querySelectorAll('.stat-counter-val');
    if (!section || !counters.length) return;

    let hasAnimated = false;

    // EaseOutExpo: Fast start with buttery-smooth exponential deceleration
    function easeOutExpo(t) {
        return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
    }

    function animateCounters() {
        if (hasAnimated) return;
        hasAnimated = true;

        const duration = 2200; // 2.2 seconds animation
        const startTime = performance.now();

        counters.forEach(counter => {
            counter.classList.add('is-counting');
        });

        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easedProgress = easeOutExpo(progress);

            counters.forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-target')) || 0;
                const suffix = counter.getAttribute('data-suffix') || '';
                const decimals = parseInt(counter.getAttribute('data-decimals'), 10) || 0;

                const currentVal = easedProgress * target;

                if (decimals > 0) {
                    counter.textContent = currentVal.toFixed(decimals) + suffix;
                } else {
                    counter.textContent = Math.floor(currentVal).toLocaleString('en-US') + suffix;
                }
            });

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Ensure exact final formatted value is displayed
                counters.forEach(counter => {
                    const target = parseFloat(counter.getAttribute('data-target')) || 0;
                    const suffix = counter.getAttribute('data-suffix') || '';
                    const decimals = parseInt(counter.getAttribute('data-decimals'), 10) || 0;

                    if (decimals > 0) {
                        counter.textContent = target.toFixed(decimals) + suffix;
                    } else {
                        counter.textContent = target.toLocaleString('en-US') + suffix;
                    }
                    counter.classList.remove('is-counting');
                });
            }
        }

        requestAnimationFrame(step);
    }

    // Trigger when section comes into view
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.25,
            rootMargin: '0px 0px -40px 0px'
        });
        observer.observe(section);
    } else {
        animateCounters();
    }
});
</script>
@endpush
