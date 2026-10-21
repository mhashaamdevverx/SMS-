@extends('layouts.public')

@section('title', 'Admissions Open 2026-2027 - Excellence School System')

@section('content')
<!-- Page Banner Header -->
<section class="page-banner text-center">
    <div class="container">
        <span class="page-banner-tag"><i class="bi bi-stars text-warning me-1"></i> Admissions Open &bull; Session {{ $academicSession ?? '2026-2027' }}</span>
        <h1 class="page-banner-title">Begin Your Journey of Excellence</h1>
        <p class="page-banner-sub">
            Join a vibrant academic community where intellectual curiosity meets cutting-edge innovation. Apply online today or schedule a personal campus tour.
        </p>
    </div>
</section>

<!-- Admissions Process & Form Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <!-- Success Alert -->
        @if(session('inquiry_success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 shadow-sm mb-5" role="alert" style="border-left: 5px solid #10B981;">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Inquiry Submitted Successfully!</h5>
                        <p class="mb-0 text-muted">{{ session('inquiry_success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-5">
            <!-- Left Column: Admission Steps & Guidelines -->
            <div class="col-lg-6">
                <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-3" style="font-size: 13px;">4-STEP SIMPLE PROCESS</span>
                <h2 class="fw-bold mb-4" style="color: #0F172A; font-size: 32px;">How to Secure Your Admission</h2>

                <div class="d-flex flex-column gap-4">
                    <!-- Step 1 -->
                    <div class="d-flex gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: #4F46E5; font-size: 18px;">
                            1
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Online Inquiry or Campus Visit</h5>
                            <p class="text-muted small mb-0">Fill out our online inquiry form on the right or visit our Admissions Desk on campus to collect the prospectus.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="d-flex gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: #7C3AED; font-size: 18px;">
                            2
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Assessment & Interaction</h5>
                            <p class="text-muted small mb-0">Age-appropriate readiness evaluation for Pre-School or basic English, Urdu, and Math diagnostic test for Classes 1 to 9.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="d-flex gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: #065F46; font-size: 18px;">
                            3
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Offer & Documentation</h5>
                            <p class="text-muted small mb-0">Formal admission offer letter issued. Parents submit Birth Certificate/B-Form, previous school leaving certificate, and photos.</p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="d-flex gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: #D97706; font-size: 18px;">
                            4
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Fee Clearance & Welcome Kit</h5>
                            <p class="text-muted small mb-0">Pay the admission voucher, collect your student uniform, books, and orientation pack, and access the Academic Portal.</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-4 bg-light border mt-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-check text-primary me-2"></i> Required Documents:</h6>
                    <ul class="text-muted small mb-0 ps-3">
                        <li class="mb-1">Copy of Student's NADRA B-Form or Birth Certificate</li>
                        <li class="mb-1">Copy of Parent/Guardian CNIC</li>
                        <li class="mb-1">4 recent passport-size photographs of the student</li>
                        <li>Original School Leaving Certificate (SLC) for Class 2 onwards</li>
                    </ul>
                </div>
            </div>

            <!-- Right Column: Interactive Admission Inquiry Form -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5" style="border-top: 5px solid #4F46E5 !important;">
                    <div class="mb-4">
                        <span class="badge px-3 py-1 rounded-pill bg-success-subtle text-success fw-bold mb-2">SPRING & FALL {{ $academicSession ?? '2026-2027' }}</span>
                        <h3 class="fw-bold mb-1" style="color: #0F172A;">Online Admission Inquiry</h3>
                        <p class="text-muted small">Fill out this quick form and our admissions officer will contact you within 24 hours.</p>
                    </div>

                    <form action="{{ route('inquiry.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Parent / Guardian Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="parent_name" class="form-control py-2 rounded-3" placeholder="e.g. Tariq Mahmood" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Contact Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control py-2 rounded-3" placeholder="0300-1234567" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Email Address (Optional)</label>
                                <input type="email" name="email" class="form-control py-2 rounded-3" placeholder="parent@gmail.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Student's Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="student_name" class="form-control py-2 rounded-3" placeholder="Child's full name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Applying For Grade/Wing <span class="text-danger">*</span></label>
                                <select name="grade" class="form-select py-2 rounded-3" required>
                                    <option value="" disabled selected>Select Grade</option>
                                    <option value="Pre-School (Playgroup / KG)">Pre-School (Playgroup / KG)</option>
                                    <option value="Primary Wing (Grade 1 - 5)">Primary Wing (Grade 1 - 5)</option>
                                    <option value="Middle Wing (Grade 6 - 8)">Middle Wing (Grade 6 - 8)</option>
                                    <option value="Matriculation (Grade 9 - 10)">Matriculation (Grade 9 - 10 FBISE)</option>
                                    <option value="Cambridge O-Level">Cambridge O-Level</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Questions or Previous School Details</label>
                                <textarea name="message" rows="3" class="form-control rounded-3" placeholder="Any specific requirements, sibling details, or questions..."></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
                                    <i class="bi bi-send-fill me-2"></i> Submit Admission Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
