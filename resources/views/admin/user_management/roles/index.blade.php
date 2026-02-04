@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Roles')

@section('content_header')
    <h1>Role Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <!-- Add Role -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#roleModal"
            data-form="#roleForm"
            data-title="Add Role"
            data-url="/admin/roles">
            <i class="fas fa-plus mr-1"></i> Add Role
        </button>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table id="rolesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- Include Role Modal --}}
@include('admin.user_management.roles.partials.role-modal')

@stop

@push('js')
<script>
$(function () {
    /* ================================
       DataTable
    ================================ */
    if ($.fn.DataTable.isDataTable('#rolesTable')) {
        $('#rolesTable').DataTable().destroy();
    }

    const table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.roles.index") }}',
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'name' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        responsive: true,
        autoWidth: false,
        pageLength: 10,
    });


    /* ================================
       AJAX DELETE ROLE
    ================================ */
    $(document).on("submit", ".ajax-delete-role", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr("action");
        const row = form.closest("tr");
        const table = $("#rolesTable").DataTable();

        Swal.fire({
            title: "Delete this role?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#dc3545",
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    _method: "DELETE"
                },
                success: function (res) {
                    Swal.fire({
                        type: "success",
                        title: "Deleted",
                        text: res.message || "Role deleted successfully",
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
