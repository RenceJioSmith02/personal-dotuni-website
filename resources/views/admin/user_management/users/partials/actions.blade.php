{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $user->id }}"
    data-modal="#userModal"
    data-form="#userForm"
    data-title="Edit User"
    data-url="/admin/users"
    data-label="Edit"
    aria-label="Edit user">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle — hidden for own account --}}
@if($user->id !== auth()->id())
    @if(is_null($user->deleted_at))
        <form
            action="{{ route('admin.users.archive', $user) }}"
            method="POST"
            class="d-inline ajax-archive-user">
            @csrf
            @method('PATCH')
            <button
                type="submit"
                class="btn-icon btn-icon-archive"
                data-label="Archive"
                aria-label="Archive user">
                <i class="fas fa-archive" aria-hidden="true"></i>
            </button>
        </form>
    @else
        <form
            action="{{ route('admin.users.unarchive', $user) }}"
            method="POST"
            class="d-inline ajax-archive-user">
            @csrf
            @method('PATCH')
            <button
                type="submit"
                class="btn-icon btn-icon-unarchive"
                data-label="Unarchive"
                aria-label="Unarchive user">
                <i class="fas fa-box-open" aria-hidden="true"></i>
            </button>
        </form>
    @endif
@endif

{{-- Hard delete — only when inactive, hidden for own account --}}
@if($user->id !== auth()->id())
    <form
        action="{{ route('admin.users.destroy', $user) }}"
        method="POST"
        class="d-inline ajax-delete-user">
        @csrf
        @method('DELETE')
        @if($user->is_active)
            <button
                type="button"
                class="btn-icon btn-icon-delete"
                style="opacity:0.38; cursor:not-allowed;"
                data-label="Archive before deleting"
                aria-label="Cannot delete — archive the user first"
                aria-disabled="true"
                tabindex="-1"
                disabled>
                <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
        @else
            <button
                type="submit"
                class="btn-icon btn-icon-delete"
                data-label="Delete"
                aria-label="Delete user">
                <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
        @endif
    </form>
@endif