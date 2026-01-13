@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User</h1>
@stop

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif



<form method="POST" action="{{ route('admin.users.update', $user) }}">
@csrf
@method('PUT')

<div class="card">
    <div class="card-body">

        {{-- Email --}}
        <div class="form-group">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email', $user->email) }}"
                   required>
        </div>

        {{-- Display Name --}}
        <div class="form-group">
            <label>Display Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name', $user->name) }}">
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
            <input type="password"
                   name="password"
                   class="form-control">
        </div>

        {{-- Status --}}
        <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $user->is_active ? 'selected' : '' }}>
                    Active
                </option>
                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </div>

        {{-- Roles --}}
        @if(auth()->id() !== $user->id)
            <div class="form-group">
                <label>Roles</label>

                @foreach($roles as $role)
                    <div class="form-check">
                        <input type="checkbox"
                            class="form-check-input"
                            name="roles[]"
                            value="{{ $role->id }}"
                            {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                        <label class="form-check-label">
                            {{ $role->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update
        </button>

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>
</div>
</form>
@stop
