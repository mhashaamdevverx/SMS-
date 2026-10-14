<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a New Password - Excellence School System</title>
    <link rel="icon" type="image/png" href="{{ asset('upload/Logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body>
<main class="login-wrapper">
    <div class="login-card-container">
        <section class="form-side">
            <div class="badge-logo-outer"><div class="badge-logo-inner"><img src="{{ asset('upload/Logo.png') }}" alt="Excellence School System Logo"></div></div>
            <h1 class="form-heading">Choose a new password</h1>
            <p class="form-subheading">Use at least 10 characters. You’ll sign in with it next time.</p>

            @if($errors->any())<div class="alert-box alert-error"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i><span>{{ $errors->first() }}</span></div>@endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <label for="reset-email" class="form-label">Account Email</label>
                    <div class="input-pill-wrapper"><i class="bi bi-envelope field-icon" aria-hidden="true"></i><input id="reset-email" type="email" name="email" class="input-pill" value="{{ old('email', $email) }}" autocomplete="email" required></div>
                </div>
                <div class="form-group">
                    <label for="new-password" class="form-label">New Password</label>
                    <div class="input-pill-wrapper"><i class="bi bi-lock field-icon" aria-hidden="true"></i><input id="new-password" type="password" name="password" class="input-pill" minlength="10" autocomplete="new-password" required></div>
                </div>
                <div class="form-group">
                    <label for="confirm-password" class="form-label">Confirm New Password</label>
                    <div class="input-pill-wrapper"><i class="bi bi-lock-fill field-icon" aria-hidden="true"></i><input id="confirm-password" type="password" name="password_confirmation" class="input-pill" minlength="10" autocomplete="new-password" required></div>
                </div>
                <button type="submit" class="btn-primary-submit"><span>Save new password</span><i class="bi bi-check2 fs-17" aria-hidden="true"></i></button>
            </form>

            <div class="form-footer-note"><a href="{{ route('login') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to sign in</a></div>
        </section>
        <aside class="hero-side" aria-hidden="true"><div class="hero-inner"><div class="hero-overlay"></div></div></aside>
    </div>
</main>
</body>
</html>