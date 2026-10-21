@extends('admin.layout', ['currentPage' => 'academics', 'pageTitle' => 'Add Subject'])

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
                    <i class="bi bi-book-half" style="color: #4F46E5;"></i> Curriculum &amp; Subject Registration
                </h3>
                <p class="text-muted small mb-0">Unified administration hub for registering curriculum subjects, linking class batches, and establishing exam marks.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/admin/academics" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Academics
                </a>
                <a href="/subjects" class="admin-btn admin-btn-primary admin-btn-sm fw-bold">
                    <i class="bi bi-collection me-1"></i> View All Subjects
                </a>
                <a href="/add-class" class="admin-btn admin-btn-info text-white admin-btn-sm fw-bold" style="background: #06B6D4; border-color: #06B6D4;">
                    <i class="bi bi-easel2-fill me-1"></i> Add Class
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
                            <i class="bi bi-journal-plus"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0 fs-5">Add New Subject</h4>
                            <p class="text-muted small mb-0">Assign title, select target class &amp; section stream, and set evaluation maximum score.</p>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        Curriculum Setup
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

                <form action="{{ route('subjects.store') }}" method="POST" id="addSubjectForm">
                    @csrf
                    <div class="row g-4">
                        {{-- Field 1: Class & Section --}}
                        <div class="col-12">
                            <label for="class-id" class="form-label fw-bold text-dark small mb-1">
                                Class &amp; Section <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-mortarboard-fill"></i></span>
                                <select id="class-id" name="class_id" class="form-select border-start-0 @error('class_id') is-invalid @enderror" style="height: 46px;" required autofocus>
                                    <option value="" disabled {{ old('class_id', $selectedClassId) ? '' : 'selected' }}>Select target class and active section stream...</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected((string) old('class_id', $selectedClassId) === (string) $class->id)>
                                            {{ $class->class_name }} ({{ $class->section_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('class_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if($classes->isEmpty())
                                <div class="text-danger small mt-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                    <span>No classes found in the system. <a href="/add-class" class="fw-bold text-decoration-underline text-danger">Click here to add a class</a> first.</span>
                                </div>
                            @else
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Choose the class grade and section stream that this subject belongs to.</small>
                            @endif
                        </div>

                        {{-- Field 2: Subject Name --}}
                        <div class="col-md-7">
                            <label for="subject-name" class="form-label fw-bold text-dark small mb-1">
                                Subject Title <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-indigo" style="color: #4F46E5;"><i class="bi bi-book-half"></i></span>
                                <input id="subject-name" type="text" name="subject_name" placeholder="e.g. Mathematics, English Literature, Science, Urdu" value="{{ old('subject_name') }}" maxlength="255" class="form-control border-start-0 @error('subject_name') is-invalid @enderror" style="height: 46px;" required>
                            </div>
                            @error('subject_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Official course or academic subject name.</small>
                        </div>

                        {{-- Field 3: Total Marks --}}
                        <div class="col-md-5">
                            <label for="total-marks" class="form-label fw-bold text-dark small mb-1">
                                Total Marks <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-success"><i class="bi bi-award-fill"></i></span>
                                <input id="total-marks" type="number" name="total_marks" min="1" max="100000" placeholder="e.g. 100" value="{{ old('total_marks', 100) }}" class="form-control border-start-0 @error('total_marks') is-invalid @enderror" style="height: 46px;" required>
                            </div>
                            @error('total_marks')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Maximum examination score quota (default: 100).</small>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-12 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                            <button type="submit" class="admin-btn admin-btn-primary px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
                                <i class="bi bi-check-circle-fill me-1"></i> Save Subject
                            </button>
                            <a href="{{ route('subjects.list', $selectedClassId ? ['class_id' => $selectedClassId] : []) }}" class="admin-btn admin-btn-outline px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
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
                    Every subject saved here is immediately registered in the database and automatically populated in all dropdown selectors across all panels:
                </p>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Teacher Subject Assignments &amp; Course Mapping</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Exam Results Entry &amp; Student Marksheets</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Examination Roll Number Slips &amp; Date Sheets</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Student Portal Course Syllabus &amp; Progress View</span>
                    </li>
                </ul>
            </div>

            {{-- Widget 2: Standard Marks Reference Guide --}}
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 bg-white" style="border-top: 4px solid #06B6D4 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-info-circle text-info me-1"></i> Marks Scheme Guide
                    </h6>
                    <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                        Standard Marks
                    </span>
                </div>
                <p class="text-muted small mb-3">Recommended mark structures based on academic accreditation standards:</p>
                <div class="d-flex flex-column gap-2" style="font-size: 12px;">
                    <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-semibold">Core Theory (Maths, Eng, Sci)</span>
                        <span class="badge bg-primary text-white fw-bold">100 Marks</span>
                    </div>
                    <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-semibold">Theory + Practical (Computers)</span>
                        <span class="badge bg-info text-white fw-bold">75 / 25 Marks</span>
                    </div>
                    <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-semibold">Subsidiary / Language Elective</span>
                        <span class="badge bg-secondary text-white fw-bold">50 Marks</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection