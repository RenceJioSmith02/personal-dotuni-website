<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#courseModal"
    data-form="#courseForm"
    data-title="Edit Course"
    data-url="/admin/courses"
    data-id="{{ $c->id }}">
    Edit
</button>

<form
    action="{{ route('admin.courses.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-course">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
