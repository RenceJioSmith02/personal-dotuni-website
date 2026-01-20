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
                    <th>Name</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>
                        <!-- Edit -->
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-modal="#roleModal"
                            data-form="#roleForm"
                            data-title="Edit Role"
                            data-url="/admin/roles"
                            data-id="{{ $role->id }}">
                            Edit
                        </button>

                        <!-- Delete -->
                        <form
                            action="{{ route('admin.roles.destroy', $role) }}"
                            method="POST"
                            class="d-inline ajax-delete-role">
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

    $('#rolesTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: 1 }]
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
