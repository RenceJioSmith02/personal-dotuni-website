<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $l->id }}"
    data-modal="#linkageModal"
    data-form="#linkageForm"
    data-title="Edit Linkage"
    data-url="{{ route('admin.linkages.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($l->deleted_at))
    <form
        action="{{ route('admin.linkages.archive', $l) }}"
        method="POST"
        class="d-inline ajax-archive-linkage">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.linkages.unarchive', $l) }}"
        method="POST"
        class="d-inline ajax-archive-linkage">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.linkages.destroy', $l) }}"
    method="POST"
    class="d-inline ajax-delete-linkage">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($l->is_active)
            disabled
            title="Deactivate the linkage before deleting"
        @endif>
        Delete
    </button>
</form>