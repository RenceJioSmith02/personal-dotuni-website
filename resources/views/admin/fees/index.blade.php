@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Fees')

@section('content_header')
<h1>Fees</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#feeModal"
            data-form="#feeForm"
            data-title="Add Fee"
            data-url="{{ route('admin.fees.store') }}">
            <i class="fas fa-plus"></i> Add Fee
        </button>
    </div>

    <div class="card-body">
        <table id="feeTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Title</th>
                    <th>Caption</th>
                    <th>Order</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

@include('admin.fees.partials.fee-modal')

@stop

@push('js')
<script>

$(function () {

    if ($.fn.DataTable.isDataTable('#feeTable')) {
        $('#feeTable').DataTable().destroy();
    }

    $('#feeTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: "{{ route('admin.fees.index') }}",

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
            { data: 'title' },
            { data: 'caption', orderable: false },
            { data: 'sort_order' },
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
            { data: 'status',   orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

});


/* DELETE FEE (AJAX) */
$(document).on("submit", ".ajax-delete-fee", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#feeTable").DataTable();

    Swal.fire({
        title: "Delete this fee?",
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
                    text: "Failed to delete fee."
                });
            }
        });
    });
});


$(document).on("submit", ".ajax-archive-fee", function (e) {
    e.preventDefault();

    const form      = $(this);
    const url       = form.attr("action");
    const isArchive = url.includes("/archive") && !url.includes("/unarchive");
    const table     = $("#feeTable").DataTable(); // ✅ update to your table ID

    Swal.fire({
        title: isArchive ? "Archive this fee?" : "Unarchive this fee?",
        text: isArchive
            ? "This will mark the fee as inactive and archived."
            : "This will restore the fee and mark it as active.",
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
