{{-- Metric Stats Grid (Matching Student Portal UI Cards) --}}
<section class="metric-grid">
    <a href="{{ route('admin.page', 'students') }}" class="metric-card-link">
        <article>
            <span class="metric-icon blue"><i class="bi bi-mortarboard-fill"></i></span>
            <small>TOTAL STUDENTS ENROLLED</small>
            <strong>{{ number_format($totalStudents ?? 0) }}</strong>
            <p>Active students assigned</p>
        </article>
    </a>

    <a href="{{ route('admin.page', 'teachers') }}" class="metric-card-link">
        <article>
            <span class="metric-icon green"><i class="bi bi-person-badge-fill"></i></span>
            <small>TEACHING STAFF MEMBERS</small>
            <strong>{{ number_format($totalTeachers ?? 0) }}</strong>
            <p>Full-time faculty members</p>
        </article>
    </a>

    <a href="{{ route('admin.page', 'leaves') }}" class="metric-card-link">
        <article>
            <span class="metric-icon amber"><i class="bi bi-calendar2-check-fill"></i></span>
            <small>PENDING LEAVES APPROVAL</small>
            <strong>{{ number_format($pendingLeavesCount ?? 0) }}</strong>
            <p>Awaiting head approval</p>
        </article>
    </a>

    <a href="{{ route('admin.page', 'fees') }}" class="metric-card-link">
        <article>
            <span class="metric-icon rose"><i class="bi bi-receipt"></i></span>
            <small>STUDENT FEES COLLECTED</small>
            <strong>Rs {{ number_format($collectedFees ?? 0, 0) }}</strong>
            <p>Total fees collected</p>
        </article>
    </a>

    <a href="{{ route('admin.page', 'salaries') }}" class="metric-card-link">
        <article>
            <span class="metric-icon teal"><i class="bi bi-cash-stack"></i></span>
            <small>TEACHER SALARIES PAID</small>
            <strong>Rs {{ number_format($salariesPaid ?? 0, 0) }}</strong>
            <p>Total salaries disbursed</p>
        </article>
    </a>
</section>

{{-- Quick Action Cards Row --}}
<div class="row g-4 mb-4">
    {{-- Pending Leaves Approval Card --}}
    <div class="col-lg-6">
        <div class="admin-card h-100 mb-0">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="bi bi-clock-history text-warning"></i> Pending Leaves Needing Approval
                </h3>
                <a href="{{ route('admin.page', 'leaves') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: var(--radius-sm); font-weight: 700;">
                    View All Leaves
                </a>
            </div>

            @if(($recentStudentLeaves->isEmpty()) && ($recentTeacherLeaves->isEmpty()))
                <div class="admin-empty-state py-4">
                    <i class="bi bi-check-circle text-success fs-2 display-6 d-block mb-2"></i>
                    <p class="mb-0">All leave applications are up to date!</p>
                </div>
            @else
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Type</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTeacherLeaves as $tLeave)
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark">{{ $tLeave->teacher_name }}</strong>
                                        <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">Teacher</span>
                                    </td>
                                    <td>{{ $tLeave->leave_type }}</td>
                                    <td><small class="text-muted">{{ Str::limit($tLeave->reason, 25) }}</small></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('admin.teacher-leave.approve', $tLeave->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success px-2 py-1" title="Approve" style="border-radius: 6px;">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.teacher-leave.reject', $tLeave->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Reject" style="border-radius: 6px;">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($recentStudentLeaves as $sLeave)
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark">{{ $sLeave->student_name }}</strong>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">{{ $sLeave->student_class }}</span>
                                    </td>
                                    <td>{{ $sLeave->leave_type }}</td>
                                    <td><small class="text-muted">{{ Str::limit($sLeave->reason, 25) }}</small></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('admin.student-leave.approve', $sLeave->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success px-2 py-1" title="Approve" style="border-radius: 6px;">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.student-leave.reject', $sLeave->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Reject" style="border-radius: 6px;">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- System Quick Links & Operations Card --}}
    <div class="col-lg-6">
        <div class="admin-card h-100 mb-0">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="bi bi-sliders text-primary"></i> Quick Administration Operations
                </h3>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <a href="{{ route('admin.page', 'salaries') }}" class="card text-decoration-none p-3 border-0 bg-light rounded-3 text-center shadow-sm h-100 transition-hover">
                        <i class="bi bi-cash-coin fs-2 text-success mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Declare Salaries</h6>
                        <small class="text-muted">Set & manage teacher salaries</small>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.page', 'fees') }}" class="card text-decoration-none p-3 border-0 bg-light rounded-3 text-center shadow-sm h-100 transition-hover">
                        <i class="bi bi-receipt-cutoff fs-2 text-primary mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Student Fees</h6>
                        <small class="text-muted">Add fees & track payments</small>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.page', 'students') }}" class="card text-decoration-none p-3 border-0 bg-light rounded-3 text-center shadow-sm h-100 transition-hover">
                        <i class="bi bi-mortarboard-fill fs-2 text-warning mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Students Profiles</h6>
                        <small class="text-muted">Full access to student data</small>
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('admin.page', 'teachers') }}" class="card text-decoration-none p-3 border-0 bg-light rounded-3 text-center shadow-sm h-100 transition-hover">
                        <i class="bi bi-person-badge-fill fs-2 text-info mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Teachers Profiles</h6>
                        <small class="text-muted">Full access to teacher data</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
