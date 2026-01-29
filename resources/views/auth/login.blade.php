@extends('layouts.auth-saas')

@section('auth-content')
<h3 class="mb-3">Sign in</h3>
<p class="text-muted mb-4">Welcome back</p>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-floating mb-3 position-relative">
        <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
        <label>Email address</label>
    </div>

    <div class="form-floating mb-4 position-relative">
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        <label>Password</label>
        <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Sign In
    </button>

        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>

        @if (Route::has('register'))
        <div class="text-center mt-2">
            <span class="text-muted">Don’t have an account?</span>
            <a href="{{ route('register') }}" class="fw-semibold">
                Create one
            </a>
        </div>
        @endif


</form>
@endsection

@push('js')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
