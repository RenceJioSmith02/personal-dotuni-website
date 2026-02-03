<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $article->id }}"
    data-modal="#articleModal"
    data-form="#articleForm"
    data-title="Edit Article"
    data-url="{{ route('admin.rule_articles.index') }}">
    Edit
</button>

<form
    action="{{ route('admin.rule_articles.destroy', $article) }}"
    method="POST"
    class="d-inline ajax-delete-article">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">
        Delete
    </button>
</form>
