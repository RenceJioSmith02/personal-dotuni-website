@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'DotUni News')

@section('content_header')
<h1>DotUni News</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#dotuniNewsModal"
            data-form="#dotuniNewsForm"
            data-title="Add DotUni News"
            data-url="{{ route('admin.dotuni_news.store') }}">
            <i class="fas fa-plus"></i> Add News
        </button>
    </div>

    <div class="card-body">
        <table id="dotuniNewsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>SEO Description</th>
                    <th>Status</th>
                    <th>Visibility</th>
                    <th>Published</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($news as $item)
                @php
                    $thumbnail = $item->attachments
                        ->firstWhere('is_thumbnail', true)?->asset;
                @endphp
                <tr data-id="{{ $item->id }}">
                    <td class="text-center">
                        @if ($thumbnail)
                            <img
                                src="{{ asset('storage/' . $thumbnail->storage_path) }}"
                                class="img-thumbnail"
                                style="max-width:50px;"
                                alt="{{ $thumbnail->alt_text ?? $item->title }}">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>

                    <td>{{ $item->title }}</td>

                    <td>{{ Str::limit($item->seo_description, 80) }}</td>

                    <td>
                        @php
                            $statusClass = match($item->status) {
                                'published' => 'badge-success',
                                'submitted' => 'badge-warning',
                                'archived' => 'badge-secondary',
                                default => 'badge-info', // draft
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>

                    <td>
                        @php
                            $visClass = match($item->visibility) {
                                'public' => 'badge-success',
                                'unlisted' => 'badge-warning',
                                default => 'badge-secondary', // private
                            };
                        @endphp
                        <span class="badge {{ $visClass }}">
                            {{ ucfirst($item->visibility) }}
                        </span>
                    </td>

                    <td>
                        @if($item->published_at)
                            {{ $item->published_at->format('Y-m-d H:i') }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-id="{{ $item->id }}"
                            data-modal="#dotuniNewsModal"
                            data-form="#dotuniNewsForm"
                            data-title="Edit DotUni News"
                            data-url="{{ route('admin.dotuni_news.index') }}">
                            Edit
                        </button>

                        <form
                            action="{{ route('admin.dotuni_news.destroy', $item) }}"
                            method="POST"
                            class="d-inline ajax-delete-dotuni-news">
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

{{-- DotUni News Modal --}}
@include('admin.dotuni_news.partials.dotuni-news-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#dotuniNewsTable')) {
        $('#dotuniNewsTable').DataTable().destroy();
    }

    const table = $('#dotuniNewsTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [0, 6] }]
    });

});

/* DELETE DOTUNI NEWS (AJAX) */
$(document).on("submit", ".ajax-delete-dotuni-news", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#dotuniNewsTable").DataTable();

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
                    text: res.message || "News deleted successfully",
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
