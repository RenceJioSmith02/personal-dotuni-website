@extends('adminlte::page')

@section('title', 'Edit Role')

@section('content_header')
    <h1>Edit Role</h1>
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

<form method="POST" action="{{ route('admin.roles.update', $role) }}">
@csrf
@method('PUT')

<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label>Role Name</label>
            <input name="name"
                   class="form-control"
                   value="{{ old('name', $role->name) }}"
                   required>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary">
            <i class="fas fa-save"></i> Update
        </button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>
</div>
</form>
@stop
