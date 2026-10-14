@extends('admin.layout')

@section('content')
    {{-- EXECUTIVE HERO BANNER WITH EMBEDDED TAB PILLS (Matching Student Portal Hero) --}}
    <section class="student-hero admin-hero">
        <div>
            <span class="hero-pill"><i></i> Excellence Admin Portal &bull; System Active</span>
            <h1>Admin & Head Management Authority Portal</h1>
            <p>Welcome back, <strong>{{ session('user_name', 'Abdul Moeez') }}</strong>! Full authority and control over leaves, teacher salaries, student fees, attendance, academic records, and system accounts.</p>
        </div>

        <div class="hero-tabs">
            @php
            $tabMenu = [
                'dashboard' => ['Dashboard', 'bi-grid-1x2-fill'],
                'leaves' => ['Leaves Approval', 'bi-calendar2-check-fill', $pendingLeavesCount ?? 0],
                'salaries' => ['Teacher Salaries', 'bi-cash-stack', 0],
                'students' => ['Students Directory', 'bi-mortarboard-fill', 0],
                'teachers' => ['Teachers Directory', 'bi-person-badge-fill', 0],
                'fees' => ['Fees Management', 'bi-receipt', 0],
                'attendance' => ['Attendance Logs', 'bi-clock-history', 0],
                'academics' => ['Academics & Operations', 'bi-journal-bookmark-fill', 0],
                'roles' => ['Roles & Incharges', 'bi-shield-lock-fill', $pendingAccessRequestsCount ?? 0],
                'users' => ['System Accounts', 'bi-people-fill', 0],
            ];
            @endphp

            @foreach($tabMenu as $key => $item)
                <a href="{{ route('admin.page', $key) }}" class="{{ ($currentPage ?? 'dashboard') === $key ? 'selected' : '' }}">
                    <i class="bi {{ $item[1] }}"></i> {{ $item[0] }}
                    @if(!empty($item[2]) && $item[2] > 0)
                        <span style="background: #EF4444; color: #fff; font-size: 10px; font-weight: 800; padding: 1px 6px; border-radius: 99px; margin-left: 4px;">{{ $item[2] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    {{-- PAGE CONTENT RENDERER --}}
    @if(view()->exists("admin.pages.{$currentPage}"))
        @include("admin.pages.{$currentPage}")
    @else
        <div class="admin-card">
            <div class="admin-empty-state">
                <i class="bi bi-exclamation-octagon admin-empty-icon"></i>
                <h4>Page standard module not found</h4>
                <p>The requested view page module <code>admin.pages.{{ $currentPage }}</code> is currently loading.</p>
                <a href="{{ route('admin.page', 'dashboard') }}" class="btn btn-primary" style="border-radius: var(--radius-md);">Return to Dashboard</a>
            </div>
        </div>
    @endif
@endsection
