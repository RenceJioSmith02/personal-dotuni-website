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

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($article->deleted_at))
    <form
        action="{{ route('admin.rule_articles.archive', $article) }}"
        method="POST"
        class="d-inline ajax-archive-rule-article">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">
            Archive
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_articles.unarchive', $article) }}"
        method="POST"
        class="d-inline ajax-archive-rule-article">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">
            Unarchive
        </button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.rule_articles.destroy', $article) }}"
    method="POST"
    class="d-inline ajax-delete-article">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($article->is_active)
            disabled
            title="Deactivate the article before deleting"
        @endif>
        Delete
    </button>
</form>