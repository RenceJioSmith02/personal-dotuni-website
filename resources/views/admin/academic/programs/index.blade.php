@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Programs')

@section('content_header')
    <div class="card-header">
        <h3 class="card-title-dt">Programs</h3>
        <div class="card-header-actions">
            <button
                class="open-modal btn btn-primary"
                data-action="add"
                data-modal="#programModal"
                data-form="#programForm"
                data-title="Add Program"
                data-url="{{ route('admin.programs.store') }}">
                <i class="fas fa-plus"></i> Add Program
            </button>
        </div>
    </div>
@stop

@section('content')

<div class="card">
    <div class="card-body">
        <table id="programsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Total Units</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="100">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

@include('admin.academic.programs.partials.program-modal')

@stop



@push('js')
<script>
$(function () {
    if ($.fn.DataTable.isDataTable('#programsTable')) {
        $('#programsTable').DataTable().destroy();
    }

    const table = $('#programsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        autoWidth: true,
        scrollCollapse: true,
        scrollX: true,
        order: [[1, 'asc']],
        lengthMenu: [10, 20, 50, 100],
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ],
        ajax: {
            url: "{{ route('admin.programs.index') }}",
            type: "GET",
            // dataSrc: function (json) {
            //     console.log('Programs returned:', json.data.length);
            //     return json.data;
            // }
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                textAlign: "center",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'title' },
            { data: 'description' },
            { data: 'type' },
            { data: 'total_units' },
            { data: 'status', searchable: false, orderable: false },
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
            { data: 'actions', searchable: false, orderable: false }
        ]
    });
});


/* DELETE PROGRAM (AJAX) */
$(document).on("submit", ".ajax-delete-program", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#programsTable").DataTable();

    Swal.fire({
        title: "Delete this program?",
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
                    title: "Error",
                    text: xhr.responseJSON?.message || "Failed to delete program."
                });
            }
        });
    });
});

$(document).on("submit", ".ajax-archive-program", function (e) {
    e.preventDefault();

    const form      = $(this);
    const url       = form.attr("action");
    const isArchive = url.includes("/archive") && !url.includes("/unarchive");
    const table     = $("#programsTable").DataTable(); // ✅ update to your table ID

    Swal.fire({
        title: isArchive ? "Archive this program?" : "Unarchive this program?",
        text: isArchive
            ? "This will mark the program as inactive and archived."
            : "This will restore the program and mark it as active.",
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
