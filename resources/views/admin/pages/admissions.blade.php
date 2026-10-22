{{-- ADMIN PORTAL: ADMISSIONS APPLICATIONS MANAGEMENT --}}
<div class="admin-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge px-3 py-1 rounded-pill" style="background: rgba(79, 70, 229, 0.12); color: #4F46E5; font-weight: 800; font-size: 11px; letter-spacing: 0.05em;">
                    <i class="bi bi-shield-check me-1"></i> ENROLLMENT DESK
                </span>
                <span class="text-muted small">&bull; Session {{ $academicSession ?? '2026-2027' }}</span>
            </div>
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Admissions Applications</h2>
            <p class="text-muted mb-0 small">Manage online applicant records, verify NADRA B-Form & CNIC credentials, and process admission decisions.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admissions') }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3 fw-bold shadow-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Public Form
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
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Total Inquiries</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalAdmissionsCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">All submissions received</small>
                </div>
                <div class="rounded-3 p-3 text-primary" style="background: rgba(79, 70, 229, 0.1); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-file-earmark-person fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #FFFFFF; border-left: 4px solid #F59E0B !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Pending Review</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($pendingAdmissionsCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">Awaiting initial verification</small>
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
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Contacted</span>
                    <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($contactedAdmissionsCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">Interview / test scheduled</small>
                </div>
                <div class="rounded-3 p-3 text-info" style="background: rgba(14, 165, 233, 0.12); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-telephone-forward fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #FFFFFF; border-left: 4px solid #10B981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Approved</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($approvedAdmissionsCount ?? 0) }}</h3>
                    <small class="text-muted" style="font-size: 11.5px;">Offer letter dispatched</small>
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
    <form action="{{ route('admin.page', 'admissions') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search by Student Name, Guardian, B-Form, CNIC, Phone, App #..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select bg-light">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted / Interview</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="grade" class="form-select bg-light">
                <option value="">All Grades</option>
                <option value="Pre-School (Playgroup / KG)" {{ request('grade') === 'Pre-School (Playgroup / KG)' ? 'selected' : '' }}>Pre-School</option>
                <option value="Primary Wing (Grade 1 - 5)" {{ request('grade') === 'Primary Wing (Grade 1 - 5)' ? 'selected' : '' }}>Primary Wing</option>
                <option value="Middle Wing (Grade 6 - 8)" {{ request('grade') === 'Middle Wing (Grade 6 - 8)' ? 'selected' : '' }}>Middle Wing</option>
                <option value="Matriculation (Grade 9 - 10)" {{ request('grade') === 'Matriculation (Grade 9 - 10)' ? 'selected' : '' }}>Matriculation</option>
                <option value="Cambridge O-Level" {{ request('grade') === 'Cambridge O-Level' ? 'selected' : '' }}>Cambridge O-Level</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-bold">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'grade']))
                <a href="{{ route('admin.page', 'admissions') }}" class="btn btn-light border px-3" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- DATA TABLE --}}
<div class="admin-card">
    @if(empty($applications) || $applications->isEmpty())
        <div class="p-5 text-center text-muted">
            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                <i class="bi bi-inbox fs-2 text-muted"></i>
            </div>
            <h5 class="fw-bold text-dark">No Admission Applications Found</h5>
            <p class="small text-muted mb-0">No student admission applications match your current query or none have been submitted yet.</p>
        </div>
    @else
        <div class="admin-table-wrapper" style="overflow-x: hidden;">
            <table class="admin-table" style="width: 100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 25%;">Student Details</th>
                        <th class="text-center" style="width: 21%;">Parent / Guardian</th>
                        <th class="text-center" style="width: 16%;">Grade Applied</th>
                        <th class="text-center" style="width: 14%;">Prior Education</th>
                        <th class="text-center" style="width: 12%;">Status</th>
                        <th class="text-center" style="width: 12%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" style="width: 36px; height: 36px; background: linear-gradient(135deg, #4F46E5, #3730A3); font-size: 13px;">
                                    {{ strtoupper(substr($app->student_name, 0, 1)) }}
                                </div>
                                <div class="text-start">
                                    <strong class="text-dark d-block" style="font-size: 13.5px; line-height: 1.2;">{{ $app->student_name }}</strong>
                                    <div class="d-flex align-items-center gap-1 mt-0.5">
                                        <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 10.5px;">#{{ $app->application_no }}</span>
                                        @if($app->gender)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 10.5px;">{{ $app->gender }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block font-monospace" style="font-size: 11px;">
                                        <i class="bi bi-card-text text-primary me-1"></i>{{ $app->student_bform }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="text-center">
                                <span class="fw-semibold text-dark d-block" style="font-size: 13px;">{{ $app->guardian_name }}</span>
                                <small class="text-muted d-block font-monospace" style="font-size: 11px;">
                                    <i class="bi bi-person-vcard text-muted me-1"></i>{{ $app->guardian_cnic }}
                                </small>
                                <a href="tel:{{ $app->guardian_phone }}" class="text-decoration-none small text-primary fw-bold" style="font-size: 11.5px;">
                                    <i class="bi bi-telephone me-1"></i>{{ $app->guardian_phone }}
                                </a>
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: rgba(79, 70, 229, 0.12); color: #4F46E5; font-size: 11.5px;">
                                {{ $app->grade_applied }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if($app->previous_class || $app->previous_school)
                                <div class="small">
                                    <strong class="text-dark d-block" style="font-size: 12.5px;">{{ $app->previous_class ?: 'Prior' }}</strong>
                                    @if($app->previous_school)
                                        <small class="text-muted d-block text-truncate mx-auto" style="max-width: 150px; font-size: 11px;" title="{{ $app->previous_school }}">
                                            {{ $app->previous_school }}
                                        </small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small" style="font-size: 11.5px;">Fresh Admission</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($app->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Approved
                                </span>
                            @elseif($app->status === 'contacted')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                    <i class="bi bi-telephone-forward-fill me-1"></i> Contacted
                                </span>
                            @elseif($app->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                    <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                                    <i class="bi bi-hourglass-split me-1"></i> Pending
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="admin-table-actions justify-content-center">
                                <!-- View Details Modal Trigger -->
                                <button type="button" class="admin-btn-icon view" data-bs-toggle="modal" data-bs-target="#viewAppModal{{ $app->id }}" title="View Application Details">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Quick Status Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <button class="admin-btn-icon edit" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Change Status">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="font-size: 13px; border-radius: 12px;">
                                        <li>
                                            <form action="{{ route('admin.admission.status', $app->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="dropdown-item py-1.5 text-success">
                                                    <i class="bi bi-check-circle me-1"></i> Mark Approved
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.admission.status', $app->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="contacted">
                                                <button type="submit" class="dropdown-item py-1.5 text-info">
                                                    <i class="bi bi-telephone me-1"></i> Mark Contacted
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.admission.status', $app->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="dropdown-item py-1.5 text-danger">
                                                    <i class="bi bi-x-circle me-1"></i> Mark Rejected
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Delete Trigger -->
                                <form action="{{ route('admin.admission.destroy', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete application #{{ $app->application_no }} for {{ addslashes($app->student_name) }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn-icon danger" title="Delete Application">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- DETAIL MODAL FOR EACH APPLICATION --}}
                    <div class="modal fade" id="viewAppModal{{ $app->id }}" tabindex="-1" aria-labelledby="viewAppModalLabel{{ $app->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1E1B4B 0%, #4F46E5 100%); padding: 18px 24px;">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-warning text-dark font-monospace" style="font-size: 11px;">#{{ $app->application_no }}</span>
                                            <small class="opacity-75">Submitted: {{ $app->created_at ? $app->created_at->format('M d, Y h:i A') : 'Recent' }}</small>
                                        </div>
                                        <h5 class="modal-title fw-bold text-white mb-0" id="viewAppModalLabel{{ $app->id }}">
                                            <i class="bi bi-mortarboard-fill me-2"></i> {{ $app->student_name }} &bull; Admission Dossier
                                        </h5>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        {{-- Student Profile --}}
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-4 border bg-light h-100">
                                                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                                    <i class="bi bi-person-fill me-1"></i> Student Information
                                                </h6>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Student Full Name</small>
                                                    <strong class="text-dark fs-6">{{ $app->student_name }}</strong>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Student Gender</small>
                                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1 fw-bold">{{ $app->gender ?: 'Not specified' }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">NADRA B-Form / Smart Card No (13 Digits)</small>
                                                    <span class="badge bg-white text-dark border px-2 py-1 font-monospace fs-6">
                                                        {{ $app->student_bform }}
                                                    </span>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Applying For Grade / Wing</small>
                                                    <span class="badge px-3 py-1 rounded-pill" style="background: rgba(79, 70, 229, 0.15); color: #4F46E5; font-size: 12px; font-weight: 700;">
                                                        {{ $app->grade_applied }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Guardian Profile --}}
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-4 border bg-light h-100">
                                                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                                    <i class="bi bi-person-badge me-1"></i> Parent / Guardian Information
                                                </h6>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Parent / Guardian Full Name</small>
                                                    <strong class="text-dark fs-6">{{ $app->guardian_name }}</strong>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Guardian CNIC No (13 Digits)</small>
                                                    <span class="badge bg-white text-dark border px-2 py-1 font-monospace fs-6">
                                                        {{ $app->guardian_cnic }}
                                                    </span>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Contact Phone</small>
                                                        <a href="tel:{{ $app->guardian_phone }}" class="fw-bold text-primary text-decoration-none">
                                                            <i class="bi bi-telephone-fill me-1"></i> {{ $app->guardian_phone }}
                                                        </a>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Email Address</small>
                                                        <span class="text-dark">{{ $app->guardian_email ?: 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Prior Academic History --}}
                                        <div class="col-12">
                                            <div class="p-3 rounded-4 border bg-light">
                                                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                                    <i class="bi bi-journal-bookmark me-1"></i> Prior Academic History
                                                </h6>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Last Passed Class / Grade</small>
                                                        <strong class="text-dark">{{ $app->previous_class ?: 'Fresh Admission / None' }}</strong>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <small class="text-muted d-block">Previous School / Institute</small>
                                                        <strong class="text-dark">{{ $app->previous_school ?: 'N/A' }}</strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block">Previous Marks / Grade</small>
                                                        <strong class="text-dark">{{ $app->previous_marks ?: 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                                @if($app->message)
                                                    <div class="mt-3 pt-2 border-top">
                                                        <small class="text-muted d-block">Parent Notes / Special Inquiries</small>
                                                        <p class="mb-0 text-dark small bg-white p-2 rounded-2 border mt-1">{{ $app->message }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Status & Admin Notes Form --}}
                                        <div class="col-12">
                                            <div class="p-3 rounded-4 border" style="background: #F8FAFC;">
                                                <h6 class="fw-bold text-dark mb-3">
                                                    <i class="bi bi-gear-fill text-secondary me-1"></i> Process Admission Decision
                                                </h6>
                                                <form action="{{ route('admin.admission.status', $app->id) }}" method="POST">
                                                    @csrf
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-dark">Decision Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="pending" {{ $app->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                                                <option value="contacted" {{ $app->status === 'contacted' ? 'selected' : '' }}>Contacted / Interview Scheduled</option>
                                                                <option value="approved" {{ $app->status === 'approved' ? 'selected' : '' }}>Approved / Admission Granted</option>
                                                                <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>Rejected / Ineligible</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label class="form-label fw-bold small text-dark">Admin Internal Notes / Remarks</label>
                                                            <input type="text" name="admin_notes" class="form-control" placeholder="e.g. Called parent on 23rd Oct, interview scheduled on Monday 10:00 AM..." value="{{ $app->admin_notes }}">
                                                        </div>
                                                        <div class="col-12 text-end">
                                                            <button type="submit" class="btn btn-primary px-4 fw-bold rounded-3">
                                                                <i class="bi bi-save me-1"></i> Save Decision & Notes
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} applications</small>
                {{ $applications->links() }}
            </div>
        @endif
    @endif
</div>
