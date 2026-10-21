<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>

    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Excellence School System</title>

    <link rel="icon" type="image/png" href="{{ asset('upload/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('upload/Logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- BOOTSTRAP CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- CUSTOM CSS (Modular & Dedicated Style) --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/admin-portal.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/tables.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/students.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/student-profile.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/subjects.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/teachers.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/courses.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/classes.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/sections.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/results.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/fee-structure.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/roll-slips.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/arrangements.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}?v={{ time() }}">

    <script src="{{ asset('js/theme-customizer.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/common/notifications.js') }}?v={{ time() }}" defer></script>

    @stack('page-css')

</head>


<body class="student-shell admin-shell">

    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>


        {{-- REDESIGNED PREMIUM ADMIN SIDEBAR --}}
        <aside class="student-side admin-side" id="adminSidebar">
            {{-- Brand --}}
            <a class="student-brand admin-brand" href="/">
                <div class="brand-badge">ESS</div>
                <div class="brand-text">
                    <b>Excellence School System</b>
                    <small class="brand-subtitle">Admin Portal</small>
                </div>
            </a>

            {{-- Session Pill --}}
            <div class="student-term admin-term">
                <i class="bi bi-circle-fill"></i>
                <span>Current Session</span>
                <b>{{ now()->format('Y') }}</b>
            </div>

            {{-- Navigation --}}
            <nav>
                <p class="admin-menu-label">MANAGEMENT</p>
                @if($currentUser->isAdmin())
                    <a href="/roles" class="{{ request()->is('roles*') ? 'is-active' : '' }}">
                        <i class="bi bi-shield-lock-fill"></i><span>Roles &amp; Permissions</span>
                    </a>
                @endif
                <a href="/" class="{{ request()->is('/') ? 'is-active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
                </a>
                <a href="/list" class="{{ request()->is('list*') || request()->is('students*') || request()->is('add-student*') ? 'is-active' : '' }}">
                    <i class="bi bi-mortarboard-fill"></i><span>Students</span>
                </a>
                <a href="/teachers" class="{{ request()->is('teachers*') || request()->is('add-teacher*') ? 'is-active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i><span>Teachers</span>
                </a>

                <p class="admin-menu-label" style="margin-top: 14px;">ACADEMICS</p>
                <a href="/classes" class="{{ request()->is('classes*') || request()->is('add-class*') ? 'is-active' : '' }}">
                    <i class="bi bi-easel2-fill"></i><span>Classes</span>
                </a>
                <a href="/sections" class="{{ request()->is('sections*') || request()->is('add-section*') ? 'is-active' : '' }}">
                    <i class="bi bi-grid-3x3-gap-fill"></i><span>Sections</span>
                </a>
                <a href="/subjects" class="{{ request()->is('subjects*') || request()->is('add-subject*') ? 'is-active' : '' }}">
                    <i class="bi bi-book-fill"></i><span>Subjects</span>
                </a>
                <a href="/courses" class="{{ request()->is('courses*') || request()->is('add-course*') ? 'is-active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill"></i><span>Courses</span>
                </a>
                <a href="/attendance" class="{{ request()->is('attendance*') ? 'is-active' : '' }}">
                    <i class="bi bi-calendar-check-fill"></i><span>Attendance</span>
                </a>
                <a href="/results" class="{{ request()->is('results*') || request()->is('add-result*') ? 'is-active' : '' }}">
                    <i class="bi bi-clipboard2-data-fill"></i><span>Results</span>
                </a>

                <p class="admin-menu-label" style="margin-top: 14px;">FINANCE &amp; ADMIN</p>
                <a href="/roll-slips" class="{{ request()->is('roll-slips*') || request()->is('issue-roll-slips*') ? 'is-active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i><span>Roll No Slips</span>
                </a>
                <a href="/fee-structure" class="{{ request()->is('fee-structure*') ? 'is-active' : '' }}">
                    <i class="bi bi-cash-stack"></i><span>Fee Structure</span>
                </a>
                <a href="/arrangements" class="{{ request()->is('arrangements*') || request()->is('add-arrangement*') ? 'is-active' : '' }}">
                    <i class="bi bi-columns-gap"></i><span>Arrangements</span>
                </a>
                @if($currentUser->isAdmin() || $currentUser->hasPermission('users.view'))
                    <a href="/users" class="{{ request()->is('users*') ? 'is-active' : '' }}">
                        <i class="bi bi-people-fill"></i><span>Users</span>
                    </a>
                @endif
            </nav>

            {{-- Sidebar Bottom --}}
            <div class="sidebar-bottom">
                <div class="student-mini-card admin-mini-card">
                    <div class="student-mini-avatar admin-mini-avatar" style="width: 38px; height: 38px; min-width: 38px; max-width: 38px; min-height: 38px; max-height: 38px; border-radius: 50%; overflow: hidden; flex-shrink: 0; background: transparent;">
                        @php
                            $admSideApp = session('user_image') && session('user_image') !== 'dummy.jpeg'
                                ? asset('storage/'.session('user_image'))
                                : asset('upload/dummy.jpeg');
                        @endphp
                        <img src="{{ $admSideApp }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="{{ session('user_name', 'Admin') }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
                    </div>
                    <div class="student-user-info admin-user-info">
                        <b>{{ session('user_name') }}</b>
                        <small>{{ $currentUser->role?->name ?? 'Administrator' }}</small>
                    </div>
                </div>
                <div class="sidebar-controls">
                    <button type="button" id="adminSidebarCollapseBtn" class="btn-sidebar-control" title="Toggle Sidebar">
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


    {{-- MAIN PORTAL WRAPPER --}}
    <main class="student-main admin-main" id="adminMain">

        {{-- REDESIGNED ADMIN PORTAL NAVBAR (Glassmorphism) --}}
        <header class="student-top admin-top">
            <div class="nav-left-group">
                <button type="button" class="mobile-nav-toggle nav-icon-btn" id="mobileNavToggle" aria-label="Toggle navigation drawer">
                    <i class="bi bi-list"></i>
                </button>
                <div class="portal-breadcrumb">
                    <span>Admin Portal</span> /
                    <strong>{{ ucwords(str_replace(['-', '/'], ' ', request()->path())) ?: 'Dashboard' }}</strong>
                </div>
            </div>

            {{-- Center: Search Bar --}}
            <div class="nav-search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="adminGlobalSearch" placeholder="Search students, teachers, results...">
                <span class="kbd-shortcut">⌘K</span>
            </div>

            <div class="nav-right-actions">
                {{-- Dark/Light Mode Toggle --}}
                <button type="button" class="nav-icon-btn" id="adminThemeToggleBtn" title="Toggle Theme">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>

                @if(session()->has('user_id') || auth()->check())
                    {{-- Notification Bell --}}
                    <div class="notification-wrapper" style="position: relative;">
                        <button type="button" id="adminNotifBellBtn" class="nav-icon-btn" title="Notifications" style="position: relative;">
                            <i class="bi bi-bell-fill"></i>
                            <span id="adminNotifBadge" style="display: none; position: absolute; top: -4px; right: -4px; background: #EF4444; color: #ffffff; font-size: 10px; font-weight: 900; padding: 1px 5px; border-radius: 99px; border: 2px solid var(--surface); box-shadow: 0 2px 4px rgba(239, 68, 68, 0.4);">0</span>
                        </button>

                        {{-- Notification Dropdown --}}
                        <div id="adminNotifDropdown" style="display: none; position: absolute; top: calc(100% + 12px); right: 0; width: 360px; max-width: 90vw; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); z-index: 999999; overflow: hidden;">
                            <div style="padding: 14px 18px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="bi bi-bell-fill" style="color: var(--primary); font-size: 16px;"></i>
                                    <strong style="font-size: 14px; color: var(--text);">Notifications</strong>
                                </div>
                                <button type="button" onclick="markAllNotificationsAsReadAdmin()" style="background: transparent; border: none; color: var(--primary); font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: underline;">Mark all as read</button>
                            </div>
                            <div id="adminNotifListContainer" style="max-height: 360px; overflow-y: auto; padding: 6px 0;">
                                <div style="text-align: center; padding: 20px; color: var(--muted); font-size: 12.5px;">
                                    <i class="bi bi-hourglass-split" style="font-size: 18px; display: block; margin-bottom: 4px;"></i> Loading notifications...
                                </div>
                            </div>
                            <div style="padding: 8px 14px; background: var(--bg); border-top: 1px solid var(--border); text-align: center;">
                                <span style="font-size: 11px; color: var(--muted); font-weight: 600;">Excellence Real-Time Notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- Admin User Pill --}}
                    <div class="admin-user-pill" id="adminTopProfileUser" style="position: relative !important;">
                        <div class="student-avatar-ring admin-avatar-ring" style="width: 36px; height: 36px; min-width: 36px; max-width: 36px; min-height: 36px; max-height: 36px; border-radius: 50%; overflow: hidden; flex-shrink: 0;">
                            @php
                                $admTopImg = session('user_image') && session('user_image') !== 'dummy.jpeg'
                                    ? asset('storage/'.session('user_image'))
                                    : asset('upload/dummy.jpeg');
                            @endphp
                            <img src="{{ $admTopImg }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="{{ session('user_name', 'Admin') }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;">
                        </div>
                        <div class="admin-user-details">
                            <b>{{ session('user_name') }}</b>
                            <small>Administrator</small>
                        </div>
                        <i class="bi bi-chevron-down" style="font-size: 11px; color: var(--muted); margin-left: 2px;"></i>

                        {{-- Profile Dropdown --}}
                        <div id="adminUserDropdown" style="display: none; position: absolute; top: calc(100% + 8px); left: 0; right: 0; width: 100%; min-width: 180px; box-sizing: border-box; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 9999; padding: 6px 0; overflow: hidden;">
                            <div style="padding: 10px 14px; border-bottom: 1px solid var(--border-light); overflow: hidden;">
                                <strong style="display:block; font-size: 13px; color: var(--text); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ session('user_name') }}</strong>
                                <small style="font-size: 11px; color: var(--primary); font-weight: 700; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; display: block;">{{ auth()->user()?->email }}</small>
                            </div>
                            <button type="button" class="profile-menu-action" data-modal-target="profile-modal" style="width:100%; display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); background: transparent; border: none; font-size: 13px; font-weight: 600; cursor: pointer; text-align: left;">
                                <i class="bi bi-person-circle" style="color: var(--primary);"></i> Profile
                            </button>
                            <button type="button" class="profile-menu-action" data-modal-target="settings-modal" style="width:100%; display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: var(--text); background: transparent; border: none; font-size: 13px; font-weight: 600; cursor: pointer; text-align: left;">
                                <i class="bi bi-gear-fill" style="color: var(--primary);"></i> Settings
                            </button>
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

                @else
                    <a href="/login" class="nav-icon-btn" style="text-decoration:none; font-size: 13px; width: auto; padding: 0 14px;">Login</a>
                @endif
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <div class="student-content admin-content">

            @yield('content')

        </div>

        <!-- FOOTER -->
        <footer class="main-system-footer">
            <div class="footer-grid-container">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <div class="brand-badge" style="width:34px; height:34px; font-size:13px; border-radius:10px;">ESS</div>
                        <span class="footer-brand-title">Excellence School System</span>
                    </div>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 16px; line-height: 1.5; max-width: 320px;">
                        Central Control & Governance Authority Portal for Academic Tracking, Leaves, Salaries, Student Fees & School System Management.
                    </p>
                    <span class="status-pill-live">
                        <span class="status-dot-pulse"></span> Head Office Active &bull; v2.4 Encrypted
                    </span>
                </div>

                <div>
                    <h5 class="footer-col-title">Administration</h5>
                    <ul class="footer-links">
                        <li><a href="/">Dashboard</a></li>
                        <li><a href="/list">Students Directory</a></li>
                        <li><a href="/teachers">Teachers Directory</a></li>
                        <li><a href="/attendance">Attendance Logs</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="footer-col-title">Academics & Operations</h5>
                    <ul class="footer-links">
                        <li><a href="/classes">Classes & Enrolment</a></li>
                        <li><a href="/sections">Sections Management</a></li>
                        <li><a href="/subjects">Subjects & Syllabus</a></li>
                        <li><a href="/results">Examination Results</a></li>
                        <li><a href="/roll-slips">Roll No Slips</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="footer-col-title">Contact & Support</h5>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Main Administrative Complex, Academic Wing A</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <span>support@excellence.edu.pk</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone-fill"></i>
                        <span>+92 (042) 111-222-333</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom-bar">
                <div>&copy; {{ date('Y') }} <strong>Excellence School System</strong> &bull; All Rights Reserved.</div>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Protocol</a>
                    <a href="#">Terms of Governance</a>
                    <a href="#">Portal Security</a>
                </div>
            </div>
        </footer>
    </main>



    <div class="crud-confirm-backdrop" id="crud-confirm-modal" hidden>
        <section class="crud-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="crud-confirm-title">
            <button type="button" class="modal-close" data-crud-confirm-close aria-label="Close confirmation dialog">&times;</button>
            <div class="crud-confirm-icon"><i class="bi bi-question-lg" aria-hidden="true"></i></div>
            <h2 id="crud-confirm-title">Please confirm</h2>
            <p id="crud-confirm-message">Are you sure you want to continue?</p>
            <div class="crud-confirm-actions">
                <button type="button" class="modal-secondary-btn" data-crud-confirm-cancel>Cancel</button>
                <button type="button" class="modal-danger-btn" data-crud-confirm-submit>Confirm</button>
            </div>
        </section>
    </div>


    <div class="modal-backdrop" id="profile-modal" hidden>
        <section class="account-modal" role="dialog" aria-modal="true" aria-labelledby="profile-modal-title">
            <button type="button" class="modal-close" data-modal-close aria-label="Close profile dialog">&times;</button>

            <div class="modal-profile-header">
                @php
                    $admModalImg = session('user_image') && session('user_image') !== 'dummy.jpeg'
                        ? asset('storage/'.session('user_image'))
                        : asset('upload/dummy.jpeg');
                @endphp
                <img
                    src="{{ $admModalImg }}"
                    onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';"
                    alt="Profile image"
                    class="modal-avatar"
                >
                <h2 id="profile-modal-title">Profile</h2>
                <p>{{ $currentUser->name ?: $currentUser->username }}</p>
            </div>

            <dl class="profile-details">
                <div>
                    <dt>Full Name</dt>
                    <dd>{{ $currentUser->name ?: $currentUser->username }}</dd>
                </div>
                <div>
                    <dt>Email Address</dt>
                    <dd>{{ $currentUser->email }}</dd>
                </div>
                <div>
                    <dt>Role</dt>
                    <dd>{{ $currentUser->role?->name ?? 'Awaiting role assignment' }}</dd>
                </div>
                @if($currentUser->role?->slug === 'student')
                    @php($stdRecord = $currentUser->studentRecord ?: \App\Models\Student::where('login_user_id', $currentUser->id)->first())
                    <div>
                        <dt>Class & Section</dt>
                        <dd>{{ $stdRecord?->class ?? 'Unassigned' }} / {{ $stdRecord?->section ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt>Phone Number</dt>
                        <dd>{{ $currentUser->phone ?: $stdRecord?->phone_no ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt>Roll No</dt>
                        <dd>#{{ $stdRecord?->std_id ?? $currentUser->id }}</dd>
                    </div>
                @else
                    @if($currentUser->classIncharge?->classRecord)
                        <div>
                            <dt>Incharge assignment</dt>
                            <dd>{{ $currentUser->classIncharge->classRecord->class_name }} / {{ $currentUser->classIncharge->classRecord->section_name }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt>Phone Number</dt>
                        <dd>{{ $currentUser->phone ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt>Account ID</dt>
                        <dd>#{{ $currentUser->id }}</dd>
                    </div>
                @endif
            </dl>
        </section>
    </div>


    <div class="modal-backdrop" id="settings-modal" hidden>
        <section class="account-modal" role="dialog" aria-modal="true" aria-labelledby="settings-modal-title">
            <button type="button" class="modal-close" data-modal-close aria-label="Close settings dialog">&times;</button>

            <div class="modal-heading">
                <p class="modal-kicker">Account</p>
                <h2 id="settings-modal-title">Settings & Theme</h2>
                <p>Keep your account details and portal appearance updated.</p>
            </div>

            <form action="/profile/settings" method="POST" class="settings-form">
                @csrf

                <div class="settings-section">
                    <h3>Profile information</h3>

                    <label for="settings-name">Name</label>
                    <input id="settings-name" type="text" name="name" value="{{ $currentUser->name ?: $currentUser->username }}" required>

                    <label for="settings-email">Email</label>
                    <input id="settings-email" type="email" name="email" value="{{ $currentUser->email }}" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" placeholder="name@gmail.com" title="Please enter a valid Gmail address (e.g. name@gmail.com)" required>
                    <small style="display:block;margin-top:3px;color:var(--muted);font-size:11.5px;">Must be a valid @gmail.com address.</small>

                    <label for="settings-phone">Phone</label>
                    <input id="settings-phone" type="text" name="phone" value="{{ $currentUser->phone }}">

                    <p class="settings-role-summary">Current role: <strong>{{ $currentUser->role?->name ?? 'Awaiting role assignment' }}</strong></p>

                    @error('name') <span class="settings-error">{{ $message }}</span> @enderror
                    @error('email') <span class="settings-error">{{ $message }}</span> @enderror
                    @error('phone') <span class="settings-error">{{ $message }}</span> @enderror
                </div>

                <div class="settings-section settings-options">
                    <h3>Preferences & Theme Color</h3>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Navbar & Sidebar Theme Color</label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                            <button type="button" onclick="setAppTheme('indigo')" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; text-align: left;">
                                <span style="width: 16px; height: 16px; border-radius: 50%; background: #4F46E5; display: inline-block;"></span>
                                <span style="font-size: 12px; font-weight: 600; color: #1e293b;">Deep Indigo (Default)</span>
                            </button>
                            <button type="button" onclick="setAppTheme('emerald')" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; text-align: left;">
                                <span style="width: 16px; height: 16px; border-radius: 50%; background: #14B8A6; display: inline-block;"></span>
                                <span style="font-size: 12px; font-weight: 600; color: #1e293b;">Teal Mint</span>
                            </button>
                            <button type="button" onclick="setAppTheme('ocean')" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; text-align: left;">
                                <span style="width: 16px; height: 16px; border-radius: 50%; background: #2563eb; display: inline-block;"></span>
                                <span style="font-size: 12px; font-weight: 600; color: #1e293b;">Ocean Blue</span>
                            </button>
                            <button type="button" onclick="setAppTheme('sunset')" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; text-align: left;">
                                <span style="width: 16px; height: 16px; border-radius: 50%; background: #7c3aed; display: inline-block;"></span>
                                <span style="font-size: 12px; font-weight: 600; color: #1e293b;">Sunset Violet</span>
                            </button>
                            <button type="button" onclick="setAppTheme('midnight')" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; text-align: left;">
                                <span style="width: 16px; height: 16px; border-radius: 50%; background: #334155; display: inline-block;"></span>
                                <span style="font-size: 12px; font-weight: 600; color: #1e293b;">Midnight Slate</span>
                            </button>
                            <button type="button" onclick="setAppTheme('rose')" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; text-align: left;">
                                <span style="width: 16px; height: 16px; border-radius: 50%; background: #e11d48; display: inline-block;"></span>
                                <span style="font-size: 12px; font-weight: 600; color: #1e293b;">Vibrant Rose</span>
                            </button>
                        </div>
                    </div>

                    <label class="toggle-row">
                        <span><strong>Email notifications</strong><small>Receive updates about your account.</small></span>
                        <input type="checkbox" name="notifications" value="1" checked>
                    </label>
                </div>

                <button type="submit" class="settings-save-btn">Save Changes</button>
            </form>

            <form action="{{ route('account.password.update') }}" method="POST" class="settings-form settings-password-form">
                @csrf
                @method('PUT')
                <div class="settings-section">
                    <h3>Update password</h3>
                    <label for="settings-current-password">Current password</label>
                    <input id="settings-current-password" type="password" name="current_password" autocomplete="current-password" required>
                    @error('current_password') <span class="settings-error">{{ $message }}</span> @enderror
                    <label for="settings-new-password">New password</label>
                    <input id="settings-new-password" type="password" name="password" minlength="10" autocomplete="new-password" required>
                    @error('password') <span class="settings-error">{{ $message }}</span> @enderror
                    <label for="settings-password-confirmation">Confirm new password</label>
                    <input id="settings-password-confirmation" type="password" name="password_confirmation" minlength="10" autocomplete="new-password" required>
                    @error('password_confirmation') <span class="settings-error">{{ $message }}</span> @enderror
                    <button type="submit" class="settings-save-btn">Update Password</button>
                </div>
            </form>
        </section>
    </div>



    {{-- BOOTSTRAP JS --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>



    {{-- AUTO HIDE SUCCESS MESSAGES --}}


    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile drawer toggle - Admin Portal
        const drawerToggle = document.getElementById('mobileNavToggle');
        const sidebar = document.getElementById('adminSidebar') || document.querySelector('.admin-side') || document.querySelector('.sidebar');
        const overlay = document.getElementById('mobileNavOverlay');

        if (drawerToggle && sidebar && overlay) {
            drawerToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }

        // Desktop Sidebar Collapse Toggle
        const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn') || document.getElementById('adminSidebarCollapseBtn');
        const mainContent = document.querySelector('.admin-main');
        if (sidebarCollapseBtn && sidebar) {
            sidebarCollapseBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                if (mainContent) {
                    mainContent.classList.toggle('main-expanded');
                    mainContent.classList.toggle('sidebar-collapsed');
                }
            });
        }

        // Theme Toggle (Dark / Light)
        const themeBtn = document.getElementById('adminThemeToggleBtn');
        if (themeBtn) {
            const savedTheme = localStorage.getItem('akm_theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.classList.add('dark');
                themeBtn.innerHTML = '<i class="bi bi-sun-fill" style="color: #F59E0B;"></i>';
            }

            themeBtn.addEventListener('click', () => {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark' || document.documentElement.classList.contains('dark');
                if (isDark) {
                    document.documentElement.removeAttribute('data-theme');
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('akm_theme', 'light');
                    themeBtn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
                } else {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('akm_theme', 'dark');
                    themeBtn.innerHTML = '<i class="bi bi-sun-fill" style="color: #F59E0B;"></i>';
                }
            });
        }

        // Admin Profile Dropdown
        const userPill = document.getElementById('adminTopProfileUser');
        const userDropdown = document.getElementById('adminUserDropdown');
        if (userPill && userDropdown) {
            userPill.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = userDropdown.style.display === 'block';
                userDropdown.style.display = isOpen ? 'none' : 'block';
            });
            document.addEventListener('click', (e) => {
                if (!userPill.contains(e.target)) {
                    userDropdown.style.display = 'none';
                }
            });
        }
    });
    </script>
</body>

</html>
