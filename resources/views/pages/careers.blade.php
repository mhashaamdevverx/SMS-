@extends('layouts.public')

@section('title', 'Careers & Faculty Openings - Excellence School System')

@section('content')
<!-- Page Banner Header -->
<section class="page-banner text-center">
    <div class="container">
        <span class="page-banner-tag"><i class="bi bi-briefcase-fill me-1"></i> Join Our Team</span>
        <h1 class="page-banner-title">Build the Future of Education With Us</h1>
        <p class="page-banner-sub">
            Excellence School System is seeking passionate educators, curriculum innovators, and administrative leaders who believe in transforming student lives.
        </p>
    </div>
</section>

<!-- Careers Content Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <!-- Success Alert -->
        @if(session('career_success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 shadow-sm mb-5" role="alert" style="border-left: 5px solid #10B981;">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Application Submitted Successfully!</h5>
                        <p class="mb-0 text-muted">{{ session('career_success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-5">
            <!-- Open Positions List -->
            <div class="col-lg-7">
                <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-3" style="font-size: 13px;">CURRENT OPENINGS</span>
                <h2 class="fw-bold mb-4" style="color: #0F172A; font-size: 32px;">Available Faculty & Staff Positions</h2>

                <div class="d-flex flex-column gap-3">
                    <!-- Job 1 -->
                    <div class="card border rounded-4 p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-primary-subtle text-primary fw-bold mb-1">SENIOR WING</span>
                                <h5 class="fw-bold text-dark mb-0">Senior Secondary Physics & Math Lecturer (FBISE)</h5>
                            </div>
                            <span class="badge bg-light text-dark border">Full Time</span>
                        </div>
                        <p class="text-muted small mb-3">Requirements: M.Sc / M.Phil in Physics/Math with 3+ years experience teaching Matric/FBISE board classes with proven distinction results.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-geo-alt me-1"></i> Islamabad Main Campus</span>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" onclick="selectJobPosition('Senior Physics & Math Lecturer')">
                                Apply for this Role <i class="bi bi-arrow-right-short"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Job 2 -->
                    <div class="card border rounded-4 p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-success-subtle text-success fw-bold mb-1">STEM INNOVATION</span>
                                <h5 class="fw-bold text-dark mb-0">Robotics & Computer Science Instructor</h5>
                            </div>
                            <span class="badge bg-light text-dark border">Full Time</span>
                        </div>
                        <p class="text-muted small mb-3">Requirements: BS Computer Science / Software Engineering with proficiency in Python, Arduino, Scratch, and 3D printing instruction.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-geo-alt me-1"></i> Innovation Hub, Islamabad</span>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" onclick="selectJobPosition('Robotics & Computer Science Instructor')">
                                Apply for this Role <i class="bi bi-arrow-right-short"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Job 3 -->
                    <div class="card border rounded-4 p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-warning-subtle text-warning fw-bold mb-1">PRIMARY WING</span>
                                <h5 class="fw-bold text-dark mb-0">Montessori Directress & Primary English Homeroom</h5>
                            </div>
                            <span class="badge bg-light text-dark border">Full Time</span>
                        </div>
                        <p class="text-muted small mb-3">Requirements: Certified Montessori diploma with bachelor's degree and native-level English phonics and creative writing teaching experience.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-geo-alt me-1"></i> Junior Wing, Islamabad</span>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" onclick="selectJobPosition('Montessori & Primary Homeroom')">
                                Apply for this Role <i class="bi bi-arrow-right-short"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Form -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5" style="border-top: 5px solid #7C3AED !important;">
                    <h3 class="fw-bold mb-1 text-dark">Submit Application</h3>
                    <p class="text-muted small mb-4">Complete your candidate profile to apply for active or future openings.</p>

                    <form action="{{ route('career.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_name" class="form-control py-2 rounded-3" placeholder="e.g. Dr. Salman Khan" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-dark">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control py-2 rounded-3" placeholder="name@domain.com" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-dark">Phone <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control py-2 rounded-3" placeholder="03xx-xxxxxxx" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Target Position <span class="text-danger">*</span></label>
                            <input type="text" name="position" id="jobPositionInput" class="form-control py-2 rounded-3" placeholder="e.g. Physics Lecturer" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Years of Experience <span class="text-danger">*</span></label>
                            <select name="experience" class="form-select py-2 rounded-3" required>
                                <option value="" disabled selected>Select Experience</option>
                                <option value="Fresh Graduate (0-1 yrs)">Fresh Graduate (0 - 1 years)</option>
                                <option value="1-3 years">1 - 3 years</option>
                                <option value="3-5 years">3 - 5 years</option>
                                <option value="5-10 years">5 - 10 years</option>
                                <option value="10+ years">10+ years</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-dark">Cover Note / Summary</label>
                            <textarea name="cover_note" rows="3" class="form-control rounded-3" placeholder="Briefly highlight your qualifications, achievements, and teaching philosophy..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow" style="background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%);">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i> Submit Career Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function selectJobPosition(title) {
        const input = document.getElementById('jobPositionInput');
        if (input) {
            input.value = title;
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
</script>
@endpush
