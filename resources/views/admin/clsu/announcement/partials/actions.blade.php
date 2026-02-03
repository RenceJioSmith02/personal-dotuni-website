
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
