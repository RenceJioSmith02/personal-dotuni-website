@extends('layouts.admin')  

@section('title', 'Settings — Change Password')

@section('content_header')
    <h1>Settings</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lock mr-2"></i> Change Password
                </h3>
            </div>

            <form action="{{ route('admin.settings.changePassword') }}" method="POST" novalidate>
                @csrf
                <div class="card-body">

                    {{-- Current Password --}}
                    <div class="form-group">
                        <label for="current_password">
                            Current Password <span style="color:#b42318">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="Enter current password"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" tabindex="-1" aria-label="Show password"
                                onclick="togglePw('current_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="form-group">
                        <label for="password">
                            New Password <span style="color:#b42318">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Min. 8 characters"
                                autocomplete="new-password"
                                oninput="onNewPasswordInput()"
                                required
                            >
                            <button type="button" tabindex="-1" aria-label="Show password"
                                onclick="togglePw('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        {{-- Strength Bar --}}
                        <div class="strength-bar-wrap mt-2">
                            <div class="strength-bar-track">
                                <div id="strength-bar" class="strength-bar-fill"></div>
                            </div>
                            <span id="strength-label" class="strength-label"></span>
                        </div>

                        {{-- Requirements checklist --}}
                        <ul class="pw-requirements mt-2" id="pw-requirements">
                            <li id="req-length">  <i class="fas fa-times req-icon"></i> At least 8 characters</li>
                            <li id="req-upper">   <i class="fas fa-times req-icon"></i> One uppercase letter (A–Z)</li>
                            <li id="req-lower">   <i class="fas fa-times req-icon"></i> One lowercase letter (a–z)</li>
                            <li id="req-number">  <i class="fas fa-times req-icon"></i> One number (0–9)</li>
                            <li id="req-special"> <i class="fas fa-times req-icon"></i> One special character (!@#$…)</li>
                        </ul>
                    </div>

                    {{-- Confirm New Password --}}
                    <div class="form-group">
                        <label for="password_confirmation">
                            Confirm New Password <span style="color:#b42318">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Repeat new password"
                                autocomplete="new-password"
                                oninput="onConfirmInput()"
                                required
                            >
                            <button type="button" tabindex="-1" aria-label="Show password"
                                onclick="togglePw('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="confirm_match_msg" class="pw-feedback"></div>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" id="submit-btn" class="btn btn-primary" disabled>
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@stop

@push('css')
<style>
    /* ── Password wrapper ── */
    .password-wrapper {
        position: relative;
    }
    .password-wrapper .form-control {
        padding-right: 44px;
    }
    .password-wrapper > button {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: #6c757d;
        font-size: 15px;
        line-height: 1;
        z-index: 10;
        transition: color 0.15s;
    }
    .password-wrapper > button:hover { color: #038303; }
    .password-wrapper > button:focus { outline: none; box-shadow: none; }

    /* ── Strength bar ── */
    .strength-bar-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .strength-bar-track {
        flex: 1;
        height: 6px;
        background: #e0e0e0;
        border-radius: 999px;
        overflow: hidden;
    }
    .strength-bar-fill {
        height: 100%;
        width: 0%;
        border-radius: 999px;
        transition: width 0.35s ease, background 0.35s ease;
    }
    .strength-label {
        font-size: 12px;
        font-weight: 700;
        min-width: 60px;
        text-align: right;
    }

    /* ── Requirements list ── */
    .pw-requirements {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .pw-requirements li {
        font-size: 12.5px;
        color: #9aaa9a;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: color 0.2s;
    }
    .pw-requirements li .req-icon {
        font-size: 11px;
        width: 12px;
        text-align: center;
        transition: color 0.2s;
        color: #c0c0c0;
    }
    .pw-requirements li.met {
        color: #1e7f3f;
    }
    .pw-requirements li.met .req-icon {
        color: #1e7f3f;
    }
    .pw-requirements li.unmet {
        color: #b42318;
    }
    .pw-requirements li.unmet .req-icon {
        color: #b42318;
    }

    /* ── Match feedback ── */
    .pw-feedback {
        font-size: 12.5px;
        font-weight: 600;
        margin-top: 5px;
        min-height: 18px;
    }
    .pw-feedback.match    { color: #1e7f3f; }
    .pw-feedback.no-match { color: #b42318; }

    /* ── Disabled submit ── */
    #submit-btn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        pointer-events: auto;
    }
</style>
@endpush

@push('js')
<script>
    /* ── Toggle show/hide ── */
    function togglePw(id, btn) {
        var inp = document.getElementById(id);
        var ico = btn.querySelector('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            inp.type = 'password';
            ico.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    /* ── Rules ── */
    var rules = [
        { id: 'req-length',  test: function(v){ return v.length >= 8; } },
        { id: 'req-upper',   test: function(v){ return /[A-Z]/.test(v); } },
        { id: 'req-lower',   test: function(v){ return /[a-z]/.test(v); } },
        { id: 'req-number',  test: function(v){ return /[0-9]/.test(v); } },
        { id: 'req-special', test: function(v){ return /[^A-Za-z0-9]/.test(v); } },
    ];

    var strengthConfig = [
        { label: '',       color: '',        width: '0%'   },
        { label: 'Weak',   color: '#e53935', width: '20%'  },
        { label: 'Weak',   color: '#e53935', width: '40%'  },
        { label: 'Fair',   color: '#f59e0b', width: '60%'  },
        { label: 'Good',   color: '#3b82f6', width: '80%'  },
        { label: 'Strong', color: '#1e7f3f', width: '100%' },
    ];

    function checkSubmitState() {
        var newPw     = document.getElementById('password').value;
        var confirmPw = document.getElementById('password_confirmation').value;
        var btn       = document.getElementById('submit-btn');

        var allRulesMet = rules.every(function(rule){ return rule.test(newPw); });
        var isMatch     = confirmPw.length > 0 && newPw === confirmPw;

        btn.disabled = !(allRulesMet && isMatch);
    }

    function onNewPasswordInput() {
        var val   = document.getElementById('password').value;
        var score = 0;

        rules.forEach(function(rule) {
            var li  = document.getElementById(rule.id);
            var ico = li.querySelector('.req-icon');
            var met = rule.test(val);

            if (met) {
                score++;
                li.className = 'met';
                ico.classList.replace('fa-times', 'fa-check');
            } else {
                li.className = val.length > 0 ? 'unmet' : '';
                ico.classList.replace('fa-check', 'fa-times');
            }
        });

        var cfg = val.length === 0 ? strengthConfig[0] : strengthConfig[score];
        var bar = document.getElementById('strength-bar');
        var lbl = document.getElementById('strength-label');
        bar.style.width      = cfg.width;
        bar.style.background = cfg.color;
        lbl.textContent      = cfg.label;
        lbl.style.color      = cfg.color;

        onConfirmInput();
    }

    function onConfirmInput() {
        var newPw     = document.getElementById('password').value;
        var confirmPw = document.getElementById('password_confirmation').value;
        var msg       = document.getElementById('confirm_match_msg');

        if (confirmPw.length === 0) {
            msg.textContent = '';
            msg.className   = 'pw-feedback';
        } else if (newPw === confirmPw) {
            msg.textContent = '✓ Passwords match';
            msg.className   = 'pw-feedback match';
        } else {
            msg.textContent = '✗ Passwords do not match';
            msg.className   = 'pw-feedback no-match';
        }

        checkSubmitState();
    }
</script>
@endpush