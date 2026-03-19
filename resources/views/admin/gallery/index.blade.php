@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Gallery')

@section('content_header')
    <div class="card-header">
        <h3 class="card-title-dt">Gallery</h3>
        <div class="card-header-actions">
            <button
                class="open-modal btn btn-primary"
                data-action="add"
                data-modal="#galleryModal"
                data-form="#galleryForm"
                data-title="Add Gallery Item"
                data-url="{{ route('admin.gallery.store') }}">
                <i class="fas fa-plus mr-1"></i> Add Image
            </button>
        </div>
    </div>
@stop

@section('content')

<div class="card">


    <div class="card-body">
        <table id="galleryTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Image</th>
                    <th>File Name</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="100px">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('admin.gallery.partials.gallery-modal')

@stop

@push('js')
<script>
$(function () {

    let table;

    if ($.fn.DataTable.isDataTable('#galleryTable')) {
        $('#galleryTable').DataTable().destroy();
    }

    table = $('#galleryTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        autoWidth: true,
        scrollCollapse: true,
        scrollX: true,
        order: [[3, 'asc']],
        ajax: "{{ route('admin.gallery.index') }}",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) =>
                    meta.row + meta.settings._iDisplayStart + 1
            },
            { data: 'image',      orderable: false, searchable: false },
            { data: 'file_name' },
            { data: 'sort_order' },
            { data: 'status',     orderable: false, searchable: false },
            { data: 'created_at', render: (data) => new Date(data).toLocaleString() },
            { data: 'updated_at', render: (data) => new Date(data).toLocaleString() },
            { data: 'actions',    orderable: false, searchable: false }
        ]
    });

    // Delete handler
    $(document).on("submit", ".ajax-delete-gallery", function (e) {
        e.preventDefault();

        const form = $(this);
        const row  = form.closest("tr");

        Swal.fire({
            title: "Delete this gallery item?",
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
                    Swal.fire({ type: "success", title: "Deleted", text: res.message, timer: 1200, showConfirmButton: false });
                    table.row(row).remove().draw(false);
                },
                error: function (xhr) {
                    Swal.fire({ type: "error", title: "Delete failed", text: xhr.responseJSON?.message || "Something went wrong" });
                }
            });
        });
    });

    // Archive / Unarchive handler
    $(document).on("submit", ".ajax-archive-gallery", function (e) {
        e.preventDefault();

        const form      = $(this);
        const url       = form.attr("action");
        const isArchive = url.includes("/archive") && !url.includes("/unarchive");

        Swal.fire({
            title: isArchive ? "Archive this item?" : "Unarchive this item?",
            text: isArchive
                ? "This will mark the item as inactive and archived."
                : "This will restore the item and mark it as active.",
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
                    Swal.fire({ type: "success", title: isArchive ? "Archived" : "Unarchived", text: res.message, timer: 1200, showConfirmButton: false });
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire({ type: "error", title: "Action failed", text: xhr.responseJSON?.message || "Something went wrong" });
                }
            });
        });
    });

});
</script>
@endpush