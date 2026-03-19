@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Linkage Categories')

@section('content_header')
    <div class="card-header">
        <h3 class="card-title-dt">Linkage Categories</h3>
        <div class="card-header-actions">
            <button
                class="open-modal btn btn-primary"
                data-action="add"
                data-modal="#categoryModal"
                data-form="#categoryForm"
                data-title="Add Category"
                data-url="/admin/linkage_categories">
                <i class="fas fa-plus mr-1"></i> Add Category
            </button>
        </div>
    </div>
@stop

@section('content')
<div class="card">

    <div class="card-body">

        <table id="categoriesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Name</th>
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

{{-- Category Modal --}}
@include('admin.linkage.categories.partials.category-modal')

@stop


@push('js')

<script>

let table; 

$(function () {

    if ($.fn.DataTable.isDataTable('#categoriesTable')) {
        $('#categoriesTable').DataTable().destroy();
    }

    table = $('#categoriesTable').DataTable({ // ✅ Assigned, not declared
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        autoWidth: true,
        scrollCollapse: true,
        scrollX: true,
        order: [[1, 'asc']],
        lengthMenu: [10, 20, 50, 100],
        ajax: {
            url: "{{ route('admin.linkage_categories.index') }}",
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
            { data: "name" },
            { data: "sort_order" },
            { data: 'status',     orderable: false, searchable: false },
            { data: "created_at", render: (data) => data ? new Date(data).toLocaleString() : "" },
            { data: "updated_at", render: (data) => data ? new Date(data).toLocaleString() : "" },
            { data: "actions",    orderable: false, searchable: false }
        ]
    });

    /* ================================
     * AJAX DELETE CATEGORY
     * ================================ */

    // ✅ Delete handler
    $(document).on("submit", ".ajax-delete-category", function (e) {
        e.preventDefault();

        const form = $(this);
        const row  = form.closest("tr");

        Swal.fire({
            title: "Delete this category?",
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
    $(document).on("submit", ".ajax-archive-linkage-category", function (e) {
        e.preventDefault();

        const form      = $(this);
        const url       = form.attr("action");
        const isArchive = url.includes("/archive") && !url.includes("/unarchive");

        Swal.fire({
            title: isArchive ? "Archive this category?" : "Unarchive this category?",
            text: isArchive
                ? "This will mark the category as archived."
                : "This will restore the category.",
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
