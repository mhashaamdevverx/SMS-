<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Portal | Excellence School System</title>
    
    <!-- Google Fonts: Plus Jakarta Sans for Headings, Inter for Body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Fonts & Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/student-portal.css') }}?v={{ time() }}">
    
    <!-- Custom Theme & Interactive Logic -->
    <script src="{{ asset('js/student-portal.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/common/notifications.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/student/student-portal-page.js') }}?v={{ time() }}" defer></script>
</head>
<body class="student-shell">
    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    <!-- REDESIGNED SIDEBAR (Deep Teal Gradient + Grain Pattern Overlay) -->
    <aside class="student-side">
        <!-- Top Brand Badge -->
        <a class="student-brand" href="{{ route('student.dashboard') }}">
            <div class="brand-badge">ESS</div>
            <div class="brand-text">
                <b>Excellence School System</b>
                <small class="brand-subtitle">Student Portal</small>
            </div>
        </a>

        <!-- Session Glassmorphism Pill -->
        <div class="student-term">
            <i class="bi bi-circle-fill"></i>
            <span>Current Session</span>
            <b>{{ now()->format('Y') }}</b>
        </div>

        <!-- Navigation Categories & Links -->
        <nav>
            <p class="student-menu-label">ACADEMICS</p>
            @php
            $academicMenu = [
                'dashboard' => ['Dashboard', 'bi-grid-1x2-fill'],
                'profile' => ['My Profile', 'bi-person-vcard'],
                'teachers' => ['Teachers Directory', 'bi-person-badge-fill'],
                'academics' => ['Class & Enrolled Subjects', 'bi-book-half'],
                'outlines' => ['Course Outlines', 'bi-journal-bookmark-fill'],
                'results' => ['Academic Results', 'bi-bar-chart-line-fill'],
                'attendance' => ['Attendance Record', 'bi-calendar2-check-fill'],
            ];
            @endphp
            @foreach($academicMenu as $key => $item)
                <a href="{{ $key==='dashboard' ? route('student.dashboard') : route('student.page', $key) }}" class="{{ ($page ?? 'dashboard') === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item[1] }}"></i>
                    <span>{{ $item[0] }}</span>
                </a>
            @endforeach

            <p class="student-menu-label" style="margin-top: 14px;">FINANCE & TESTS</p>
            @php
            $financeMenu = [
                'tests' => ['Monthly Test Sessions', 'bi-journal-check'],
                'fees' => ['Fees & Dues', 'bi-receipt'],
                'slips' => ['Roll No Slips', 'bi-file-earmark-text'],
            ];
            @endphp
            @foreach($financeMenu as $key => $item)
                <a href="{{ $key==='tests' ? route('student.tests') : route('student.page', $key) }}" class="{{ ($page ?? 'dashboard') === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item[1] }}"></i>
                    <span>{{ $item[0] }}</span>
                </a>
            @endforeach

            <p class="student-menu-label" style="margin-top: 14px;">REQUESTS & ACCOUNT</p>
            <a href="{{ route('student.page', 'leaves') }}" class="{{ ($page ?? 'dashboard') === 'leaves' ? 'is-active' : '' }}">
                <i class="bi bi-calendar2-week"></i>
                <span>Leaves & Requests</span>
            </a>
        </nav>

        <!-- Bottom Student Mini-Card & Desktop Collapse Trigger -->
        <div class="sidebar-bottom">
            <div class="student-mini-card">
                <div class="student-mini-avatar" style="overflow: hidden; background: transparent;">
                    @php
                        $stdSideImg = !empty($student->image) && $student->image !== 'dummy.jpeg'
                            ? asset('storage/'.$student->image)
                            : asset('upload/dummy.jpeg');
                    @endphp
                    <img src="{{ $stdSideImg }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="{{ $student->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                </div>
                <div class="student-user-info">
                    <b>{{ $student->name }}</b>
                    <small>{{ $student->class }} ({{ $student->section }})</small>
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
    <main class="student-main">
        <header class="student-top">
            <!-- Left Group: Mobile Drawer Toggle & Breadcrumb -->
            <div class="nav-left-group">
                <button type="button" class="nav-icon-btn mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Mobile Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="portal-breadcrumb">
                    <span>Student Portal</span> /
                    <strong>{{ ucfirst($page ?? 'Dashboard') }}</strong>
                </div>
            </div>

            <!-- Center Group: Command-Style Search Bar -->
            <div class="nav-search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="globalSearchInput" placeholder="Search outlines, results, fees...">
            </div>

            <!-- Right Actions: Theme Toggle, Notifications, Profile Menu -->
            <div class="nav-right-actions">
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
                            <span style="font-size: 11.5px; color: var(--muted); font-weight: 600;">Excellence Real-Time Notification System</span>
                        </div>
                    </div>
                </div>

                <!-- User Profile Pill & Dropdown -->
                <div class="student-user-pill" id="topProfileUser" style="position: relative !important;">
                    <div class="student-avatar-ring">
                        @php
                            $stdTopImg = !empty($student->image) && $student->image !== 'dummy.jpeg'
                                ? asset('storage/'.$student->image)
                                : asset('upload/dummy.jpeg');
                        @endphp
                        <img src="{{ $stdTopImg }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="{{ $student->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    </div>
                    <div class="student-user-details">
                        <b>{{ $student->name }}</b>
                        <small>{{ $student->class }} ({{ $student->section }})</small>
                    </div>
                    <i class="bi bi-chevron-down" id="topProfileArrow" style="font-size: 11px; color: var(--muted); margin-left: 2px;"></i>

                    <!-- Profile Dropdown Menu (Aligned perfectly between pill lines) -->
                    <div id="userDropdownMenu" style="display: none; position: absolute; top: calc(100% + 8px); left: 0; right: 0; width: 100%; min-width: 170px; box-sizing: border-box; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 9999; padding: 6px 0; overflow: hidden;">
                        <div style="padding: 10px 14px; border-bottom: 1px solid var(--border-light); overflow: hidden;">
                            <strong style="display:block; font-size: 13px; color: var(--text); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ $student->name }}</strong>
                            <small style="font-size: 11px; color: var(--primary); font-weight: 700; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; display: block;">Roll #{{ $student->std_id }}</small>
                        </div>
                        <a href="javascript:void(0)" id="dropdownProfileBtn" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); text-decoration: none; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-person-circle" style="color: var(--primary);"></i> Profile Details
                        </a>
                        <a href="{{ route('student.page', 'profile') }}" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); text-decoration: none; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-gear-fill" style="color: var(--primary);"></i> Settings
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
        <div class="student-content">
            @yield('content')
        </div>

        <!-- REDESIGNED MULTI-COLUMN SURFACE FOOTER (Rounded Top Corners + Live Status Pill) -->
        <footer class="main-system-footer">
            <div class="footer-grid-container">
                <!-- Col 1: Brand & Status -->
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <div class="brand-badge" style="width:34px; height:34px; font-size:13px; border-radius:10px;">ESS</div>
                        <span class="footer-brand-title">Excellence School System</span>
                    </div>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 16px; line-height: 1.5; max-width: 320px;">
                        Integrated Cloud Platform for Real-Time Academic Records, Attendance Monitoring & Secure Student Communication.
                    </p>
                    <span class="status-pill-live">
                        <span class="status-dot-pulse"></span> System Operational &bull; v2.4 Encrypted
                    </span>
                </div>

                <!-- Col 2: Navigation -->
                <div>
                    <h5 class="footer-col-title">Navigation</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('student.page', 'profile') }}">My Profile</a></li>
                        <li><a href="{{ route('student.page', 'academics') }}">Enrolled Subjects</a></li>
                        <li><a href="{{ route('student.page', 'outlines') }}">Course Outlines</a></li>
                    </ul>
                </div>

                <!-- Col 3: Portals -->
                <div>
                    <h5 class="footer-col-title">System Portals</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('student.page', 'leaves') }}">Leave Requests</a></li>
                        <li><a href="{{ route('student.page', 'results') }}">Academic Results</a></li>
                        <li><a href="{{ route('student.page', 'fees') }}">Fees & Vouchers</a></li>
                        <li><a href="{{ route('student.page', 'slips') }}">Roll No Slips</a></li>
                    </ul>
                </div>

                <!-- Col 4: Security & Support -->
                <div>
                    <h5 class="footer-col-title">Security & Support</h5>
                    <p style="font-size: 12.5px; color: var(--muted); margin: 0 0 12px; line-height: 1.4;">
                        Have questions about your portal account or academic records? Contact the IT help desk.
                    </p>
                    <a href="{{ route('student.page', 'profile') }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--primary); font-size: 12.5px; font-weight: 800; text-decoration: none;">
                        Account Security Portal <i class="bi bi-shield-check"></i>
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

    <!-- Student Profile Modal Dialog -->
    <div id="student-profile-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(10, 21, 20, 0.7); backdrop-filter: blur(6px); place-items: center; padding: 20px;">
        <section class="account-modal" style="position: relative; width: min(100%, 460px); background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 32px 28px; box-shadow: var(--shadow-lg); text-align: center;">
            <button type="button" id="closeStudentModal" style="position: absolute; top: 16px; right: 16px; border: none; background: var(--bg); width: 36px; height: 36px; border-radius: 50%; font-size: 20px; cursor: pointer; color: var(--muted); display: grid; place-items: center;" title="Close Profile">&times;</button>
            
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 22px;">
                @php
                    $stdModalImg = !empty($student->image) && $student->image !== 'dummy.jpeg'
                        ? asset('storage/'.$student->image)
                        : asset('upload/dummy.jpeg');
                @endphp
                <img src="{{ $stdModalImg }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="{{ $student->name }}" style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); margin-bottom: 12px; box-shadow: var(--shadow-md);">
                <h2 style="margin: 0; font-size: 22px; color: var(--text); font-weight: 800;">Student Profile</h2>
                <p style="margin: 4px 0 0; color: var(--muted); font-size: 14px; font-weight: 500;">{{ $student->name }}</p>
            </div>

            <dl style="display: flex; flex-direction: column; gap: 10px; margin: 0; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <dt style="color: var(--muted); font-size: 13px; font-weight: 600;">Full Name</dt>
                    <dd style="margin: 0; font-weight: 700; color: var(--text); font-size: 14px;">{{ $student->name }}</dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <dt style="color: var(--muted); font-size: 13px; font-weight: 600;">Email Address</dt>
                    <dd style="margin: 0; font-weight: 700; color: var(--text); font-size: 14px;">{{ $user->email }}</dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <dt style="color: var(--muted); font-size: 13px; font-weight: 600;">Role</dt>
                    <dd style="margin: 0; font-weight: 700; color: var(--primary); font-size: 14px;">Student Account</dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <dt style="color: var(--muted); font-size: 13px; font-weight: 600;">Class & Section</dt>
                    <dd style="margin: 0; font-weight: 700; color: var(--text); font-size: 14px;">{{ $student->class }} ({{ $student->section }})</dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
                    <dt style="color: var(--muted); font-size: 13px; font-weight: 600;">Phone Number</dt>
                    <dd style="margin: 0; font-weight: 700; color: var(--text); font-size: 14px;">{{ $student->phone_no ?: 'Not provided' }}</dd>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: var(--primary-light); border-radius: var(--radius-md); border: 1px solid rgba(45, 212, 191, 0.3);">
                    <dt style="color: var(--primary); font-size: 13px; font-weight: 700;">Roll Number</dt>
                    <dd style="margin: 0; font-weight: 800; color: var(--primary); font-size: 16px;">#{{ $student->std_id }}</dd>
                </div>
            </dl>
        </section>
    </div>

</body>
</html>
</html>