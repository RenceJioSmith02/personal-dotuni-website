{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#clsuNewsModal"
    data-form="#clsuNewsForm"
    data-title="Edit News"
    data-url="{{ route('admin.clsu_news.index') }}"
    data-label="Edit"
    aria-label="Edit news">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.clsu_news.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-clsu-news">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive news">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.clsu_news.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-clsu-news">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive news">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.clsu_news.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-news">
    @csrf
    @method('DELETE')
    @if($item->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the news first"
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
            aria-label="Delete news">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>