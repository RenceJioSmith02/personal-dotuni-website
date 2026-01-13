@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <h1>Create User</h1>
@stop

@section('content')
<form method="POST" action="{{ route('admin.users.store') }}">
@csrf

<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label>Email</label>
            <input name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Name</label>
            <input name="name" class="form-control">
        </div>

        <div class="form-group">
            <label>Roles</label>
            @foreach($roles as $role)
                <div class="form-check">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}">
                    {{ $role->name }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</div>
</form>
@stop
