@extends('adminlte::page')

@section('title', 'Edit Program')

@section('content_header')
<h1>Edit Program</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.programs.update', $program) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Program Title</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                       id="title" value="{{ old('title', $program->title) }}" placeholder="Enter program title">
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">Program Type</label>
                <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" 
                       id="type" value="{{ old('type', $program->type) }}" placeholder="Enter program type">
                @error('type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="total_units">Total Units</label>
                <input type="number" name="total_units" class="form-control @error('total_units') is-invalid @enderror" 
                       id="total_units" value="{{ old('total_units', $program->total_units) }}" placeholder="Enter total units">
                @error('total_units')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">Status</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $program->is_active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $program->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Program</button>
            <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop
