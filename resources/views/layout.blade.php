<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Management System</title>

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

    {{-- CUSTOM CSS (Modular) --}}
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

    @stack('page-css')

</head>


<body class="admin-shell">

    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

    {{-- REDESIGNED ADMIN PORTAL NAVBAR (Glassmorphism) --}}
    <header class="admin-top">
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
                            <span style="font-size: 11px; color: var(--muted); font-weight: 600;">AKM Real-Time Notifications</span>
                        </div>
                    </div>
                </div>

                {{-- Admin User Pill --}}
                <div class="admin-user-pill" id="adminTopProfileUser">
                    <div class="admin-avatar-ring">
                        @if(!session('user_image') || session('user_image') === 'dummy.jpeg')
                            <span>{{ strtoupper(substr(session('user_name', 'A'), 0, 1)) }}</span>
                        @else
                            <img src="{{ asset('storage/'.session('user_image')) }}" alt="">
                        @endif
                    </div>
                    <div class="admin-user-details">
                        <b>{{ session('user_name') }}</b>
                        <small>Administrator</small>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size: 11px; color: var(--muted); margin-left: 2px;"></i>

                    {{-- Profile Dropdown --}}
                    <div id="adminUserDropdown" style="display: none; position: absolute; top: calc(100% + 8px); left: 0; right: 0; min-width: 180px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 9999; padding: 6px 0; overflow: hidden;">
                        <div style="padding: 10px 14px; border-bottom: 1px solid var(--border-light);">
                            <strong style="display:block; font-size: 13px; color: var(--text);">{{ session('user_name') }}</strong>
                            <small style="font-size: 11px; color: var(--primary); font-weight: 700;">{{ auth()->user()?->email }}</small>
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

    {{-- ADMIN PORTAL LAYOUT --}}

    <div style="display: flex; flex: 1; min-height: 100vh;">

        {{-- REDESIGNED PREMIUM ADMIN SIDEBAR --}}
        <aside class="admin-side" id="adminSidebar">
            {{-- Brand --}}
            <a class="admin-brand" href="/">
                <div class="brand-badge">AKM</div>
                <div class="brand-text">
                    <b>AKM School</b>
                    <small class="brand-subtitle">Admin Portal</small>
                </div>
            </a>

            {{-- Session Pill --}}
            <div class="admin-term">
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
                <div class="admin-mini-card">
                    <div class="admin-mini-avatar">
                        @if(!session('user_image') || session('user_image') === 'dummy.jpeg')
                            {{ strtoupper(substr(session('user_name', 'A'), 0, 1)) }}
                        @else
                            <img src="{{ asset('storage/'.session('user_image')) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        @endif
                    </div>
                    <div class="admin-user-info">
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


        {{-- MAIN CONTENT --}}
        <main class="admin-main" id="adminMain">
            <div class="admin-content">

                @yield('content')

            </div>
        </main>

    </div>

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
                <img
                    src="{{ !session('user_image') || session('user_image') === 'dummy.jpeg' ? asset('upload/dummy.jpeg') : asset('storage/'.session('user_image')) }}"
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
                    <input id="settings-email" type="email" name="email" value="{{ $currentUser->email }}" required>

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

    <!-- MODERN ATTRACTIVE MULTI-COLUMN FOOTER -->
    <footer class="portal-footer" style="background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); color: #cbd5e1; font-family: 'Inter', sans-serif; padding-top: 50px; padding-bottom: 24px; border-top: 4px solid var(--primary-color, #4F46E5); margin-top: 60px;">
        <div class="container" style="max-width: 1240px; margin: 0 auto; padding: 0 20px;">
            <div class="footer-grid-container" style="display: grid; grid-template-columns: 2.2fr 1fr 1fr 1.5fr; gap: 40px; margin-bottom: 40px;">
                <!-- Column 1: Brand & About -->
                <div class="footer-col-brand">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <img src="{{ asset('upload/Logo.png') }}" alt="Logo" style="height: 44px; width: auto; filter: brightness(0) invert(1);">
                        <div>
                            <h4 style="color: #ffffff; font-family: 'Outfit', sans-serif; font-size: 19px; font-weight: 700; margin: 0; line-height: 1.2;">AKM College Portal</h4>
                            <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-light, #14b8a6); font-weight: 600;">Excellence & Innovation</span>
                        </div>
                    </div>
                    <p style="font-size: 13.5px; line-height: 1.65; color: #94a3b8; margin-bottom: 20px;">
                        Empowering educational management with real-time academic tracking, dynamic attendance monitoring, intelligent leave processing, and seamless portal interactions.
                    </p>
                    <div style="display: flex; gap: 12px;">
                        <a href="#" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.08); color: #ffffff; display: grid; place-items: center; text-decoration: none; transition: all 0.2s ease;"><i class="bi bi-facebook"></i></a>
                        <a href="#" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.08); color: #ffffff; display: grid; place-items: center; text-decoration: none; transition: all 0.2s ease;"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.08); color: #ffffff; display: grid; place-items: center; text-decoration: none; transition: all 0.2s ease;"><i class="bi bi-linkedin"></i></a>
                        <a href="#" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.08); color: #ffffff; display: grid; place-items: center; text-decoration: none; transition: all 0.2s ease;"><i class="bi bi-globe"></i></a>
                    </div>
                </div>

                <!-- Column 2: Navigation Links -->
                <div>
                    <h5 style="color: #ffffff; font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 18px; position: relative; padding-bottom: 8px;">
                        Quick Navigation
                        <span style="position: absolute; bottom: 0; left: 0; width: 30px; height: 2.5px; background: var(--primary-color, #4F46E5); border-radius: 4px;"></span>
                    </h5>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 13.5px;">
                        <li><a href="/" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-chevron-right" style="font-size: 10px; color: var(--primary-color);"></i> Dashboard</a></li>
                        <li><a href="/list" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-chevron-right" style="font-size: 10px; color: var(--primary-color);"></i> Student Directory</a></li>
                        <li><a href="/teachers" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-chevron-right" style="font-size: 10px; color: var(--primary-color);"></i> Faculty Directory</a></li>
                        <li><a href="/attendance" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-chevron-right" style="font-size: 10px; color: var(--primary-color);"></i> Attendance Log</a></li>
                        <li><a href="/results" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-chevron-right" style="font-size: 10px; color: var(--primary-color);"></i> Examination Results</a></li>
                    </ul>
                </div>

                <!-- Column 3: Portals & Resources -->
                <div>
                    <h5 style="color: #ffffff; font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 18px; position: relative; padding-bottom: 8px;">
                        System Portals
                        <span style="position: absolute; bottom: 0; left: 0; width: 30px; height: 2.5px; background: var(--primary-color, #4F46E5); border-radius: 4px;"></span>
                    </h5>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 13.5px;">
                        <li><a href="/student/portal" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-mortarboard" style="color: var(--primary-color);"></i> Student Portal</a></li>
                        <li><a href="/teacher/portal" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-person-video3" style="color: var(--primary-color);"></i> Teacher Portal</a></li>
                        <li><a href="/fee-structure" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-cash-stack" style="color: var(--primary-color);"></i> Fee Management</a></li>
                        <li><a href="/roll-slips" style="color: #cbd5e1; text-decoration: none; transition: color 0.2s;"><i class="bi bi-card-checklist" style="color: var(--primary-color);"></i> Roll Slips & Date Sheet</a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact & Support -->
                <div>
                    <h5 style="color: #ffffff; font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 18px; position: relative; padding-bottom: 8px;">
                        Contact & Support
                        <span style="position: absolute; bottom: 0; left: 0; width: 30px; height: 2.5px; background: var(--primary-color, #4F46E5); border-radius: 4px;"></span>
                    </h5>
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px; color: #94a3b8;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <i class="bi bi-geo-alt-fill" style="color: var(--primary-color); font-size: 16px; margin-top: 2px;"></i>
                            <span>AKM College Main Campus, Educational District, Pakistan</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="bi bi-envelope-fill" style="color: var(--primary-color); font-size: 15px;"></i>
                            <a href="mailto:support@akm.edu.pk" style="color: #cbd5e1; text-decoration: none;">support@akm.edu.pk</a>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="bi bi-telephone-fill" style="color: var(--primary-color); font-size: 15px;"></i>
                            <span style="color: #cbd5e1;">+92 (042) 111-222-333</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Sub-Footer -->
            <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; font-size: 12.5px; color: #64748b;">
                <div>
                    © {{ date('Y') }} <strong>AKM Student Management System</strong>. All rights reserved. Designed for optimal academic workflow.
                </div>
                <div style="display: flex; gap: 18px;">
                    <a href="#" style="color: #64748b; text-decoration: none; transition: color 0.2s;">Privacy Policy</a>
                    <a href="#" style="color: #64748b; text-decoration: none; transition: color 0.2s;">Terms of Service</a>
                    <a href="#" style="color: #64748b; text-decoration: none; transition: color 0.2s;">Security</a>
                </div>
            </div>
        </div>
    </footer>


    {{-- BOOTSTRAP JS --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>



    {{-- AUTO HIDE SUCCESS MESSAGES --}}


    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
    function fetchAdminAppNotifications() {
        fetch('/api/notifications')
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('adminNotifBadge');
                const list = document.getElementById('adminNotifListContainer');

                if (badge) {
                    if (data.unread > 0) {
                        badge.textContent = data.unread > 99 ? '99+' : data.unread;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                if (list) {
                    if (!data.notifications || data.notifications.length === 0) {
                        list.innerHTML = `
                            <div style="text-align: center; padding: 24px 16px; color: #94a3b8;">
                                <i class="bi bi-bell-slash-fill" style="font-size: 24px; color: #cbd5e1; display: block; margin-bottom: 6px;"></i>
                                <span style="font-size: 13px; font-weight: 700; color: #64748b;">No notifications yet</span>
                                <p style="font-size: 11px; margin: 4px 0 0;">You're all caught up with your updates!</p>
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    data.notifications.forEach(item => {
                        const bg = item.is_read ? '#ffffff' : '#EEF2FF';
                        const border = item.is_read ? '#f1f5f9' : '#C7D2FE';
                        const dot = item.is_read ? '' : `<span style="width:8px;height:8px;border-radius:50%;background:var(--primary-color,#4F46E5);display:inline-block;margin-left:6px;"></span>`;

                        html += `
                            <a href="${item.action_url || '#'}" onclick="markNotifReadAdmin(${item.id}, event, '${item.action_url || '#'}')" style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;background:${bg};border-bottom:1px solid ${border};text-decoration:none;transition:background 0.15s ease;">
                                <div style="width:34px;height:34px;border-radius:8px;background:#e0f2fe;color:#0369a1;display:grid;place-items:center;font-size:15px;flex-shrink:0;">
                                    <i class="bi ${item.icon || 'bi-bell-fill'}"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2px;">
                                        <strong style="font-size:12.5px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.title} ${dot}</strong>
                                        <small style="font-size:10px;color:#94a3b8;white-space:nowrap;margin-left:6px;">${item.time_ago}</small>
                                    </div>
                                    <p style="font-size:11.5px;color:#475569;margin:0;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${item.message}</p>
                                </div>
                            </a>
                        `;
                    });
                    list.innerHTML = html;
                }
            })
            .catch(err => console.error('Notification error:', err));
    }

    function markNotifReadAdmin(id, event, targetUrl) {
        if (event) event.preventDefault();
        fetch('/api/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id })
        }).finally(() => {
            if (targetUrl && targetUrl !== '#') {
                window.location.href = targetUrl;
            } else {
                fetchAdminAppNotifications();
            }
        });
    }

    function markAllNotificationsAsReadAdmin() {
        fetch('/api/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        }).then(() => fetchAdminAppNotifications());
    }

    document.addEventListener('DOMContentLoaded', () => {
        const bellBtn = document.getElementById('adminNotifBellBtn');
        const notifDropdown = document.getElementById('adminNotifDropdown');

        if (bellBtn && notifDropdown) {
            bellBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = notifDropdown.style.display === 'block';
                notifDropdown.style.display = isOpen ? 'none' : 'block';
                if (!isOpen) fetchAdminAppNotifications();
            });

            document.addEventListener('click', (e) => {
                if (!notifDropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                    notifDropdown.style.display = 'none';
                }
            });

            fetchAdminAppNotifications();
            setInterval(fetchAdminAppNotifications, 8000);
        }

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
        const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
        const mainContent = document.querySelector('.admin-main');
        if (sidebarCollapseBtn && sidebar) {
            sidebarCollapseBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('main-expanded');
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
