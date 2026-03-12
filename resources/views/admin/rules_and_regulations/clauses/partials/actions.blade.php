{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $clause->id }}"
    data-modal="#clauseModal"
    data-form="#clauseForm"
    data-title="Edit Clause"
    data-url="{{ route('admin.rule_clauses.index') }}"
    data-label="Edit"
    aria-label="Edit rule clause">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($clause->deleted_at))
    <form
        action="{{ route('admin.rule_clauses.archive', $clause) }}"
        method="POST"
        class="d-inline ajax-archive-rule-clause">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive rule clause">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_clauses.unarchive', $clause) }}"
        method="POST"
        class="d-inline ajax-archive-rule-clause">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive rule clause">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.rule_clauses.destroy', $clause) }}"
    method="POST"
    class="d-inline ajax-delete-clause">
    @csrf
    @method('DELETE')
    @if($clause->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the clause first"
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
            aria-label="Delete rule clause">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>