<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#categoryModal"
    data-form="#categoryForm"
    data-title="Edit Requirement Category"
    data-url="{{ route('admin.program_requirement_categories.index') }}"
    data-id="{{ $c->id }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($c->deleted_at))
    <form
        action="{{ route('admin.program_requirement_categories.archive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">Archive</button>
    </form>
@else
    <form
        action="{{ route('admin.program_requirement_categories.unarchive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-category">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">Unarchive</button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive (is_active = false) --}}
<form
    action="{{ route('admin.program_requirement_categories.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-category">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($c->is_active)
            disabled
            title="Deactivate the category before deleting"
        @endif>
        Delete
    </button>
</form>