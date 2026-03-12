{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $sub->id }}"
    data-modal="#subSectionModal"
    data-form="#subSectionForm"
    data-title="Edit Sub-Section"
    data-url="/admin/rule_sub_sections"
    data-label="Edit"
    aria-label="Edit rule sub-section">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($sub->deleted_at))
    <form
        action="{{ route('admin.rule_sub_sections.archive', $sub) }}"
        method="POST"
        class="d-inline ajax-archive-rule-subsection">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive rule sub-section">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_sub_sections.unarchive', $sub) }}"
        method="POST"
        class="d-inline ajax-archive-rule-subsection">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive rule sub-section">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.rule_sub_sections.destroy', $sub->id) }}"
    method="POST"
    class="d-inline ajax-delete-subsection">
    @csrf
    @method('DELETE')
    @if($sub->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the sub-section first"
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
            aria-label="Delete rule sub-section">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>