<button class="open-modal btn btn-sm btn-info" data-action="edit" data-modal="#roleModal" data-form="#roleForm"
    data-title="Edit Role" data-url="/admin/roles" data-id="{{ $role->id }}">
    Edit
</button>

<form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline ajax-delete-role">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>