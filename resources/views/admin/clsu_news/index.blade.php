@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'CLSU News')

@section('content_header')
<h1>CLSU News</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#clsuNewsModal"
            data-form="#clsuNewsForm"
            data-title="Add News"
            data-url="{{ route('admin.clsu_news.store') }}">
            <i class="fas fa-plus"></i> Add News
        </button>
    </div>

    <div class="card-body">
        <table id="clsuNewsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>URL</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($news as $item)
                <tr data-id="{{ $item->id }}">
                    <td class="text-center">
                        @if ($item->thumbnail)
                            <img
                                src="{{ asset('storage/' . $item->thumbnail->storage_path) }}"
                                class="img-thumbnail"
                                style="max-width:50px;"
                                alt="{{ $item->title }}">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>

                    <td>{{ $item->title }}</td>

                    <td>{{ Str::limit($item->description, 80) }}</td>

                    <td>
                        @if($item->url)
                            <a href="{{ $item->url }}" target="_blank">
                                View
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>{{ $item->sort_order }}</td>

                    <td>
                        {!! $item->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>

                    <td>
                        <button
                            class="open-modal btn btn-sm btn-warning"
                            data-action="edit"
                            data-id="{{ $item->id }}"
                            data-modal="#clsuNewsModal"
                            data-form="#clsuNewsForm"
                            data-title="Edit News"
                            data-url="{{ route('admin.clsu_news.index') }}">
                            Edit
                        </button>

                        <form
                            action="{{ route('admin.clsu_news.destroy', $item) }}"
                            method="POST"
                            class="d-inline ajax-delete-news">
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

@include('admin.clsu_news.partials.clsu-news-modal')

@stop


@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#clsuNewsTable')) {
        $('#clsuNewsTable').DataTable().destroy();
    }

    const table = $('#clsuNewsTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [0, 6] }]
    });

});

/* DELETE NEWS (AJAX) */
$(document).on("submit", ".ajax-delete-news", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#clsuNewsTable").DataTable();

    Swal.fire({
        title: "Delete this news?",
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
                    text: "Failed to delete news."
                });
            }
        });
    });
});
</script>
@endpush
