<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#eResourceModal"
    data-form="#eResourceForm"
    data-title="Edit E-Resource"
    data-url="{{ route('admin.e_resources.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.e_resources.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-e-resource">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.e_resources.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-e-resource">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.e_resources.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-resource">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($item->is_active)
            disabled
            title="Deactivate the E-Resource before deleting"
        @endif>
        Delete
    </button>
</form>