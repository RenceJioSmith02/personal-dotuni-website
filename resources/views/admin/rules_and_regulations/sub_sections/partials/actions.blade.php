<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#subSectionModal"
    data-form="#subSectionForm"
    data-title="Edit Sub-Section"
    data-url="/admin/rule_sub_sections"
    data-id="{{ $sub->id }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($sub->deleted_at))
    <form
        action="{{ route('admin.rule_sub_sections.archive', $sub) }}"
        method="POST"
        class="d-inline ajax-archive-rule-subsection">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_sub_sections.unarchive', $sub) }}"
        method="POST"
        class="d-inline ajax-archive-rule-subsection">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.rule_sub_sections.destroy', $sub->id) }}"
    method="POST"
    class="d-inline ajax-delete-subsection">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($sub->is_active)
            disabled
            title="Deactivate the sub-section before deleting"
        @endif>
        Delete
    </button>
</form>