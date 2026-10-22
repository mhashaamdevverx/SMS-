{{-- ADMIN PORTAL: CAREERS & FACULTY APPLICATIONS MANAGEMENT --}}
<div class="admin-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge px-3 py-1 rounded-pill" style="background: rgba(14, 165, 233, 0.12); color: #0284C7; font-weight: 800; font-size: 11px; letter-spacing: 0.05em;">
                    <i class="bi bi-briefcase-fill me-1"></i> TALENT & HR RECRUITMENT DESK
                </span>
                <span class="text-muted small">&bull; Academic Session {{ $academicSession ?? '2026-2027' }}</span>
            </div>
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Careers & Faculty Applications</h2>
            <p class="text-muted mb-0 small">Review incoming job applications across all 4 candidate verification processes, inspect NADRA CNIC credentials, relative disclosures, and manage hiring decisions.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('careers') }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3 fw-bold shadow-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Public Openings
            </a>
            <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-bold shadow-sm" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>
</div>

{{-- METRIC SUMMARY CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #FFFFFF; border-left: 4px solid #4F46E5 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Total Applicants</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalCareersCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">All candidate profiles received</small>
                </div>
                <div class="rounded-3 p-3 text-primary" style="background: rgba(79, 70, 229, 0.1); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #FFFFFF; border-left: 4px solid #F59E0B !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Pending Review</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($pendingCareersCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">Awaiting initial screening</small>
                </div>
                <div class="rounded-3 p-3 text-warning" style="background: rgba(245, 158, 11, 0.12); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #FFFFFF; border-left: 4px solid #0EA5E9 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Shortlisted</span>
                    <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($shortlistedCareersCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">Interview / demo scheduled</small>
                </div>
                <div class="rounded-3 p-3 text-info" style="background: rgba(14, 165, 233, 0.12); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-star-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #FFFFFF; border-left: 4px solid #10B981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Hired / Selected</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($hiredCareersCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">Joined faculty & staff</small>
                </div>
                <div class="rounded-3 p-3 text-success" style="background: rgba(16, 185, 129, 0.12); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH & FILTER TOOLBAR --}}
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="{{ route('admin.page', 'careers') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search Candidate, CNIC, Phone, App #, Degree, Role..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select bg-light">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                <option value="interviewed" {{ request('status') === 'interviewed' ? 'selected' : '' }}>Interviewed</option>
                <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="wing" class="form-select bg-light">
                <option value="">All Wings</option>
                <option value="SENIOR WING" {{ request('wing') === 'SENIOR WING' ? 'selected' : '' }}>Senior Wing</option>
                <option value="STEM INNOVATION" {{ request('wing') === 'STEM INNOVATION' ? 'selected' : '' }}>STEM Innovation</option>
                <option value="PRIMARY WING" {{ request('wing') === 'PRIMARY WING' ? 'selected' : '' }}>Primary Wing</option>
                <option value="MIDDLE WING" {{ request('wing') === 'MIDDLE WING' ? 'selected' : '' }}>Middle Wing</option>
                <option value="ADMINISTRATION" {{ request('wing') === 'ADMINISTRATION' ? 'selected' : '' }}>Administration</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="has_relative" class="form-select bg-light">
                <option value="">Relative Disclosure</option>
                <option value="yes" {{ request('has_relative') === 'yes' ? 'selected' : '' }}>Has Relative (Yes)</option>
                <option value="no" {{ request('has_relative') === 'no' ? 'selected' : '' }}>No Relative (No)</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-bold">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'wing', 'has_relative']))
                <a href="{{ route('admin.page', 'careers') }}" class="btn btn-outline-secondary px-3" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- APPLICATIONS DATA TABLE --}}
<div class="admin-card">
    <div class="admin-table-wrapper" style="overflow-x: hidden;">
        <table class="admin-table" style="width: 100%; table-layout: fixed;">
            <thead>
                <tr>
                    <th class="text-center" style="width: 24%;">Candidate & App #</th>
                    <th class="text-center" style="width: 18%;">Target Role & Wing</th>
                    <th class="text-center" style="width: 18%;">Qualification & Exp</th>
                    <th class="text-center" style="width: 14%;">Relative</th>
                    <th class="text-center" style="width: 14%;">Status</th>
                    <th class="text-center" style="width: 12%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        {{-- 1. CANDIDATE & APP # (24%) --}}
                        <td class="text-center" style="vertical-align: middle; padding: 12px 8px;">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0" style="width: 34px; height: 34px; background: linear-gradient(135deg, #4F46E5, #312E81); font-size: 13px;">
                                    {{ strtoupper(substr($app->full_name, 0, 1)) }}
                                </div>
                                <div class="text-start" style="min-width: 0;">
                                    <strong class="d-block text-dark text-truncate" style="font-size: 13px; max-width: 150px;" title="{{ $app->full_name }}">{{ $app->full_name }}</strong>
                                    <small class="text-muted d-block text-truncate" style="font-size: 11px; max-width: 150px;">
                                        #{{ $app->application_no }} &bull; {{ $app->phone }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        {{-- 2. TARGET ROLE & WING (18%) --}}
                        <td class="text-center" style="vertical-align: middle; padding: 12px 8px;">
                            @if($app->wing)
                                <span class="badge bg-light text-primary border px-2 py-0.5 rounded-pill fw-bold" style="font-size: 10.5px;">
                                    {{ $app->wing }}
                                </span>
                            @endif
                            <small class="text-muted d-block text-truncate mx-auto mt-0.5" style="max-width: 140px; font-size: 11px;" title="{{ $app->job_title }}">
                                {{ $app->job_title }}
                            </small>
                        </td>

                        {{-- 3. QUALIFICATION & EXP (18%) --}}
                        <td class="text-center" style="vertical-align: middle; padding: 12px 8px;">
                            <div class="fw-bold text-dark text-truncate mx-auto" style="font-size: 12px; max-width: 140px;" title="{{ $app->highest_degree }} ({{ $app->degree_title }})">
                                {{ $app->highest_degree }}
                            </div>
                            <span class="badge bg-primary-subtle text-primary fw-bold mt-1" style="font-size: 10.5px; padding: 1.5px 6px;">
                                {{ $app->experience_years }}
                            </span>
                        </td>

                        {{-- 4. RELATIVE DISCLOSURE (14%) --}}
                        <td class="text-center" style="vertical-align: middle; padding: 12px 8px;">
                            @if($app->has_relative)
                                <span class="badge bg-warning-subtle text-warning fw-bold border border-warning-subtle" style="font-size: 10.5px; padding: 2px 6px;" title="Relative: {{ $app->relative_name }} ({{ $app->relative_relation }})">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Disclosed
                                </span>
                            @else
                                <span class="badge bg-light text-secondary border" style="font-size: 10.5px; padding: 2px 6px;">
                                    <i class="bi bi-check-circle me-1 text-success"></i> None
                                </span>
                            @endif
                        </td>

                        {{-- 5. STATUS (14%) --}}
                        <td class="text-center" style="vertical-align: middle; padding: 12px 8px;">
                            @if($app->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-hourglass-split me-1"></i> Pending
                                </span>
                            @elseif($app->status === 'shortlisted')
                                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-star-fill me-1"></i> Shortlisted
                                </span>
                            @elseif($app->status === 'interviewed')
                                <span class="badge bg-info-subtle text-info fw-bold px-2 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-telephone-inbound-fill me-1"></i> Interviewed
                                </span>
                            @elseif($app->status === 'hired')
                                <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-check2-circle me-1"></i> Hired
                                </span>
                            @elseif($app->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-x-circle me-1"></i> Rejected
                                </span>
                            @endif
                        </td>

                        {{-- 6. ACTIONS (12%) --}}
                        <td class="text-center" style="vertical-align: middle; padding: 12px 8px;">
                            <div class="admin-table-actions justify-content-center">
                                {{-- View Full Details (Eye Icon) --}}
                                <button type="button" class="admin-btn-icon view" data-bs-toggle="modal" data-bs-target="#viewCareerModal{{ $app->id }}" title="View Complete 4-Step Application Details">
                                    <i class="bi bi-eye"></i>
                                </button>

                                {{-- Quick Status Update Modal Trigger --}}
                                <button type="button" class="admin-btn-icon edit" data-bs-toggle="modal" data-bs-target="#statusCareerModal{{ $app->id }}" title="Update Status & Review Notes">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                {{-- Delete --}}
                                <form action="{{ route('admin.career.destroy', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete application #{{ $app->application_no }} for {{ addslashes($app->full_name) }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn-icon danger" title="Delete Application">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- VIEW COMPLETE 4-PROCESS DETAILS MODAL --}}
                    <div class="modal fade" id="viewCareerModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-primary text-white fw-bold font-monospace">{{ $app->application_no }}</span>
                                            <span class="badge bg-light text-dark border">{{ $app->job_title }}</span>
                                            @if($app->wing)
                                                <span class="badge bg-secondary-subtle text-secondary">{{ $app->wing }}</span>
                                            @endif
                                        </div>
                                        <h4 class="modal-title fw-bold text-dark">{{ $app->full_name }}</h4>
                                        <p class="text-muted small mb-0">Submitted on {{ $app->created_at->format('F d, Y \a\t h:i A') }}</p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-4">
                                    {{-- Candidate Status Banner --}}
                                    <div class="p-3 rounded-4 mb-4 d-flex justify-content-between align-items-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284C7, #0369A1);">
                                                <i class="bi bi-person-workspace fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 14.5px;">Applied Position: {{ $app->job_title }}</div>
                                                <div class="text-muted small">Application Status: <strong>{{ ucfirst($app->status) }}</strong></div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#statusCareerModal{{ $app->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> Change Status
                                        </button>
                                    </div>

                                    {{-- PROCESS 1: INTRODUCTION & PERSONAL DETAILS --}}
                                    <div class="card border rounded-4 p-3 mb-3 bg-light-subtle">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge rounded-circle p-2 bg-primary text-white" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">1</span>
                                            <h6 class="fw-bold text-dark mb-0">Introduction & Personal Details</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Full Name</label>
                                                <div class="fw-bold text-dark">{{ $app->full_name }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">CNIC Number (13 Digits)</label>
                                                <div class="fw-bold text-primary font-monospace">{{ $app->cnic }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Email Address</label>
                                                <div class="fw-bold text-dark">{{ $app->email }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Phone / Mobile</label>
                                                <div class="fw-bold text-dark">{{ $app->phone }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">City of Residence</label>
                                                <div class="text-dark">{{ $app->city ?: 'Not specified' }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Gender</label>
                                                <div class="text-dark">{{ $app->gender ?: 'Not specified' }}</div>
                                            </div>
                                            @if($app->introduction)
                                                <div class="col-12">
                                                    <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Personal / Professional Summary</label>
                                                    <div class="p-2 bg-white rounded-3 border text-dark small" style="white-space: pre-line;">{{ $app->introduction }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- PROCESS 2: EDUCATION DETAILS --}}
                                    <div class="card border rounded-4 p-3 mb-3 bg-light-subtle">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge rounded-circle p-2 bg-primary text-white" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">2</span>
                                            <h6 class="fw-bold text-dark mb-0">Education Details</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Highest Qualification Level</label>
                                                <div class="fw-bold text-dark">{{ $app->highest_degree }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Degree / Major Title</label>
                                                <div class="fw-bold text-dark">{{ $app->degree_title }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">University / Institute</label>
                                                <div class="fw-bold text-dark">{{ $app->institute }}</div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Passing Year</label>
                                                <div class="text-dark font-monospace">{{ $app->passing_year }}</div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Marks / CGPA</label>
                                                <div class="text-dark font-monospace">{{ $app->marks_cgpa ?: 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PROCESS 3: EXPERIENCE DETAILS --}}
                                    <div class="card border rounded-4 p-3 mb-3 bg-light-subtle">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge rounded-circle p-2 bg-primary text-white" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">3</span>
                                            <h6 class="fw-bold text-dark mb-0">Experience & Professional History</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-sm-4">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Total Teaching/Work Experience</label>
                                                <div class="fw-bold text-primary">{{ $app->experience_years }}</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Last / Current Organization</label>
                                                <div class="fw-bold text-dark">{{ $app->last_organization ?: 'Not specified / Fresh' }}</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Last Designation / Role</label>
                                                <div class="fw-bold text-dark">{{ $app->last_designation ?: 'N/A' }}</div>
                                            </div>
                                            @if($app->core_skills)
                                                <div class="col-12">
                                                    <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Core Subjects / Special Skills</label>
                                                    <div class="text-dark">{{ $app->core_skills }}</div>
                                                </div>
                                            @endif
                                            @if($app->cover_note)
                                                <div class="col-12">
                                                    <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Cover Note / Teaching Philosophy</label>
                                                    <div class="p-2 bg-white rounded-3 border text-dark small" style="white-space: pre-line;">{{ $app->cover_note }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- PROCESS 4: RELATIVE / REFERENCE DISCLOSURE --}}
                                    <div class="card border rounded-4 p-3 mb-3 {{ $app->has_relative ? 'border-warning bg-warning-subtle' : 'bg-light-subtle' }}">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge rounded-circle p-2 {{ $app->has_relative ? 'bg-warning text-dark' : 'bg-primary text-white' }}" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">4</span>
                                            <h6 class="fw-bold text-dark mb-0">Relative / Staff Reference Disclosure</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Any Relative Working / Worked at Excellence School System?</label>
                                                <div>
                                                    @if($app->has_relative)
                                                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill">
                                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> YES - Relative Disclosed
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 rounded-pill">
                                                            <i class="bi bi-check-circle-fill me-1"></i> NO - No Relative Associated
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($app->has_relative)
                                                <div class="col-sm-4">
                                                    <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Relative Full Name</label>
                                                    <div class="fw-bold text-dark">{{ $app->relative_name }}</div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Designation / Dept</label>
                                                    <div class="fw-bold text-dark">{{ $app->relative_designation ?: 'Staff Member' }}</div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Relationship</label>
                                                    <div class="fw-bold text-dark">{{ $app->relative_relation ?: 'Relative' }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Admin Review Notes if present --}}
                                    @if($app->admin_notes)
                                        <div class="card border rounded-4 p-3 bg-light">
                                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-chat-left-quote-fill me-1 text-primary"></i> Administrative Review Notes</h6>
                                            <p class="text-dark mb-0 small" style="white-space: pre-line;">{{ $app->admin_notes }}</p>
                                            @if($app->reviewed_at)
                                                <small class="text-muted mt-2 d-block" style="font-size: 11px;">Reviewed by: {{ $app->reviewer->name ?? 'Admin' }} on {{ $app->reviewed_at->format('M d, Y h:i A') }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="modal-footer border-0 px-4 pb-4">
                                    <button type="button" class="btn btn-secondary rounded-3 px-4 fw-bold" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary rounded-3 px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#statusCareerModal{{ $app->id }}">
                                        Update Decision
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- UPDATE STATUS MODAL --}}
                    <div class="modal fade" id="statusCareerModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                <form action="{{ route('admin.career.status', $app->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                                        <div>
                                            <h5 class="modal-title fw-bold text-dark">Update Application Status</h5>
                                            <p class="text-muted small mb-0">{{ $app->full_name }} &bull; {{ $app->job_title }} (#{{ $app->application_no }})</p>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-dark">Decision / Recruitment Stage</label>
                                            <select name="status" class="form-select py-2 rounded-3" required>
                                                <option value="pending" {{ $app->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                                <option value="shortlisted" {{ $app->status === 'shortlisted' ? 'selected' : '' }}>Shortlisted for Interview</option>
                                                <option value="interviewed" {{ $app->status === 'interviewed' ? 'selected' : '' }}>Interview Conducted / Demo Scheduled</option>
                                                <option value="hired" {{ $app->status === 'hired' ? 'selected' : '' }}>Hired / Selection Confirmed</option>
                                                <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>Rejected / Not Selected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-dark">Administrative Notes / HR Comments</label>
                                            <textarea name="admin_notes" rows="4" class="form-control rounded-3" placeholder="Add internal notes about candidate interview performance, demo results, salary negotiations, or reasons for decision...">{{ $app->admin_notes }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 px-4 pb-4">
                                        <button type="button" class="btn btn-secondary rounded-3 px-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="rounded-circle p-4 mb-3" style="background: rgba(14, 165, 233, 0.1);">
                                    <i class="bi bi-briefcase fs-1 text-primary"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No Career Applications Found</h5>
                                <p class="text-muted small mb-3" style="max-width: 380px;">
                                    There are currently no career applications matching your selected filters or search query.
                                </p>
                                @if(request()->hasAny(['search', 'status', 'wing', 'has_relative']))
                                    <a href="{{ route('admin.page', 'careers') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                                        Reset All Filters
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($applications->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} applications
            </span>
            <div>
                {{ $applications->links() }}
            </div>
        </div>
    @endif
</div>
