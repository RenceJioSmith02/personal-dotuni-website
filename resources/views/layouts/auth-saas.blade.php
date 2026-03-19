@extends('adminlte::auth.auth-page')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/auth-saas.css') }}">
@endpush

@section('auth_body')

<div class="auth-saas">

    {{-- ── LEFT: Brand Panel ── --}}
    <div class="auth-saas-left">
        <div class="auth-saas-left-inner">  
            <div class="brand">

                <div class="brand-logo">
                    <div
                        class="navbar-header-logo-container"
                        id="navbar-header-logo-container"
                    >
                        <img
                            src="{{ asset('assets/system_images/logo.png') }}"
                            alt="DOTUNI Logo"
                            class="img-fluid navbar-header-logo"
                            style="width: 80px"
                        />
                    </div>

                    <div
                        class="navbar-header-logo-container"
                        id="navbar-header-logo-container"
                    >
                        <img
                            src="{{ asset('assets/system_images/clsu.png') }}"
                            alt="CLSU Logo"
                            class="img-fluid navbar-header-logo"
                            style="width: 80px"
                        />
                    </div>
                
                </div>

                <h2>Distance, Open, and Transnational </br> University</h2>
                <p>Content Management System</p>

                <div class="brand-features">
                    <div class="feature-pill">
                        <span class="pill-dot"><i class="fas fa-users"></i></span>
                        Unified user management
                    </div>
                    <div class="feature-pill">
                        <span class="pill-dot"><i class="fas fa-chart-line"></i></span>
                        Real-time data insights
                    </div>
                    <div class="feature-pill">
                        <span class="pill-dot"><i class="fas fa-shield-alt"></i></span>
                        Secure &amp; role-based access
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── RIGHT: Form Panel ── --}}
    <div class="auth-saas-right">
        <div class="auth-card">
            @yield('auth-content')
        </div>
    </div>

</div>

@endsection