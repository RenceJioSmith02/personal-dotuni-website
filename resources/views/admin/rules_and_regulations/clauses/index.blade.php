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
                    <th>Article</th>
                    <th>Section</th>
                    <th>Sub-Section</th>
                    <th>Number</th>
                    <th>Body</th>
                    <th>Sort Order</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clauses as $clause)
                <tr>
                    <td>{{ $clause->subSection->section->article->number ?? '-' }}</td>
                    <td>{{ $clause->subSection->section->number ?? '-' }}</td>
                    <td>{{ $clause->subSection->number ?? '-' }}</td>
                    <td>{{ $clause->number }}</td>
                    <td>{{ Str::limit($clause->body, 80) }}</td>
                    <td>{{ $clause->sort_order }}</td>
                    <td>
                        <button class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-modal="#clauseModal"
                            data-form="#clauseForm"
                            data-title="Edit Clause"
                            data-url="/admin/rule_clauses"
                            data-id="{{ $clause->id }}">
                            Edit
                        </button>

                        <form action="{{ route('admin.rule_clauses.destroy', $clause) }}"
                              method="POST"
                              class="d-inline ajax-delete-clause">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
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
    const table = $('#clausesTable').DataTable({ responsive: true, autoWidth: false, pageLength: 10, columnDefs: [{ orderable: false, targets: 6 }] });

    $(document).on("submit", ".ajax-delete-clause", function(e) {
        e.preventDefault();
        const form = $(this), url = form.attr("action"), row = form.closest("tr");
        Swal.fire({ title: "Delete this clause?", text: "This action cannot be undone.", type: "warning", showCancelButton: true, confirmButtonText: "Yes, delete it", cancelButtonText: "Cancel", confirmButtonColor: "#dc3545", reverseButtons: true })
            .then(result => { if (!result.value) return; $.ajax({ url, type: "POST", data: { _token: $('meta[name="csrf-token"]').attr("content"), _method: "DELETE" }, success: res => { Swal.fire({ type: "success", title: "Deleted", text: res.message, timer: 1200, showConfirmButton: false }); table.row(row).remove().draw(false); }, error: xhr => { Swal.fire({ type: "error", title: "Delete failed", text: xhr.responseJSON?.message || "Something went wrong" }); } }); });
    });
});
</script>
@endpush
