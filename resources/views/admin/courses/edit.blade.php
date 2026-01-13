@extends('adminlte::page')

@section('title', 'Edit Course')

@section('content_header')
    <h1>Edit Course</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.courses.update', $course) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code">Course Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $course->code) }}">
                @error('code')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="title">Course Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $course->title) }}">
                @error('title')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="units">Units</label>
                <input type="number" name="units" class="form-control" value="{{ old('units', $course->units) }}">
                @error('units')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $course->is_active)==1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $course->is_active)==0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop
