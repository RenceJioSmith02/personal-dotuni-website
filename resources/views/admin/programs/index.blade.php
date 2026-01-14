@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('title', 'Programs')

@section('content_header')
<h1>Programs</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Program
        </a>
    </div>

    <div class="card-body">
        <table id="programsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Program Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Total Units</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $program)
                <tr>
                    <td class="text-center">
                        @if ($program->asset)
                            <a href="{{ asset('storage/' . $program->asset->storage_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $program->asset->storage_path) }}"
                                    alt="{{ $program->asset->alt_text ?? $program->title }}"
                                    class="img-thumbnail"
                                    style="max-width: 100px; max-height: 70px;">
                            </a>
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>
                    <td>{{ $program->title }}</td>
                    <td>{{ $program->description }}</td>
                    <td>{{ $program->type }}</td>
                    <td>{{ $program->total_units }}</td>
                    <td>
                        {!! $program->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>
                    <td>
                        <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <a href="{{ route('admin.programs.builder', $program) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-cogs"></i> Build
                        </a>

                        <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete program?')">
                                Delete
                            </button>
                        </form>
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        $('#programsTable').DataTable({
            responsive: true,
            autoWidth: false,
            ordering: true,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ]
        });
    });
</script>
@stop