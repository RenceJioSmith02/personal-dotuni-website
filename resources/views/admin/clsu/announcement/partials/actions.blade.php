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

{{-- ✅ Hard delete — only enabled when visibility is private or unlisted --}}
<form
    action="{{ route('admin.announcements.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-announcement">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($item->visibility === 'public')
            disabled
            title="Set visibility to private or unlisted before deleting"
        @endif>
        Delete
    </button>
</form>
