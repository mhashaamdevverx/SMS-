<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Portal | Excellence School System</title>
    
    <!-- Google Fonts: Plus Jakarta Sans for Headings, Inter for Body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Fonts & Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/student-portal.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/teacher-portal.css') }}?v={{ time() }}">
    
    <!-- Custom Theme & Interactive Logic -->
    <script src="{{ asset('js/student-portal.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/common/notifications.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/teacher/teacher-portal-page.js') }}?v={{ time() }}" defer></script>
</head>
<body class="student-shell">
    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    <!-- REDESIGNED SIDEBAR (Deep Teal Gradient + Pattern Overlay) -->
    <aside class="student-side teacher-side">
        <!-- Top Brand Badge -->
        <a class="student-brand" href="{{ route('teacher.dashboard') }}">
            <div class="brand-badge" style="background: linear-gradient(135deg, #4F46E5, #14B8A6);">ESS</div>
            <div class="brand-text">
                <b>Excellence School System</b>
                <small class="brand-subtitle" style="color: #14B8A6;">Teacher Portal</small>
            </div>
        </a>

        <!-- Session Glassmorphism Pill -->
        <div class="student-term">
            <i class="bi bi-circle-fill" style="color: #14B8A6;"></i>
            <span>Current Session</span>
            <b>{{ now()->format('Y') }}</b>
        </div>

        <!-- Navigation Categories & Links -->
        <nav>
            <p class="student-menu-label">ACADEMICS & STUDENTS</p>
            @php
            $academicMenu = [
                'dashboard' => ['Dashboard', 'bi-grid-1x2-fill'],
                'profile' => ['My Profile', 'bi-person-badge-fill'],
                'attendance' => ['My Attendance', 'bi-calendar-check-fill'],
                'leaves' => ['My Leaves / Apply Leave', 'bi-calendar-plus-fill'],
                'coteachers' => ['Class Co-Teachers', 'bi-people-fill'],
                'students' => ['My Class Students', 'bi-backpack-fill'],
            ];
            @endphp
            @foreach($academicMenu as $key => $item)
                <a href="{{ $key === 'dashboard' ? route('teacher.dashboard') : route('teacher.page', $key) }}" class="{{ ($page ?? 'dashboard') === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item[1] }}"></i>
                    <span>{{ $item[0] }}</span>
                </a>
            @endforeach

            <p class="student-menu-label" style="margin-top: 14px;">TESTS & EVALUATION</p>
            @php
            $evalMenu = [
                'stdleaves' => ['Student Leaves', 'bi-envelope-paper-fill'],
                'results' => ['Class Results', 'bi-bar-chart-line-fill'],
                'tests' => ['Monthly Test Sessions', 'bi-journal-check'],
                'slips' => ['Roll No Slips', 'bi-file-earmark-pdf-fill'],
                'outlines' => ['Course Outlines', 'bi-journal-bookmark-fill'],
            ];
            @endphp
            @foreach($evalMenu as $key => $item)
                <a href="{{ route('teacher.page', $key) }}" class="{{ ($page ?? 'dashboard') === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item[1] }}"></i>
                    <span>{{ $item[0] }}</span>
                </a>
            @endforeach

            <p class="student-menu-label" style="margin-top: 14px;">ACCOUNT & FINANCE</p>
            <a href="{{ route('teacher.page', 'salary') }}" class="{{ ($page ?? 'dashboard') === 'salary' ? 'is-active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>My Salary & Dues</span>
            </a>
        </nav>

        <!-- Bottom Teacher Mini-Card & Desktop Collapse Trigger -->
        <div class="sidebar-bottom">
            <div class="student-mini-card">
                <div class="student-mini-avatar" style="overflow: hidden; background: transparent;">
                    @php
                        $tSideImg = !empty($teacher->teacher_image) && $teacher->teacher_image !== 'dummy.jpeg'
                            ? asset('storage/'.$teacher->teacher_image)
                            : asset('upload/dummy.jpeg');
                    @endphp
                    <img src="{{ $tSideImg }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="{{ $teacher->teacher_name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                </div>
                <div class="student-user-info">
                    <b>{{ $teacher->teacher_name }}</b>
                    @if(!empty($isClassIncharge) || ($user->role?->slug ?? '') === 'class-incharge')
                        <small style="color:#a5b4fc;font-weight:800;">👑 Incharge &bull; {{ $primaryClass ?? 'Class' }} ({{ $primarySection ?? 'Red' }})</small>
                    @else
                        <small>Faculty &bull; {{ $primaryClass ?? 'Class' }}</small>
                    @endif
                </div>
            </div>

            <div class="sidebar-controls">
                <button type="button" id="sidebarCollapseBtn" class="btn-sidebar-control" title="Toggle Sidebar Width">
                    <i class="bi bi-arrow-left-right"></i>
                    <span class="collapse-text">Collapse</span>
                </button>
                <form action="/logout" method="POST" style="margin:0; flex:1;">
                    @csrf
                    <button type="submit" class="btn-sidebar-control" style="width:100%; color:#FF7A59; border-color:rgba(255,122,89,0.3);" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="collapse-text">Exit</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN PORTAL WRAPPER & STICKY GLASSMORPHISM NAVBAR -->
    <main class="student-main teacher-main">
        <header class="student-top teacher-top">
            <!-- Left Group: Mobile Drawer Toggle & Breadcrumb -->
            <div class="nav-left-group">
                <button type="button" class="nav-icon-btn mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Mobile Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="portal-breadcrumb">
                    <span>Teacher Portal</span> /
                    <strong>{{ ucfirst($page ?? 'Dashboard') }}</strong>
                </div>
            </div>

            <!-- Center Group: Command-Style Search Bar -->
            <div class="nav-search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="globalSearchInput" placeholder="Search students, results, classes...">
            </div>

            <!-- Right Actions: Theme Toggle, Notifications, Profile Menu -->
            <div class="nav-right-actions">
                @if(!empty($isClassIncharge) || ($user->role?->slug ?? '') === 'class-incharge')
                <!-- In-Charge Teacher Announcement Button -->
                <button type="button" class="nav-icon-btn" onclick="openAnnouncementModal()" title="Broadcast Class Announcement">
                    <i class="bi bi-megaphone-fill" style="color: var(--primary);"></i>
                </button>
                @endif

                <!-- Light / Dark Theme Switcher Button -->
                <button type="button" class="nav-icon-btn" id="themeToggleBtn" title="Switch Theme">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>

                <!-- Notification Bell Component -->
                <div class="notification-wrapper" style="position: relative;">
                    <button type="button" class="nav-icon-btn" id="notifBellBtn" title="Notifications" style="position: relative;">
                        <i class="bi bi-bell-fill"></i>
                        <span id="notifBadge" style="display: none; position: absolute; top: -4px; right: -4px; background: #EF4444; color: #ffffff; font-size: 10px; font-weight: 900; padding: 1px 5px; border-radius: 99px; border: 2px solid var(--surface); box-shadow: 0 2px 4px rgba(239, 68, 68, 0.4);">0</span>
                    </button>

                    <!-- Dropdown Container -->
                    <div id="notifDropdown" style="display: none; position: absolute; top: calc(100% + 12px); right: 0; width: 360px; max-width: 90vw; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); z-index: 999999; overflow: hidden;">
                        <div style="padding: 14px 18px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-bell-fill" style="color: var(--primary); font-size: 16px;"></i>
                                <strong style="font-size: 14px; color: var(--text);">Notifications</strong>
                            </div>
                            <button type="button" onclick="markAllNotificationsAsRead()" style="background: transparent; border: none; color: var(--primary); font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: underline;">
                                Mark all as read
                            </button>
                        </div>

                        <div id="notifListContainer" style="max-height: 360px; overflow-y: auto; padding: 6px 0;">
                            <div style="text-align: center; padding: 24px; color: var(--muted); font-size: 13px;">
                                <i class="bi bi-hourglass-split" style="font-size: 20px; display: block; margin-bottom: 6px;"></i> Loading updates...
                            </div>
                        </div>

                        <div style="padding: 10px 16px; background: var(--bg); border-top: 1px solid var(--border); text-align: center;">
                            <span style="font-size: 11.5px; color: var(--muted); font-weight: 600;">Excellence Notification System</span>
                        </div>
                    </div>
                </div>

                <!-- User Profile Pill & Dropdown -->
                <div class="student-user-pill" id="topProfileUser" style="position: relative !important;">
                    <div class="student-avatar-ring">
                        @php
                            $tTopImg = !empty($teacher->teacher_image) && $teacher->teacher_image !== 'dummy.jpeg'
                                ? asset('storage/'.$teacher->teacher_image)
                                : asset('upload/dummy.jpeg');
                        @endphp
                        <img src="{{ $tTopImg }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="{{ $teacher->teacher_name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    </div>
                    <div class="student-user-details">
                        <b>{{ $teacher->teacher_name }}</b>
                        @if(!empty($isClassIncharge) || ($user->role?->slug ?? '') === 'class-incharge')
                            <small style="color:#4338ca;font-weight:800;">👑 Incharge &bull; {{ $primaryClass ?? 'TEN' }} ({{ $primarySection ?? 'Red' }})</small>
                        @else
                            <small>{{ $primaryClass ?? 'Pre-KG' }} ({{ $primarySection ?? 'Red' }})</small>
                        @endif
                    </div>
                    <i class="bi bi-chevron-down" id="topProfileArrow" style="font-size: 11px; color: var(--muted); margin-left: 2px;"></i>

                    <!-- Profile Dropdown Menu (Aligned perfectly between pill lines) -->
                    <div id="userDropdownMenu" style="display: none; position: absolute; top: calc(100% + 8px); left: 0; right: 0; width: 100%; min-width: 170px; box-sizing: border-box; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 9999; padding: 6px 0; overflow: hidden;">
                        <div style="padding: 10px 14px; border-bottom: 1px solid var(--border-light); overflow: hidden;">
                            <strong style="display:block; font-size: 13px; color: var(--text); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ $teacher->teacher_name }}</strong>
                            <small style="font-size: 11px; color: var(--primary); font-weight: 700; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; display: block;">{{ $user->email }}</small>
                            @if(!empty($isClassIncharge) || ($user->role?->slug ?? '') === 'class-incharge')
                                <span style="display:inline-block;background:#e0e7ff;color:#4338ca;font-size:10.5px;font-weight:800;padding:2px 8px;border-radius:6px;margin-top:4px;">👑 Class Incharge Teacher ({{ $primaryClass ?? 'TEN' }} {{ $primarySection ?? 'Red' }})</span>
                            @else
                                <span style="display:inline-block;background:#f1f5f9;color:#64748b;font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:6px;margin-top:4px;">Teacher &bull; Faculty</span>
                            @endif
                        </div>
                        <a href="{{ route('teacher.page', 'profile') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); text-decoration: none; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-person-circle" style="color: var(--primary);"></i> Profile Settings
                        </a>
                        <a href="{{ route('teacher.page', 'salary') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); text-decoration: none; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-cash-stack" style="color: var(--primary);"></i> My Pay Slips
                        </a>
                        <a href="{{ route('teacher.page', 'leaves') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); text-decoration: none; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-calendar-plus" style="color: var(--primary);"></i> My Leaves
                        </a>
                        <div style="border-top: 1px solid var(--border-light); margin-top: 4px; padding-top: 4px;">
                            <form action="/logout" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" style="width:100%; display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--secondary); background: transparent; border: none; font-size: 13px; font-weight: 700; cursor: pointer; text-align: left;">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT SLOTS -->
        <div class="student-content teacher-content">
            @yield('content')
        </div>

        <!-- REDESIGNED MULTI-COLUMN SURFACE FOOTER -->
        <footer class="main-system-footer">
            <div class="footer-grid-container">
                <!-- Col 1: Brand & Status -->
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <div class="brand-badge" style="width:34px; height:34px; font-size:13px; border-radius:10px; background: linear-gradient(135deg, #0D9488, #059669);">ESS</div>
                        <span class="footer-brand-title">Excellence Faculty Portal</span>
                    </div>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 16px; line-height: 1.5; max-width: 320px;">
                        Integrated Cloud Platform for Class Incharges & Teachers: Attendance, Leave Processing, Marks Entry & Outline Management.
                    </p>
                    <span class="status-pill-live">
                        <span class="status-dot-pulse"></span> System Operational &bull; Faculty Secured
                    </span>
                </div>

                <!-- Col 2: Navigation -->
                <div>
                    <h5 class="footer-col-title">Navigation</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('teacher.page', 'profile') }}">My Profile</a></li>
                        <li><a href="{{ route('teacher.page', 'students') }}">Class Roster</a></li>
                        <li><a href="{{ route('teacher.page', 'outlines') }}">Course Outlines</a></li>
                    </ul>
                </div>

                <!-- Col 3: Faculty Tools -->
                <div>
                    <h5 class="footer-col-title">Faculty Tools</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('teacher.page', 'attendance') }}">Mark Attendance</a></li>
                        <li><a href="{{ route('teacher.page', 'stdleaves') }}">Student Leave Portal</a></li>
                        <li><a href="{{ route('teacher.page', 'results') }}">Class Results</a></li>
                        <li><a href="{{ route('teacher.page', 'salary') }}">Salary & Pay Slips</a></li>
                    </ul>
                </div>

                <!-- Col 4: Security & Help -->
                <div>
                    <h5 class="footer-col-title">Support & Assistance</h5>
                    <p style="font-size: 12.5px; color: var(--muted); margin: 0 0 12px; line-height: 1.4;">
                        Need assistance with class assignment, student leave processing, or marks entry? Contact IT Help Desk.
                    </p>
                    <a href="{{ route('teacher.page', 'profile') }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--primary); font-size: 12.5px; font-weight: 800; text-decoration: none;">
                        Faculty Settings <i class="bi bi-shield-check"></i>
                    </a>
                </div>
            </div>

            <!-- Bottom Copyright & SSL Bar -->
            <div class="footer-bottom-bar">
                <span>&copy; {{ date('Y') }} Excellence School System. All rights reserved.</span>
                <span class="ssl-badge">
                    <i class="bi bi-lock-fill"></i> 256-Bit SSL Encrypted Connection
                </span>
                <div class="footer-social-icons">
                    <a href="#" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" title="Support"><i class="bi bi-envelope-fill"></i></a>
                </div>
            </div>
        </footer>
    </main>

    @include('partials.announcement-modal')

</body>
</html>
