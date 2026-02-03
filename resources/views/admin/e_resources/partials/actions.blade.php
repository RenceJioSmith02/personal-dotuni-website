<button class="open-modal btn btn-sm btn-info" data-action="edit" data-id="{{ $item->id }}" data-modal="#eResourceModal"
    data-form="#eResourceForm" data-title="Edit E-Resource" data-url="{{ route('admin.e_resources.index') }}">
    Edit
</button>

<form action="{{ route('admin.e_resources.destroy', $item) }}" method="POST" class="d-inline ajax-delete-resource">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Delete</button>
</form>