<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#userModal"
    data-form="#userForm"
    data-title="Edit User"
    data-url="/admin/users"
    data-id="{{ $user->id }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle — hide for own account --}}
@if($user->id !== auth()->id())
    @if(is_null($user->deleted_at))
        <form
            action="{{ route('admin.users.archive', $user) }}"
            method="POST"
            class="d-inline ajax-archive-user">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-warning">
                Archive
            </button>
        </form>
    @else
        <form
            action="{{ route('admin.users.unarchive', $user) }}"
            method="POST"
            class="d-inline ajax-archive-user">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-secondary">
                Unarchive
            </button>
        </form>
    @endif
@endif

{{-- ✅ Hard delete — only enabled when inactive, hidden for own account --}}
@if($user->id !== auth()->id())
    <form
        action="{{ route('admin.users.destroy', $user) }}"
        method="POST"
        class="d-inline ajax-delete-user">
        @csrf
        @method('DELETE')
        <button
            type="submit"
            class="btn btn-sm btn-danger"
            @if($user->is_active)
                disabled
                title="Deactivate the user before deleting"
            @endif>
            Delete
        </button>
    </form>
@endif