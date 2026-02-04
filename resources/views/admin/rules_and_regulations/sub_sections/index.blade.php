@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Rule Sub-Sections')

@section('content_header')
    <h1>Rule Sub-Sections</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <button class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#subSectionModal"
            data-form="#subSectionForm"
            data-title="Add Sub-Section"
            data-url="/admin/rule_sub_sections">
            <i class="fas fa-plus mr-1"></i> Add Sub-Section
        </button>

    </div>

    <div class="card-body">
        <table id="subSectionsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Article</th>
                    <th>Section</th>
                    <th>Number</th>
                    <th>Body</th>
                    <th>Sort Order</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- Sub-Section Modal --}}
@include('admin.rules_and_regulations.sub_sections.partials.sub-section-modal')
@stop

@push('js')
<script>
$(function () {
    if ($.fn.DataTable.isDataTable('#subSectionsTable')) $('#subSectionsTable').DataTable().destroy();

    const table = $('#subSectionsTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/rule_sub_sections',
            type: 'GET',
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
            { data: 'article' },
            { data: 'section' },
            { data: 'number' },
            { data: 'body' },
            { data: 'sort_order' },
            { data: 'actions', orderable: false, searchable: false },
        ]
    });


    $(document).on("submit", ".ajax-delete-subsection", function(e) {
        e.preventDefault();
        const form = $(this), url = form.attr("action"), row = form.closest("tr");
        Swal.fire({ title: "Delete this sub-section?", text: "This action cannot be undone.", type: "warning", showCancelButton: true, confirmButtonText: "Yes, delete it", cancelButtonText: "Cancel", confirmButtonColor: "#dc3545", reverseButtons: true })
            .then(result => { if (!result.value) return; $.ajax({ url, type: "POST", data: { _token: $('meta[name="csrf-token"]').attr("content"), _method: "DELETE" }, success: res => { Swal.fire({ type: "success", title: "Deleted", text: res.message, timer: 1200, showConfirmButton: false }); table.row(row).remove().draw(false); }, error: xhr => { Swal.fire({ type: "error", title: "Delete failed", text: xhr.responseJSON?.message || "Something went wrong" }); } }); });
    });
});
</script>
@endpush
