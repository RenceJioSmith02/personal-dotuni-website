<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#sectionModal"
    data-form="#sectionForm"
    data-title="Edit Section"
    data-url="{{ url('/admin/rule_sections') }}"
    data-id="{{ $section->id }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($section->deleted_at))
    <form
        action="{{ route('admin.rule_sections.archive', $section) }}"
        method="POST"
        class="d-inline ajax-archive-rule-section">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_sections.unarchive', $section) }}"
        method="POST"
        class="d-inline ajax-archive-rule-section">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.rule_sections.destroy', $section) }}"
    method="POST"
    class="d-inline ajax-delete-section">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($section->is_active)
            disabled
            title="Deactivate the section before deleting"
        @endif>
        Delete
    </button>
</form>