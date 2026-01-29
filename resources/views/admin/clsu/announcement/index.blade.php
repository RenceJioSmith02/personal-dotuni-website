@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Announcements')

@section('content_header')
<h1>Announcements</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#announcementModal"
            data-form="#announcementForm"
            data-title="Add Announcement"
            data-url="{{ route('admin.announcements.store') }}">
            <i class="fas fa-plus"></i> Add Announcement
        </button>
    </div>

    <div class="card-body">
        <table id="announcementsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>SEO Description</th>
                    <th>Status</th>
                    <th>Visibility</th>
                    <th>Publish Window</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($announcements as $item)
                @php
                    $thumbnail = $item->assets
                        ->firstWhere('pivot.is_thumbnail', true);
                @endphp
                <tr data-id="{{ $item->id }}">
                    <td class="text-center">
                        @if ($thumbnail && $thumbnail->kind === 'image')
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
                                default => 'badge-info',
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
                                default => 'badge-secondary',
                            };
                        @endphp
                        <span class="badge {{ $visClass }}">
                            {{ ucfirst($item->visibility) }}
                        </span>
                    </td>

                    <td>
                        @if($item->publish_start)
                            {{ $item->publish_start->format('Y-m-d') }}
                            @if($item->publish_end)
                                <br>
                                <small class="text-muted">
                                    to {{ $item->publish_end->format('Y-m-d') }}
                                </small>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-id="{{ $item->id }}"
                            data-modal="#announcementModal"
                            data-form="#announcementForm"
                            data-title="Edit Announcement"
                            data-url="{{ route('admin.announcements.index') }}">
                            Edit
                        </button>

                        <form
                            action="{{ route('admin.announcements.destroy', $item) }}"
                            method="POST"
                            class="d-inline ajax-delete-announcement">
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

{{-- Announcement Modal --}}
@include('admin.clsu.announcement.partials.announcement-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#announcementsTable')) {
        $('#announcementsTable').DataTable().destroy();
    }

    const table = $('#announcementsTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [0, 6] }]
    });

});

/* DELETE ANNOUNCEMENT (AJAX) */
$(document).on("submit", ".ajax-delete-announcement", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#announcementsTable").DataTable();

    Swal.fire({
        title: "Delete this announcement?",
        text: "This action cannot be undone.",
        icon: "warning",
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
                    icon: "success",
                    title: "Deleted",
                    text: res.message || "Announcement deleted successfully",
                    timer: 1200,
                    showConfirmButton: false
                });

                table.row(row).remove().draw(false);
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to delete announcement."
                });
            }
        });
    });
});
</script>
@endpush
