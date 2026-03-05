@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Rule Clauses')

@section('content_header')
    <h1>Rule Clauses</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <button class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#clauseModal"
            data-form="#clauseForm"
            data-title="Add Clause"
            data-url="/admin/rule_clauses">
            <i class="fas fa-plus mr-1"></i> Add Clause
        </button>

    </div>

    <div class="card-body">
        <table id="clausesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Article</th>
                    <th>Section</th>
                    <th>Sub-Section</th>
                    <th>Number</th>
                    <th>Body</th>
                    <th>Sort Order</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- Clause Modal --}}
@include('admin.rules_and_regulations.clauses.partials.clause-modal')
@stop

@push('js')
<script>

let table;

$(function () {

    if ($.fn.DataTable.isDataTable('#clausesTable')) {
        $('#clausesTable').DataTable().destroy();
    }

    table = $('#clausesTable').DataTable({ 
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: "{{ route('admin.rule_clauses.index') }}",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) =>
                    meta.row + meta.settings._iDisplayStart + 1
            },
            { data: 'article' },
            { data: 'section' },
            { data: 'sub_section' },
            { data: 'number' },
            { data: 'body',       orderable: false },
            { data: 'sort_order' },
            { data: 'status',     orderable: false, searchable: false }, // ✅ Add
            { data: 'actions',    orderable: false, searchable: false }
        ]
    });

    // ✅ Delete handler
    $(document).on("submit", ".ajax-delete-clause", function (e) {
        e.preventDefault();

        const form = $(this);
        const row  = form.closest("tr");

        Swal.fire({
            title: "Delete this clause?",
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
    $(document).on("submit", ".ajax-archive-rule-clause", function (e) {
        e.preventDefault();

        const form      = $(this);
        const url       = form.attr("action");
        const isArchive = url.includes("/archive") && !url.includes("/unarchive");

        Swal.fire({
            title: isArchive ? "Archive this clause?" : "Unarchive this clause?",
            text: isArchive
                ? "This will mark the clause as inactive and archived."
                : "This will restore the clause and mark it as active.",
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
