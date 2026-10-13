@php
    $isTeacherTab = request('tab') === 'teacher' || request()->has('teacher_page');
@endphp

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-file-earmark-check-fill text-primary"></i> Leave Approval & Management Center
        </h3>

        <div class="admin-filter-bar">
            <a href="{{ route('admin.page', ['page' => 'leaves', 'status' => 'all']) }}" class="admin-btn {{ $filterStatus === 'all' ? 'admin-btn-primary' : 'admin-btn-outline' }} admin-btn-sm">
                All Leaves
            </a>
            <a href="{{ route('admin.page', ['page' => 'leaves', 'status' => 'pending']) }}" class="admin-btn {{ $filterStatus === 'pending' ? 'admin-btn-primary' : 'admin-btn-outline' }} admin-btn-sm">
                Pending
                @if(isset($pendingLeavesCount) && $pendingLeavesCount > 0)
                    <span class="badge bg-danger ms-1">{{ $pendingLeavesCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.page', ['page' => 'leaves', 'status' => 'approved']) }}" class="admin-btn {{ $filterStatus === 'approved' ? 'admin-btn-primary' : 'admin-btn-outline' }} admin-btn-sm">
                Approved
            </a>
            <a href="{{ route('admin.page', ['page' => 'leaves', 'status' => 'rejected']) }}" class="admin-btn {{ $filterStatus === 'rejected' ? 'admin-btn-primary' : 'admin-btn-outline' }} admin-btn-sm">
                Rejected
            </a>
        </div>
    </div>

    <!-- Sub-tabs for Teacher Leaves vs Student Leaves -->
    <ul class="nav nav-pills mb-3 px-3 pt-3" id="leaveTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $isTeacherTab ? 'active' : '' }} fw-bold px-3 py-2" id="teacher-leaves-tab" data-bs-toggle="pill" data-bs-target="#teacher-leaves-panel" type="button" role="tab">
                <i class="bi bi-person-badge me-1"></i> Teacher Leaves ({{ $teacherLeaves instanceof \Illuminate\Pagination\LengthAwarePaginator ? $teacherLeaves->total() : count($teacherLeaves) }})
                @if(isset($pendingTeacherLeavesCount) && $pendingTeacherLeavesCount > 0)
                    <span class="badge bg-danger ms-1" style="font-size: 10px;">{{ $pendingTeacherLeavesCount }} Pending</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $isTeacherTab ? '' : 'active' }} fw-bold px-3 py-2" id="student-leaves-tab" data-bs-toggle="pill" data-bs-target="#student-leaves-panel" type="button" role="tab">
                <i class="bi bi-mortarboard me-1"></i> Student Leaves ({{ $studentLeaves instanceof \Illuminate\Pagination\LengthAwarePaginator ? $studentLeaves->total() : count($studentLeaves) }})
                @if(isset($pendingStudentLeavesCount) && $pendingStudentLeavesCount > 0)
                    <span class="badge bg-danger ms-1" style="font-size: 10px;">{{ $pendingStudentLeavesCount }} Pending</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content" id="leaveTabsContent">
        {{-- TEACHER LEAVES PANEL --}}
        <div class="tab-pane fade {{ $isTeacherTab ? 'show active' : '' }}" id="teacher-leaves-panel" role="tabpanel">
            @if($teacherLeaves->isEmpty())
                <div class="admin-empty-state">
                    <i class="bi bi-calendar-x admin-empty-icon"></i>
                    <p>No teacher leave records found matching current criteria.</p>
                </div>
            @else
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Leave Type</th>
                                <th>Leave Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherLeaves as $tLeave)
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark" style="font-size: 13px;">{{ $tLeave->teacher_name }}</strong>
                                        <small class="text-muted">ID: #{{ $tLeave->teacher_id }}</small>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span class="badge bg-info-subtle text-info-emphasis fw-bold" style="font-size: 10.5px;">
                                            {{ $tLeave->leave_type }}
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <small class="fw-bold d-block text-dark">{{ $tLeave->leave_date ? Carbon\Carbon::parse($tLeave->leave_date)->format('M d, Y') : '-' }}</small>
                                    </td>
                                    <td>
                                        <span style="max-width: 100px; display: inline-block; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $tLeave->reason }}">
                                            {{ $tLeave->reason }}
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span class="admin-badge {{ strtolower($tLeave->status) }}">
                                            {{ $tLeave->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-secondary fw-semibold" style="max-width: 110px; display: inline-block; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $tLeave->approved_by ?: 'Not Actioned' }}">
                                            {{ $tLeave->approved_by ?: 'Not Actioned' }}
                                        </small>
                                    </td>
                                    <td class="text-center" style="white-space: nowrap; width: 1%;">
                                        @if(strtolower($tLeave->status) === 'pending')
                                            <div class="d-inline-flex gap-1 justify-content-center">
                                                <form action="{{ route('admin.teacher-leave.approve', $tLeave->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="admin-btn admin-btn-success admin-btn-xs" style="padding: 4px 8px; font-size: 11px;">
                                                        <i class="bi bi-check-circle-fill"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.teacher-leave.reject', $tLeave->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-xs" style="padding: 4px 8px; font-size: 11px;">
                                                        <i class="bi bi-x-circle-fill"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif(strtolower($tLeave->status) === 'approved')
                                            <form action="{{ route('admin.teacher-leave.reject', $tLeave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject Leave" style="padding: 3px 8px; font-size: 11px; font-weight: 700; border-radius: 6px;">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </form>
                                        @elseif(strtolower($tLeave->status) === 'rejected')
                                            <form action="{{ route('admin.teacher-leave.approve', $tLeave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve Leave" style="padding: 3px 8px; font-size: 11px; font-weight: 700; border-radius: 6px;">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($teacherLeaves instanceof \Illuminate\Pagination\LengthAwarePaginator && $teacherLeaves->hasPages())
                    <div class="admin-pagination-bar px-3 pb-3">
                        <div class="admin-pagination-info">
                            Showing <strong>{{ $teacherLeaves->firstItem() }}</strong> to <strong>{{ $teacherLeaves->lastItem() }}</strong> of <strong>{{ $teacherLeaves->total() }}</strong> teacher leaves
                        </div>
                        <div class="admin-pagination-links">
                            <ul class="pagination mb-0">
                                @if ($teacherLeaves->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $teacherLeaves->previousPageUrl() }}&tab=teacher"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach ($teacherLeaves->getUrlRange(1, $teacherLeaves->lastPage()) as $page => $url)
                                    @if ($page == $teacherLeaves->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif ($page == 1 || $page == $teacherLeaves->lastPage() || abs($page - $teacherLeaves->currentPage()) <= 2)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}&tab=teacher">{{ $page }}</a></li>
                                    @elseif (abs($page - $teacherLeaves->currentPage()) == 3)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if ($teacherLeaves->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $teacherLeaves->nextPageUrl() }}&tab=teacher"><i class="bi bi-chevron-right"></i></a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- STUDENT LEAVES PANEL --}}
        <div class="tab-pane fade {{ $isTeacherTab ? '' : 'show active' }}" id="student-leaves-panel" role="tabpanel">
            @if($studentLeaves->isEmpty())
                <div class="admin-empty-state">
                    <i class="bi bi-calendar-x admin-empty-icon"></i>
                    <p>No student leave records found matching current criteria.</p>
                </div>
            @else
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Class & Section</th>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentLeaves as $sLeave)
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark" style="font-size: 13px;">{{ $sLeave->student_name }}</strong>
                                        <small class="text-muted">Roll #{{ $sLeave->std_id ?: $sLeave->student_id }}</small>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span class="badge bg-secondary-subtle text-dark fw-bold" style="font-size: 10.5px;">
                                            {{ $sLeave->student_class }} - {{ $sLeave->student_section ?: 'Gen' }}
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 10.5px;">
                                            {{ $sLeave->leave_type }}
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <small class="fw-bold d-block text-dark">{{ $sLeave->leave_date ? $sLeave->leave_date->format('M d, Y') : '-' }}</small>
                                        @if($sLeave->end_date && $sLeave->end_date->gt($sLeave->leave_date))
                                            <small class="text-muted d-block" style="font-size: 10px;">to {{ $sLeave->end_date->format('M d, Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="max-width: 100px; display: inline-block; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $sLeave->reason }}">
                                            {{ $sLeave->reason }}
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span class="admin-badge {{ strtolower($sLeave->status) }}">
                                            {{ $sLeave->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-secondary fw-semibold" style="max-width: 110px; display: inline-block; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $sLeave->approved_by ?: 'Pending' }}">
                                            {{ $sLeave->approved_by ?: 'Pending' }}
                                        </small>
                                    </td>
                                    <td class="text-center" style="white-space: nowrap; width: 1%;">
                                        @if(strtolower($sLeave->status) === 'pending')
                                            <div class="d-inline-flex gap-1 justify-content-center">
                                                <form action="{{ route('admin.student-leave.approve', $sLeave->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="admin-btn admin-btn-success admin-btn-xs" style="padding: 4px 8px; font-size: 11px;">
                                                        <i class="bi bi-check-circle-fill"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.student-leave.reject', $sLeave->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-xs" style="padding: 4px 8px; font-size: 11px;">
                                                        <i class="bi bi-x-circle-fill"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif(strtolower($sLeave->status) === 'approved')
                                            <form action="{{ route('admin.student-leave.reject', $sLeave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject Leave" style="padding: 3px 8px; font-size: 11px; font-weight: 700; border-radius: 6px;">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </form>
                                        @elseif(strtolower($sLeave->status) === 'rejected')
                                            <form action="{{ route('admin.student-leave.approve', $sLeave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve Leave" style="padding: 3px 8px; font-size: 11px; font-weight: 700; border-radius: 6px;">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($studentLeaves instanceof \Illuminate\Pagination\LengthAwarePaginator && $studentLeaves->hasPages())
                    <div class="admin-pagination-bar px-3 pb-3">
                        <div class="admin-pagination-info">
                            Showing <strong>{{ $studentLeaves->firstItem() }}</strong> to <strong>{{ $studentLeaves->lastItem() }}</strong> of <strong>{{ $studentLeaves->total() }}</strong> student leaves
                        </div>
                        <div class="admin-pagination-links">
                            <ul class="pagination mb-0">
                                @if ($studentLeaves->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $studentLeaves->previousPageUrl() }}&tab=student"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach ($studentLeaves->getUrlRange(1, $studentLeaves->lastPage()) as $page => $url)
                                    @if ($page == $studentLeaves->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif ($page == 1 || $page == $studentLeaves->lastPage() || abs($page - $studentLeaves->currentPage()) <= 2)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}&tab=student">{{ $page }}</a></li>
                                    @elseif (abs($page - $studentLeaves->currentPage()) == 3)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if ($studentLeaves->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $studentLeaves->nextPageUrl() }}&tab=student"><i class="bi bi-chevron-right"></i></a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>


