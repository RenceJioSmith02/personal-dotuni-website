@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'E-Resources')

@section('content_header')
<h1>E-Resources</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#eResourceModal"
            data-form="#eResourceForm"
            data-title="Add E-Resource"
            data-url="{{ route('admin.e_resources.store') }}">
            <i class="fas fa-plus"></i> Add E-Resource
        </button>
    </div>

    <div class="card-body">
        <table id="eResourcesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Link</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resources as $item)
                <tr data-id="{{ $item->id }}">
                    <td>{{ $item->name }}</td>

                    <td>{{ Str::limit($item->description, 80) }}</td>

                    <td>
                        @if($item->link_url)
                            <a href="{{ $item->link_url }}" target="_blank">View</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>{{ $item->sort_order }}</td>

                    <td>
                        {!! $item->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>

                    <td>
                        <button
                            class="open-modal btn btn-sm btn-warning"
                            data-action="edit"
                            data-id="{{ $item->id }}"
                            data-modal="#eResourceModal"
                            data-form="#eResourceForm"
                            data-title="Edit E-Resource"
                            data-url="{{ route('admin.e_resources.index') }}">
                            Edit
                        </button>

                        <form
                            action="{{ route('admin.e_resources.destroy', $item) }}"
                            method="POST"
                            class="d-inline ajax-delete-resource">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('admin.e_resources.partials.e-resources-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#eResourcesTable')) {
        $('#eResourcesTable').DataTable().destroy();
    }

    const table = $('#eResourcesTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [5] }] // actions column
    });

});

/* DELETE E-RESOURCE (AJAX) */
$(document).on("submit", ".ajax-delete-resource", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#eResourcesTable").DataTable();

    Swal.fire({
        title: "Delete this E-Resource?",
        text: "This action cannot be undone.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it",
        confirmButtonColor: "#dc3545",
        reverseButtons: true
    }).then((result) => {
        if (!result.value) return;

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                _method: "DELETE"
            },
            success: function (res) {
                Swal.fire({
                    type: "success",
                    title: "Deleted",
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });

                table.row(row).remove().draw(false);
            },
            error: function () {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: "Failed to delete E-Resource."
                });
            }
        });
    });
});
</script>
@endpush
