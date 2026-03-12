{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $category->id }}"
    data-modal="#categoryModal"
    data-form="#categoryForm"
    data-title="Edit Category"
    data-url="{{ route('admin.prospective_student_categories.index') }}"
    data-label="Edit"
    aria-label="Edit prospective student category">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($category->deleted_at))
    <form
        action="{{ route('admin.prospective_student_categories.archive', $category) }}"
        method="POST"
        class="d-inline ajax-archive-prospective-category">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive prospective student category">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.prospective_student_categories.unarchive', $category) }}"
        method="POST"
        class="d-inline ajax-archive-prospective-category">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive prospective student category">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.prospective_student_categories.destroy', $category) }}"
    method="POST"
    class="d-inline ajax-delete-category">
    @csrf
    @method('DELETE')
    @if($category->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the category first"
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
            aria-label="Delete prospective student category">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>