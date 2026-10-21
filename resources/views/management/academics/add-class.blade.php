@extends('admin.layout', ['currentPage' => 'academics', 'pageTitle' => 'Add Class'])

@section('content')
<div class="academic-operations-hub">
    {{-- TOP COMMAND BAR & HEADER (EXACT FIRST IMAGE AESTHETIC) --}}
    <div class="admin-card mb-4 border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
        <div class="admin-card-header d-flex flex-wrap align-items-center justify-content-between p-4 border-bottom" style="gap: 16px;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 11.5px;">
                        <i class="bi bi-mortarboard-fill me-1"></i> Academic Operations
                    </span>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 rounded-pill" style="font-size: 11.5px;">
                        Session {{ now()->format('Y') }} Active
                    </span>
                </div>
                <h3 class="admin-card-title mb-1 fw-bold fs-4 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-journal-bookmark-fill" style="color: #4F46E5;"></i> Academic Operations & Curriculum Center
                </h3>
                <p class="text-muted small mb-0">Unified administration hub for classes, sections, curricula, exams, roll number slips, and seating allocations.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/admin/academics" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Academics
                </a>
                <a href="/classes" class="admin-btn admin-btn-primary admin-btn-sm fw-bold">
                    <i class="bi bi-easel2-fill me-1"></i> View All Classes
                </a>
                <a href="/add-section" class="admin-btn admin-btn-success admin-btn-sm fw-bold">
                    <i class="bi bi-grid-plus me-1"></i> Add Section
                </a>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT AREA (FULL-WIDTH, HIGH-END RESPONSIVE GRID) --}}
    <div class="row g-4 mb-4">
        {{-- FORM CONTAINER (LEFT COLUMN: 8 COLS) --}}
        <div class="col-lg-8">
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white" style="border-top: 4px solid #4F46E5 !important;">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: #EEF2FF; color: #4F46E5; display: grid; place-items: center; font-size: 24px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                            <i class="bi bi-easel2-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0 fs-5">Add New Class Batch</h4>
                            <p class="text-muted small mb-0">Create standard grade level, set student chair quota, and assign an active section.</p>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        Room #{{ $nextClassNo }}
                    </span>
                </div>

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #FEF2F2; border-left: 4px solid #EF4444 !important;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                            <strong class="text-danger small">Please correct the following:</strong>
                        </div>
                        <ul class="mb-0 small text-danger ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('classes.store') }}" method="POST" id="addClassForm">
                    @csrf
                    <div class="row g-4">
                        {{-- Field 1: Class No --}}
                        <div class="col-md-6">
                            <label for="class-no" class="form-label fw-bold text-dark small mb-1">Class Room No</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-hash"></i></span>
                                <input id="class-no" type="number" value="{{ $nextClassNo }}" readonly class="form-control bg-light border-start-0 fw-bold text-dark" style="cursor: not-allowed; height: 44px;">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Auto-incremented sequence for exam hall tracking.</small>
                        </div>

                        {{-- Field 2: Class Name --}}
                        <div class="col-md-6">
                            <label for="class-name" class="form-label fw-bold text-dark small mb-1">
                                Class Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-easel"></i></span>
                                <input id="class-name" type="text" name="class_name" list="class-options" placeholder="Select or type (e.g. ONE, Class 10)" value="{{ old('class_name') }}" maxlength="50" class="form-control border-start-0 @error('class_name') is-invalid @enderror" style="height: 44px;" required autofocus>
                            </div>
                            <datalist id="class-options">
                                @foreach($classOptions as $classOption)
                                    <option value="{{ $classOption }}"></option>
                                @endforeach
                            </datalist>
                            @error('class_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Preset or custom grade name (e.g., Pre-KG, Class 1, Grade 10).</small>
                        </div>

                        {{-- Field 3: Chairs --}}
                        <div class="col-md-6">
                            <label for="chairs" class="form-label fw-bold text-dark small mb-1">
                                Desk / Chairs Capacity <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-success"><i class="bi bi-chair"></i></span>
                                <input id="chairs" type="number" name="chairs" min="1" max="10000" placeholder="e.g. 35" value="{{ old('chairs', 30) }}" class="form-control border-start-0 @error('chairs') is-invalid @enderror" style="height: 44px;" required>
                            </div>
                            @error('chairs')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Maximum seating quota for student admissions & exam halls.</small>
                        </div>

                        {{-- Field 4: Section Name --}}
                        <div class="col-md-6">
                            <label for="section-name" class="form-label fw-bold text-dark small mb-1">
                                Section Stream <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-info"><i class="bi bi-grid-3x3-gap"></i></span>
                                <select id="section-name" name="section_name" class="form-select border-start-0 @error('section_name') is-invalid @enderror" style="height: 44px;" required>
                                    <option value="" disabled {{ old('section_name') ? '' : 'selected' }}>Select an active section...</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section }}" @selected(old('section_name') === $section)>Section {{ $section }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('section_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if($sections->isEmpty())
                                <small class="text-danger d-block mt-1" style="font-size: 11.5px;">No sections found. Please add a section first.</small>
                            @else
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Assign standard branch stream (Blue, Green, Red, etc.).</small>
                            @endif
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="col-12 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                            <button type="submit" class="admin-btn admin-btn-primary px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
                                <i class="bi bi-check-circle-fill me-1"></i> Save Class Batch
                            </button>
                            <a href="/admin/academics" class="admin-btn admin-btn-outline px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT SIDEBAR WIDGETS (RIGHT COLUMN: 4 COLS) --}}
        <div class="col-lg-4">
            {{-- Widget 1: System Integration Notice --}}
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" style="border-top: 4px solid #10B981 !important;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #10B981; display: grid; place-items: center; font-size: 18px;">
                        <i class="bi bi-database-check"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Automatic System Reflection</h6>
                        <small class="text-muted">Instant Database Synchronization</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.6;">
                    Every class added here is directly recorded in the MySQL database and immediately reflected across all admin & portal dropdowns:
                </p>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Student Directory & Admission Class Selectors</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Teacher Assignment & Subject Allocations</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Fee Structure Vouchers & Billing Plans</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Roles & Perms (Class In-Charge Designation)</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Roll Number Slips & Exam Seating Plans</span>
                    </li>
                </ul>
            </div>

            {{-- Widget 2: Currently Configured Class Badges --}}
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 bg-white" style="border-top: 4px solid #06B6D4 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-collection-fill text-info me-1"></i> Active Classes
                    </h6>
                    <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                        {{ count($classOptions) }} Grades
                    </span>
                </div>
                <p class="text-muted small mb-3">Registered class grades in the system:</p>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($classOptions as $opt)
                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px; font-weight: 600;">
                            {{ $opt }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection