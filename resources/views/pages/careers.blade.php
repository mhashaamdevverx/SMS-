@extends('layouts.public')

@section('title', __('Careers & Faculty Openings') . ' - Excellence School System')

@section('content')
<!-- Page Banner Header -->
<section class="page-banner text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%); padding: 125px 0 50px; color: #FFFFFF;">
    <div class="container position-relative" style="z-index: 2;">
        <span class="badge px-3 py-2 rounded-pill mb-3 shadow-sm" style="background: rgba(79, 70, 229, 0.25); color: #A5B4FC; border: 1px solid rgba(165, 180, 252, 0.3); font-size: 12px; letter-spacing: 0.05em;">
            <i class="bi bi-briefcase-fill me-1 text-warning"></i> {{ __('JOIN OUR WORLD-CLASS FACULTY') }}
        </span>
        <h1 class="fw-bold mb-3 text-white" style="color: #FFFFFF !important; font-size: clamp(2rem, 4vw, 2.8rem); letter-spacing: -0.02em;">{{ __('Build the Future of Education With Us') }}</h1>
        <p class="text-white opacity-85 mx-auto mb-0" style="color: rgba(255, 255, 255, 0.85) !important; max-width: 680px; font-size: 15.5px; line-height: 1.6;">
            {{ __('Excellence School System is seeking forward-thinking educators, curriculum innovators, and academic mentors committed to shaping tomorrow\'s leaders.') }}
        </p>
    </div>
</section>

<!-- Main Careers Section -->
<section class="py-5 bg-light-subtle">
    <div class="container py-3">

        <!-- Success Alert -->
        @if(session('career_success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 shadow-sm mb-4 border-0" role="alert" style="background: #ECFDF5; border-left: 6px solid #10B981 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; background: #10B981; flex-shrink: 0;">
                        <i class="bi bi-check-lg fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">{{ __('Application Submitted Successfully!') }}</h5>
                        <p class="mb-0 text-muted small" style="font-size: 13.5px;">{{ session('career_success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 p-4 shadow-sm mb-4 border-0" role="alert" style="background: #FEF2F2; border-left: 6px solid #EF4444 !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; background: #EF4444; flex-shrink: 0;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-danger mb-2">{{ __('Please correct the following errors:') }}</h6>
                        <ul class="mb-0 small ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        <!-- TOP SEARCH & FILTER BAR -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-lg rounded-3 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 text-primary ps-3">
                            <i class="bi bi-search fs-5"></i>
                        </span>
                        <input type="text" id="careerSearchInput" class="form-control border-0 shadow-none ps-2" placeholder="{{ __('Search by job title, subject (e.g. Physics, Computer, Montessori)...') }}" autocomplete="off" style="font-size: 15px;">
                        <button type="button" id="clearSearchBtn" class="btn btn-white border-0 text-muted d-none" title="{{ __('Clear Search') }}">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                <div class="col-lg-4 col-md-8">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-bold text-nowrap d-none d-sm-inline"><i class="bi bi-funnel-fill me-1 text-primary"></i> {{ __('Wing:') }}</span>
                        <select id="wingFilterSelect" class="form-select form-select-lg rounded-3 border" style="font-size: 14.5px;">
                            <option value="all">{{ __('All Departments & Wings') }}</option>
                            <option value="SENIOR WING">{{ __('Senior Wing (Matric & FBISE)') }}</option>
                            <option value="STEM INNOVATION">{{ __('STEM & Computer Science') }}</option>
                            <option value="PRIMARY WING">{{ __('Primary & Montessori Wing') }}</option>
                            <option value="CAMBRIDGE WING">{{ __('Cambridge O / A Levels') }}</option>
                            <option value="MIDDLE WING">{{ __('Middle School Wing') }}</option>
                            <option value="ADMINISTRATION">{{ __('Administration & Operations') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 text-lg-end text-center">
                    <span class="badge rounded-pill px-3 py-2 text-primary bg-primary-subtle fw-bold" id="vacanciesCountBadge" style="font-size: 12.5px;">
                        <i class="bi bi-briefcase me-1"></i> {{ __('Showing') }} <span id="visibleCount">6</span> {{ __('Openings') }}
                    </span>
                </div>
            </div>

            <!-- Quick Filter Tags -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                <span class="text-muted small fw-bold me-1">{{ __('Popular:') }}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 quick-tag-btn" data-keyword="Physics">{{ __('Physics') }}</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 quick-tag-btn" data-keyword="Computer">{{ __('Computer & Robotics') }}</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 quick-tag-btn" data-keyword="Montessori">{{ __('Montessori') }}</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 quick-tag-btn" data-keyword="Cambridge">{{ __('Cambridge O-Level') }}</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 quick-tag-btn" data-keyword="Administration">{{ __('Administration') }}</button>
                <button type="button" class="btn btn-sm btn-link text-decoration-none py-1 px-2 text-muted small ms-auto" id="resetFiltersBtn">{{ __('Reset Filters') }}</button>
            </div>
        </div>

        <!-- VACANCIES LIST (FULL WIDTH GRID) -->
        <div class="row g-4" id="vacanciesContainer">

            <!-- VACANCY CARD 1 -->
            <div class="col-lg-6 vacancy-item" data-title="Senior Secondary Physics & Math Lecturer (FBISE)" data-wing="SENIOR WING" data-keywords="physics math mathematics lecturer fbise senior matric science teacher">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative vacancy-card bg-white" style="transition: all 0.3s ease; border-left: 5px solid #4F46E5 !important;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <span class="badge px-3 py-1 rounded-pill" style="background: rgba(79, 70, 229, 0.12); color: #4F46E5; font-weight: 700; font-size: 11.5px;">
                            {{ __('SENIOR WING') }}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small fw-semibold">
                            <i class="bi bi-clock me-1 text-primary"></i> {{ __('Full Time') }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-2 job-title" style="font-size: 18.5px; line-height: 1.35;">
                        {{ __('Senior Secondary Physics & Math Lecturer (FBISE)') }}
                    </h4>

                    <p class="text-muted small mb-3" style="line-height: 1.55;">
                        {{ __('Requirements: M.Sc / M.Phil in Physics or Mathematics with 3+ years experience teaching Matric and FBISE Federal Board classes with proven distinction results.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('FBISE Board') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Physics Lab') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('3+ Yrs Exp') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <span class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('Islamabad Main Campus') }}</span>
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm apply-btn"
                            onclick="openCareerModal('Senior Secondary Physics & Math Lecturer (FBISE)', 'SENIOR WING')">
                            {{ __('Apply for this Role') }} <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- VACANCY CARD 2 -->
            <div class="col-lg-6 vacancy-item" data-title="Robotics & Computer Science Instructor" data-wing="STEM INNOVATION" data-keywords="robotics computer science programming coding stem arduino python software ai">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative vacancy-card bg-white" style="transition: all 0.3s ease; border-left: 5px solid #0EA5E9 !important;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <span class="badge px-3 py-1 rounded-pill" style="background: rgba(14, 165, 233, 0.12); color: #0284C7; font-weight: 700; font-size: 11.5px;">
                            {{ __('STEM INNOVATION') }}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small fw-semibold">
                            <i class="bi bi-clock me-1 text-primary"></i> {{ __('Full Time') }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-2 job-title" style="font-size: 18.5px; line-height: 1.35;">
                        {{ __('Robotics & Computer Science Instructor') }}
                    </h4>

                    <p class="text-muted small mb-3" style="line-height: 1.55;">
                        {{ __('Requirements: BS Computer Science / Software Engineering with proficiency in Python, Arduino, Scratch, and 3D printing instruction for young innovators.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Python / C++') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Arduino Robotics') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('1+ Yrs Exp') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <span class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('Innovation Hub, Islamabad') }}</span>
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm apply-btn"
                            onclick="openCareerModal('Robotics & Computer Science Instructor', 'STEM INNOVATION')">
                            {{ __('Apply for this Role') }} <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- VACANCY CARD 3 -->
            <div class="col-lg-6 vacancy-item" data-title="Montessori Directress & Primary English Homeroom" data-wing="PRIMARY WING" data-keywords="montessori directress primary english homeroom early childhood phonics kindergarten">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative vacancy-card bg-white" style="transition: all 0.3s ease; border-left: 5px solid #F59E0B !important;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <span class="badge px-3 py-1 rounded-pill" style="background: rgba(245, 158, 11, 0.15); color: #B45309; font-weight: 700; font-size: 11.5px;">
                            {{ __('PRIMARY WING') }}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small fw-semibold">
                            <i class="bi bi-clock me-1 text-primary"></i> {{ __('Full Time') }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-2 job-title" style="font-size: 18.5px; line-height: 1.35;">
                        {{ __('Montessori Directress & Primary English Homeroom') }}
                    </h4>

                    <p class="text-muted small mb-3" style="line-height: 1.55;">
                        {{ __('Requirements: Certified Montessori diploma with bachelor\'s degree and native-level English phonics, storytelling, and early childhood creative instruction experience.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Montessori Diploma') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Jolly Phonics') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('2+ Yrs Exp') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <span class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('Junior Wing, Islamabad') }}</span>
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm apply-btn"
                            onclick="openCareerModal('Montessori Directress & Primary English Homeroom', 'PRIMARY WING')">
                            {{ __('Apply for this Role') }} <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- VACANCY CARD 4 -->
            <div class="col-lg-6 vacancy-item" data-title="Cambridge O-Level Chemistry & Biology Teacher" data-wing="CAMBRIDGE WING" data-keywords="cambridge igcse o level a level chemistry biology science teacher igcse cie">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative vacancy-card bg-white" style="transition: all 0.3s ease; border-left: 5px solid #10B981 !important;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <span class="badge px-3 py-1 rounded-pill" style="background: rgba(16, 185, 129, 0.12); color: #047857; font-weight: 700; font-size: 11.5px;">
                            {{ __('CAMBRIDGE WING') }}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small fw-semibold">
                            <i class="bi bi-clock me-1 text-primary"></i> {{ __('Full Time') }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-2 job-title" style="font-size: 18.5px; line-height: 1.35;">
                        {{ __('Cambridge O-Level Chemistry & Biology Teacher') }}
                    </h4>

                    <p class="text-muted small mb-3" style="line-height: 1.55;">
                        {{ __('Requirements: M.Sc in Chemistry/Biochemistry/Zoology with at least 3 years teaching Cambridge CAIE IGCSE / O-Level curriculum with A* results track record.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('CAIE Curriculum') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Science Lab Skills') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('3+ Yrs Exp') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <span class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('Cambridge Campus, Islamabad') }}</span>
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm apply-btn"
                            onclick="openCareerModal('Cambridge O-Level Chemistry & Biology Teacher', 'CAMBRIDGE WING')">
                            {{ __('Apply for this Role') }} <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- VACANCY CARD 5 -->
            <div class="col-lg-6 vacancy-item" data-title="Middle School Social Studies & Urdu Literature Specialist" data-wing="MIDDLE WING" data-keywords="middle school social studies history urdu literature geography islamic studies">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative vacancy-card bg-white" style="transition: all 0.3s ease; border-left: 5px solid #8B5CF6 !important;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <span class="badge px-3 py-1 rounded-pill" style="background: rgba(139, 92, 246, 0.12); color: #6D28D9; font-weight: 700; font-size: 11.5px;">
                            {{ __('MIDDLE WING') }}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small fw-semibold">
                            <i class="bi bi-clock me-1 text-primary"></i> {{ __('Full Time') }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-2 job-title" style="font-size: 18.5px; line-height: 1.35;">
                        {{ __('Middle School Social Studies & Urdu Literature Specialist') }}
                    </h4>

                    <p class="text-muted small mb-3" style="line-height: 1.55;">
                        {{ __('Requirements: Master\'s in Urdu / Pakistan Studies / History with strong pedagogical skills to foster critical thinking, debate, and bilingual cultural literacy.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Pak Studies') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Urdu Literature') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('2+ Yrs Exp') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <span class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('Main Campus, Islamabad') }}</span>
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm apply-btn"
                            onclick="openCareerModal('Middle School Social Studies & Urdu Literature Specialist', 'MIDDLE WING')">
                            {{ __('Apply for this Role') }} <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- VACANCY CARD 6 -->
            <div class="col-lg-6 vacancy-item" data-title="Student Academic Counselor & Operations Administrator" data-wing="ADMINISTRATION" data-keywords="counselor academic advisor student psychologist career counseling administration coordinator">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative vacancy-card bg-white" style="transition: all 0.3s ease; border-left: 5px solid #EC4899 !important;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <span class="badge px-3 py-1 rounded-pill" style="background: rgba(236, 72, 153, 0.12); color: #BE185D; font-weight: 700; font-size: 11.5px;">
                            {{ __('ADMINISTRATION') }}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small fw-semibold">
                            <i class="bi bi-clock me-1 text-primary"></i> {{ __('Full Time') }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-2 job-title" style="font-size: 18.5px; line-height: 1.35;">
                        {{ __('Student Academic Counselor & Operations Administrator') }}
                    </h4>

                    <p class="text-muted small mb-3" style="line-height: 1.55;">
                        {{ __('Requirements: BS / MS in Applied Psychology, Educational Leadership, or Guidance Counseling with exceptional parent liaison and career advisory abilities.') }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Career Guidance') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('Student Welfare') }}</span>
                        <span class="badge bg-light text-secondary border small"><i class="bi bi-check2 me-1 text-success"></i> {{ __('2+ Yrs Exp') }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                        <span class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('Admin Block, Islamabad') }}</span>
                        <button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm apply-btn"
                            onclick="openCareerModal('Student Academic Counselor & Operations Administrator', 'ADMINISTRATION')">
                            {{ __('Apply for this Role') }} <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- NO RESULTS PLACEHOLDER -->
        <div id="noVacanciesAlert" class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4 d-none">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; background: rgba(79, 70, 229, 0.1); color: #4F46E5;">
                    <i class="bi bi-search fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('No Matching Vacancies Found') }}</h4>
                <p class="text-muted small mb-4" style="max-width: 460px;">
                    {{ __('We couldn\'t find any job openings matching your search criteria. You can clear the search filter or submit a general application for future openings.') }}
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-bold" onclick="resetAllSearchFilters()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset Search & View All') }}
                    </button>
                    <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" onclick="openCareerModal('General Faculty / Staff Application', 'GENERAL')">
                        <i class="bi bi-file-earmark-plus me-1"></i> {{ __('Submit General Profile') }}
                    </button>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ============================================================== -->
<!-- 4-PROCESS INTERACTIVE APPLICATION MODAL WIZARD               -->
<!-- ============================================================== -->
<div class="modal fade" id="careerApplyModal" tabindex="-1" aria-labelledby="careerApplyModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-height: 92vh;">
        <!-- Single Form as Modal Content (Ensures fixed header/footer and full scrollable body) -->
        <form action="{{ route('career.submit') }}" method="POST" id="careerApplicationForm" class="modal-content border-0 rounded-4 shadow-2xl overflow-hidden h-100" novalidate>
            @csrf
            <!-- Hidden inputs for selected job and wing -->
            <input type="hidden" name="job_title" id="formJobTitle" value="">
            <input type="hidden" name="wing" id="formJobWing" value="">

            <!-- Modal Header (Pinned at top) -->
            <div class="modal-header border-0 text-white p-3 p-md-4 flex-shrink-0" style="background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%);">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge px-3 py-1 rounded-pill" id="modalWingBadge" style="background: rgba(79, 70, 229, 0.3); color: #C7D2FE; border: 1px solid rgba(199, 210, 254, 0.2); font-size: 11.5px;">
                            SENIOR WING
                        </span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <h4 class="modal-title fw-bold text-white mb-1" id="careerApplyModalLabel" style="font-size: 19px;">
                        {{ __('Apply for Role') }}
                    </h4>
                    <p class="text-light opacity-75 small mb-0" style="font-size: 12.5px;">
                        {{ __('Complete the 4 application processes below. You can navigate back at any step to edit your filled details.') }}
                    </p>
                </div>
            </div>

            <!-- Visual 4-Step Stepper Progress Bar (Pinned under header) -->
            <div class="bg-white border-bottom px-3 px-md-4 py-2 flex-shrink-0">
                <div class="stepper-wrapper d-flex justify-content-between position-relative">
                    <!-- Step 1 Indicator -->
                    <div class="step-indicator text-center is-active" id="stepIndicator1" onclick="goToStep(1)">
                        <div class="step-circle mx-auto mb-1">1</div>
                        <div class="step-title">{{ __('Personal Info') }}</div>
                    </div>
                    <!-- Step 2 Indicator -->
                    <div class="step-indicator text-center" id="stepIndicator2" onclick="goToStep(2)">
                        <div class="step-circle mx-auto mb-1">2</div>
                        <div class="step-title">{{ __('Education') }}</div>
                    </div>
                    <!-- Step 3 Indicator -->
                    <div class="step-indicator text-center" id="stepIndicator3" onclick="goToStep(3)">
                        <div class="step-circle mx-auto mb-1">3</div>
                        <div class="step-title">{{ __('Experience') }}</div>
                    </div>
                    <!-- Step 4 Indicator -->
                    <div class="step-indicator text-center" id="stepIndicator4" onclick="goToStep(4)">
                        <div class="step-circle mx-auto mb-1">4</div>
                        <div class="step-title">{{ __('Relative & Review') }}</div>
                    </div>
                </div>
            </div>

            <!-- Modal Body (Takes remaining height and scrolls smoothly, never overlapping footer) -->
            <div class="modal-body p-4 bg-light-subtle flex-grow-1" style="overflow-y: auto;">

                <!-- ============================================== -->
                <!-- PROCESS 1: INTRODUCTION & PERSONAL DETAILS     -->
                <!-- ============================================== -->
                <div class="wizard-step" id="wizardStep1">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge rounded-circle bg-primary text-white p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">1</span>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ __('Introduction & Personal Details') }}</h5>
                            <small class="text-muted">{{ __('Provide your legal identification and contact details.') }}</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Full Legal Name') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="full_name" id="input_full_name" class="form-control border-start-0" placeholder="{{ __('e.g. Dr. Salman Khan') }}" required>
                            </div>
                            <div class="invalid-feedback d-none" id="err_full_name">{{ __('Full name is required.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('CNIC / ID Card No (13 Digits)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-person-vcard-fill"></i></span>
                                <input type="text" name="cnic" id="input_cnic" class="form-control border-start-0 font-monospace" placeholder="XXXXX-XXXXXXX-X" maxlength="15" required>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">{{ __('NADRA 13-digit format (e.g. 61101-1234567-1)') }}</small>
                            <div class="invalid-feedback d-none" id="err_cnic">{{ __('CNIC must be exactly 13 numeric digits.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" id="input_email" class="form-control border-start-0" placeholder="salman.khan@domain.com" required>
                            </div>
                            <div class="invalid-feedback d-none" id="err_email">{{ __('Valid email is required.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Mobile / WhatsApp Phone') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-telephone-fill"></i></span>
                                <input type="tel" name="phone" id="input_phone" class="form-control border-start-0" placeholder="0300-1234567" required>
                            </div>
                            <div class="invalid-feedback d-none" id="err_phone">{{ __('Mobile phone number is required.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('City of Current Residence') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" name="city" id="input_city" class="form-control border-start-0" placeholder="{{ __('e.g. Islamabad, Rawalpindi') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Gender') }} <span class="text-danger">*</span></label>
                            <select name="gender" id="input_gender" class="form-select" required>
                                <option value="" disabled selected>{{ __('Select Gender') }}</option>
                                <option value="Male">{{ __('Male') }}</option>
                                <option value="Female">{{ __('Female') }}</option>
                                <option value="Other">{{ __('Other') }}</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">{{ __('Brief Introduction / Professional Summary') }}</label>
                            <textarea name="introduction" id="input_introduction" rows="2" class="form-control" placeholder="{{ __('Introduce yourself, your teaching philosophy, or career aspirations...') }}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- PROCESS 2: EDUCATION DETAILS                   -->
                <!-- ============================================== -->
                <div class="wizard-step d-none" id="wizardStep2">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge rounded-circle bg-primary text-white p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">2</span>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ __('Education Details') }}</h5>
                            <small class="text-muted">{{ __('Enter your highest academic degree and university credentials.') }}</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Highest Qualification Level') }} <span class="text-danger">*</span></label>
                            <select name="highest_degree" id="input_highest_degree" class="form-select" required>
                                <option value="" disabled selected>{{ __('Select Degree Level') }}</option>
                                <option value="Ph.D. / Doctorate">{{ __('Ph.D. / Doctorate') }}</option>
                                <option value="MS / M.Phil (18 Years)">{{ __('MS / M.Phil (18 Years)') }}</option>
                                <option value="Master's / M.Sc / M.A (16 Years)">{{ __("Master's / M.Sc / M.A (16 Years)") }}</option>
                                <option value="Bachelor's / BS (4 Years)">{{ __("Bachelor's / BS (4 Years)") }}</option>
                                <option value="Associate Degree / 2-Yr BA/B.Sc">{{ __('Associate Degree / 2-Yr BA/B.Sc') }}</option>
                                <option value="Montessori / B.Ed / Teaching Diploma">{{ __('Montessori / B.Ed / Teaching Diploma') }}</option>
                                <option value="Intermediate / A-Levels">{{ __('Intermediate / A-Levels') }}</option>
                            </select>
                            <div class="invalid-feedback d-none" id="err_highest_degree">{{ __('Please select your highest degree level.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Degree Title / Major Subject') }} <span class="text-danger">*</span></label>
                            <input type="text" name="degree_title" id="input_degree_title" class="form-control" placeholder="{{ __('e.g. M.Phil Physics, BS Computer Science') }}" required>
                            <div class="invalid-feedback d-none" id="err_degree_title">{{ __('Degree title is required.') }}</div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-dark">{{ __('University / Board / Awarding Institute') }} <span class="text-danger">*</span></label>
                            <input type="text" name="institute" id="input_institute" class="form-control" placeholder="{{ __('e.g. Quaid-i-Azam University, NUST, Punjab University') }}" required>
                            <div class="invalid-feedback d-none" id="err_institute">{{ __('University or Institute name is required.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Passing Year') }} <span class="text-danger">*</span></label>
                            <input type="number" name="passing_year" id="input_passing_year" class="form-control" placeholder="{{ __('e.g. 2022') }}" min="1980" max="2027" required>
                            <div class="invalid-feedback d-none" id="err_passing_year">{{ __('Passing year is required.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('CGPA / Percentage / Division') }}</label>
                            <input type="text" name="marks_cgpa" id="input_marks_cgpa" class="form-control" placeholder="{{ __('e.g. 3.82 CGPA or 85%') }}">
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- PROCESS 3: EXPERIENCE DETAILS                  -->
                <!-- ============================================== -->
                <div class="wizard-step d-none" id="wizardStep3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge rounded-circle bg-primary text-white p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">3</span>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ __('Experience & Professional History') }}</h5>
                            <small class="text-muted">{{ __('Outline your teaching or administrative track record.') }}</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Total Relevant Experience') }} <span class="text-danger">*</span></label>
                            <select name="experience_years" id="input_experience_years" class="form-select" required>
                                <option value="" disabled selected>{{ __('Select Experience') }}</option>
                                <option value="Fresh Graduate (0-1 yrs)">{{ __('Fresh Graduate (0 - 1 years)') }}</option>
                                <option value="1-3 years">{{ __('1 - 3 years') }}</option>
                                <option value="3-5 years">{{ __('3 - 5 years') }}</option>
                                <option value="5-10 years">{{ __('5 - 10 years') }}</option>
                                <option value="10+ years">{{ __('10+ years') }}</option>
                            </select>
                            <div class="invalid-feedback d-none" id="err_experience_years">{{ __('Please select your experience.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Current / Last Organization') }}</label>
                            <input type="text" name="last_organization" id="input_last_organization" class="form-control" placeholder="{{ __('e.g. Army Public School, Roots, Beaconhouse') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Last Designation / Role') }}</label>
                            <input type="text" name="last_designation" id="input_last_designation" class="form-control" placeholder="{{ __('e.g. Senior Lecturer, Subject Lead') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ __('Core Skills / Subjects Taught') }}</label>
                            <input type="text" name="core_skills" id="input_core_skills" class="form-control" placeholder="{{ __('e.g. FBISE Physics, Cambridge CAIE, Lab Instruction') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">{{ __('Cover Note / Key Achievements') }}</label>
                            <textarea name="cover_note" id="input_cover_note" rows="2" class="form-control" placeholder="{{ __('Highlight why you are an ideal fit for Excellence School System...') }}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- PROCESS 4: RELATIVE / REFERENCE & REVIEW       -->
                <!-- ============================================== -->
                <div class="wizard-step d-none" id="wizardStep4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge rounded-circle bg-primary text-white p-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">4</span>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ __('Relative / Staff Disclosure & Final Review') }}</h5>
                            <small class="text-muted">{{ __('Mandatory institutional transparency disclosure and profile confirmation.') }}</small>
                        </div>
                    </div>

                    <!-- Relative Disclosure Box -->
                    <div class="card border rounded-4 p-3 mb-3 bg-white shadow-sm">
                        <label class="form-label fw-bold text-dark mb-2" style="font-size: 14px;">
                            <i class="bi bi-people-fill text-primary me-2"></i>
                            {{ __('Do you have any relative currently or previously working at Excellence School System?') }} <span class="text-danger">*</span>
                        </label>

                        <div class="d-flex gap-4 mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_relative" id="rel_no" value="no" checked onchange="toggleRelativeDetails(false)">
                                <label class="form-check-label fw-semibold text-dark" for="rel_no">
                                    {{ __('No, I do not have any relative working here') }}
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_relative" id="rel_yes" value="yes" onchange="toggleRelativeDetails(true)">
                                <label class="form-check-label fw-semibold text-dark" for="rel_yes">
                                    {{ __('Yes, I have a relative working / worked here') }}
                                </label>
                            </div>
                        </div>

                        <!-- Conditional Relative Fields -->
                        <div id="relativeDetailsFields" class="row g-2 p-3 rounded-3 bg-light border d-none mt-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">{{ __("Relative's Full Name") }} <span class="text-danger">*</span></label>
                                <input type="text" name="relative_name" id="input_relative_name" class="form-control" placeholder="{{ __('e.g. Sir Tariq Mehmood') }}">
                                <div class="invalid-feedback d-none" id="err_relative_name">{{ __('Relative\'s name is required when Yes is selected.') }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">{{ __('Their Designation / Dept') }}</label>
                                <input type="text" name="relative_designation" id="input_relative_designation" class="form-control" placeholder="{{ __('e.g. Senior Faculty / Admin') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">{{ __('Relationship') }}</label>
                                <input type="text" name="relative_relation" id="input_relative_relation" class="form-control" placeholder="{{ __('e.g. Brother, Sister, Spouse, Uncle') }}">
                            </div>
                        </div>
                    </div>

                    <!-- LIVE APPLICATION SUMMARY REVIEW CARD -->
                    <div class="card border-0 rounded-4 p-3 bg-white shadow-sm" style="border-left: 4px solid #4F46E5 !important;">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-check-fill text-primary me-2"></i> {{ __('Review Summary Before Submission') }}</h6>
                        <div class="row g-2 text-dark small">
                            <div class="col-sm-6">
                                <span class="text-muted">{{ __('Candidate Name:') }}</span>
                                <strong id="rev_name" class="d-block text-truncate">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">{{ __('CNIC:') }}</span>
                                <strong id="rev_cnic" class="d-block font-monospace">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">{{ __('Target Position:') }}</span>
                                <strong id="rev_position" class="d-block text-primary">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">{{ __('Highest Degree:') }}</span>
                                <strong id="rev_degree" class="d-block text-truncate">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">{{ __('Experience:') }}</span>
                                <strong id="rev_exp" class="d-block">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">{{ __('Relative Disclosure:') }}</span>
                                <strong id="rev_relative" class="d-block text-success">{{ __('No Relative') }}</strong>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Modal Footer with Back & Continue / Submit Buttons (PINNED AT BOTTOM, ALWAYS VISIBLE!) -->
            <div class="modal-footer border-top bg-white p-3 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="box-shadow: 0 -4px 16px rgba(15, 23, 42, 0.05); min-height: 72px;">
                <div>
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-bold shadow-sm d-none" id="wizardBackBtn" onclick="navigateStep(-1)">
                        <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Previous') }}
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light px-3 py-2 rounded-3 text-muted fw-bold border" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm" id="wizardNextBtn" onclick="navigateStep(1)" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); min-width: 190px;">
                        {{ __('Continue to Education') }} <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-bold shadow-sm d-none" id="wizardSubmitBtn" style="background: #10B981; border: none; min-width: 220px;">
                        <i class="bi bi-check2-circle me-1"></i> {{ __('Submit Application') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Modern Vacancy Card Styles */
.vacancy-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.vacancy-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08) !important;
}

/* Stepper Progress Styles */
.stepper-wrapper {
    margin: 0 10px;
}
.stepper-wrapper::before {
    content: '';
    position: absolute;
    top: 16px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #E2E8F0;
    z-index: 1;
}
.step-indicator {
    position: relative;
    z-index: 2;
    cursor: pointer;
    flex: 1;
}
.step-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #F1F5F9;
    color: #64748B;
    border: 2px solid #CBD5E1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    transition: all 0.25s ease;
}
.step-title {
    font-size: 11.5px;
    font-weight: 600;
    color: #64748B;
}
.step-indicator.is-active .step-circle {
    background: #4F46E5;
    color: #FFFFFF;
    border-color: #4F46E5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
}
.step-indicator.is-active .step-title {
    color: #4F46E5;
    font-weight: 700;
}
.step-indicator.is-completed .step-circle {
    background: #10B981;
    color: #FFFFFF;
    border-color: #10B981;
}
.step-indicator.is-completed .step-title {
    color: #059669;
}
</style>
@endpush

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 4;

    // Open modal with selected job info
    function openCareerModal(jobTitle, wing) {
        document.getElementById('formJobTitle').value = jobTitle;
        document.getElementById('formJobWing').value = wing;
        document.getElementById('careerApplyModalLabel').innerText = @json(__('Apply for')) + ' ' + jobTitle;
        document.getElementById('modalWingBadge').innerText = wing;

        // Reset to Step 1
        goToStep(1);

        const modalEl = document.getElementById('careerApplyModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    // Toggle Relative disclosure fields
    function toggleRelativeDetails(show) {
        const fields = document.getElementById('relativeDetailsFields');
        const relativeNameInput = document.getElementById('input_relative_name');
        if (show) {
            fields.classList.remove('d-none');
            relativeNameInput.setAttribute('required', 'required');
            relativeNameInput.focus();
        } else {
            fields.classList.add('d-none');
            relativeNameInput.removeAttribute('required');
            document.getElementById('err_relative_name').classList.add('d-none');
        }
        updateReviewSummary();
    }

    // CNIC Auto-Formatting XXXXX-XXXXXXX-X
    const cnicInput = document.getElementById('input_cnic');
    if (cnicInput) {
        cnicInput.addEventListener('input', function(e) {
            let val = this.value.replace(/\D/g, '');
            if (val.length > 13) val = val.substring(0, 13);
            let formatted = val;
            if (val.length > 5 && val.length <= 12) {
                formatted = val.substring(0, 5) + '-' + val.substring(5);
            } else if (val.length > 12) {
                formatted = val.substring(0, 5) + '-' + val.substring(5, 12) + '-' + val.substring(12, 13);
            }
            this.value = formatted;
        });
    }

    // Validate a specific step
    function validateStep(step) {
        let isValid = true;

        if (step === 1) {
            const name = document.getElementById('input_full_name');
            const errName = document.getElementById('err_full_name');
            if (!name.value.trim()) {
                name.classList.add('is-invalid');
                errName.classList.remove('d-none');
                isValid = false;
            } else {
                name.classList.remove('is-invalid');
                errName.classList.add('d-none');
            }

            const cnic = document.getElementById('input_cnic');
            const errCnic = document.getElementById('err_cnic');
            const cleanCnic = cnic.value.replace(/\D/g, '');
            if (cleanCnic.length !== 13) {
                cnic.classList.add('is-invalid');
                errCnic.classList.remove('d-none');
                isValid = false;
            } else {
                cnic.classList.remove('is-invalid');
                errCnic.classList.add('d-none');
            }

            const email = document.getElementById('input_email');
            const errEmail = document.getElementById('err_email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim() || !emailRegex.test(email.value.trim())) {
                email.classList.add('is-invalid');
                errEmail.classList.remove('d-none');
                isValid = false;
            } else {
                email.classList.remove('is-invalid');
                errEmail.classList.add('d-none');
            }

            const phone = document.getElementById('input_phone');
            const errPhone = document.getElementById('err_phone');
            if (!phone.value.trim()) {
                phone.classList.add('is-invalid');
                errPhone.classList.remove('d-none');
                isValid = false;
            } else {
                phone.classList.remove('is-invalid');
                errPhone.classList.add('d-none');
            }
        } else if (step === 2) {
            const degree = document.getElementById('input_highest_degree');
            const errDegree = document.getElementById('err_highest_degree');
            if (!degree.value) {
                degree.classList.add('is-invalid');
                errDegree.classList.remove('d-none');
                isValid = false;
            } else {
                degree.classList.remove('is-invalid');
                errDegree.classList.add('d-none');
            }

            const title = document.getElementById('input_degree_title');
            const errTitle = document.getElementById('err_degree_title');
            if (!title.value.trim()) {
                title.classList.add('is-invalid');
                errTitle.classList.remove('d-none');
                isValid = false;
            } else {
                title.classList.remove('is-invalid');
                errTitle.classList.add('d-none');
            }

            const inst = document.getElementById('input_institute');
            const errInst = document.getElementById('err_institute');
            if (!inst.value.trim()) {
                inst.classList.add('is-invalid');
                errInst.classList.remove('d-none');
                isValid = false;
            } else {
                inst.classList.remove('is-invalid');
                errInst.classList.add('d-none');
            }

            const yr = document.getElementById('input_passing_year');
            const errYr = document.getElementById('err_passing_year');
            if (!yr.value.trim()) {
                yr.classList.add('is-invalid');
                errYr.classList.remove('d-none');
                isValid = false;
            } else {
                yr.classList.remove('is-invalid');
                errYr.classList.add('d-none');
            }
        } else if (step === 3) {
            const exp = document.getElementById('input_experience_years');
            const errExp = document.getElementById('err_experience_years');
            if (!exp.value) {
                exp.classList.add('is-invalid');
                errExp.classList.remove('d-none');
                isValid = false;
            } else {
                exp.classList.remove('is-invalid');
                errExp.classList.add('d-none');
            }
        } else if (step === 4) {
            const relYes = document.getElementById('rel_yes');
            if (relYes.checked) {
                const relName = document.getElementById('input_relative_name');
                const errRelName = document.getElementById('err_relative_name');
                if (!relName.value.trim()) {
                    relName.classList.add('is-invalid');
                    errRelName.classList.remove('d-none');
                    isValid = false;
                } else {
                    relName.classList.remove('is-invalid');
                    errRelName.classList.add('d-none');
                }
            }
        }

        return isValid;
    }

    // Step Navigation
    function navigateStep(direction) {
        if (direction === 1) {
            // Forward: Validate current step first
            if (!validateStep(currentStep)) {
                return;
            }
            goToStep(currentStep + 1);
        } else if (direction === -1) {
            // Backward: No validation needed, all inputs are preserved!
            goToStep(currentStep - 1);
        }
    }

    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;

        // If jumping forward via indicator, validate previous steps
        if (step > currentStep) {
            for (let s = currentStep; s < step; s++) {
                if (!validateStep(s)) return;
            }
        }

        currentStep = step;

        // Show/Hide step panes
        for (let i = 1; i <= totalSteps; i++) {
            const pane = document.getElementById('wizardStep' + i);
            const indicator = document.getElementById('stepIndicator' + i);
            if (pane) {
                if (i === currentStep) {
                    pane.classList.remove('d-none');
                } else {
                    pane.classList.add('d-none');
                }
            }

            if (indicator) {
                if (i === currentStep) {
                    indicator.className = 'step-indicator text-center is-active';
                } else if (i < currentStep) {
                    indicator.className = 'step-indicator text-center is-completed';
                } else {
                    indicator.className = 'step-indicator text-center';
                }
            }
        }

        // Configure buttons
        const backBtn = document.getElementById('wizardBackBtn');
        const nextBtn = document.getElementById('wizardNextBtn');
        const submitBtn = document.getElementById('wizardSubmitBtn');

        if (currentStep === 1) {
            backBtn.classList.add('d-none');
            nextBtn.classList.remove('d-none');
            nextBtn.innerHTML = @json(__('Continue to Education')) + ' <i class="bi bi-arrow-right ms-1"></i>';
            submitBtn.classList.add('d-none');
        } else if (currentStep === 2) {
            backBtn.classList.remove('d-none');
            backBtn.innerHTML = '<i class="bi bi-arrow-left me-1"></i> ' + @json(__('Back to Personal'));
            nextBtn.classList.remove('d-none');
            nextBtn.innerHTML = @json(__('Continue to Experience')) + ' <i class="bi bi-arrow-right ms-1"></i>';
            submitBtn.classList.add('d-none');
        } else if (currentStep === 3) {
            backBtn.classList.remove('d-none');
            backBtn.innerHTML = '<i class="bi bi-arrow-left me-1"></i> ' + @json(__('Back to Education'));
            nextBtn.classList.remove('d-none');
            nextBtn.innerHTML = @json(__('Continue to Relative Disclosure')) + ' <i class="bi bi-arrow-right ms-1"></i>';
            submitBtn.classList.add('d-none');
        } else if (currentStep === 4) {
            backBtn.classList.remove('d-none');
            backBtn.innerHTML = '<i class="bi bi-arrow-left me-1"></i> ' + @json(__('Back to Experience'));
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
            updateReviewSummary();
        }

        // Scroll modal body smoothly to top when step changes
        const modalBody = document.querySelector('#careerApplyModal .modal-body');
        if (modalBody) {
            modalBody.scrollTop = 0;
        }
    }

    // Populate Step 4 review card
    function updateReviewSummary() {
        document.getElementById('rev_name').innerText = document.getElementById('input_full_name').value || @json(__('Not provided'));
        document.getElementById('rev_cnic').innerText = document.getElementById('input_cnic').value || @json(__('Not provided'));
        document.getElementById('rev_position').innerText = document.getElementById('formJobTitle').value || @json(__('Faculty Role'));
        document.getElementById('rev_degree').innerText = (document.getElementById('input_highest_degree').value || '') + ' - ' + (document.getElementById('input_degree_title').value || '');
        document.getElementById('rev_exp').innerText = document.getElementById('input_experience_years').value || @json(__('Not selected'));

        const isRelYes = document.getElementById('rel_yes').checked;
        const revRel = document.getElementById('rev_relative');
        if (isRelYes) {
            const relName = document.getElementById('input_relative_name').value || @json(__('Relative'));
            const relDesig = document.getElementById('input_relative_designation').value || '';
            revRel.className = 'd-block text-warning fw-bold';
            revRel.innerText = @json(__('Yes:')) + ' ' + relName + (relDesig ? ' (' + relDesig + ')' : '');
        } else {
            revRel.className = 'd-block text-success fw-bold';
            revRel.innerText = @json(__('No Relative Working Here'));
        }
    }

    // Form submission validation
    const careerForm = document.getElementById('careerApplicationForm');
    if (careerForm) {
        careerForm.addEventListener('submit', function(e) {
            if (!validateStep(4)) {
                e.preventDefault();
                return;
            }
            const submitBtn = document.getElementById('wizardSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>' + @json(__('Submitting Application...'));
        });
    }

    // ==============================================================
    // REAL-TIME SEARCH & FILTER FUNCTIONALITY FOR VACANCIES
    // ==============================================================
    const searchInput = document.getElementById('careerSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const wingFilterSelect = document.getElementById('wingFilterSelect');
    const visibleCountSpan = document.getElementById('visibleCount');
    const noVacanciesAlert = document.getElementById('noVacanciesAlert');
    const vacancies = document.querySelectorAll('.vacancy-item');

    function filterVacancies() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedWing = wingFilterSelect.value;
        let visibleCount = 0;

        // Toggle clear button
        if (query.length > 0) {
            clearSearchBtn.classList.remove('d-none');
        } else {
            clearSearchBtn.classList.add('d-none');
        }

        vacancies.forEach(card => {
            const title = card.getAttribute('data-title').toLowerCase();
            const wing = card.getAttribute('data-wing');
            const keywords = card.getAttribute('data-keywords').toLowerCase();

            const matchesQuery = query === '' || title.includes(query) || keywords.includes(query);
            const matchesWing = selectedWing === 'all' || wing === selectedWing;

            if (matchesQuery && matchesWing) {
                card.classList.remove('d-none');
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        visibleCountSpan.innerText = visibleCount;

        if (visibleCount === 0) {
            noVacanciesAlert.classList.remove('d-none');
        } else {
            noVacanciesAlert.classList.add('d-none');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterVacancies);
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterVacancies();
            searchInput.focus();
        });
    }

    if (wingFilterSelect) {
        wingFilterSelect.addEventListener('change', filterVacancies);
    }

    // Quick filter tags
    document.querySelectorAll('.quick-tag-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const kw = this.getAttribute('data-keyword');
            searchInput.value = kw;
            filterVacancies();
        });
    });

    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', resetAllSearchFilters);
    }

    function resetAllSearchFilters() {
        if (searchInput) searchInput.value = '';
        if (wingFilterSelect) wingFilterSelect.value = 'all';
        filterVacancies();
    }
</script>
@endpush
