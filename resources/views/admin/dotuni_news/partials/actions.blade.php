{{-- Publish — only for submitted items and authorized roles --}}
@if($item->status === 'submitted' && auth()->user()->hasAnyRole(['admin','publisher']))
    <form
        action="{{ route('admin.dotuni_news.publish', $item) }}"
        method="POST"
        class="d-inline ajax-publish-dotuni-news">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-publish"
            data-label="Publish"
            aria-label="Publish DotUni news">
            <i class="fas fa-paper-plane" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#dotuniNewsModal"
    data-form="#dotuniNewsForm"
    data-title="Edit DotUni News"
    data-url="{{ route('admin.dotuni_news.index') }}"
    data-label="Edit"
    aria-label="Edit DotUni news">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Hard delete — only for authorized roles, disabled when public --}}
@if(auth()->user()->hasAnyRole(['admin','editor']))
    <form
        action="{{ route('admin.dotuni_news.destroy', $item) }}"
        method="POST"
        class="d-inline ajax-delete-dotuni-news">
        @csrf
        @method('DELETE')
        @if($item->visibility === 'public')
            <button
                type="button"
                class="btn-icon btn-icon-delete"
                style="opacity:0.38; cursor:not-allowed;"
                data-label="Set visibility to private first or unlisted first"
                aria-label="Cannot delete — set visibility to private or unlisted first"
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
                aria-label="Delete DotUni news">
                <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
        @endif
    </form>
@endif