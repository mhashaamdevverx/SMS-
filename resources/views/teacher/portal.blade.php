@extends('teacher-layout')
@section('content')

@if(session('success'))
    <div class="student-toast"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="student-toast" style="background:#fef2f2;color:#991b1b;border-color:#fecaca;"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
@endif

@php
    $classFilterHtml = function() use ($myClasses, $primaryClass, $page) {
        if ($myClasses->count() <= 1) return '';
        $html = '<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 8px rgba(15,23,42,0.03);">';
        $html .= '<div style="display:flex;align-items:center;gap:10px;"><i class="bi bi-funnel-fill" style="color:#4F46E5;font-size:16px;"></i><strong style="font-size:13px;color:#0f172a;">Active Class Filter:</strong><span style="font-size:12px;color:#64748b;">(You are assigned to multiple classes)</span></div>';
        $html .= '<form method="GET" action="' . ($page === 'dashboard' ? route('teacher.dashboard') : route('teacher.page', $page)) . '" style="margin:0;display:flex;align-items:center;gap:8px;">';
        $html .= '<select name="class" onchange="this.form.submit()" style="padding:8px 14px;border:1px solid #4F46E5;border-radius:10px;font-size:13px;font-weight:700;color:#1E1B4B;background:#EEF2FF;cursor:pointer;">';
        foreach ($myClasses as $c) {
            $sel = strtolower(trim($c['class_name'])) === strtolower(trim($primaryClass)) ? 'selected' : '';
            $html .= "<option value=\"{$c['class_name']}\" {$sel}>Class: {$c['label']}</option>";
        }
        $html .= '</select>';
        $html .= '</form></div>';
        return $html;
    };
@endphp

@if($page === 'dashboard')
<section class="student-hero">
    <div>
        <span class="hero-pill">Excellence Teacher Portal <i></i> Session Active</span>
        <h1>Welcome back, {{ $teacher->teacher_name }}!</h1>
        <p>Active Class: <strong>{{ $primaryClass }} ({{ $primarySection }})</strong> <i></i> Subjects: <strong>{{ $mySubjects->implode(', ') }}</strong> <i></i> Reg Email: <strong>{{ $user->email }}</strong> <i></i> Keep guiding and inspiring.</p>
    </div>
    <a href="javascript:void(0)" onclick="document.getElementById('uploadOutlineModal').style.display='grid'" class="hero-action"><i class="bi bi-upload"></i> Upload Outline</a>
    <div class="hero-tabs">
        @foreach(['dashboard'=>'Dashboard', 'profile'=>'Profile', 'attendance'=>'My Attendance', 'coteachers'=>'Class Co-Teachers', 'students'=>'My Class Students', 'results'=>'Class Results', 'outlines'=>'Course Outlines', 'salary'=>'My Salary'] as $key => $label)
            <a href="{{ $key === 'dashboard' ? route('teacher.dashboard') : route('teacher.page', $key) }}" class="{{ $key === $page ? 'selected' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
</section>

{!! $classFilterHtml() !!}

<section class="metric-grid">
    <a href="{{ route('teacher.page', 'students') }}" class="metric-card-link">
        <article>
            <span class="metric-icon blue"><i class="bi bi-building"></i></span>
            <small>ASSIGNED CLASS</small>
            <strong>{{ $primaryClass }}</strong>
            <p>Section {{ $primarySection }} &bull; {{ $studentCount }} Enrolled Students</p>
        </article>
    </a>
    <a href="{{ route('teacher.page', 'coteachers') }}" class="metric-card-link">
        <article>
            <span class="metric-icon green"><i class="bi bi-people"></i></span>
            <small>CLASS CO-TEACHERS</small>
            <strong>{{ $coTeacherAssignments->pluck('teacher_id')->unique()->count() }}</strong>
            <p>Subject Specialists in {{ $primaryClass }}</p>
        </article>
    </a>
    <a href="{{ route('teacher.page', 'salary') }}" class="metric-card-link">
        <article class="fee-card">
            <span class="metric-icon amber"><i class="bi bi-cash-stack"></i></span>
            <small>MY MONTHLY SALARY</small>
            <strong>Rs {{ number_format($latestSalary?->net_salary ?? 50000) }}</strong>
            <p>{{ $latestSalary?->status ?? 'Paid' }} &bull; Oct 2026</p>
        </article>
    </a>
    <a href="{{ route('teacher.page', 'attendance') }}" class="metric-card-link">
        <article>
            <span class="metric-icon purple"><i class="bi bi-calendar2-check"></i></span>
            <small>{{ strtoupper($activeMonthName) }} ATTENDANCE RATE</small>
            <strong style="color:#4F46E5;">{{ $monthPct }}%</strong>
            <p>{{ $monthPresent }} Present &bull; {{ $monthLate }} Late &bull; {{ $monthLeave }} Leaves</p>
        </article>
    </a>
</section>

<section class="student-dashboard-grid">
    <article class="student-panel">
        <div class="panel-head">
            <div>
                <h2>{{ $primaryClass }} {{ $primarySection }} &bull; Class Teachers & Subjects</h2>
                <p>Teachers assigned to {{ $primaryClass }} ({{ $primarySection }}) and their respective subjects</p>
            </div>
            <a href="{{ route('teacher.page', 'coteachers') }}">View all</a>
        </div>
        <div class="table-panel">
            <table>
                <thead>
                    <tr>
                        <th>Teacher Name</th>
                        <th>Subject Assigned</th>
                        <th>Class & Sec</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coTeacherAssignments as $ca)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:#C7D2FE;color:#0f766e;display:grid;place-items:center;font-weight:800;font-size:13px;">
                                    {{ strtoupper(substr($ca->teacher?->teacher_name ?? 'T', 0, 1)) }}
                                </div>
                                <div>
                                    <strong style="color:#0f172a;display:block;">{{ $ca->teacher?->teacher_name ?? "Ma'am Fatima Zahra" }}</strong>
                                    @if($ca->teacher_id == $teacher->id)
                                        <span style="font-size:10px;background:#C7D2FE;color:#0f766e;padding:2px 6px;border-radius:4px;font-weight:700;">(You)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span style="font-weight:700;color:#4F46E5;">{{ $ca->subject_or_course }}</span></td>
                        <td>{{ $ca->class_name }} ({{ $ca->section_name }})</td>
                        <td>{{ $ca->teacher?->teacher_phone ?: '0300-1234567' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#64748b;">No co-teachers assigned yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <div class="student-right">
        <!-- Monthly Attendance Summary Panel on Dashboard -->
        <article class="mini-panel" style="border-left:4px solid #4F46E5;">
            <h3 style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="font-size:14px;font-weight:800;color:#0f172a;"><i class="bi bi-clock-history" style="color:#4F46E5;"></i> {{ $activeMonthName }} Attendance</span>
                <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:8px;font-size:12px;font-weight:800;">{{ $monthPct }}%</span>
            </h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;margin-bottom:16px;">
                <div style="background:#f8fafc;padding:8px 10px;border-radius:8px;border:1px solid #e2e8f0;">
                    <span style="color:#64748b;font-size:10px;display:block;text-transform:uppercase;font-weight:700;">Present</span>
                    <strong style="color:#059669;font-size:13.5px;">{{ $monthPresent }} Days</strong>
                </div>
                <div style="background:#f8fafc;padding:8px 10px;border-radius:8px;border:1px solid #e2e8f0;">
                    <span style="color:#64748b;font-size:10px;display:block;text-transform:uppercase;font-weight:700;">Late</span>
                    <strong style="color:#dc2626;font-size:13.5px;">{{ $monthLate }} Days</strong>
                </div>
                <div style="background:#f8fafc;padding:8px 10px;border-radius:8px;border:1px solid #e2e8f0;">
                    <span style="color:#64748b;font-size:10px;display:block;text-transform:uppercase;font-weight:700;">Leaves</span>
                    <strong style="color:#0284c7;font-size:13.5px;">{{ $monthLeave }} Days</strong>
                </div>
                <div style="background:#f8fafc;padding:8px 10px;border-radius:8px;border:1px solid #e2e8f0;">
                    <span style="color:#64748b;font-size:10px;display:block;text-transform:uppercase;font-weight:700;">Absences</span>
                    <strong style="color:#b91c1c;font-size:13.5px;">{{ $monthAbsent }} Days</strong>
                </div>
            </div>
            <a href="{{ route('teacher.page', 'attendance') }}" style="display:inline-block;width:100%;text-align:center;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#fff;padding:10px;border-radius:10px;text-decoration:none;font-weight:800;font-size:13px;box-shadow:0 4px 12px rgba(79,70,229,0.25);">
                Open Attendance Terminal <i class="bi bi-arrow-right-short"></i>
            </a>
        </article>

        <article class="mini-panel">
            <h3><i class="bi bi-backpack-fill" style="color:#4F46E5;"></i> Enrolled Students</h3>
            <p style="font-size:12.5px;color:#64748b;margin:0 0 16px;">You have {{ $studentCount }} enrolled students in {{ $primaryClass }} {{ $primarySection }}.</p>
            <a href="{{ route('teacher.page', 'students') }}" style="display:inline-block;width:100%;text-align:center;background:#4F46E5;color:#fff;padding:10px;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px;">View Class Roster</a>
        </article>

        <article class="mini-panel">
            <h3><i class="bi bi-cash" style="color:#059669;"></i> My Salary Overview</h3>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <span style="font-size:13px;color:#64748b;">Net Salary:</span>
                <strong style="font-size:16px;color:#059669;">Rs {{ number_format($latestSalary?->net_salary ?? 50000) }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <span style="font-size:13px;color:#64748b;">Status:</span>
                <span class="status"><i class="bi bi-check-circle-fill"></i> {{ $latestSalary?->status ?? 'Paid' }}</span>
            </div>
            <a href="{{ route('teacher.page', 'salary') }}" style="display:inline-block;width:100%;text-align:center;background:#f1f5f9;color:#0f172a;padding:10px;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px;">View Pay Slip</a>
        </article>
    </div>
</section>
@endif

@if($page === 'profile')
<div class="page-heading">
    <p>EXCELLENCE TEACHER PORTAL &bull; PROFILE</p>
    <h1>Teacher Profile & Account Settings</h1>
</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:24px;">
    <article class="student-panel" style="text-align:center;padding:32px 24px;">
        <div style="width:90px;height:90px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-size:36px;font-weight:800;margin:0 auto 16px;border:4px solid #C7D2FE;box-shadow:0 8px 20px rgba(79,70,229,0.2);">
            {{ strtoupper(substr($teacher->teacher_name, 0, 1)) }}
        </div>
        <h2 style="margin:0;font-size:20px;font-weight:800;color:#0f172a;">{{ $teacher->teacher_name }}</h2>
        <p style="margin:4px 0 16px;color:#4F46E5;font-weight:700;font-size:13.5px;">Subject Specialist &bull; Teacher</p>
        
        <dl style="display:flex;flex-direction:column;gap:10px;margin:0;text-align:left;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Full Name</dt>
                <dd style="margin:0;font-weight:700;color:#0f172a;font-size:13.5px;">{{ $teacher->teacher_name }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Email</dt>
                <dd style="margin:0;font-weight:700;color:#0f172a;font-size:13.5px;">{{ $user->email }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Assigned Classes</dt>
                <dd style="margin:0;font-weight:700;color:#4F46E5;font-size:13.5px;">{{ $myClasses->pluck('label')->implode(', ') }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Phone</dt>
                <dd style="margin:0;font-weight:700;color:#0f172a;font-size:13.5px;">{{ $teacher->teacher_phone ?: '0300-1234567' }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#EEF2FF;border-radius:10px;border:1px solid #C7D2FE;">
                <dt style="color:#0f766e;font-size:13px;font-weight:700;">Status</dt>
                <dd style="margin:0;font-weight:800;color:#4F46E5;font-size:14px;">Active Staff</dd>
            </div>
        </dl>
    </article>

    <article class="student-panel" style="padding:28px;">
        <h3 style="margin:0 0 20px;font-size:18px;font-weight:800;color:#0f172a;border-bottom:1px solid #f1f5f9;padding-bottom:12px;"><i class="bi bi-shield-lock" style="color:#4F46E5;"></i> Account Security & Settings</h3>
        
        <form action="{{ route('teacher.profile.update') }}" method="POST" style="margin-bottom:28px;">
            @csrf
            @method('PUT')
            <h4 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;">Edit Profile Information</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Full Name</label>
                    <input type="text" name="teacher_name" value="{{ $teacher->teacher_name }}" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Phone Number</label>
                    <input type="text" name="teacher_phone" value="{{ $teacher->teacher_phone ?: '0300-1234567' }}" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                </div>
            </div>
            <button type="submit" style="background:#4F46E5;color:#fff;border:none;padding:10px 20px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;">Update Profile</button>
        </form>

        <form action="{{ route('teacher.password.update') }}" method="POST">
            @csrf
            <h4 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;border-top:1px solid #f1f5f9;padding-top:20px;">Change Password</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Current Password</label>
                    <input type="password" name="current_password" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">New Password</label>
                    <input type="password" name="new_password" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                </div>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" required style="width:100%;max-width:300px;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
            </div>
            <button type="submit" style="background:#0f172a;color:#fff;border:none;padding:10px 20px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;">Update Password</button>
        </form>
    </article>
</div>
@endif

@if($page === 'coteachers')
<div class="page-heading">
    <p>EXCELLENCE TEACHER PORTAL &bull; CLASS & SUBJECTS</p>
    <h1>{{ $primaryClass }} {{ $primarySection }} &bull; Class Co-Teachers</h1>
</div>

{!! $classFilterHtml() !!}

<div class="table-panel">
    <table>
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Assigned Subject</th>
                <th>Class / Section</th>
                <th>Contact Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coTeacherAssignments as $ca)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:15px;">
                            {{ strtoupper(substr($ca->teacher?->teacher_name ?? 'T', 0, 1)) }}
                        </div>
                        <div>
                            <strong style="color:#0f172a;font-size:14px;display:block;">{{ $ca->teacher?->teacher_name ?? "Ma'am Fatima Zahra" }}</strong>
                            <span style="font-size:11px;color:#64748b;">Subject Specialist</span>
                            @if($ca->teacher_id == $teacher->id)
                                <span style="font-size:10px;background:#C7D2FE;color:#0f766e;padding:2px 6px;border-radius:4px;font-weight:700;margin-left:4px;">(Logged in)</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td><strong style="color:#4F46E5;font-size:14px;">{{ $ca->subject_or_course }}</strong></td>
                <td>{{ $ca->class_name }} ({{ $ca->section_name }})</td>
                <td><i class="bi bi-telephone" style="color:#64748b;margin-right:4px;"></i> {{ $ca->teacher?->teacher_phone ?: '0300-1234567' }}</td>
                <td><span class="status"><i class="bi bi-check-circle-fill"></i> Active</span></td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:20px;color:#64748b;">No co-teachers assigned for this class.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@if($page === 'students')
<div class="page-heading">
    <p>EXCELLENCE TEACHER PORTAL &bull; STUDENTS ROSTER</p>
    <h1>{{ $primaryClass }} {{ $primarySection }} &bull; Enrolled Students</h1>
</div>

{!! $classFilterHtml() !!}

<div class="table-panel">
    <table>
        <thead>
            <tr>
                <th>Roll #</th>
                <th>Student Name</th>
                <th>Registration No</th>
                <th>Class & Section</th>
                <th>Emergency Contact</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $std)
            @php
                $regNo = $std->registration_no ?: ('REG-2026-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $std->class ?? 'STD')).'-'.str_pad($std->std_id ?? 1, 3, '0', STR_PAD_LEFT));
            @endphp
            <tr>
                <td><strong style="color:#4F46E5;font-size:14px;">#{{ $std->std_id }}</strong></td>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#0f172a;color:#fff;display:grid;place-items:center;font-weight:800;font-size:14px;">
                            {{ strtoupper(substr($std->name, 0, 1)) }}
                        </div>
                        <div>
                            <strong style="color:#0f172a;font-size:14px;display:block;">{{ $std->name }}</strong>
                            <span style="font-size:11px;color:#64748b;">Enrolled Student</span>
                        </div>
                    </div>
                </td>
                <td><span style="font-family:monospace;font-size:12.5px;font-weight:700;color:#475569;">{{ $regNo }}</span></td>
                <td><strong>{{ $std->class }}</strong> ({{ $std->section }})</td>
                <td><i class="bi bi-telephone" style="color:#64748b;margin-right:4px;"></i> {{ $std->emergency_phone ?: ($std->phone_no ?: '0300-9876543') }}</td>
                <td><span class="status"><i class="bi bi-check-circle-fill"></i> Active Student</span></td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#64748b;">No students enrolled in {{ $primaryClass }} {{ $primarySection }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@if($page === 'stdleaves')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:14px;">
    <div>
        <p>EXCELLENCE TEACHER PORTAL &bull; STUDENT LEAVE APPLICATIONS</p>
        <h1>{{ $primaryClass }} ({{ $primarySection }}) &bull; Student Leave Requests</h1>
    </div>
</div>

{!! $classFilterHtml() !!}

<!-- View Mode Toggle & Month Filter Bar (Exact Match with UI Design) -->
<div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;background:#ffffff;border:1px solid #e2e8f0;padding:16px 20px;border-radius:18px;box-shadow:0 4px 14px rgba(0,0,0,0.03);">
    <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:13px;font-weight:700;color:#64748b;">View Mode:</span>
        <a href="{{ route('teacher.page', ['page' => 'stdleaves', 'class' => $primaryClass, 'leave_view' => 'monthly', 'leave_month' => $selectedLeaveMonth ?? 'all']) }}" 
           style="padding:9px 20px;border-radius:12px;font-size:13px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:8px;{{ ($selectedLeaveView ?? 'monthly') !== 'yearly' ? 'background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;box-shadow:0 4px 12px rgba(79,70,229,0.3);' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;' }}">
            <i class="bi bi-calendar3"></i> Monthly Daily Logs
        </a>
        <a href="{{ route('teacher.page', ['page' => 'stdleaves', 'class' => $primaryClass, 'leave_view' => 'yearly']) }}" 
           style="padding:9px 20px;border-radius:12px;font-size:13px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:8px;{{ ($selectedLeaveView ?? 'monthly') === 'yearly' ? 'background:linear-gradient(135deg,#107c41,#0b5a2f);color:#ffffff;box-shadow:0 4px 12px rgba(16,124,65,0.3);' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;' }}">
            <i class="bi bi-grid-3x3-gap-fill"></i> Yearly 12-Month Summary
        </a>
    </div>

    @if(($selectedLeaveView ?? 'monthly') !== 'yearly')
    <form method="GET" action="{{ route('teacher.page', 'stdleaves') }}" id="leaveMonthFilterForm" style="margin:0;display:flex;align-items:center;gap:10px;">
        <input type="hidden" name="class" value="{{ $primaryClass }}">
        <input type="hidden" name="leave_view" value="monthly">
        
        <label style="font-size:12.5px;font-weight:700;color:#475569;">Select Month:</label>
        <select name="leave_month" onchange="document.getElementById('leaveMonthFilterForm').submit()" style="padding:9px 16px;border:1.5px solid #4F46E5;border-radius:10px;font-size:13px;font-weight:800;color:#0f172a;background:#EEF2FF;cursor:pointer;">
            <option value="all" {{ ($selectedLeaveMonth ?? 'all') === 'all' ? 'selected' : '' }}>All 12 Months (Full Year)</option>
            @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mName)
                <option value="{{ $mName }}" {{ ($selectedLeaveMonth ?? 'all') === $mName ? 'selected' : '' }}>{{ $mName }}</option>
            @endforeach
        </select>

        @if(($selectedLeaveMonth ?? 'all') !== 'all')
            <a href="{{ route('teacher.page', ['page' => 'stdleaves', 'class' => $primaryClass, 'leave_view' => 'monthly']) }}" style="padding:9px 14px;border-radius:10px;background:#e2e8f0;color:#334155;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        @endif
    </form>
    @endif
</div>

<!-- Class In-Charge Authorization Notice Banner -->
<div style="margin-bottom: 24px; padding: 18px 22px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px; box-shadow: 0 4px 14px rgba(0, 30, 25, 0.06); 
    @if($isClassIncharge) background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1.5px solid #a7f3d0; @else background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); border: 1.5px solid #bfdbfe; @endif">
    <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; font-size: 22px; 
            @if($isClassIncharge) background: #10b981; color: #fff; @else background: #3b82f6; color: #fff; @endif">
            <i class="bi @if($isClassIncharge) bi-shield-check @else bi-eye-fill @endif"></i>
        </div>
        <div>
            <h4 style="margin: 0; font-size: 15px; font-weight: 800; @if($isClassIncharge) color: #065f46; @else color: #1e40af; @endif">
                @if($isClassIncharge)
                    Class In-Charge Authorization Active
                @else
                    Read-Only Access Notice
                @endif
            </h4>
            <p style="margin: 3px 0 0; font-size: 13px; @if($isClassIncharge) color: #047857; @else color: #1d4ed8; @endif">
                @if($isClassIncharge)
                    You are the designated <b>Class In-Charge</b> for <b>{{ $primaryClass }} ({{ $primarySection }})</b>. You have full rights to <b>Approve</b> or <b>Reject</b> student leave applications and sync them to attendance.
                @else
                    You are viewing in <b>Read-Only Mode</b>. Only the designated Class In-Charge (<b>{{ $classInchargeTeacherName }}</b>) or Admin can approve or reject leave requests for {{ $primaryClass }}.
                @endif
            </p>
        </div>
    </div>
    <div>
        <span style="padding: 6px 14px; border-radius: 99px; font-size: 12.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 6px; 
            @if($isClassIncharge) background: #d1fae5; color: #047857; border: 1px solid #6ee7b7; @else background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; @endif">
            <i class="bi @if($isClassIncharge) bi-check-circle-fill @else bi-info-circle-fill @endif"></i>
            {{ $isClassIncharge ? 'Full Admin Rights' : 'Read-Only View' }}
        </span>
    </div>
</div>

@if(($selectedLeaveView ?? 'monthly') === 'yearly')
<!-- SECTION: 12-Month Yearly Summary Matrix Grid for Student Leaves -->
<div class="student-panel" style="margin-bottom:28px;padding:26px;">
    <div class="panel-head" style="margin-bottom:20px;border-bottom:1px solid #f1f5f9;padding-bottom:14px;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h2><i class="bi bi-calendar-range-fill" style="color:#107c41;"></i> 2026 Yearly 12-Month Student Leaves Matrix Summary</h2>
            <p>Click on any month card below to view daily leave applications for {{ $primaryClass }} ({{ $primarySection }}).</p>
        </div>
        <div>
            <span style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:800;">
                <i class="bi bi-calendar3"></i> 12 Months Overview
            </span>
        </div>
    </div>

    <!-- 12-Month Cards Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
        @foreach($yearlyLeaveSummaries ?? [] as $ms)
        <a href="{{ route('teacher.page', ['page' => 'stdleaves', 'class' => $primaryClass, 'leave_view' => 'monthly', 'leave_month' => $ms['month_name']]) }}" style="text-decoration:none;display:block;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:18px;box-shadow:0 3px 10px rgba(0,0,0,0.03);transition:all 0.2s ease;position:relative;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="font-size:14.5px;font-weight:800;color:#0f172a;">{{ $ms['month_name'] }}</span>
                <span style="font-size:12px;font-weight:800;color:#4F46E5;background:#C7D2FE;padding:4px 10px;border-radius:99px;">{{ $ms['total_leaves'] }} Leaves</span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;font-size:11px;text-align:center;">
                <div style="background:#f0fdf4;padding:6px 4px;border-radius:8px;border:1px solid #dcfce7;">
                    <span style="display:block;color:#166534;font-size:9.5px;font-weight:800;text-transform:uppercase;">Approved</span>
                    <strong style="color:#15803d;font-size:13px;">{{ $ms['approved'] }}</strong>
                </div>
                <div style="background:#fffbeb;padding:6px 4px;border-radius:8px;border:1px solid #fef3c7;">
                    <span style="display:block;color:#92400e;font-size:9.5px;font-weight:800;text-transform:uppercase;">Pending</span>
                    <strong style="color:#b45309;font-size:13px;">{{ $ms['pending'] }}</strong>
                </div>
                <div style="background:#fef2f2;padding:6px 4px;border-radius:8px;border:1px solid #fee2e2;">
                    <span style="display:block;color:#991b1b;font-size:9.5px;font-weight:800;text-transform:uppercase;">Rejected</span>
                    <strong style="color:#b91c1c;font-size:13px;">{{ $ms['rejected'] }}</strong>
                </div>
            </div>

            <div style="margin-top:12px;text-align:right;font-size:11.5px;font-weight:700;color:#4F46E5;">
                View Monthly Logs <i class="bi bi-arrow-right-short"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>
@else

<div class="table-panel" style="overflow:hidden;width:100%;max-width:100%;border-radius:18px;box-shadow:0 8px 30px rgba(0,0,0,0.05);background:#ffffff;padding:6px 16px 16px 16px;">
    <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
        <thead>
            <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                <th style="width:7%;padding:12px 10px;text-align:left;font-size:11.5px;font-weight:800;color:#475569;text-transform:uppercase;">Std ID</th>
                <th style="width:20%;padding:12px 12px;text-align:left;font-size:11.5px;font-weight:800;color:#475569;text-transform:uppercase;">Student Name</th>
                <th style="width:16%;padding:12px 10px;text-align:left;font-size:11.5px;font-weight:800;color:#475569;text-transform:uppercase;">Class & Sec</th>
                <th style="width:15%;padding:12px 10px;text-align:left;font-size:11.5px;font-weight:800;color:#475569;text-transform:uppercase;">Leave Date</th>
                <th style="width:8%;padding:12px 8px;text-align:center;font-size:11.5px;font-weight:800;color:#475569;text-transform:uppercase;">Type</th>
                <th style="width:14%;padding:12px 8px;text-align:center;font-size:11.5px;font-weight:800;color:#475569;text-transform:uppercase;">Status</th>
                <th style="width:20%;padding:12px 10px;text-align:center;font-size:11.5px;font-weight:800;color:#475569;text-transform:uppercase;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentLeavesPaginated as $leave)
            @php
                $statusColor = match(strtolower($leave->status)) {
                    'approved' => '#15803d',
                    'rejected' => '#b91c1c',
                    default => '#b45309',
                };
                $statusBg = match(strtolower($leave->status)) {
                    'approved' => '#dcfce7',
                    'rejected' => '#fee2e2',
                    default => '#fef3c7',
                };
                $rawType = $leave->leave_type;
                if (preg_match('/\((.*?)\)/', $rawType, $m)) {
                    $shortType = strtoupper($m[1]);
                } else {
                    $shortType = match(strtolower(trim($rawType))) {
                        'sick leave', 'sick' => 'SL',
                        'casual leave', 'casual' => 'CL',
                        'emergency leave', 'emergency' => 'EL',
                        default => strtoupper(substr($rawType, 0, 3)),
                    };
                }
            @endphp
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:12px 10px;"><strong style="color:#4F46E5;font-size:13px;">#{{ $leave->std_id ?: $leave->student_id }}</strong></td>
                <td style="padding:12px 12px;overflow:hidden;">
                    <div style="display:flex;align-items:center;gap:10px;overflow:hidden;">
                        <div style="width:32px;height:32px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:13px;box-shadow:0 2px 6px rgba(79,70,229,0.2);flex-shrink:0;">
                            {{ strtoupper(substr($leave->student_name, 0, 1)) }}
                        </div>
                        <div style="min-width:0;flex:1;">
                            <strong style="color:#0f172a;font-size:13px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $leave->student_name }}">{{ $leave->student_name }}</strong>
                            <span style="font-size:11px;color:#64748b;">ID: #{{ $leave->student_id }}</span>
                        </div>
                    </div>
                </td>
                <td style="padding:12px 10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <strong style="color:#0f172a;font-size:12.5px;">{{ $leave->student_class }}</strong> 
                    <span style="color:#4F46E5;font-weight:700;font-size:11.5px;">({{ $leave->student_section ?: 'Red' }})</span>
                </td>
                <td style="padding:12px 10px;white-space:nowrap;">
                    <span style="font-weight:700;color:#0f172a;font-size:12.5px;">{{ $leave->leave_date ? \Carbon\Carbon::parse($leave->leave_date)->format('d M Y') : 'N/A' }}</span>
                </td>
                <td style="padding:12px 8px;text-align:center;white-space:nowrap;">
                    <span style="font-size:11.5px;font-weight:800;padding:4px 10px;border-radius:6px;background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;display:inline-block;" title="{{ $leave->leave_type }}">
                        {{ $shortType }}
                    </span>
                </td>
                <td style="padding:12px 8px;text-align:center;white-space:nowrap;">
                    <span style="font-size:11px;font-weight:800;padding:4px 12px;border-radius:999px;background:{{ $statusBg }};color:{{ $statusColor }};display:inline-flex;align-items:center;gap:4px;">
                        <i class="bi @if(strtolower($leave->status) === 'approved') bi-check-circle-fill @elseif(strtolower($leave->status) === 'rejected') bi-x-circle-fill @else bi-clock-history @endif"></i>
                        {{ ucfirst($leave->status) }}
                    </span>
                </td>
                <td style="padding:12px 10px;text-align:center;white-space:nowrap;">
                    <div style="display:inline-flex;align-items:center;justify-content:center;gap:6px;">
                        <button type="button" 
                                data-name="{{ $leave->student_name }}"
                                data-stdid="{{ $leave->std_id ?: $leave->student_id }}"
                                data-classsec="{{ $leave->student_class }} ({{ $leave->student_section ?: 'Red' }})"
                                data-date="{{ $leave->leave_date ? \Carbon\Carbon::parse($leave->leave_date)->format('d M Y') : 'N/A' }}"
                                data-type="{{ $leave->leave_type }}"
                                data-reason="{{ e($leave->reason) }}"
                                data-fine="{{ $leave->is_fine_applied ? ('Rs ' . number_format($leave->fine_amount, 0) . ' Fine') : 'Free Leave' }}"
                                data-isfine="{{ $leave->is_fine_applied ? '1' : '0' }}"
                                data-status="{{ ucfirst($leave->status) }}"
                                data-approvedby="{{ $leave->approved_by ?: 'Pending Review' }}"
                                data-canaction="{{ ($isClassIncharge && strtolower($leave->status) === 'pending') ? '1' : '0' }}"
                                data-approveurl="{{ route('teacher.student-leave.approve', $leave->id) }}"
                                data-rejecturl="{{ route('teacher.student-leave.reject', $leave->id) }}"
                                onclick="openLeaveModalFromBtn(this)" 
                                style="background:#3b82f6;color:#fff;border:none;width:30px;height:30px;border-radius:8px;font-size:14px;font-weight:800;cursor:pointer;display:grid;place-items:center;box-shadow:0 2px 6px rgba(59,130,246,0.3);transition:transform 0.15s;" 
                                title="View Full Leave Details">
                            <i class="bi bi-eye-fill"></i>
                        </button>

                        @if($isClassIncharge)
                            @if(strtolower($leave->status) === 'pending')
                                <form action="{{ route('teacher.student-leave.approve', $leave->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="background:#10b981;color:#fff;border:none;width:30px;height:30px;border-radius:8px;font-size:14px;font-weight:800;cursor:pointer;display:grid;place-items:center;box-shadow:0 2px 6px rgba(16,185,129,0.3);transition:transform 0.15s;" title="Approve Leave & Sync Attendance">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('teacher.student-leave.reject', $leave->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="background:#ef4444;color:#fff;border:none;width:30px;height:30px;border-radius:8px;font-size:14px;font-weight:800;cursor:pointer;display:grid;place-items:center;box-shadow:0 2px 6px rgba(239,68,68,0.3);transition:transform 0.15s;" title="Reject Leave Request">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:28px;color:#64748b;font-weight:600;">
                    No student leave applications found for {{ $primaryClass }} ({{ $primarySection }}).
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($studentLeavesPaginated) && $studentLeavesPaginated->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;background:#ffffff;border-top:1px solid #e2e8f0;border-radius:0 0 18px 18px;">
        <div style="font-size:13.5px;color:#64748b;font-weight:500;">
            Showing <strong style="color:#0f172a;font-weight:700;">{{ $studentLeavesPaginated->firstItem() ?? 0 }}</strong> to <strong style="color:#0f172a;font-weight:700;">{{ $studentLeavesPaginated->lastItem() ?? 0 }}</strong> of <strong style="color:#0f172a;font-weight:700;">{{ $studentLeavesPaginated->total() }}</strong> leave requests
        </div>

        <div style="display:flex;align-items:center;gap:8px;">
            {{-- Previous Page Link --}}
            @if ($studentLeavesPaginated->onFirstPage())
                <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                    <i class="bi bi-chevron-left"></i> Previous
                </span>
            @else
                <a href="{{ $studentLeavesPaginated->previousPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($studentLeavesPaginated->getUrlRange(1, $studentLeavesPaginated->lastPage()) as $pageNumber => $url)
                @if ($pageNumber == $studentLeavesPaginated->currentPage())
                    <span style="padding:8px 16px;border-radius:11px;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;font-size:13px;font-weight:800;border:none;box-shadow:0 4px 12px rgba(79,70,229,0.35);display:inline-flex;align-items:center;justify-content:center;">
                        {{ $pageNumber }}
                    </span>
                @else
                    <a href="{{ $url }}" style="padding:8px 16px;border-radius:11px;border:1px solid #cbd5e1;background:#ffffff;color:#334155;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                        {{ $pageNumber }}
                    </a>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($studentLeavesPaginated->hasMorePages())
                <a href="{{ $studentLeavesPaginated->nextPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                    Next <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>
@endif
@endif

<!-- STUDENT LEAVE DETAILS MODAL -->
<div id="stdLeaveDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); z-index: 999999; place-items: center; padding: 20px; box-sizing: border-box; overflow-y: auto;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 580px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden; animation: securityRise 0.2s ease both; margin: auto; position: relative;">
        
        <!-- Modal Header -->
        <div style="padding: 18px 24px; background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%); color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.2); display: grid; place-items: center; font-size: 18px;">
                    <i class="bi bi-envelope-paper-fill"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #ffffff; line-height: 1.2;">Leave Application Details</h3>
                    <span style="font-size: 11.5px; color: #a7f3d0; font-weight: 600;" id="modalStdSubTitle">Student Details</span>
                </div>
            </div>
            <button type="button" onclick="closeLeaveModal()" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 8px; font-size: 18px; cursor: pointer; display: grid; place-items: center; transition: background 0.15s;" title="Close Modal">
                &times;
            </button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 22px 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px;">
                <div style="padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px;">Student Name</span>
                    <strong style="font-size: 13.5px; color: #0f172a;" id="modalStdName">-</strong>
                </div>
                <div style="padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px;">Class & Section</span>
                    <strong style="font-size: 13.5px; color: #4F46E5;" id="modalStdClassSec">-</strong>
                </div>
                <div style="padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px;">Leave Date</span>
                    <strong style="font-size: 13.5px; color: #0f172a;" id="modalLeaveDate">-</strong>
                </div>
                <div style="padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px;">Leave Type</span>
                    <strong style="font-size: 13.5px; color: #0284c7;" id="modalLeaveType">-</strong>
                </div>
                <div style="padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px;">Fine / Quota Status</span>
                    <strong style="font-size: 13.5px;" id="modalFineStatus">-</strong>
                </div>
                <div style="padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px;">Status</span>
                    <span id="modalStatusBadge" style="font-size: 12px; font-weight: 800; padding: 3px 10px; border-radius: 99px;">-</span>
                </div>
            </div>

            <div style="padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 18px;">
                <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px;">Processed / Approved By</span>
                <strong style="font-size: 13px; color: #334155;" id="modalApprovedBy">-</strong>
            </div>

            <!-- Reason Box -->
            <div style="margin-bottom: 6px;">
                <span style="font-size: 11.5px; font-weight: 800; color: #475569; text-transform: uppercase; display: block; margin-bottom: 6px;">Leave Reason & Explanation</span>
                <div style="padding: 14px 16px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 13px; color: #1e293b; line-height: 1.5; white-space: pre-wrap; word-break: break-word; max-height: 140px; overflow-y: auto;" id="modalLeaveReason">
                    -
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div id="modalActionButtons" style="display: flex; align-items: center; gap: 8px;">
                <!-- Forms inserted dynamically via JS -->
            </div>
            <button type="button" onclick="closeLeaveModal()" style="padding: 9px 20px; border-radius: 10px; background: #e2e8f0; color: #334155; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openLeaveModalFromBtn(btn) {
    const modal = document.getElementById('stdLeaveDetailModal');
    if (!modal) return;

    const name = btn.dataset.name || 'N/A';
    const stdId = btn.dataset.stdid || '';
    const classSec = btn.dataset.classsec || 'N/A';
    const date = btn.dataset.date || 'N/A';
    const type = btn.dataset.type || 'N/A';
    const reason = btn.dataset.reason || 'No reason provided.';
    const fine = btn.dataset.fine || 'Free Leave';
    const isFine = btn.dataset.isfine === '1';
    const status = btn.dataset.status || 'Pending';
    const approvedBy = btn.dataset.approvedby || 'Pending Review';
    const canAction = btn.dataset.canaction === '1';
    const approveUrl = btn.dataset.approveurl;
    const rejectUrl = btn.dataset.rejecturl;

    document.getElementById('modalStdSubTitle').textContent = `Roll #${stdId} • ${classSec}`;
    document.getElementById('modalStdName').textContent = name;
    document.getElementById('modalStdClassSec').textContent = classSec;
    document.getElementById('modalLeaveDate').textContent = date;
    document.getElementById('modalLeaveType').textContent = type;
    document.getElementById('modalLeaveReason').textContent = reason;
    document.getElementById('modalApprovedBy').textContent = approvedBy;

    const fineEl = document.getElementById('modalFineStatus');
    fineEl.textContent = fine;
    fineEl.style.color = isFine ? '#dc2626' : '#16a34a';

    const badgeEl = document.getElementById('modalStatusBadge');
    badgeEl.textContent = status;
    if (status.toLowerCase() === 'approved') {
        badgeEl.style.background = '#dcfce7';
        badgeEl.style.color = '#15803d';
    } else if (status.toLowerCase() === 'rejected') {
        badgeEl.style.background = '#fee2e2';
        badgeEl.style.color = '#b91c1c';
    } else {
        badgeEl.style.background = '#fef3c7';
        badgeEl.style.color = '#b45309';
    }

    const actionContainer = document.getElementById('modalActionButtons');
    actionContainer.innerHTML = '';

    if (canAction && status.toLowerCase() === 'pending' && approveUrl && rejectUrl) {
        actionContainer.innerHTML = `
            <form action="${approveUrl}" method="POST" style="margin:0;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" onclick="return confirm('Approve leave for ${name}?')" style="padding:9px 18px;border-radius:10px;background:#10b981;color:#ffffff;font-size:13px;font-weight:800;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(16,185,129,0.3);">
                    <i class="bi bi-check-circle-fill"></i> Approve Leave
                </button>
            </form>
            <form action="${rejectUrl}" method="POST" style="margin:0;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" onclick="return confirm('Reject leave for ${name}?')" style="padding:9px 18px;border-radius:10px;background:#ef4444;color:#ffffff;font-size:13px;font-weight:800;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(239,68,68,0.3);">
                    <i class="bi bi-x-circle-fill"></i> Reject Leave
                </button>
            </form>
        `;
    }

    modal.style.display = 'grid';
}

function closeLeaveModal() {
    const modal = document.getElementById('stdLeaveDetailModal');
    if (modal) modal.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('stdLeaveDetailModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeLeaveModal();
        });
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLeaveModal();
    });
});
</script>

@if($page === 'results')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <p>EXCELLENCE TEACHER PORTAL &bull; ACADEMIC RESULTS</p>
        <h1>{{ $primaryClass }} {{ $primarySection }} &bull; Student Academic Results</h1>
    </div>
    <div>
        @if($isClassIncharge)
            <button type="button" onclick="openAddStudentResultModal()" style="background:#4F46E5;color:#fff;border:none;padding:10px 20px;border-radius:12px;font-weight:800;font-size:13.5px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(79,70,229,0.25);cursor:pointer;">
                <i class="bi bi-plus-circle-fill"></i> Add Class Results
            </button>
        @else
            <span style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:800;">
                <i class="bi bi-lock-fill"></i> Subject Teacher (Read-Only)
            </span>
        @endif
    </div>
</div>

{!! $classFilterHtml() !!}

@if(!$isClassIncharge)
    <div style="background:#fffbeb;border:1px solid #fde68a;color:#b45309;padding:16px 20px;border-radius:14px;margin-bottom:24px;display:flex;align-items:center;gap:14px;">
        <i class="bi bi-shield-lock-fill" style="font-size:26px;color:#d97706;"></i>
        <div>
            <strong style="display:block;font-size:14.5px;">🔒 Read-Only Access Mode</strong>
            <span style="font-size:13px;">Entering, editing, or deleting student academic results is restricted to the designated Class In-Charge teacher (<strong>{{ $classInchargeTeacherName }}</strong>) for {{ $primaryClass }} ({{ $primarySection }}).</span>
        </div>
    </div>
@else
    <div style="background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;padding:16px 22px;border-radius:16px;margin-bottom:24px;box-shadow:0 10px 25px rgba(79,70,229,0.22);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.18);display:grid;place-items:center;font-size:24px;"><i class="bi bi-patch-check-fill"></i></div>
            <div>
                <span style="font-size:11px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#a7f3d0;">👑 Class In-Charge Management Access</span>
                <h3 style="margin:2px 0 0;font-size:17px;font-weight:800;color:#ffffff;">Full Authorization to Add, Edit, or Delete Academic Results</h3>
            </div>
        </div>
        <a href="{{ route('results.class', ['class' => $primaryClass . ($primarySection ? ' ('.$primarySection.')' : '')]) }}" style="background:#ffffff;color:#4F46E5;padding:10px 20px;border-radius:12px;font-weight:800;font-size:13px;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
            <i class="bi bi-pencil-square"></i> Open Marks Entry Tool
        </a>
    </div>
@endif

<!-- Academic Results Term & Year Filter Bar -->
<div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;background:#ffffff;border:1px solid #e2e8f0;padding:16px 20px;border-radius:18px;box-shadow:0 4px 14px rgba(0,0,0,0.03);">
    <div style="display:flex;align-items:center;gap:10px;">
        <i class="bi bi-funnel-fill" style="color:#4F46E5;font-size:18px;"></i>
        <span style="font-size:13.5px;font-weight:800;color:#0f172a;">Filter Academic Results:</span>
    </div>

    <form method="GET" action="{{ route('teacher.page', 'results') }}" id="resultFilterForm" style="margin:0;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <input type="hidden" name="class" value="{{ $primaryClass }}">
        
        {{-- Term Selector --}}
        <div style="display:flex;align-items:center;gap:6px;">
            <label style="font-size:12.5px;font-weight:700;color:#475569;">Select Term:</label>
            <select name="result_term" onchange="document.getElementById('resultFilterForm').submit()" style="padding:9px 16px;border:1.5px solid #4F46E5;border-radius:10px;font-size:13px;font-weight:800;color:#0f172a;background:#EEF2FF;cursor:pointer;outline:none;">
                <option value="all" {{ ($selectedResultTerm ?? 'all') === 'all' ? 'selected' : '' }}>All Terms</option>
                <option value="First Term" {{ ($selectedResultTerm ?? 'all') === 'First Term' ? 'selected' : '' }}>First Term</option>
                <option value="Mid Term" {{ ($selectedResultTerm ?? 'all') === 'Mid Term' ? 'selected' : '' }}>Mid Term</option>
                <option value="Final Term" {{ ($selectedResultTerm ?? 'all') === 'Final Term' ? 'selected' : '' }}>Final Term</option>
            </select>
        </div>

        {{-- Year Selector --}}
        <div style="display:flex;align-items:center;gap:6px;">
            <label style="font-size:12.5px;font-weight:700;color:#475569;">Select Year:</label>
            <select name="result_year" onchange="document.getElementById('resultFilterForm').submit()" style="padding:9px 16px;border:1.5px solid #4F46E5;border-radius:10px;font-size:13px;font-weight:800;color:#0f172a;background:#EEF2FF;cursor:pointer;outline:none;">
                <option value="all" {{ ($selectedResultYear ?? 'all') === 'all' ? 'selected' : '' }}>All Years</option>
                @foreach(['2026', '2025', '2024'] as $yVal)
                    <option value="{{ $yVal }}" {{ ($selectedResultYear ?? 'all') === $yVal ? 'selected' : '' }}>{{ $yVal }}</option>
                @endforeach
            </select>
        </div>

        @if(($selectedResultTerm ?? 'all') !== 'all' || ($selectedResultYear ?? 'all') !== 'all')
            <a href="{{ route('teacher.page', ['page' => 'results', 'class' => $primaryClass]) }}" style="padding:9px 14px;border-radius:10px;background:#fee2e2;color:#dc2626;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-x-circle-fill"></i> Clear Filters
            </a>
        @endif
    </form>
</div>

<div class="table-panel">
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Class & Sec</th>
                <th>Term</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentResultsPaginated as $stdResult)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:38px;height:38px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:14px;box-shadow:0 3px 8px rgba(79,70,229,0.2);">
                            {{ strtoupper(substr($stdResult->name, 0, 1)) }}
                        </div>
                        <div>
                            <strong style="color:#0f172a;font-size:14.5px;display:block;">{{ $stdResult->name }}</strong>
                            <span style="font-size:11.5px;color:#64748b;">Roll #{{ $stdResult->std_id }} &bull; Reg: {{ $stdResult->registration_no ?: ('REG-2026-'.$stdResult->std_id) }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <strong style="color:#0f172a;font-size:13.5px;">{{ $stdResult->class }}</strong> 
                    <span style="color:#4F46E5;font-weight:700;">({{ $stdResult->section ?: 'Red' }})</span>
                </td>
                <td>
                    <span class="status" style="background:#EEF2FF;color:#4F46E5;border:1px solid #C7D2FE;font-weight:700;">
                        <i class="bi bi-award-fill"></i> {{ $stdResult->terms_count }} Terms Evaluated (Overall Grade: {{ $stdResult->overall_grade }} • {{ $stdResult->overall_pct }}%)
                    </span>
                </td>
                <td>
                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                        <!-- Eye View Button -->
                        <button type="button" onclick="openStudentResultModal({{ json_encode([
                            'id' => $stdResult->id,
                            'name' => $stdResult->name,
                            'roll_no' => $stdResult->std_id,
                            'class' => $stdResult->class,
                            'section' => $stdResult->section ?: 'Red',
                            'overall_pct' => $stdResult->overall_pct,
                            'overall_grade' => $stdResult->overall_grade,
                            'download_url' => route('students.results.download', $stdResult->id),
                            'results' => $stdResult->class_results
                        ]) }})" style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#0284c7,#0369a1);color:#ffffff;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:17px;box-shadow:0 4px 10px rgba(2,132,199,0.3);transition:all 0.2s ease;" title="View Complete Result Sheet">
                            <i class="bi bi-eye-fill"></i>
                        </button>

                        <!-- Download PDF Button -->
                        <a href="{{ route('students.results.download', $stdResult->id) }}" target="_blank" style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#059669,#047857);color:#ffffff;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:17px;box-shadow:0 4px 10px rgba(5,150,105,0.3);transition:all 0.2s ease;text-decoration:none;" title="Download Official Result Card">
                            <i class="bi bi-download"></i>
                        </a>

                        @if($isClassIncharge)
                        <!-- Edit Button (Pencil Icon) -->
                        <button type="button" onclick="openEditStudentResultModal({{ json_encode([
                            'id' => $stdResult->id,
                            'name' => $stdResult->name,
                            'roll_no' => $stdResult->std_id,
                            'class' => $stdResult->class,
                            'section' => $stdResult->section ?: 'Red',
                            'subject' => $stdResult->subject,
                            'results' => $stdResult->class_results
                        ]) }})" style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#d97706,#b45309);color:#ffffff;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:17px;box-shadow:0 4px 10px rgba(217,119,6,0.3);transition:all 0.2s ease;" title="Edit Academic Marks">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;padding:28px;color:#64748b;font-weight:600;">No student results recorded for {{ $primaryClass }} {{ $primarySection }}.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;background:#ffffff;border-top:1px solid #e2e8f0;border-radius:0 0 18px 18px;">
        <div style="font-size:13.5px;color:#64748b;font-weight:500;">
            Showing <strong style="color:#0f172a;font-weight:700;">{{ $studentResultsPaginated->firstItem() ?? 0 }}</strong> to <strong style="color:#0f172a;font-weight:700;">{{ $studentResultsPaginated->lastItem() ?? 0 }}</strong> of <strong style="color:#0f172a;font-weight:700;">{{ $studentResultsPaginated->total() }}</strong> student records
        </div>

        @if($studentResultsPaginated->hasPages())
        <div style="display:flex;align-items:center;gap:8px;">
            {{-- Previous Page Link --}}
            @if ($studentResultsPaginated->onFirstPage())
                <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                    <i class="bi bi-chevron-left"></i> Previous
                </span>
            @else
                <a href="{{ $studentResultsPaginated->previousPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($studentResultsPaginated->getUrlRange(1, $studentResultsPaginated->lastPage()) as $pageNumber => $url)
                @if ($pageNumber == $studentResultsPaginated->currentPage())
                    <span style="padding:8px 16px;border-radius:11px;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;font-size:13px;font-weight:800;border:none;box-shadow:0 4px 12px rgba(79,70,229,0.35);display:inline-flex;align-items:center;justify-content:center;">
                        {{ $pageNumber }}
                    </span>
                @else
                    <a href="{{ $url }}" style="padding:8px 16px;border-radius:11px;border:1px solid #cbd5e1;background:#ffffff;color:#334155;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                        {{ $pageNumber }}
                    </a>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($studentResultsPaginated->hasMorePages())
                <a href="{{ $studentResultsPaginated->nextPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                    Next <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Modal Popup for Viewing Full Leave Request Details -->
<div id="viewLeaveDetailModal" style="display:none;position:fixed;inset:0;background:rgba(7,39,36,0.65);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;overflow-y:auto;">
    <div style="background:#fff;border-radius:22px;width:min(100%,580px);padding:30px 26px;box-shadow:0 28px 70px rgba(0,30,25,0.35);position:relative;animation:securityRise 0.25s ease both;margin:auto;">
        <button type="button" onclick="document.getElementById('viewLeaveDetailModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:#f0f6f5;border:none;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;color:#475569;display:grid;place-items:center;" title="Close">&times;</button>
        
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e2e8f0;">
            <div id="leaveModalAvatar" style="width:46px;height:46px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:18px;">A</div>
            <div>
                <h3 id="leaveModalStdName" style="margin:0;font-size:18px;font-weight:800;color:#0f172a;">Student Name</h3>
                <p id="leaveModalStdSub" style="margin:2px 0 0;font-size:12.5px;color:#64748b;">Class: Pre-KG (Red) &bull; Roll #1</p>
            </div>
            <div style="margin-left:auto;text-align:right;">
                <span id="leaveModalStatusBadge" style="background:#fef3c7;color:#b45309;padding:5px 14px;border-radius:99px;font-weight:800;font-size:13px;display:inline-block;">Pending</span>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;background:#f8fafc;padding:14px;border-radius:14px;border:1px solid #e2e8f0;font-size:13px;">
            <div>
                <span style="color:#64748b;font-weight:600;display:block;font-size:11px;text-transform:uppercase;">Leave Date</span>
                <strong id="leaveModalDate" style="color:#0f172a;font-size:13.5px;">-</strong>
            </div>
            <div>
                <span style="color:#64748b;font-weight:600;display:block;font-size:11px;text-transform:uppercase;">Leave Type</span>
                <strong id="leaveModalType" style="color:#0369a1;font-size:13.5px;">-</strong>
            </div>
            <div>
                <span style="color:#64748b;font-weight:600;display:block;font-size:11px;text-transform:uppercase;">Fine Applied</span>
                <strong id="leaveModalFine" style="color:#166534;font-size:13.5px;">-</strong>
            </div>
            <div>
                <span style="color:#64748b;font-weight:600;display:block;font-size:11px;text-transform:uppercase;">Processed By</span>
                <strong id="leaveModalProcessedBy" style="color:#334155;font-size:13.5px;">-</strong>
            </div>
        </div>

        <div style="margin-bottom:22px;">
            <label style="font-size:12px;font-weight:800;color:#475569;text-transform:uppercase;display:block;margin-bottom:6px;">Reason for Leave:</label>
            <div id="leaveModalReason" style="background:#ffffff;padding:14px;border-radius:12px;border:1px solid #cbd5e1;font-size:13.5px;color:#1e293b;line-height:1.55;min-height:70px;white-space:pre-wrap;">
                Full leave reason details...
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid #e2e8f0;">
            <button type="button" onclick="document.getElementById('viewLeaveDetailModal').style.display='none'" style="padding:9px 18px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:13px;font-weight:700;cursor:pointer;">Close</button>
            <div id="leaveModalActionBtns" style="display:flex;gap:10px;">
                <!-- Populated via JS -->
            </div>
        </div>
    </div>
</div>

<script>
function closeLeaveDetailModal() {
    const modal = document.getElementById('viewLeaveDetailModal');
    if (modal) modal.style.display = 'none';
}

function openLeaveModalFromBtn(btn) {
    if (!btn) return;
    const data = {
        name: btn.getAttribute('data-name') || '',
        std_id: btn.getAttribute('data-stdid') || '',
        class_sec: btn.getAttribute('data-classsec') || '',
        date: btn.getAttribute('data-date') || '',
        type: btn.getAttribute('data-type') || '',
        reason: btn.getAttribute('data-reason') || '',
        fine: btn.getAttribute('data-fine') || '',
        is_fine: btn.getAttribute('data-isfine') === '1',
        status: btn.getAttribute('data-status') || 'Pending',
        approved_by: btn.getAttribute('data-approvedby') || '',
        can_action: btn.getAttribute('data-canaction') === '1',
        approve_url: btn.getAttribute('data-approveurl') || '',
        reject_url: btn.getAttribute('data-rejecturl') || ''
    };
    openLeaveDetailModal(data);
}

function openLeaveDetailModal(data) {
    document.getElementById('leaveModalStdName').innerText = data.name;
    document.getElementById('leaveModalStdSub').innerText = 'Class: ' + data.class_sec + ' • Roll #' + data.std_id;
    document.getElementById('leaveModalAvatar').innerText = (data.name || 'S').charAt(0).toUpperCase();
    
    document.getElementById('leaveModalStatusBadge').innerText = data.status;
    if (data.status.toLowerCase() === 'approved') {
        document.getElementById('leaveModalStatusBadge').style.background = '#dcfce7';
        document.getElementById('leaveModalStatusBadge').style.color = '#15803d';
    } else if (data.status.toLowerCase() === 'rejected') {
        document.getElementById('leaveModalStatusBadge').style.background = '#fee2e2';
        document.getElementById('leaveModalStatusBadge').style.color = '#b91c1c';
    } else {
        document.getElementById('leaveModalStatusBadge').style.background = '#fef3c7';
        document.getElementById('leaveModalStatusBadge').style.color = '#b45309';
    }

    document.getElementById('leaveModalDate').innerText = data.date;
    document.getElementById('leaveModalType').innerText = data.type;
    document.getElementById('leaveModalFine').innerText = data.fine;
    document.getElementById('leaveModalFine').style.color = data.is_fine ? '#dc2626' : '#166534';
    document.getElementById('leaveModalProcessedBy').innerText = data.approved_by;
    document.getElementById('leaveModalReason').innerText = data.reason;

    const actionContainer = document.getElementById('leaveModalActionBtns');
    if (actionContainer) {
        actionContainer.innerHTML = '';
        if (data.can_action) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
            actionContainer.innerHTML = `
                <form action="${data.approve_url}" method="POST" style="margin:0;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" style="padding:9px 18px;border-radius:10px;background:#10b981;color:#fff;border:none;font-size:13px;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 3px 10px rgba(16,185,129,0.25);">
                        <i class="bi bi-check-lg"></i> Approve Leave
                    </button>
                </form>
                <form action="${data.reject_url}" method="POST" style="margin:0;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" style="padding:9px 18px;border-radius:10px;background:#ef4444;color:#fff;border:none;font-size:13px;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 3px 10px rgba(239,68,68,0.25);">
                        <i class="bi bi-x-lg"></i> Reject Leave
                    </button>
                </form>
            `;
        }
    }

    const modal = document.getElementById('viewLeaveDetailModal');
    if (modal) {
        modal.style.display = 'grid';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const leaveModal = document.getElementById('viewLeaveDetailModal');
    if (leaveModal) {
        leaveModal.addEventListener('click', (e) => {
            if (e.target === leaveModal) {
                closeLeaveDetailModal();
            }
        });
    }
});
</script>

<!-- Modal Popup for Viewing Full Result Card -->
<div id="viewStudentResultModal" style="display:none;position:fixed;inset:0;background:rgba(7,39,36,0.65);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;overflow-y:auto;">
    <div style="background:#fff;border-radius:22px;width:min(100%,750px);padding:32px 28px;box-shadow:0 28px 70px rgba(0,30,25,0.35);position:relative;animation:securityRise 0.25s ease both;margin:auto;">
        <button type="button" onclick="document.getElementById('viewStudentResultModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:#f0f6f5;border:none;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;color:#475569;display:grid;place-items:center;" title="Close">&times;</button>
        
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e2e8f0;">
            <div id="modalStdAvatar" style="width:48px;height:48px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:18px;">A</div>
            <div>
                <h3 id="modalStdName" style="margin:0;font-size:20px;font-weight:800;color:#0f172a;">Student Name</h3>
                <p id="modalStdSub" style="margin:2px 0 0;font-size:13px;color:#64748b;">Class: Pre-KG (Red) &bull; Roll #1</p>
            </div>
            <div style="margin-left:auto;text-align:right;">
                <span id="modalGradeBadge" style="background:#dcfce7;color:#15803d;padding:6px 14px;border-radius:99px;font-weight:800;font-size:14px;display:inline-block;">A+ (92%)</span>
            </div>
        </div>

        <div style="display:flex;gap:8px;margin-bottom:16px;" id="modalTermFilters">
            <button type="button" onclick="filterModalTerm('all')" class="modal-term-btn active" style="padding:6px 14px;border-radius:8px;border:1px solid #4F46E5;background:#4F46E5;color:#fff;font-size:12px;font-weight:700;cursor:pointer;">All Terms</button>
            <button type="button" onclick="filterModalTerm('First Term')" class="modal-term-btn" style="padding:6px 14px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:12px;font-weight:700;cursor:pointer;">First Term</button>
            <button type="button" onclick="filterModalTerm('Mid Term')" class="modal-term-btn" style="padding:6px 14px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:12px;font-weight:700;cursor:pointer;">Mid Term</button>
            <button type="button" onclick="filterModalTerm('Final Term')" class="modal-term-btn" style="padding:6px 14px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:12px;font-weight:700;cursor:pointer;">Final Term</button>
        </div>

        <div style="max-height:360px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:14px;margin-bottom:20px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="position:sticky;top:0;background:#f8fafc;z-index:1;">
                    <tr>
                        <th style="padding:10px 14px;text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;">Subject</th>
                        <th style="padding:10px 14px;text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;">Term</th>
                        <th style="padding:10px 14px;text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;">Marks</th>
                        <th style="padding:10px 14px;text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;">Percentage</th>
                        <th style="padding:10px 14px;text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;">Grade</th>
                    </tr>
                </thead>
                <tbody id="modalResultsBody">
                    <!-- Populated via JavaScript -->
                </tbody>
            </table>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;">
            <button type="button" onclick="document.getElementById('viewStudentResultModal').style.display='none'" style="padding:10px 20px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:13px;font-weight:700;cursor:pointer;">Close</button>
            <a id="modalDownloadBtn" href="#" target="_blank" style="padding:10px 20px;border-radius:10px;background:#4F46E5;color:#fff;text-decoration:none;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(79,70,229,0.25);">
                <i class="bi bi-file-earmark-pdf-fill"></i> Download Official Result PDF
            </a>
        </div>
    </div>
</div>

<script>
let currentModalStudentResults = [];

function openStudentResultModal(data) {
    document.getElementById('modalStdName').innerText = data.name;
    document.getElementById('modalStdSub').innerText = 'Class: ' + data.class + ' (' + (data.section || 'A') + ') • Roll #' + data.roll_no;
    document.getElementById('modalStdAvatar').innerText = data.name.charAt(0).toUpperCase();
    document.getElementById('modalGradeBadge').innerText = data.overall_grade + ' (' + data.overall_pct + '%)';
    document.getElementById('modalDownloadBtn').href = data.download_url;

    currentModalStudentResults = data.results || [];
    filterModalTerm('all');

    document.getElementById('viewStudentResultModal').style.display = 'grid';
}

function filterModalTerm(term) {
    document.querySelectorAll('.modal-term-btn').forEach(btn => {
        if (btn.innerText.trim().toLowerCase() === term.toLowerCase() || (term === 'all' && btn.innerText.trim().toLowerCase() === 'all terms')) {
            btn.style.background = '#4F46E5';
            btn.style.color = '#fff';
            btn.style.borderColor = '#4F46E5';
        } else {
            btn.style.background = '#fff';
            btn.style.color = '#475569';
            btn.style.borderColor = '#cbd5e1';
        }
    });

    renderModalResults(term);
}

function renderModalResults(termFilter) {
    const tbody = document.getElementById('modalResultsBody');
    tbody.innerHTML = '';

    const filtered = termFilter === 'all' 
        ? currentModalStudentResults 
        : currentModalStudentResults.filter(r => (r.term || '').toLowerCase() === termFilter.toLowerCase());

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:16px;color:#64748b;">No results found for ' + termFilter + '.</td></tr>';
        return;
    }

    filtered.forEach(r => {
        const pct = r.total_marks > 0 ? Math.round((r.obtained_marks / r.total_marks) * 100) : 0;
        const gradeColor = pct >= 80 ? '#059669' : (pct >= 60 ? '#d97706' : '#dc2626');
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid #f1f5f9';
        tr.innerHTML = `
            <td style="padding:10px 14px;font-weight:700;color:#0f172a;">${r.subject}</td>
            <td style="padding:10px 14px;"><span class="status" style="font-size:10.5px;">${r.term}</span></td>
            <td style="padding:10px 14px;font-weight:700;color:#0f172a;">${r.obtained_marks} / ${r.total_marks}</td>
            <td style="padding:10px 14px;font-weight:700;color:${gradeColor};">${pct}%</td>
            <td style="padding:10px 14px;font-weight:800;color:${gradeColor};">${r.grade || 'A'}</td>
        `;
        tbody.appendChild(tr);
    });
}

// Global Class Students Data for Add Modal
window.allClassStudentsData = @json($students);

let currentEditStudentData = null;

function openEditStudentResultModal(data) {
    currentEditStudentData = data;
    document.getElementById('editModalStudentId').value = data.id;
    document.getElementById('editModalStudentClass').value = data.class + (data.section ? ' (' + data.section + ')' : '');
    document.getElementById('editModalStdName').innerText = data.name;
    document.getElementById('editModalStdSub').innerText = 'Class: ' + data.class + ' (' + (data.section || 'A') + ') • Roll #' + data.roll_no;
    document.getElementById('editModalStdAvatar').innerText = data.name.charAt(0).toUpperCase();

    document.getElementById('editModalTermSelect').value = 'First Term';
    renderEditSubjectMarks();

    document.getElementById('editStudentResultModal').style.display = 'grid';
}

function renderEditSubjectMarks() {
    if (!currentEditStudentData) return;
    const selectedTerm = document.getElementById('editModalTermSelect').value;
    const tbody = document.getElementById('editModalSubjectsBody');
    tbody.innerHTML = '';

    let subjects = ['English', 'Mathematics', 'Urdu', 'General Science', 'Islamiyat', 'Computer Science', 'Drawing & Arts'];
    if (currentEditStudentData.subject && typeof currentEditStudentData.subject === 'string' && currentEditStudentData.subject.trim() !== '') {
        const assigned = currentEditStudentData.subject.split(',').map(s => s.trim()).filter(Boolean);
        if (assigned.length > 0) subjects = assigned;
    }

    const existingResults = (currentEditStudentData.results || []).filter(r => (r.term || '').toLowerCase() === selectedTerm.toLowerCase());
    const existingMap = {};
    existingResults.forEach(r => {
        existingMap[r.subject.toLowerCase().trim()] = r.obtained_marks;
    });

    subjects.forEach(subj => {
        const key = subj.toLowerCase().trim();
        const obtainedVal = existingMap[key] !== undefined && existingMap[key] !== null ? existingMap[key] : '';
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid #f1f5f9';
        tr.innerHTML = `
            <td style="padding:12px 14px;font-weight:700;color:#0f172a;">${subj}</td>
            <td style="padding:12px 14px;text-align:center;font-weight:700;color:#64748b;">100</td>
            <td style="padding:10px 14px;text-align:center;">
                <input type="number" step="0.1" min="0" max="100" name="marks[${subj}]" value="${obtainedVal}" placeholder="Marks" style="width:110px;padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1;font-weight:700;text-align:center;font-size:14px;color:#0f172a;outline:none;" onfocus="this.style.borderColor='#4F46E5'" onblur="this.style.borderColor='#cbd5e1'">
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function openAddStudentResultModal() {
    document.getElementById('addModalStudentSelect').value = '';
    document.getElementById('addModalTermSelect').value = 'First Term';
    document.getElementById('addModalSubjectsBody').innerHTML = '<tr><td colspan="3" style="text-align:center;padding:24px;color:#64748b;">Please select a student above to enter marks.</td></tr>';
    document.getElementById('addStudentResultModal').style.display = 'grid';
}

function onAddStudentSelect(stdId) {
    if (!stdId) {
        document.getElementById('addModalSubjectsBody').innerHTML = '<tr><td colspan="3" style="text-align:center;padding:24px;color:#64748b;">Please select a student above to enter marks.</td></tr>';
        return;
    }
    const student = (window.allClassStudentsData || []).find(s => s.id == stdId);
    let subjects = ['English', 'Mathematics', 'Urdu', 'General Science', 'Islamiyat', 'Computer Science', 'Drawing & Arts'];
    if (student && student.subject && typeof student.subject === 'string' && student.subject.trim() !== '') {
        const assigned = student.subject.split(',').map(s => s.trim()).filter(Boolean);
        if (assigned.length > 0) subjects = assigned;
    }

    const tbody = document.getElementById('addModalSubjectsBody');
    tbody.innerHTML = '';
    subjects.forEach(subj => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid #f1f5f9';
        tr.innerHTML = `
            <td style="padding:12px 14px;font-weight:700;color:#0f172a;">${subj}</td>
            <td style="padding:12px 14px;text-align:center;font-weight:700;color:#64748b;">100</td>
            <td style="padding:10px 14px;text-align:center;">
                <input type="number" step="0.1" min="0" max="100" name="marks[${subj}]" value="" placeholder="Enter Marks" style="width:110px;padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1;font-weight:700;text-align:center;font-size:14px;color:#0f172a;outline:none;" onfocus="this.style.borderColor='#4F46E5'" onblur="this.style.borderColor='#cbd5e1'">
            </td>
        `;
        tbody.appendChild(tr);
    });
}
</script>

<!-- Modal Popup for Editing Student Result -->
<div id="editStudentResultModal" style="display:none;position:fixed;inset:0;background:rgba(7,39,36,0.65);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;overflow-y:auto;">
    <div style="background:#fff;border-radius:22px;width:min(100%,700px);padding:32px 28px;box-shadow:0 28px 70px rgba(0,30,25,0.35);position:relative;margin:auto;">
        <button type="button" onclick="document.getElementById('editStudentResultModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:#f0f6f5;border:none;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;color:#475569;display:grid;place-items:center;" title="Close">&times;</button>
        
        <form action="{{ route('results.student.store') }}" method="POST" id="editStudentResultForm">
            @csrf
            <input type="hidden" name="student_id" id="editModalStudentId">
            <input type="hidden" name="student_class" id="editModalStudentClass">

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e2e8f0;">
                <div id="editModalStdAvatar" style="width:48px;height:48px;border-radius:50%;background:#d97706;color:#fff;display:grid;place-items:center;font-weight:800;font-size:18px;">E</div>
                <div>
                    <h3 id="editModalStdName" style="margin:0;font-size:20px;font-weight:800;color:#0f172a;">Edit Student Marks</h3>
                    <p id="editModalStdSub" style="margin:2px 0 0;font-size:13px;color:#64748b;">Class: Pre-KG (Red) &bull; Roll #1</p>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-space-between;gap:16px;margin-bottom:16px;background:#f8fafc;padding:12px 16px;border-radius:12px;border:1px solid #e2e8f0;">
                <label style="font-weight:700;font-size:13.5px;color:#334155;">Select Exam Term to Edit:</label>
                <select name="term" id="editModalTermSelect" onchange="renderEditSubjectMarks()" style="padding:8px 16px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;font-size:13.5px;font-weight:700;color:#0f172a;cursor:pointer;">
                    <option value="First Term">First Term</option>
                    <option value="Mid Term">Mid Term</option>
                    <option value="Final Term">Final Term</option>
                </select>
            </div>

            <div style="max-height:360px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:14px;margin-bottom:20px;">
                <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                    <thead style="position:sticky;top:0;background:#f1f5f9;z-index:1;">
                        <tr>
                            <th style="padding:10px 14px;text-align:left;color:#475569;font-size:11px;text-transform:uppercase;">Subject</th>
                            <th style="padding:10px 14px;text-align:center;color:#475569;font-size:11px;text-transform:uppercase;">Total Marks</th>
                            <th style="padding:10px 14px;text-align:center;color:#475569;font-size:11px;text-transform:uppercase;width:180px;">Obtained Marks</th>
                        </tr>
                    </thead>
                    <tbody id="editModalSubjectsBody">
                        <!-- Populated dynamically via JS -->
                    </tbody>
                </table>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;">
                <button type="button" onclick="document.getElementById('editStudentResultModal').style.display='none'" style="padding:10px 20px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:13px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="submit" style="padding:10px 22px;border-radius:10px;background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border:none;font-size:13.5px;font-weight:800;cursor:pointer;box-shadow:0 4px 14px rgba(217,119,6,0.3);display:inline-flex;align-items:center;gap:8px;">
                    <i class="bi bi-check-circle-fill"></i> Save & Update Database
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Popup for Adding Class Result -->
<div id="addStudentResultModal" style="display:none;position:fixed;inset:0;background:rgba(7,39,36,0.65);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;overflow-y:auto;">
    <div style="background:#fff;border-radius:22px;width:min(100%,700px);padding:32px 28px;box-shadow:0 28px 70px rgba(0,30,25,0.35);position:relative;margin:auto;">
        <button type="button" onclick="document.getElementById('addStudentResultModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:#f0f6f5;border:none;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;color:#475569;display:grid;place-items:center;" title="Close">&times;</button>
        
        <form action="{{ route('results.student.store') }}" method="POST" id="addStudentResultForm">
            @csrf
            <input type="hidden" name="student_class" value="{{ $primaryClass . ($primarySection ? ' ('.$primarySection.')' : '') }}">

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #e2e8f0;">
                <div style="width:48px;height:48px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:22px;"><i class="bi bi-plus-lg"></i></div>
                <div>
                    <h3 style="margin:0;font-size:20px;font-weight:800;color:#0f172a;">Add Class Student Result</h3>
                    <p style="margin:2px 0 0;font-size:13px;color:#64748b;">Class: {{ $primaryClass }} ({{ $primarySection }}) &bull; Enter Student Marks</p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;background:#f8fafc;padding:16px;border-radius:14px;border:1px solid #e2e8f0;">
                <div>
                    <label style="font-weight:700;font-size:12.5px;color:#475569;display:block;margin-bottom:6px;">Select Student:</label>
                    <select name="student_id" id="addModalStudentSelect" onchange="onAddStudentSelect(this.value)" required style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;font-size:13.5px;font-weight:700;color:#0f172a;">
                        <option value="">-- Choose Student --</option>
                        @foreach($students as $std)
                            <option value="{{ $std->id }}">{{ $std->name }} (Roll #{{ $std->std_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight:700;font-size:12.5px;color:#475569;display:block;margin-bottom:6px;">Select Exam Term:</label>
                    <select name="term" id="addModalTermSelect" style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;font-size:13.5px;font-weight:700;color:#0f172a;">
                        <option value="First Term">First Term</option>
                        <option value="Mid Term">Mid Term</option>
                        <option value="Final Term">Final Term</option>
                    </select>
                </div>
            </div>

            <div style="max-height:360px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:14px;margin-bottom:20px;">
                <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                    <thead style="position:sticky;top:0;background:#f1f5f9;z-index:1;">
                        <tr>
                            <th style="padding:10px 14px;text-align:left;color:#475569;font-size:11px;text-transform:uppercase;">Subject</th>
                            <th style="padding:10px 14px;text-align:center;color:#475569;font-size:11px;text-transform:uppercase;">Total Marks</th>
                            <th style="padding:10px 14px;text-align:center;color:#475569;font-size:11px;text-transform:uppercase;width:180px;">Obtained Marks</th>
                        </tr>
                    </thead>
                    <tbody id="addModalSubjectsBody">
                        <tr><td colspan="3" style="text-align:center;padding:24px;color:#64748b;">Please select a student above to enter marks.</td></tr>
                    </tbody>
                </table>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;">
                <button type="button" onclick="document.getElementById('addStudentResultModal').style.display='none'" style="padding:10px 20px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:13px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="submit" style="padding:10px 22px;border-radius:10px;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#fff;border:none;font-size:13.5px;font-weight:800;cursor:pointer;box-shadow:0 4px 14px rgba(79,70,229,0.3);display:inline-flex;align-items:center;gap:8px;">
                    <i class="bi bi-save-fill"></i> Save Result to Database
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if($page === 'outlines')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
        <p>EXCELLENCE TEACHER PORTAL &bull; SYLLABUS</p>
        <h1>Course Outlines & Syllabi</h1>
    </div>
    <button type="button" onclick="document.getElementById('uploadOutlineModal').style.display='grid'" style="background:#4F46E5;color:#fff;border:none;padding:12px 20px;border-radius:12px;font-weight:700;font-size:13.5px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(79,70,229,0.2);">
        <i class="bi bi-plus-circle"></i> Add Course Outline
    </button>
</div>

{!! $classFilterHtml() !!}

<div class="table-panel">
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Outline Title</th>
                <th>Term</th>
                <th>Description</th>
                <th>File Attached</th>
                <th>Date Uploaded</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outlines as $out)
            <tr>
                <td><strong style="color:#4F46E5;">{{ $out->subject_name }}</strong></td>
                <td><strong>{{ $out->title }}</strong></td>
                <td><span class="status">{{ $out->term }}</span></td>
                <td style="max-width:300px;font-size:12.5px;color:#475569;">{{ $out->description }}</td>
                <td>
                    @if($out->file_path)
                        <a href="{{ asset('storage/'.$out->file_path) }}" target="_blank" style="color:#0284c7;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
                        </a>
                    @else
                        <span style="color:#94a3b8;font-size:12px;">Syllabus Sheet</span>
                    @endif
                </td>
                <td>{{ $out->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#64748b;">No course outlines uploaded yet. Click "Add Course Outline" above.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="uploadOutlineModal" style="display:none;position:fixed;inset:0;background:rgba(7,39,36,0.65);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;">
    <div style="background:#fff;border-radius:22px;width:min(100%,520px);padding:32px 28px;box-shadow:0 28px 70px rgba(0,30,25,0.35);position:relative;animation:securityRise 0.25s ease both;">
        <button type="button" onclick="document.getElementById('uploadOutlineModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:#f0f6f5;border:none;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;color:#475569;display:grid;place-items:center;" title="Close">&times;</button>
        <h3 style="margin:0 0 18px;font-size:20px;font-weight:800;color:#1E1B4B;">Upload Course Outline</h3>
        <form action="{{ route('teacher.outline.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Select Subject</label>
                <select name="subject_name" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                    @foreach($mySubjects as $sub)
                        <option value="{{ $sub }}">{{ $sub }} ({{ $primaryClass }} {{ $primarySection }})</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Outline Title</label>
                <input type="text" name="title" required placeholder="e.g. English Alphabet Phonics & Vocabulary" style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Term</label>
                <select name="term" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                    <option value="First Term">First Term</option>
                    <option value="Mid Term">Mid Term</option>
                    <option value="Final Term">Final Term</option>
                </select>
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Syllabus Summary & Description</label>
                <textarea name="description" rows="3" required placeholder="Detailed topic breakdown, weekly schedules..." style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;"></textarea>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Upload Syllabus File (Optional PDF/Doc)</label>
                <input type="file" name="outline_file" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:10px;font-size:12px;">
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('uploadOutlineModal').style.display='none'" style="padding:10px 18px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;cursor:pointer;font-weight:600;font-size:13px;">Cancel</button>
                <button type="submit" style="padding:10px 20px;border-radius:10px;border:none;background:#4F46E5;color:#fff;cursor:pointer;font-weight:700;font-size:13px;">Save & Publish</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($page === 'salary')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <p>EXCELLENCE TEACHER PORTAL &bull; SALARY & DUES</p>
        <h1>My Salary & Dues Record</h1>
    </div>

    {{-- Month & Year Filter Form --}}
    <form method="GET" action="{{ route('teacher.page', 'salary') }}" style="display:flex;align-items:center;gap:12px;background:#ffffff;border:1px solid #cbd5e1;padding:8px 16px;border-radius:14px;box-shadow:0 3px 10px rgba(0,0,0,0.04);">
        <div style="display:flex;align-items:center;gap:6px;">
            <i class="bi bi-funnel-fill" style="color:#4F46E5;font-size:15px;"></i>
            <span style="font-size:12.5px;font-weight:700;color:#64748b;">Filter:</span>
        </div>

        {{-- Month Selector --}}
        <div style="display:flex;align-items:center;gap:4px;">
            <span style="font-size:12px;font-weight:700;color:#334155;">Month:</span>
            <select name="salary_month" onchange="this.form.submit()" style="border:1px solid #cbd5e1;background:#f8fafc;padding:6px 12px;border-radius:8px;font-weight:700;font-size:13px;color:#0f172a;cursor:pointer;outline:none;">
                <option value="all" {{ ($selectedSalaryMonth ?? 'all') === 'all' ? 'selected' : '' }}>All Months</option>
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mName)
                    <option value="{{ $mName }}" {{ ($selectedSalaryMonth ?? 'all') === $mName ? 'selected' : '' }}>{{ $mName }}</option>
                @endforeach
            </select>
        </div>

        {{-- Year Selector --}}
        <div style="display:flex;align-items:center;gap:4px;">
            <span style="font-size:12px;font-weight:700;color:#334155;">Year:</span>
            <select name="salary_year" onchange="this.form.submit()" style="border:1px solid #cbd5e1;background:#f8fafc;padding:6px 12px;border-radius:8px;font-weight:700;font-size:13px;color:#0f172a;cursor:pointer;outline:none;">
                <option value="all" {{ ($selectedSalaryYear ?? 'all') === 'all' ? 'selected' : '' }}>All Years</option>
                @foreach(['2026', '2025', '2024'] as $yVal)
                    <option value="{{ $yVal }}" {{ ($selectedSalaryYear ?? 'all') === $yVal ? 'selected' : '' }}>{{ $yVal }}</option>
                @endforeach
            </select>
        </div>

        @if(($selectedSalaryMonth ?? 'all') !== 'all' || ($selectedSalaryYear ?? 'all') !== 'all')
            <a href="{{ route('teacher.page', 'salary') }}" style="padding:6px 10px;border-radius:8px;background:#fee2e2;color:#dc2626;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" title="Clear Salary Filters">
                <i class="bi bi-x-circle-fill"></i> Reset
            </a>
        @endif
    </form>
</div>

<div class="metric-grid" style="margin-bottom:24px;">
    <article>
        <small>BASIC SALARY</small>
        <strong style="color:#0f172a;">Rs {{ number_format($latestSalary?->basic_salary ?? 45000) }}</strong>
        <p>Fixed Monthly Base</p>
    </article>
    <article>
        <small>ALLOWANCES</small>
        <strong style="color:#059669;">Rs {{ number_format($latestSalary?->allowances ?? 5000) }}</strong>
        <p>Conveyance & Medical</p>
    </article>
    <article>
        <small>NET PAYABLE</small>
        <strong style="color:#4F46E5;">Rs {{ number_format($latestSalary?->net_salary ?? 50000) }}</strong>
        <p>Total Disbursed Monthly</p>
    </article>
    <article>
        <small>LAST PAYMENT STATUS</small>
        <strong style="color:#059669;">{{ $latestSalary?->status ?? 'Paid' }}</strong>
        <p>Date: {{ $latestSalary?->paid_date ? \Carbon\Carbon::parse($latestSalary->paid_date)->format('d M Y') : '05 Oct 2026' }}</p>
    </article>
</div>

<div class="table-panel">
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Basic Salary</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Net Payable</th>
                <th>Status</th>
                <th>Disbursement Date</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salaries as $sal)
            <tr>
                <td><strong>{{ \Carbon\Carbon::parse($sal->month.'-01')->format('F Y') }}</strong></td>
                <td>Rs {{ number_format($sal->basic_salary) }}</td>
                <td style="color:#059669;">+ Rs {{ number_format($sal->allowances) }}</td>
                <td style="color:#dc2626;">- Rs {{ number_format($sal->deductions) }}</td>
                <td><strong style="color:#4F46E5;font-size:15px;">Rs {{ number_format($sal->net_salary) }}</strong></td>
                <td><span class="status"><i class="bi bi-check-circle-fill"></i> {{ $sal->status }}</span></td>
                <td>{{ $sal->paid_date ? \Carbon\Carbon::parse($sal->paid_date)->format('d M Y') : '—' }}</td>
                <td style="text-align:center;">
                    <a href="{{ route('teacher.salary.download', $sal->id) }}" target="_blank" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#0284c7,#0369a1);color:#ffffff;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:18px;box-shadow:0 4px 12px rgba(2,132,199,0.3);transition:all 0.2s ease;text-decoration:none;" title="Print Official Pay Slip">
                        <i class="bi bi-printer-fill"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:20px;color:#64748b;">No salary records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@if($page === 'attendance')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <div>
        <p>EXCELLENCE TEACHER PORTAL &bull; EXCEL ATTENDANCE MATRIX & TIMESTAMPS</p>
        <h1>Teacher Daily Attendance & Yearly Excel Matrix</h1>
    </div>
    <div style="display:flex;align-items:center;gap:12px;">
        @if($selectedMonth)
            <a href="{{ route('teacher.page', 'attendance') }}" style="background:#f1f5f9;color:#0f172a;border:1px solid #cbd5e1;padding:12px 20px;border-radius:12px;font-weight:700;font-size:13.5px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 2px 6px rgba(0,0,0,0.05);transition:all 0.2s ease;">
                <i class="bi bi-arrow-left"></i> Back to 12-Month Overview
            </a>
        @endif
        <button type="button" onclick="document.getElementById('teacherLeaveModal').style.display='grid'" style="background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#fff;border:none;padding:12px 22px;border-radius:12px;font-weight:700;font-size:13.5px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 6px 16px rgba(79,70,229,0.3);transition:all 0.2s ease;">
            <i class="bi bi-calendar-plus-fill"></i> Apply For Leave
        </button>
    </div>
</div>

<!-- Attendance Punctuality Rules Banner -->
<div style="background:#ffffff;border:1px solid #e2e8f0;border-left:6px solid #4F46E5;border-radius:16px;padding:20px 24px;margin-bottom:24px;box-shadow:0 4px 16px rgba(0,0,0,0.04);display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;align-items:center;">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#dcfce7;color:#15803d;display:grid;place-items:center;font-size:22px;flex-shrink:0;">
            <i class="bi bi-clock-history"></i>
        </div>
        <div>
            <h4 style="margin:0 0 2px;font-size:14.5px;font-weight:800;color:#0f172a;">Official Check-In Window</h4>
            <p style="margin:0;font-size:12.5px;color:#475569;">
                Standard: <strong>08:00 AM</strong> &bull; Grace Period: <strong>Up to 08:10 AM</strong> <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:800;">GREEN</span>
            </p>
        </div>
    </div>

    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#fef2f2;color:#dc2626;display:grid;place-items:center;font-size:22px;flex-shrink:0;">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div>
            <h4 style="margin:0 0 2px;font-size:14.5px;font-weight:800;color:#0f172a;">Late Arrival Cutoff</h4>
            <p style="margin:0;font-size:12.5px;color:#475569;">
                Check-In <strong>08:11 AM & Beyond</strong> <span style="background:#fef2f2;color:#dc2626;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:800;">RED</span>
            </p>
        </div>
    </div>

    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#fff7ed;color:#ea580c;display:grid;place-items:center;font-size:22px;flex-shrink:0;">
            <i class="bi bi-box-arrow-right"></i>
        </div>
        <div>
            <h4 style="margin:0 0 2px;font-size:14.5px;font-weight:800;color:#0f172a;">Check-Out Punctuality</h4>
            <p style="margin:0;font-size:12.5px;color:#475569;">
                Shift Closing: <strong>02:30 PM</strong> &bull; Before 02:30 PM: <span style="background:#fef2f2;color:#dc2626;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:800;">RED EARLY</span>
            </p>
        </div>
    </div>
</div>

<!-- Smart Biometric Punch Terminal Card -->
<div style="background:linear-gradient(135deg,#1E1B4B,#09564f,#06332f);color:#fff;border-radius:22px;padding:28px 32px;margin-bottom:28px;box-shadow:0 14px 36px rgba(7,63,59,0.3);position:relative;overflow:hidden;">
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:24px;margin-bottom:24px;border-bottom:1px solid rgba(255,255,255,0.15);padding-bottom:20px;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);padding:5px 14px;border-radius:99px;font-size:11.5px;font-weight:700;margin-bottom:12px;">
                <i class="bi bi-geo-alt-fill" style="color:#2dd4bf;"></i> Main Campus • Biometric Gate Terminal
            </div>
            <h2 style="margin:0 0 6px;font-size:24px;font-weight:800;letter-spacing:-0.02em;">Teacher Self Punch-In & Punch-Out Terminal</h2>
            <p style="margin:0;color:#99f6e4;font-size:13.5px;">Today: <strong>{{ \Carbon\Carbon::now()->format('l, d F Y') }}</strong> &bull; Live Digital Clock: <strong id="liveDigitalClock" style="font-family:monospace;font-size:16px;color:#ffffff;background:rgba(0,0,0,0.35);padding:3px 10px;border-radius:8px;letter-spacing:1px;">--:--:-- --</strong></p>
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            @if($todayAttendance && $todayAttendance->time_in)
                <div style="background:rgba(45,212,191,0.15);border:1px solid #2dd4bf;color:#2dd4bf;padding:10px 18px;border-radius:14px;font-size:13px;font-weight:700;">
                    <i class="bi bi-check-circle-fill"></i> Time In: {{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('h:i A') }}
                </div>
            @endif
            @if($todayAttendance && $todayAttendance->time_out)
                <div style="background:rgba(56,189,248,0.15);border:1px solid #38bdf8;color:#38bdf8;padding:10px 18px;border-radius:14px;font-size:13px;font-weight:700;">
                    <i class="bi bi-box-arrow-right"></i> Time Out: {{ \Carbon\Carbon::parse($todayAttendance->time_out)->format('h:i A') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Dual Punch Terminal Controls -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:24px;">
        <!-- Left: Punch-In Terminal Box -->
        <div style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.15);border-radius:18px;padding:22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#ffffff;display:inline-flex;align-items:center;gap:8px;">
                    <i class="bi bi-fingerprint" style="color:#2dd4bf;font-size:20px;"></i> Check-In (Time In)
                </h3>
                <span style="font-size:11px;color:#99f6e4;background:rgba(20,184,166,0.2);padding:3px 8px;border-radius:6px;font-weight:700;">Cutoff: 08:10 AM</span>
            </div>
            
            <form action="{{ route('teacher.punchIn') }}" method="POST" style="margin:0;">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:11px;color:#C7D2FE;font-weight:700;margin-bottom:6px;text-transform:uppercase;">Select / Custom Punch-In Time</label>
                    <div style="display:flex;gap:8px;">
                        <input type="time" name="custom_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.2);color:#ffffff;padding:10px 14px;border-radius:10px;font-size:14px;font-weight:700;flex-grow:1;">
                        <button type="submit" style="background:linear-gradient(135deg,#14b8a6,#0d9488);color:#fff;border:none;padding:10px 20px;border-radius:10px;font-weight:800;font-size:13.5px;cursor:pointer;white-space:nowrap;box-shadow:0 4px 14px rgba(20,184,166,0.4);">
                            Punch-In <i class="bi bi-arrow-right-short"></i>
                        </button>
                    </div>
                </div>

                <!-- Simulation Shortcut Pills for Testing -->
                <div style="display:flex;align-items:center;gap:8px;font-size:11.5px;color:#99f6e4;">
                    <span>Quick Test:</span>
                    <button type="submit" name="custom_time" value="07:55" style="background:rgba(34,197,94,0.2);border:1px solid #4ade80;color:#4ade80;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">07:55 AM (Green)</button>
                    <button type="submit" name="custom_time" value="08:15" style="background:rgba(239,68,68,0.2);border:1px solid #f87171;color:#f87171;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">08:15 AM (Red Late)</button>
                </div>
            </form>
        </div>

        <!-- Right: Punch-Out Terminal Box -->
        <div style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.15);border-radius:18px;padding:22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#ffffff;display:inline-flex;align-items:center;gap:8px;">
                    <i class="bi bi-box-arrow-right" style="color:#38bdf8;font-size:20px;"></i> Check-Out (Time Out)
                </h3>
                <span style="font-size:11px;color:#7dd3fc;background:rgba(56,189,248,0.2);padding:3px 8px;border-radius:6px;font-weight:700;">Closing: 02:30 PM</span>
            </div>

            <form action="{{ route('teacher.punchOut') }}" method="POST" style="margin:0;">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:11px;color:#e0f2fe;font-weight:700;margin-bottom:6px;text-transform:uppercase;">Select / Custom Punch-Out Time</label>
                    <div style="display:flex;gap:8px;">
                        <input type="time" name="custom_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.2);color:#ffffff;padding:10px 14px;border-radius:10px;font-size:14px;font-weight:700;flex-grow:1;">
                        <button type="submit" style="background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;border:none;padding:10px 20px;border-radius:10px;font-weight:800;font-size:13.5px;cursor:pointer;white-space:nowrap;box-shadow:0 4px 14px rgba(2,132,199,0.4);">
                            Punch-Out <i class="bi bi-arrow-right-short"></i>
                        </button>
                    </div>
                </div>

                <!-- Simulation Shortcut Pills for Testing -->
                <div style="display:flex;align-items:center;gap:8px;font-size:11.5px;color:#7dd3fc;">
                    <span>Quick Test:</span>
                    <button type="submit" name="custom_time" value="13:45" style="background:rgba(239,68,68,0.2);border:1px solid #f87171;color:#f87171;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">01:45 PM (Red Early)</button>
                    <button type="submit" name="custom_time" value="14:30" style="background:rgba(34,197,94,0.2);border:1px solid #4ade80;color:#4ade80;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">02:30 PM (Green Shift)</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Analytics Active Monthly Metrics Cards -->
<div class="metric-grid" style="margin-bottom:28px;">
    <article>
        <span class="metric-icon blue"><i class="bi bi-pie-chart-fill"></i></span>
        <small>{{ strtoupper($activeMonthName) }} ATTENDANCE RATE</small>
        <strong style="color:#4F46E5;">{{ $monthPct }}%</strong>
        <p>Punctuality Score: {{ $monthPresent }} / {{ $monthWorkingDays }} Duty Days</p>
    </article>
    <article>
        <span class="metric-icon green"><i class="bi bi-calendar-check-fill"></i></span>
        <small>DAYS PRESENT ({{ strtoupper($activeMonthName) }})</small>
        <strong style="color:#059669;">{{ $monthPresent }} Days Present</strong>
        <p>{{ $monthLate }} Late Check-Ins recorded</p>
    </article>
    <article class="fee-card" style="border-color:#a7f3d0;background:#f0fdf4;">
        <span class="metric-icon amber" style="background:#dcfce7;color:#15803d;"><i class="bi bi-balloon-heart-fill"></i></span>
        <small style="color:#166534;">ALLOWED MONTHLY LEAVES</small>
        <strong style="color:#15803d;">{{ $leavesRemainingCount }} / {{ $leavesAllowedCount }} Free Left</strong>
        <p style="color:#166534;">{{ $monthLeave }} Approved Leaves in {{ $activeMonthName }}</p>
    </article>
    <article>
        <span class="metric-icon purple"><i class="bi bi-person-workspace"></i></span>
        <small>ABSENCES & WEEKENDS</small>
        <strong style="color:#dc2626;">{{ $monthAbsent }} Absences</strong>
        <p>{{ $monthHoliday }} Weekend Offs in {{ $activeMonthName }}</p>
    </article>
</div>

<!-- View Mode Toggle & Month Filter Bar -->
<div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;background:#ffffff;border:1px solid #e2e8f0;padding:16px 20px;border-radius:18px;box-shadow:0 4px 14px rgba(0,0,0,0.03);">
    <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:13px;font-weight:700;color:#64748b;">View Mode:</span>
        <a href="{{ route('teacher.page', ['page' => 'attendance', 'view' => 'monthly', 'month' => $selectedMonth ?: \Carbon\Carbon::now()->format('Y-m')]) }}" style="padding:9px 20px;border-radius:12px;font-size:13px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:8px;{{ $selectedView !== 'yearly' ? 'background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;box-shadow:0 4px 12px rgba(79,70,229,0.3);' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;' }}">
            <i class="bi bi-calendar3"></i> Monthly Daily Logs
        </a>
        <a href="{{ route('teacher.page', ['page' => 'attendance', 'view' => 'yearly']) }}" style="padding:9px 20px;border-radius:12px;font-size:13px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:8px;{{ $selectedView === 'yearly' ? 'background:linear-gradient(135deg,#107c41,#0b5a2f);color:#ffffff;box-shadow:0 4px 12px rgba(16,124,65,0.3);' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;' }}">
            <i class="bi bi-grid-3x3-gap-fill"></i> Yearly 12-Month Summary
        </a>
    </div>

    @if($selectedView !== 'yearly')
    <form action="{{ route('teacher.page', 'attendance') }}" method="GET" id="monthFilterForm" style="margin:0;display:flex;align-items:center;gap:8px;">
        <input type="hidden" name="view" value="monthly">
        <label style="font-size:12.5px;font-weight:700;color:#475569;">Select Month:</label>
        <select name="month" onchange="document.getElementById('monthFilterForm').submit()" style="padding:9px 16px;border:1.5px solid #4F46E5;border-radius:10px;font-size:13px;font-weight:800;color:#0f172a;background:#EEF2FF;cursor:pointer;">
            <option value="">All 12 Months (Full Year)</option>
            @foreach($monthlySummaries as $ms)
                <option value="{{ $ms['month_key'] }}" {{ $selectedMonth === $ms['month_key'] ? 'selected' : '' }}>
                    {{ $ms['month_name'] }} ({{ $ms['total_days'] }} Days)
                </option>
            @endforeach
        </select>
        @if($selectedMonth)
            <a href="{{ route('teacher.page', ['page' => 'attendance', 'view' => 'monthly']) }}" style="padding:9px 14px;border-radius:10px;background:#e2e8f0;color:#334155;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        @endif
    </form>
    @endif
</div>

@if($selectedView === 'yearly')
<!-- SECTION: 12-Month Yearly Excel Matrix Grid (Shown ONLY in Yearly View Mode) -->
<div class="student-panel" style="margin-bottom:28px;padding:26px;">
    <div class="panel-head" style="margin-bottom:20px;border-bottom:1px solid #f1f5f9;padding-bottom:14px;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h2><i class="bi bi-file-earmark-excel-fill" style="color:#107c41;"></i> {{ $selectedYear }} Yearly 12-Month Attendance Matrix Summary</h2>
            <p>Click on any month card below to drill down into daily attendance timestamps for that month.</p>
        </div>
        <div>
            <span style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:800;">
                <i class="bi bi-calendar3"></i> 12 Months Summary
            </span>
        </div>
    </div>

    <!-- 12-Month Cards Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:16px;">
        @foreach($monthlySummaries as $ms)
        <a href="{{ route('teacher.page', ['page' => 'attendance', 'view' => 'monthly', 'month' => $ms['month_key']]) }}" style="text-decoration:none;display:block;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:18px;box-shadow:0 3px 10px rgba(0,0,0,0.03);transition:all 0.2s ease;position:relative;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <span style="font-size:14px;font-weight:800;color:#0f172a;">{{ $ms['month_name'] }}</span>
                <span style="font-size:12px;font-weight:800;color:#107c41;background:#dcfce7;padding:3px 8px;border-radius:8px;">{{ $ms['rate'] }}%</span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:11.5px;color:#475569;margin-bottom:12px;">
                <div style="background:#f8fafc;padding:6px 8px;border-radius:8px;border:1px solid #f1f5f9;">
                    <span style="display:block;color:#64748b;font-size:10px;text-transform:uppercase;">Present</span>
                    <strong style="color:#059669;font-size:13px;">{{ $ms['present_count'] }} Days</strong>
                </div>
                <div style="background:#f8fafc;padding:6px 8px;border-radius:8px;border:1px solid #f1f5f9;">
                    <span style="display:block;color:#64748b;font-size:10px;text-transform:uppercase;">Late</span>
                    <strong style="color:#dc2626;font-size:13px;">{{ $ms['late_count'] }} Days</strong>
                </div>
                <div style="background:#f8fafc;padding:6px 8px;border-radius:8px;border:1px solid #f1f5f9;">
                    <span style="display:block;color:#64748b;font-size:10px;text-transform:uppercase;">Leaves</span>
                    <strong style="color:#0284c7;font-size:13px;">{{ $ms['leave_count'] }} Days</strong>
                </div>
                <div style="background:#f8fafc;padding:6px 8px;border-radius:8px;border:1px solid #f1f5f9;">
                    <span style="display:block;color:#64748b;font-size:10px;text-transform:uppercase;">Weekends</span>
                    <strong style="color:#854d0e;font-size:13px;">{{ $ms['holiday_count'] }} Off</strong>
                </div>
            </div>

            <div style="font-size:11.5px;color:#107c41;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                View Excel Daily Logs <i class="bi bi-chevron-right" style="font-size:10px;"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>
@else
<!-- SECTION: Daily Attendance Excel Spreadsheet Table with Month Filter & Pagination -->
<div class="student-panel" style="padding:26px;">
    <div class="panel-head" style="margin-bottom:20px;border-bottom:1px solid #f1f5f9;padding-bottom:16px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
        <div>
            <h2><i class="bi bi-table" style="color:#4F46E5;"></i> Daily Attendance Logs (Excel Sheet View)</h2>
            <p>Showing logs for: <strong style="color:#0f172a;">{{ $selectedMonth ? \Carbon\Carbon::parse($selectedMonth.'-01')->format('F Y') : 'All 2026 Months' }}</strong></p>
        </div>
        <div>
            <span style="background:#e0f2fe;color:#0284c7;border:1px solid #bae6fd;padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:800;">
                <i class="bi bi-file-earmark-spreadsheet"></i> {{ $attendancesPaginated->total() }} Records Total
            </span>
        </div>
    </div>

    <div class="table-panel" style="border:1px solid #cbd5e1;border-radius:14px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.03);">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#107c41;color:#ffffff;">
                    <th style="padding:12px 14px;text-align:center;font-size:11.5px;text-transform:uppercase;border-right:1px solid rgba(255,255,255,0.2);">#</th>
                    <th style="padding:12px 14px;text-align:left;font-size:11.5px;text-transform:uppercase;border-right:1px solid rgba(255,255,255,0.2);">Date</th>
                    <th style="padding:12px 14px;text-align:left;font-size:11.5px;text-transform:uppercase;border-right:1px solid rgba(255,255,255,0.2);">Day</th>
                    <th style="padding:12px 14px;text-align:left;font-size:11.5px;text-transform:uppercase;border-right:1px solid rgba(255,255,255,0.2);">Check In (Time In)</th>
                    <th style="padding:12px 14px;text-align:left;font-size:11.5px;text-transform:uppercase;border-right:1px solid rgba(255,255,255,0.2);">Check Out (Time Out)</th>
                    <th style="padding:12px 14px;text-align:left;font-size:11.5px;text-transform:uppercase;border-right:1px solid rgba(255,255,255,0.2);">Attendance Status</th>
                    <th style="padding:12px 14px;text-align:left;font-size:11.5px;text-transform:uppercase;">Remarks & Verification</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendancesPaginated as $index => $att)
                @php 
                    $dt = \Carbon\Carbon::parse($att->date); 
                    $timeInFormatted = $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('h:i A') : null;
                    $timeOutFormatted = $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('h:i A') : null;
                    
                    $isTimeInLate = $att->time_in && (\Carbon\Carbon::parse($att->time_in)->format('H:i:s') > '08:10:00');
                    $isTimeOutEarly = $att->time_out && (\Carbon\Carbon::parse($att->time_out)->format('H:i:s') < '14:30:00');
                    
                    $rowNum = $attendancesPaginated->firstItem() + $index;
                @endphp
                <tr style="border-bottom:1px solid #e2e8f0;background:{{ $index % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                    <td style="text-align:center;font-weight:700;color:#64748b;font-size:12px;border-right:1px solid #e2e8f0;background:#f1f5f9;">{{ $rowNum }}</td>
                    <td style="padding:12px 14px;border-right:1px solid #e2e8f0;"><strong style="color:#0f172a;">{{ $dt->format('d M Y') }}</strong></td>
                    <td style="padding:12px 14px;font-weight:600;color:#64748b;border-right:1px solid #e2e8f0;">{{ $dt->format('l') }}</td>
                    
                    <!-- Check-In (Time In) Cell with Color-Coding -->
                    <td style="padding:12px 14px;border-right:1px solid #e2e8f0;">
                        @if($att->time_in)
                            @if($isTimeInLate)
                                <span style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:5px 12px;border-radius:10px;font-weight:800;font-size:12.5px;" title="Late Check-In (Past 08:10 AM grace window)">
                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $timeInFormatted }} <small style="font-size:10px;opacity:0.9;">(Late)</small>
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;padding:5px 12px;border-radius:10px;font-weight:800;font-size:12.5px;" title="On-Time Check-In">
                                    <i class="bi bi-check-circle-fill"></i> {{ $timeInFormatted }} <small style="font-size:10px;opacity:0.9;">(On Time)</small>
                                </span>
                            @endif
                        @else
                            <span style="color:#94a3b8;font-size:13px;font-weight:500;">—</span>
                        @endif
                    </td>

                    <!-- Check-Out (Time Out) Cell with Color-Coding -->
                    <td style="padding:12px 14px;border-right:1px solid #e2e8f0;">
                        @if($att->time_out)
                            @if($isTimeOutEarly)
                                <span style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:5px 12px;border-radius:10px;font-weight:800;font-size:12.5px;" title="Early Departure (Before 02:30 PM shift end)">
                                    <i class="bi bi-exclamation-octagon-fill"></i> {{ $timeOutFormatted }} <small style="font-size:10px;opacity:0.9;">(Early)</small>
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;padding:5px 12px;border-radius:10px;font-weight:800;font-size:12.5px;" title="Full Duty Shift Completed">
                                    <i class="bi bi-check-circle-fill"></i> {{ $timeOutFormatted }} <small style="font-size:10px;opacity:0.9;">(Full Shift)</small>
                                </span>
                            @endif
                        @else
                            <span style="color:#94a3b8;font-size:13px;font-weight:500;">—</span>
                        @endif
                    </td>

                    <!-- Overall Attendance Status Cell -->
                    <td style="padding:12px 14px;border-right:1px solid #e2e8f0;">
                        @if($att->status === 'present')
                            <span class="status" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;font-weight:800;"><i class="bi bi-check-circle-fill"></i> Present (On Time)</span>
                        @elseif($att->status === 'late')
                            <span class="status" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-weight:800;"><i class="bi bi-exclamation-triangle-fill"></i> Late Check-In</span>
                        @elseif($att->status === 'leave')
                            <span class="status" style="background:#e0f2fe;color:#0284c7;border:1px solid #bae6fd;font-weight:800;"><i class="bi bi-calendar-check-fill"></i> Approved Leave</span>
                        @elseif($att->status === 'holiday')
                            <span class="status" style="background:#fef08a;color:#854d0e;border:1px solid #fde047;font-weight:700;"><i class="bi bi-star-fill"></i> Weekend Off</span>
                        @else
                            <span class="status" style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;font-weight:800;"><i class="bi bi-x-circle-fill"></i> Uninformed Absence</span>
                        @endif
                    </td>

                    <td style="padding:12px 14px;color:#475569;font-size:12.5px;font-weight:600;">{{ $att->remarks ?: 'Normal Duty' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:28px;color:#64748b;">No attendance records found for this month filter.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Controls Bar -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;background:#ffffff;border-top:1px solid #e2e8f0;">
            <div style="font-size:13.5px;color:#64748b;font-weight:500;">
                Showing <strong style="color:#0f172a;font-weight:700;">{{ $attendancesPaginated->firstItem() ?? 0 }}</strong> to <strong style="color:#0f172a;font-weight:700;">{{ $attendancesPaginated->lastItem() ?? 0 }}</strong> of <strong style="color:#0f172a;font-weight:700;">{{ $attendancesPaginated->total() }}</strong> daily log records
            </div>

            @if($attendancesPaginated->hasPages())
            <div style="display:flex;align-items:center;gap:8px;">
                {{-- Previous Page Link --}}
                @if ($attendancesPaginated->onFirstPage())
                    <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                        <i class="bi bi-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $attendancesPaginated->previousPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                @endif

                {{-- Page Numbers Range --}}
                @foreach ($attendancesPaginated->getUrlRange(max(1, $attendancesPaginated->currentPage() - 2), min($attendancesPaginated->lastPage(), $attendancesPaginated->currentPage() + 2)) as $pageNumber => $url)
                    @if ($pageNumber == $attendancesPaginated->currentPage())
                        <span style="padding:8px 16px;border-radius:11px;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;font-size:13px;font-weight:800;border:none;box-shadow:0 4px 12px rgba(79,70,229,0.35);display:inline-flex;align-items:center;justify-content:center;">
                            {{ $pageNumber }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="padding:8px 16px;border-radius:11px;border:1px solid #cbd5e1;background:#ffffff;color:#334155;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                            {{ $pageNumber }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($attendancesPaginated->hasMorePages())
                    <a href="{{ $attendancesPaginated->nextPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                        Next <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Modal Popup for Applying Teacher Leave -->
<div id="teacherLeaveModal" style="display:none;position:fixed;inset:0;background:rgba(7,39,36,0.65);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;">
    <div style="background:#fff;border-radius:22px;width:min(100%,540px);padding:32px 28px;box-shadow:0 28px 70px rgba(0,30,25,0.35);position:relative;animation:securityRise 0.25s ease both;">
        <button type="button" onclick="document.getElementById('teacherLeaveModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:#f0f6f5;border:none;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;color:#475569;display:grid;place-items:center;" title="Close">&times;</button>
        
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
            <div style="width:42px;height:42px;border-radius:12px;background:#e0f2fe;color:#0284c7;display:grid;place-items:center;font-size:20px;">
                <i class="bi bi-calendar-plus-fill"></i>
            </div>
            <div>
                <h3 style="margin:0;font-size:20px;font-weight:800;color:#1E1B4B;">Apply For Leave</h3>
                <p style="margin:2px 0 0;font-size:12.5px;color:#64748b;">Quota: <strong>2 Allowed Paid Leaves / Month</strong> &bull; Remaining: <strong style="color:#059669;">{{ $leavesRemainingCount }} Free</strong></p>
            </div>
        </div>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:12.5px;color:#166534;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-info-circle-fill" style="font-size:16px;color:#15803d;flex-shrink:0;"></i>
            <span>Approved leaves automatically sync into your daily attendance as <strong>Approved Leave</strong> so it is never marked as an absence!</span>
        </div>

        <form action="{{ route('teacher.leave.apply') }}" method="POST">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Select Leave Type</label>
                <select name="leave_type" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;font-weight:600;">
                    <option value="Casual Leave (CL)">Casual Leave (CL)</option>
                    <option value="Sick Leave (SL)">Sick Leave (SL)</option>
                    <option value="Personal Emergency">Personal Emergency</option>
                    <option value="Special Duty Leave">Special Duty Leave</option>
                </select>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Leave Date</label>
                <input type="date" name="leave_date" value="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Reason for Leave</label>
                <textarea name="reason" rows="3" required placeholder="Please enter reason for requesting leave..." style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;resize:vertical;"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="document.getElementById('teacherLeaveModal').style.display='none'" style="padding:10px 20px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:#475569;font-size:13px;font-weight:700;cursor:pointer;">Cancel</button>
                <button type="submit" style="padding:10px 24px;border-radius:10px;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#fff;border:none;font-size:13px;font-weight:800;cursor:pointer;box-shadow:0 4px 14px rgba(79,70,229,0.3);">
                    Submit Leave Application
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateLiveClock() {
    const clockEl = document.getElementById('liveDigitalClock');
    if (clockEl) {
        const now = new Date();
        clockEl.innerText = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}
setInterval(updateLiveClock, 1000);
updateLiveClock();
</script>
@endif

@if($page === 'tests')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <p>EXCELLENCE TEACHER PORTAL &bull; MONTHLY TEST EVALUATION</p>
        <h1>{{ $primaryClass }} {{ $primarySection }} &bull; Monthly Test Sessions</h1>
    </div>
    
    <div style="display:flex;align-items:center;gap:12px;">
        {{-- MONTH FILTER DROPDOWN --}}
        <div style="display:inline-flex;align-items:center;gap:8px;background:#ffffff;border:1px solid #cbd5e1;padding:6px 14px;border-radius:12px;box-shadow:0 2px 6px rgba(0,0,0,0.03);">
            <i class="bi bi-calendar3" style="color:#4F46E5;font-size:16px;"></i>
            <span style="font-size:12.5px;font-weight:700;color:#64748b;">Select Month:</span>
            <select id="teacherTestMonthSelect" onchange="window.location.href='{{ route('teacher.page', ['page' => 'tests']) }}?month=' + this.value + '&class={{ $primaryClass }}'" style="border:none;background:transparent;font-weight:800;font-size:14px;color:#0f172a;cursor:pointer;outline:none;">
                @foreach($testMonthsList as $mK => $mLabel)
                    <option value="{{ $mK }}" {{ ($selectedTestMonth ?? '2026-10') === $mK ? 'selected' : '' }}>{{ $mLabel }}</option>
                @endforeach
            </select>
        </div>

        @if($isClassIncharge)
            <button type="button" onclick="openAddTestSessionModal()" style="background:#4F46E5;color:#fff;border:none;padding:10px 20px;border-radius:12px;font-weight:800;font-size:13.5px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(79,70,229,0.25);cursor:pointer;">
                <i class="bi bi-plus-circle-fill"></i> Add Test Result
            </button>
        @else
            <span style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:800;">
                <i class="bi bi-lock-fill"></i> Subject Teacher (Read-Only View)
            </span>
        @endif
    </div>
</div>

{!! $classFilterHtml() !!}

@if(!$isClassIncharge)
    <div style="background:#fffbeb;border:1px solid #fde68a;color:#b45309;padding:16px 20px;border-radius:14px;margin-bottom:24px;display:flex;align-items:center;gap:14px;">
        <i class="bi bi-shield-lock-fill" style="font-size:26px;color:#d97706;"></i>
        <div>
            <strong style="display:block;font-size:14.5px;">🔒 Read-Only Access Mode (Subject Teacher)</strong>
            <span style="font-size:13px;">You can view the full subject-wise test marksheets of all students. Editing or entering marks is restricted to the Class In-Charge teacher (<strong>{{ $classInchargeTeacherName }}</strong>) for {{ $primaryClass }} ({{ $primarySection }}).</span>
        </div>
    </div>
@else
    <div style="background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;padding:16px 22px;border-radius:16px;margin-bottom:24px;box-shadow:0 10px 25px rgba(79,70,229,0.22);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.18);display:grid;place-items:center;font-size:24px;"><i class="bi bi-journal-check"></i></div>
            <div>
                <span style="font-size:11px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#a7f3d0;">👑 Class In-Charge Management Access</span>
                <h3 style="margin:2px 0 0;font-size:17px;font-weight:800;color:#ffffff;">1-Week Monthly Test Series Evaluation & Marks Entry</h3>
            </div>
        </div>
        <div style="font-size:12.5px;background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:10px;">
            Active Month: <strong>{{ $testMonthsList[$selectedTestMonth ?? '2026-10'] ?? 'October 2026' }}</strong>
        </div>
    </div>
@endif

<div class="table-panel">
    <table>
        <thead>
            <tr>
                <th>Roll #</th>
                <th>Student Name</th>
                <th>Class & Sec</th>
                <th>Session Month</th>
                <th style="text-align:center;">Obtained / Max Marks</th>
                <th style="text-align:center;">Percentage & Grade</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testStudentsPaginated as $std)
            @php
                $tSession = $testSessionsMap[$std->id] ?? null;
                $sched = [];
                if($tSession) {
                    $sched = is_string($tSession->schedule_json) ? json_decode($tSession->schedule_json, true) : ($tSession->schedule_json ?: []);
                }
            @endphp
            <tr>
                <td><strong style="color:#4F46E5;font-size:14px;">#{{ $std->std_id }}</strong></td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:13px;">
                            {{ strtoupper(substr($std->name, 0, 1)) }}
                        </div>
                        <div>
                            <strong style="color:#0f172a;font-size:14px;display:block;">{{ $std->name }}</strong>
                            <span style="font-size:11px;color:#64748b;">Enrolled Student</span>
                        </div>
                    </div>
                </td>
                <td><strong>{{ $std->class }}</strong> ({{ $std->section }})</td>
                <td><span class="status" style="background:#EEF2FF;color:#4F46E5;border:1px solid #C7D2FE;font-weight:700;"><i class="bi bi-calendar-event"></i> {{ $testMonthsList[$selectedTestMonth ?? '2026-10'] ?? 'October 2026' }}</span></td>
                <td style="text-align:center;"><strong style="font-size:14px;color:#0f172a;">{{ $tSession?->total_obtained_marks ?? 0 }} / {{ $tSession?->total_max_marks ?? 300 }}</strong></td>
                <td style="text-align:center;">
                    <span style="background:{{ ($tSession?->percentage ?? 0) >= 80 ? '#dcfce7' : (($tSession?->percentage ?? 0) >= 60 ? '#fef3c7' : '#fee2e2') }};color:{{ ($tSession?->percentage ?? 0) >= 80 ? '#15803d' : (($tSession?->percentage ?? 0) >= 60 ? '#b45309' : '#b91c1c') }};padding:4px 12px;border-radius:99px;font-weight:800;font-size:12.5px;">
                        Grade {{ $tSession?->grade ?: 'A' }} ({{ $tSession?->percentage ?: 0 }}%)
                    </span>
                </td>
                <td style="text-align:center;">
                    <span class="status" style="background:#ecfdf5;color:#047857;font-weight:700;"><i class="bi bi-check-circle-fill"></i> {{ $tSession?->status ?: 'Evaluated' }}</span>
                </td>
                <td style="text-align:center;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                        {{-- Eye View Modal Button for ALL Teachers --}}
                        <button type="button" onclick="openTestSessionViewModal({{ json_encode([
                            'student_id' => $std->id,
                            'name' => $std->name,
                            'roll_no' => $std->std_id,
                            'class' => $std->class,
                            'section' => $std->section ?: 'Red',
                            'month_title' => $testMonthsList[$selectedTestMonth ?? '2026-10'] ?? 'October 2026',
                            'total_max' => $tSession?->total_max_marks ?? 300,
                            'total_obtained' => $tSession?->total_obtained_marks ?? 0,
                            'percentage' => $tSession?->percentage ?? 0,
                            'grade' => $tSession?->grade ?: 'A',
                            'schedule' => $sched
                        ]) }})" style="width:36px;height:36px;border-radius:10px;background:#e0f2fe;color:#0284c7;border:1px solid #bae6fd;display:inline-grid;place-items:center;cursor:pointer;font-size:16px;" title="View Complete Test Marksheet (All Subjects)">
                            <i class="bi bi-eye-fill"></i>
                        </button>

                        {{-- Edit Button ONLY for Class In-Charge --}}
                        @if($isClassIncharge)
                            <button type="button" onclick="openTestSessionEditModal({{ json_encode([
                                'student_id' => $std->id,
                                'name' => $std->name,
                                'roll_no' => $std->std_id,
                                'class' => $std->class,
                                'section' => $std->section ?: 'Red',
                                'month_key' => $selectedTestMonth ?? '2026-10',
                                'month_title' => $testMonthsList[$selectedTestMonth ?? '2026-10'] ?? 'October 2026',
                                'schedule' => $sched
                            ]) }})" style="width:36px;height:36px;border-radius:10px;background:#4F46E5;color:#ffffff;border:none;display:inline-grid;place-items:center;cursor:pointer;font-size:16px;box-shadow:0 3px 8px rgba(79,70,229,0.25);" title="Edit / Update Test Marks (Class In-Charge)">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:24px;color:#64748b;">No students found in {{ $primaryClass }} {{ $primarySection }}.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINATION CONTROLS (10 PER PAGE) --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;background:#ffffff;border-top:1px solid #e2e8f0;border-radius:0 0 18px 18px;">
        <div style="font-size:13.5px;color:#64748b;font-weight:500;">
            Showing <strong style="color:#0f172a;font-weight:700;">{{ $testStudentsPaginated->firstItem() ?? 0 }}</strong> to <strong style="color:#0f172a;font-weight:700;">{{ $testStudentsPaginated->lastItem() ?? 0 }}</strong> of <strong style="color:#0f172a;font-weight:700;">{{ $testStudentsPaginated->total() }}</strong> student records
        </div>

        @if($testStudentsPaginated->hasPages())
        <div style="display:flex;align-items:center;gap:8px;">
            {{-- Previous Page Link --}}
            @if ($testStudentsPaginated->onFirstPage())
                <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                    <i class="bi bi-chevron-left"></i> Previous
                </span>
            @else
                <a href="{{ $testStudentsPaginated->previousPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($testStudentsPaginated->getUrlRange(1, $testStudentsPaginated->lastPage()) as $pageNumber => $url)
                @if ($pageNumber == $testStudentsPaginated->currentPage())
                    <span style="padding:8px 16px;border-radius:11px;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;font-size:13px;font-weight:800;border:none;box-shadow:0 4px 12px rgba(79,70,229,0.35);display:inline-flex;align-items:center;justify-content:center;">
                        {{ $pageNumber }}
                    </span>
                @else
                    <a href="{{ $url }}" style="padding:8px 16px;border-radius:11px;border:1px solid #cbd5e1;background:#ffffff;color:#334155;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                        {{ $pageNumber }}
                    </a>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($testStudentsPaginated->hasMorePages())
                <a href="{{ $testStudentsPaginated->nextPageUrl() }}" style="padding:8px 16px;border-radius:11px;border:1px solid #4F46E5;background:#EEF2FF;color:#4F46E5;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(79,70,229,0.12);">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span style="padding:8px 16px;border-radius:11px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:not-allowed;">
                    Next <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- VIEW STUDENT TEST SESSION MARKSHEET MODAL --}}
<div id="viewTestSessionModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.65);backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;">
    <div style="background:#ffffff;border-radius:20px;width:min(100%,680px);padding:28px;box-shadow:0 25px 60px rgba(0,0,0,0.3);position:relative;animation:securityRise 0.2s ease both;">
        <button type="button" onclick="document.getElementById('viewTestSessionModal').style.display='none'" style="position:absolute;top:16px;right:16px;border:none;background:#f1f5f9;width:34px;height:34px;border-radius:50%;font-size:20px;cursor:pointer;color:#64748b;display:grid;place-items:center;" title="Close">&times;</button>

        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;border-bottom:1px solid #f1f5f9;padding-bottom:14px;">
            <div id="tModalAvatar" style="width:48px;height:48px;border-radius:50%;background:#4F46E5;color:#fff;display:grid;place-items:center;font-weight:800;font-size:18px;">A</div>
            <div>
                <h3 id="tModalStdName" style="margin:0;font-size:18px;color:#0f172a;font-weight:800;">Student Name</h3>
                <span id="tModalSubTitle" style="font-size:12.5px;color:#4F46E5;font-weight:700;">Class & Roll Info</span>
            </div>
            <div style="margin-left:auto;text-align:right;">
                <span id="tModalGradeBadge" style="background:#dcfce7;color:#15803d;padding:4px 14px;border-radius:99px;font-weight:800;font-size:13px;display:inline-block;">Grade A (88%)</span>
            </div>
        </div>

        <h4 style="margin:0 0 12px;font-size:14px;color:#475569;font-weight:700;">1-Week Subject Date Sheet & Marks Breakdown</h4>
        <div style="max-height:340px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:20px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;text-align:left;">
                <thead style="position:sticky;top:0;background:#f8fafc;z-index:1;">
                    <tr style="border-bottom:1px solid #cbd5e1;">
                        <th style="padding:10px 12px;color:#475569;">Date & Day</th>
                        <th style="padding:10px 12px;color:#475569;">Subject Exam</th>
                        <th style="padding:10px 12px;color:#475569;">Timing & Venue</th>
                        <th style="padding:10px 12px;text-align:center;color:#475569;">Obtained / Max Marks</th>
                    </tr>
                </thead>
                <tbody id="tModalTableBody">
                    <!-- Populated via JavaScript -->
                </tbody>
            </table>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="button" onclick="document.getElementById('viewTestSessionModal').style.display='none'" style="padding:10px 20px;border-radius:10px;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;font-size:13px;font-weight:700;cursor:pointer;">Close Window</button>
        </div>
    </div>
</div>

{{-- EDIT STUDENT TEST MARKS MODAL (CLASS IN-CHARGE ONLY) --}}
@if($isClassIncharge)
<div id="editTestMarksModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.65);backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;">
    <div style="background:#ffffff;border-radius:20px;width:min(100%,560px);padding:28px;box-shadow:0 25px 60px rgba(0,0,0,0.3);position:relative;animation:securityRise 0.2s ease both;">
        <button type="button" onclick="document.getElementById('editTestMarksModal').style.display='none'" style="position:absolute;top:16px;right:16px;border:none;background:#f1f5f9;width:34px;height:34px;border-radius:50%;font-size:20px;cursor:pointer;color:#64748b;display:grid;place-items:center;" title="Close">&times;</button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">
            <div style="width:42px;height:42px;border-radius:12px;background:#e0f2fe;color:#0284c7;display:grid;place-items:center;font-size:20px;"><i class="bi bi-pencil-square"></i></div>
            <div>
                <h3 style="margin:0;font-size:18px;color:#0f172a;font-weight:800;">Edit Test Session Marks</h3>
                <span id="eModalSubTitle" style="font-size:12.5px;color:#4F46E5;font-weight:700;">Student & Session Month</span>
            </div>
        </div>

        <form action="{{ route('teacher.test-marks.update') }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" id="eModalStudentId">
            <input type="hidden" name="month_key" id="eModalMonthKey">

            <div style="max-height:300px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;padding:14px;margin-bottom:20px;display:flex;flex-direction:column;gap:12px;" id="eModalMarksInputs">
                <!-- Subject Marks Inputs Populated via JS -->
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('editTestMarksModal').style.display='none'" style="padding:10px 18px;border-radius:10px;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;font-weight:700;font-size:13px;cursor:pointer;">Cancel</button>
                <button type="submit" style="padding:10px 22px;border-radius:10px;border:none;background:#4F46E5;color:#fff;font-weight:800;font-size:13px;cursor:pointer;box-shadow:0 4px 14px rgba(79,70,229,0.25);">
                    Save & Update Marks
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function openTestSessionViewModal(data) {
    document.getElementById('tModalStdName').innerText = data.name;
    document.getElementById('tModalSubTitle').innerText = 'Class: ' + data.class + ' (' + data.section + ') • Roll #' + data.roll_no + ' • ' + data.month_title;
    document.getElementById('tModalAvatar').innerText = data.name.charAt(0).toUpperCase();
    document.getElementById('tModalGradeBadge').innerText = 'Grade ' + data.grade + ' (' + data.percentage + '%)';

    const tbody = document.getElementById('tModalTableBody');
    tbody.innerHTML = '';

    const sched = data.schedule || [];
    sched.forEach(row => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid #f1f5f9';
        if (row.is_off) {
            tr.style.background = '#f8fafc';
            tr.innerHTML = `
                <td style="padding:10px 12px;color:#64748b;"><b>${row.date}</b><small style="display:block;">${row.day}</small></td>
                <td style="padding:10px 12px;" colspan="3"><span style="background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:6px;font-size:11.5px;font-weight:700;"><i class="bi bi-moon-stars"></i> Weekend Off</span></td>
            `;
        } else {
            tr.innerHTML = `
                <td style="padding:10px 12px;"><b>${row.date}</b><small style="display:block;color:#4F46E5;font-weight:700;">${row.day}</small></td>
                <td style="padding:10px 12px;"><strong style="color:#0f172a;">${row.subject}</strong></td>
                <td style="padding:10px 12px;color:#475569;font-size:12px;">${row.time}<br><small style="color:#4F46E5;">${row.venue}</small></td>
                <td style="padding:10px 12px;text-align:center;"><strong style="color:#047857;background:#ecfdf5;padding:3px 10px;border-radius:6px;border:1px solid #a7f3d0;">${row.obtained_marks !== null ? row.obtained_marks : 'Pending'} / ${row.max_marks}</strong></td>
            `;
        }
        tbody.appendChild(tr);
    });

    document.getElementById('viewTestSessionModal').style.display = 'grid';
}

function openTestSessionEditModal(data) {
    document.getElementById('eModalStudentId').value = data.student_id;
    document.getElementById('eModalMonthKey').value = data.month_key;
    document.getElementById('eModalSubTitle').innerText = data.name + ' (Roll #' + data.roll_no + ') • ' + data.month_title;

    const container = document.getElementById('eModalMarksInputs');
    container.innerHTML = '';

    const sched = data.schedule || [];
    sched.forEach(row => {
        if (!row.is_off && row.subject) {
            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.justifySpaceBetween = 'space-between';
            div.style.gap = '12px';
            div.style.background = '#f8fafc';
            div.style.padding = '10px 14px';
            div.style.borderRadius = '10px';
            div.style.border = '1px solid #e2e8f0';

            const obt = row.obtained_marks !== null ? row.obtained_marks : '';
            div.innerHTML = `
                <div style="flex:1;">
                    <strong style="color:#0f172a;display:block;font-size:13.5px;">${row.subject}</strong>
                    <small style="color:#64748b;font-size:11px;">Max Marks: ${row.max_marks || 50}</small>
                </div>
                <div style="width:120px;">
                    <input type="number" name="marks[${row.subject}]" value="${obt}" min="0" max="${row.max_marks || 50}" required style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-weight:700;font-size:13.5px;color:#0f172a;">
                </div>
            `;
            container.appendChild(div);
        }
    });

    document.getElementById('editTestMarksModal').style.display = 'grid';
}

function openAddTestSessionModal() {
    document.getElementById('addTestStudentSelect').value = '';
    document.getElementById('addTestMarksInputs').innerHTML = '<div style="text-align:center;padding:20px;color:#64748b;">Please select a student above to enter test marks.</div>';
    document.getElementById('addTestSessionModal').style.display = 'grid';
}

function onAddTestStudentChange() {
    const stdId = document.getElementById('addTestStudentSelect').value;
    const monthKey = document.getElementById('addTestMonthSelect').value;
    const container = document.getElementById('addTestMarksInputs');

    if (!stdId) {
        container.innerHTML = '<div style="text-align:center;padding:20px;color:#64748b;">Please select a student above to enter test marks.</div>';
        return;
    }

    const student = (window.allClassStudentsData || []).find(s => s.id == stdId);
    let subjects = ['English', 'Mathematics', 'Urdu', 'General Science', 'Islamiyat', 'Computer Science', 'Drawing & Arts'];
    if (student && student.subject && typeof student.subject === 'string' && student.subject.trim() !== '') {
        const assigned = student.subject.split(',').map(s => s.trim()).filter(Boolean);
        if (assigned.length > 0) subjects = assigned;
    }

    container.innerHTML = '';
    subjects.forEach(subj => {
        const div = document.createElement('div');
        div.style.display = 'flex';
        div.style.alignItems = 'center';
        div.style.justifyContent = 'space-between';
        div.style.gap = '12px';
        div.style.background = '#f8fafc';
        div.style.padding = '10px 14px';
        div.style.borderRadius = '10px';
        div.style.border = '1px solid #e2e8f0';

        div.innerHTML = `
            <div style="flex:1;">
                <strong style="color:#0f172a;display:block;font-size:13.5px;">${subj}</strong>
                <small style="color:#64748b;font-size:11px;">Max Marks: 50</small>
            </div>
            <div style="width:120px;">
                <input type="number" name="marks[${subj}]" value="" placeholder="Marks" min="0" max="50" required style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-weight:700;font-size:13.5px;color:#0f172a;outline:none;" onfocus="this.style.borderColor='#4F46E5'" onblur="this.style.borderColor='#cbd5e1'">
            </div>
        `;
        container.appendChild(div);
    });
}
</script>

{{-- ADD STUDENT TEST MARKS MODAL (CLASS IN-CHARGE ONLY) --}}
<div id="addTestSessionModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.65);backdrop-filter:blur(5px);z-index:9999;place-items:center;padding:20px;">
    <div style="background:#ffffff;border-radius:20px;width:min(100%,580px);padding:28px;box-shadow:0 25px 60px rgba(0,0,0,0.3);position:relative;margin:auto;">
        <button type="button" onclick="document.getElementById('addTestSessionModal').style.display='none'" style="position:absolute;top:16px;right:16px;border:none;background:#f1f5f9;width:34px;height:34px;border-radius:50%;font-size:20px;cursor:pointer;color:#64748b;display:grid;place-items:center;" title="Close">&times;</button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">
            <div style="width:42px;height:42px;border-radius:12px;background:#4F46E5;color:#ffffff;display:grid;place-items:center;font-size:20px;"><i class="bi bi-plus-lg"></i></div>
            <div>
                <h3 style="margin:0;font-size:18px;color:#0f172a;font-weight:800;">Add Monthly Test Results</h3>
                <span style="font-size:12.5px;color:#4F46E5;font-weight:700;">Class: {{ $primaryClass }} ({{ $primarySection }})</span>
            </div>
        </div>

        <form action="{{ route('teacher.test-marks.update') }}" method="POST">
            @csrf
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;background:#f8fafc;padding:14px;border-radius:12px;border:1px solid #e2e8f0;">
                <div>
                    <label style="font-weight:700;font-size:12px;color:#475569;display:block;margin-bottom:6px;">Select Student:</label>
                    <select name="student_id" id="addTestStudentSelect" onchange="onAddTestStudentChange()" required style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1;font-weight:700;font-size:13px;color:#0f172a;">
                        <option value="">-- Choose Student --</option>
                        @foreach($students as $sItem)
                            <option value="{{ $sItem->id }}">{{ $sItem->name }} (Roll #{{ $sItem->std_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight:700;font-size:12px;color:#475569;display:block;margin-bottom:6px;">Select Month:</label>
                    <select name="month_key" id="addTestMonthSelect" onchange="onAddTestStudentChange()" required style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1;font-weight:700;font-size:13px;color:#0f172a;">
                        @foreach($testMonthsList as $mK => $mL)
                            <option value="{{ $mK }}" {{ ($selectedTestMonth ?? '2026-10') === $mK ? 'selected' : '' }}>{{ $mL }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="max-height:300px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;padding:14px;margin-bottom:20px;display:flex;flex-direction:column;gap:12px;" id="addTestMarksInputs">
                <div style="text-align:center;padding:20px;color:#64748b;">Please select a student above to enter test marks.</div>
            </div>

            <div style="display:flex;gap:10px;justify-content:space-between;align-items:center;">
                <button type="button" onclick="document.getElementById('addTestSessionModal').style.display='none'" style="padding:10px 18px;border-radius:10px;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;font-weight:700;font-size:13px;cursor:pointer;">Cancel</button>
                <button type="submit" style="padding:10px 22px;border-radius:10px;border:none;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#fff;font-weight:800;font-size:13px;cursor:pointer;box-shadow:0 4px 14px rgba(79,70,229,0.25);display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-check-circle-fill"></i> Save Test Marks to Database
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if($page === 'slips')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <p>EXCELLENCE TEACHER PORTAL &bull; SEATING & ROLL SLIPS</p>
        <h1>{{ $primaryClass }} {{ $primarySection }} &bull; Roll Number Slips & Seating Plan</h1>
    </div>
    <div>
        @if($isClassIncharge)
            <a href="{{ route('roll-slips.create') }}" style="background:#4F46E5;color:#fff;border:none;padding:10px 20px;border-radius:12px;font-weight:800;font-size:13.5px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(79,70,229,0.25);">
                <i class="bi bi-plus-circle-fill"></i> Issue Roll Number Slip
            </a>
        @else
            <span style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:800;">
                <i class="bi bi-lock-fill"></i> Subject Teacher (Read-Only)
            </span>
        @endif
    </div>
</div>

{!! $classFilterHtml() !!}

@if(!$isClassIncharge)
    <div style="background:#fffbeb;border:1px solid #fde68a;color:#b45309;padding:16px 20px;border-radius:14px;margin-bottom:24px;display:flex;align-items:center;gap:14px;">
        <i class="bi bi-shield-lock-fill" style="font-size:26px;color:#d97706;"></i>
        <div>
            <strong style="display:block;font-size:14.5px;">🔒 Read-Only Access Mode</strong>
            <span style="font-size:13px;">Generating, editing, or deleting Roll Number Slips is restricted to the Class In-Charge teacher (<strong>{{ $classInchargeTeacherName }}</strong>) for {{ $primaryClass }} ({{ $primarySection }}).</span>
        </div>
    </div>
@else
    <div style="background:linear-gradient(135deg,#0284c7,#0369a1);color:#ffffff;padding:16px 22px;border-radius:16px;margin-bottom:24px;box-shadow:0 10px 25px rgba(2,132,199,0.22);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.18);display:grid;place-items:center;font-size:24px;"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div>
                <span style="font-size:11px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#bae6fd;">👑 Class In-Charge Authorization</span>
                <h3 style="margin:2px 0 0;font-size:17px;font-weight:800;color:#ffffff;">Roll Number Slips Generation & Seating Allocation</h3>
            </div>
        </div>
        <a href="{{ route('roll-slips.create') }}" style="background:#ffffff;color:#0284c7;padding:10px 20px;border-radius:12px;font-weight:800;font-size:13px;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
            <i class="bi bi-plus-circle-fill"></i> Generate Roll Slip
        </a>
    </div>
@endif

<div class="table-panel">
    <table>
        <thead>
            <tr>
                <th>Roll #</th>
                <th>Student Name</th>
                <th>Class & Sec</th>
                <th>Assigned Room / Hall</th>
                <th>Chair / Seat #</th>
                <th>Exam Center</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $std)
            <tr>
                <td><strong style="color:#4F46E5;">#{{ $std->std_id }}</strong></td>
                <td><strong>{{ $std->name }}</strong></td>
                <td>{{ $std->class }} ({{ $std->section }})</td>
                <td><span class="status" style="background:#e0f2fe;color:#0284c7;"><i class="bi bi-door-open-fill"></i> Room-{{ $std->std_id <= 5 ? 'A' : 'B' }}</span></td>
                <td><strong>Seat #{{ $std->std_id }}</strong></td>
                <td><span style="color:#475569;font-weight:600;">Excellence Main Hall</span></td>
                <td style="text-align:center;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                        @if($isClassIncharge)
                            <a href="{{ route('roll-slips.create') }}" style="border:1px solid #cbd5e1;background:#fff;color:#4F46E5;padding:5px 10px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
                                <i class="bi bi-pencil-fill"></i> Edit Slip
                            </a>
                        @endif
                        <a href="{{ route('students.show', $std->id) }}" target="_blank" style="border:none;background:#4F46E5;color:#fff;padding:5px 10px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
                            <i class="bi bi-eye-fill"></i> Slip View
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:24px;color:#64748b;">No students in {{ $primaryClass }} {{ $primarySection }}.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@endsection

