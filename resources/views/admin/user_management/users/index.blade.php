
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
                    <th>Email</th>
                    <th>Name</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge badge-info">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        {!! $user->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>
                    <td>

                        <!-- Edit -->
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-modal="#userModal"
                            data-form="#userForm"
                            data-title="Edit User"
                            data-url="/admin/users"
                            data-id="{{ $user->id }}">
                            Edit
                        </button>

                        <!-- Delete -->
                        <form
                            action="{{ route('admin.users.destroy', $user) }}"
                            method="POST"
                            class="d-inline ajax-delete-user">
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

    $('#usersTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: 4 }
        ]
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
