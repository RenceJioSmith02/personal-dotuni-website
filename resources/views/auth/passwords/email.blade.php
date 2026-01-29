@extends('layouts.auth-saas')

@section('auth-content')
<h3 class="mb-3">Reset password</h3>
<p class="text-muted mb-4">We’ll email you a reset link</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-floating mb-4">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <label>Email address</label>
    </div>

    <button class="btn btn-primary w-100">
        Send reset link
    </button>
</form>
@endsection
