<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-modal="#courseModal"
    data-form="#courseForm"
    data-title="Edit Course"
    data-url="/admin/courses"
    data-id="{{ $c->id }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($c->deleted_at))
    {{-- Active: show Archive button --}}
    <form
        action="{{ route('admin.courses.archive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-course">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    {{-- Archived: show Unarchive button --}}
    <form
        action="{{ route('admin.courses.unarchive', $c) }}"
        method="POST"
        class="d-inline ajax-archive-course">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete --}}
{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.courses.destroy', $c) }}"
    method="POST"
    class="d-inline ajax-delete-course">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($c->is_active)
            disabled
            title="Deactivate the course before deleting"
        @endif>
        Delete
    </button>
</form>