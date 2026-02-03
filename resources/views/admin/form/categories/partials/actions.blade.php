<button class="open-modal btn btn-sm btn-info" data-action="edit" data-id="{{ $category->id }}"
    data-modal="#formCategoryModal" data-form="#formCategoryForm" data-title="Edit Category"
    data-url="{{ route('admin.form_categories.index') }}">
    Edit
</button>

<form action="{{ route('admin.form_categories.destroy', $category) }}" method="POST"
    class="d-inline ajax-delete-form-category">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-sm btn-danger">
        Delete
    </button>
</form>