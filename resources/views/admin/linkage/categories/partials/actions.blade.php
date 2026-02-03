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

<form
    action="{{ route('admin.linkage_categories.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-category">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
