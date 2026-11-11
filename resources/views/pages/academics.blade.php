@extends('layouts.public')

@section('title', __('Academic Wings & Curriculum') . ' - Excellence School System')

@section('content')
<!-- FULL-WIDTH 3-SECOND HERO IMAGE SLIDER -->
<section class="position-relative overflow-hidden w-100 p-0 m-0">
    <div id="academicCampusCarousel" class="carousel slide carousel-fade position-relative" data-bs-ride="carousel" data-bs-interval="3000" data-bs-pause="false">
        <!-- Carousel Indicators -->
        <div class="carousel-indicators mb-4">
            <button type="button" data-bs-target="#academicCampusCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="{{ __('Slide 1') }}"></button>
            <button type="button" data-bs-target="#academicCampusCarousel" data-bs-slide-to="1" aria-label="{{ __('Slide 2') }}"></button>
            <button type="button" data-bs-target="#academicCampusCarousel" data-bs-slide-to="2" aria-label="{{ __('Slide 3') }}"></button>
            <button type="button" data-bs-target="#academicCampusCarousel" data-bs-slide-to="3" aria-label="{{ __('Slide 4') }}"></button>
        </div>

        <!-- Carousel Items -->
        <div class="carousel-inner">
            <!-- Slide 1: Main Campus & Collegiate Architecture -->
            <div class="carousel-item active" data-bs-interval="3000">
                <div class="carousel-img-wrapper position-relative">
                    <img src="{{ asset('images/landing_hero.jpg') }}" class="d-block w-100 carousel-slide-img" alt="{{ __('Main Academic Campus') }}">
                    <div class="carousel-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end p-4 p-md-5">
                        <div class="container pb-2">
                            <div class="glass-caption p-4 rounded-4 text-white" style="max-width: 680px;">
                                <span class="badge px-3 py-1 rounded-pill bg-primary fw-bold mb-2 shadow-sm" style="font-size: 11px; letter-spacing: 0.05em;">{{ __('ACADEMIC EXCELLENCE • SESSION :session', ['session' => '2026-2027']) }}</span>
                                <h2 class="fw-bold mb-2 text-white" style="font-size: clamp(1.4rem, 2.6vw, 2.1rem); letter-spacing: -0.01em;">{{ __('World-Class Academic Programs & Campus') }}</h2>
                                <p class="mb-0 text-white-50 small" style="line-height: 1.6;">{{ __('From sensory discovery in early childhood to FBISE board mastery and STEM robotics, our programs inspire critical inquiry and leadership.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: STEM & Robotics Innovation Lab -->
            <div class="carousel-item" data-bs-interval="3000">
                <div class="carousel-img-wrapper position-relative">
                    <img src="{{ asset('images/academic_stem_lab.jpg') }}" class="d-block w-100 carousel-slide-img" alt="{{ __('STEM & Robotics Innovation Lab') }}">
                    <div class="carousel-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end p-4 p-md-5">
                        <div class="container pb-2">
                            <div class="glass-caption p-4 rounded-4 text-white" style="max-width: 680px;">
                                <span class="badge px-3 py-1 rounded-pill bg-info text-dark fw-bold mb-2 shadow-sm" style="font-size: 11px; letter-spacing: 0.05em;">{{ __('ROBOTICS & STEM LAB') }}</span>
                                <h2 class="fw-bold mb-2 text-white" style="font-size: clamp(1.4rem, 2.6vw, 2.1rem); letter-spacing: -0.01em;">{{ __('Hands-On Robotics & Applied Sciences') }}</h2>
                                <p class="mb-0 text-white-50 small" style="line-height: 1.6;">{{ __("3D printing, Arduino automation, and Python programming workshops empowering tomorrow's tech innovators.") }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Two-Story Central Academic Library -->
            <div class="carousel-item" data-bs-interval="3000">
                <div class="carousel-img-wrapper position-relative">
                    <img src="{{ asset('images/academic_library.jpg') }}" class="d-block w-100 carousel-slide-img" alt="{{ __('Central Academic Library') }}">
                    <div class="carousel-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end p-4 p-md-5">
                        <div class="container pb-2">
                            <div class="glass-caption p-4 rounded-4 text-white" style="max-width: 680px;">
                                <span class="badge px-3 py-1 rounded-pill bg-warning text-dark fw-bold mb-2 shadow-sm" style="font-size: 11px; letter-spacing: 0.05em;">{{ __('CENTRAL LIBRARY') }}</span>
                                <h2 class="fw-bold mb-2 text-white" style="font-size: clamp(1.4rem, 2.6vw, 2.1rem); letter-spacing: -0.01em;">{{ __('Over 15,000 Academic Titles & Quiet Pods') }}</h2>
                                <p class="mb-0 text-white-50 small" style="line-height: 1.6;">{{ __('Two-story naturally lit reading hall with digital JSTOR access, international journals, and collaborative study desks.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4: Science Exploration Laboratories -->
            <div class="carousel-item" data-bs-interval="3000">
                <div class="carousel-img-wrapper position-relative">
                    <img src="{{ asset('images/stem_lab.jpg') }}" class="d-block w-100 carousel-slide-img" alt="{{ __('Advanced Physics and Chemistry Labs') }}">
                    <div class="carousel-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end p-4 p-md-5">
                        <div class="container pb-2">
                            <div class="glass-caption p-4 rounded-4 text-white" style="max-width: 680px;">
                                <span class="badge px-3 py-1 rounded-pill bg-success fw-bold mb-2 shadow-sm" style="font-size: 11px; letter-spacing: 0.05em;">{{ __('SCIENTIFIC RESEARCH') }}</span>
                                <h2 class="fw-bold mb-2 text-white" style="font-size: clamp(1.4rem, 2.6vw, 2.1rem); letter-spacing: -0.01em;">{{ __('Rigorous Science & Analytical Equipment') }}</h2>
                                <p class="mb-0 text-white-50 small" style="line-height: 1.6;">{{ __('Complete FBISE and Cambridge O/A-level experimental apparatus with fume hoods, optics kits, and digital sensor probes.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#academicCampusCarousel" data-bs-slide="prev">
            <span class="carousel-nav-btn"><i class="bi bi-chevron-left"></i></span>
            <span class="visually-hidden">{{ __('Previous') }}</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#academicCampusCarousel" data-bs-slide="next">
            <span class="carousel-nav-btn"><i class="bi bi-chevron-right"></i></span>
            <span class="visually-hidden">{{ __('Next') }}</span>
        </button>
    </div>
</section>

<!-- ============================================================== -->
<!-- ACADEMIC WINGS SECTIONS                                        -->
<!-- ============================================================== -->
<section class="py-5 bg-white">
    <div class="container py-3">

        <!-- WING 1: EARLY CHILDHOOD DEVELOPMENT WING (WITH EQUAL HEIGHT BOXES) -->
        <div class="row g-5 align-items-center mb-5 pb-5 border-bottom wing-section" id="preschool">
            <div class="col-lg-6">
                <span class="badge px-3 py-2 rounded-pill bg-warning-subtle text-warning-emphasis fw-bold mb-3 shadow-2xs" style="font-size: 12.5px;">
                    <i class="bi bi-stars me-1 text-warning"></i> {{ __('AGES 3 - 5 YEARS') }}
                </span>
                <h2 class="fw-bold mb-3 text-dark" style="font-size: clamp(1.8rem, 3vw, 2.2rem); letter-spacing: -0.02em;">
                    {{ __('Early Childhood Development Wing') }}
                </h2>
                <p class="text-muted mb-4" style="font-size: 15.5px; line-height: 1.8;">
                    {{ __('Our kindergarten and Montessori classrooms are rich sensory environments designed around curiosity, play, and foundational motor development.') }}
                </p>

                <!-- EQUAL HEIGHT SUB-CARDS (FIXED EQUAL HEIGHT) -->
                <div class="row g-3">
                    <div class="col-sm-6 d-flex">
                        <div class="p-3 rounded-4 bg-light border w-100 h-100 d-flex flex-column justify-content-between equal-box hover-lift">
                            <div>
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-music-note-beamed text-primary me-2"></i> {{ __('Phonics & Rhymes') }}
                                </h6>
                                <p class="small text-muted mb-0" style="line-height: 1.6;">
                                    {{ __('Early literacy, expressive speech, and joyful reading habit through multisensory phonics.') }}
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-top">
                                <span class="badge bg-white text-primary border small fw-bold">{{ __('Daily Sessions') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 d-flex">
                        <div class="p-3 rounded-4 bg-light border w-100 h-100 d-flex flex-column justify-content-between equal-box hover-lift">
                            <div>
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-puzzle-fill text-success me-2"></i> {{ __('Motor Skills') }}
                                </h6>
                                <p class="small text-muted mb-0" style="line-height: 1.6;">
                                    {{ __('Fine motor coordination, Montessori sensory kits, and creative arts exploration.') }}
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-top">
                                <span class="badge bg-white text-success border small fw-bold">{{ __('Sensory Kits') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="img-zoom-card rounded-4 shadow-sm border overflow-hidden">
                    <img src="{{ asset('images/landing_hero.jpg') }}" alt="{{ __('Early Childhood Wing Campus') }}" class="img-fluid w-100 wing-feature-img">
                </div>
            </div>
        </div>

        <!-- WING 2: PRIMARY SCHOOL INNOVATION WING -->
        <div class="row g-5 align-items-center mb-5 pb-5 border-bottom flex-lg-row-reverse wing-section" id="primary">
            <div class="col-lg-6">
                <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-3 shadow-2xs" style="font-size: 12.5px;">
                    <i class="bi bi-mortarboard-fill me-1 text-primary"></i> {{ __('GRADES 1 - 5') }}
                </span>
                <h2 class="fw-bold mb-3 text-dark" style="font-size: clamp(1.8rem, 3vw, 2.2rem); letter-spacing: -0.02em;">
                    {{ __('Primary School Innovation Wing') }}
                </h2>
                <p class="text-muted mb-4" style="font-size: 15.5px; line-height: 1.8;">
                    {{ __('Bridging foundational literacy into scientific inquiry, conceptual mathematics, bilingual communication (English & Urdu), and introductory coding.') }}
                </p>
                <div class="d-flex flex-column gap-2 mb-4">
                    <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                        <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                        <span>{{ __('Experiential STEM activities & young coder challenges') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                        <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                        <span>{{ __('Mental math exercises, reading clubs, and spelling bees') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-dark small fw-semibold">
                        <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                        <span>{{ __('Character building, athletics day, and bilingual debate society') }}</span>
                    </div>
                </div>
                <a href="{{ route('admissions') }}" class="btn btn-outline-primary btn-sm px-4 py-2 rounded-pill fw-bold">
                    {{ __('Inquire for Primary Wing') }} <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="col-lg-6">
                <div class="img-zoom-card rounded-4 shadow-sm border overflow-hidden">
                    <img src="{{ asset('images/stem_lab.jpg') }}" alt="{{ __('Primary School STEM Lab') }}" class="img-fluid w-100 wing-feature-img">
                </div>
            </div>
        </div>

        <!-- WING 3: SENIOR SCHOOL & BOARD PREP WING (WITH BRIGHT CRISP WHITE TEXT) -->
        <div class="row g-5 align-items-center wing-section" id="matric">
            <div class="col-lg-6">
                <span class="badge px-3 py-2 rounded-pill bg-success-subtle text-success fw-bold mb-3 shadow-2xs" style="font-size: 12.5px;">
                    <i class="bi bi-award-fill me-1 text-success"></i> {{ __('GRADES 6 - 10 / MATRICULATION & FBISE') }}
                </span>
                <h2 class="fw-bold mb-3 text-dark" style="font-size: clamp(1.8rem, 3vw, 2.2rem); letter-spacing: -0.02em;">
                    {{ __('Senior School & Board Prep Wing') }}
                </h2>
                <p class="text-muted mb-4" style="font-size: 15.5px; line-height: 1.8;">
                    {{ __('Accredited with the Federal Board of Intermediate and Secondary Education (FBISE), our senior wing equips scholars with deep subject mastery in Physics, Chemistry, Biology, Computer Science, and Mathematics.') }}
                </p>

                <div class="p-4 rounded-4 bg-light border mb-4 shadow-sm" style="border-left: 4px solid #F59E0B !important;">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-trophy-fill text-warning me-2"></i> {{ __('99.8% Distinction Track Record') }}
                    </h6>
                    <p class="small text-muted mb-0" style="line-height: 1.6;">
                        {{ __('Comprehensive test session series, monthly diagnostic reporting, and individual mentorship ensuring top FBISE positions every year.') }}
                    </p>
                </div>

                <a href="{{ route('admissions') }}" class="btn btn-primary px-4 py-3 fw-bold rounded-3 shadow hover-lift" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                    {{ __('Apply for Senior Wing') }} <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>

            <!-- RIGHT DARK FACILITY CARD WITH 100% BRIGHT WHITE VISIBLE TEXT -->
            <div class="col-lg-6">
                <div class="p-4 p-md-5 rounded-4 shadow-xl position-relative overflow-hidden" style="background: #0F172A !important; border: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.15);">
                            <i class="bi bi-cpu-fill fs-5"></i>
                        </div>
                        <h5 class="fw-bold text-warning mb-0" style="font-size: 18px; letter-spacing: -0.01em;">
                            {{ __('Advanced Laboratory Facilities:') }}
                        </h5>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <!-- Lab Item 1 (Bright Crisp White Heading & Text) -->
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 lab-item-box transition-all" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-info" style="width: 44px; height: 44px; background: rgba(14, 165, 233, 0.15); flex-shrink: 0;">
                                <i class="bi bi-laptop fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="color: #FFFFFF !important; font-size: 15px;">
                                    {{ __('AI & Computer Systems Lab') }}
                                </h6>
                                <small class="d-block text-white-50" style="color: rgba(255, 255, 255, 0.82) !important; font-size: 12.5px; line-height: 1.4;">
                                    {{ __('High-speed gigabit workstations with Python, C++, and Scratch') }}
                                </small>
                            </div>
                        </div>

                        <!-- Lab Item 2 (Bright Crisp White Heading & Text) -->
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 lab-item-box transition-all" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.15); flex-shrink: 0;">
                                <i class="bi bi-radioactive fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="color: #FFFFFF !important; font-size: 15px;">
                                    {{ __('Physics & Chemistry Labs') }}
                                </h6>
                                <small class="d-block text-white-50" style="color: rgba(255, 255, 255, 0.82) !important; font-size: 12.5px; line-height: 1.4;">
                                    {{ __('Full safety apparatus and high-precision analytical equipment') }}
                                </small>
                            </div>
                        </div>

                        <!-- Lab Item 3 (Bright Crisp White Heading & Text) -->
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 lab-item-box transition-all" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.15); flex-shrink: 0;">
                                <i class="bi bi-book fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="color: #FFFFFF !important; font-size: 15px;">
                                    {{ __('Digital Central Library') }}
                                </h6>
                                <small class="d-block text-white-50" style="color: rgba(255, 255, 255, 0.82) !important; font-size: 12.5px; line-height: 1.4;">
                                    {{ __('Over 15,000 academic titles, research journals, and quiet pods') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('styles')
<style>
/* Full-Width Carousel Styling */
.carousel-slide-img {
    height: 480px;
    object-fit: cover;
    filter: brightness(0.9);
    transition: transform 6s ease;
}
.carousel-item.active .carousel-slide-img {
    transform: scale(1.03);
}
.carousel-overlay {
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.1) 0%, rgba(15, 23, 42, 0.8) 100%);
}
.glass-caption {
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    animation: fadeInUp 0.8s ease forwards;
}

/* Carousel Nav Buttons */
.carousel-nav-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.25s ease;
}
.carousel-control-prev:hover .carousel-nav-btn,
.carousel-control-next:hover .carousel-nav-btn {
    background: #4F46E5;
    color: #FFFFFF;
    transform: scale(1.1);
}

/* Equal Box Styles */
.equal-box {
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.equal-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border-color: #4F46E5 !important;
}

/* Image Zoom Card */
.img-zoom-card {
    position: relative;
    overflow: hidden;
}
.wing-feature-img {
    height: 360px;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.img-zoom-card:hover .wing-feature-img {
    transform: scale(1.05);
}

/* Laboratory Dark Box Hover */
.lab-item-box {
    transition: all 0.25s ease;
}
.lab-item-box:hover {
    background: rgba(255, 255, 255, 0.14) !important;
    transform: translateX(6px);
}

/* Keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hover-lift {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(79, 70, 229, 0.25) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const carouselEl = document.getElementById('academicCampusCarousel');
    if (carouselEl) {
        // Initialize bootstrap carousel with explicit 3000ms (3 seconds) cycle
        const carousel = new bootstrap.Carousel(carouselEl, {
            interval: 3000,
            ride: 'carousel',
            pause: false,
            wrap: true
        });
        carousel.cycle();
    }
});
</script>
@endpush
