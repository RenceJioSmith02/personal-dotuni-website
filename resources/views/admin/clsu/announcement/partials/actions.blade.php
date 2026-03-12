{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#announcementModal"
    data-form="#announcementForm"
    data-title="Edit Announcement"
    data-url="{{ route('admin.announcements.index') }}"
    data-label="Edit"
    aria-label="Edit announcement">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Hard delete — only when visibility is not public --}}
<form
    action="{{ route('admin.announcements.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-announcement">
    @csrf
    @method('DELETE')
    @if($item->visibility === 'public')
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Set visibility to private first or unlisted first"
            aria-label="Cannot delete — set visibility to private or unlisted first"
            aria-disabled="true"
            tabindex="-1"
            disabled>
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @else
        <button
            type="submit"
            class="btn-icon btn-icon-delete"
            data-label="Delete"
            aria-label="Delete announcement">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>