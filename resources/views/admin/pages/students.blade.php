<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-mortarboard-fill text-warning"></i> Students Master Directory & Profiles
        </h3>

        <div class="admin-filter-bar">
            <a href="/add-student" class="admin-btn admin-btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Student
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.page', 'students') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name, roll no, father name..." value="{{ $searchQuery ?? '' }}">
        </div>
        <div class="col-md-3">
            <select name="class" class="form-select">
                <option value="">-- All Classes --</option>
                @foreach($classes as $c)
                    <option value="{{ $c->class_name }}" {{ ($selectedClass ?? '') == $c->class_name ? 'selected' : '' }}>
                        {{ $c->class_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="section" class="form-select">
                <option value="">-- All Sections --</option>
                @foreach($sections as $sec)
                    <option value="{{ $sec->section_name }}" {{ ($selectedSection ?? '') == $sec->section_name ? 'selected' : '' }}>
                        {{ $sec->section_name }}
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

    @if($students->isEmpty())
        <div class="admin-empty-state">
            <i class="bi bi-people admin-empty-icon"></i>
            <p>No student records match the given criteria.</p>
        </div>
    @else
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Roll #</th>
                        <th>Student Name</th>
                        <th>Class & Section</th>
                        <th>Father / Guardian</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $std)
                        <tr>
                            <td>
                                <strong class="badge bg-dark text-light fw-bold" style="font-size: 11px;">
                                    #{{ $std->std_id ?: $std->id }}
                                </strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ !empty($std->student_image) && $std->student_image !== 'dummy.jpeg' ? asset('upload/'.$std->student_image) : asset('upload/dummy.jpeg') }}" class="admin-avatar" alt="Avatar">
                                    <div>
                                        <strong class="d-block text-dark">{{ $std->name }}</strong>
                                        <small class="text-muted">{{ $std->email ?: 'No email' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-bold">
                                    {{ $std->class }} - {{ $std->section ?: 'Gen' }}
                                </span>
                            </td>
                            <td>{{ $std->father_name ?: '-' }}</td>
                            <td><small class="fw-semibold text-secondary">{{ $std->phone_no ?: '-' }}</small></td>
                            <td>
                                <span class="badge bg-success-subtle text-success">Active</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="admin-btn admin-btn-outline admin-btn-sm" data-bs-toggle="modal" data-bs-target="#viewStudentModal{{ $std->id }}" title="Quick View Student Details">
                                        <i class="bi bi-eye"></i> Details
                                    </button>

                                    <a href="/students/{{ $std->id }}" class="admin-btn admin-btn-secondary admin-btn-sm" title="View Full Profile Page">
                                        <i class="bi bi-person-bounding-box"></i> Profile
                                    </a>

                                    <form action="/students/{{ $std->id }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- STUDENT DETAILS MODAL --}}
                        <div class="modal fade admin-modal" id="viewStudentModal{{ $std->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-mortarboard-fill text-warning me-2"></i> Student Details: {{ $std->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-4 text-center">
                                                <div class="p-3 bg-light rounded-3 border">
                                                    <img src="{{ !empty($std->student_image) && $std->student_image !== 'dummy.jpeg' ? asset('upload/'.$std->student_image) : asset('upload/dummy.jpeg') }}" class="rounded-circle mb-3 border shadow-sm" style="width: 110px; height: 110px; object-fit: cover;" alt="Student Photo">
                                                    <h6 class="fw-bold text-dark mb-1">{{ $std->name }}</h6>
                                                    <span class="badge bg-primary px-3 py-1 mb-2">{{ $std->class }} - Section {{ $std->section ?: 'Gen' }}</span>
                                                    <small class="d-block text-muted">Roll No: #{{ $std->std_id ?: $std->id }}</small>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="p-3 bg-light rounded-3 border h-100">
                                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                                        <i class="bi bi-info-circle me-2"></i> Academic & Personal Records
                                                    </h6>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <p class="mb-2"><strong>Father Name:</strong> {{ $std->father_name ?: 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-2"><strong>Reg No:</strong> {{ $std->registration_no ?: '#' . $std->id }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-2"><strong>Email:</strong> {{ $std->email ?: 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-2"><strong>Phone No:</strong> {{ $std->phone_no ?: 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-2"><strong>Emergency Contact:</strong> {{ $std->emergency_phone ?: 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-2"><strong>Monthly Fee:</strong> Rs {{ number_format($std->monthly_fee ?? 0, 2) }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Active Enrolled</span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="/students/{{ $std->id }}" class="admin-btn admin-btn-primary admin-btn-sm me-auto">
                                            <i class="bi bi-person-bounding-box"></i> View Full Profile Page
                                        </a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div>

        @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->hasPages())
            <div class="admin-pagination-bar">
                <div class="admin-pagination-info">
                    Showing <strong>{{ $students->firstItem() }}</strong> to <strong>{{ $students->lastItem() }}</strong> of <strong>{{ $students->total() }}</strong> students
                </div>
                <div class="admin-pagination-links">
                    <ul class="pagination mb-0">
                        @if ($students->onFirstPage())
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $students->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                        @endif

                        @foreach ($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                            @if ($page == $students->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @elseif ($page == 1 || $page == $students->lastPage() || abs($page - $students->currentPage()) <= 2)
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @elseif (abs($page - $students->currentPage()) == 3)
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            @endif
                        @endforeach

                        @if ($students->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $students->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
    @endif
</div>
