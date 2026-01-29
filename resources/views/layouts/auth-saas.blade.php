@extends('adminlte::auth.auth-page')

@section('auth_body')

<div class="auth-saas">
    <div class="auth-saas-left">
        <div class="brand">
            <h2>DOTUNI</h2>
            <p>Manage users, data, and content effortlessly.</p>
        </div>
    </div>

    <div class="auth-saas-right">
        <div class="auth-card">
            @yield('auth-content')
        </div>
    </div>
</div>

@endsection
