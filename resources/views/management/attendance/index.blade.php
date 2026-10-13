@extends('layout')

@section('content')
<div class="attendance-page">

    {{-- PAGE HEADER --}}
    <div class="att-page-header">
        <div class="att-header-left">
            <div class="att-header-icon-wrap">
                <i class="bi bi-calendar-check-fill" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="att-page-title">Attendance Management</h1>
                <p class="att-page-sub">Track, record and manage student attendance across all classes</p>
            </div>
        </div>
        <div class="att-header-right">
            <span class="att-header-date-chip">
                <i class="bi bi-calendar3" aria-hidden="true"></i>
                {{ now()->format('l, d M Y') }}
            </span>
        </div>
    </div>

    {{-- MODE TABS --}}
    <div class="att-mode-tabs">
        <a href="{{ route('attendance.index', array_merge(request()->except('mode'), ['mode' => 'daily'])) }}"
           class="att-mode-tab {{ $mode === 'daily' ? 'active' : '' }}">
            <i class="bi bi-calendar-day" aria-hidden="true"></i>
            <span>Daily</span>
        </a>
        <a href="{{ route('attendance.index', array_merge(request()->except('mode'), ['mode' => 'monthly'])) }}"
           class="att-mode-tab {{ $mode === 'monthly' ? 'active' : '' }}">
            <i class="bi bi-calendar-month" aria-hidden="true"></i>
            <span>Monthly Register</span>
        </a>
        <a href="{{ route('attendance.index', array_merge(request()->except('mode'), ['mode' => 'yearly'])) }}"
           class="att-mode-tab {{ $mode === 'yearly' ? 'active' : '' }}">
            <i class="bi bi-calendar-range" aria-hidden="true"></i>
            <span>Yearly Overview</span>
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="att-filter-bar">
        <form method="GET" action="{{ route('attendance.index') }}" class="att-filter-form" id="att-filter-form">
            <input type="hidden" name="mode" value="{{ $mode }}">

            {{-- CLASS DROPDOWN --}}
            <div class="att-filter-group">
                <label for="att-class-input"><i class="bi bi-mortarboard" aria-hidden="true"></i> Class</label>
                <input type="hidden" name="class" id="att-class-input" value="{{ $selectedClass }}">
                
                <div class="att-custom-dropdown" id="att-class-dropdown">
                    <button type="button" class="att-custom-dropdown-btn" id="att-class-trigger">
                        <span>{{ $selectedClass }}</span>
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="att-custom-dropdown-menu" role="listbox">
                        @foreach($classes as $cls)
                            @php $isSelected = strtolower(trim($cls)) === strtolower(trim($selectedClass)); @endphp
                            <div class="att-custom-dropdown-item {{ $isSelected ? 'selected' : '' }}"
                                 data-value="{{ $cls }}" role="option">
                                <span>{{ $cls }}</span>
                                @if($isSelected)<i class="bi bi-check2"></i>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SECTION DROPDOWN --}}
            <div class="att-filter-group">
                <label for="att-section-input"><i class="bi bi-grid-3x3-gap" aria-hidden="true"></i> Section</label>
                <input type="hidden" name="section" id="att-section-input" value="{{ $selectedSection }}">
                
                <div class="att-custom-dropdown" id="att-section-dropdown">
                    <button type="button" class="att-custom-dropdown-btn" id="att-section-trigger">
                        <span>{{ $selectedSection }}</span>
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="att-custom-dropdown-menu" role="listbox">
                        @foreach($sections as $sec)
                            @php $isSelected = strtolower(trim($sec)) === strtolower(trim($selectedSection)); @endphp
                            <div class="att-custom-dropdown-item {{ $isSelected ? 'selected' : '' }}"
                                 data-value="{{ $sec }}" role="option">
                                <span>{{ $sec }}</span>
                                @if($isSelected)<i class="bi bi-check2"></i>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($mode === 'daily')
            <div class="att-filter-group">
                <label for="att-date"><i class="bi bi-calendar-event" aria-hidden="true"></i> Date</label>
                <input type="date" name="date" id="att-date" value="{{ $selectedDate }}" onchange="document.getElementById('att-filter-form').submit()">
            </div>
            @endif

            @if($mode === 'monthly')
            <div class="att-filter-group">
                <label for="att-month"><i class="bi bi-calendar-month" aria-hidden="true"></i> Month</label>
                <input type="month" name="month" id="att-month" value="{{ $selectedMonth }}" onchange="document.getElementById('att-filter-form').submit()">
            </div>
            @endif

            @if($mode === 'yearly')
            <div class="att-filter-group">
                <label for="att-year"><i class="bi bi-calendar-range" aria-hidden="true"></i> Year</label>
                <select name="year" id="att-year" onchange="document.getElementById('att-filter-form').submit()">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            @endif

            <button type="submit" class="att-filter-btn">
                <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                Apply
            </button>
        </form>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="att-success-alert">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============================== DAILY MODE ============================== --}}
    @if($mode === 'daily')
        @php
            $dateCarbon = \Carbon\Carbon::parse($selectedDate);
            $isWeekend = $dateCarbon->isSaturday() || $dateCarbon->isSunday();
        @endphp

        {{-- DAILY SUMMARY CARDS --}}
        <div class="att-daily-summary-cards">
            <div class="att-stat-card att-stat-total">
                <div class="att-stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $dailySummary['total'] }}</span>
                    <span class="att-stat-label">Total Students</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-present">
                <div class="att-stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $dailySummary['present'] }}</span>
                    <span class="att-stat-label">Present</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-absent">
                <div class="att-stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $dailySummary['absent'] }}</span>
                    <span class="att-stat-label">Absent</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-leave">
                <div class="att-stat-icon"><i class="bi bi-clock-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $dailySummary['leave'] }}</span>
                    <span class="att-stat-label">On Leave</span>
                </div>
            </div>
        </div>

        @if($isWeekend)
            <div class="att-weekend-banner">
                <i class="bi bi-sun-fill" aria-hidden="true"></i>
                <div>
                    <strong>Weekend — {{ $dateCarbon->format('l') }}</strong>
                    <p>{{ $dateCarbon->format('d M, Y') }} is a weekend day. Attendance cannot be marked on Saturdays & Sundays.</p>
                </div>
            </div>
        @else
            {{-- DAILY ATTENDANCE FORM --}}
            <form method="POST" action="{{ route('attendance.store') }}" class="att-daily-form">
                @csrf
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <input type="hidden" name="class" value="{{ $selectedClass }}">
                <input type="hidden" name="section" value="{{ $selectedSection }}">

                <div class="att-table-header-bar">
                    <h3 class="att-table-title">
                        <i class="bi bi-list-check" aria-hidden="true"></i>
                        Mark Attendance — {{ $dateCarbon->format('l, d M Y') }}
                    </h3>
                    @if($currentUser->hasPermission('attendance.create'))
                    <div class="att-quick-actions">
                        <button type="button" class="att-quick-btn att-quick-all-present" onclick="setAllStatus('present')">
                            <i class="bi bi-check-all"></i> All Present
                        </button>
                        @if($currentUser->hasPermission('attendance.create'))
                        <button type="button" class="att-quick-btn att-quick-all-absent" onclick="setAllStatus('absent')">
                            <i class="bi bi-x-lg"></i> All Absent
                        </button>
                        @endif
                    </div>
                    @endif
                </div>

                @if($students->count() > 0)
                    <div class="att-table-wrap">
                        <table class="att-daily-table">
                            <thead>
                                <tr>
                                    <th class="att-th-num">#</th>
                                    <th class="att-th-id">Roll No</th>
                                    <th class="att-th-name">Student Name</th>
                                    <th class="att-th-status">Status</th>
                                    <th class="att-th-remarks">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    @php
                                        $rec = $dailyRecords[$student->id] ?? ['status' => null, 'remarks' => null];
                                        $currentStatus = $rec['status'] ?? 'present';
                                    @endphp
                                    <tr class="att-student-row" data-student-id="{{ $student->id }}">
                                        <td class="att-td-num">{{ $index + 1 }}</td>
                                        <td class="att-td-id">
                                            <span class="att-roll-chip">#{{ $student->std_id }}</span>
                                        </td>
                                        <td class="att-td-name">
                                            <div class="att-student-name-wrap">
                                                <img src="{{ $student->image ? asset('storage/'.$student->image) : asset('upload/DPF.jpeg') }}" alt="" class="att-student-thumb">
                                                <div>
                                                    <strong>{{ $student->name }}</strong>
                                                    <small>{{ $student->class }} · {{ $student->section }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="att-td-status">
                                            <div class="att-status-toggle" data-student="{{ $student->id }}">
                                                <label class="att-status-option att-opt-present {{ $currentStatus === 'present' ? 'selected' : '' }}">
                                                    <input type="radio" name="attendance[{{ $student->id }}][status]" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }} @disabled(!$currentUser->hasPermission('attendance.create'))>
                                                    <span><i class="bi bi-check-lg"></i> P</span>
                                                </label>
                                                <label class="att-status-option att-opt-absent {{ $currentStatus === 'absent' ? 'selected' : '' }}">
                                                    <input type="radio" name="attendance[{{ $student->id }}][status]" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }} @disabled(!$currentUser->hasPermission('attendance.create'))>
                                                    <span><i class="bi bi-x-lg"></i> A</span>
                                                </label>
                                                <label class="att-status-option att-opt-leave {{ $currentStatus === 'leave' ? 'selected' : '' }}">
                                                    <input type="radio" name="attendance[{{ $student->id }}][status]" value="leave" {{ $currentStatus === 'leave' ? 'checked' : '' }} @disabled(!$currentUser->hasPermission('attendance.create'))>
                                                    <span><i class="bi bi-clock"></i> L</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="att-td-remarks">
                                            <input type="text" name="attendance[{{ $student->id }}][remarks]" value="{{ $rec['remarks'] ?? '' }}" class="att-remarks-input" placeholder="Optional note..." @disabled(!$currentUser->hasPermission('attendance.create'))>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="att-form-footer">
                        @if($currentUser->hasPermission('attendance.create'))
                        <button type="submit" class="att-save-btn">
                            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                            Save Attendance
                        </button>
                        @endif
                    </div>

                    {{-- PAGINATION --}}
                    @if($students->total() > 0)
                        <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
                            <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                            </p>

                            @if($students->hasPages())
                                <div class="student-pagination">
                                    {{ $students->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="att-empty-box">
                        <i class="bi bi-person-x-fill" aria-hidden="true"></i>
                        <p>No students found for <strong>{{ $selectedClass }} — {{ $selectedSection }}</strong>. Try a different class or section filter.</p>
                    </div>
                @endif
            </form>
        @endif
    @endif

    {{-- ============================== MONTHLY MODE ============================== --}}
    @if($mode === 'monthly')
        @php
            $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y');
        @endphp

        {{-- MONTHLY CLASS STATS CARDS --}}
        <div class="att-daily-summary-cards">
            <div class="att-stat-card att-stat-total">
                <div class="att-stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $students->count() }}</span>
                    <span class="att-stat-label">Students</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-present">
                <div class="att-stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $monthlyClassStats['present'] }}</span>
                    <span class="att-stat-label">Total Present</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-absent">
                <div class="att-stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $monthlyClassStats['absent'] }}</span>
                    <span class="att-stat-label">Total Absent</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-leave">
                <div class="att-stat-icon"><i class="bi bi-percent"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $monthlyClassStats['percentage'] }}%</span>
                    <span class="att-stat-label">Class Avg</span>
                </div>
            </div>
        </div>

        <div class="att-table-header-bar">
            <h3 class="att-table-title">
                <i class="bi bi-table" aria-hidden="true"></i>
                Monthly Attendance Register — {{ $monthLabel }}
            </h3>
            <span class="att-badge-class">{{ $selectedClass }} · {{ $selectedSection }}</span>
        </div>

        @if($students->count() > 0)
            <div class="att-monthly-table-wrap">
                <table class="att-monthly-table">
                    <thead>
                        <tr>
                            <th class="att-mth-sticky att-mth-num">#</th>
                            <th class="att-mth-sticky att-mth-name">Student</th>
                            @foreach($monthlyDays as $dayInfo)
                                <th class="att-mth-day {{ $dayInfo['is_weekend'] ? 'att-mth-weekend' : '' }}">
                                    <span class="att-mth-day-num">{{ $dayInfo['day'] }}</span>
                                    <span class="att-mth-day-name">{{ $dayInfo['day_name'] }}</span>
                                </th>
                            @endforeach
                            <th class="att-mth-summary-th">P</th>
                            <th class="att-mth-summary-th">A</th>
                            <th class="att-mth-summary-th">L</th>
                            <th class="att-mth-summary-th att-mth-pct-th">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyStudentsData as $mIdx => $mStudent)
                            <tr>
                                <td class="att-mth-sticky att-mth-num">{{ $mIdx + 1 }}</td>
                                <td class="att-mth-sticky att-mth-name">
                                    <span class="att-mth-student-name" title="{{ $mStudent['student']->name }}">
                                        {{ $mStudent['student']->name }}
                                    </span>
                                    <small class="att-mth-roll">#{{ $mStudent['student']->std_id }}</small>
                                </td>
                                @foreach($monthlyDays as $dayInfo)
                                    @php
                                        $dayAtt = $mStudent['days'][$dayInfo['day']] ?? ['status' => null];
                                        $st = $dayAtt['status'];
                                    @endphp
                                    <td class="att-mth-cell {{ $dayInfo['is_weekend'] ? 'att-mth-weekend' : '' }} {{ $st ? 'att-mth-status-'.$st : 'att-mth-unmarked' }}">
                                        @if($dayInfo['is_weekend'])
                                            <span class="att-mth-wk-icon">—</span>
                                        @elseif($st === 'present')
                                            <span class="att-mth-mark att-mark-p" title="Present">P</span>
                                        @elseif($st === 'absent')
                                            <span class="att-mth-mark att-mark-a" title="Absent">A</span>
                                        @elseif($st === 'leave')
                                            <span class="att-mth-mark att-mark-l" title="Leave">L</span>
                                        @else
                                            <span class="att-mth-mark att-mark-none">·</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="att-mth-summary att-mth-p-count">{{ $mStudent['present'] }}</td>
                                <td class="att-mth-summary att-mth-a-count">{{ $mStudent['absent'] }}</td>
                                <td class="att-mth-summary att-mth-l-count">{{ $mStudent['leave'] }}</td>
                                <td class="att-mth-summary att-mth-pct {{ $mStudent['percentage'] >= 75 ? 'att-pct-good' : ($mStudent['percentage'] >= 50 ? 'att-pct-warning' : 'att-pct-danger') }}">
                                    {{ $mStudent['percentage'] }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="att-monthly-legend">
                <span class="att-legend-item"><span class="att-legend-dot att-legend-present"></span> Present</span>
                <span class="att-legend-item"><span class="att-legend-dot att-legend-absent"></span> Absent</span>
                <span class="att-legend-item"><span class="att-legend-dot att-legend-leave"></span> Leave</span>
                <span class="att-legend-item"><span class="att-legend-dot att-legend-weekend"></span> Weekend (Off)</span>
                <span class="att-legend-item"><span class="att-legend-dot att-legend-unmarked"></span> Not Marked</span>
            </div>

            {{-- PAGINATION --}}
            @if($students->total() > 0)
                <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
                    <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                    </p>

                    @if($students->hasPages())
                        <div class="student-pagination">
                            {{ $students->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="att-empty-box">
                <i class="bi bi-person-x-fill" aria-hidden="true"></i>
                <p>No students found for <strong>{{ $selectedClass }} — {{ $selectedSection }}</strong>.</p>
            </div>
        @endif
    @endif

    {{-- ============================== YEARLY MODE ============================== --}}
    @if($mode === 'yearly')
        {{-- YEARLY CLASS STATS --}}
        <div class="att-daily-summary-cards">
            <div class="att-stat-card att-stat-total">
                <div class="att-stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $students->count() }}</span>
                    <span class="att-stat-label">Students</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-present">
                <div class="att-stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $yearlyClassStats['present'] }}</span>
                    <span class="att-stat-label">Total Present</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-absent">
                <div class="att-stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $yearlyClassStats['absent'] }}</span>
                    <span class="att-stat-label">Total Absent</span>
                </div>
            </div>
            <div class="att-stat-card att-stat-leave">
                <div class="att-stat-icon"><i class="bi bi-percent"></i></div>
                <div class="att-stat-info">
                    <span class="att-stat-num">{{ $yearlyClassStats['percentage'] }}%</span>
                    <span class="att-stat-label">Annual Avg</span>
                </div>
            </div>
        </div>

        <div class="att-table-header-bar">
            <h3 class="att-table-title">
                <i class="bi bi-calendar-range" aria-hidden="true"></i>
                Yearly Attendance Overview — {{ $selectedYear }}
            </h3>
            <span class="att-badge-class">{{ $selectedClass }} · {{ $selectedSection }}</span>
        </div>

        @if($students->count() > 0)
            <div class="att-monthly-table-wrap">
                <table class="att-yearly-table">
                    <thead>
                        <tr>
                            <th class="att-mth-sticky att-mth-num">#</th>
                            <th class="att-mth-sticky att-mth-name">Student</th>
                            @foreach($yearlyMonths as $mNum => $mInfo)
                                <th class="att-yr-month-th">{{ $mInfo['name'] }}</th>
                            @endforeach
                            <th class="att-mth-summary-th">P</th>
                            <th class="att-mth-summary-th">A</th>
                            <th class="att-mth-summary-th">L</th>
                            <th class="att-mth-summary-th att-mth-pct-th">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($yearlyStudentsData as $yIdx => $yStudent)
                            <tr>
                                <td class="att-mth-sticky att-mth-num">{{ $yIdx + 1 }}</td>
                                <td class="att-mth-sticky att-mth-name">
                                    <span class="att-mth-student-name">{{ $yStudent['student']->name }}</span>
                                    <small class="att-mth-roll">#{{ $yStudent['student']->std_id }}</small>
                                </td>
                                @foreach($yearlyMonths as $mNum => $mInfo)
                                    @php $mData = $yStudent['months'][$mNum]; @endphp
                                    <td class="att-yr-cell">
                                        @if($mData['total'] > 0)
                                            <div class="att-yr-cell-data {{ $mData['percentage'] >= 75 ? 'att-yr-good' : ($mData['percentage'] >= 50 ? 'att-yr-warning' : 'att-yr-danger') }}">
                                                <span class="att-yr-pct">{{ $mData['percentage'] }}%</span>
                                                <small class="att-yr-breakdown">{{ $mData['present'] }}P · {{ $mData['absent'] }}A</small>
                                            </div>
                                        @else
                                            <span class="att-yr-no-data">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="att-mth-summary att-mth-p-count">{{ $yStudent['present'] }}</td>
                                <td class="att-mth-summary att-mth-a-count">{{ $yStudent['absent'] }}</td>
                                <td class="att-mth-summary att-mth-l-count">{{ $yStudent['leave'] }}</td>
                                <td class="att-mth-summary att-mth-pct {{ $yStudent['percentage'] >= 75 ? 'att-pct-good' : ($yStudent['percentage'] >= 50 ? 'att-pct-warning' : 'att-pct-danger') }}">
                                    {{ $yStudent['percentage'] }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($students->total() > 0)
                <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
                    <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                    </p>

                    @if($students->hasPages())
                        <div class="student-pagination">
                            {{ $students->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="att-empty-box">
                <i class="bi bi-person-x-fill" aria-hidden="true"></i>
                <p>No students found for <strong>{{ $selectedClass }} — {{ $selectedSection }}</strong>.</p>
            </div>
        @endif
    @endif

</div>

<script>
// Daily attendance status toggle UI
document.querySelectorAll('.att-status-toggle').forEach(group => {
    group.querySelectorAll('.att-status-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            group.querySelectorAll('.att-status-option').forEach(opt => opt.classList.remove('selected'));
            this.closest('.att-status-option').classList.add('selected');

            // Update row visual
            const row = this.closest('.att-student-row');
            row.classList.remove('att-row-present', 'att-row-absent', 'att-row-leave');
            row.classList.add('att-row-' + this.value);
        });
    });
});

// Quick mark all
function setAllStatus(status) {
    document.querySelectorAll('.att-status-toggle').forEach(group => {
        const radio = group.querySelector(`input[value="${status}"]`);
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
}

// Custom Downward Dropdown Logic
function initCustomDropdowns() {
    const dropdowns = document.querySelectorAll('.att-custom-dropdown');
    
    dropdowns.forEach(function (dropdown) {
        const trigger = dropdown.querySelector('.att-custom-dropdown-btn');
        const items = dropdown.querySelectorAll('.att-custom-dropdown-item');
        const group = dropdown.closest('.att-filter-group');
        const hiddenInput = group ? group.querySelector('input[type="hidden"]') : null;
        const form = document.getElementById('att-filter-form');
        
        if (trigger) {
            trigger.onclick = function (e) {
                e.stopPropagation();
                dropdowns.forEach(d => { if (d !== dropdown) d.classList.remove('open'); });
                dropdown.classList.toggle('open');
            };
        }

        items.forEach(function (item) {
            item.onclick = function (e) {
                e.stopPropagation();
                const val = item.getAttribute('data-value');
                if (hiddenInput) {
                    hiddenInput.value = val;
                }
                dropdown.classList.remove('open');
                if (form) {
                    form.submit();
                }
            };
        });
    });

    document.onclick = function () {
        dropdowns.forEach(d => d.classList.remove('open'));
    };
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCustomDropdowns);
} else {
    initCustomDropdowns();
}
</script>
@endsection
