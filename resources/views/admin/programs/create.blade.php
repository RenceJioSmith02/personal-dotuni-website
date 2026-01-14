@extends('adminlte::page')

@section('title', 'Add Program')

@section('content_header')
<h1>Add Program</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="image">Program Image</label>
                <input type="file"
                    name="image"
                    id="image"
                    class="form-control-file @error('image') is-invalid @enderror"
                    accept="image/*">

                @error('image')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="title">Program Title</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                       id="title" value="{{ old('title') }}" placeholder="Enter program title">
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                       id="description" value="{{ old('description') }}" placeholder="Enter program description">
                @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">Program Type</label>
                <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" 
                       id="type" value="{{ old('type') }}" placeholder="Enter program type">
                @error('type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="total_units">Total Units</label>
                <input type="number" name="total_units" class="form-control @error('total_units') is-invalid @enderror" 
                       id="total_units" value="{{ old('total_units') }}" placeholder="Enter total units">
                @error('total_units')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">Status</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Create Program</button>
            <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop
