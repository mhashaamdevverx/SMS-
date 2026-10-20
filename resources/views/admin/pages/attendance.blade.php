<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-clock-history text-primary"></i> Attendance Logs & Daily Register
        </h3>

        <form method="GET" action="{{ route('admin.page', 'attendance') }}" class="admin-filter-bar d-flex align-items-center gap-2">
            {{-- Date Picker --}}
            <div style="position: relative; display: inline-flex; align-items: center;">
                <input type="date" name="date" class="form-control form-control-sm admin-input" value="{{ $selectedDate ?? date('Y-m-d') }}" onchange="this.form.submit()" style="width: auto; min-width: 145px; height: 38px; border-radius: 10px; font-weight: 600; border: 1.5px solid var(--border); background: var(--surface); color: var(--heading); padding: 0 12px;">
            </div>

            {{-- Class Dropdown with Explicit Chevron --}}
            <div style="position: relative; display: inline-flex; align-items: center;">
                <select name="class" class="form-select form-select-sm admin-select" onchange="this.form.submit()" style="width: auto; min-width: 155px; height: 38px; border-radius: 10px; font-weight: 600; border: 1.5px solid var(--border); background-color: var(--surface); color: var(--heading); padding-left: 14px; padding-right: 32px; cursor: pointer;">
                    <option value="">-- All Classes --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->class_name }}" {{ ($selectedClass ?? '') == $c->class_name ? 'selected' : '' }}>
                            {{ $c->class_name }}
                        </option>
                    @endforeach
                </select>
                <i class="bi bi-chevron-down" style="position: absolute; right: 12px; font-size: 11px; color: var(--muted); pointer-events: none;"></i>
            </div>

            {{-- Section Dropdown with Explicit Chevron --}}
            <div style="position: relative; display: inline-flex; align-items: center;">
                <select name="section" class="form-select form-select-sm admin-select" onchange="this.form.submit()" style="width: auto; min-width: 145px; height: 38px; border-radius: 10px; font-weight: 600; border: 1.5px solid var(--border); background-color: var(--surface); color: var(--heading); padding-left: 14px; padding-right: 32px; cursor: pointer;">
                    <option value="">-- All Sections --</option>
                    @if(isset($sections))
                        @foreach($sections as $sec)
                            <option value="{{ $sec->section_name }}" {{ ($selectedSection ?? '') == $sec->section_name ? 'selected' : '' }}>
                                {{ $sec->section_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <i class="bi bi-chevron-down" style="position: absolute; right: 12px; font-size: 11px; color: var(--muted); pointer-events: none;"></i>
            </div>

            <a href="/attendance" class="admin-btn admin-btn-outline admin-btn-sm" style="height: 38px; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="bi bi-pencil-square"></i> Open Attendance Sheet
            </a>
        </form>
    </div>

    <!-- Attendance Tabs -->
    <ul class="nav nav-pills mb-3" id="attendanceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4" id="teacher-attendance-tab" data-bs-toggle="pill" data-bs-target="#teacher-attendance-panel" type="button" role="tab">
                <i class="bi bi-person-badge me-2"></i> Teacher Punch Logs ({{ count($teacherAttendance) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4" id="student-attendance-tab" data-bs-toggle="pill" data-bs-target="#student-attendance-panel" type="button" role="tab">
                <i class="bi bi-mortarboard me-2"></i> Student Daily Records ({{ count($studentAttendance) }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="attendanceTabsContent">
        {{-- TEACHERS ATTENDANCE PANEL --}}
        <div class="tab-pane fade show active" id="teacher-attendance-panel" role="tabpanel">
            @if($teacherAttendance->isEmpty())
                <div class="admin-empty-state">
                    <i class="bi bi-clock-slash admin-empty-icon"></i>
                    <p>No teacher attendance punch logs recorded for {{ Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}.</p>
                </div>
            @else
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Date</th>
                                <th>Punch In</th>
                                <th>Punch Out</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherAttendance as $tAtt)
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark">{{ $tAtt->teacher?->teacher_name ?? 'Teacher' }}</strong>
                                        <small class="text-muted">{{ $tAtt->teacher?->course ?: 'Staff' }}</small>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($tAtt->date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success fw-bold">
                                            <i class="bi bi-box-arrow-in-right"></i> {{ $tAtt->punch_in ? Carbon\Carbon::parse($tAtt->punch_in)->format('h:i A') : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger fw-bold">
                                            <i class="bi bi-box-arrow-right"></i> {{ $tAtt->punch_out ? Carbon\Carbon::parse($tAtt->punch_out)->format('h:i A') : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ strtolower($tAtt->status) }}">
                                            {{ $tAtt->status }}
                                        </span>
                                    </td>
                                    <td><small class="text-muted">{{ $tAtt->remarks ?: '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- STUDENTS ATTENDANCE PANEL --}}
        <div class="tab-pane fade" id="student-attendance-panel" role="tabpanel">
            @if($studentAttendance->isEmpty())
                <div class="admin-empty-state">
                    <i class="bi bi-calendar-x admin-empty-icon"></i>
                    <p>No student attendance records marked for {{ Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}.</p>
                </div>
            @else
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Class & Section</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentAttendance as $sAtt)
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark">ID #{{ $sAtt->student_id }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-dark">
                                            {{ $sAtt->class_name }} - {{ $sAtt->section_name ?: 'Gen' }}
                                        </span>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($sAtt->date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="admin-badge {{ strtolower($sAtt->status) }}">
                                            {{ $sAtt->status }}
                                        </span>
                                    </td>
                                    <td><small class="text-muted">{{ $sAtt->remarks ?: '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
