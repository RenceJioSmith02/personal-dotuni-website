<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#feeModal"
    data-form="#feeForm"
    data-title="Edit Fee"
    data-url="{{ route('admin.fees.index') }}">
    Edit
</button>

<form
    action="{{ route('admin.fees.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-fee">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
