@extends('layout')

@push('page-css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="dashboard-shell">
    <header class="dashboard-heading">
        <div>
            <p class="dashboard-eyebrow">SCHOOL OVERVIEW <span></span> {{ now()->format('l, F j, Y') }}</p>
            <h1>{{ $greeting }}, {{ session('user_name', 'there') }}.</h1>
            <p class="dashboard-subtitle">Your campus, in one clear view.</p>
        </div>
        <div class="dashboard-heading-actions">
            <a class="dashboard-outline-action" href="/attendance"><i class="bi bi-calendar2-check" aria-hidden="true"></i> Take attendance</a>
            <a class="dashboard-primary-action" href="/add-student"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add student</a>
        </div>
    </header>

    <section class="dashboard-welcome" aria-label="Today's school overview">
        <div class="dashboard-welcome-copy">
            <p class="dashboard-welcome-kicker"><i class="bi bi-sun" aria-hidden="true"></i> TODAY AT A GLANCE</p>
            <h2>A good day to make progress.</h2>
            <p>
                @if($inchargeLabel)
                    You are responsible for <strong>{{ $inchargeLabel }}</strong> with <strong>{{ number_format($totalStudents) }} students</strong>. Keep attendance and class records up to date.
                @elseif($canViewStudents)
                    There are <strong>{{ number_format($totalStudents) }} students</strong> across your school. Keep the day moving with attendance, records, and class updates.
                @else
                    Your school workspace is ready. Open the areas assigned to your role to continue.
                @endif
            </p>
            @if($isAdmin || $canViewStudents)
                <a href="{{ $isAdmin ? '/classes' : '/list' }}" class="dashboard-welcome-link">{{ $isAdmin ? 'Explore classes' : 'View students' }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            @endif
        </div>
        <div class="dashboard-welcome-art" aria-hidden="true">
            <div class="dashboard-orbit orbit-one"></div>
            <div class="dashboard-orbit orbit-two"></div>
            <div class="dashboard-art-core"><img src="{{ asset('upload/school_campus.jpg') }}" alt=""></div>
            <span class="dashboard-art-note note-one"><i class="bi bi-check2-circle"></i> Learn</span>
            <span class="dashboard-art-note note-two"><i class="bi bi-stars"></i> Grow</span>
        </div>
    </section>

    <section class="dashboard-stat-grid {{ $isAdmin ? '' : 'dashboard-restricted-stats' }}" aria-label="School statistics">
        @if($canViewStudents)
            <a href="/list" class="dashboard-stat-card">
                <span class="dashboard-stat-icon green"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></span>
                <span class="dashboard-stat-label">{{ $inchargeLabel ? 'My students' : 'Total students' }}</span>
                <strong>{{ number_format($totalStudents) }}</strong>
                <span class="dashboard-stat-foot">View student directory <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
            </a>
        @endif
        @if($canViewTeachers)
            <a href="/teachers" class="dashboard-stat-card">
                <span class="dashboard-stat-icon blue"><i class="bi bi-person-badge-fill" aria-hidden="true"></i></span>
                <span class="dashboard-stat-label">Teaching staff</span>
                <strong>{{ number_format($totalTeachers) }}</strong>
                <span class="dashboard-stat-foot">Meet the team <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
            </a>
        @endif
        @if($canViewAttendance)
            <a href="/attendance" class="dashboard-stat-card">
                <span class="dashboard-stat-icon coral"><i class="bi bi-calendar2-check-fill" aria-hidden="true"></i></span>
                <span class="dashboard-stat-label">Present today</span>
                <strong>{{ number_format($presentToday) }}<small>{{ $attendanceMarked > 0 ? ' / '.number_format($attendanceMarked) : '' }}</small></strong>
                <span class="dashboard-stat-foot">{{ $attendanceMarked > 0 ? $attendanceRate.'% of marked attendance' : 'Mark today’s attendance' }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
            </a>
        @endif
        @if($canViewFees)
            <a href="/fee-structure" class="dashboard-stat-card">
                <span class="dashboard-stat-icon gold"><i class="bi bi-cash-stack" aria-hidden="true"></i></span>
                <span class="dashboard-stat-label">Fees collected</span>
                <strong class="dashboard-currency">Rs {{ number_format($collectedFees, 0) }}</strong>
                <span class="dashboard-stat-foot">Rs {{ number_format($outstandingFees, 0) }} outstanding <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
            </a>
        @elseif($canViewResults)
            <a href="/results" class="dashboard-stat-card">
                <span class="dashboard-stat-icon gold"><i class="bi bi-clipboard2-data-fill" aria-hidden="true"></i></span>
                <span class="dashboard-stat-label">Class result records</span>
                <strong>{{ number_format($totalResults) }}</strong>
                <span class="dashboard-stat-foot">Review class results <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
            </a>
        @endif
    </section>

    <section class="dashboard-analytics {{ !$isAdmin ? 'dashboard-restricted-analytics' : '' }}" aria-label="School analytics">
        @if($canViewAttendance)
        <article class="dashboard-panel dashboard-attendance-panel">
            <div class="dashboard-panel-heading">
                <div>
                    <p class="dashboard-panel-kicker">WEEKLY PULSE</p>
                    <h2>Attendance trend</h2>
                </div>
                <a href="/attendance" class="dashboard-panel-link" aria-label="Open attendance"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div class="dashboard-chart-wrap dashboard-chart-tall">
                <canvas id="attendance-trend-chart" aria-label="Present and absent attendance records this week" role="img"></canvas>
                @if(array_sum($weeklyPresent) + array_sum($weeklyAbsent) === 0)
                    <span class="dashboard-chart-empty">Attendance records will appear here.</span>
                @endif
            </div>
            <div class="dashboard-chart-legend"><span><i class="legend-present"></i> Present</span><span><i class="legend-absent"></i> Absent</span></div>
        </article>
        @endif

        @if($canViewFees)
        <article class="dashboard-panel">
            <div class="dashboard-panel-heading">
                <div>
                    <p class="dashboard-panel-kicker">FEE HEALTH</p>
                    <h2>Collection status</h2>
                </div>
                <a href="/fee-structure" class="dashboard-panel-link" aria-label="Open fee management"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div class="dashboard-chart-wrap dashboard-chart-donut">
                <canvas id="fee-status-chart" aria-label="Paid, pending, and overdue fee vouchers" role="img"></canvas>
                @if(array_sum($feeStatusCounts) === 0)
                    <span class="dashboard-chart-empty">Fee records will appear here.</span>
                @endif
            </div>
            <div class="dashboard-chart-legend"><span><i class="legend-paid"></i> Paid</span><span><i class="legend-pending"></i> Pending</span><span><i class="legend-overdue"></i> Overdue</span></div>
        </article>
        @endif

        @if($canViewClasses && $canViewStudents)
        <article class="dashboard-panel dashboard-class-panel">
            <div class="dashboard-panel-heading">
                <div>
                    <p class="dashboard-panel-kicker">ENROLMENT</p>
                    <h2>Students by class</h2>
                </div>
                <a href="/classes" class="dashboard-panel-link" aria-label="Open classes"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div class="dashboard-chart-wrap dashboard-chart-classes">
                <canvas id="class-enrollment-chart" aria-label="Student enrollment across the largest classes" role="img"></canvas>
                @if(array_sum($classTotals) === 0)
                    <span class="dashboard-chart-empty">Class enrolment will appear here.</span>
                @endif
            </div>
        </article>
        @endif
    </section>

    <section class="dashboard-modules-section">
        <div class="dashboard-section-heading">
            <div>
                <p class="dashboard-panel-kicker">YOUR WORKSPACE</p>
                <h2>School departments</h2>
            </div>
            <span>{{ count($moduleCards) }} areas</span>
        </div>
        <div class="dashboard-module-grid">
            @foreach($moduleCards as $card)
                <a href="{{ $card['href'] }}" class="dashboard-module-card">
                    <span class="dashboard-module-icon {{ $card['tone'] }}"><i class="bi {{ $card['icon'] }}" aria-hidden="true"></i></span>
                    <span class="dashboard-module-copy"><strong>{{ $card['title'] }}</strong><small>{{ $card['detail'] }}</small></span>
                    <i class="bi bi-arrow-up-right dashboard-module-arrow" aria-hidden="true"></i>
                </a>
            @endforeach
        </div>
    </section>
</div>

<script type="application/json" id="dashboard-chart-data">@json($chartData)</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js" defer></script>
<script src="{{ asset('js/dashboard.js') }}?v={{ time() }}" defer></script>
@endsection