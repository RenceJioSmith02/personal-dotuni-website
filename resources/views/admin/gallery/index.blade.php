@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Gallery')

@section('content_header')
<h1>Gallery</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#galleryModal"
            data-form="#galleryForm"
            data-title="Add Gallery Item"
            data-url="{{ route('admin.gallery.store') }}">
            <i class="fas fa-plus"></i> Add Image
        </button>
    </div>

    <div class="card-body">
        <table id="galleryTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>File Name</th>
                    <th>Order</th>
                    <th width="180">Actions</th>
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

    if ($.fn.DataTable.isDataTable('#galleryTable')) {
        $('#galleryTable').DataTable().destroy();
    }

    $('#galleryTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ordering: true,
        ajax: {
            url: "{{ route('admin.gallery.index') }}",
            type: "GET"
        },
        columns: [
            { data: 'image', orderable: false, searchable: false },
            { data: 'file_name' },
            { data: 'sort_order' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});

/* DELETE GALLERY (AJAX) */
$(document).on("submit", ".ajax-delete-gallery", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#galleryTable").DataTable();

    Swal.fire({
        title: "Delete this image?",
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
                    text: res.message || "Gallery item deleted successfully",
                    timer: 1200,
                    showConfirmButton: false
                });

                table.row(row).remove().draw(false);
            },
            error: function (xhr) {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: xhr.responseJSON?.message || "Failed to delete image."
                });
            }
        });
    });
});
</script>
@endpush
