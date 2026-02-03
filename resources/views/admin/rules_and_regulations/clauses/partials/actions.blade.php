<button class="open-modal btn btn-sm btn-info"
        data-action="edit"
        data-id="{{ $clause->id }}"
        data-modal="#clauseModal"
        data-form="#clauseForm"
        data-title="Edit Clause"
        data-url="{{ route('admin.rule_clauses.index') }}">
    Edit
</button>

<form action="{{ route('admin.rule_clauses.destroy', $clause) }}"
      method="POST"
      class="d-inline ajax-delete-clause">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Delete</button>
</form>
