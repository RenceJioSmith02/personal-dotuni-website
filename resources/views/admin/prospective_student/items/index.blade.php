@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Prospective Student Items')

@section('content_header')
    <h1>Prospective Student Items</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Item -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#prospectiveStudentItemModal"
            data-form="#prospectiveStudentItemForm"
            data-title="Add Item"
            data-url="{{ route('admin.prospective_student_items.store') }}">
            <i class="fas fa-plus mr-1"></i> Add Item
        </button>

    </div>

    <div class="card-body">

        <table id="prospectiveStudentItemsTable"
               class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Content</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>

    </div>
</div>

{{-- Item Modal --}}
@include('admin.prospective_student.items.partials.prospective-student-item-modal')

@stop


@push('js')
<script>
$(function () {

    /* ================================
     * DataTable
     * ================================ */
    if ($.fn.DataTable.isDataTable('#prospectiveStudentItemsTable')) {
        $('#prospectiveStudentItemsTable').DataTable().destroy();
    }

    const table = $('#prospectiveStudentItemsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.prospective_student_items.index') }}",
            type: "GET"
        },
        columns: [
            { data: 'category', name: 'category' },
            { data: 'content', name: 'content' },
            { data: 'sort_order', name: 'sort_order', className: 'text-center' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    /* ================================
     * AJAX DELETE ITEM
     * ================================ */
    $(document).on("submit", ".ajax-delete-item", function (e) {
        e.preventDefault();

        const form = $(this);
        const row = form.closest("tr");

        Swal.fire({
            title: "Delete this item?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
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
                        text: res.message || "Item deleted successfully",
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

});
</script>
@endpush
