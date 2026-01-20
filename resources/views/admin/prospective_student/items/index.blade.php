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
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->category->name ?? '-' }}</td>

                    <td>
                        {{ Str::limit(strip_tags($item->content), 80) }}
                    </td>

                    <td class="text-center">
                        {{ $item->sort_order }}
                    </td>

                    <td>
                        {!! $item->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>

                    <td>
                        <!-- Edit -->
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-id="{{ $item->id }}"
                            data-modal="#prospectiveStudentItemModal"
                            data-form="#prospectiveStudentItemForm"
                            data-title="Edit Item"
                            data-url="{{ route('admin.prospective_student_items.index') }}">
                            Edit
                        </button>

                        <!-- Delete -->
                        <form
                            action="{{ route('admin.prospective_student_items.destroy', $item) }}"
                            method="POST"
                            class="d-inline ajax-delete-item">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
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
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [4] }
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
