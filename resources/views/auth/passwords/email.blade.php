@extends('layouts.auth-saas')

@section('auth-content')
<h3 class="mb-3">Reset password</h3>
<p class="text-muted mb-4">We'll email you a reset link</p>

{{-- Success status --}}
@if (session('status'))
    <div class="alert alert-success mb-4">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-floating mb-3">
        <input
            type="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="Email"
            value="{{ old('email') }}"
            required
            autofocus
        >
        <label>Email address</label>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-primary w-100">
        Send reset link
    </button>
</form>
@endsection