<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#clsuNewsModal"
    data-form="#clsuNewsForm"
    data-title="Edit News"
    data-url="{{ route('admin.clsu_news.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.clsu_news.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-clsu-news">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.clsu_news.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-clsu-news">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.clsu_news.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-news">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($item->is_active)
            disabled
            title="Deactivate the news before deleting"
        @endif>
        Delete
    </button>
</form>