<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $clause->id }}"
    data-modal="#clauseModal"
    data-form="#clauseForm"
    data-title="Edit Clause"
    data-url="{{ route('admin.rule_clauses.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($clause->deleted_at))
    <form
        action="{{ route('admin.rule_clauses.archive', $clause) }}"
        method="POST"
        class="d-inline ajax-archive-rule-clause">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_clauses.unarchive', $clause) }}"
        method="POST"
        class="d-inline ajax-archive-rule-clause">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.rule_clauses.destroy', $clause) }}"
    method="POST"
    class="d-inline ajax-delete-clause">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($clause->is_active)
            disabled
            title="Deactivate the clause before deleting"
        @endif>
        Delete
    </button>
</form>