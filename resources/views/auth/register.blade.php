@extends('layouts.auth-saas')

@section('auth-content')
<h3 class="mb-3">Create account</h3>
<p class="text-muted mb-4">Get started in seconds</p>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-floating mb-3">
        <input type="text" name="name" class="form-control" placeholder="Name" required>
        <label>Full name</label>
    </div>

    <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <label>Email address</label>
    </div>

    <div class="form-floating mb-4">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <label>Password</label>
    </div>

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
