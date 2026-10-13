<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-person-badge-fill text-info"></i> Teaching Staff Master Directory & Profiles
        </h3>

        <div class="admin-filter-bar">
            <a href="/add-teacher" class="admin-btn admin-btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Teacher
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.page', 'teachers') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search teacher, email, phone..." value="{{ $searchQuery ?? '' }}">
        </div>
        <div class="col-md-3">
            <select name="class" class="form-select">
                <option value="">-- All Classes --</option>
                @foreach($allClasses as $c)
                    <option value="{{ $c->class_name }}" {{ ($selectedClass ?? '') == $c->class_name ? 'selected' : '' }}>
                        {{ $c->class_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="section" class="form-select">
                <option value="">-- All Sections --</option>
                @foreach($allSections as $sec)
                    <option value="{{ $sec->section_name }}" {{ ($selectedSection ?? '') == $sec->section_name ? 'selected' : '' }}>
                        Section {{ $sec->section_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="admin-btn admin-btn-primary w-100 justify-content-center">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </form>

    @if($teachers->isEmpty())
        <div class="admin-empty-state">
            <i class="bi bi-person-badge admin-empty-icon"></i>
            <p>No teaching staff records found matching current criteria.</p>
        </div>
    @else
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Teacher Name</th>
                        <th>Email & Phone</th>
                        <th>Assigned Classes / Course</th>
                        <th>Latest Salary</th>
                        <th>User Account</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $t)
                        @php
                            $latestSal = $t->salaries?->first();
                        @endphp
                        <tr>
                            <td>
                                <strong class="d-block text-dark fs-6">{{ $t->teacher_name }}</strong>
                                <small class="text-muted">Staff ID: #{{ $t->id }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-secondary">{{ $t->teacher_email ?: '-' }}</div>
                                <small class="text-muted"><i class="bi bi-telephone"></i> {{ $t->teacher_phone ?: '-' }}</small>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-info-subtle text-info-emphasis fw-bold">
                                        {{ $t->course ?: 'General Subjects' }}
                                    </span>
                                    @foreach($t->assignments as $asg)
                                        <span class="badge bg-primary-subtle text-primary fw-semibold" style="font-size: 10px;">
                                            {{ $asg->class_name }} ({{ $asg->section_name }})
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($latestSal)
                                    <strong class="text-dark">Rs {{ number_format($latestSal->net_salary, 2) }}</strong>
                                    <small class="d-block text-muted">({{ $latestSal->month }} - {{ $latestSal->status }})</small>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">Not Declared</span>
                                @endif
                            </td>
                            <td>
                                @if($t->user)
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle"></i> {{ $t->user->role?->name ?: 'Active Account' }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">No Account</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="admin-btn admin-btn-outline admin-btn-sm" data-bs-toggle="modal" data-bs-target="#viewTeacherModal{{ $t->id }}" title="View Full Teacher Profile">
                                        <i class="bi bi-eye"></i> Details
                                    </button>

                                    <a href="{{ route('admin.page', ['page' => 'salaries', 'search' => $t->teacher_name]) }}" class="admin-btn admin-btn-success admin-btn-sm">
                                        <i class="bi bi-cash-coin"></i> Salary
                                    </a>

                                    <form action="/teachers/{{ $t->id }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher profile?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- TEACHER DETAILS MODAL --}}
                        <div class="modal fade admin-modal" id="viewTeacherModal{{ $t->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-person-badge-fill text-info me-2"></i> Staff Full Profile: {{ $t->teacher_name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-4">
                                            {{-- General Info Card --}}
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded-3 h-100 border">
                                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                                        <i class="bi bi-person-vcard me-2"></i> Contact & Personal Information
                                                    </h6>
                                                    <p class="mb-2"><strong>Full Name:</strong> {{ $t->teacher_name }}</p>
                                                    <p class="mb-2"><strong>Staff ID:</strong> #{{ $t->id }}</p>
                                                    <p class="mb-2"><strong>Email Address:</strong> {{ $t->teacher_email ?: 'N/A' }}</p>
                                                    <p class="mb-2"><strong>Phone Number:</strong> {{ $t->teacher_phone ?: 'N/A' }}</p>
                                                    <p class="mb-0"><strong>Primary Course/Subject:</strong> {{ $t->course ?: 'General' }}</p>
                                                </div>
                                            </div>

                                            {{-- Salary Status Card --}}
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded-3 h-100 border">
                                                    <h6 class="fw-bold text-success mb-3 border-bottom pb-2">
                                                        <i class="bi bi-cash-stack me-2"></i> Financial & Salary Overview
                                                    </h6>
                                                    @if($latestSal)
                                                        <p class="mb-2"><strong>Latest Month:</strong> {{ $latestSal->month }}</p>
                                                        <p class="mb-2"><strong>Basic Salary:</strong> Rs {{ number_format($latestSal->basic_salary, 2) }}</p>
                                                        <p class="mb-2"><strong>Allowances:</strong> +Rs {{ number_format($latestSal->allowances ?? 0, 2) }}</p>
                                                        <p class="mb-2"><strong>Deductions:</strong> -Rs {{ number_format($latestSal->deductions ?? 0, 2) }}</p>
                                                        <p class="mb-2"><strong>Net Salary:</strong> <span class="fw-bold text-success">Rs {{ number_format($latestSal->net_salary, 2) }}</span></p>
                                                        <p class="mb-0"><strong>Payment Status:</strong> <span class="badge bg-success">{{ $latestSal->status }}</span></p>
                                                    @else
                                                        <p class="text-muted mb-0">No salary declaration record found for this staff member.</p>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Class & Subject Assignments --}}
                                            <div class="col-12">
                                                <div class="p-3 bg-light rounded-3 border">
                                                    <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">
                                                        <i class="bi bi-journal-bookmark-fill me-2"></i> Assigned Classes & Subjects
                                                    </h6>
                                                    @if($t->assignments->isEmpty())
                                                        <p class="text-muted mb-0 fs-7">No specific class/section assignments mapped to this teacher yet.</p>
                                                    @else
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($t->assignments as $asg)
                                                                <div class="p-2 bg-white rounded border d-flex align-items-center gap-2">
                                                                    <i class="bi bi-book text-primary"></i>
                                                                    <div>
                                                                        <strong class="d-block text-dark fs-7">{{ $asg->class_name }} (Section {{ $asg->section_name }})</strong>
                                                                        <small class="text-muted">{{ $asg->subject_or_course }}</small>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('admin.page', ['page' => 'salaries', 'search' => $t->teacher_name]) }}" class="admin-btn admin-btn-success admin-btn-sm me-auto">
                                            <i class="bi bi-cash-coin"></i> Manage Salary
                                        </a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
        </div>

        @if($teachers instanceof \Illuminate\Pagination\LengthAwarePaginator && $teachers->hasPages())
            <div class="admin-pagination-bar">
                <div class="admin-pagination-info">
                    Showing <strong>{{ $teachers->firstItem() }}</strong> to <strong>{{ $teachers->lastItem() }}</strong> of <strong>{{ $teachers->total() }}</strong> teachers
                </div>
                <div class="admin-pagination-links">
                    <ul class="pagination mb-0">
                        @if ($teachers->onFirstPage())
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $teachers->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                        @endif

                        @foreach ($teachers->getUrlRange(1, $teachers->lastPage()) as $page => $url)
                            @if ($page == $teachers->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @elseif ($page == 1 || $page == $teachers->lastPage() || abs($page - $teachers->currentPage()) <= 2)
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @elseif (abs($page - $teachers->currentPage()) == 3)
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            @endif
                        @endforeach

                        @if ($teachers->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $teachers->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
    @endif
</div>
