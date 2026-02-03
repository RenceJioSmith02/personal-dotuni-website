<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $form->id }}"
    data-modal="#formModal"
    data-form="#formForm"
    data-title="Edit Form"
    data-url="{{ route('admin.forms.index') }}">
    Edit
</button>

<form
    action="{{ route('admin.forms.destroy', $form) }}"
    method="POST"
    class="d-inline ajax-delete-form">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
