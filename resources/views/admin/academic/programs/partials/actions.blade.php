{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $p->id }}"
    data-modal="#programModal"
    data-form="#programForm"
    data-title="Edit Program"
    data-url="{{ route('admin.programs.index') }}"
    data-label="Edit"
    aria-label="Edit program">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Program Builder --}}
<a
    href="{{ route('admin.academic.programs.builder', $p) }}"
    class="btn-icon btn-icon-settings"
    data-label="Program Builder"
    aria-label="Open program builder">
    <i class="fas fa-cogs" aria-hidden="true"></i>
</a>

{{-- Archive / Unarchive toggle --}}
@if(is_null($p->deleted_at))
    <form
        action="{{ route('admin.programs.archive', $p) }}"
        method="POST"
        class="d-inline ajax-archive-program">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive program">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.programs.unarchive', $p) }}"
        method="POST"
        class="d-inline ajax-archive-program">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive program">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.programs.destroy', $p) }}"
    method="POST"
    class="d-inline ajax-delete-program">
    @csrf
    @method('DELETE')
    @if($p->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the program first"
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
            aria-label="Delete program">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>