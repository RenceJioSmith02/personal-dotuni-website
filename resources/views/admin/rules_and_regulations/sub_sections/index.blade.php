@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Rule Sub-Sections')

@section('content_header')
    <div class="card-header">
        <h3 class="card-title-dt">Rule Sub-Sections</h3>
        <div class="card-header-actions">
            <button class="open-modal btn btn-primary"
                data-action="add"
                data-modal="#subSectionModal"
                data-form="#subSectionForm"
                data-title="Add Sub-Section"
                data-url="/admin/rule_sub_sections">
                <i class="fas fa-plus mr-1"></i> Add Sub-Section
            </button>
        </div>
    </div>
@stop

@section('content')
<div class="card">

    <div class="card-body">
        <table id="subSectionsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Article</th>
                    <th>Section</th>
                    <th>Number</th>
                    <th>Body</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th width="100">Actions</th>
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

    let table;

$(function () {

    if ($.fn.DataTable.isDataTable('#subSectionsTable')) {
        $('#subSectionsTable').DataTable().destroy();
    }

    table = $('#subSectionsTable').DataTable({ // ✅ update to your table ID
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: true,
        scrollCollapse: true,
        scrollX: true,
        pageLength: 10,
        ajax: "{{ route('admin.rule_sub_sections.index') }}",
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
            { data: 'number' },
            { data: 'body',       orderable: false },
            { data: 'sort_order' },
            { data: 'status',     orderable: false, searchable: false }, // ✅ Add
            { data: 'actions',    orderable: false, searchable: false }
        ]
    });

    // ✅ Delete handler
    $(document).on("submit", ".ajax-delete-subsection", function (e) {
        e.preventDefault();

        const form = $(this);
        const row  = form.closest("tr");

        Swal.fire({
            title: "Delete this sub-section?",
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
    $(document).on("submit", ".ajax-archive-rule-subsection", function (e) {
        e.preventDefault();

        const form      = $(this);
        const url       = form.attr("action");
        const isArchive = url.includes("/archive") && !url.includes("/unarchive");

        Swal.fire({
            title: isArchive ? "Archive this sub-section?" : "Unarchive this sub-section?",
            text: isArchive
                ? "This will mark the sub-section as inactive and archived."
                : "This will restore the sub-section and mark it as active.",
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
