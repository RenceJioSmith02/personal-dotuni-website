@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Forms')

@section('content_header')
    <h1>Forms</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Form -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#formModal"
            data-form="#formForm"
            data-title="Add Form"
            data-url="/admin/forms">
            <i class="fas fa-plus mr-1"></i> Add Form
        </button>

    </div>

    <div class="card-body">

        <table id="formsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>File</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $form)
                <tr>
                    <td>
                        @if($form->file_url)
                            <a href="{{ $form->file_url }}" target="_blank">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $form->name }}</td>
                    <td>{{ $form->category->name ?? '-' }}</td>
                    <td>{{ strtoupper($form->asset->mime_type ?? '-') }}</td>
                    <td>
                        {!! $form->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>
                    <td>

                        <!-- Edit -->
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-id="{{ $form->id }}"
                            data-modal="#formModal"
                            data-form="#formForm"
                            data-title="Edit Form"
                            data-url="{{ route('admin.forms.index') }}">
                            Edit
                        </button>

                        <!-- Delete -->
                        <form
                            action="{{ route('admin.forms.destroy', $form) }}"
                            method="POST"
                            class="d-inline ajax-delete-form">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-sm btn-danger">
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

{{-- Form Modal --}}
@include('admin.form.forms.partials.form-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#formsTable')) {
        $('#formsTable').DataTable().destroy();
    }

    const table = $('#formsTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: 5 }
        ]
    });

    $(document).on("submit", ".ajax-delete-form", function (e) {
        e.preventDefault();

        const form = $(this);
        const row = form.closest("tr");

        Swal.fire({
            title: "Delete this form?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
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
                error: function (xhr) {
                    Swal.fire({
                        type: "error",
                        title: "Delete failed",
                        text: xhr.responseJSON?.message || "Something went wrong"
                    });
                }
            });
        });
    });

});
</script>
@endpush
