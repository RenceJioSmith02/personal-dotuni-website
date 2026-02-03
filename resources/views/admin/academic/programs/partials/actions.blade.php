
<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $p->id }}"
    data-modal="#programModal"
    data-form="#programForm"
    data-title="Edit Program"
    data-url="{{ route('admin.programs.index') }}">
    Edit
</button>

<a href="{{ route('admin.academic.programs.builder', $p) }}"
   class="btn btn-sm btn-info">
    <i class="fas fa-cogs"></i>
</a>

<form
    action="{{ route('admin.programs.destroy', $p) }}"
    method="POST"
    class="d-inline ajax-delete-program">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
