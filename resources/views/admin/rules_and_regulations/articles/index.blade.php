@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Rule Articles')

@section('content_header')
    <div class="card-header">
        <h3 class="card-title-dt">Rule Articles</h3>
        <div class="card-header-actions">
            <button
                class="open-modal btn btn-primary"
                data-action="add"
                data-modal="#articleModal"
                data-form="#articleForm"
                data-title="Add Article"
                data-url="/admin/rule_articles">
                <i class="fas fa-plus mr-1"></i> Add Article
            </button>
        </div>
    </div>
@stop

@section('content')
<div class="card">

    <div class="card-body">
        <table id="articlesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Number</th>
                    <th>Title</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="100">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- Article Modal --}}
@include('admin.rules_and_regulations.articles.partials.article-modal')
@stop

@push('js')

<script>
    let table;

$(function () {

    if ($.fn.DataTable.isDataTable('#articlesTable')) {
        $('#articlesTable').DataTable().destroy();
    }

    table = $('#articlesTable').DataTable({ // ✅ update to your table ID
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: true,
        scrollCollapse: true,
        scrollX: true,
        pageLength: 10,
        ajax: "{{ route('admin.rule_articles.index') }}",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) =>
                    meta.row + meta.settings._iDisplayStart + 1
            },
            { data: 'number' },
            { data: 'title' },
            { data: 'sort_order' },
            { data: 'status',     orderable: false, searchable: false },
            { data: 'created_at', render: (data) => new Date(data).toLocaleString() },
            { data: 'updated_at', render: (data) => new Date(data).toLocaleString() },
            { data: 'actions',    orderable: false, searchable: false }
        ]
    });

    // ✅ Delete handler
    $(document).on("submit", ".ajax-delete-article", function (e) {
        e.preventDefault();

        const form = $(this);
        const row  = form.closest("tr");

        Swal.fire({
            title: "Delete this article?",
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
    $(document).on("submit", ".ajax-archive-rule-article", function (e) {
        e.preventDefault();

        const form      = $(this);
        const url       = form.attr("action");
        const isArchive = url.includes("/archive") && !url.includes("/unarchive");

        Swal.fire({
            title: isArchive ? "Archive this article?" : "Unarchive this article?",
            text: isArchive
                ? "This will mark the article as inactive and archived."
                : "This will restore the article and mark it as active.",
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
