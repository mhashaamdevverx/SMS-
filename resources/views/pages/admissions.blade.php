@extends('layouts.public')

@section('title', 'Admissions Open 2026-2027 - Excellence School System')

@section('content')
<!-- Page Banner Header (Compact so form is immediately in view) -->
<section class="page-banner text-center" style="padding: 120px 0 35px !important;">
    <div class="container">
        <span class="page-banner-tag mb-2"><i class="bi bi-stars text-warning me-1"></i> Admissions Open &bull; Session {{ $academicSession ?? '2026-2027' }}</span>
        <h1 class="page-banner-title mb-1" style="font-size: clamp(1.8rem, 3vw, 2.3rem);">Begin Your Journey of Excellence</h1>
        <p class="page-banner-sub mb-0" style="max-width: 650px; font-size: 14px; margin: 0 auto; opacity: 0.85;">
            Join a vibrant academic community where intellectual curiosity meets cutting-edge innovation.
        </p>
    </div>
</section>

<!-- SECTION 1: WIDE OFFICIAL ADMISSION APPLICATION FORM (ON TOP) -->
<section class="py-4 bg-light-subtle">
    <div class="container-xxl px-3 px-md-4">
        <!-- Success Alert -->
        @if(session('inquiry_success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 shadow-sm mb-4 border-0" role="alert" style="background: #ECFDF5; border-left: 6px solid #10B981 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: #10B981; flex-shrink: 0;">
                        <i class="bi bi-check-lg fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Application Submitted Successfully!</h5>
                        <p class="mb-0 text-muted small">{{ session('inquiry_success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Validation Errors Display --}}
        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger rounded-3 p-3 mb-4 shadow-sm border-0" role="alert" style="background: #FEF2F2; border-left: 5px solid #EF4444 !important;">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger flex-shrink-0 mt-1"></i>
                    <div>
                        <strong class="d-block mb-1 text-danger">Please correct the following errors:</strong>
                        <ul class="mb-0 ps-3 small text-danger">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- WIDE HORIZONTAL FORM CARD -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-4 bg-white" style="border-top: 6px solid #4F46E5 !important;">
            <!-- Header Row -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 mb-1 px-3 py-1 rounded-pill bg-success-subtle text-success fw-bold" style="font-size: 11.5px;">
                        <i class="bi bi-check-circle-fill"></i> ADMISSIONS {{ $academicSession ?? '2026-2027' }} ACTIVE &bull; ALL WINGS OPEN
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 22px; letter-spacing: -0.02em;">Online Admission Application</h2>
                    <p class="text-muted small mb-0">Fill out this official form to initiate student registration and credential verification.</p>
                </div>
                <div class="d-none d-md-flex align-items-center gap-2 text-muted small bg-light px-3 py-2 rounded-3 border">
                    <i class="bi bi-patch-check-fill text-primary fs-5"></i>
                    <span><strong>Official Admissions</strong> &bull; Fast-Track Processing</span>
                </div>
            </div>

            <form action="{{ route('inquiry.submit') }}" method="POST" id="admissionInquiryForm" novalidate>
                @csrf
                <div class="row g-3">

                    <!-- ============================================================== -->
                    <!-- ROW 1: PRIMARY IDENTIFICATION & 13-DIGIT CREDENTIALS (4 COLS) -->
                    <!-- ============================================================== -->
                    <!-- 1. Student Full Name -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Student Full Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-fill"></i></span>
                            <input type="text" 
                                   name="student_name" 
                                   class="form-control border-start-0 py-2 rounded-end-3 @error('student_name') is-invalid @enderror" 
                                   placeholder="e.g. Muhammad Rayan" 
                                   value="{{ old('student_name') }}" 
                                   required>
                        </div>
                    </div>

                    <!-- 2. Student B-Form / Smart Card No (13 Digits) -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1 d-flex justify-content-between">
                            <span>Student B-Form No <span class="text-danger">*</span></span>
                            <span class="text-muted" style="font-size: 11px;" id="bformDigitsCount">13 Digits</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-card-text"></i></span>
                            <input type="text" 
                                   name="student_bform" 
                                   id="student_bform"
                                   class="form-control font-monospace border-start-0 py-2 rounded-end-3 @error('clean_bform') is-invalid @enderror" 
                                   placeholder="XXXXX-XXXXXXX-X" 
                                   maxlength="15" 
                                   value="{{ old('student_bform') }}" 
                                   required>
                        </div>
                    </div>

                    <!-- 3. Parent / Guardian Full Name -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Parent / Guardian Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-badge-fill"></i></span>
                            <input type="text" 
                                   name="guardian_name" 
                                   class="form-control border-start-0 py-2 rounded-end-3 @error('guardian_name') is-invalid @enderror" 
                                   placeholder="e.g. Tariq Mahmood" 
                                   value="{{ old('guardian_name', old('parent_name')) }}" 
                                   required>
                        </div>
                    </div>

                    <!-- 4. Guardian CNIC No (13 Digits) -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1 d-flex justify-content-between">
                            <span>Guardian CNIC No <span class="text-danger">*</span></span>
                            <span class="text-muted" style="font-size: 11px;" id="cnicDigitsCount">13 Digits</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-vcard"></i></span>
                            <input type="text" 
                                   name="guardian_cnic" 
                                   id="guardian_cnic"
                                   class="form-control font-monospace border-start-0 py-2 rounded-end-3 @error('clean_cnic') is-invalid @enderror" 
                                   placeholder="XXXXX-XXXXXXX-X" 
                                   maxlength="15" 
                                   value="{{ old('guardian_cnic') }}" 
                                   required>
                        </div>
                    </div>

                    <!-- ============================================================== -->
                    <!-- ROW 2: CONTACT & ACADEMIC ENROLLMENT (4 COLS)                  -->
                    <!-- ============================================================== -->
                    <!-- 5. Guardian Phone Number -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Contact Phone Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone-fill"></i></span>
                            <input type="tel" 
                                   name="guardian_phone" 
                                   class="form-control border-start-0 py-2 rounded-end-3 @error('guardian_phone') is-invalid @enderror" 
                                   placeholder="0300-1234567" 
                                   value="{{ old('guardian_phone', old('phone')) }}" 
                                   required>
                        </div>
                    </div>

                    <!-- 6. Guardian Email Address -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">Email Address (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" 
                                   name="guardian_email" 
                                   class="form-control border-start-0 py-2 rounded-end-3 @error('guardian_email') is-invalid @enderror" 
                                   placeholder="parent@gmail.com" 
                                   value="{{ old('guardian_email', old('email')) }}">
                        </div>
                    </div>

                    <!-- 7. Grade / Wing Applying For -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Applying Grade / Wing <span class="text-danger">*</span>
                        </label>
                        <select name="grade_applied" class="form-select py-2 rounded-3 @error('grade_applied') is-invalid @enderror" required>
                            <option value="" disabled {{ old('grade_applied', old('grade')) ? '' : 'selected' }}>Select Applying Grade</option>
                            <optgroup label="Early Childhood Wing">
                                <option value="Pre-School (Playgroup / KG)" {{ old('grade_applied', old('grade')) === 'Pre-School (Playgroup / KG)' ? 'selected' : '' }}>Playgroup / Pre-School</option>
                                <option value="Nursery" {{ old('grade_applied', old('grade')) === 'Nursery' ? 'selected' : '' }}>Nursery</option>
                                <option value="Kindergarten (KG)" {{ old('grade_applied', old('grade')) === 'Kindergarten (KG)' ? 'selected' : '' }}>Kindergarten (KG)</option>
                            </optgroup>
                            <optgroup label="Primary School Wing">
                                <option value="Grade 1" {{ old('grade_applied', old('grade')) === 'Grade 1' ? 'selected' : '' }}>Grade 1</option>
                                <option value="Grade 2" {{ old('grade_applied', old('grade')) === 'Grade 2' ? 'selected' : '' }}>Grade 2</option>
                                <option value="Grade 3" {{ old('grade_applied', old('grade')) === 'Grade 3' ? 'selected' : '' }}>Grade 3</option>
                                <option value="Grade 4" {{ old('grade_applied', old('grade')) === 'Grade 4' ? 'selected' : '' }}>Grade 4</option>
                                <option value="Grade 5" {{ old('grade_applied', old('grade')) === 'Grade 5' ? 'selected' : '' }}>Grade 5</option>
                            </optgroup>
                            <optgroup label="Middle & Secondary Wing">
                                <option value="Grade 6" {{ old('grade_applied', old('grade')) === 'Grade 6' ? 'selected' : '' }}>Grade 6</option>
                                <option value="Grade 7" {{ old('grade_applied', old('grade')) === 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                <option value="Grade 8" {{ old('grade_applied', old('grade')) === 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                <option value="Matriculation (Grade 9)" {{ old('grade_applied', old('grade')) === 'Matriculation (Grade 9)' ? 'selected' : '' }}>Matriculation (Grade 9 FBISE)</option>
                                <option value="Matriculation (Grade 10)" {{ old('grade_applied', old('grade')) === 'Matriculation (Grade 10)' ? 'selected' : '' }}>Matriculation (Grade 10 FBISE)</option>
                                <option value="Cambridge O-Level" {{ old('grade_applied', old('grade')) === 'Cambridge O-Level' ? 'selected' : '' }}>Cambridge O-Level</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- 8. Last Class Passed -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Last Class / Grade Passed
                        </label>
                        <select name="previous_class" class="form-select py-2 rounded-3">
                            <option value="" disabled {{ old('previous_class') ? '' : 'selected' }}>Select Passed Class</option>
                            <option value="Fresh Admission (No Prior School)" {{ old('previous_class') === 'Fresh Admission (No Prior School)' ? 'selected' : '' }}>Fresh Admission (No Prior School)</option>
                            <option value="Playgroup / Nursery" {{ old('previous_class') === 'Playgroup / Nursery' ? 'selected' : '' }}>Playgroup / Nursery</option>
                            <option value="Kindergarten (KG)" {{ old('previous_class') === 'Kindergarten (KG)' ? 'selected' : '' }}>Kindergarten (KG)</option>
                            <option value="Class 1" {{ old('previous_class') === 'Class 1' ? 'selected' : '' }}>Class 1</option>
                            <option value="Class 2" {{ old('previous_class') === 'Class 2' ? 'selected' : '' }}>Class 2</option>
                            <option value="Class 3" {{ old('previous_class') === 'Class 3' ? 'selected' : '' }}>Class 3</option>
                            <option value="Class 4" {{ old('previous_class') === 'Class 4' ? 'selected' : '' }}>Class 4</option>
                            <option value="Class 5" {{ old('previous_class') === 'Class 5' ? 'selected' : '' }}>Class 5</option>
                            <option value="Class 6" {{ old('previous_class') === 'Class 6' ? 'selected' : '' }}>Class 6</option>
                            <option value="Class 7" {{ old('previous_class') === 'Class 7' ? 'selected' : '' }}>Class 7</option>
                            <option value="Class 8" {{ old('previous_class') === 'Class 8' ? 'selected' : '' }}>Class 8</option>
                            <option value="Class 9" {{ old('previous_class') === 'Class 9' ? 'selected' : '' }}>Class 9</option>
                        </select>
                    </div>

                    <!-- ============================================================== -->
                    <!-- ROW 3: PRIOR ACADEMIC BACKGROUND & INQUIRIES (3 COLS)          -->
                    <!-- ============================================================== -->
                    <!-- 9. Previous School Name -->
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">Previous School / Institute Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-building"></i></span>
                            <input type="text" 
                                   name="previous_school" 
                                   class="form-control border-start-0 py-2 rounded-end-3" 
                                   placeholder="e.g. Army Public School / Beaconhouse" 
                                   value="{{ old('previous_school') }}">
                        </div>
                    </div>

                    <!-- 10. Previous Percentage / Grade -->
                    <div class="col-xl-2 col-lg-2 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">Previous Marks</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-award-fill"></i></span>
                            <input type="text" 
                                   name="previous_marks" 
                                   class="form-control border-start-0 py-2 rounded-end-3" 
                                   placeholder="e.g. 88%" 
                                   value="{{ old('previous_marks') }}">
                        </div>
                    </div>

                    <!-- 11. Student Gender Dropdown (Male, Female, Other) -->
                    <div class="col-xl-2 col-lg-2 col-md-6">
                        <label class="form-label fw-bold small text-dark mb-1">
                            Gender <span class="text-danger">*</span>
                        </label>
                        <select name="gender" class="form-select py-2 rounded-3 @error('gender') is-invalid @enderror" required>
                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- 12. Additional Questions or Notes -->
                    <div class="col-xl-4 col-lg-4 col-md-12">
                        <label class="form-label fw-bold small text-dark mb-1">Additional Questions / Notes (Optional)</label>
                        <textarea name="message" 
                                  rows="1" 
                                  class="form-control rounded-3 py-2" 
                                  style="resize: none;" 
                                  placeholder="Sibling details, scholarship queries, transport request...">{{ old('message') }}</textarea>
                    </div>

                    <!-- ============================================================== -->
                    <!-- ROW 4: SUBMIT ACTION BAR                                       -->
                    <!-- ============================================================== -->
                    <div class="col-12 mt-3 pt-2 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="small text-muted d-flex align-items-center gap-2">
                                <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                                <span>SSL 256-Bit Encrypted &bull; Application tracking number generated instantly</span>
                            </div>
                            <button type="submit" id="submitAppBtn" class="btn btn-primary px-5 py-2 fw-bold rounded-3 shadow-sm" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); font-size: 15px; min-width: 280px;">
                                <i class="bi bi-send-fill me-2"></i> Submit Official Application
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>

<!-- SECTION 2: ADMISSION DETAILS & STEP GUIDELINES (BELOW THE FORM) -->
<section class="py-5" style="background: #F8FAFC; border-top: 1px solid #E2E8F0;">
    <div class="container py-4">
        <div class="text-center mb-5 max-w-700 mx-auto">
            <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-2" style="font-size: 12.5px;">
                <i class="bi bi-check2-all me-1"></i> 4-STEP SIMPLE PROCESS
            </span>
            <h2 class="fw-bold mb-2 text-dark" style="font-size: 32px; letter-spacing: -0.02em;">How to Secure Your Admission</h2>
            <p class="text-muted small mb-0">Follow our transparent, merit-driven evaluation pathway from application submission to welcome orientation.</p>
        </div>

        {{-- 4 Step Cards In Horizontal Grid --}}
        <div class="row g-4 mb-5">
            <!-- Step 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white" style="border-top: 4px solid #4F46E5 !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm mb-3" style="width: 44px; height: 44px; background: #4F46E5; font-size: 18px;">
                        1
                    </div>
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 16px;">Online Application</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Complete the registration form above with accurate student credentials and parent contact info.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white" style="border-top: 4px solid #F59E0B !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm mb-3" style="width: 44px; height: 44px; background: #F59E0B; font-size: 18px;">
                        2
                    </div>
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 16px;">Assessment & Interview</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Admissions desk contacts you within 24 hours to schedule student screening and campus interaction.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white" style="border-top: 4px solid #10B981 !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm mb-3" style="width: 44px; height: 44px; background: #10B981; font-size: 18px;">
                        3
                    </div>
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 16px;">Offer & Verification</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Merit evaluation is completed and an official provisional admission letter is dispatched via email/portal.
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white" style="border-top: 4px solid #0EA5E9 !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm mb-3" style="width: 44px; height: 44px; background: #0EA5E9; font-size: 18px;">
                        4
                    </div>
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 16px;">Enrollment & Welcome</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Fee challan settlement, uniform/books pack collection, and joining the student portal orientation.
                    </p>
                </div>
            </div>
        </div>

        {{-- ACADEMIC WINGS SPOTLIGHT BANNER --}}
        <div class="card border-0 rounded-4 p-4 p-md-5 text-white shadow-lg overflow-hidden position-relative" style="background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%);">
            <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
                <div class="col-lg-8">
                    <span class="badge px-3 py-2 rounded-pill mb-2" style="background: rgba(79, 70, 229, 0.3); color: #C7D2FE; border: 1px solid rgba(199, 210, 254, 0.2); font-size: 11.5px;">
                        <i class="bi bi-mortarboard-fill me-1 text-warning"></i> NATIONALLY ACCREDITED & CAMBRIDGE AFFILIATED
                    </span>
                    <h3 class="fw-bold mb-2 text-white" style="font-size: 26px; color: #FFFFFF !important; letter-spacing: -0.01em;">Exceptional Infrastructure for All Stages of Growth</h3>
                    <p class="text-light opacity-75 mb-0 small" style="max-width: 650px; line-height: 1.6;">
                        From child-centric Montessori spaces to world-class robotics laboratories, high-speed fiber campus network, and dedicated sports complexes, Excellence School System guarantees the highest standard of holistic education.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('contact') }}" class="btn btn-warning px-4 py-3 fw-bold rounded-3 shadow">
                        <i class="bi bi-geo-alt-fill me-1"></i> Visit Campus / Book Tour
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 13-digit mask formatter: XXXXX-XXXXXXX-X
    function applyCnicMask(inputElement, counterElement) {
        if (!inputElement) return;

        inputElement.addEventListener('input', function (e) {
            let val = inputElement.value.replace(/[^0-9]/g, '');
            if (val.length > 13) val = val.substring(0, 13);

            let formatted = '';
            if (val.length > 0) {
                formatted = val.substring(0, 5);
                if (val.length > 5) {
                    formatted += '-' + val.substring(5, 12);
                    if (val.length > 12) {
                        formatted += '-' + val.substring(12, 13);
                    }
                }
            }
            inputElement.value = formatted;

            if (counterElement) {
                if (val.length === 13) {
                    counterElement.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check2-circle"></i> 13 Digits (Valid)</span>';
                    inputElement.classList.remove('is-invalid');
                    inputElement.classList.add('is-valid');
                } else {
                    counterElement.innerHTML = `<span class="${val.length > 0 ? 'text-warning' : 'text-muted'}">${val.length}/13 Digits</span>`;
                    inputElement.classList.remove('is-valid');
                }
            }
        });
    }

    const bformInput = document.getElementById('student_bform');
    const bformCounter = document.getElementById('bformDigitsCount');
    applyCnicMask(bformInput, bformCounter);

    const cnicInput = document.getElementById('guardian_cnic');
    const cnicCounter = document.getElementById('cnicDigitsCount');
    applyCnicMask(cnicInput, cnicCounter);

    // Initial check on load (for old values)
    if (bformInput && bformInput.value) {
        bformInput.dispatchEvent(new Event('input'));
    }
    if (cnicInput && cnicInput.value) {
        cnicInput.dispatchEvent(new Event('input'));
    }
});
</script>
@endpush
