<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $form->id }}"
    data-modal="#formModal"
    data-form="#formForm"
    data-title="Edit Form"
    data-url="{{ route('admin.forms.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($form->deleted_at))
    <form
        action="{{ route('admin.forms.archive', $form) }}"
        method="POST"
        class="d-inline ajax-archive-form">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.forms.unarchive', $form) }}"
        method="POST"
        class="d-inline ajax-archive-form">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.forms.destroy', $form) }}"
    method="POST"
    class="d-inline ajax-delete-form">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($form->is_active)
            disabled
            title="Deactivate the form before deleting"
        @endif>
        Delete
    </button>
</form>