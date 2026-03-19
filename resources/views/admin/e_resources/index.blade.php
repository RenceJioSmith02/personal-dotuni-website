@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'E-Resources')

@section('content_header')
    <div class="card-header">
        <h3 class="card-title-dt">E-Resources</h3>
        <div class="card-header-actions">
            <button
                class="open-modal btn btn-primary"
                data-action="add"
                data-modal="#eResourceModal"
                data-form="#eResourceForm"
                data-title="Add E-Resource"
                data-url="{{ route('admin.e_resources.store') }}">
                <i class="fas fa-plus"></i> Add E-Resource
            </button>
        </div>
    </div>
@stop

@section('content')

<div class="card">

    <div class="card-body">
        <table id="eResourcesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th with="10">#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Link</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="100">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

@include('admin.e_resources.partials.e-resources-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#eResourcesTable')) {
        $('#eResourcesTable').DataTable().destroy();
    }

    const table = $('#eResourcesTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,       
        pageLength: 10,
        scrollX: true,
        scrollCollapse: true,  
        order: [[1, 'asc']],
        ajax: {
            url: "{{ route('admin.e_resources.index') }}",
            type: "GET",
            dataSrc: function(json) {
                console.log('Eresources returned:', json.data.length);
                return json.data;
            }
        },
        columns: [
            {       
                data: null,
                searchable: false,
                orderable: false,
                textAlign: 'center',
                render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'name' },
            { data: 'description' },
            { data: 'link_url', orderable: false, searchable: false },
            { data: 'sort_order' },
            { data: 'status', orderable: false, searchable: false },
            {
                data: "created_at",
                render: (data) =>
                    new Date(data).toLocaleString()
            },
            {
                data: "updated_at",
                render: (data) =>
                    new Date(data).toLocaleString()
            },
            { data: 'actions', orderable: false, searchable: false },
        ]
    });

});


/* DELETE E-RESOURCE (AJAX) */
$(document).on("submit", ".ajax-delete-resource", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#eResourcesTable").DataTable();

    Swal.fire({
        title: "Delete this E-Resource?",
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
            error: function () {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: "Failed to delete E-Resource."
                });
            }
        });
    });
});

$(document).on("submit", ".ajax-archive-e-resource", function (e) {
    e.preventDefault();

    const form      = $(this);
    const url       = form.attr("action");
    const isArchive = url.includes("/archive") && !url.includes("/unarchive");
    const table     = $("#eResourcesTable").DataTable(); // ✅ update to your table ID

    Swal.fire({
        title: isArchive ? "Archive this E-Resource?" : "Unarchive this E-Resource?",
        text: isArchive
            ? "This will mark the E-Resource as inactive and archived."
            : "This will restore the E-Resource and mark it as active.",
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
</script>
@endpush
