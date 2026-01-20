@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Programs')

@section('content_header')
<h1>Programs</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#programModal"
            data-form="#programForm"
            data-title="Add Program"
            data-url="{{ route('admin.programs.store') }}">
            <i class="fas fa-plus"></i> Add Program
        </button>
    </div>

    <div class="card-body">
        <table id="programsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Total Units</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $program)
                <tr data-id="{{ $program->id }}">
                    <td class="text-center">
                        @if ($program->image_url)
                            <img
                                src="{{ $program->image_url }}"
                                class="img-thumbnail"
                                style="max-width:50px;"
                                alt="{{ $program->title }}">
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
                        <button
                            class="open-modal btn btn-sm btn-warning"
                            data-action="edit"
                            data-id="{{ $program->id }}"
                            data-modal="#programModal"
                            data-form="#programForm"
                            data-title="Edit Program"
                            data-url="{{ route('admin.programs.index') }}">
                            Edit
                        </button>

                        <a href="{{ route('admin.academic.programs.builder', $program) }}"
                           class="btn btn-sm btn-info">
                            <i class="fas fa-cogs"></i>
                        </a>

                        <form
                            action="{{ route('admin.programs.destroy', $program) }}"
                            method="POST"
                            class="d-inline ajax-delete-program">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
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

@include('admin.academic.programs.partials.program-modal')

@stop



@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#programsTable')) {
        $('#programsTable').DataTable().destroy();
    }

    const table = $('#programsTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [0, 6] }]
    });

});

/* DELETE PROGRAM (AJAX) */
$(document).on("submit", ".ajax-delete-program", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#programsTable").DataTable();

    Swal.fire({
        title: "Delete this program?",
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
                    text: "Failed to delete program."
                });
            }
        });
    });
});


// $(document).on("change", "#programImageInput", function (e) {
//     const file = e.target.files[0];

//     if (!file) return;

//     const reader = new FileReader();
//     reader.onload = function (e) {
//         $("#programImagePreview").attr("src", e.target.result);
//     };
//     reader.readAsDataURL(file);
// });


</script>
@endpush
