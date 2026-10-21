@extends('layouts.public')

@section('title', 'Academic Wings & Curriculum - Excellence School System')

@section('content')
<!-- Page Banner Header -->
<section class="page-banner text-center">
    <div class="container">
        <span class="page-banner-tag"><i class="bi bi-book-half me-1"></i> Academic Excellence</span>
        <h1 class="page-banner-title">World-Class Academic Programs</h1>
        <p class="page-banner-sub">
            From sensory discovery in early years to rigorous board preparation and STEM robotics, our programs inspire critical inquiry, innovation, and ethical leadership.
        </p>
    </div>
</section>

<!-- Academic Wings Tabs / Sections -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <!-- Wing 1: Early Childhood -->
        <div class="row g-5 align-items-center mb-5 pb-5 border-bottom" id="preschool">
            <div class="col-lg-6">
                <span class="badge px-3 py-2 rounded-pill bg-warning-subtle text-warning fw-bold mb-3" style="font-size: 13px;">AGES 3 - 5 YEARS</span>
                <h2 class="fw-bold mb-3" style="color: #0F172A; font-size: 32px;">Early Childhood Development Wing</h2>
                <p class="text-muted mb-4" style="font-size: 16px; line-height: 1.8;">
                    Our kindergarten and Montessori classrooms are rich sensory environments designed around curiosity, play, and foundational motor development.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-music-note-beamed text-primary me-2"></i> Phonics & Rhymes</h6>
                            <p class="small text-muted mb-0">Early literacy, expressive speech, and joyful reading habit.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-puzzle-fill text-success me-2"></i> Motor Skills</h6>
                            <p class="small text-muted mb-0">Fine motor coordination, Montessori sensory kits, and creative arts.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/landing_hero.jpg') }}" alt="Early Childhood Wing" class="img-fluid rounded-4 shadow border" style="width: 100%; height: 350px; object-fit: cover;">
            </div>
        </div>

        <!-- Wing 2: Primary Wing -->
        <div class="row g-5 align-items-center mb-5 pb-5 border-bottom flex-lg-row-reverse" id="primary">
            <div class="col-lg-6">
                <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-3" style="font-size: 13px;">GRADES 1 - 5</span>
                <h2 class="fw-bold mb-3" style="color: #0F172A; font-size: 32px;">Primary School Innovation Wing</h2>
                <p class="text-muted mb-4" style="font-size: 16px; line-height: 1.8;">
                    Bridging foundational literacy into scientific inquiry, conceptual mathematics, bilingual communication (English & Urdu), and introductory coding.
                </p>
                <ul class="list-unstyled text-muted mb-4">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Experiential STEM activities & young coder challenges</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Mental math, daily mental exercises, and reading clubs</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> Character building, sports day, and debate society</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/stem_lab.jpg') }}" alt="Primary School STEM Lab" class="img-fluid rounded-4 shadow border" style="width: 100%; height: 350px; object-fit: cover;">
            </div>
        </div>

        <!-- Wing 3: Middle & Matriculation FBISE / Cambridge -->
        <div class="row g-5 align-items-center" id="matric">
            <div class="col-lg-6">
                <span class="badge px-3 py-2 rounded-pill bg-success-subtle text-success fw-bold mb-3" style="font-size: 13px;">GRADES 6 - 10 / MATRICULATION</span>
                <h2 class="fw-bold mb-3" style="color: #0F172A; font-size: 32px;">Senior School & Board Prep Wing</h2>
                <p class="text-muted mb-4" style="font-size: 16px; line-height: 1.8;">
                    Accredited with the Federal Board of Intermediate and Secondary Education (FBISE), our senior wing equips scholars with deep subject mastery in Physics, Chemistry, Biology, Computer Science, and Mathematics.
                </p>
                <div class="p-3 rounded-4 bg-light border mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-trophy-fill text-warning me-2"></i> 99.8% Distinction Track Record</h6>
                    <p class="small text-muted mb-0">Comprehensive test session series, monthly diagnostic reporting, and individual mentorship ensuring top FBISE positions every year.</p>
                </div>
                <a href="{{ route('admissions') }}" class="btn btn-primary px-4 py-3 fw-bold rounded-3 shadow" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                    Apply for Senior Wing <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-6">
                <div class="p-4 rounded-4 bg-dark text-white shadow-lg" style="background: #0F172A !important;">
                    <h5 class="fw-bold text-warning mb-3"><i class="bi bi-cpu-fill me-2"></i> Advanced Laboratory Facilities:</h5>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(255,255,255,0.06);">
                            <i class="bi bi-laptop fs-3 text-info"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">AI & Computer Systems Lab</h6>
                                <small class="text-light opacity-75">High-speed gigabit workstations with Python, C++, and Scratch</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(255,255,255,0.06);">
                            <i class="bi bi-radioactive fs-3 text-warning"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Physics & Chemistry Labs</h6>
                                <small class="text-light opacity-75">Full safety apparatus and high-precision analytical equipment</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(255,255,255,0.06);">
                            <i class="bi bi-book fs-3 text-success"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Digital Central Library</h6>
                                <small class="text-light opacity-75">Over 15,000 academic titles, research journals, and quiet pods</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
