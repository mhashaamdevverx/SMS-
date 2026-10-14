<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin & Head Management Portal | Excellence School System</title>
    
    <link rel="icon" type="image/png" href="{{ asset('upload/Logo.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Fonts & Stylesheets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/student-portal.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/admin-portal.css') }}?v={{ time() }}">
    
    <!-- Custom Theme & Interactive Logic -->
    <script src="{{ asset('js/student-portal.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/common/notifications.js') }}?v={{ time() }}" defer></script>
</head>
<body class="student-shell admin-shell">
    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    <!-- REDESIGNED SIDEBAR (Deep Teal Gradient + Grain Pattern Overlay) -->
    <aside class="student-side admin-side">
        <!-- Top Brand Badge -->
        <a class="student-brand admin-brand" href="{{ route('admin.dashboard') }}">
            <div class="brand-badge">ESS</div>
            <div class="brand-text">
                <b>Excellence School System</b>
                <small class="brand-subtitle">Admin Portal</small>
            </div>
        </a>

        <!-- Session Glassmorphism Pill -->
        <div class="student-term admin-term">
            <i class="bi bi-circle-fill"></i>
            <span>Current Session</span>
            <b>{{ now()->format('Y') }}</b>
        </div>

        <!-- Navigation Categories & Links -->
        <nav>
            <p class="student-menu-label admin-menu-label">MAIN ADMINISTRATION</p>
            @php
            $currentPageName = $currentPage ?? 'dashboard';
            $mainMenu = [
                'dashboard' => ['Dashboard', 'bi-grid-1x2-fill'],
                'leaves' => ['Leaves Approval', 'bi-calendar2-check-fill'],
                'salaries' => ['Teacher Salaries', 'bi-cash-stack'],
                'students' => ['Students Directory', 'bi-mortarboard-fill'],
                'teachers' => ['Teachers Directory', 'bi-person-badge-fill'],
                'fees' => ['Student Fees', 'bi-receipt'],
                'attendance' => ['Attendance Logs', 'bi-clock-history'],
            ];
            @endphp
            @foreach($mainMenu as $key => $item)
                <a href="{{ route('admin.page', $key) }}" class="{{ $currentPageName === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item[1] }}"></i>
                    <span>{{ $item[0] }}</span>
                    @if($key === 'leaves' && isset($pendingLeavesCount) && $pendingLeavesCount > 0)
                        <span class="nav-badge-pill" style="background: #EF4444; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 99px; margin-left: auto;">{{ $pendingLeavesCount }}</span>
                    @endif
                </a>
            @endforeach

            <p class="student-menu-label admin-menu-label" style="margin-top: 14px;">SCHOOL OPERATIONS</p>
            @php
            $opsMenu = [
                'academics' => ['Academics & Operations', 'bi-journal-bookmark-fill'],
                'roles' => ['Roles & Incharges', 'bi-shield-lock-fill'],
            ];
            @endphp
            @foreach($opsMenu as $key => $item)
                <a href="{{ route('admin.page', $key) }}" class="{{ $currentPageName === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item[1] }}"></i>
                    <span>{{ $item[0] }}</span>
                    @if($key === 'roles' && isset($pendingAccessRequestsCount) && $pendingAccessRequestsCount > 0)
                        <span class="nav-badge-pill" style="background: #F59E0B; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 99px; margin-left: auto;">{{ $pendingAccessRequestsCount }}</span>
                    @endif
                </a>
            @endforeach

            <p class="student-menu-label admin-menu-label" style="margin-top: 14px;">SYSTEM CONFIG</p>
            <a href="{{ route('admin.page', 'users') }}" class="{{ $currentPageName === 'users' ? 'is-active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>System Accounts</span>
            </a>
        </nav>

        <!-- Bottom Admin Mini-Card & Controls -->
        <div class="sidebar-bottom">
            <div class="student-mini-card admin-mini-card">
                <div class="student-mini-avatar admin-mini-avatar">
                    @if(session('user_image') && session('user_image') !== 'dummy.jpeg')
                        <img src="{{ asset('storage/'.session('user_image')) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        A
                    @endif
                </div>
                <div class="student-user-info admin-user-info">
                    <b>{{ session('user_name', 'Abdul Moeez') }}</b>
                    <small>Administrator</small>
                </div>
            </div>

            <div class="sidebar-controls">
                <button type="button" id="sidebarCollapseBtn" class="btn-sidebar-control" title="Toggle Sidebar">
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
    <main class="student-main admin-main">
        <header class="student-top admin-top">
            <!-- Left Group: Mobile Drawer Toggle & Breadcrumb -->
            <div class="nav-left-group">
                <button type="button" class="nav-icon-btn mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Mobile Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="portal-breadcrumb">
                    <span>Admin Portal</span> /
                    <strong>{{ ucfirst($currentPageName ?? 'Dashboard') }}</strong>
                </div>
            </div>

            <!-- Center Group: Command-Style Search Bar -->
            <div class="nav-search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="globalSearchInput" placeholder="Search leaves, teachers, fees, students...">
            </div>

            <!-- Right Actions: Theme Toggle, Notifications, Profile Menu -->
            <div class="nav-right-actions">
                <!-- Light / Dark Theme Switcher Button -->
                <button type="button" class="nav-icon-btn" id="themeToggleBtn" title="Switch Theme">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>

                <!-- Notification Bell Component -->
                <div class="notification-wrapper" style="position: relative;">
                    <button type="button" class="nav-icon-btn" id="adminNotifBellBtn" title="Notifications" style="position: relative;">
                        <i class="bi bi-bell-fill"></i>
                        <span id="adminNotifBadge" class="notif-count-badge" style="display: none; position: absolute; top: -4px; right: -4px; background: #EF4444; color: #ffffff; font-size: 10px; font-weight: 900; padding: 1px 5px; border-radius: 99px; border: 2px solid var(--surface);">0</span>
                    </button>

                    <!-- Dropdown Container -->
                    <div id="adminNotifDropdown" style="display: none; position: absolute; top: calc(100% + 12px); right: 0; width: 360px; max-width: 90vw; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); z-index: 999999; overflow: hidden;">
                        <div style="padding: 14px 18px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-bell-fill" style="color: var(--primary); font-size: 16px;"></i>
                                <strong style="font-size: 14px; color: var(--text);">Admin Notifications</strong>
                            </div>
                            <button type="button" onclick="markAllNotificationsAsReadAdmin()" style="background: transparent; border: none; color: var(--primary); font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: underline;">
                                Mark all as read
                            </button>
                        </div>

                        <div id="adminNotifListContainer" style="max-height: 360px; overflow-y: auto; padding: 6px 0;">
                            <div style="text-align: center; padding: 24px; color: var(--muted); font-size: 13px;">
                                <i class="bi bi-hourglass-split" style="font-size: 20px; display: block; margin-bottom: 6px;"></i> Loading updates...
                            </div>
                        </div>

                        <div style="padding: 10px 16px; background: var(--bg); border-top: 1px solid var(--border); text-align: center;">
                            <span style="font-size: 11.5px; color: var(--muted); font-weight: 600;">Excellence Admin Real-Time System</span>
                        </div>
                    </div>
                </div>

                <!-- Admin Profile Pill & Dropdown -->
                <div class="student-user-pill admin-user-pill" id="topProfileUser">
                    <div class="student-avatar-ring admin-avatar-ring">
                        @if(session('user_image') && session('user_image') !== 'dummy.jpeg')
                            <img src="{{ asset('storage/'.session('user_image')) }}" alt="">
                        @else
                            <span>A</span>
                        @endif
                    </div>
                    <div class="student-user-details admin-user-details">
                        <b>{{ session('user_name', 'Abdul Moeez') }}</b>
                        <small>Administrator</small>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size: 11px; color: var(--muted); margin-left: 2px;"></i>

                    <!-- Profile Dropdown Menu -->
                    <div id="userDropdownMenu" style="display: none; position: absolute; top: calc(100% + 8px); right: 0; min-width: 200px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 9999; padding: 6px 0; overflow: hidden;">
                        <div style="padding: 10px 14px; border-bottom: 1px solid var(--border-light);">
                            <strong style="display:block; font-size: 13px; color: var(--text);">{{ session('user_name', 'Abdul Moeez') }}</strong>
                            <small style="font-size: 11px; color: var(--primary); font-weight: 700;">Head Administrator</small>
                        </div>
                        <a href="{{ route('admin.page', 'users') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); text-decoration: none; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-person-circle" style="color: var(--primary);"></i> Account Details
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
        <div class="student-content admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- FOOTER -->
        <footer class="main-system-footer">
            <div class="footer-grid-container">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <div class="brand-badge" style="width:34px; height:34px; font-size:13px; border-radius:10px;">ESS</div>
                        <span class="footer-brand-title">Excellence Head Administration</span>
                    </div>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 16px; line-height: 1.5; max-width: 320px;">
                        Central Control & Governance Authority Portal for Leaves, Salaries, Student Fees & School System Management.
                    </p>
                    <span class="status-pill-live">
                        <span class="status-dot-pulse"></span> Head Office Active &bull; v2.4 Encrypted
                    </span>
                </div>

                <div>
                    <h5 class="footer-col-title">Administration</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('admin.page', 'dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.page', 'leaves') }}">Leaves Approval</a></li>
                        <li><a href="{{ route('admin.page', 'salaries') }}">Teacher Salaries</a></li>
                        <li><a href="{{ route('admin.page', 'fees') }}">Student Fees</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="footer-col-title">Directories</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('admin.page', 'students') }}">Students Directory</a></li>
                        <li><a href="{{ route('admin.page', 'teachers') }}">Teachers Directory</a></li>
                        <li><a href="{{ route('admin.page', 'academics') }}">Academics & Operations</a></li>
                        <li><a href="{{ route('admin.page', 'roles') }}">Roles & Incharges</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="footer-col-title">System Security</h5>
                    <p style="font-size: 12.5px; color: var(--muted); margin: 0 0 12px; line-height: 1.4;">
                        Full governance control over system accounts, access logs, and user roles.
                    </p>
                    <a href="{{ route('admin.page', 'users') }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--primary); font-size: 12.5px; font-weight: 800; text-decoration: none;">
                        System Accounts <i class="bi bi-shield-check"></i>
                    </a>
                </div>
            </div>

            <div class="footer-bottom-bar">
                <span>&copy; {{ date('Y') }} Excellence School System. All rights reserved.</span>
                <span class="ssl-badge">
                    <i class="bi bi-lock-fill"></i> 256-Bit SSL Encrypted Admin Session
                </span>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
