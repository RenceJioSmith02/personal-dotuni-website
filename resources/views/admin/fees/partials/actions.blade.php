{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#feeModal"
    data-form="#feeForm"
    data-title="Edit Fee"
    data-url="{{ route('admin.fees.index') }}"
    data-label="Edit"
    aria-label="Edit fee">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.fees.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-fee">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive fee">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.fees.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-fee">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive fee">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.fees.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-fee">
    @csrf
    @method('DELETE')
    @if($item->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the fee first"
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
            aria-label="Delete fee">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>