@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Linkages')

@section('content_header')
    <h1>Linkages</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Linkage -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#linkageModal"
            data-form="#linkageForm"
            data-title="Add Linkage"
            data-url="/admin/linkages">
            <i class="fas fa-plus mr-1"></i> Add Linkage
        </button>

    </div>

    <div class="card-body">

        <table id="linkagesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($linkages as $linkage)
                <tr>
                    <td>
                        @if($linkage->image_url)
                            <img
                                src="{{ $linkage->image_url }}"
                                alt="{{ $linkage->title }}"
                                style="max-height: 50px;">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $linkage->title }}</td>
                    <td>{{ $linkage->category->name ?? '-' }}</td>
                    <td>
                        <a href="{{ $linkage->url }}" target="_blank">
                            {{ Str::limit($linkage->url, 40) }}
                        </a>
                    </td>
                    <td>
                        {!! $linkage->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>
                    <td>
                        <!-- Edit -->
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-id="{{ $linkage->id }}"
                            data-modal="#linkageModal"
                            data-form="#linkageForm"
                            data-title="Edit Linkage"
                            data-url="{{ route('admin.linkages.index') }}">
                            Edit
                        </button>

                        <!-- Delete -->
                        <form
                            action="{{ route('admin.linkages.destroy', $linkage) }}"
                            method="POST"
                            class="d-inline ajax-delete-linkage">
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

{{-- Linkage Modal --}}
@include('admin.linkage.linkages.partials.linkage-modal')

@stop


@push('js')
<script>
$(function () {

    /* ================================
     * DataTable
     * ================================ */
    if ($.fn.DataTable.isDataTable('#linkagesTable')) {
        $('#linkagesTable').DataTable().destroy();
    }

    const table = $('#linkagesTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: 4 }
        ]
    });

    /* ================================
     * AJAX DELETE LINKAGE
     * ================================ */
    $(document).on("submit", ".ajax-delete-linkage", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr("action");
        const row = form.closest("tr");

        Swal.fire({
            title: "Delete this linkage?",
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
                        text: res.message || "Linkage deleted successfully",
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
