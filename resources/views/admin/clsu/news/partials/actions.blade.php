
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

<form
    action="{{ route('admin.clsu_news.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-news">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
