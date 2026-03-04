@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Linkages')

@section('content_header')
    <h1>Linkages</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Linkage -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#linkageModal"
            data-form="#linkageForm"
            data-title="Add Linkage"
            data-url="/admin/linkages">
            <i class="fas fa-plus mr-1"></i> Add Linkage
        </button>

    </div>

    <div class="card-body">

        <table id="linkagesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
        </table>


    </div>
</div>

{{-- Linkage Modal --}}
@include('admin.linkage.linkages.partials.linkage-modal')

@stop


@push('js')
<script>


let table;

$(function () {

    if ($.fn.DataTable.isDataTable('#linkagesTable')) {
        $('#linkagesTable').DataTable().destroy();
    }

    table = $('#linkagesTable').DataTable({ // ✅ update to your table ID
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: "{{ route('admin.linkages.index') }}",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) =>
                    meta.row + meta.settings._iDisplayStart + 1
            },
            { data: 'title' },
            { data: 'category' },
            { data: 'url',     orderable: false },
            { data: 'status',  orderable: false, searchable: false }, // ✅ Add
            { data: 'created_at', render: (data) => new Date(data).toLocaleString() },
            { data: 'updated_at', render: (data) => new Date(data).toLocaleString() },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    // ✅ Delete handler
    $(document).on("submit", ".ajax-delete-linkage", function (e) {
        e.preventDefault();

        const form = $(this);
        const row  = form.closest("tr");

        Swal.fire({
            title: "Delete this linkage?",
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

    // ✅ Archive / Unarchive handler
    $(document).on("submit", ".ajax-archive-linkage", function (e) {
        e.preventDefault();

        const form      = $(this);
        const url       = form.attr("action");
        const isArchive = url.includes("/archive") && !url.includes("/unarchive");

        Swal.fire({
            title: isArchive ? "Archive this linkage?" : "Unarchive this linkage?",
            text: isArchive
                ? "This will mark the linkage as inactive and archived."
                : "This will restore the linkage and mark it as active.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: isArchive ? "Yes, archive it" : "Yes, unarchive it",
            cancelButtonText: "Cancel",
            confirmButtonColor: isArchive ? "#ffc107" : "#6c757d",
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    _method: "PATCH"
                },
                success: function (res) {
                    Swal.fire({
                        type: "success",
                        title: isArchive ? "Archived" : "Unarchived",
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    });

                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire({
                        type: "error",
                        title: "Action failed",
                        text: xhr.responseJSON?.message || "Something went wrong"
                    });
                }
            });
        });
    });

});


</script>
@endpush
