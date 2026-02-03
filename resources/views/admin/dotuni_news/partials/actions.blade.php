<button class="open-modal btn btn-sm btn-info" data-action="edit" data-id="{{ $item->id }}"
    data-modal="#dotuniNewsModal" data-form="#dotuniNewsForm" data-title="Edit DotUni News"
    data-url="{{ route('admin.dotuni_news.index') }}">
    Edit
</button>

<form action="{{ route('admin.dotuni_news.destroy', $item) }}" method="POST" class="d-inline ajax-delete-dotuni-news">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>