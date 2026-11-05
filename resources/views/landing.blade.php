<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('languages.supported.'.app()->getLocale().'.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Excellence School System - Nurturing Young Minds Through Innovation & Play</title>

    <link rel="icon" type="image/png" href="{{ asset('images/school_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/school_logo.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scoped Landing Page Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

    @include('partials.locale-head')
</head>
<body>

    <!-- =========================================================================
         1. NAVBAR (STICKY, BRAND, MENU & PORTAL LOGIN)
         ========================================================================= -->
    @include('partials.public-header')

    <!-- =========================================================================
         2. HERO SECTION (FULL WIDTH EDGE-TO-EDGE SLIDER DIRECTLY BELOW NAVBAR)
         ========================================================================= -->
    <section class="hero-section" id="home">
        <!-- Background Image Crossfade Slider (5s interval, 1s fade) -->
        <div class="hero-bg-slider" id="heroBgSlider">
            <div class="hero-bg-slide active">
                <img src="{{ asset('images/stem_lab.jpg') }}" alt="STEM Robotics Lab" onerror="this.onerror=null; this.src='{{ asset('images/landing_hero.jpg') }}';">
            </div>
            <div class="hero-bg-slide">
                <img src="{{ asset('images/landing_hero.jpg') }}" alt="Smart Classroom" onerror="this.onerror=null; this.src='{{ asset('images/stem_lab.jpg') }}';">
            </div>
            <div class="hero-bg-slide">
                <img src="{{ asset('upload/school_campus.jpg') }}" alt="Excellence Campus" onerror="this.onerror=null; this.src='{{ asset('images/landing_hero.jpg') }}';">
            </div>
            <div class="hero-bg-slide">
                <img src="{{ asset('upload/signup_hero.jpg') }}" alt="Academic Collaborative Library" onerror="this.onerror=null; this.src='{{ asset('images/stem_lab.jpg') }}';">
            </div>
        </div>

        <!-- Dark Indigo Frosted Gradient Overlay -->
        <div class="hero-overlay"></div>

        <!-- Hero Content Container: Left Aligned (~50-60% Screen on Desktop) -->
        <div class="container hero-container position-relative">
            <div class="row justify-content-start align-items-center">
                <div class="col-lg-7 col-xl-6 hero-text-col">
                    
                    <!-- Clean, Bold Heading with Amber Accent -->
                    <h1 class="hero-heading">
                        Nurturing Young Minds Through
                        <span class="hero-text-accent">Play,</span>
                        <span class="hero-text-accent">Wonder &amp; 3D</span>
                        Interactive Learning.
                    </h1>

                    <!-- Lead Description -->
                    <p class="hero-lead">
                        Where early discovery seamlessly blossoms into academic mastery. Blending Montessori methods, cutting-edge STEM robotics labs, and comprehensive character building from Kindergarten to Matriculation.
                    </p>

                    <!-- CTA Action Buttons -->
                    <div class="hero-cta-group">
                        <a href="{{ route('admissions') }}" class="btn-cta-orange">
                            <span>Enroll Your Child</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="#tourSection" class="btn-outline-white">
                            <i class="bi bi-calendar2-event me-1"></i>
                            <span>Book Campus Tour</span>
                        </a>
                    </div>

                    <!-- Rating & Credibility Row -->
                    <div class="hero-rating-strip">
                        <div class="d-flex align-items-center gap-1 text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="fw-bold text-white ms-1">4.9 / 5</span>
                        </div>
                        <span class="text-white-50">&bull;</span>
                        <span class="text-light opacity-90 small">(1,200+ Happy Parent Reviews)</span>
                        <span class="hero-affiliation-badge">
                            <i class="bi bi-patch-check-fill me-1"></i> FBISE &amp; Cambridge
                        </span>
                    </div>

                </div>
            </div>

            <!-- Subtle Slider Dots Indicator Centered Below -->
            <div class="hero-slider-dots" id="heroSliderDots">
                <button type="button" class="hero-dot active" data-slide="0" aria-label="Slide 1"></button>
                <button type="button" class="hero-dot" data-slide="1" aria-label="Slide 2"></button>
                <button type="button" class="hero-dot" data-slide="2" aria-label="Slide 3"></button>
                <button type="button" class="hero-dot" data-slide="3" aria-label="Slide 4"></button>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         3. FOUR PILLARS SECTION (LIGHT BACKGROUND, 4 EQUAL HEIGHT CARDS)
         ========================================================================= -->
    <section class="pillars-section section-spacing">
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
                        <div class="pillar-icon-box pillar-icon-gold">
                            <i class="bi bi-puzzle-fill"></i>
                        </div>
                        <h3 class="pillar-title">Montessori &amp; STEM</h3>
                        <p class="pillar-desc">
                            Hands-on sensory exploration, coding logic, robotics sets, and practical observation from Early Years.
                        </p>
                        <a href="{{ route('academics') }}#preschool" class="pillar-link">
                            <span>Learn More</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box pillar-icon-burgundy">
                            <i class="bi bi-person-heart"></i>
                        </div>
                        <h3 class="pillar-title">Nurturing Educators</h3>
                        <p class="pillar-desc">
                            Compassionate, certified teachers delivering individualized attention with a low 15:1 student-to-teacher ratio.
                        </p>
                        <a href="{{ route('about') }}" class="pillar-link">
                            <span>Meet Culture</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box pillar-icon-maroon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="pillar-title">Safe &amp; Active Sports</h3>
                        <p class="pillar-desc">
                            2-acre safe green campus, modern sports courts, karate, gymnastics, athletics, and physical wellness.
                        </p>
                        <a href="{{ route('why.choose.us') }}" class="pillar-link">
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
                        <h3 class="pillar-title">Arts, Music &amp; Ethics</h3>
                        <p class="pillar-desc">
                            Creative studio work, public speaking, Islamic moral ethics, and performing arts fostering well-rounded character.
                        </p>
                        <a href="{{ route('academics') }}" class="pillar-link">
                            <span>Discover Arts</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         4. PROGRAMS SECTION (EARLY YEARS TO MATRICULATION, LAVENDER BACKGROUND)
         ========================================================================= -->
    <section class="programs-section section-spacing">
        <div class="container">
            <!-- Header Title -->
            <div class="programs-header-box mb-4">
                <span class="section-tag-pill">EARLY YEARS TO MATRICULATION</span>
                <h2 class="section-heading-bold mb-2">Programs Tailored to Every Age Stage</h2>
                <p class="section-subheading-clean text-start ms-0 mb-0">Select an academic tier tailored to your child's age, growth stage, and learning ambitions.</p>
            </div>

            <!-- Cohesive Controls Toolbar: Unified Segmented Tabs & Action Button -->
            <div class="program-toolbar-row mb-5">
                <div class="program-segmented-bar" id="programTabsGroup">
                    <button type="button" class="program-tab-pill active" data-filter="all">All Wings</button>
                    <button type="button" class="program-tab-pill" data-filter="preschool">Pre-School</button>
                    <button type="button" class="program-tab-pill" data-filter="primary">Primary Wing</button>
                    <button type="button" class="program-tab-pill" data-filter="senior">Senior FBISE</button>
                </div>

                <a href="{{ route('academics') }}" class="btn-view-all-wings" title="View Full Academic Wings Syllabus">
                    <span>View All Wings</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="row g-4 justify-content-center" id="programCardsGrid">
                <!-- Program 1: Pre-School -->
                <div class="col-md-6 col-lg-4 program-item" data-category="preschool">
                    <div class="program-card">
                        <div class="program-img-box">
                            <span class="program-age-pill"><i class="bi bi-balloon-heart text-danger me-1"></i> Ages 3 - 5 Years</span>
                            <img src="{{ asset('images/landing_hero.jpg') }}" alt="Little Sprouts Discovery">
                        </div>
                        <div class="program-body">
                            <h3 class="program-title">Little Sprouts Discovery</h3>
                            <p class="program-desc">Nurturing curiosity and fine motor skills through playful sensory activities and phonics.</p>
                            <ul class="program-bullets">
                                <li><i class="bi bi-check2-circle text-primary"></i> Multi-sensory play &amp; alphabet phonics</li>
                                <li><i class="bi bi-check2-circle text-primary"></i> Social sharing, empathy, and manners</li>
                                <li><i class="bi bi-check2-circle text-primary"></i> Daily outdoor supervised sports play</li>
                            </ul>
                            <div class="program-footer-bar">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Wing Enrollment</small>
                                    <strong class="text-dark" style="font-size: 13.5px;">Spring 2026 Open</strong>
                                </div>
                                <a href="{{ route('admissions') }}" class="btn-program-apply">
                                    Apply Now &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program 2: Primary Wing -->
                <div class="col-md-6 col-lg-4 program-item" data-category="primary">
                    <div class="program-card">
                        <div class="program-img-box">
                            <span class="program-age-pill"><i class="bi bi-cpu text-primary me-1"></i> Grades 1 - 5</span>
                            <img src="{{ asset('images/stem_lab.jpg') }}" alt="Junior Explorers STEM Hub">
                        </div>
                        <div class="program-body">
                            <h3 class="program-title">Junior Explorers STEM Hub</h3>
                            <p class="program-desc">Building strong foundational literacy, conceptual math, science projects, and coding.</p>
                            <ul class="program-bullets">
                                <li><i class="bi bi-check2-circle text-success"></i> Hands-on weekly science experiments</li>
                                <li><i class="bi bi-check2-circle text-success"></i> Scratch coding &amp; robotics logic</li>
                                <li><i class="bi bi-check2-circle text-success"></i> Bilingual English &amp; Urdu eloquence</li>
                            </ul>
                            <div class="program-footer-bar">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Wing Enrollment</small>
                                    <strong class="text-dark" style="font-size: 13.5px;">Seats Available</strong>
                                </div>
                                <a href="{{ route('admissions') }}" class="btn-program-apply">
                                    Apply Now &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program 3: Senior Wing -->
                <div class="col-md-6 col-lg-4 program-item" data-category="senior">
                    <div class="program-card">
                        <div class="program-img-box">
                            <span class="program-age-pill"><i class="bi bi-award text-warning me-1"></i> Grades 6 - 10 / Matric</span>
                            <img src="{{ asset('upload/school_campus.jpg') }}" alt="Master Innovators Senior Wing" onerror="this.onerror=null; this.src='{{ asset('images/landing_hero.jpg') }}';">
                        </div>
                        <div class="program-body">
                            <h3 class="program-title">Master Innovators Senior Wing</h3>
                            <p class="program-desc">Rigorous FBISE board syllabus mastery with structured monthly test diagnostics.</p>
                            <ul class="program-bullets">
                                <li><i class="bi bi-check2-circle text-warning"></i> FBISE Board Distinction track record</li>
                                <li><i class="bi bi-check2-circle text-warning"></i> Fully equipped physics, chem &amp; bio labs</li>
                                <li><i class="bi bi-check2-circle text-warning"></i> Career counseling &amp; Olympiad training</li>
                            </ul>
                            <div class="program-footer-bar">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Wing Enrollment</small>
                                    <strong class="text-dark" style="font-size: 13.5px;">Merit Admissions</strong>
                                </div>
                                <a href="{{ route('admissions') }}" class="btn-program-apply">
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
         5. "BEST MENTORS" DARK SECTION (INDIGO GRADIENT, 2 COLUMNS)
         ========================================================================= -->
    <section class="mentor-banner-section section-spacing">
        <div class="container">
            <div class="row align-items-center g-4 g-xl-5">
                <!-- Left: Content & Features -->
                <div class="col-lg-6">
                    <span class="section-tag-pill-dark">TOP-TIER ACCREDITATION</span>
                    <h2 class="display-6 fw-bold mb-3 text-white">We Are The Best Mentors In Modern Holistic Education</h2>
                    <p class="lead opacity-90 mb-4">
                        Dedicated to creating an inspiring environment where every learner thrives academically, socially, and creatively through compassionate guidance and world-class digital tools.
                    </p>

                    <!-- 2x2 Feature Grid -->
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

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admissions') }}" class="btn-cta-orange">
                            <span>Schedule Campus Visit</span>
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('about') }}" class="btn-outline-white">
                            About Our Legacy
                        </a>
                    </div>
                </div>

                <!-- Right: Image with Rounded Frame and Badges -->
                <div class="col-lg-6">
                    <div class="mentor-showcase-box">
                        <img src="{{ asset('images/stem_lab.jpg') }}" alt="Innovation Lab Showcase" class="mentor-showcase-img">
                        <div class="mt-3 d-flex justify-content-between align-items-center text-white px-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-warning" style="font-size: 14px;">
                                    <i class="bi bi-shield-lock-fill me-1"></i> ISO 9001 &amp; FBISE Certified
                                </h6>
                                <small class="text-light opacity-75" style="font-size: 12px;">Recognized nationwide for academic standard</small>
                            </div>
                            <span class="badge-sector-rank">Ranked #1 in Sector</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         6. CAMPUS LIFE (GALLERY) SECTION (WHITE BACKGROUND, 4 CARDS)
         ========================================================================= -->
    <section class="campus-gallery-section section-spacing">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-tag-pill">CAMPUS LIFE &amp; CULTURE</span>
                <h2 class="section-heading-bold">Life Inside Excellence Campus</h2>
                <p class="section-subheading-clean">
                    From robotic hackathons to sports galas and art exhibitions, explore everyday snapshots of our vibrant community.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-6 col-lg-3">
                    <div class="gallery-photo-card">
                        <img src="{{ asset('images/stem_lab.jpg') }}" alt="STEM Robotics Lab">
                        <div class="gallery-caption">STEM &amp; 3D Prototyping</div>
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
                        <div class="gallery-caption">Sports Gala &amp; Athletics</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="gallery-photo-card">
                        <img src="{{ asset('upload/signup_hero.jpg') }}" alt="Computer & Coding Hub" onerror="this.onerror=null; this.src='{{ asset('images/stem_lab.jpg') }}';">
                        <div class="gallery-caption">AI Workstations &amp; Coding</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         7. TESTIMONIALS SECTION (LAVENDER BACKGROUND, 3 CARDS)
         ========================================================================= -->
    <section class="testimonials-section section-spacing">
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
                            <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="review-quote-text">
                                "My daughter joined the Early Years wing two years ago. The personalized care and interactive STEM activities boosted her confidence tremendously!"
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm avatar-initials-burgundy" style="width: 44px; height: 44px;">
                                FA
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Farah Ahmed</h4>
                                <small class="text-muted" style="font-size: 12.5px;">Mother of Ayla (Grade 1)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="col-md-4">
                    <div class="review-card">
                        <div>
                            <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="review-quote-text">
                                "The online academic portal is fantastic. We can monitor attendance, monthly test session scores, and teacher notes in real-time from anywhere."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm avatar-initials-gold" style="width: 44px; height: 44px;">
                                TR
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Tariq Rehman</h4>
                                <small class="text-muted" style="font-size: 12.5px;">Father of Bilal (Class 8)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="col-md-4">
                    <div class="review-card">
                        <div>
                            <div class="review-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="review-quote-text">
                                "Outstanding FBISE board preparation! The faculty goes above and beyond with extra remedial sessions. My son scored 96% in his Matric exams."
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm avatar-initials-maroon" style="width: 44px; height: 44px;">
                                SK
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark" style="font-size: 15px;">Dr. Saima Khan</h4>
                                <small class="text-muted" style="font-size: 12.5px;">Parent of Hamza (Matric Board)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         8. CAMPUS VISIT / BOOKING FORM SECTION (DARK INDIGO, CENTERED WHITE CARD)
         ========================================================================= -->
    <section class="tour-cta-section section-spacing" id="tourSection">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-tag-pill-dark">CAMPUS VISIT &amp; REGISTRATION</span>
                <h2 class="display-6 fw-bold mb-2 text-white">Schedule Your Private Campus Tour Today</h2>
                <p class="lead opacity-90 max-w-600 mx-auto text-white" style="font-size: 16px;">
                    Experience our smart classrooms, STEM robotics labs, and meet the leadership team in person.
                </p>
            </div>

            <!-- Tour Success Alert -->
            @if(session('tour_success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 p-3 shadow-sm mb-4 mx-auto" style="max-width: 760px;" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                        <div>
                            <strong class="d-block">Campus Tour Booked!</strong>
                            <span class="small">{{ session('tour_success') }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Centered White Form Card (Max-Width 760px) -->
            <div class="tour-form-card mx-auto">
                <form action="{{ route('tour.schedule') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark mb-1">Contact Phone</label>
                            <input type="tel" name="parent_phone" class="form-control" placeholder="0300-1234567" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark mb-1">Select Wing</label>
                            <select name="student_wing" class="form-select" required>
                                <option value="" disabled selected>Select Wing</option>
                                <option value="Early Childhood Wing">Early Childhood Wing</option>
                                <option value="Primary School Wing">Primary School Wing</option>
                                <option value="Senior / Matric Wing">Senior / Matric Wing</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark mb-1">Preferred Date</label>
                            <input type="date" name="tour_date" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-tour-submit w-100">
                                <i class="bi bi-calendar-check-fill me-2"></i> Book Free Campus Visit &amp; Tour
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
         9. FOOTER (DARK NAVY / INDIGO, 4 COLUMNS, BADGES & COPYRIGHT)
         ========================================================================= -->
    @include('partials.public-footer')

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Pure Lightweight Vanilla JS for Hero Slider & Header -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Sticky Header Scroll Effect
            const header = document.getElementById('landingHeader');
            if (header) {
                window.addEventListener('scroll', function () {
                    if (window.scrollY > 20) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }, { passive: true });
            }

            // 2. Full-Width Hero Background Slider (5s interval, 1s crossfade)
            const slides = document.querySelectorAll('#heroBgSlider .hero-bg-slide');
            const dots = document.querySelectorAll('#heroSliderDots .hero-dot');
            if (slides.length > 0) {
                let currentSlide = 0;
                let sliderTimer = null;

                function showSlide(index) {
                    slides.forEach((slide, i) => {
                        slide.classList.toggle('active', i === index);
                    });
                    dots.forEach((dot, i) => {
                        dot.classList.toggle('active', i === index);
                    });
                    currentSlide = index;
                }

                function nextSlide() {
                    let next = (currentSlide + 1) % slides.length;
                    showSlide(next);
                }

                function startSlider() {
                    stopSlider();
                    sliderTimer = setInterval(nextSlide, 5000);
                }

                function stopSlider() {
                    if (sliderTimer) {
                        clearInterval(sliderTimer);
                        sliderTimer = null;
                    }
                }

                // Dot click handlers
                dots.forEach((dot, idx) => {
                    dot.addEventListener('click', function () {
                        showSlide(idx);
                        startSlider();
                    });
                });

                // Pause on hover over hero container
                const heroSection = document.getElementById('home');
                if (heroSection) {
                    heroSection.addEventListener('mouseenter', stopSlider);
                    heroSection.addEventListener('mouseleave', startSlider);
                }

                startSlider();
            }

            // 3. Interactive Programs Tab Filtering
            const programTabButtons = document.querySelectorAll('#programTabsGroup .program-tab-pill');
            const programCards = document.querySelectorAll('.program-item');

            function filterPrograms(filter) {
                programTabButtons.forEach(b => {
                    b.classList.toggle('active', b.getAttribute('data-filter') === filter);
                });

                programCards.forEach(card => {
                    const cat = card.getAttribute('data-category');
                    if (filter === 'all' || cat === filter) {
                        card.style.display = '';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.96) translateY(8px)';
                        setTimeout(() => {
                            card.style.transition = 'opacity 0.28s ease, transform 0.28s ease';
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1) translateY(0)';
                        }, 20);
                    } else {
                        card.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.96) translateY(8px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 200);
                    }
                });
            }

            if (programTabButtons.length > 0 && programCards.length > 0) {
                programTabButtons.forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const filter = this.getAttribute('data-filter');
                        filterPrograms(filter);
                    });
                });

                // Check URL hash if someone links directly to #preschool, #primary, #matric
                const hash = window.location.hash.toLowerCase();
                if (hash.includes('preschool')) {
                    filterPrograms('preschool');
                } else if (hash.includes('primary')) {
                    filterPrograms('primary');
                } else if (hash.includes('senior') || hash.includes('matric')) {
                    filterPrograms('senior');
                }
            }
        });
    </script>
</body>
</html>
