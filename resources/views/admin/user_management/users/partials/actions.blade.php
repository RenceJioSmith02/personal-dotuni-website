<button class="open-modal btn btn-sm btn-info" data-action="edit" data-modal="#userModal" data-form="#userForm"
    data-title="Edit User" data-url="/admin/users" data-id="{{ $user->id }}">
    Edit
</button>

<form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline ajax-delete-user">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>