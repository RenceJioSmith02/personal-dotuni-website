@extends('adminlte::page')

@section('title', 'Add Requirement Category')

@section('content_header')
    <h1>Add Requirement Category</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.program_requirement_categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 1) }}" required>
                @error('sort_order') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Save
            </button>
            <a href="{{ route('admin.program_requirement_categories.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop
