<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Excellence School System</title>

    <link rel="icon" type="image/png" href="{{ asset('upload/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('upload/Logo.png') }}">

    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    
</head>

<body>

    <div class="signup-split-wrapper">

        <!-- Page Top-Right Help Icon -->
        <a href="{{ route('support') }}" class="help-corner-btn" title="Help Desk & Support Center" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">?</a>

        <div class="signup-card-container">

            <!-- LEFT FORM PANE -->
            <div class="signup-form-pane">

                <div class="signup-header-area">
                    <div class="akm-logo-container">
                        <img src="{{ asset('upload/Logo.png') }}" alt="Excellence School System Logo" class="akm-logo-img">
                    </div>
                    <h1>Excellence School System</h1>
                    <p>Request teacher portal access. The administrator must approve your account and send your temporary sign-in details.</p>
                </div>

                {{-- SUCCESS MESSAGE --}}
                @if(session('success'))
                    <div class="signup-success-alert">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="/signup" method="POST" enctype="multipart/form-data" id="signupForm" class="signup-form-grid" novalidate>
                    @csrf

                    <!-- Full Name -->
                    <div class="form-field-item">
                        <label for="signup-name">Full Name <span class="text-danger">*</span></label>
                        <div class="input-icon-box">
                            <span class="input-icon-left">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input id="signup-name" type="text" name="name" placeholder="Enter your full name" value="{{ old('name') }}" class="input-styled @error('name') input-error @enderror" required>
                        </div>
                        @error('name')<span class="field-error-text">{{ $message }}</span>@enderror
                    </div>

                    <!-- Email Address -->
                    <div class="form-field-item">
                        <label for="signup-email">Email Address <span class="text-danger">*</span></label>
                        <div class="input-icon-box">
                            <span class="input-icon-left">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input id="signup-email" type="email" name="email" placeholder="ali.khan@gmail.com" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Please enter a valid Gmail address (e.g. ali.khan@gmail.com)" value="{{ old('email') }}" class="input-styled @error('email') input-error @enderror" required>
                        </div>
                        @error('email')<span class="field-error-text">{{ $message }}</span>@enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="form-field-item">
                        <label for="signup-phone">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-icon-box">
                            <span class="input-icon-left">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <input id="signup-phone" type="text" name="phone" placeholder="Enter your phone number" value="{{ old('phone') }}" class="input-styled @error('phone') input-error @enderror">
                        </div>
                        @error('phone')<span class="field-error-text">{{ $message }}</span>@enderror
                    </div>

                    <!-- Profile Image (Optional) -->
                    <div class="form-field-item">
                        <label for="signup-image">Profile Image (Optional)</label>
                        <div class="file-input-wrapper">
                            <span class="input-icon-left">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <span class="file-text-holder" id="file-name-display">Choose file...</span>
                            <span class="file-browse-btn">Browse</span>
                            <input id="signup-image" type="file" name="image" accept="image/jpeg,image/png" >
                        </div>
                        @error('image')<span class="field-error-text">{{ $message }}</span>@enderror
                    </div>

                    <!-- Terms checkbox & Submit button -->
                    <div class="grid-col-full">
                        <div class="terms-row">
                            <input type="checkbox" id="terms" required checked>
                            <label for="terms">I agree to the <a href="#">Terms of Service</a> & <a href="#">Privacy Policy</a></label>
                        </div>

                        <button type="submit" class="create-btn">
                            Create Account
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                </form>

                <p class="bottom-login-link">
                    Already have an account?
                    <a href="/login">Login</a>
                </p>

            </div>

            <!-- RIGHT VISUAL SHOWCASE PANE -->
            <div class="signup-visual-pane">

                <!-- Floating Top Right Badge -->
                <div class="badge-card-overlay">
                    <div class="badge-card-header">
                        <span class="rating-pill">★ Top Academic Rating</span>
                        <span class="tier-tag">TIER 1</span>
                    </div>
                    <div class="badge-card-title">Dean's Honor Roll System</div>
                    <div class="badge-card-stat">
                        <span>Graduation Success</span>
                        <strong>98.4%</strong>
                    </div>
                </div>

                <!-- Quote Overlay Card Matching User's Image Reference -->
                <div class="quote-box-overlay">
                    <div class="quote-circle-badge">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h3.983v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                    </div>
                    <p class="quote-box-text">
                        "Empowering future leaders through academic excellence, innovation, and holistic student development."
                    </p>
                    <div class="quote-card-footer">
                        <div class="quote-footer-left">
                            <div class="quote-avatar-stack">
                                <span class="quote-avatar-circle circle-1">SC</span>
                                <span class="quote-avatar-circle circle-2">ED</span>
                                <span class="quote-avatar-circle circle-3">★</span>
                            </div>
                            <span class="quote-footer-label">Student Council 2026</span>
                        </div>
                        <div class="quote-footer-badge">
                            Excellence Award
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>