<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#galleryModal"
    data-form="#galleryForm"
    data-title="Edit Gallery Item"
    data-url="{{ route('admin.gallery.index') }}">
    Edit
</button>

<form
    action="{{ route('admin.gallery.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-gallery">
    @csrf
    @method('DELETE')

    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
