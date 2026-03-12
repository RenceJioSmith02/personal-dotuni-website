{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $section->id }}"
    data-modal="#sectionModal"
    data-form="#sectionForm"
    data-title="Edit Section"
    data-url="{{ url('/admin/rule_sections') }}"
    data-label="Edit"
    aria-label="Edit rule section">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($section->deleted_at))
    <form
        action="{{ route('admin.rule_sections.archive', $section) }}"
        method="POST"
        class="d-inline ajax-archive-rule-section">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive rule section">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_sections.unarchive', $section) }}"
        method="POST"
        class="d-inline ajax-archive-rule-section">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive rule section">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.rule_sections.destroy', $section) }}"
    method="POST"
    class="d-inline ajax-delete-section">
    @csrf
    @method('DELETE')
    @if($section->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the section first"
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
            aria-label="Delete rule section">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>