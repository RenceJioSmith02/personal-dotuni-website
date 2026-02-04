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
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>URL</th>
                    <th>Status</th>
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
$(function () {

    /* ================================
     * DataTable
     * ================================ */
    if ($.fn.DataTable.isDataTable('#linkagesTable')) {
        $('#linkagesTable').DataTable().destroy();
    }

    const table = $('#linkagesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 20, 50, 100],

        ajax: {
            url: "{{ route('admin.linkages.index') }}",
            type: "GET",
            // dataSrc: function (json) {
            //     console.log('Linkages returned:', json.data.length);
            //     return json.data;
            // }
        },

        ajax: {
            url: "{{ route('admin.linkages.index') }}",
            type: "GET",
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
            {
                data: "image",
                orderable: false,
                searchable: false
            },
            { data: "title" },
            { data: "category" },
            {
                data: "url",
                render: (data) =>
                    `<a href="${data}" target="_blank">${data.substring(0,40)}</a>`
            },
            {
                data: "status",
                render: (data) =>
                    data
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>'
            },
            {
                data: "actions",
                orderable: false,
                searchable: false
            }
        ]
    });



    /* ================================
     * AJAX DELETE LINKAGE
     * ================================ */
    $(document).on("submit", ".ajax-delete-linkage", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr("action");
        const row = form.closest("tr");

        Swal.fire({
            title: "Delete this linkage?",
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
                        text: res.message || "Linkage deleted successfully",
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
