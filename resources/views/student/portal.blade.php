@extends('student-layout')
@section('content')
@php
    $present = $attendance->where('status', 'present')->count();
    $total = $attendance->count();
    $rate = $total ? round($present / $total * 100) : 0;
    $fee = $fees->first();
    $stdRegNo = $student->registration_no ?: ('REG-2026-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')).'-'.str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT));
@endphp

@if(session('success'))
    <div class="student-toast"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

@if($page === 'dashboard')
<section class="student-hero">
    <div>
        <span class="hero-pill">AKM Student Portal <i></i> Session Active</span>
        <h1>Welcome back, {{ $student->name }}!</h1>
        <p>{{ $student->class }} / {{ $student->section }} <i></i> Roll No: #{{ $student->std_id }} <i></i> Reg No: <strong>{{ $stdRegNo }}</strong> <i></i> Keep learning and growing.</p>
    </div>
    <a href="{{ route('student.page', 'slips') }}" class="hero-action"><i class="bi bi-download"></i> Roll No Slip</a>
    <div class="hero-tabs">
        @foreach(['dashboard'=>'Dashboard', 'profile'=>'Profile', 'academics'=>'Class & Subjects', 'outlines'=>'Course Outlines', 'results'=>'Academic Results', 'attendance'=>'Attendance', 'leaves'=>'Leaves', 'tests'=>'Test Sessions', 'fees'=>'Fees & Dues', 'slips'=>'Roll No Slips'] as $key => $label)
            <a href="{{ $key === 'dashboard' ? route('student.dashboard') : route('student.page', $key) }}" class="{{ $key === $page ? 'selected' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
</section>

<section class="metric-grid">
    <a href="{{ route('student.page', 'academics') }}" class="metric-card-link">
        <article>
            <span class="metric-icon blue"><i class="bi bi-book"></i></span>
            <small>ENROLLED SUBJECTS</small>
            <strong>{{ $subjectNames->count() }}</strong>
            <p>Active subjects assigned</p>
        </article>
    </a>
    <a href="{{ route('student.page', 'attendance') }}" class="metric-card-link">
        <article>
            <span class="metric-icon green"><i class="bi bi-person-check"></i></span>
            <small>ATTENDANCE RATE</small>
            <strong>{{ $rate }}%</strong>
            <p>{{ $present }} present records</p>
        </article>
    </a>
    <a href="{{ route('student.page', 'fees') }}" class="metric-card-link">
        <article class="fee-card">
            <span class="metric-icon amber"><i class="bi bi-receipt"></i></span>
            <small>FEE STATUS</small>
            <strong>{{ $fee?->status ?? 'No record' }}</strong>
            <p>{{ $fee ? 'Rs '.number_format($fee->net_amount) : 'No dues available' }}</p>
        </article>
    </a>
    @php
        $termCount = $results->pluck('term')->filter()->map(fn($t) => trim((string)$t))->unique()->count();
    @endphp
    <a href="{{ route('student.page', 'results') }}" class="metric-card-link">
        <article>
            <span class="metric-icon purple"><i class="bi bi-award"></i></span>
            <small>TERM RESULTS</small>
            <strong>{{ $termCount }} {{ $termCount === 1 ? 'Term' : 'Terms' }}</strong>
            <p>Evaluated term records</p>
        </article>
    </a>
    @php
        $octTest = isset($testSessions['2026-10']) ? $testSessions['2026-10'] : null;
        $octPct = is_object($octTest) ? $octTest->percentage : (is_array($octTest) ? ($octTest['percentage'] ?? 86.8) : 86.8);
        $octGrade = is_object($octTest) ? $octTest->grade : (is_array($octTest) ? ($octTest['grade'] ?? 'A+') : 'A+');
        $octObt = is_object($octTest) ? $octTest->total_obtained_marks : (is_array($octTest) ? ($octTest['total_obtained_marks'] ?? 217) : 217);
    @endphp
    <a href="{{ route('student.tests') }}" class="metric-card-link">
        <article>
            <span class="metric-icon teal" style="background:#e0f2fe;color:#0284c7;"><i class="bi bi-journal-check"></i></span>
            <small>TEST SESSION SCORE</small>
            <strong style="color:#0284c7;">{{ $octPct }}% ({{ $octGrade }})</strong>
            <p>{{ $octObt }}/250 Marks &bull; Oct 2026</p>
        </article>
    </a>
</section>

<section class="student-dashboard-grid">
    <article class="student-panel subjects-panel">
        <div class="panel-head">
            <div>
                <h2>Enrolled Subjects & Teachers</h2>
                <p>Academic subjects & teachers for {{ $student->class }} / {{ $student->section }}</p>
            </div>
            <a href="{{ route('student.page', 'academics') }}">View details</a>
        </div>
        @forelse($subjectNames as $index => $name)
            @php
                $mark = $results->firstWhere('subject', $name);
                $percent = $mark ? round($mark->percentage) : 0;
                $tones = ['pink', 'blue', 'orange', 'teal', 'purple'];
                $tone = $tones[$index % 5];
                $teacherInfo = $teachers->get(strtolower(trim($name)));
                $teacherName = $teacherInfo?->teacher?->teacher_name ?? 'Assigned Teacher';
            @endphp
            <div class="subject-row">
                <span class="subject-icon {{ $tone }}">{{ strtoupper(substr($name, 0, 1)) }}</span>
                <div>
                    <b>{{ $name }}</b>
                    <small style="display:block;color:#71827c;font-size:11px;"><i class="bi bi-person-badge"></i> Teacher: {{ $teacherName }}</small>
                    <div class="progress"><i style="width:{{ $percent }}%"></i></div>
                </div>
                <strong>{{ $mark ? $percent.'%' : 'Assigned' }}</strong>
            </div>
        @empty
            <div class="empty-state"><i class="bi bi-book"></i><p>No subjects have been assigned yet.</p></div>
        @endforelse
    </article>

    <aside class="student-right">
        <article class="id-card">
            <small>OFFICIAL STUDENT ID</small>
            <b class="id-roll">#{{ str_pad((string)$student->std_id, 2, '0', STR_PAD_LEFT) }}</b>
            <h2>{{ $student->name }}</h2>
            <p>Class: {{ $student->class }} ({{ $student->section }})</p>
            <hr>
            <div>
                <span><small>REGISTRATION NO</small><b>{{ $stdRegNo }}</b></span>
                <span><small>STATUS</small><b class="verified">Verified Student</b></span>
            </div>
        </article>
        <article class="mini-panel">
            <h3><i class="bi bi-calendar2-check"></i> Attendance overview</h3>
            <div class="attendance-line"><b>{{ $rate }}%</b><span>Current attendance rate</span></div>
            <div class="week-bars">
                @for($n = 0; $n < 7; $n++)
                    <i class="{{ $n < min(6, round($rate / 16)) ? 'on' : '' }}"></i>
                @endfor
            </div>
            <a href="{{ route('student.page', 'attendance') }}">Open full attendance</a>
        </article>
        <article class="mini-panel" style="margin-top:16px;">
            <h3 style="display:flex;align-items:center;gap:8px;"><i class="bi bi-journal-check" style="color:#4F46E5;"></i> Monthly Test Session</h3>
            <div style="display:flex;justify-content:space-between;align-items:center;margin:12px 0 6px;">
                <b style="font-size:15px;color:#0f172a;">Oct 2026 Score</b>
                <span style="background:#ecfdf5;color:#047857;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:800;border:1px solid #a7f3d0;">{{ $octPct }}% ({{ $octGrade }})</span>
            </div>
            <p style="font-size:12px;color:#64748b;margin:0 0 12px;line-height:1.4;">Total Marks: <b style="color:#0f172a;">{{ $octObt }} / 250</b> &bull; 1-Week Series</p>
            <a href="{{ route('student.tests') }}" style="font-weight:700;color:#4F46E5;font-size:12.5px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">Open Test Sessions & Results &rarr;</a>
        </article>
    </aside>
</section>

@elseif($page === 'attendance')
<section class="page-heading">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <div>
            <p>ATTENDANCE RECORD & EXCEL SHEET</p>
            <h1 style="margin:0;">Student Attendance Overview</h1>
        </div>
        
        {{-- TOP NAVIGATION TABS --}}
        <div style="display:inline-flex;background:#e2e8f0;padding:4px;border-radius:12px;gap:4px;">
            <button type="button" id="attTabMonthly" class="att-sheet-tab active" onclick="switchAttTab('monthly')" style="border:none;background:#4F46E5;color:#ffffff;padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Monthly Grid Sheet
            </button>
            <button type="button" id="attTabYearly" class="att-sheet-tab" onclick="switchAttTab('yearly')" style="border:none;background:transparent;color:#475569;padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-calendar-range-fill"></i> Yearly Sheet (2026)
            </button>
            <button type="button" id="attTabDaily" class="att-sheet-tab" onclick="switchAttTab('daily')" style="border:none;background:transparent;color:#475569;padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-list-check"></i> Daily Log Records
            </button>
        </div>
    </div>
</section>

@php
    $monthsConfig = [
        '2026-01' => 'January 2026',
        '2026-02' => 'February 2026',
        '2026-03' => 'March 2026',
        '2026-04' => 'April 2026',
        '2026-05' => 'May 2026',
        '2026-06' => 'June 2026',
        '2026-07' => 'July 2026',
        '2026-08' => 'August 2026',
        '2026-09' => 'September 2026',
        '2026-10' => 'October 2026',
        '2026-11' => 'November 2026',
        '2026-12' => 'December 2026',
    ];

    $processedAttendance = $attendance->map(function($item) {
        $st = strtoupper(trim($item->status));
        $isWeekend = $item->date->isSaturday() || $item->date->isSunday();

        if ($st === 'CL' || str_contains($st, 'CASUAL')) {
            $item->code = 'CL';
            $item->label = 'Casual Leave';
            $item->bg = '#eff6ff';
            $item->color = '#1d4ed8';
            $item->border = '#bfdbfe';
        } elseif ($st === 'SL' || str_contains($st, 'SICK')) {
            $item->code = 'SL';
            $item->label = 'Sick Leave';
            $item->bg = '#fffbeb';
            $item->color = '#b45309';
            $item->border = '#fde68a';
        } elseif ($st === 'ABSENT' || $st === 'A') {
            $item->code = 'A';
            $item->label = 'Absent';
            $item->bg = '#fef2f2';
            $item->color = '#dc2626';
            $item->border = '#fecdd3';
        } elseif ($isWeekend || $st === 'HOLIDAY' || $st === 'H' || $st === 'W') {
            $item->code = 'W';
            $item->label = 'Weekend / Holiday';
            $item->bg = '#f8fafc';
            $item->color = '#64748b';
            $item->border = '#e2e8f0';
        } else {
            $item->code = 'P';
            $item->label = 'Present';
            $item->bg = '#ecfdf5';
            $item->color = '#047857';
            $item->border = '#a7f3d0';
        }
        return $item;
    });

    $byMonthKey = $processedAttendance->groupBy(fn($x) => $x->date->format('Y-m'));
@endphp

{{-- METRIC SUMMARY CARDS --}}
@php
    $totalP = $processedAttendance->where('code', 'P')->count();
    $totalA = $processedAttendance->where('code', 'A')->count();
    $totalCL = $processedAttendance->where('code', 'CL')->count();
    $totalSL = $processedAttendance->where('code', 'SL')->count();
    $totalW = $processedAttendance->where('code', 'W')->count();
    $totalWork = $totalP + $totalA + $totalCL + $totalSL;
    $ratePct = $totalWork > 0 ? round(($totalP / $totalWork) * 100, 1) : 0;
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:16px;margin-bottom:20px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#ecfdf5;color:#047857;display:grid;place-items:center;font-size:22px;"><i class="bi bi-person-check-fill"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Days Present (P)</span>
            <strong style="display:block;font-size:18px;color:#065f46;font-weight:800;margin-top:2px;">{{ $totalP }}</strong>
        </div>
    </div>

    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#fef2f2;color:#dc2626;display:grid;place-items:center;font-size:22px;"><i class="bi bi-person-x-fill"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#dc2626;text-transform:uppercase;letter-spacing:0.05em;">Days Absent (A)</span>
            <strong style="display:block;font-size:18px;color:#991b1b;font-weight:800;margin-top:2px;">{{ $totalA }}</strong>
        </div>
    </div>

    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#1d4ed8;display:grid;place-items:center;font-size:22px;"><i class="bi bi-person-dash-fill"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.05em;">Casual Leave (CL)</span>
            <strong style="display:block;font-size:18px;color:#1e40af;font-weight:800;margin-top:2px;">{{ $totalCL }}</strong>
        </div>
    </div>

    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#fffbeb;color:#b45309;display:grid;place-items:center;font-size:22px;"><i class="bi bi-hospital-fill"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#b45309;text-transform:uppercase;letter-spacing:0.05em;">Sick Leave (SL)</span>
            <strong style="display:block;font-size:18px;color:#92400e;font-weight:800;margin-top:2px;">{{ $totalSL }}</strong>
        </div>
    </div>

    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#e0f2fe;color:#0284c7;display:grid;place-items:center;font-size:22px;"><i class="bi bi-pie-chart-fill"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#0284c7;text-transform:uppercase;letter-spacing:0.05em;">Attendance Rate</span>
            <strong style="display:block;font-size:18px;color:#0369a1;font-weight:800;margin-top:2px;">{{ $ratePct }}%</strong>
        </div>
    </div>
</div>

{{-- MONTHLY EXCEL GRID SHEET VIEW --}}
<div id="attViewMonthly" class="att-panel-section">
    <article class="student-panel" style="padding:22px;">
        {{-- MONTH NAVIGATION TOOLBAR --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;background:#f8fafc;padding:12px 18px;border-radius:14px;border:1px solid #e2e8f0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <button type="button" onclick="navigateAttMonth(-1)" style="border:1px solid #cbd5e1;background:#ffffff;color:#334155;padding:8px 14px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 4px rgba(0,0,0,0.04);"><i class="bi bi-chevron-left"></i> Previous Month</button>
                
                <select id="attMonthSelect" onchange="changeAttMonthSelect(this.value)" style="padding:9px 16px;border:1px solid #cbd5e1;border-radius:10px;font-weight:800;font-size:14px;color:#0f172a;background:#ffffff;cursor:pointer;">
                    @foreach($monthsConfig as $mKey => $mTitle)
                        <option value="{{ $mKey }}" {{ $mKey === '2026-10' ? 'selected' : '' }}>{{ $mTitle }}</option>
                    @endforeach
                </select>

                <button type="button" onclick="navigateAttMonth(1)" style="border:1px solid #cbd5e1;background:#ffffff;color:#334155;padding:8px 14px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 4px rgba(0,0,0,0.04);">Next Month <i class="bi bi-chevron-right"></i></button>
            </div>

            <div style="display:flex;gap:10px;align-items:center;font-size:11.5px;font-weight:700;">
                <span style="background:#ecfdf5;color:#047857;padding:4px 10px;border-radius:6px;border:1px solid #a7f3d0;">P = Present</span>
                <span style="background:#fef2f2;color:#dc2626;padding:4px 10px;border-radius:6px;border:1px solid #fecdd3;">A = Absent</span>
                <span style="background:#eff6ff;color:#1d4ed8;padding:4px 10px;border-radius:6px;border:1px solid #bfdbfe;">CL = Casual Leave</span>
                <span style="background:#fffbeb;color:#b45309;padding:4px 10px;border-radius:6px;border:1px solid #fde68a;">SL = Sick Leave</span>
                <span style="background:#f8fafc;color:#64748b;padding:4px 10px;border-radius:6px;border:1px solid #e2e8f0;">W = Weekend / Holiday</span>
            </div>
        </div>

        {{-- MONTH PANELS --}}
        @foreach($monthsConfig as $mKey => $mTitle)
            @php
                $mItems = $byMonthKey->get($mKey, collect());
                $mCarbon = \Carbon\Carbon::parse($mKey.'-01');
                $daysInMonth = $mCarbon->daysInMonth;
            @endphp
            <div id="attMonthPanel-{{ $mKey }}" class="att-month-grid-wrapper" style="display: {{ $mKey === '2026-10' ? 'block' : 'none' }};">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <h3 style="margin:0;font-size:16px;color:#0f172a;font-weight:800;"><i class="bi bi-file-earmark-spreadsheet-fill" style="color:#4F46E5;"></i> Excel Sheet Grid &bull; {{ $mTitle }}</h3>
                    <span style="font-size:12px;color:#64748b;font-weight:700;">Total Days: {{ $daysInMonth }}</span>
                </div>

                {{-- SPREADSHEET MATRIX TABLE --}}
                <div style="overflow-x:hidden;border:1px solid #cbd5e1;border-radius:12px;margin-bottom:20px;background:#ffffff;">
                    <table style="width:100%;border-collapse:collapse;font-family:sans-serif;font-size:11px;text-align:center;table-layout:fixed;">
                        <thead>
                            <tr style="background:#f1f5f9;border-bottom:2px solid #cbd5e1;color:#334155;">
                                <th style="width:16%;padding:8px 6px;text-align:left;font-family:sans-serif;font-weight:800;border-right:1px solid #cbd5e1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">STUDENT NAME</th>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dDate = \Carbon\Carbon::parse($mKey.'-'.sprintf('%02d', $d));
                                        $isW = $dDate->isSaturday() || $dDate->isSunday();
                                    @endphp
                                    <th style="padding:4px 0px;border-right:1px solid #e2e8f0;background:{{ $isW ? '#e2e8f0' : '#f1f5f9' }};">
                                        <span style="display:block;font-size:10px;font-weight:800;color:#0f172a;line-height:1.1;">{{ $d }}</span>
                                        <small style="display:block;font-size:8.5px;color:#64748b;font-family:sans-serif;line-height:1.1;">{{ $dDate->format('D')[0] }}</small>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width:16%;padding:8px 6px;text-align:left;font-family:sans-serif;background:#faf5ff;border-right:1px solid #cbd5e1;overflow:hidden;">
                                    <div style="font-size:12.5px;font-weight:800;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $student->name }}">{{ $student->name }}</div>
                                    <small style="display:block;color:#4F46E5;font-size:9.5px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $stdRegNo }}">Reg: {{ $stdRegNo }} &bull; Roll #{{ $student->std_id }}</small>
                                </td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dDateStr = $mKey.'-'.sprintf('%02d', $d);
                                        $dDate = \Carbon\Carbon::parse($dDateStr);
                                        $item = $mItems->firstWhere(fn($x) => $x->date->format('Y-m-d') === $dDateStr);
                                        $code = $item ? $item->code : ($dDate->isWeekend() ? 'W' : 'P');
                                        $bg = $item ? $item->bg : ($dDate->isWeekend() ? '#f8fafc' : '#ecfdf5');
                                        $color = $item ? $item->color : ($dDate->isWeekend() ? '#64748b' : '#047857');
                                    @endphp
                                    <td style="padding:6px 0px;border-right:1px solid #e2e8f0;background:{{ $bg }};text-align:center;">
                                        <span style="display:inline-block;font-weight:800;font-size:10px;color:{{ $color }};line-height:1;">{{ $code }}</span>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- DETAILED SPREADSHEET LOG TABLE FOR THIS MONTH --}}
                <div style="border:1px solid #cbd5e1;border-radius:14px;overflow:hidden;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;text-align:left;">
                                <th style="padding:10px 14px;">DATE & DAY</th>
                                <th style="padding:10px 14px;">STATUS CODE</th>
                                <th style="padding:10px 14px;">ATTENDANCE STATUS</th>
                                <th style="padding:10px 14px;">REMARKS & DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mItems as $mRec)
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:10px 14px;">
                                        <b>{{ $mRec->date->format('d M Y') }}</b>
                                        <small style="display:block;color:#64748b;font-size:11px;">{{ $mRec->date->format('l') }}</small>
                                    </td>
                                    <td style="padding:10px 14px;">
                                        <span style="background:{{ $mRec->bg }};color:{{ $mRec->color }};border:1px solid {{ $mRec->border }};padding:3px 10px;border-radius:6px;font-weight:800;font-family:monospace;font-size:12px;">{{ $mRec->code }}</span>
                                    </td>
                                    <td style="padding:10px 14px;">
                                        <span style="background:{{ $mRec->bg }};color:{{ $mRec->color }};padding:5px 12px;border-radius:99px;font-weight:700;font-size:12.5px;">{{ $mRec->label }}</span>
                                    </td>
                                    <td style="padding:10px 14px;color:#334155;">{{ $mRec->remarks ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center;padding:24px;color:#64748b;">No attendance logs for this month.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </article>
</div>

{{-- YEARLY ATTENDANCE SHEET VIEW (2026) --}}
<div id="attViewYearly" class="att-panel-section" style="display:none;">
    <article class="student-panel" style="padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div>
                <h3 style="margin:0;font-size:18px;color:#0f172a;font-weight:800;"><i class="bi bi-calendar-range-fill" style="color:#4F46E5;"></i> 2026 Academic Year Attendance Sheet</h3>
                <span style="font-size:12.5px;color:#64748b;">Comprehensive 12-Month Excel Summary Matrix</span>
            </div>
            <span style="background:#e0f2fe;color:#0284c7;padding:6px 14px;border-radius:10px;font-weight:800;font-size:13px;">Year: 2026</span>
        </div>

        <div style="border:1px solid #cbd5e1;border-radius:14px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f1f5f9;border-bottom:2px solid #cbd5e1;color:#334155;text-align:left;">
                        <th style="padding:12px 14px;">MONTH (2026)</th>
                        <th style="padding:12px 14px;">WORKING DAYS</th>
                        <th style="padding:12px 14px;">PRESENT (P)</th>
                        <th style="padding:12px 14px;">ABSENT (A)</th>
                        <th style="padding:12px 14px;">CASUAL LEAVE (CL)</th>
                        <th style="padding:12px 14px;">SICK LEAVE (SL)</th>
                        <th style="padding:12px 14px;">HOLIDAYS (W)</th>
                        <th style="padding:12px 14px;">ATTENDANCE %</th>
                        <th style="padding:12px 14px;">STATUS</th>
                        <th style="padding:12px 14px;text-align:center;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthsConfig as $mKey => $mTitle)
                        @php
                            $mRecs = $byMonthKey->get($mKey, collect());
                            $mP = $mRecs->where('code', 'P')->count();
                            $mA = $mRecs->where('code', 'A')->count();
                            $mCL = $mRecs->where('code', 'CL')->count();
                            $mSL = $mRecs->where('code', 'SL')->count();
                            $mW = $mRecs->where('code', 'W')->count();
                            $mWorkDays = $mP + $mA + $mCL + $mSL;
                            $mPct = $mWorkDays > 0 ? round(($mP / $mWorkDays) * 100, 1) : 0;
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 14px;"><b>{{ $mTitle }}</b></td>
                            <td style="padding:12px 14px;color:#334155;font-weight:600;">{{ $mWorkDays }} days</td>
                            <td style="padding:12px 14px;"><span style="color:#047857;font-weight:800;">{{ $mP }}</span></td>
                            <td style="padding:12px 14px;"><span style="color:#dc2626;font-weight:800;">{{ $mA }}</span></td>
                            <td style="padding:12px 14px;"><span style="color:#1d4ed8;font-weight:800;">{{ $mCL }}</span></td>
                            <td style="padding:12px 14px;"><span style="color:#b45309;font-weight:800;">{{ $mSL }}</span></td>
                            <td style="padding:12px 14px;"><span style="color:#64748b;font-weight:600;">{{ $mW }}</span></td>
                            <td style="padding:12px 14px;"><strong style="color:#4F46E5;font-size:14px;">{{ $mPct }}%</strong></td>
                            <td style="padding:12px 14px;">
                                @if($mPct >= 90)
                                    <span style="background:#ecfdf5;color:#047857;padding:4px 10px;border-radius:99px;font-size:11.5px;font-weight:700;"><i class="bi bi-star-fill"></i> Excellent</span>
                                @elseif($mPct >= 75)
                                    <span style="background:#eff6ff;color:#1d4ed8;padding:4px 10px;border-radius:99px;font-size:11.5px;font-weight:700;"><i class="bi bi-hand-thumbs-up-fill"></i> Good</span>
                                @else
                                    <span style="background:#fef2f2;color:#dc2626;padding:4px 10px;border-radius:99px;font-size:11.5px;font-weight:700;"><i class="bi bi-exclamation-triangle-fill"></i> Attention</span>
                                @endif
                            </td>
                            <td style="padding:12px 14px;text-align:center;">
                                <button type="button" onclick="selectAndShowAttMonth('{{ $mKey }}')" style="border:1px solid #cbd5e1;background:#ffffff;color:#4F46E5;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                    <i class="bi bi-eye-fill"></i> View Sheet
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </article>
</div>

{{-- DAILY LOG RECORDS VIEW --}}
<div id="attViewDaily" class="att-panel-section" style="display:none;">
    <article class="student-panel table-panel">
        <table>
            <thead>
                <tr>
                    <th>Date & Day</th>
                    <th>Attendance Status</th>
                    <th>Remarks & Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processedAttendance as $item)
                    <tr>
                        <td>
                            <b>{{ $item->date->format('d M Y') }}</b>
                            <small style="display:block;color:#64748b;font-size:11px;">{{ $item->date->format('l') }}</small>
                        </td>
                        <td>
                            <span class="status" style="background:{{ $item->bg }};color:{{ $item->color }};padding:5px 12px;border-radius:99px;font-weight:700;">{{ $item->label }}</span>
                        </td>
                        <td>{{ $item->remarks ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;padding:30px;color:#71827c;">No attendance records found yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </article>
</div>

<script>
function switchAttTab(type) {
    const btnM = document.getElementById('attTabMonthly');
    const btnY = document.getElementById('attTabYearly');
    const btnD = document.getElementById('attTabDaily');

    const panelM = document.getElementById('attViewMonthly');
    const panelY = document.getElementById('attViewYearly');
    const panelD = document.getElementById('attViewDaily');

    const btns = [btnM, btnY, btnD];
    btns.forEach(b => {
        if(b) { b.style.background = 'transparent'; b.style.color = '#475569'; }
    });

    if (panelM) panelM.style.display = 'none';
    if (panelY) panelY.style.display = 'none';
    if (panelD) panelD.style.display = 'none';

    if (type === 'monthly') {
        if (btnM) { btnM.style.background = '#4F46E5'; btnM.style.color = '#ffffff'; }
        if (panelM) panelM.style.display = 'block';
    } else if (type === 'yearly') {
        if (btnY) { btnY.style.background = '#4F46E5'; btnY.style.color = '#ffffff'; }
        if (panelY) panelY.style.display = 'block';
    } else {
        if (btnD) { btnD.style.background = '#4F46E5'; btnD.style.color = '#ffffff'; }
        if (panelD) panelD.style.display = 'block';
    }
}

function changeAttMonthSelect(mKey) {
    const wrappers = document.querySelectorAll('.att-month-grid-wrapper');
    wrappers.forEach(w => w.style.display = 'none');
    
    const target = document.getElementById('attMonthPanel-' + mKey);
    if (target) target.style.display = 'block';
}

function navigateAttMonth(direction) {
    const sel = document.getElementById('attMonthSelect');
    if (!sel) return;

    let idx = sel.selectedIndex + direction;
    if (idx >= 0 && idx < sel.options.length) {
        sel.selectedIndex = idx;
        changeAttMonthSelect(sel.value);
    }
}

function selectAndShowAttMonth(mKey) {
    switchAttTab('monthly');
    const sel = document.getElementById('attMonthSelect');
    if (sel) {
        sel.value = mKey;
        changeAttMonthSelect(mKey);
    }
}
</script>

@elseif($page === 'results')
<section class="page-heading">
    <div style="display:flex;justify-content:space-between;align-items:center;width:100%;">
        <div>
            <p>ACADEMIC EVALUATION</p>
            <h1>Academic Results (First Term, Mid Term & Final Term)</h1>
        </div>
        <a href="{{ route('students.results.download', $student->id) }}" class="hero-action" style="padding:10px 18px;font-size:13px;text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill"></i> Download Result Card (PDF)</a>
    </div>
</section>

@php
    $termOrder = ['First Term' => 1, 'Mid Term' => 2, 'Final Term' => 3];
    $groupedResults = $results->groupBy(function($item) {
        return $item->term ?: 'General Evaluation';
    })->sortBy(function($items, $key) use ($termOrder) {
        return $termOrder[$key] ?? 99;
    });
@endphp

@forelse($groupedResults as $termName => $termItems)
<article class="student-panel table-panel" style="margin-bottom:24px;">
    <div style="padding:16px 20px;background:#f4f9f6;border-bottom:1px solid #e0ece5;display:flex;align-items:center;gap:10px;">
        <i class="bi bi-award-fill" style="color:#25845d;font-size:18px;"></i>
        <h3 style="margin:0;font-size:16px;color:#1b382f;font-weight:700;">{{ $termName }}</h3>
        <span style="margin-left:auto;background:#e2f3e9;color:#237452;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">{{ $termItems->count() }} Subjects</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Marks Obtained</th>
                <th>Percentage</th>
                <th>Grade / Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($termItems as $item)
            <tr>
                <td><b>{{ $item->subject }}</b></td>
                <td>{{ $item->obtained_marks }} / {{ $item->total_marks }}</td>
                <td><strong>{{ round($item->percentage, 1) }}%</strong></td>
                <td>
                    <span class="status" style="background:{{ $item->percentage >= 50 ? '#e2f3e9' : '#fde8e8' }};color:{{ $item->percentage >= 50 ? '#237452' : '#c0392b' }};padding:4px 10px;border-radius:4px;font-weight:700;">
                        {{ $item->grade ?: ($item->percentage >= 50 ? 'PASS' : 'FAIL') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</article>
@empty
<article class="student-panel table-panel">
    <div style="padding:40px;text-align:center;color:#71827c;">
        <i class="bi bi-clipboard2-x" style="font-size:36px;display:block;margin-bottom:10px;"></i>
        <p>No evaluated result records found for 1st Term, Mid Term, or Final Term.</p>
    </div>
</article>
@endforelse

@elseif($page === 'academics')
<section class="page-heading">
    <p>ACADEMICS & TEACHERS</p>
    <h1>Class & Enrolled Subjects ({{ $student->class }} - {{ $student->section }})</h1>
</section>
<article class="student-panel table-panel">
    <table>
        <thead>
            <tr>
                <th>Subject Name</th>
                <th>Assigned Teacher</th>
                <th>Class & Section</th>
                <th>Latest Performance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjectNames as $name)
                @php
                    $teacherInfo = $teachers->get(strtolower(trim($name)));
                    $teacherName = $teacherInfo?->teacher?->teacher_name ?? 'Assigned Subject Teacher';
                    $latestResult = $results->firstWhere('subject', $name);
                @endphp
                <tr>
                    <td><b>{{ $name }}</b></td>
                    <td><i class="bi bi-person-badge" style="color:#25845d;margin-right:6px;"></i> {{ $teacherName }}</td>
                    <td>{{ $student->class }} ({{ $student->section }})</td>
                    <td>{{ $latestResult ? round($latestResult->percentage).'%' : 'Evaluation Pending' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No enrolled subjects found for your class.</td></tr>
            @endforelse
        </tbody>
    </table>
</article>

@elseif($page === 'slips')
<section class="page-heading">
    <p>EXAMINATION & SEATING PLAN</p>
    <h1>Roll Number Slips & Sitting Arrangement</h1>
</section>

@forelse($slips as $slip)
<div style="display:flex;flex-direction:column;gap:20px;margin-bottom:32px;">

    {{-- SEATING PLAN CARDS --}}
    <article class="student-panel" style="padding:24px;background:#fff;border-radius:18px;border:1px solid #e2e8f0;box-shadow:0 4px 16px rgba(15,23,42,0.04);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            <div>
                <span style="background:#C7D2FE;color:#0f766e;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;"><i class="bi bi-shield-check"></i> OFFICIAL SEATING ALLOCATION</span>
                <h2 style="margin:8px 0 2px;font-size:20px;color:#0f172a;font-weight:800;">{{ $slip->exam?->name ?? 'Annual Examination' }} {{ $slip->exam?->session ? '('.$slip->exam->session.')' : '' }}</h2>
                <p style="margin:0;color:#64748b;font-size:13px;">Class & Section: <strong>{{ $slip->class_name }} {{ $slip->section_name ? '('.$slip->section_name.')' : '' }}</strong></p>
            </div>
            <a href="{{ route('roll-slips.download', $slip->id) }}" class="hero-action" style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;font-size:13px;text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill" style="font-size:16px;"></i> Download Official Roll Slip (PDF)</a>
        </div>

        {{-- 4 SITTING PLAN HIGHLIGHT METRICS --}}
        <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:16px;margin-bottom:20px;">
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;position:relative;">
                <span style="position:absolute;right:14px;top:14px;width:34px;height:34px;border-radius:10px;background:#e0f2fe;color:#0284c7;display:grid;place-items:center;font-size:16px;"><i class="bi bi-person-vcard-fill"></i></span>
                <small style="display:block;font-size:10.5px;font-weight:800;color:#64748b;letter-spacing:0.05em;text-transform:uppercase;">ROLL NO</small>
                <strong style="display:block;font-size:24px;font-weight:800;color:#0f172a;margin-top:6px;">#{{ $slip->roll_no }}</strong>
                <span style="font-size:11px;color:#4F46E5;font-weight:600;">Verified Student ID</span>
            </div>

            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:16px;position:relative;">
                <span style="position:absolute;right:14px;top:14px;width:34px;height:34px;border-radius:10px;background:#dcfce7;color:#16a34a;display:grid;place-items:center;font-size:16px;"><i class="bi bi-door-open-fill"></i></span>
                <small style="display:block;font-size:10.5px;font-weight:800;color:#15803d;letter-spacing:0.05em;text-transform:uppercase;">ROOM / HALL NO</small>
                <strong style="display:block;font-size:24px;font-weight:800;color:#14532d;margin-top:6px;">{{ $slip->room_no ?: 'Room-A' }}</strong>
                <span style="font-size:11px;color:#16a34a;font-weight:600;">Assigned Exam Room</span>
            </div>

            <div style="background:#fefce8;border:1px solid #fef08a;border-radius:14px;padding:16px;position:relative;">
                <span style="position:absolute;right:14px;top:14px;width:34px;height:34px;border-radius:10px;background:#fef9c3;color:#ca8a04;display:grid;place-items:center;font-size:16px;"><i class="bi bi-pc-display"></i></span>
                <small style="display:block;font-size:10.5px;font-weight:800;color:#a16207;letter-spacing:0.05em;text-transform:uppercase;">SEAT / CHAIR NO</small>
                <strong style="display:block;font-size:24px;font-weight:800;color:#713f12;margin-top:6px;">Seat #{{ $slip->chair_no ?: '1' }}</strong>
                <span style="font-size:11px;color:#ca8a04;font-weight:600;">Allocated Chair</span>
            </div>

            <div style="background:#faf5ff;border:1px solid #f3e8ff;border-radius:14px;padding:16px;position:relative;">
                <span style="position:absolute;right:14px;top:14px;width:34px;height:34px;border-radius:10px;background:#f3e8ff;color:#9333ea;display:grid;place-items:center;font-size:16px;"><i class="bi bi-building-check"></i></span>
                <small style="display:block;font-size:10.5px;font-weight:800;color:#7e22ce;letter-spacing:0.05em;text-transform:uppercase;">EXAM CENTER</small>
                <strong style="display:block;font-size:16px;font-weight:800;color:#581c87;margin-top:10px;">AKM Main Hall</strong>
                <span style="font-size:11px;color:#9333ea;font-weight:600;">School Center</span>
            </div>
        </div>

        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:12px;color:#1e40af;font-size:13px;font-weight:600;">
            <i class="bi bi-info-circle-fill" style="font-size:18px;color:#2563eb;"></i>
            <span>Seating Plan Rule: Please arrive at <strong>Room {{ $slip->room_no }}</strong> and sit on <strong>Seat #{{ $slip->chair_no }}</strong> at least 15 minutes before the paper start time.</span>
        </div>
    </article>

    {{-- DATESHEET TABLE --}}
    <article class="student-panel table-panel">
        <div style="padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;gap:10px;">
            <i class="bi bi-calendar-check-fill" style="color:#4F46E5;font-size:18px;"></i>
            <h3 style="margin:0;font-size:15px;color:#0f172a;font-weight:700;">Examination Datesheet Schedule</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:50px;text-align:center;">#</th>
                    <th>Subject Name</th>
                    <th>Paper Date & Day</th>
                    <th>Exam Timing</th>
                    <th>Seating Venue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slip->datesheet ?? [] as $pIdx => $paper)
                    @php
                        $examDate = !empty($paper['exam_date']) ? \Carbon\Carbon::parse($paper['exam_date']) : null;
                    @endphp
                    <tr>
                        <td style="text-align:center;font-weight:800;color:#64748b;">{{ $pIdx + 1 }}</td>
                        <td><b>{{ $paper['subject_name'] ?? 'Subject' }}</b></td>
                        <td>
                            @if($examDate)
                                <b>{{ $examDate->format('d M Y') }}</b>
                                <small style="display:block;color:#64748b;font-size:11px;">{{ $examDate->format('l') }}</small>
                            @else
                                <span style="color:#94a3b8;">Date pending</span>
                            @endif
                        </td>
                        <td><span style="background:#f1f5f9;color:#334155;padding:4px 10px;border-radius:6px;font-weight:700;font-size:12px;"><i class="bi bi-clock"></i> {{ $paper['start_time'] ?? '09:00 AM' }} - {{ $paper['end_time'] ?? '12:00 PM' }}</span></td>
                        <td><span style="color:#4F46E5;font-weight:700;"><i class="bi bi-geo-alt-fill"></i> Room {{ $slip->room_no }} (Seat #{{ $slip->chair_no }})</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:30px;color:#64748b;">No datesheet subjects assigned.</td></tr>
                @endforelse
            </tbody>
        </table>
    </article>
</div>
@empty
<article class="student-panel table-panel">
    <div style="padding:50px;text-align:center;color:#64748b;">
        <i class="bi bi-file-earmark-x-fill" style="font-size:42px;display:block;margin-bottom:12px;color:#94a3b8;"></i>
        <h3 style="margin:0 0 6px;color:#0f172a;font-size:18px;">No Roll Number Slips Issued</h3>
        <p style="margin:0;font-size:13.5px;">Roll slips and seating arrangements will be available here once published by your class teacher or administration.</p>
    </div>
</article>
@endforelse

@elseif($page === 'leaves')
<section class="page-heading" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <p><a href="{{ route('student.dashboard') }}" style="color:#4F46E5;text-decoration:none;font-weight:700;"><i class="bi bi-chevron-left"></i> Leaves Dashboard</a></p>
        <h1 style="margin:0;">My Leaves & Quota</h1>
    </div>
    <button type="button" onclick="openApplyLeaveModal()" style="border:none;background:#1e3a8a;color:#ffffff;padding:10px 22px;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(30,58,138,0.3);transition:all 0.2s;">
        <i class="bi bi-plus-circle-fill"></i> Apply for Leave
    </button>
</section>

@if(session('fine_warning'))
    <div style="background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:14px 18px;border-radius:14px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size:22px;color:#e11d48;"></i>
            <div>
                <strong style="display:block;font-size:14px;">Quota Limit Reached Warning (Rs 250 Fine)</strong>
                <span style="font-size:12.5px;">{{ session('fine_warning') }}</span>
            </div>
        </div>
        <button type="button" onclick="openConfirmFineModal()" style="border:none;background:#e11d48;color:#ffffff;padding:8px 16px;border-radius:8px;font-weight:700;font-size:12.5px;cursor:pointer;white-space:nowrap;">
            Admit & Proceed (OK)
        </button>
    </div>
@endif

{{-- LEAVES BALANCE CARD --}}
<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:20px 24px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 15px rgba(0,0,0,0.03);margin-bottom:24px;">
    <div>
        <span style="font-size:15px;font-weight:800;color:#e11d48;letter-spacing:0.02em;">Leaves Balance (This Month)</span>
        <small style="display:block;color:#64748b;font-size:12px;margin-top:2px;">Monthly free allowance: 3 leaves (4th+ leave incurs Rs 250 fine)</small>
    </div>
    <strong style="font-size:28px;font-weight:900;color:#e11d48;background:#fff1f2;padding:6px 18px;border-radius:12px;">{{ $leavesBalance }}</strong>
</div>

{{-- APPLIED LEAVES HISTORY --}}
<div style="display:flex;flex-direction:column;gap:16px;margin-bottom:24px;">
    @forelse($leavesHistory as $leave)
        @php
            $st = strtolower(trim($leave->status));
        @endphp
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;position:relative;box-shadow:0 3px 10px rgba(0,0,0,0.03);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                <div>
                    <h3 style="margin:0;font-size:16px;color:#0f172a;font-weight:800;">Leave &bull; <span style="font-size:13px;color:#4F46E5;font-weight:700;">{{ $leave->leave_type }}</span></h3>
                    @if($leave->is_fine_applied)
                        <span style="background:#fee2e2;color:#dc2626;font-size:11px;font-weight:700;padding:2px 8px;border-radius:6px;display:inline-block;margin-top:4px;">
                            <i class="bi bi-shield-exclamation"></i> Extra Leave Fine: Rs {{ number_format($leave->fine_amount, 2) }}
                        </span>
                    @endif
                </div>

                <div>
                    @if($st === 'approved')
                        <span style="background:#e0e7ff;color:#4338ca;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                            <i class="bi bi-check-circle-fill"></i> Approved
                        </span>
                    @elseif($st === 'rejected')
                        <span style="background:#fee2e2;color:#b91c1c;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                            <i class="bi bi-x-circle-fill"></i> Rejected
                        </span>
                    @else
                        <span style="background:#fef3c7;color:#b45309;padding:5px 14px;border-radius:99px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                            <i class="bi bi-clock-history"></i> Pending
                        </span>
                    @endif
                </div>
            </div>

            <p style="margin:0 0 14px;color:#475569;font-size:13.5px;line-height:1.5;">
                <strong style="color:#64748b;">Reason:</strong> {{ $leave->reason }}
            </p>

            <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f1f5f9;padding-top:10px;font-size:12px;color:#64748b;">
                <span><i class="bi bi-calendar3" style="color:#4F46E5;"></i> Leave Date: <b>{{ \Carbon\Carbon::parse($leave->leave_date)->format('d M Y') }}</b></span>

                @if($st === 'approved')
                    <small style="color:#64748b;font-weight:600;"><i class="bi bi-person-check-fill" style="color:#047857;"></i> Approved by: {{ $leave->approved_by ?: 'Class Teacher / Admin' }}</small>
                @elseif($st === 'rejected')
                    <small style="color:#dc2626;font-weight:600;"><i class="bi bi-x-circle-fill"></i> Rejected by: {{ $leave->approved_by ?: 'Class Teacher / Admin' }}</small>
                @else
                    <small style="color:#b45309;font-weight:600;"><i class="bi bi-hourglass-split"></i> Awaiting Teacher / Admin Approval</small>
                @endif
            </div>
        </div>
    @empty
        <div style="background:#ffffff;border:1px dashed #cbd5e1;border-radius:16px;padding:40px;text-align:center;color:#64748b;">
            <i class="bi bi-calendar2-x" style="font-size:40px;color:#94a3b8;display:block;margin-bottom:10px;"></i>
            <h3 style="margin:0 0 6px;color:#0f172a;font-size:16px;">No Leave Records</h3>
            <p style="margin:0;font-size:13px;">You have not applied for any leave requests yet.</p>
        </div>
    @endforelse
</div>

{{-- CUSTOM PAGINATION FOR LEAVES HISTORY (SHOWN ONLY IF RECORDS > 10 AND HAS MULTIPLE PAGES) --}}
@if($leavesHistory instanceof \Illuminate\Pagination\LengthAwarePaginator && $leavesHistory->total() > 10 && $leavesHistory->hasPages())
    <div style="margin-top:24px;display:flex;flex-direction:column;align-items:center;gap:10px;margin-bottom:24px;">
        <div style="font-size:12.5px;color:#64748b;font-weight:600;">
            Showing <b>{{ $leavesHistory->firstItem() }}</b> to <b>{{ $leavesHistory->lastItem() }}</b> of <b>{{ $leavesHistory->total() }}</b> leave records
        </div>
        
        <div style="display:inline-flex;align-items:center;gap:6px;background:#ffffff;padding:6px 14px;border:1px solid #cbd5e1;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            @if($leavesHistory->onFirstPage())
                <span style="color:#cbd5e1;padding:6px 12px;font-size:13px;font-weight:700;cursor:not-allowed;"><i class="bi bi-chevron-left"></i> Previous</span>
            @else
                <a href="{{ $leavesHistory->previousPageUrl() }}" style="color:#4F46E5;padding:6px 12px;font-size:13px;font-weight:700;text-decoration:none;"><i class="bi bi-chevron-left"></i> Previous</a>
            @endif

            @foreach($leavesHistory->getUrlRange(1, $leavesHistory->lastPage()) as $pageNumber => $url)
                @if($pageNumber == $leavesHistory->currentPage())
                    <span style="background:#4F46E5;color:#ffffff;width:34px;height:34px;border-radius:8px;display:grid;place-items:center;font-size:13px;font-weight:800;box-shadow:0 2px 6px rgba(79,70,229,0.3);">{{ $pageNumber }}</span>
                @else
                    <a href="{{ $url }}" style="color:#475569;width:34px;height:34px;border-radius:8px;display:grid;place-items:center;font-size:13px;font-weight:700;text-decoration:none;transition:all 0.15s;">{{ $pageNumber }}</a>
                @endif
            @endforeach

            @if($leavesHistory->hasMorePages())
                <a href="{{ $leavesHistory->nextPageUrl() }}" style="color:#4F46E5;padding:6px 12px;font-size:13px;font-weight:700;text-decoration:none;">Next <i class="bi bi-chevron-right"></i></a>
            @else
                <span style="color:#cbd5e1;padding:6px 12px;font-size:13px;font-weight:700;cursor:not-allowed;">Next <i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
@endif

{{-- APPLY LEAVE MODAL --}}
<div id="applyLeaveModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px); place-items: center; padding: 20px;">
    <div style="position: relative; width: min(100%, 460px); background: #ffffff; border-radius: 20px; padding: 28px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); animation: securityRise 0.2s ease both;">
        <button type="button" onclick="closeApplyLeaveModal()" style="position: absolute; top: 16px; right: 16px; border: none; background: #f1f5f9; width: 34px; height: 34px; border-radius: 50%; font-size: 20px; cursor: pointer; color: #64748b; display: grid; place-items: center;" title="Close">&times;</button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <div style="width:42px;height:42px;border-radius:12px;background:#eff6ff;color:#1d4ed8;display:grid;place-items:center;font-size:20px;"><i class="bi bi-pencil-square"></i></div>
            <div>
                <h3 style="margin:0;font-size:18px;color:#0f172a;font-weight:800;">Apply for Leave</h3>
                <span style="font-size:12px;color:#64748b;font-weight:600;">Monthly free quota: 3 leaves</span>
            </div>
        </div>

        <form id="applyLeaveForm" method="POST" action="{{ route('student.leave.apply') }}" style="display:flex;flex-direction:column;gap:16px;">
            @csrf
            <input type="hidden" name="admit_fine" id="admitFineInput" value="0">

            <div>
                <label for="leave_date" style="display:block;font-size:12.5px;font-weight:700;color:#475569;margin-bottom:6px;">Leave Date *</label>
                <input id="leave_date" type="date" name="leave_date" value="{{ date('Y-m-d') }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;color:#0f172a;">
            </div>

            <div>
                <label for="leave_type" style="display:block;font-size:12.5px;font-weight:700;color:#475569;margin-bottom:6px;">Types of Leave *</label>
                <select id="leave_type" name="leave_type" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;color:#0f172a;background:#ffffff;">
                    <option value="">--select--</option>
                    <option value="Casual Leave (CL)">Casual Leave (CL)</option>
                    <option value="Sick Leave (SL)">Sick Leave (SL)</option>
                </select>
            </div>

            <div>
                <label for="reason" style="display:block;font-size:12.5px;font-weight:700;color:#475569;margin-bottom:6px;">Reason of Leave *</label>
                <textarea id="reason" name="reason" rows="4" placeholder="Type your reason here" required style="width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;color:#0f172a;resize:vertical;"></textarea>
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="button" onclick="closeApplyLeaveModal()" style="flex:1;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;padding:12px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;">Cancel / Back</button>
                <button type="button" onclick="handleLeaveSubmit()" style="flex:1.5;border:none;background:#1e3a8a;color:#ffffff;padding:12px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 4px 12px rgba(30,58,138,0.3);">Submit Application</button>
            </div>
        </form>
    </div>
</div>

{{-- FINE CONFIRMATION MODAL FOR 4TH LEAVE --}}
<div id="fineConfirmModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(5px); place-items: center; padding: 20px;">
    <div style="position: relative; width: min(100%, 440px); background: #ffffff; border-radius: 20px; padding: 28px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); animation: securityRise 0.2s ease both; text-align: center;">
        <button type="button" onclick="closeFineConfirmModal()" style="position: absolute; top: 16px; right: 16px; border: none; background: #f1f5f9; width: 34px; height: 34px; border-radius: 50%; font-size: 20px; cursor: pointer; color: #64748b; display: grid; place-items: center;" title="Close">&times;</button>

        <div style="width:54px;height:54px;border-radius:50%;background:#fee2e2;color:#dc2626;display:grid;place-items:center;font-size:26px;margin:0 auto 16px;"><i class="bi bi-exclamation-diamond-fill"></i></div>

        <h3 style="margin:0 0 8px;font-size:18px;color:#0f172a;font-weight:800;">Monthly Quota Exceeded!</h3>
        <p style="margin:0 0 20px;color:#475569;font-size:13.5px;line-height:1.5;">
            You have already used all <strong>3 free leaves</strong> for this month. Applying for an extra leave will charge a fine of <strong style="color:#dc2626;">Rs 250</strong> which will be added directly to your monthly fee voucher.
        </p>

        <div style="display:flex;gap:10px;">
            <button type="button" onclick="closeFineConfirmModal()" style="flex:1;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;padding:12px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;">Cancel / Back</button>
            <button type="button" onclick="confirmAdmitFineAndSubmit()" style="flex:1.5;border:none;background:#e11d48;color:#ffffff;padding:12px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 4px 12px rgba(225,29,72,0.3);">Admit / Proceed (OK)</button>
        </div>
    </div>
</div>

<script>
const userLeavesBalance = {{ $leavesBalance ?? 3 }};

function openApplyLeaveModal() {
    const m = document.getElementById('applyLeaveModal');
    if (m) m.style.display = 'grid';
}

function closeApplyLeaveModal() {
    const m = document.getElementById('applyLeaveModal');
    if (m) m.style.display = 'none';
}

function openConfirmFineModal() {
    const m = document.getElementById('fineConfirmModal');
    if (m) m.style.display = 'grid';
}

function closeFineConfirmModal() {
    const m = document.getElementById('fineConfirmModal');
    if (m) m.style.display = 'none';
}

function handleLeaveSubmit() {
    const typeSelect = document.getElementById('leave_type');
    const reasonText = document.getElementById('reason');
    if (!typeSelect.value) {
        alert('Please select a leave type.');
        return;
    }
    if (!reasonText.value.trim()) {
        alert('Please type the reason for leave.');
        return;
    }

    if (userLeavesBalance <= 0) {
        openConfirmFineModal();
    } else {
        document.getElementById('admitFineInput').value = '0';
        document.getElementById('applyLeaveForm').submit();
    }
}

function confirmAdmitFineAndSubmit() {
    document.getElementById('admitFineInput').value = '1';
    closeFineConfirmModal();
    document.getElementById('applyLeaveForm').submit();
}
</script>

@elseif($page === 'tests')
<section class="page-heading">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <div>
            <p>EXAMINATION & ASSESSMENT</p>
            <h1 style="margin:0;">Monthly Test Sessions & Results</h1>
        </div>

        {{-- MONTH FILTER DROPDOWN --}}
        <div style="display:inline-flex;align-items:center;gap:8px;background:#ffffff;border:1px solid #cbd5e1;padding:6px 14px;border-radius:12px;box-shadow:0 2px 6px rgba(0,0,0,0.03);">
            <i class="bi bi-calendar3" style="color:#4F46E5;font-size:16px;"></i>
            <span style="font-size:12.5px;font-weight:700;color:#64748b;">Select Month:</span>
            <select id="testSessionMonthSelect" onchange="switchTestSessionMonth(this.value)" style="border:none;background:transparent;font-weight:800;font-size:14px;color:#0f172a;cursor:pointer;outline:none;">
                @foreach($testSessions as $mKey => $sData)
                    <option value="{{ $mKey }}" {{ $mKey === '2026-10' ? 'selected' : '' }}>{{ $sData['month_title'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
</section>

{{-- TEST SESSION NOTICE BADGE --}}
<div style="background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;padding:16px 22px;border-radius:16px;margin-bottom:24px;box-shadow:0 10px 25px rgba(79,70,229,0.22);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.18);display:grid;place-items:center;font-size:24px;"><i class="bi bi-journal-check"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#a7f3d0;">Official Test Schedule & Assessment</span>
            <h3 style="margin:2px 0 0;font-size:17px;font-weight:800;color:#ffffff;">1-Week Monthly Test Series (Mon – Fri Exams &bull; Sat & Sun Off)</h3>
        </div>
    </div>
    <div style="background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:12px;backdrop-filter:blur(4px);font-size:12.5px;font-weight:700;">
        <i class="bi bi-info-circle-fill" style="color:#a7f3d0;"></i> Read-only student schedule view
    </div>
</div>

{{-- MONTHLY TEST SESSION PANELS --}}
@foreach($testSessions as $mKey => $sData)
    <div id="testSessionPanel-{{ $mKey }}" class="test-session-month-panel" style="display: {{ $mKey === '2026-10' ? 'block' : 'none' }};">
        
        {{-- ROLL SLIP / EXAM NOTICE CARD --}}
        <article class="student-panel" style="padding:22px;margin-bottom:24px;border:1px solid #cbd5e1;background:#ffffff;box-shadow:0 4px 12px rgba(0,0,0,0.03);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #f1f5f9;padding-bottom:16px;margin-bottom:18px;flex-wrap:wrap;gap:12px;">
                <div>
                    <span style="display:inline-block;background:#e0f2fe;color:#0369a1;padding:4px 12px;border-radius:99px;font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">
                        <i class="bi bi-card-checklist"></i> Test Session Schedule & Slip Info
                    </span>
                    <h2 style="margin:0;font-size:20px;color:#0f172a;font-weight:800;">{{ $sData['title'] }}</h2>
                    <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Exam Duration: <b>{{ $sData['start_date'] }} &ndash; {{ $sData['end_date'] }}</b> (7 Consecutive Days)</p>
                </div>
                
                <div style="text-align:right;">
                    @if($sData['is_completed'])
                        <span style="background:#ecfdf5;color:#047857;padding:6px 16px;border-radius:99px;font-size:12.5px;font-weight:800;border:1px solid #a7f3d0;display:inline-flex;align-items:center;gap:6px;">
                            <i class="bi bi-check-circle-fill"></i> {{ $sData['status_label'] }}
                        </span>
                    @else
                        <span style="background:#fffbeb;color:#b45309;padding:6px 16px;border-radius:99px;font-size:12.5px;font-weight:800;border:1px solid #fde68a;display:inline-flex;align-items:center;gap:6px;">
                            <i class="bi bi-clock-history"></i> {{ $sData['status_label'] }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- STUDENT CANDIDATE DETAILS GRID --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:14px;background:#f8fafc;padding:14px 18px;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:20px;">
                <div>
                    <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Student Name</small>
                    <strong style="font-size:14px;color:#0f172a;">{{ $student->name }}</strong>
                </div>
                <div>
                    <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Registration No</small>
                    <strong style="font-size:14px;color:#4F46E5;">{{ $stdRegNo }}</strong>
                </div>
                <div>
                    <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll Number</small>
                    <strong style="font-size:14px;color:#0f172a;">Roll #{{ $student->std_id }}</strong>
                </div>
                <div>
                    <small style="display:block;font-size:10.5px;color:#64748b;font-weight:700;text-transform:uppercase;">Class & Section</small>
                    <strong style="font-size:14px;color:#0f172a;">{{ $student->class }} ({{ $student->section }})</strong>
                </div>
            </div>

            {{-- DATE SHEET TABLE (1 WEEK: MON-FRI EXAMS, SAT-SUN OFF) --}}
            <div style="border:1px solid #cbd5e1;border-radius:12px;overflow:hidden;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;text-align:left;">
                    <thead>
                        <tr style="background:#f1f5f9;border-bottom:2px solid #cbd5e1;color:#334155;">
                            <th style="padding:12px 16px;">DATE & DAY</th>
                            <th style="padding:12px 16px;">SUBJECT EXAM</th>
                            <th style="padding:12px 16px;">TIMING</th>
                            <th style="padding:12px 16px;">VENUE</th>
                            <th style="padding:12px 16px;text-align:center;">MAX MARKS</th>
                            <th style="padding:12px 16px;text-align:center;">OBTAINED MARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sData['schedule'] as $row)
                            <tr style="border-bottom:1px solid #e2e8f0;background:{{ $row['is_off'] ? '#f8fafc' : '#ffffff' }};">
                                <td style="padding:12px 16px;">
                                    <b style="color:#0f172a;">{{ $row['date'] }}</b>
                                    <small style="display:block;color:{{ $row['is_off'] ? '#64748b' : '#4F46E5' }};font-weight:700;">{{ $row['day'] }}</small>
                                </td>
                                <td style="padding:12px 16px;">
                                    @if($row['is_off'])
                                        <span style="background:#f1f5f9;color:#64748b;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:800;border:1px solid #e2e8f0;display:inline-flex;align-items:center;gap:6px;">
                                            <i class="bi bi-moon-stars-fill"></i> Weekend Off (No Exam)
                                        </span>
                                    @else
                                        <strong style="color:#0f172a;font-size:14px;">{{ $row['subject'] }}</strong>
                                    @endif
                                </td>
                                <td style="padding:12px 16px;color:#475569;font-weight:600;">
                                    {{ $row['time'] }}
                                </td>
                                <td style="padding:12px 16px;color:#475569;font-weight:600;">
                                    {{ $row['venue'] }}
                                </td>
                                <td style="padding:12px 16px;text-align:center;font-weight:800;color:#334155;">
                                    {{ $row['is_off'] ? '—' : $row['max_marks'] }}
                                </td>
                                <td style="padding:12px 16px;text-align:center;">
                                    @if($row['is_off'])
                                        <span style="color:#94a3b8;font-weight:700;">—</span>
                                    @elseif($sData['is_completed'])
                                        <strong style="background:#ecfdf5;color:#047857;padding:4px 12px;border-radius:6px;border:1px solid #a7f3d0;font-size:13px;">
                                            {{ $row['obtained_marks'] }} / {{ $row['max_marks'] }}
                                        </strong>
                                    @else
                                        <span style="color:#b45309;font-size:12px;font-weight:700;background:#fffbeb;padding:4px 10px;border-radius:6px;border:1px solid #fde68a;">
                                            Pending Evaluation
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        {{-- TEST SESSION MARKS & PERFORMANCE SUMMARY --}}
        @if($sData['is_completed'])
            <article class="student-panel" style="padding:22px;border:1px solid #cbd5e1;background:#faf5ff;border-radius:16px;margin-bottom:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
                    <div>
                        <span style="font-size:11px;font-weight:800;color:#7e22ce;text-transform:uppercase;letter-spacing:0.05em;">Published Evaluation Summary</span>
                        <h3 style="margin:2px 0 0;font-size:18px;color:#581c87;font-weight:800;">Monthly Test Result & Performance Card</h3>
                    </div>

                    <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
                        <div style="background:#ffffff;border:1px solid #e9d5ff;padding:10px 18px;border-radius:12px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                            <small style="display:block;font-size:10px;font-weight:800;color:#6b21a8;text-transform:uppercase;">Total Marks</small>
                            <strong style="font-size:16px;color:#581c87;">{{ $sData['total_obtained'] }} / {{ $sData['total_max'] }}</strong>
                        </div>

                        <div style="background:#ffffff;border:1px solid #e9d5ff;padding:10px 18px;border-radius:12px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                            <small style="display:block;font-size:10px;font-weight:800;color:#6b21a8;text-transform:uppercase;">Percentage</small>
                            <strong style="font-size:16px;color:#047857;">{{ $sData['percentage'] }}%</strong>
                        </div>

                        <div style="background:#ffffff;border:1px solid #e9d5ff;padding:10px 18px;border-radius:12px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                            <small style="display:block;font-size:10px;font-weight:800;color:#6b21a8;text-transform:uppercase;">Grade</small>
                            <span style="background:#f3e8ff;color:#7e22ce;padding:2px 10px;border-radius:6px;font-weight:800;font-size:14px;display:inline-block;">{{ $sData['grade'] }}</span>
                        </div>
                    </div>
                </div>
            </article>
        @endif
    </div>
@endforeach

<script>
function switchTestSessionMonth(mKey) {
    const panels = document.querySelectorAll('.test-session-month-panel');
    panels.forEach(p => p.style.display = 'none');

    const target = document.getElementById('testSessionPanel-' + mKey);
    if (target) target.style.display = 'block';
}
</script>

@elseif($page === 'fees')
<section class="page-heading">
    <p>FEE MANAGEMENT & DUES</p>
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <h1 style="margin:0;">Fees & Monthly Vouchers</h1>
        
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            {{-- YEAR & MONTH FILTERS --}}
            <div style="display:inline-flex;align-items:center;gap:6px;background:#ffffff;border:1px solid #cbd5e1;padding:5px 12px;border-radius:10px;box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                <i class="bi bi-calendar-event-fill" style="color:#4F46E5;font-size:14px;"></i>
                <span style="font-size:12px;font-weight:700;color:#64748b;">Year:</span>
                <select id="feeYearFilter" onchange="filterFeeTable()" style="border:none;background:transparent;font-weight:800;font-size:13px;color:#0f172a;cursor:pointer;outline:none;">
                    <option value="ALL">All Years</option>
                    <option value="2026" selected>2026</option>
                    <option value="2025">2025</option>
                </select>
            </div>

            <div style="display:inline-flex;align-items:center;gap:6px;background:#ffffff;border:1px solid #cbd5e1;padding:5px 12px;border-radius:10px;box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                <i class="bi bi-funnel-fill" style="color:#4F46E5;font-size:14px;"></i>
                <span style="font-size:12px;font-weight:700;color:#64748b;">Month:</span>
                <select id="feeMonthFilter" onchange="filterFeeTable()" style="border:none;background:transparent;font-weight:800;font-size:13px;color:#0f172a;cursor:pointer;outline:none;">
                    <option value="ALL" selected>All Months</option>
                    <option value="january">January</option>
                    <option value="february">February</option>
                    <option value="march">March</option>
                    <option value="april">April</option>
                    <option value="may">May</option>
                    <option value="june">June</option>
                    <option value="july">July</option>
                    <option value="august">August</option>
                    <option value="september">September</option>
                    <option value="october">October</option>
                    <option value="november">November</option>
                    <option value="december">December</option>
                </select>
            </div>

            {{-- FILTER TYPE TABS --}}
            <div style="display:inline-flex;background:#e2e8f0;padding:4px;border-radius:12px;gap:4px;">
                <button type="button" id="tabBtnMonthly" class="fee-tab-btn active" onclick="switchFeeTab('monthly')" style="border:none;background:#4F46E5;color:#ffffff;padding:8px 16px;border-radius:9px;font-size:12.5px;font-weight:700;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-calendar2-month-fill"></i> Monthly Vouchers
                </button>
                <button type="button" id="tabBtnYearly" class="fee-tab-btn" onclick="switchFeeTab('yearly')" style="border:none;background:transparent;color:#475569;padding:8px 16px;border-radius:9px;font-size:12.5px;font-weight:700;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-calendar-range-fill"></i> Yearly Overview (2026)
                </button>
            </div>
        </div>
    </div>
</section>

{{-- YEARLY SUMMARY CARDS --}}
@php
    $totalYearlyGross = 0;
    $totalYearlyPaid = 0;
    $totalYearlyPending = 0;
    
    foreach($fees as $fItem) {
        $totalYearlyGross += $fItem->net_amount;
        if(strtolower(trim($fItem->status)) === 'paid') {
            $totalYearlyPaid += $fItem->net_amount;
        } else {
            $totalYearlyPending += $fItem->net_amount;
        }
    }
@endphp

<div id="yearlySummarySection" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:20px;">
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#e0f2fe;color:#0284c7;display:grid;place-items:center;font-size:22px;"><i class="bi bi-cash-stack"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">2026 Yearly Net Fee</span>
            <strong style="display:block;font-size:18px;color:#0f172a;font-weight:800;margin-top:2px;">Rs {{ number_format($totalYearlyGross, 2) }}</strong>
        </div>
    </div>

    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#dcfce7;color:#15803d;display:grid;place-items:center;font-size:22px;"><i class="bi bi-check-circle-fill"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#15803d;text-transform:uppercase;letter-spacing:0.05em;">Total Fee Paid</span>
            <strong style="display:block;font-size:18px;color:#166534;font-weight:800;margin-top:2px;">Rs {{ number_format($totalYearlyPaid, 2) }}</strong>
        </div>
    </div>

    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;box-shadow:0 2px 6px rgba(0,0,0,0.03);display:flex;align-items:center;gap:14px;">
        <div style="width:44px;height:44px;border-radius:12px;background:#fef3c7;color:#b45309;display:grid;place-items:center;font-size:22px;"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div>
            <span style="font-size:11px;font-weight:800;color:#b45309;text-transform:uppercase;letter-spacing:0.05em;">Pending Net Dues</span>
            <strong style="display:block;font-size:18px;color:#92400e;font-weight:800;margin-top:2px;">Rs {{ number_format($totalYearlyPending, 2) }}</strong>
        </div>
    </div>
</div>

{{-- FEE VOUCHERS TABLE --}}
<article class="student-panel table-panel">
    {{-- ICON & ACTION GUIDE BAR --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:12px 20px;">
        <div style="display:flex;align-items:center;gap:14px;font-size:12px;font-weight:700;color:#475569;flex-wrap:wrap;">
            <span style="color:#0f172a;"><i class="bi bi-info-circle-fill" style="color:#4F46E5;font-size:14px;"></i> <b>Icon Guide:</b></span>
            <span style="display:inline-flex;align-items:center;gap:5px;background:#ffffff;padding:4px 10px;border-radius:6px;border:1px solid #cbd5e1;"><i class="bi bi-eye-fill" style="color:#4F46E5;"></i> View Details</span>
            <span style="display:inline-flex;align-items:center;gap:5px;background:#ffffff;padding:4px 10px;border-radius:6px;border:1px solid #cbd5e1;"><i class="bi bi-credit-card-fill" style="color:#4F46E5;"></i> Pay Online</span>
            <span style="display:inline-flex;align-items:center;gap:5px;background:#ffffff;padding:4px 10px;border-radius:6px;border:1px solid #cbd5e1;"><i class="bi bi-download" style="color:#4F46E5;"></i> Download PDF</span>
            <span style="display:inline-flex;align-items:center;gap:5px;background:#ecfdf5;padding:4px 10px;border-radius:6px;border:1px solid #a7f3d0;color:#047857;"><i class="bi bi-check-circle-fill" style="color:#059669;"></i> Cleared (Paid Status)</span>
        </div>
        <span style="font-size:12px;color:#64748b;font-weight:700;"><i class="bi bi-calendar-check" style="color:#4F46E5;"></i> Due Date: 10th of every month</span>
    </div>

    <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
        <thead>
            <tr style="background:#f8fafc;border-bottom:2px solid #cbd5e1;color:#475569;text-align:left;">
                <th style="padding:12px 14px;white-space:nowrap;font-weight:700;">MONTH</th>
                <th style="padding:12px 14px;white-space:nowrap;font-weight:700;">VOUCHER NO</th>
                <th style="padding:12px 14px;white-space:nowrap;font-weight:700;">NET FEE</th>
                <th style="padding:12px 14px;white-space:nowrap;font-weight:700;">DUE DATE</th>
                <th style="padding:12px 14px;white-space:nowrap;font-weight:700;">TOTAL PAYABLE</th>
                <th style="padding:12px 14px;white-space:nowrap;font-weight:700;">STATUS</th>
                <th style="padding:12px 14px;white-space:nowrap;text-align:center;font-weight:700;">ACTION</th>
            </tr>
        </thead>
        <tbody id="feeTableBody">
            @forelse($fees as $item)
                @php
                    $isPaid = strtolower(trim($item->status)) === 'paid';
                    $dueDateFormatted = $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d M Y') : '10 Jan 2026';
                    $mName = strtolower(trim($item->month));
                    $yNum = $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('Y') : '2026';
                @endphp
                <tr class="fee-voucher-row" data-month="{{ $mName }}" data-year="{{ $yNum }}" style="border-bottom:1px solid #f1f5f9;vertical-align:middle;">
                    <td style="padding:12px 14px;white-space:nowrap;"><b>{{ $item->month }}</b></td>
                    <td style="padding:12px 14px;white-space:nowrap;"><span style="font-family:monospace;font-weight:700;color:#4F46E5;">{{ $item->voucher_no }}</span></td>
                    <td style="padding:12px 14px;white-space:nowrap;"><span style="color:#334155;font-weight:600;">Rs {{ number_format($item->net_amount, 2) }}</span></td>
                    <td style="padding:12px 14px;white-space:nowrap;">
                        <span style="font-weight:700;color:#0f172a;white-space:nowrap;display:inline-flex;align-items:center;gap:4px;"><i class="bi bi-calendar-event" style="color:#4F46E5;"></i> {{ $dueDateFormatted }}</span>
                    </td>
                    <td style="padding:12px 14px;white-space:nowrap;"><strong style="color:#0f172a;font-size:13.5px;font-weight:800;">Rs {{ number_format($item->total_payable, 2) }}</strong></td>
                    <td style="padding:12px 14px;white-space:nowrap;">
                        @if($isPaid)
                            <span class="status" style="background:#ecfdf5;color:#047857;padding:4px 10px;border-radius:99px;font-weight:700;font-size:11px;"><i class="bi bi-check-circle-fill"></i> Paid</span>
                        @else
                            <span class="status" style="background:#fffbeb;color:#b45309;padding:4px 10px;border-radius:99px;font-weight:700;font-size:11px;"><i class="bi bi-clock-history"></i> Pending</span>
                        @endif
                    </td>
                    <td style="padding:12px 14px;white-space:nowrap;text-align:center;">
                        <div style="display:inline-flex;gap:6px;align-items:center;justify-content:center;">
                            {{-- VIEW ICON BUTTON --}}
                            <button type="button" class="btn-view-fee" 
                                data-id="{{ $item->id }}" 
                                data-voucher="{{ $item->voucher_no }}" 
                                data-month="{{ $item->month }}" 
                                data-net="{{ number_format($item->net_amount, 2) }}" 
                                data-tuition="{{ number_format($item->tuition_fee, 2) }}" 
                                data-exam="{{ number_format($item->exam_fee, 2) }}" 
                                data-other="{{ number_format($item->other_charges, 2) }}" 
                                data-fine="{{ number_format($item->late_fine, 2) }}"
                                data-overduedays="{{ $item->overdue_days }}"
                                data-total="{{ number_format($item->total_payable, 2) }}"
                                data-status="{{ $item->status }}" 
                                data-duedate="{{ $dueDateFormatted }}" 
                                data-paydate="{{ $item->payment_date ? \Carbon\Carbon::parse($item->payment_date)->format('d M Y') : '-' }}" 
                                data-paymethod="{{ $item->payment_method ?: '-' }}" 
                                title="View Voucher Details"
                                style="border:1px solid #cbd5e1;background:#ffffff;color:#4F46E5;width:32px;height:32px;border-radius:8px;font-size:14px;cursor:pointer;display:inline-grid;place-items:center;transition:all 0.15s;box-shadow:0 2px 4px rgba(0,0,0,0.04);">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            
                            {{-- PAY / CLEARED STATUS --}}
                            @if(!$isPaid)
                                <button type="button" class="btn-pay-fee" 
                                    data-id="{{ $item->id }}" 
                                    data-voucher="{{ $item->voucher_no }}" 
                                    data-month="{{ $item->month }}" 
                                    data-net="{{ number_format($item->net_amount, 2) }}" 
                                    data-fine="{{ number_format($item->late_fine, 2) }}"
                                    data-total="{{ number_format($item->total_payable, 2) }}"
                                    data-action="{{ route('student.fee.pay', $item->id) }}" 
                                    title="Pay Fee Online"
                                    style="border:none;background:#4F46E5;color:#ffffff;width:32px;height:32px;border-radius:8px;font-size:14px;cursor:pointer;display:inline-grid;place-items:center;box-shadow:0 2px 6px rgba(79,70,229,0.25);transition:all 0.15s;">
                                    <i class="bi bi-credit-card-fill"></i>
                                </button>
                            @else
                                <span title="Fee Cleared & Verified" style="display:inline-grid;place-items:center;width:32px;height:32px;border-radius:8px;background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;font-size:15px;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </span>
                            @endif

                            {{-- DOWNLOAD ICON BUTTON --}}
                            <a href="{{ route('student.fee.download', $item->id) }}" target="_blank" 
                                title="Download Fee Voucher PDF"
                                style="border:1px solid #4F46E5;background:#e6f4f2;color:#4F46E5;width:32px;height:32px;border-radius:8px;font-size:14px;text-decoration:none;display:inline-grid;place-items:center;transition:all 0.15s;box-shadow:0 2px 4px rgba(79,70,229,0.1);">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;color:#64748b;">No fee records are available.</td></tr>
            @endforelse
        </tbody>
    </table>
</article>

{{-- CUSTOM MODERN PAGINATION CONTAINER (SHOWN ONLY IF RECORDS > 10 AND HAS MULTIPLE PAGES) --}}
@if($fees instanceof \Illuminate\Pagination\LengthAwarePaginator && $fees->total() > 10 && $fees->hasPages())
    <div style="margin-top:24px;display:flex;flex-direction:column;align-items:center;gap:10px;">
        <div style="font-size:12.5px;color:#64748b;font-weight:600;">
            Showing <b>{{ $fees->firstItem() }}</b> to <b>{{ $fees->lastItem() }}</b> of <b>{{ $fees->total() }}</b> fee records
        </div>
        
        <div style="display:inline-flex;align-items:center;gap:6px;background:#ffffff;padding:6px 14px;border:1px solid #cbd5e1;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            @if($fees->onFirstPage())
                <span style="color:#cbd5e1;padding:6px 12px;font-size:13px;font-weight:700;cursor:not-allowed;"><i class="bi bi-chevron-left"></i> Previous</span>
            @else
                <a href="{{ $fees->previousPageUrl() }}" style="color:#4F46E5;padding:6px 12px;font-size:13px;font-weight:700;text-decoration:none;"><i class="bi bi-chevron-left"></i> Previous</a>
            @endif

            @foreach($fees->getUrlRange(1, $fees->lastPage()) as $pageNumber => $url)
                @if($pageNumber == $fees->currentPage())
                    <span style="background:#4F46E5;color:#ffffff;width:34px;height:34px;border-radius:8px;display:grid;place-items:center;font-size:13px;font-weight:800;box-shadow:0 2px 6px rgba(79,70,229,0.3);">{{ $pageNumber }}</span>
                @else
                    <a href="{{ $url }}" style="color:#475569;width:34px;height:34px;border-radius:8px;display:grid;place-items:center;font-size:13px;font-weight:700;text-decoration:none;transition:all 0.15s;">{{ $pageNumber }}</a>
                @endif
            @endforeach

            @if($fees->hasMorePages())
                <a href="{{ $fees->nextPageUrl() }}" style="color:#4F46E5;padding:6px 12px;font-size:13px;font-weight:700;text-decoration:none;">Next <i class="bi bi-chevron-right"></i></a>
            @else
                <span style="color:#cbd5e1;padding:6px 12px;font-size:13px;font-weight:700;cursor:not-allowed;">Next <i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
@endif

{{-- VIEW VOUCHER MODAL --}}
<div id="feeViewModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px); place-items: center; padding: 20px;">
    <div style="position: relative; width: min(100%, 500px); background: #ffffff; border-radius: 20px; padding: 28px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3); animation: securityRise 0.2s ease both;">
        <button type="button" class="close-fee-view" style="position: absolute; top: 16px; right: 16px; border: none; background: #f1f5f9; width: 34px; height: 34px; border-radius: 50%; font-size: 20px; cursor: pointer; color: #64748b; display: grid; place-items: center;" title="Close">&times;</button>
        
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <div style="width:42px;height:42px;border-radius:12px;background:#e0f2fe;color:#0284c7;display:grid;place-items:center;font-size:20px;"><i class="bi bi-receipt"></i></div>
            <div>
                <h3 style="margin:0;font-size:18px;color:#0f172a;font-weight:800;">Fee Voucher Details</h3>
                <span id="vModalVoucherNo" style="font-size:12px;color:#4F46E5;font-weight:700;font-family:monospace;">-</span>
            </div>
        </div>

        <dl style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:0 0 18px;">
            <div style="background:#f8fafc;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:11px;font-weight:700;">STUDENT NAME</dt>
                <dd style="margin:4px 0 0;font-weight:700;color:#0f172a;font-size:13px;">{{ $student->name }}</dd>
            </div>
            <div style="background:#f8fafc;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:11px;font-weight:700;">ROLL NO & CLASS</dt>
                <dd style="margin:4px 0 0;font-weight:700;color:#0f172a;font-size:13px;">#{{ $student->std_id }} ({{ $student->class }})</dd>
            </div>
            <div style="background:#f8fafc;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:11px;font-weight:700;">MONTH & DUE DATE</dt>
                <dd id="vModalMonth" style="margin:4px 0 0;font-weight:700;color:#0f172a;font-size:13px;">-</dd>
            </div>
            <div style="background:#f8fafc;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:11px;font-weight:700;">PAYMENT STATUS</dt>
                <dd id="vModalStatus" style="margin:4px 0 0;font-weight:800;font-size:13px;">-</dd>
            </div>
        </dl>

        <h4 style="margin:16px 0 10px;font-size:13px;color:#475569;text-transform:uppercase;letter-spacing:0.05em;">Fee Charges Breakdown</h4>
        <div style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:20px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 14px;color:#64748b;">Tuition Fee</td><td id="vModalTuition" style="padding:10px 14px;text-align:right;font-weight:700;color:#0f172a;">Rs 0.00</td></tr>
                <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 14px;color:#64748b;">Examination Fee</td><td id="vModalExam" style="padding:10px 14px;text-align:right;font-weight:700;color:#0f172a;">Rs 0.00</td></tr>
                <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px 14px;color:#64748b;">Other Dues / Services</td><td id="vModalOther" style="padding:10px 14px;text-align:right;font-weight:700;color:#0f172a;">Rs 0.00</td></tr>
                <tr style="background:#f8fafc;"><td style="padding:12px 14px;font-weight:800;color:#0f172a;">Total Payable Amount</td><td id="vModalTotal" style="padding:12px 14px;text-align:right;font-weight:800;color:#4F46E5;font-size:16px;">Rs 0.00</td></tr>
            </table>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="button" class="close-fee-view" style="border:1px solid #cbd5e1;background:#f8fafc;color:#334155;padding:10px 20px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;">Close Window</button>
        </div>
    </div>
</div>

{{-- PAY FEE MODAL --}}
<div id="feePayModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px); place-items: center; padding: 20px;">
    <div style="position: relative; width: min(100%, 480px); background: #ffffff; border-radius: 20px; padding: 28px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3); animation: securityRise 0.2s ease both;">
        <button type="button" class="close-fee-pay" style="position: absolute; top: 16px; right: 16px; border: none; background: #f1f5f9; width: 34px; height: 34px; border-radius: 50%; font-size: 20px; cursor: pointer; color: #64748b; display: grid; place-items: center;" title="Close">&times;</button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <div style="width:42px;height:42px;border-radius:12px;background:#ecfdf5;color:#047857;display:grid;place-items:center;font-size:20px;"><i class="bi bi-credit-card-fill"></i></div>
            <div>
                <h3 style="margin:0;font-size:18px;color:#0f172a;font-weight:800;">Online Fee Payment</h3>
                <span id="pModalVoucherNo" style="font-size:12px;color:#4F46E5;font-weight:700;font-family:monospace;">Voucher</span>
            </div>
        </div>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 16px;margin-bottom:20px;display:flex;flex-direction:column;gap:6px;">
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:#334155;">
                <span>Base Net Fee:</span>
                <strong id="pModalNet" style="color:#0f172a;">Rs 0.00</strong>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:#dc2626;">
                <span>Late Fine (Rs 500/day):</span>
                <strong id="pModalFine">+ Rs 0.00</strong>
            </div>
            <div style="border-top:1px dashed #bbf7d0;padding-top:6px;margin-top:2px;display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#166534;font-size:13px;font-weight:800;">Total Amount Payable:</span>
                <strong id="pModalTotal" style="color:#15803d;font-size:20px;font-weight:800;">Rs 0.00</strong>
            </div>
        </div>

        <form id="feePayForm" method="POST" action="" style="display:flex;flex-direction:column;gap:14px;">
            @csrf
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:8px;">Select Payment Gateway</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <label style="border:1px solid #cbd5e1;border-radius:10px;padding:10px;display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;font-weight:700;color:#0f172a;">
                        <input type="radio" name="payment_method" value="JazzCash" checked>
                        <i class="bi bi-wallet2" style="color:#e11d48;font-size:16px;"></i> JazzCash
                    </label>
                    <label style="border:1px solid #cbd5e1;border-radius:10px;padding:10px;display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;font-weight:700;color:#0f172a;">
                        <input type="radio" name="payment_method" value="EasyPaisa">
                        <i class="bi bi-phone" style="color:#16a34a;font-size:16px;"></i> EasyPaisa
                    </label>
                    <label style="border:1px solid #cbd5e1;border-radius:10px;padding:10px;display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;font-weight:700;color:#0f172a;">
                        <input type="radio" name="payment_method" value="Credit/Debit Card">
                        <i class="bi bi-credit-card" style="color:#2563eb;font-size:16px;"></i> Debit Card
                    </label>
                    <label style="border:1px solid #cbd5e1;border-radius:10px;padding:10px;display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;font-weight:700;color:#0f172a;">
                        <input type="radio" name="payment_method" value="Bank Transfer">
                        <i class="bi bi-bank" style="color:#9333ea;font-size:16px;"></i> Bank Transfer
                    </label>
                </div>
            </div>

            <div>
                <label for="account_title" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:4px;">Account Title / Payer Name</label>
                <input id="account_title" type="text" name="account_title" value="{{ $student->name }}" required style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;">
            </div>

            <div>
                <label for="account_no" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:4px;">Mobile / Card / Account Number</label>
                <input id="account_no" type="text" name="account_no" placeholder="03XX-XXXXXXX" required style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;">
            </div>

            <div>
                <label for="transaction_id" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:4px;">Transaction Ref / TID (Optional)</label>
                <input id="transaction_id" type="text" name="transaction_id" placeholder="e.g. TXN98765432" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;">
            </div>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <button type="button" class="close-fee-pay" style="flex:1;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;padding:12px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;">Cancel / Back</button>
                <button type="submit" style="flex:1.5;border:none;background:#4F46E5;color:#ffffff;padding:12px;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 4px 12px rgba(79,70,229,0.3);"><i class="bi bi-check-circle-fill"></i> Pay Dues Now</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterFeeTable() {
    const monthVal = document.getElementById('feeMonthFilter')?.value.toLowerCase() || 'all';
    const yearVal = document.getElementById('feeYearFilter')?.value.toLowerCase() || 'all';
    
    const rows = document.querySelectorAll('.fee-voucher-row');
    rows.forEach(r => {
        const m = (r.getAttribute('data-month') || '').toLowerCase();
        const y = (r.getAttribute('data-year') || '').toLowerCase();

        const matchMonth = (monthVal === 'all' || m.includes(monthVal));
        const matchYear = (yearVal === 'all' || y.includes(yearVal));

        if (matchMonth && matchYear) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

function switchFeeTab(type) {
    const btnM = document.getElementById('tabBtnMonthly');
    const btnY = document.getElementById('tabBtnYearly');
    const summarySec = document.getElementById('yearlySummarySection');

    if (type === 'monthly') {
        if(btnM) { btnM.style.background = '#4F46E5'; btnM.style.color = '#ffffff'; }
        if(btnY) { btnY.style.background = 'transparent'; btnY.style.color = '#475569'; }
        
        // reset filters to show all
        const selM = document.getElementById('feeMonthFilter');
        if (selM) { selM.value = 'ALL'; filterFeeTable(); }
    } else {
        if(btnY) { btnY.style.background = '#4F46E5'; btnY.style.color = '#ffffff'; }
        if(btnM) { btnM.style.background = 'transparent'; btnM.style.color = '#475569'; }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // View Fee Modal Logic
    const viewModal = document.getElementById('feeViewModal');
    const viewBtns = document.querySelectorAll('.btn-view-fee');
    const closeViewBtns = document.querySelectorAll('.close-fee-view');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('vModalVoucherNo').innerText = btn.dataset.voucher;
            document.getElementById('vModalMonth').innerText = btn.dataset.month + ' (Due: ' + btn.dataset.duedate + ')';
            document.getElementById('vModalTuition').innerText = 'Rs ' + btn.dataset.tuition;
            document.getElementById('vModalExam').innerText = 'Rs ' + btn.dataset.exam;
            document.getElementById('vModalOther').innerText = 'Rs ' + btn.dataset.other;
            document.getElementById('vModalFine').innerText = '+ Rs ' + btn.dataset.fine;
            document.getElementById('vModalTotal').innerText = 'Rs ' + btn.dataset.total;

            const st = btn.dataset.status;
            const statusEl = document.getElementById('vModalStatus');
            statusEl.innerText = st;
            statusEl.style.color = st.toLowerCase() === 'paid' ? '#047857' : '#b45309';

            if (viewModal) viewModal.style.display = 'grid';
        });
    });

    closeViewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (viewModal) viewModal.style.display = 'none';
        });
    });

    // Pay Fee Modal Logic
    const payModal = document.getElementById('feePayModal');
    const payBtns = document.querySelectorAll('.btn-pay-fee');
    const closePayBtns = document.querySelectorAll('.close-fee-pay');
    const payForm = document.getElementById('feePayForm');

    payBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('pModalVoucherNo').innerText = btn.dataset.voucher + ' (' + btn.dataset.month + ')';
            document.getElementById('pModalNet').innerText = 'Rs ' + btn.dataset.net;
            document.getElementById('pModalFine').innerText = '+ Rs ' + btn.dataset.fine;
            document.getElementById('pModalTotal').innerText = 'Rs ' + btn.dataset.total;
            if (payForm) payForm.action = btn.dataset.action;

            if (payModal) payModal.style.display = 'grid';
        });
    });

    closePayBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (payModal) payModal.style.display = 'none';
        });
    });

    if (viewModal) {
        viewModal.addEventListener('click', (e) => {
            if (e.target === viewModal) viewModal.style.display = 'none';
        });
    }

    if (payModal) {
        payModal.addEventListener('click', (e) => {
            if (e.target === payModal) payModal.style.display = 'none';
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (viewModal) viewModal.style.display = 'none';
            if (payModal) payModal.style.display = 'none';
        }
    });
});
</script>

@elseif($page === 'profile')
<div class="page-heading">
    <p>AKM STUDENT PORTAL &bull; PROFILE</p>
    <h1>Student Profile & Account Settings</h1>
</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:24px;">
    <article class="student-panel" style="text-align:center;padding:32px 24px;">
        <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#fff;display:grid;place-items:center;font-size:36px;font-weight:800;margin:0 auto 16px;border:4px solid #C7D2FE;box-shadow:0 8px 20px rgba(79,70,229,0.25);">
            @if($student->image)
                <img src="{{ asset('storage/'.$student->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            @else
                {{ strtoupper(substr($student->name, 0, 1)) }}
            @endif
        </div>
        <h2 style="margin:0;font-size:20px;font-weight:800;color:#0f172a;">{{ $student->name }}</h2>
        <p style="margin:4px 0 16px;color:#4F46E5;font-weight:700;font-size:13.5px;">Enrolled Student &bull; {{ $student->class }} ({{ $student->section }})</p>
        
        <dl style="display:flex;flex-direction:column;gap:10px;margin:0;text-align:left;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Full Name</dt>
                <dd style="margin:0;font-weight:700;color:#0f172a;font-size:13.5px;">{{ $student->name }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Email</dt>
                <dd style="margin:0;font-weight:700;color:#0f172a;font-size:13.5px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $user->email }}">{{ $user->email }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Class & Section</dt>
                <dd style="margin:0;font-weight:700;color:#4F46E5;font-size:13.5px;">{{ $student->class }} ({{ $student->section }})</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Roll & Reg No</dt>
                <dd style="margin:0;font-weight:700;color:#0f172a;font-size:12.5px;">#{{ $student->std_id }} &bull; {{ $student->registration_no ?: ('REG-2026-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')).'-'.str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT)) }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;">
                <dt style="color:#64748b;font-size:13px;font-weight:600;">Phone</dt>
                <dd style="margin:0;font-weight:700;color:#0f172a;font-size:13.5px;">{{ $student->phone_no ?: '0300-1234567' }}</dd>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#EEF2FF;border-radius:10px;border:1px solid #C7D2FE;">
                <dt style="color:#0f766e;font-size:13px;font-weight:700;">Status</dt>
                <dd style="margin:0;font-weight:800;color:#4F46E5;font-size:14px;">Active Student</dd>
            </div>
        </dl>
    </article>

    <article class="student-panel" style="padding:28px;">
        <h3 style="margin:0 0 20px;font-size:18px;font-weight:800;color:#0f172a;border-bottom:1px solid #f1f5f9;padding-bottom:12px;"><i class="bi bi-shield-lock" style="color:#4F46E5;"></i> Account Security & Settings</h3>
        
        @if($errors->any())
            <div style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:12px 16px;border-radius:12px;font-size:13px;font-weight:600;margin-bottom:18px;">
                @foreach($errors->all() as $err)
                    <div>&bull; {{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:28px;">
            @csrf
            @method('PUT')
            <h4 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;">Edit Profile Information</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Full Name</label>
                    <input type="text" name="name" value="{{ $student->name }}" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;font-weight:600;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Guardian Phone Number</label>
                    <input type="text" name="phone_no" value="{{ $student->phone_no }}" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;font-weight:600;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Emergency Phone</label>
                    <input type="text" name="emergency_phone" value="{{ $student->emergency_phone }}" style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Profile Photo</label>
                    <input type="file" name="image" accept="image/*" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:10px;font-size:12.5px;">
                </div>
            </div>
            <button type="submit" style="background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#fff;border:none;padding:10px 22px;border-radius:10px;font-weight:800;font-size:13px;cursor:pointer;box-shadow:0 4px 14px rgba(79,70,229,0.25);">
                Update Profile
            </button>
        </form>

        <form action="{{ route('student.password.update') }}" method="POST">
            @csrf
            <h4 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:14px;border-top:1px solid #f1f5f9;padding-top:20px;">Change Password</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Current Password</label>
                    <input type="password" name="current_password" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">New Password</label>
                    <input type="password" name="password" minlength="8" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
                </div>
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Confirm New Password</label>
                <input type="password" name="password_confirmation" minlength="8" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:10px;font-size:13.5px;">
            </div>
            <button type="submit" style="background:#0f172a;color:#fff;border:none;padding:10px 22px;border-radius:10px;font-weight:800;font-size:13px;cursor:pointer;box-shadow:0 4px 14px rgba(15,23,42,0.25);">
                Update Password
            </button>
        </form>
    </article>
</div>

@elseif($page === 'outlines')
<div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <div>
        <p>AKM STUDENT PORTAL &bull; COURSE SYLLABI & ROADMAPS</p>
        <h1>Class Course Outlines & Syllabi</h1>
    </div>
    <div>
        <span style="background:#e0f2fe;color:#0284c7;border:1px solid #bae6fd;padding:8px 16px;border-radius:99px;font-size:13px;font-weight:800;">
            <i class="bi bi-book-half"></i> {{ $student->class }} ({{ $student->section }}) Syllabi
        </span>
    </div>
</div>

<div class="student-panel" style="padding:26px;">
    <div class="panel-head" style="margin-bottom:20px;border-bottom:1px solid #f1f5f9;padding-bottom:14px;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h2><i class="bi bi-journal-bookmark-fill" style="color:#4F46E5;"></i> Enrolled Subject Course Outlines</h2>
            <p>Official course outlines, term topics, and study material uploaded by subject teachers for {{ $student->class }} ({{ $student->section }}).</p>
        </div>
        <div style="font-size:12.5px;color:#64748b;font-weight:600;">
            Total Outlines: <strong style="color:#4F46E5;">{{ count($outlines ?? []) }} Available</strong>
        </div>
    </div>

    <div class="table-panel">
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Outline Title</th>
                    <th>Term</th>
                    <th>Course Description & Topics</th>
                    <th>Teacher Assigned</th>
                    <th>Date Uploaded</th>
                    <th style="text-align:center;">Action (Download)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outlines ?? [] as $out)
                <tr>
                    <td><strong style="color:#4F46E5;font-size:14px;">{{ $out->subject_name }}</strong></td>
                    <td><strong style="color:#0f172a;">{{ $out->title }}</strong></td>
                    <td>
                        <span class="status" style="background:#e0f2fe;color:#0284c7;border:1px solid #bae6fd;font-weight:800;">
                            <i class="bi bi-tag-fill"></i> {{ $out->term }}
                        </span>
                    </td>
                    <td style="max-width:320px;font-size:12.5px;color:#475569;line-height:1.5;">{{ $out->description }}</td>
                    <td>
                        @php
                            $subjKey = strtolower(trim($out->subject_name));
                            $tAssigned = $teachers->get($subjKey)?->teacher?->teacher_name;
                            if(!$tAssigned) {
                                foreach($teachers as $tK => $tVal) {
                                    if(str_contains($subjKey, $tK) || str_contains($tK, $subjKey)) {
                                        $tAssigned = $tVal->teacher?->teacher_name;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <strong style="color:#0f172a;font-size:13px;">{{ $tAssigned ?: ($out->teacher?->teacher_name ?: 'Subject Teacher') }}</strong>
                    </td>
                    <td style="color:#64748b;font-size:12.5px;">{{ \Carbon\Carbon::parse($out->created_at)->format('d M Y') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('student.outlines.download', $out->id) }}" target="_blank" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%);color:#ffffff;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:18px;box-shadow:0 4px 12px rgba(79,70,229,0.3);transition:all 0.2s ease;text-decoration:none;" title="Download Official Course Outline PDF">
                            <i class="bi bi-download"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:28px;color:#64748b;font-weight:600;">No course outlines uploaded for {{ $student->class }} yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
<section class="page-heading">
    <p>{{ strtoupper($page) }}</p>
    <h1>{{ ucfirst($page) }}</h1>
</section>
<article class="student-panel table-panel">
    <table>
        <thead>
            <tr>
                @if($page==='fees')
                    <th>Month</th><th>Voucher</th><th>Amount</th><th>Status</th>
                @elseif($page==='attendance')
                    <th>Date</th><th>Status</th><th>Remarks</th>
                @else
                    <th>Detail</th><th>Value</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if($page==='fees')
                @forelse($fees as $item)
                    <tr>
                        <td>{{ $item->month }}</td>
                        <td>{{ $item->voucher_no }}</td>
                        <td>Rs {{ number_format($item->net_amount,2) }}</td>
                        <td><span class="status">{{ $item->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4">No fee records are available.</td></tr>
                @endforelse
            @elseif($page==='attendance')
                @forelse($attendance as $item)
                    <tr>
                        <td>{{ $item->date->format('d M Y') }}</td>
                        <td><span class="status">{{ ucfirst($item->status) }}</span></td>
                        <td>{{ $item->remarks ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No attendance records are available.</td></tr>
                @endforelse
            @else
                <tr><td>Email</td><td>{{ $student->email }}</td></tr>
                <tr><td>Guardian phone</td><td>{{ $student->phone_no }}</td></tr>
            @endif
        </tbody>
    </table>
</article>
@endif

@endsection