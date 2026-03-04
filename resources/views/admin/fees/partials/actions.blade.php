<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#feeModal"
    data-form="#feeForm"
    data-title="Edit Fee"
    data-url="{{ route('admin.fees.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.fees.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-fee">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.fees.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-fee">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.fees.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-fee">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($item->is_active)
            disabled
            title="Deactivate the fee before deleting"
        @endif>
        Delete
    </button>
</form>