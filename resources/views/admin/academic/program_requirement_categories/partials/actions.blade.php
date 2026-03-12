{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $c->id }}"
    data-modal="#categoryModal"
    data-form="#categoryForm"
    data-title="Edit Requirement Category"
    data-url="{{ route('admin.program_requirement_categories.index') }}"
    data-label="Edit"
    aria-label="Edit requirement category">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($c->deleted_at))
    <form
        action="{{ route('admin.program_requirement_categories.archive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-category">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive requirement category">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.program_requirement_categories.unarchive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-category">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive requirement category">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.program_requirement_categories.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-category">
    @csrf
    @method('DELETE')
    @if($c->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the category first"
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
            aria-label="Delete requirement category">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>