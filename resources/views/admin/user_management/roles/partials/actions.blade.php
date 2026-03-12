{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $role->id }}"
    data-modal="#roleModal"
    data-form="#roleForm"
    data-title="Edit Role"
    data-url="/admin/roles"
    data-label="Edit"
    aria-label="Edit role">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Delete --}}
<form
    action="{{ route('admin.roles.destroy', $role->id) }}"
    method="POST"
    class="d-inline ajax-delete-role">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn-icon btn-icon-delete"
        data-label="Delete"
        aria-label="Delete role">
        <i class="fas fa-trash" aria-hidden="true"></i>
    </button>
</form>