@extends('layout')

@section('content')
<div class="student-profile-page">

    {{-- TOP ACTION BAR --}}
    <div class="student-profile-top-bar">
        @if($currentUser->role?->slug === 'student')
            <a href="{{ route('dashboard') }}" class="student-profile-back-btn">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <span>Back to Dashboard</span>
            </a>
        @else
        <a href="{{ url('/list') }}" class="student-profile-back-btn">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>Back to All Students</span>
        </a>
        @endif

        <div class="student-profile-actions">
            @if($currentUser->role?->slug === 'student')
                <a href="{{ route('students.results.download', $student) }}" class="student-profile-action-btn list-btn"><i class="bi bi-download" aria-hidden="true"></i><span>Download Results</span></a>
            @endif
            <button type="button" class="student-profile-action-btn print-btn" onclick="window.print()">
                <i class="bi bi-printer-fill" aria-hidden="true"></i>
                <span>Print Profile</span>
            </button>
            @if($currentUser->role?->slug !== 'student')
                <a href="{{ url('/list') }}" class="student-profile-action-btn list-btn">
                    <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                    <span>Students List</span>
                </a>
            @endif
        </div>
    </div>

    {{-- HERO HEADER --}}
    <section class="student-profile-hero">
        <div class="student-profile-hero-left">
            <div class="student-profile-avatar-wrap">
                <img
                    src="{{ $student->image && $student->image !== 'dummy.jpeg' ? asset('storage/'.$student->image) : asset('upload/dummy.jpeg') }}"
                    onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';"
                    alt="{{ $student->name }}"
                    class="student-profile-avatar"
                >
                <span class="student-profile-avatar-badge" title="Roll No">#{{ $student->std_id }}</span>
            </div>
            <div class="student-profile-hero-info">
                <div class="student-profile-kicker-row">
                    <span class="student-profile-kicker">Student Profile</span>
                    <span class="section-badge section-{{ strtolower($student->section) }}">{{ $student->section }} Section</span>
                    <span class="student-class-chip"><i class="bi bi-mortarboard" aria-hidden="true"></i> Class {{ $student->class }}</span>
                </div>
                <h1 class="student-profile-name">{{ $student->name }}</h1>
                <div class="student-profile-hero-meta">
                    <span><i class="bi bi-card-text" aria-hidden="true"></i> {{ $student->registration_no ?? ('REG-2026-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')).'-'.str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT)) }}</span>
                    <span><i class="bi bi-envelope-fill" aria-hidden="true"></i> {{ $student->email }}</span>
                    <span><i class="bi bi-telephone-fill" aria-hidden="true"></i> {{ $student->phone_no }}</span>
                    <span><i class="bi bi-hash" aria-hidden="true"></i> Roll No: {{ $student->std_id }}</span>
                </div>
            </div>
        </div>

        <div class="student-profile-hero-stats">
            <div class="student-hero-stat-card">
                <span class="student-hero-stat-label">Overall Grade</span>
                <span class="student-hero-stat-value grade-{{ strtolower(str_replace('+', '-plus', $grade ?? 'none')) }}">
                    {{ $grade ?? '—' }}
                </span>
                <small class="student-hero-stat-sub">
                    @if($percentage !== null)
                        {{ number_format($percentage, 1) }}% Aggregate
                    @else
                        No Results
                    @endif
                </small>
            </div>

            <div class="student-hero-stat-card">
                <span class="student-hero-stat-label">Exam Marks</span>
                <span class="student-hero-stat-value marks-val">
                    @if($totalMarks > 0)
                        {{ rtrim(rtrim(number_format($obtainedMarks, 2), '0'), '.') }}
                    @else
                        —
                    @endif
                </span>
                <small class="student-hero-stat-sub">
                    @if($totalMarks > 0)
                        out of {{ rtrim(rtrim(number_format($totalMarks, 2), '0'), '.') }}
                    @else
                        Pending
                    @endif
                </small>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT GRID --}}
    <div class="student-profile-layout">

        {{-- LEFT COLUMN: PERSONAL DETAILS & SEATING ARRANGEMENT --}}
        <div class="student-profile-col student-profile-col-left">

            {{-- PERSONAL INFORMATION CARD --}}
            <section class="student-profile-card">
                <div class="student-profile-card-header">
                    <div class="student-profile-card-title">
                        <i class="bi bi-person-vcard-fill" aria-hidden="true"></i>
                        <h2>Student Information</h2>
                    </div>
                    <span class="student-card-tag">Profile Details</span>
                </div>

                <div class="student-details-list">
                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-person" aria-hidden="true"></i> Full Name</span>
                        <strong class="student-detail-val">{{ $student->name }}</strong>
                    </div>

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-card-heading" aria-hidden="true"></i> Registration No</span>
                        <strong class="student-detail-val reg-no-highlight">{{ $student->registration_no ?? ('REG-2026-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')).'-'.str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT)) }}</strong>
                    </div>

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-hash" aria-hidden="true"></i> Roll No</span>
                        <strong class="student-detail-val">
                            <span class="student-id-chip">#{{ $student->std_id }}</span>
                        </strong>
                    </div>

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-envelope" aria-hidden="true"></i> Email Address</span>
                        <a href="mailto:{{ $student->email }}" class="student-detail-val student-link">
                            {{ $student->email }}
                        </a>
                    </div>

                    @if($currentUser->isAdmin() || ($currentUser->role?->slug === 'student' && $currentUser->id === $student->login_user_id))
                        @if($student->loginAccount)
                            <div class="student-detail-item student-detail-item-full">
                                <span class="student-detail-label"><i class="bi bi-key-fill" aria-hidden="true"></i> Portal Login</span>
                                <strong class="student-detail-val">{{ $student->loginAccount->email }}</strong>
                                @if($student->loginAccount->must_change_password)
                                    <small class="student-empty-inline">Temporary password: Student@123. Change it at first sign-in.</small>
                                @else
                                    <small class="student-empty-inline">Password changed. Use your private password to sign in.</small>
                                @endif
                            </div>
                        @endif
                    @endif

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-telephone" aria-hidden="true"></i> Guardian Phone</span>
                        <a href="tel:{{ $student->phone_no }}" class="student-detail-val student-link">
                            {{ $student->phone_no }}
                        </a>
                    </div>

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-telephone-plus" aria-hidden="true"></i> Emergency Phone</span>
                        <span class="student-detail-val">{{ $student->emergency_phone ?? '—' }}</span>
                    </div>

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-book" aria-hidden="true"></i> Class</span>
                        <strong class="student-detail-val">{{ $student->class }}</strong>
                    </div>

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-grid" aria-hidden="true"></i> Section</span>
                        <span class="section-badge section-{{ strtolower($student->section) }}">{{ $student->section }}</span>
                    </div>

                    <div class="student-detail-item student-detail-item-full">
                        <span class="student-detail-label"><i class="bi bi-journal-bookmark" aria-hidden="true"></i> Assigned Subjects</span>
                        <div class="student-assigned-subjects">
                            @if($assignedSubjects->isNotEmpty())
                                @foreach($assignedSubjects as $subj)
                                    <span class="student-subject-tag"><i class="bi bi-check2" aria-hidden="true"></i> {{ $subj }}</span>
                                @endforeach
                            @else
                                <span class="student-empty-inline">No specific subjects assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            @if($currentUser->hasPermission('fees.view'))
            {{-- FEE STRUCTURE & PARTICULARS CARD --}}
            <section class="student-profile-card">
                <div class="student-profile-card-header">
                    <div class="student-profile-card-title">
                        <i class="bi bi-wallet2" aria-hidden="true"></i>
                        <h2>Fee Structure & Breakdown</h2>
                    </div>
                    <span class="student-card-tag">Class {{ $student->class }} Fee</span>
                </div>

                @php
                    $fs = $classFeeStructure;
                    $studentMonthly = (float) $student->monthly_fee;
                    $studentTest = (float) $student->test_session_fee;
                    $studentAdmission = (float) $student->admission_fee;

                    $tuition = $studentMonthly > 0 ? $studentMonthly : ($fs ? (float)$fs->tuition_fee : 3500);
                    $testFee = $studentTest > 0 ? $studentTest : ($fs ? (float)$fs->test_session_fee : 500);
                    $examFee = (float) ($fs ? $fs->exam_fee : 500);
                    $labSports = (float) ($fs ? $fs->lab_sports_fee : 300);
                    $otherCharges = (float) ($fs ? $fs->other_charges : 200);
                    $admissionFee = $studentAdmission > 0 ? $studentAdmission : ($fs ? (float)$fs->admission_fee : 5000);

                    $totalMonthlyRecurring = $tuition + $testFee + $examFee + $labSports + $otherCharges;
                    $totalInitialEnrolment = $totalMonthlyRecurring + $admissionFee;
                @endphp

                <div class="student-details-list">
                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-cash-stack" aria-hidden="true"></i> Monthly Tuition Fee</span>
                        <strong class="student-detail-val reg-no-highlight">Rs. {{ number_format($tuition) }} / month</strong>
                    </div>

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-card-checklist" aria-hidden="true"></i> Test Session Fee</span>
                        <strong class="student-detail-val">Rs. {{ number_format($testFee) }} / month</strong>
                    </div>

                    @if($examFee > 0)
                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Exam Charges</span>
                        <strong class="student-detail-val">Rs. {{ number_format($examFee) }}</strong>
                    </div>
                    @endif

                    @if($labSports > 0)
                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-trophy" aria-hidden="true"></i> Lab / Sports Fee</span>
                        <strong class="student-detail-val">Rs. {{ number_format($labSports) }}</strong>
                    </div>
                    @endif

                    @if($otherCharges > 0)
                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-three-dots" aria-hidden="true"></i> Other Charges</span>
                        <strong class="student-detail-val">Rs. {{ number_format($otherCharges) }}</strong>
                    </div>
                    @endif

                    <div class="student-detail-item">
                        <span class="student-detail-label"><i class="bi bi-award-fill" aria-hidden="true"></i> One-Time Admission Fee</span>
                        <strong class="student-detail-val">
                            Rs. {{ number_format($admissionFee) }}
                            <span class="badge bg-info text-dark ms-1" style="font-size: 10px;">One-Time</span>
                        </strong>
                    </div>

                    <div class="student-detail-item student-detail-item-full ext-style-119">
                        <span class="ext-style-120"><i class="bi bi-calculator-fill" aria-hidden="true"></i> Total Recurring Monthly Fee</span>
                        <strong class="ext-style-121 text-success fs-16">Rs. {{ number_format($totalMonthlyRecurring) }} / month</strong>
                    </div>

                    <div class="student-detail-item student-detail-item-full mt-2 pt-2 border-top">
                        <span class="text-secondary fs-13"><i class="bi bi-info-circle-fill me-1" aria-hidden="true"></i> First Month Enrolment Total (with One-Time Admission): <strong>Rs. {{ number_format($totalInitialEnrolment) }}</strong></span>
                    </div>
                </div>
            </section>

            @if($currentUser->hasPermission('arrangements.view'))
            {{-- SEATING & ARRANGEMENT CARD --}}
            <section class="student-profile-card">
                <div class="student-profile-card-header">
                    <div class="student-profile-card-title">
                        <i class="bi bi-grid-3x3-gap-fill" aria-hidden="true"></i>
                        <h2>Seating Arrangement</h2>
                    </div>
                    @if($arrangement)
                        <span class="student-badge-success"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Allocated</span>
                    @else
                        <span class="student-badge-warning"><i class="bi bi-clock-history" aria-hidden="true"></i> Not Allocated</span>
                    @endif
                </div>

                @if($arrangement)
                    <div class="student-arrangement-grid">
                        <div class="student-seat-box">
                            <span class="seat-box-label">Room / Hall</span>
                            <strong class="seat-box-value">{{ $arrangement->room }}</strong>
                            <small class="seat-box-meta">Examination Hall</small>
                        </div>
                        <div class="student-seat-box">
                            <span class="seat-box-label">Chair / Desk No</span>
                            <strong class="seat-box-value">{{ $arrangement->chairs }}</strong>
                            <small class="seat-box-meta">Assigned Seat</small>
                        </div>
                        <div class="student-seat-box">
                            <span class="seat-box-label">Allocated Roll No</span>
                            <strong class="seat-box-value">{{ $arrangement->roll_no }}</strong>
                            <small class="seat-box-meta">Std. ID Match</small>
                        </div>
                    </div>

                    @if($arrangement->classRecord)
                        <div class="student-arrangement-note">
                            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                            <span>Associated with Class Group: <strong>{{ $arrangement->classRecord->class_name }}</strong></span>
                        </div>
                    @endif
                @else
                    <div class="student-profile-empty-box">
                        <i class="bi bi-chair" aria-hidden="true"></i>
                        <p>No seating arrangement has been created for Roll No #{{ $student->std_id }} yet.</p>
                        <a href="{{ route('arrangements.index') }}" class="student-empty-action">
                            <i class="bi bi-arrow-right" aria-hidden="true"></i> Manage Arrangements
                        </a>
                    </div>
                @endif
            </section>
            @endif

            @endif
        </div>

        {{-- RIGHT COLUMN: RESULTS & DATESHEET / ROLL SLIPS --}}
        <div class="student-profile-col student-profile-col-right">

            {{-- ACADEMIC RESULTS CARD --}}
            <section class="student-profile-card">
                <div class="student-profile-card-header">
                    <div class="student-profile-card-title">
                        <i class="bi bi-clipboard2-data-fill" aria-hidden="true"></i>
                        <h2>Examination Results</h2>
                    </div>

                    @if($percentage !== null)
                        <div class="student-result-pill-summary">
                            <span>Aggregate: <strong>{{ number_format($percentage, 1) }}%</strong></span>
                            <span class="student-result-pill-grade">{{ $grade }}</span>
                        </div>
                    @endif
                </div>

                {{-- 3 TERM CARDS IN A ROW --}}
                <div class="term-cards-row">
                    @foreach(['First Term', 'Mid Term', 'Final Term'] as $tName)
                        @php
                            $tData = $termResults[$tName];
                        @endphp
                        <div class="term-card {{ $tData['hasData'] ? 'has-data' : 'no-data' }}">
                            <div class="term-card-header">
                                <span class="term-card-icon">
                                    @if($tName === 'First Term')
                                        <i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
                                    @elseif($tName === 'Mid Term')
                                        <i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i>
                                    @else
                                        <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                                    @endif
                                </span>
                                <span class="term-card-badge {{ $tData['hasData'] ? 'badge-success' : 'badge-pending' }}">
                                    @if($tData['hasData'])
                                        {{ $tData['grade'] ? 'Grade '.$tData['grade'] : 'Declared' }}
                                    @else
                                        Pending
                                    @endif
                                </span>
                            </div>

                            <div class="term-card-body">
                                <h3 class="term-card-title">{{ $tName }}</h3>
                                @if($tData['hasData'])
                                    <div class="term-card-meta">
                                        <div class="term-stat-box">
                                            <small>Marks</small>
                                            <strong>{{ rtrim(rtrim(number_format($tData['obtainedMarks'], 2), '0'), '.') }} / {{ rtrim(rtrim(number_format($tData['totalMarks'], 2), '0'), '.') }}</strong>
                                        </div>
                                        <div class="term-stat-box text-end">
                                            <small>Percentage</small>
                                            <strong class="text-teal">{{ $tData['percentage'] }}%</strong>
                                        </div>
                                    </div>
                                @else
                                    <p class="term-card-empty-sub"><i class="bi bi-clock-history"></i> Result not declared yet</p>
                                @endif
                            </div>

                            <div class="term-card-footer">
                                <button type="button" class="btn-term-action" data-crud-modal-open="result-modal-{{ $tData['slug'] }}">
                                    <i class="bi bi-eye-fill" aria-hidden="true"></i>
                                    <span>View Result</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if($currentUser->hasPermission('roll-slips.view'))
            {{-- ROLL NUMBER SLIPS & DATESHEET CARD --}}
            <section class="student-profile-card">
                <div class="student-profile-card-header">
                    <div class="student-profile-card-title">
                        <i class="bi bi-file-earmark-text-fill" aria-hidden="true"></i>
                        <h2>Roll Number Slips & Datesheets</h2>
                    </div>
                    <span class="student-card-tag">3 Examinations</span>
                </div>

                {{-- 3 TERM CARDS IN A ROW FOR ROLL SLIPS --}}
                <div class="term-cards-row">
                    @foreach(['First Term', 'Mid Term', 'Final Term'] as $tName)
                        @php
                            $sData = $termRollSlips[$tName];
                            $slip = $sData['slip'];
                        @endphp
                        <div class="term-card {{ $sData['hasData'] ? 'has-data' : 'no-data' }}">
                            <div class="term-card-header">
                                <span class="term-card-icon slip-icon">
                                    <i class="bi bi-file-earmark-pdf-fill" aria-hidden="true"></i>
                                </span>
                                <span class="term-card-badge {{ $sData['hasData'] ? 'badge-issued' : 'badge-pending' }}">
                                    {{ $sData['hasData'] ? 'Issued' : 'Not Issued' }}
                                </span>
                            </div>

                            <div class="term-card-body">
                                <h3 class="term-card-title">{{ $tName }} Slip</h3>
                                @if($sData['hasData'])
                                    <div class="term-card-meta">
                                        <div class="term-stat-box">
                                            <small>Room / Hall</small>
                                            <strong>{{ $slip->room_no }}</strong>
                                        </div>
                                        <div class="term-stat-box text-end">
                                            <small>Chair No</small>
                                            <strong class="text-teal">#{{ $slip->chair_no }}</strong>
                                        </div>
                                    </div>
                                @else
                                    <p class="term-card-empty-sub"><i class="bi bi-exclamation-circle"></i> Roll slip not generated</p>
                                @endif
                            </div>

                            <div class="term-card-footer">
                                <button type="button" class="btn-term-action" data-crud-modal-open="slip-modal-{{ $sData['slug'] }}">
                                    <i class="bi bi-window-stack" aria-hidden="true"></i>
                                    <span>View Roll Slip</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- ATTENDANCE RESULT & SUMMARY CARD --}}
            <section class="student-profile-card">
                <div class="student-profile-card-header">
                    <div class="student-profile-card-title">
                        <i class="bi bi-calendar-check-fill" aria-hidden="true"></i>
                        <h2>Attendance Result & Summary</h2>
                    </div>
                    @if($attendancePercentage !== null)
                        <span class="student-card-tag">{{ $attendancePercentage }}% Overall Rate</span>
                    @else
                        <span class="student-card-tag">No Records</span>
                    @endif
                </div>

                @if($totalWorkingDays > 0)
                    {{-- ATTENDANCE STAT BOXES --}}
                    <div class="student-att-summary-card">
                        <div class="student-att-stat">
                            <span class="student-att-stat-icon att-icon-days"><i class="bi bi-calendar3"></i></span>
                            <span class="student-att-stat-num">{{ $totalWorkingDays }}</span>
                            <span class="student-att-stat-label">Working Days</span>
                        </div>
                        <div class="student-att-stat">
                            <span class="student-att-stat-icon att-icon-present"><i class="bi bi-check-circle-fill"></i></span>
                            <span class="student-att-stat-num">{{ $totalPresentCount }}</span>
                            <span class="student-att-stat-label">Present</span>
                        </div>
                        <div class="student-att-stat">
                            <span class="student-att-stat-icon att-icon-absent"><i class="bi bi-x-circle-fill"></i></span>
                            <span class="student-att-stat-num">{{ $totalAbsentCount }}</span>
                            <span class="student-att-stat-label">Absent</span>
                        </div>
                        <div class="student-att-stat">
                            <span class="student-att-stat-icon att-icon-leave"><i class="bi bi-clock-fill"></i></span>
                            <span class="student-att-stat-num">{{ $totalLeaveCount }}</span>
                            <span class="student-att-stat-label">Leave</span>
                        </div>
                    </div>

                    {{-- ATTENDANCE PROGRESS & PERFORMANCE --}}
                    <div class="student-att-progress-wrap">
                        <div class="student-att-bar-outer">
                            <div class="student-att-bar-inner {{ $attendancePercentage >= 75 ? '' : ($attendancePercentage >= 50 ? 'att-bar-warning' : 'att-bar-danger') }}"
                                 style="width: {{ $attendancePercentage }}%"></div>
                        </div>
                        <span class="student-att-pct-text">
                            @if($attendancePercentage >= 75)
                                <span class="badge bg-success"><i class="bi bi-emoji-smile"></i> {{ $attendancePercentage }}% Excellent</span>
                            @elseif($attendancePercentage >= 50)
                                <span class="badge bg-warning text-dark"><i class="bi bi-emoji-neutral"></i> {{ $attendancePercentage }}% Satisfactory</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-emoji-frown"></i> {{ $attendancePercentage }}% Low Attendance</span>
                            @endif
                        </span>
                    </div>

                    {{-- RECENT ATTENDANCE TIMELINE DOTS --}}
                    @if($recentAttendances->isNotEmpty())
                        <div style="margin-top: 18px;">
                            <p style="font-size: 11px; color: #64748b; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-clock-history" aria-hidden="true"></i> Last {{ $recentAttendances->count() }} Days Timeline
                            </p>
                            <div class="student-att-recent">
                                @foreach($recentAttendances->reverse() as $att)
                                    <span class="student-att-dot student-att-dot-{{ $att->status }}"
                                          title="{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }} — {{ ucfirst($att->status) }}">
                                        {{ strtoupper(substr($att->status, 0, 1)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- DETAILED ATTENDANCE LOG / RESULT LIST --}}
                        <div class="student-att-log-table-wrap" style="margin-top: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                            <h4 style="font-size: 12px; font-weight: 800; color: #334155; margin: 0 0 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="bi bi-journal-check" aria-hidden="true"></i> Recent Attendance Result Log
                            </h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
                                <thead>
                                    <tr style="border-bottom: 1.5px solid #cbd5e1; text-align: left; color: #64748b; font-size: 11px; text-transform: uppercase;">
                                        <th style="padding: 6px 8px;">Date</th>
                                        <th style="padding: 6px 8px;">Day</th>
                                        <th style="padding: 6px 8px; text-align: center;">Status Result</th>
                                        <th style="padding: 6px 8px;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendances->take(7) as $att)
                                        @php
                                            $dt = \Carbon\Carbon::parse($att->date);
                                        @endphp
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 8px; font-weight: 600; color: #1e293b;">{{ $dt->format('d M Y') }}</td>
                                            <td style="padding: 8px; color: #64748b; font-size: 12px;">{{ $dt->format('l') }}</td>
                                            <td style="padding: 8px; text-align: center;">
                                                @if($att->status === 'present')
                                                    <span class="badge bg-success" style="padding: 5px 10px; font-size: 11px;"><i class="bi bi-check-circle-fill"></i> Present</span>
                                                @elseif($att->status === 'absent')
                                                    <span class="badge bg-danger" style="padding: 5px 10px; font-size: 11px;"><i class="bi bi-x-circle-fill"></i> Absent</span>
                                                @elseif($att->status === 'leave')
                                                    <span class="badge bg-warning text-dark" style="padding: 5px 10px; font-size: 11px;"><i class="bi bi-clock-fill"></i> Leave</span>
                                                @endif
                                            </td>
                                            <td style="padding: 8px; color: #64748b; font-size: 12px;">{{ $att->remarks ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="student-profile-empty-box" style="text-align: center; padding: 24px 16px;">
                        <i class="bi bi-calendar-x" aria-hidden="true" style="font-size: 32px; color: #94a3b8;"></i>
                        <p style="margin: 8px 0 0; color: #64748b; font-size: 13.5px; font-weight: 600;">No attendance results logged for {{ $student->name }} yet.</p>
                    </div>
                @endif
            </section>

        </div>

    </div>

    {{-- RESULT POPUP MODALS FOR THE 3 TERMS --}}
    @foreach(['First Term', 'Mid Term', 'Final Term'] as $tName)
        @php
            $tData = $termResults[$tName];
        @endphp
        <div class="crud-backdrop" id="result-modal-{{ $tData['slug'] }}" hidden>
            <section class="crud-modal term-result-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="result-title-{{ $tData['slug'] }}">
                <div class="modal-top-bar">
                    <button type="button" class="btn-modal-back" data-crud-modal-close>
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        <span>Back to Profile</span>
                    </button>
                    <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>
                </div>

                <div class="modal-header-strip">
                    <div class="modal-header-icon-badge">
                        <i class="bi bi-award-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 id="result-title-{{ $tData['slug'] }}">{{ $tName }} Examination Result</h2>
                        <p>Student: <strong>{{ $student->name }}</strong> · Roll No: <strong>#{{ $student->std_id }}</strong> · Class: <strong>{{ $student->class }}</strong> ({{ $student->section }})</p>
                    </div>
                </div>

                @if($tData['hasData'])
                    {{-- SCORE SUMMARY BAR --}}
                    <div class="modal-score-summary-grid">
                        <div class="score-summary-item">
                            <small>Total Marks</small>
                            <strong>{{ rtrim(rtrim(number_format($tData['totalMarks'], 2), '0'), '.') }}</strong>
                        </div>
                        <div class="score-summary-item">
                            <small>Obtained Marks</small>
                            <strong class="text-teal">{{ rtrim(rtrim(number_format($tData['obtainedMarks'], 2), '0'), '.') }}</strong>
                        </div>
                        <div class="score-summary-item">
                            <small>Percentage</small>
                            <strong>{{ $tData['percentage'] }}%</strong>
                        </div>
                        <div class="score-summary-item">
                            <small>Overall Grade</small>
                            <span class="student-grade-pill grade-pill-{{ strtolower(str_replace('+', '-plus', $tData['grade'] ?? 'none')) }}">
                                {{ $tData['grade'] ?? '—' }}
                            </span>
                        </div>
                    </div>

                    {{-- SUBJECT WISE TABLE --}}
                    <div class="student-table-container mt-3">
                        <table class="student-results-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Total Marks</th>
                                    <th>Obtained</th>
                                    <th>Percentage</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allSubjects as $subjName)
                                    @php
                                        $res = $tData['resultsBySubject']->get(strtolower(trim($subjName)));
                                    @endphp
                                    <tr>
                                        <td class="subj-name-cell">
                                            <strong>{{ $subjName }}</strong>
                                        </td>
                                        @if($res)
                                            <td>{{ rtrim(rtrim(number_format($res->total_marks, 2), '0'), '.') }}</td>
                                            <td class="marks-obtained-cell">{{ rtrim(rtrim(number_format($res->obtained_marks, 2), '0'), '.') }}</td>
                                            <td>
                                                <div class="subj-pct-cell">
                                                    <span>{{ number_format($res->percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="student-grade-pill grade-pill-{{ strtolower(str_replace('+', '-plus', $res->grade ?? 'none')) }}">
                                                    {{ $res->grade ?? '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(strtoupper($res->grade ?? '') === 'F' || (float)$res->percentage < 40)
                                                    <span class="status-chip status-fail"><i class="bi bi-x-circle-fill" aria-hidden="true"></i> Fail</span>
                                                @else
                                                    <span class="status-chip status-pass"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Pass</span>
                                                @endif
                                            </td>
                                        @else
                                            <td colspan="4" class="student-result-pending-cell">
                                                <i class="bi bi-hourglass-split" aria-hidden="true"></i> Result not entered for {{ $tName }}
                                            </td>
                                            <td>
                                                <span class="status-chip status-pending">Pending</span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="student-profile-empty-box my-4">
                        <i class="bi bi-clipboard2-x" aria-hidden="true"></i>
                        <p>No results have been declared or entered for <strong>{{ $tName }}</strong> yet.</p>
                    </div>
                @endif

                <div class="modal-footer-actions">
                    <button type="button" class="btn-modal-back-sec" data-crud-modal-close>
                        <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Profile
                    </button>
                    @if($tData['hasData'])
                        <button type="button" class="btn-modal-print" onclick="window.print()">
                            <i class="bi bi-printer-fill" aria-hidden="true"></i> Print {{ $tName }} Result
                        </button>
                    @endif
                </div>
            </section>
        </div>
    @endforeach

    {{-- ROLL SLIP POPUP MODALS FOR THE 3 TERMS --}}
    @foreach(['First Term', 'Mid Term', 'Final Term'] as $tName)
        @php
            $sData = $termRollSlips[$tName];
            $slip = $sData['slip'];
        @endphp
        <div class="crud-backdrop" id="slip-modal-{{ $sData['slug'] }}" hidden>
            <section class="crud-modal roll-slip-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="slip-title-{{ $sData['slug'] }}">
                <div class="modal-top-bar">
                    <button type="button" class="btn-modal-back" data-crud-modal-close>
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        <span>Back to Profile</span>
                    </button>
                    <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>
                </div>

                <div class="modal-header-strip">
                    <div class="modal-header-icon-badge slip-header-badge">
                        <i class="bi bi-file-earmark-pdf-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 id="slip-title-{{ $sData['slug'] }}">{{ $tName }} Roll Number Slip & Datesheet</h2>
                        <p>Student: <strong>{{ $student->name }}</strong> · Roll No: <strong>#{{ $student->std_id }}</strong> · Class: <strong>{{ $student->class }}</strong> ({{ $student->section }})</p>
                    </div>
                </div>

                @if($sData['hasData'] && $slip)
                    {{-- SEATING INFO ROW --}}
                    <div class="student-slip-meta-grid">
                        <div>
                            <small>Roll No</small>
                            <strong>#{{ $slip->roll_no }}</strong>
                        </div>
                        <div>
                            <small>Class / Section</small>
                            <strong>{{ $slip->class_name }} {{ $slip->section_name ? '('.$slip->section_name.')' : '' }}</strong>
                        </div>
                        <div>
                            <small>Exam Room / Hall</small>
                            <strong>{{ $slip->room_no }}</strong>
                        </div>
                        <div>
                            <small>Chair No</small>
                            <strong>{{ $slip->chair_no }}</strong>
                        </div>
                    </div>

                    {{-- DATESHEET SCHEDULE TABLE --}}
                    <div class="student-datesheet-wrap">
                        <h4 class="student-datesheet-heading"><i class="bi bi-calendar3" aria-hidden="true"></i> Official Datesheet Schedule</h4>
                        @if(!empty($slip->datesheet) && is_array($slip->datesheet))
                            <table class="student-datesheet-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject</th>
                                        <th>Paper Date</th>
                                        <th>Day</th>
                                        <th>Timings</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slip->datesheet as $index => $paper)
                                        @php
                                            $examDate = !empty($paper['exam_date']) ? \Carbon\Carbon::parse($paper['exam_date']) : null;
                                        @endphp
                                        <tr>
                                            <td class="datesheet-index">{{ $index + 1 }}</td>
                                            <td class="datesheet-subj">
                                                <strong>{{ $paper['subject_name'] ?? 'Subject' }}</strong>
                                            </td>
                                            <td>
                                                <span class="datesheet-date">
                                                    <i class="bi bi-calendar2-check" aria-hidden="true"></i>
                                                    {{ $examDate ? $examDate->format('d M, Y') : 'Date not fixed' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="datesheet-day">{{ $examDate ? $examDate->format('l') : '—' }}</span>
                                            </td>
                                            <td>
                                                @if(!empty($paper['start_time']))
                                                    <span class="datesheet-time"><i class="bi bi-clock" aria-hidden="true"></i> {{ $paper['start_time'] }} - {{ $paper['end_time'] ?? '' }}</span>
                                                @else
                                                    <span class="datesheet-time-default"><i class="bi bi-clock" aria-hidden="true"></i> Standard Morning Shift</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="student-profile-empty">No datesheet schedule generated for this exam yet.</p>
                        @endif
                    </div>

                    <div class="modal-footer-actions">
                        <button type="button" class="btn-modal-back-sec" data-crud-modal-close>
                            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Profile
                        </button>
                        <a href="{{ route('roll-slips.download', $slip) }}" class="btn-modal-print">
                            <i class="bi bi-download" aria-hidden="true"></i> Download PDF Slip
                        </a>
                    </div>
                @else
                    <div class="student-profile-empty-box my-4">
                        <i class="bi bi-file-earmark-x" aria-hidden="true"></i>
                        <p>No roll number slip or datesheet has been issued for <strong>{{ $tName }}</strong> yet.</p>
                    </div>

                    <div class="modal-footer-actions">
                        <button type="button" class="btn-modal-back-sec" data-crud-modal-close>
                            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Profile
                        </button>
                    </div>
                @endif
            </section>
        </div>
    @endforeach

    </div>



</div>
@endsection
