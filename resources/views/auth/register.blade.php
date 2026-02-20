@extends('layouts.auth-saas')

@section('auth-content')
<h3 class="mb-3">Create account</h3>
<p class="text-muted mb-4">Get started in seconds</p>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Name -->
    <div class="form-floating mb-3">
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               placeholder="Name" value="{{ old('name') }}" required>
        <label>Full name</label>
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Email -->
    <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               placeholder="Email" value="{{ old('email') }}" required>
        <label>Email address</label>
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Password -->
    <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               placeholder="Password" required>
        <label>Password</label>
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="form-floating mb-4">
        <input type="password" name="password_confirmation" class="form-control"
               placeholder="Confirm Password" required>
        <label>Confirm Password</label>
    </div>

    <!-- Submit -->
    <button class="btn btn-primary w-100">
        Create Account
    </button>

    <div class="text-center mt-3">
        <span class="text-muted">Already have an account?</span>
        <a href="{{ route('login') }}" class="fw-semibold">
            Sign in
        </a>
    </div>

</form>
@endsection