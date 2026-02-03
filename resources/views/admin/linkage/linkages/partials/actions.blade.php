<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $l->id }}"
    data-modal="#linkageModal"
    data-form="#linkageForm"
    data-title="Edit Linkage"
    data-url="{{ route('admin.linkages.index') }}">
    Edit
</button>

<form
    action="{{ route('admin.linkages.destroy', $l) }}"
    method="POST"
    class="d-inline ajax-delete-linkage">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
