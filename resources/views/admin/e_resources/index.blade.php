@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'E-Resources')

@section('content_header')
<h1>E-Resources</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#eResourceModal"
            data-form="#eResourceForm"
            data-title="Add E-Resource"
            data-url="{{ route('admin.e_resources.store') }}">
            <i class="fas fa-plus"></i> Add E-Resource
        </button>
    </div>

    <div class="card-body">
        <table id="eResourcesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Link</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

@include('admin.e_resources.partials.e-resources-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#eResourcesTable')) {
        $('#eResourcesTable').DataTable().destroy();
    }

    const table = $('#eResourcesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.e_resources.index') }}",
            type: "GET",
            dataSrc: function(json) {
                console.log('Eresources returned:', json.data.length);
                return json.data;
            }
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
            { data: 'name' },
            { data: 'description' },
            { data: 'link_url', orderable: false, searchable: false },
            { data: 'sort_order' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false },
        ]
    });

});


/* DELETE E-RESOURCE (AJAX) */
$(document).on("submit", ".ajax-delete-resource", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#eResourcesTable").DataTable();

    Swal.fire({
        title: "Delete this E-Resource?",
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
            error: function () {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: "Failed to delete E-Resource."
                });
            }
        });
    });
});
</script>
@endpush
