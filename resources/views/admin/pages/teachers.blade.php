<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-person-badge-fill text-info"></i> Teaching Staff Master Directory & Profiles
        </h3>

        <div class="admin-filter-bar">
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addNewTeacherModal">
                <i class="bi bi-plus-lg"></i> Add New Teacher
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.page', 'teachers') }}" class="admin-filter-form row g-3 mb-0">
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
                        <th class="text-center">Teacher Name</th>
                        <th class="text-center">Email & Phone</th>
                        <th class="text-center">Assigned Classes / Course</th>
                        <th class="text-center">User Account</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $t)
                        @php
                            $latestSal = $t->salaries?->first();
                        @endphp
                        <tr>
                            <td class="text-center" style="white-space: nowrap;">
                                <strong class="d-block text-dark fs-6" style="white-space: nowrap;">{{ $t->teacher_name }}</strong>
                                <small class="text-muted">Staff ID: #{{ $t->id }}</small>
                            </td>
                            <td class="text-center">
                                <div class="fw-semibold text-secondary" style="white-space: nowrap;">{{ $t->teacher_email ?: '-' }}</div>
                                <small class="text-muted"><i class="bi bi-telephone"></i> {{ $t->teacher_phone ?: '-' }}</small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
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
                            <td class="text-center">
                                @if($t->user)
                                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle"></i> {{ $t->user->role?->name ?: 'Active Account' }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">No Account</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="admin-table-actions justify-content-center">
                                    {{-- View Full Profile & Details --}}
                                    <button type="button" class="admin-btn-icon view" data-bs-toggle="modal" data-bs-target="#viewTeacherModal{{ $t->id }}" title="View Full Teacher Profile & Salary Details">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Teacher Profile & Salary --}}
                                    <button type="button" class="admin-btn-icon edit" data-bs-toggle="modal" data-bs-target="#editTeacherModal{{ $t->id }}" title="Edit Teacher Profile & View Salary">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('teachers.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher profile?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn-icon danger" title="Delete Teacher">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- VIEW TEACHER MODALS --}}
        @foreach($teachers as $t)
            @php $latestSal = $t->salaries?->first(); @endphp
            <div class="modal fade" id="viewTeacherModal{{ $t->id }}" tabindex="-1" aria-labelledby="viewTeacherModalLabel{{ $t->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); padding: 16px 22px;">
                            <h5 class="modal-title fw-bold text-white mb-0" id="viewTeacherModalLabel{{ $t->id }}">
                                <i class="bi bi-person-badge-fill me-2"></i> Staff Profile: {{ $t->teacher_name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        <p class="mb-0"><strong>Primary Course/Subject:</strong> {{ $t->course ?: 'General Subjects' }}</p>
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
                        <div class="modal-footer bg-light">
                            <a href="{{ route('admin.page', ['page' => 'salaries', 'search' => $t->teacher_name]) }}" class="admin-btn admin-btn-success admin-btn-sm me-auto">
                                <i class="bi bi-cash-coin"></i> Manage Full Salary Record
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- EDIT TEACHER MODALS --}}
        @foreach($teachers as $t)
            @php $latestSal = $t->salaries?->first(); @endphp
            <div class="modal fade" id="editTeacherModal{{ $t->id }}" tabindex="-1" aria-labelledby="editTeacherModalLabel{{ $t->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" style="margin-top: 1.5rem; margin-bottom: 3rem;">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <form action="{{ route('teachers.update', $t->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); padding: 16px 22px; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                                <h5 class="modal-title fw-bold text-white mb-0" id="editTeacherModalLabel{{ $t->id }}">
                                    <i class="bi bi-pencil-square me-2"></i> Edit Teacher: {{ $t->teacher_name }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto; padding-bottom: 35px;">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Teacher Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="teacher_name" class="form-control" value="{{ $t->teacher_name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <input type="email" name="teacher_email" class="form-control" value="{{ $t->teacher_email }}" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Must be a valid @gmail.com address">
                                        <small class="text-muted" style="font-size: 11px;">Must be a valid @gmail.com address.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <input type="text" name="teacher_phone" class="form-control" value="{{ $t->teacher_phone }}" pattern="^03[0-9]{9}$" maxlength="11" title="11 digits starting with 03">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Course / Primary Subject</label>
                                        <input type="text" name="course" class="form-control" value="{{ $t->course }}" placeholder="e.g. Mathematics, Science">
                                    </div>
                                </div>

                                {{-- Teacher Editable Salary Card inside Edit Modal --}}
                                <div class="card border-0" style="background: linear-gradient(145deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #86EFAC !important; border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-success border-opacity-25">
                                            <span class="fw-bold text-success">
                                                <i class="bi bi-cash-stack me-1"></i> Salary Structure & Update
                                            </span>
                                            <span class="badge bg-success-subtle text-success border border-success fw-bold">Live Synced</span>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small mb-1">Basic Salary (PKR)</label>
                                                <input type="number" step="0.01" min="0" name="basic_salary" class="form-control form-control-sm" value="{{ $latestSal ? $latestSal->basic_salary : 50000 }}" placeholder="50000">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small mb-1">Allowances (PKR)</label>
                                                <input type="number" step="0.01" min="0" name="allowances" class="form-control form-control-sm" value="{{ $latestSal ? $latestSal->allowances : 0 }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small mb-1">Deductions (PKR)</label>
                                                <input type="number" step="0.01" min="0" name="deductions" class="form-control form-control-sm" value="{{ $latestSal ? $latestSal->deductions : 0 }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label fw-semibold small mb-1">Salary Month</label>
                                                <input type="text" name="salary_month" class="form-control form-control-sm" value="{{ $latestSal ? $latestSal->month : date('F Y') }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label fw-semibold small mb-1">Payment Status</label>
                                                <select name="salary_status" class="form-select form-select-sm">
                                                    <option value="Paid" {{ ($latestSal?->status ?? 'Paid') === 'Paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="Pending" {{ ($latestSal?->status ?? '') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @if($teachers instanceof \Illuminate\Pagination\LengthAwarePaginator && $teachers->total() > 0)
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

{{-- ADD NEW TEACHER MODAL --}}
<div class="modal fade" id="addNewTeacherModal" tabindex="-1" aria-labelledby="addNewTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="margin-top: 1.5rem; margin-bottom: 3rem;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form action="{{ url('/add-teacher') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); padding: 18px 24px; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="addNewTeacherModalLabel">
                            <i class="bi bi-person-plus-fill me-2"></i> Register New Teaching Staff
                        </h5>
                        <small class="text-white-50">Create teacher record, assign classes & configure starting salary.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto; padding-bottom: 40px;">
                    {{-- Basic Teacher Info --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lines-fill"></i> Personal & Contact Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teacher Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="teacher_name" class="form-control" placeholder="e.g. Muhammad Usman" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gmail Address <span class="text-danger">*</span></label>
                                <input type="email" name="teacher_email" class="form-control" placeholder="usman.teacher@gmail.com" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Must be a valid @gmail.com address" required>
                                <small class="text-muted" style="font-size: 11px;">Must end with @gmail.com (used for teacher portal login).</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="teacher_phone" class="form-control" placeholder="03xxxxxxxxx (11 digits)" pattern="^03[0-9]{9}$" maxlength="11" title="11-digit mobile number starting with 03" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Profile Photo</label>
                                <input type="file" name="teacher_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                    </div>

                    {{-- Academic Assignments --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-journal-check"></i> Academic Allocation & Subject
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Primary Course / Subject <span class="text-danger">*</span></label>
                                <select name="course" class="form-select" required>
                                    <option value="" disabled selected>-- Select Subject / Course --</option>
                                    @php
                                        $presetSubjects = ['General Subjects', 'Mathematics', 'Science', 'English', 'Urdu', 'Islamiat', 'Pakistan Studies', 'Computer Science', 'Physics', 'Chemistry', 'Biology'];
                                        $dbSubjects = isset($allSubjects) ? $allSubjects->pluck('subject_name')->toArray() : [];
                                        $mergedSubjects = array_values(array_unique(array_filter(array_merge($presetSubjects, $dbSubjects))));
                                        sort($mergedSubjects);
                                    @endphp
                                    @foreach($mergedSubjects as $subName)
                                        <option value="{{ $subName }}">{{ $subName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Assigned Class & Section <span class="text-danger">*</span></label>
                                <select name="assigned_class" class="form-select" required>
                                    <option value="" disabled selected>-- Select Class & Section --</option>
                                    @if(isset($allClasses))
                                        @foreach($allClasses as $c)
                                            @if(isset($allSections) && $allSections->count())
                                                <optgroup label="Class {{ $c->class_name }}">
                                                    @foreach($allSections as $sec)
                                                        <option value="{{ $c->class_name }}|{{ $sec->section_name }}">
                                                            {{ $c->class_name }} (Section {{ $sec->section_name }})
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <option value="{{ $c->class_name }}">{{ $c->class_name }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- SALARY DECLARATION CARD --}}
                    <div class="card border-0 mb-3" style="background: linear-gradient(145deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #86EFAC !important; border-radius: 14px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-success border-opacity-25">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-success mb-0">Salary Structure & Initial Declaration</h6>
                                        <small class="text-muted" style="font-size: 11.5px;">Reflects automatically on Admin Salaries and Teacher's Portal.</small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1">Auto-Linked</span>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small mb-1">Basic Salary (PKR) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white fw-bold">Rs</span>
                                        <input type="number" step="0.01" min="0" name="basic_salary" id="add_teacher_basic" class="form-control" placeholder="50000" required oninput="calculateAddTeacherNet()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small mb-1">Allowances (PKR)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white fw-bold">+</span>
                                        <input type="number" step="0.01" min="0" name="allowances" id="add_teacher_allowances" class="form-control" value="0" oninput="calculateAddTeacherNet()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small mb-1">Deductions (PKR)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white fw-bold">-</span>
                                        <input type="number" step="0.01" min="0" name="deductions" id="add_teacher_deductions" class="form-control" value="0" oninput="calculateAddTeacherNet()">
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label class="form-label fw-semibold text-dark small mb-1">Salary Month</label>
                                    <input type="text" name="salary_month" class="form-control form-control-sm" value="{{ date('F Y') }}" placeholder="e.g. May 2026">
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label class="form-label fw-semibold text-dark small mb-1">Payment Status</label>
                                    <select name="salary_status" class="form-select form-select-sm">
                                        <option value="Paid" selected>Paid (Disbursed)</option>
                                        <option value="Pending">Pending (Processing)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-top border-success border-opacity-25 d-flex align-items-center justify-content-between">
                                <span class="small text-muted fw-semibold">Computed Monthly Net Pay:</span>
                                <span class="fs-6 fw-bold text-success" id="add_teacher_net_preview">Rs 0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Portal Login Credentials Note --}}
                    <div class="alert alert-info border-0 p-3 mb-0 d-flex align-items-center gap-2" style="border-radius: 12px; font-size: 12px; background: #EEF2FF; color: #3730A3;">
                        <i class="bi bi-shield-lock-fill fs-5 text-primary"></i>
                        <div>
                            <strong>Automatic Portal Account:</strong> A secure teacher portal login will be generated with their Gmail address and temporary default password <code>Teacher@123</code>.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <i class="bi bi-check-circle-fill"></i> Register Teacher & Declare Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateAddTeacherNet() {
    const basic = parseFloat(document.getElementById('add_teacher_basic')?.value || 0);
    const allowances = parseFloat(document.getElementById('add_teacher_allowances')?.value || 0);
    const deductions = parseFloat(document.getElementById('add_teacher_deductions')?.value || 0);
    const net = Math.max(0, basic + allowances - deductions);
    const preview = document.getElementById('add_teacher_net_preview');
    if (preview) {
        preview.textContent = 'Rs ' + net.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}
</script>
