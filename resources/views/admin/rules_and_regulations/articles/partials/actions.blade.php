{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $article->id }}"
    data-modal="#articleModal"
    data-form="#articleForm"
    data-title="Edit Article"
    data-url="{{ route('admin.rule_articles.index') }}"
    data-label="Edit"
    aria-label="Edit rule article">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($article->deleted_at))
    <form
        action="{{ route('admin.rule_articles.archive', $article) }}"
        method="POST"
        class="d-inline ajax-archive-rule-article">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive rule article">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.rule_articles.unarchive', $article) }}"
        method="POST"
        class="d-inline ajax-archive-rule-article">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive rule article">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.rule_articles.destroy', $article) }}"
    method="POST"
    class="d-inline ajax-delete-article">
    @csrf
    @method('DELETE')
    @if($article->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the article first"
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
            aria-label="Delete rule article">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>