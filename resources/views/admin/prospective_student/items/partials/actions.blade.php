<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#prospectiveStudentItemModal"
    data-form="#prospectiveStudentItemForm"
    data-title="Edit Item"
    data-url="{{ route('admin.prospective_student_items.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.prospective_student_items.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-prospective-item">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.prospective_student_items.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-prospective-item">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.prospective_student_items.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-item">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($item->is_active)
            disabled
            title="Deactivate the item before deleting"
        @endif>
        Delete
    </button>
</form>