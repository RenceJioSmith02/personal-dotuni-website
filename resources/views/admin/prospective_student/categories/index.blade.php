@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Prospective Student Categories')

@section('content_header')
    <h1>Prospective Student Categories</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Category -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#categoryModal"
            data-form="#categoryForm"
            data-title="Add Category"
            data-url="{{ route('admin.prospective_student_categories.store') }}">
            <i class="fas fa-plus mr-1"></i> Add Category
        </button>

    </div>

    <div class="card-body">

        <table id="categoriesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr data-id="{{ $category->id }}">
                    <td>{{ $category->name }}</td>

                    <td class="text-center">
                        {{ $category->sort_order }}
                    </td>

                    <td>
                        {!! $category->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>

                    <td>

                        <!-- Edit -->
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-modal="#categoryModal"
                            data-form="#categoryForm"
                            data-title="Edit Category"
                            data-url="{{ route('admin.prospective_student_categories.index') }}"
                            data-id="{{ $category->id }}">
                            Edit
                        </button>

                        <!-- Delete -->
                        <form
                            action="{{ route('admin.prospective_student_categories.destroy', $category) }}"
                            method="POST"
                            class="d-inline ajax-delete-category">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-sm btn-danger">
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

{{-- Category Modal --}}
@include('admin.prospective_student.categories.partials.prospective-student-category-modal')

@stop


@push('js')
<script>
$(function () {

    /* ================================
     * DATATABLE
     * ================================ */
    if ($.fn.DataTable.isDataTable('#categoriesTable')) {
        $('#categoriesTable').DataTable().destroy();
    }

    const table = $('#categoriesTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [3] }
        ]
    });

    /* ================================
     * AJAX DELETE CATEGORY
     * ================================ */
    $(document).on("submit", ".ajax-delete-category", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr("action");
        const row = form.closest("tr");

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
                url: url,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    _method: "DELETE"
                },
                success: function (res) {
                    Swal.fire({
                        type: "success",
                        title: "Deleted",
                        text: res.message || "Category deleted successfully",
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
