
<div class="btn-group btn-group-sm" role="group" style="gap: 8px;">

    @if($item->status === 'submitted' && auth()->user()->hasAnyRole(['admin','publisher']))
        <form action="{{ route('admin.dotuni_news.publish', $item) }}"
              method="POST"
              class="d-inline ajax-publish-dotuni-news m-0 p-0">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">
                Publish
            </button>
        </form>
    @endif

    <button type="button"
        class="open-modal btn btn-info rounded"
        data-action="edit"
        data-id="{{ $item->id }}"
        data-modal="#dotuniNewsModal"
        data-form="#dotuniNewsForm"
        data-title="Edit DotUni News"
        data-url="{{ route('admin.dotuni_news.index') }}">
        Edit
    </button>

    @if(auth()->user()->hasAnyRole(['admin','editor']))
        <form action="{{ route('admin.dotuni_news.destroy', $item) }}"
              method="POST"
              class="d-inline ajax-delete-dotuni-news m-0 p-0">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                Delete
            </button>
        </form>
    @endif

</div>
