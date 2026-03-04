<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $p->id }}"
    data-modal="#programModal"
    data-form="#programForm"
    data-title="Edit Program"
    data-url="{{ route('admin.programs.index') }}">
    Edit
</button>

<a href="{{ route('admin.academic.programs.builder', $p) }}"
   class="btn btn-sm btn-info">
    <i class="fas fa-cogs"></i>
</a>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($p->deleted_at))
    <form
        action="{{ route('admin.programs.archive', $p) }}"
        method="POST"
        class="d-inline ajax-archive-program">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.programs.unarchive', $p) }}"
        method="POST"
        class="d-inline ajax-archive-program">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.programs.destroy', $p) }}"
    method="POST"
    class="d-inline ajax-delete-program">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($p->is_active)
            disabled
            title="Deactivate the program before deleting"
        @endif>
        Delete
    </button>
</form>