<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-mortarboard-fill text-warning"></i> Students Master Directory & Profiles
        </h3>

        <div class="admin-filter-bar">
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addNewStudentModal">
                <i class="bi bi-plus-lg"></i> Add New Student
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.page', 'students') }}" class="admin-filter-form row g-3 mb-0">
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
                        <th class="text-center">Roll #</th>
                        <th class="text-center">Student Name</th>
                        <th class="text-center">Class & Section</th>
                        <th class="text-center">Father / Guardian</th>
                        <th class="text-center">Phone</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $std)
                        <tr>
                            <td class="text-center">
                                <strong class="badge bg-dark text-light fw-bold" style="font-size: 11px;">
                                    #{{ $std->std_id ?: $std->id }}
                                </strong>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <img src="{{ !empty($std->student_image) && $std->student_image !== 'dummy.jpeg' ? asset('upload/'.$std->student_image) : asset('upload/dummy.jpeg') }}" class="admin-avatar" style="width: 38px; height: 38px; min-width: 38px; min-height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" alt="{{ $std->name }}">
                                    <div class="text-start">
                                        <strong class="d-block text-dark" style="white-space: nowrap;">{{ $std->name }}</strong>
                                        <small class="text-muted">{{ $std->email ?: 'No email' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary fw-bold">
                                    {{ $std->class }} - {{ $std->section ?: 'Gen' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $std->father_name ?: '-' }}</td>
                            <td class="text-center"><small class="fw-semibold text-secondary">{{ $std->phone_no ?: '-' }}</small></td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success">Active</span>
                            </td>
                            <td class="text-center">
                                <div class="admin-table-actions justify-content-center">
                                    {{-- View Details --}}
                                    <button type="button" class="admin-btn-icon view" data-bs-toggle="modal" data-bs-target="#viewStudentModal{{ $std->id }}" title="Quick View Student Details">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Student --}}
                                    <button type="button" class="admin-btn-icon edit" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $std->id }}" title="Edit Student Profile">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('students.destroy', $std->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn-icon danger" title="Delete Student">
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

        {{-- VIEW STUDENT MODALS --}}
        @foreach($students as $std)
            <div class="modal fade" id="viewStudentModal{{ $std->id }}" tabindex="-1" aria-labelledby="viewStudentModalLabel{{ $std->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); padding: 16px 22px;">
                            <h5 class="modal-title fw-bold text-white mb-0" id="viewStudentModalLabel{{ $std->id }}">
                                <i class="bi bi-mortarboard-fill me-2"></i> Student Profile: {{ $std->name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <div class="col-md-4 text-center">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <img src="{{ !empty($std->student_image) && $std->student_image !== 'dummy.jpeg' ? asset('upload/'.$std->student_image) : asset('upload/dummy.jpeg') }}" class="rounded-circle mb-3 border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;" alt="Student Photo">
                                        <h6 class="fw-bold text-dark mb-1">{{ $std->name }}</h6>
                                        <span class="badge bg-primary px-3 py-1 mb-2">{{ $std->class }} - Section {{ $std->section ?: 'Gen' }}</span>
                                        <small class="d-block text-muted">Roll No: <strong>#{{ $std->std_id ?: $std->id }}</strong></small>
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
                                                <p class="mb-2"><strong>Email Address:</strong> {{ $std->email ?: 'N/A' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><strong>Phone Number:</strong> {{ $std->phone_no ?: 'N/A' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><strong>Emergency Contact:</strong> {{ $std->emergency_phone ?: 'N/A' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><strong>Monthly Fee:</strong> <span class="fw-bold text-success">Rs {{ number_format($std->monthly_fee ?? 3500, 2) }}</span></p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><strong>Status:</strong> <span class="badge bg-success">Active Enrolled</span></p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><strong>Registered:</strong> {{ $std->created_at ? $std->created_at->format('M d, Y') : 'Active' }}</p>
                                            </div>
                                        </div>

                                        {{-- Quick Fee Account Status --}}
                                        @php
                                            $latestFee = $std->feeCollections ? $std->feeCollections->first() : null;
                                        @endphp
                                        <div class="mt-3 p-2 rounded-2 border d-flex align-items-center justify-content-between" style="background: #F0FDF4; border-color: #86EFAC !important; font-size: 12px;">
                                            <span class="d-flex align-items-center gap-1 text-success fw-bold">
                                                <i class="bi bi-wallet2"></i> Fee Account:
                                            </span>
                                            @if($latestFee)
                                                <span>Voucher: <strong class="text-primary">{{ $latestFee->voucher_no }}</strong> ({{ $latestFee->month }})</span>
                                                <span class="badge {{ strtolower($latestFee->status) === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $latestFee->status }}
                                                </span>
                                            @else
                                                <span class="text-muted">Monthly Tuition: Rs {{ number_format($std->monthly_fee ?? 3500) }}</span>
                                                <span class="badge bg-success">Standard</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <a href="/students/{{ $std->id }}" class="admin-btn admin-btn-primary admin-btn-sm me-auto">
                                <i class="bi bi-person-bounding-box"></i> View Full Profile Page
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- EDIT STUDENT MODALS --}}
        @foreach($students as $std)
            <div class="modal fade" id="editStudentModal{{ $std->id }}" tabindex="-1" aria-labelledby="editStudentModalLabel{{ $std->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                        <form action="{{ route('students.update', $std->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="std_id" value="{{ $std->std_id ?: $std->id }}">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); padding: 16px 22px;">
                                <h5 class="modal-title fw-bold text-white mb-0" id="editStudentModalLabel{{ $std->id }}">
                                    <i class="bi bi-pencil-square me-2"></i> Edit Student: {{ $std->name }} (#{{ $std->std_id ?: $std->id }})
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Student Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $std->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Father Name</label>
                                        <input type="text" name="father_name" class="form-control" value="{{ $std->father_name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <input type="email" name="email" class="form-control" value="{{ $std->email }}" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Must be a valid @gmail.com address">
                                        <small class="text-muted" style="font-size: 11px;">Must end with @gmail.com if updated.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <input type="text" name="phone_no" class="form-control" value="{{ $std->phone_no }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Emergency Contact</label>
                                        <input type="text" name="emergency_phone" class="form-control" value="{{ $std->emergency_phone }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Monthly Fee (PKR)</label>
                                        <input type="number" step="0.01" min="0" name="monthly_fee" class="form-control" value="{{ $std->monthly_fee ?? 0 }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Class</label>
                                        <select name="class" class="form-select" required>
                                            @foreach($classes as $c)
                                                <option value="{{ $c->class_name }}" {{ strcasecmp($std->class, $c->class_name) === 0 ? 'selected' : '' }}>
                                                    {{ $c->class_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Section</label>
                                        <select name="section" class="form-select" required>
                                            @foreach($sections as $sec)
                                                <option value="{{ $sec->section_name }}" {{ strcasecmp($std->section, $sec->section_name) === 0 ? 'selected' : '' }}>
                                                    {{ $sec->section_name }}
                                                </option>
                                            @endforeach
                                        </select>
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

        @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->total() > 0)
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

{{-- ADD NEW STUDENT MODAL --}}
<div class="modal fade" id="addNewStudentModal" tabindex="-1" aria-labelledby="addNewStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="margin-top: 1.5rem; margin-bottom: 2rem;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <form action="{{ url('add-student') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="admin_source" value="1">
                
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); padding: 16px 24px;">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="addNewStudentModalLabel">
                            <i class="bi bi-mortarboard-fill me-2"></i> Enroll New Student
                        </h5>
                        <p class="mb-0 text-white-50" style="font-size: 12.5px;">Register student profile, auto-create portal account, and initialize fee record.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto; padding-bottom: 40px;">
                    {{-- Basic Student Info --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lines-fill"></i> Personal & Guardian Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" placeholder="e.g. Hashaam" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name / Father Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" placeholder="e.g. Ahmad" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Student Login Gmail <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="hashaam.ahmad@gmail.com" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Must be a valid @gmail.com address" required>
                                <small class="text-muted" style="font-size: 11px;">Must end with @gmail.com (used for student portal login).</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Guardian Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone_no" class="form-control" placeholder="03xxxxxxxxx (11 digits)" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Emergency Contact Phone <small class="text-muted">(Optional)</small></label>
                                <input type="text" name="emergency_phone" class="form-control" placeholder="e.g. 03211234567">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Profile Photo</label>
                                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                    </div>

                    {{-- Class & Section Allocation --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-journal-bookmark-fill"></i> Academic Enrollment
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                                <select name="class" class="form-select" required>
                                    <option value="" disabled selected>-- Select Class --</option>
                                    @php
                                        $presetClasses = ['Pre-KG', 'KG', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN'];
                                        $dbClasses = isset($classes) ? $classes->pluck('class_name')->toArray() : [];
                                        $mergedClasses = array_values(array_unique(array_filter(array_merge($presetClasses, $dbClasses))));
                                    @endphp
                                    @foreach($mergedClasses as $cName)
                                        <option value="{{ $cName }}">{{ $cName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Section <span class="text-danger">*</span></label>
                                <select name="section" class="form-select" required>
                                    <option value="" disabled selected>-- Select Section --</option>
                                    @php
                                        $presetSections = ['Red', 'Blue', 'Pink', 'Green', 'Yellow', 'A', 'B'];
                                        $dbSections = isset($sections) ? $sections->pluck('section_name')->toArray() : [];
                                        $mergedSections = array_values(array_unique(array_filter(array_merge($presetSections, $dbSections))));
                                    @endphp
                                    @foreach($mergedSections as $secName)
                                        <option value="{{ $secName }}">{{ $secName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Roll No <small class="text-muted">(Optional - Auto If Empty)</small></label>
                                <input type="number" name="std_id" min="1" class="form-control" placeholder="Auto Next Roll">
                            </div>
                        </div>
                    </div>

                    {{-- FEE DECLARATION CARD --}}
                    <div class="card border-0 mb-3" style="background: linear-gradient(145deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #86EFAC !important; border-radius: 14px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-success border-opacity-25">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-success mb-0">Fee Structure & Initial Voucher</h6>
                                        <small class="text-muted" style="font-size: 11.5px;">Auto-creates Fee Collection voucher and reflects on student profile & portal.</small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1">Auto-Linked</span>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 12.5px;">Monthly Tuition Fee (PKR) <span class="text-danger">*</span></label>
                                    <input type="number" name="monthly_fee" class="form-control border-success" placeholder="3500" value="3500" min="0" step="50" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 12.5px;">Admission Fee (PKR)</label>
                                    <input type="number" name="admission_fee" class="form-control border-success" placeholder="5000" value="5000" min="0" step="50">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 12.5px;">Test Session Fee (PKR)</label>
                                    <input type="number" name="test_session_fee" class="form-control border-success" placeholder="500" value="500" min="0" step="50">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Login Account Note --}}
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                        <i class="bi bi-shield-lock-fill text-primary fs-3"></i>
                        <div style="font-size: 12.5px;">
                            <strong class="text-dark d-block">Automatic Student Portal Account</strong>
                            <span class="text-muted">A student portal login account will be automatically generated with temporary password <code>Student@123</code>.</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary px-4">
                        <i class="bi bi-check-circle-fill"></i> Save Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

