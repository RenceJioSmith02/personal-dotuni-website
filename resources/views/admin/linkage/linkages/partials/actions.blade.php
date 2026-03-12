{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $l->id }}"
    data-modal="#linkageModal"
    data-form="#linkageForm"
    data-title="Edit Linkage"
    data-url="{{ route('admin.linkages.index') }}"
    data-label="Edit"
    aria-label="Edit linkage">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($l->deleted_at))
    <form
        action="{{ route('admin.linkages.archive', $l) }}"
        method="POST"
        class="d-inline ajax-archive-linkage">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive linkage">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.linkages.unarchive', $l) }}"
        method="POST"
        class="d-inline ajax-archive-linkage">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive linkage">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.linkages.destroy', $l) }}"
    method="POST"
    class="d-inline ajax-delete-linkage">
    @csrf
    @method('DELETE')
    @if($l->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the linkage first"
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
            aria-label="Delete linkage">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>