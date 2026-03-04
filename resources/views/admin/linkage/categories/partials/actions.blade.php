<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#categoryModal"
    data-form="#categoryForm"
    data-title="Edit Category"
    data-url="/admin/linkage_categories"
    data-id="{{ $c->id }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle — deleted_at only, no is_active change --}}
@if(is_null($c->deleted_at))
    <form
        action="{{ route('admin.linkage_categories.archive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-linkage-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.linkage_categories.unarchive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-linkage-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.linkage_categories.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-category">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($c->is_active)
            disabled
            title="Deactivate the category before deleting"
        @endif>
        Delete
    </button>
</form>