@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Fees')

@section('content_header')
<h1>Fees</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#feeModal"
            data-form="#feeForm"
            data-title="Add Fee"
            data-url="{{ route('admin.fees.store') }}">
            <i class="fas fa-plus"></i> Add Fee
        </button>
    </div>

    <div class="card-body">
        <table id="feeTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Title</th>
                    <th>Caption</th>
                    <th>Order</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fees as $item)
                <tr data-id="{{ $item->id }}">
                    <td class="text-center">
                        @if ($item->asset)
                            @if ($item->asset->kind === 'image')
                                <img
                                    src="{{ asset('storage/' . $item->asset->storage_path) }}"
                                    class="img-thumbnail"
                                    style="max-width:50px;"
                                    alt="{{ $item->title }}">
                            @else
                                <span class="text-muted">{{ ucfirst($item->asset->kind) }}</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>{{ $item->title }}</td>

                    <td>{{ Str::limit($item->caption, 80) }}</td>

                    <td>{{ $item->sort_order }}</td>

                    <td>
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-id="{{ $item->id }}"
                            data-modal="#feeModal"
                            data-form="#feeForm"
                            data-title="Edit Fee"
                            data-url="{{ route('admin.fees.index') }}">
                            Edit
                        </button>

                        <form
                            action="{{ route('admin.fees.destroy', $item) }}"
                            method="POST"
                            class="d-inline ajax-delete-fee">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
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

@include('admin.fees.partials.fee-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#feeTable')) {
        $('#feeTable').DataTable().destroy();
    }

    const table = $('#feeTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [0, 4] }]
    });

});

/* DELETE FEE (AJAX) */
$(document).on("submit", ".ajax-delete-fee", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#feeTable").DataTable();

    Swal.fire({
        title: "Delete this fee?",
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
                    text: "Failed to delete fee."
                });
            }
        });
    });
});
</script>
@endpush
