<button class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#sectionModal"
    data-form="#sectionForm"
    data-title="Edit Section"
    data-url="{{ url('/admin/rule_sections') }}"
    data-id="{{ $section->id }}">
    Edit
</button>

<form action="{{ route('admin.rule_sections.destroy', $section) }}"
      method="POST"
      class="d-inline ajax-delete-section">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>
