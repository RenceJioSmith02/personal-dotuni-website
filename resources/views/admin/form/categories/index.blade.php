@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Form Categories')

@section('content_header')
    <h1>Form Categories</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Category -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#formCategoryModal"
            data-form="#formCategoryForm"
            data-title="Add Category"
            data-url="/admin/form_categories">
            <i class="fas fa-plus mr-1"></i> Add Category
        </button>

    </div>

    <div class="card-body">

        <table id="formCategoriesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Name</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>

    </div>
</div>

{{-- Category Modal --}}
@include('admin.form.categories.partials.form-category-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#formCategoriesTable')) {
        $('#formCategoriesTable').DataTable().destroy();
    }

    $('#formCategoriesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.form_categories.index') }}",
            type: "GET"
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                textAlign: "center",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'name' },
            { data: 'sort_order', className: 'text-center' },
            { data: 'status', orderable: false, searchable: false },
            {
                data: "created_at",
                render: (data) =>
                    new Date(data).toLocaleString()
            },
            {
                data: "updated_at",
                render: (data) =>
                    new Date(data).toLocaleString()
            },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $(document).on("submit", ".ajax-delete-form-category", function (e) {
        e.preventDefault();

        const form = $(this);
        const row = form.closest("tr");

        Swal.fire({
            title: "Delete this category?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
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
                        text: xhr.responseJSON?.message || "Cannot delete category"
                    });
                }
            });
        });
    });

});
</script>
@endpush
