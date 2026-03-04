<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $category->id }}"
    data-modal="#formCategoryModal"
    data-form="#formCategoryForm"
    data-title="Edit Category"
    data-url="{{ route('admin.form_categories.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($category->deleted_at))
    <form
        action="{{ route('admin.form_categories.archive', $category) }}"
        method="POST"
        class="d-inline ajax-archive-form-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.form_categories.unarchive', $category) }}"
        method="POST"
        class="d-inline ajax-archive-form-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.form_categories.destroy', $category) }}"
    method="POST"
    class="d-inline ajax-delete-form-category">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($category->is_active)
            disabled
            title="Deactivate the category before deleting"
        @endif>
        Delete
    </button>
</form>