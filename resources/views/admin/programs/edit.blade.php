@extends('adminlte::page')

@section('title', 'Edit Program')

@section('content_header')
<h1>Edit Program</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.programs.update', $program) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Program Image</label>

                {{-- Image Preview --}}
                <div class="mb-2">
                    @if ($program->asset)
                        <img id="imagePreview"
                            src="{{ asset('storage/' . $program->asset->storage_path) }}"
                            class="img-thumbnail"
                            style="max-height: 200px;">
                    @else
                        <img id="imagePreview"
                            src="https://via.placeholder.com/300x200?text=No+Image"
                            class="img-thumbnail"
                            style="max-height: 200px;">
                    @endif
                </div>

                {{-- Upload New Image --}}
                <div class="custom-file">
                    <input type="file"
                        name="image"
                        class="custom-file-input @error('image') is-invalid @enderror"
                        id="imageInput"
                        accept="image/*">

                    <label class="custom-file-label" for="imageInput">
                        Choose new image
                    </label>

                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <small class="form-text text-muted">
                    Uploading a new image will replace the existing one.
                </small>
            </div>


            <div class="form-group">
                <label for="title">Program Title</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                       id="title" value="{{ old('title', $program->title) }}" placeholder="Enter program title">
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                       id="description" value="{{ old('description', $program->description) }}" placeholder="Enter program description">
                @error('description')
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


@section('js')
<script>
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('imagePreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@stop
