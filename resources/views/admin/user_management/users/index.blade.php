
@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Users')

@section('content_header')
    <h1>User Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add User -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#userModal"
            data-form="#userForm"
            data-title="Add User"
            data-url="/admin/users">
            <i class="fas fa-plus mr-1"></i> Add User
        </button>

    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table id="usersTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- User Modal --}}
@include('admin.user_management.users.partials.user-modal')

@stop


@push('js')
<script>
$(function () {

    /* ================================
     * DataTable
     * ================================ */
    if ($.fn.DataTable.isDataTable('#usersTable')) {
        $('#usersTable').DataTable().destroy();
    }

    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.users.index") }}',
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'email' },
            { data: 'name' },
            { data: 'roles', orderable: false, searchable: false },
            { data: 'status', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ],
        responsive: true,
        autoWidth: false,
        pageLength: 10,
    });


    /* ================================
     * AJAX DELETE USER
     * ================================ */
    $(document).on("submit", ".ajax-delete-user", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr("action");
        const row = form.closest("tr");
        const table = $("#usersTable").DataTable();

        Swal.fire({
            title: "Delete this user?",
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
                        text: res.message || "User deleted successfully",
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
