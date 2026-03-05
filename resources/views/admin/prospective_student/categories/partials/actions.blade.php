<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#categoryModal"
    data-form="#categoryForm"
    data-title="Edit Category"
    data-url="{{ route('admin.prospective_student_categories.index') }}"
    data-id="{{ $category->id }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($category->deleted_at))
    <form
        action="{{ route('admin.prospective_student_categories.archive', $category) }}"
        method="POST"
        class="d-inline ajax-archive-prospective-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.prospective_student_categories.unarchive', $category) }}"
        method="POST"
        class="d-inline ajax-archive-prospective-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.prospective_student_categories.destroy', $category) }}"
    method="POST"
    class="d-inline ajax-delete-category">
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