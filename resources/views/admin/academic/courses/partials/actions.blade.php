{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $c->id }}"
    data-modal="#courseModal"
    data-form="#courseForm"
    data-title="Edit Course"
    data-url="/admin/courses"
    data-label="Edit"
    aria-label="Edit course">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($c->deleted_at))
    <form
        action="{{ route('admin.courses.archive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-course">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive course">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.courses.unarchive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-course">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive course">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.courses.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-course">
    @csrf
    @method('DELETE')
    @if($c->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the course first"
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
            aria-label="Delete course">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>