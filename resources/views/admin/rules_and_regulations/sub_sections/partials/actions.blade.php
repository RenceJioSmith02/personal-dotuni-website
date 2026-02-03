<button class="open-modal btn btn-sm btn-info" data-action="edit" data-modal="#subSectionModal"
    data-form="#subSectionForm" data-title="Edit Sub-Section" data-url="/admin/rule_sub_sections"
    data-id="{{ $sub->id }}">
    Edit
</button>

<form action="{{ route('admin.rule_sub_sections.destroy', $sub->id) }}" method="POST"
    class="d-inline ajax-delete-subsection">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>