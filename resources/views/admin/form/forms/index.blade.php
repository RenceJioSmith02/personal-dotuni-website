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
                    <th>#</th>
                    <th>File</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
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

    $('#formsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.forms.index') }}",
            type: "GET"
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'file', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'category' },
            { data: 'type', searchable: false },
            { data: 'status', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
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
