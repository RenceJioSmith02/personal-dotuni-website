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
                    <th>#</th>
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
$(function () {
    if ($.fn.DataTable.isDataTable('#clausesTable')) $('#clausesTable').DataTable().destroy();

    const table = $('#clausesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.rule_clauses.index') }}",
            type: "GET"
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
            { data: 'article', name: 'article' },
            { data: 'section', name: 'section' },
            { data: 'sub_section', name: 'sub_section' },
            { data: 'number', name: 'number' },
            { data: 'body', name: 'body' },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
    
    $(document).on("submit", ".ajax-delete-clause", function(e) {
        e.preventDefault();
        const form = $(this), url = form.attr("action"), row = form.closest("tr");
        Swal.fire({ title: "Delete this clause?", text: "This action cannot be undone.", type: "warning", showCancelButton: true, confirmButtonText: "Yes, delete it", cancelButtonText: "Cancel", confirmButtonColor: "#dc3545", reverseButtons: true })
            .then(result => { if (!result.value) return; $.ajax({ url, type: "POST", data: { _token: $('meta[name="csrf-token"]').attr("content"), _method: "DELETE" }, success: res => { Swal.fire({ type: "success", title: "Deleted", text: res.message, timer: 1200, showConfirmButton: false }); table.row(row).remove().draw(false); }, error: xhr => { Swal.fire({ type: "error", title: "Delete failed", text: xhr.responseJSON?.message || "Something went wrong" }); } }); });
    });
});
</script>
@endpush
