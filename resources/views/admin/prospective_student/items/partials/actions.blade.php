<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#prospectiveStudentItemModal"
    data-form="#prospectiveStudentItemForm"
    data-title="Edit Item"
    data-url="{{ route('admin.prospective_student_items.index') }}">
    Edit
</button>

<form
    action="{{ route('admin.prospective_student_items.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-item">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
