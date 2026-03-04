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

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.gallery.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-gallery">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.gallery.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-gallery">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.gallery.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-gallery">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($item->is_active)
            disabled
            title="Deactivate the gallery item before deleting"
        @endif>
        Delete
    </button>
</form>