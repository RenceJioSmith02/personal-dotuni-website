<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#categoryModal"
    data-form="#categoryForm"
    data-title="Edit Requirement Category"
    data-url="{{ route('admin.program_requirement_categories.index') }}"
    data-id="{{ $c->id }}">
    Edit
</button>

<form
    action="{{ route('admin.program_requirement_categories.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-category">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
