@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Requirement Categories')

@section('content_header')
    <h1>Requirement Categories</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#categoryModal"
            data-form="#categoryForm"
            data-title="Add Requirement Category"
            data-url="{{ route('admin.program_requirement_categories.store') }}">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>

    <div class="card-body">
        <table id="categoriesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sort Order</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

@include('admin.academic.program_requirement_categories.partials.category-modal')
@stop

@push('js')
<script>
$(function () {
    if ($.fn.DataTable.isDataTable('#categoriesTable')) {
        $('#categoriesTable').DataTable().destroy();
    }

    const table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 20, 50, 100],

        ajax: {
            url: "{{ route('admin.program_requirement_categories.index') }}",
            type: "GET",
            // dataSrc: function (json) {
            //     console.log('Requirement categories returned:', json.data.length);
            //     return json.data;
            // }
        },

        columns: [
            { data: "name" },
            { data: "sort_order" },
            {
                data: "actions",
                orderable: false,
                searchable: false
            }
        ]
    });


});

/* DELETE (AJAX) */
$(document).on("submit", ".ajax-delete-category", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#categoriesTable").DataTable();

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
            }
        });
    });
});
</script>
@endpush
