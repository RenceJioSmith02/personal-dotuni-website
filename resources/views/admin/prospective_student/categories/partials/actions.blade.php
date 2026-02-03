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

<form
    action="{{ route('admin.prospective_student_categories.destroy', $category) }}"
    method="POST"
    class="d-inline ajax-delete-category">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
