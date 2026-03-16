@extends('layouts.auth-saas')

@section('auth-content')
<h3 class="mb-3">Set New Password</h3>
<p class="text-muted mb-4">Enter your new password below</p>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-floating mb-3">
        <input
            type="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="Email"
            value="{{ $email ?? old('email') }}"
            required
            autofocus
        >
        <label>Email address</label>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-floating mb-3">
        <input
            type="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="New Password"
            required
        >
        <label>New Password</label>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-floating mb-4">
        <input
            type="password"
            name="password_confirmation"
            class="form-control"
            placeholder="Confirm Password"
            required
        >
        <label>Confirm Password</label>
    </div>

    <button class="btn btn-primary w-100">
        Reset Password
    </button>
</form>
@endsection