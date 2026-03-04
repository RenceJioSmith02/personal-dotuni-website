@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'CLSU News')

@section('content_header')
<h1>CLSU News</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#clsuNewsModal"
            data-form="#clsuNewsForm"
            data-title="Add News"
            data-url="{{ route('admin.clsu_news.store') }}">
            <i class="fas fa-plus"></i> Add News
        </button>
    </div>

    <div class="card-body">
        <table id="clsuNewsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>URL</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

@include('admin.clsu.news.partials.clsu-news-modal')

@stop


@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#clsuNewsTable')) {
        $('#clsuNewsTable').DataTable().destroy();
    }

    const table = $('#clsuNewsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 20, 50, 100],
        ajax: {
            url: "{{ route('admin.clsu_news.index') }}",
            type: "GET",
            dataSrc: function(json) {
                console.log('CLSU News returned:', json.data.length);
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
            { data: 'title' },
            { data: 'description' },
            { data: 'url', orderable: false, searchable: false },
            { data: 'sort_order' },
            { data: 'status' },
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
            { data: 'actions', orderable: false, searchable: false }
        ]
    });


});

/* DELETE NEWS (AJAX) */
$(document).on("submit", ".ajax-delete-news", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#clsuNewsTable").DataTable();

    Swal.fire({
        title: "Delete this news?",
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
                    text: "Failed to delete news."
                });
            }
        });
    });
});


$(document).on("submit", ".ajax-archive-clsu-news", function (e) {
    e.preventDefault();

    const form      = $(this);
    const url       = form.attr("action");
    const isArchive = url.includes("/archive") && !url.includes("/unarchive");
    const table     = $("#clsuNewsTable").DataTable(); // ✅ update to your table ID

    Swal.fire({
        title: isArchive ? "Archive this news?" : "Unarchive this news?",
        text: isArchive
            ? "This will mark the news as inactive and archived."
            : "This will restore the news and mark it as active.",
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
