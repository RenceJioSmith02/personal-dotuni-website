{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $form->id }}"
    data-modal="#formModal"
    data-form="#formForm"
    data-title="Edit Form"
    data-url="{{ route('admin.forms.index') }}"
    data-label="Edit"
    aria-label="Edit form">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($form->deleted_at))
    <form
        action="{{ route('admin.forms.archive', $form) }}"
        method="POST"
        class="d-inline ajax-archive-form">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive form">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.forms.unarchive', $form) }}"
        method="POST"
        class="d-inline ajax-archive-form">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive form">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.forms.destroy', $form) }}"
    method="POST"
    class="d-inline ajax-delete-form">
    @csrf
    @method('DELETE')
    @if($form->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the form first"
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
            aria-label="Delete form">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>