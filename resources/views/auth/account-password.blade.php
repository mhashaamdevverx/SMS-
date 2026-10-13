@extends('layout')

@push('page-css')
    <link rel="stylesheet" href="{{ asset('css/account-password.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="account-password-page">
    <div class="account-password-backdrop" aria-hidden="true"></div>
    <section class="account-password-card" role="alertdialog" aria-modal="true" aria-labelledby="account-password-title" aria-describedby="account-password-copy">
        <form action="{{ route('account.password.dismiss') }}" method="POST" class="account-password-dismiss-form">
            @csrf
            <button type="submit" class="account-password-dismiss" aria-label="Close password reminder and continue to dashboard" title="Do this later"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
        </form>
        <span class="account-password-icon"><i class="bi bi-exclamation-shield-fill" aria-hidden="true"></i></span>
        <p class="account-password-kicker">ACCOUNT SECURITY</p>
        <h1 id="account-password-title">Password update required</h1>
        <p id="account-password-copy">Your temporary password is still active. Update it now, or close this reminder and continue to the school portal.</p>
        @if($errors->any())<div class="field-error has-error">{{ $errors->first() }}</div>@endif
        <form action="{{ route('account.password.update') }}" method="POST" class="account-password-form">
            @csrf
            @method('PUT')
            <label for="current-password">Current temporary password</label>
            <input id="current-password" type="password" name="current_password" autocomplete="current-password" required autofocus>
            <label for="new-password">New password</label>
            <input id="new-password" type="password" name="password" minlength="10" autocomplete="new-password" required>
            <label for="confirm-password">Confirm new password</label>
            <input id="confirm-password" type="password" name="password_confirmation" minlength="10" autocomplete="new-password" required>
            <button type="submit" class="student-add-btn">Update password</button>
        </form>
    </section>
</div>
@endsection