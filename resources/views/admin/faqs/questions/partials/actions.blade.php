{{-- Edit --}}
<button
    class="open-modal btn-icon btn-icon-edit"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#faqQuestionModal"
    data-form="#faqQuestionForm"
    data-title="Edit FAQ Question"
    data-url="{{ route('admin.faqs_questions.index') }}"
    data-label="Edit"
    aria-label="Edit FAQ question">
    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
</button>

{{-- Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.faqs_questions.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-faq-question">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-archive"
            data-label="Archive"
            aria-label="Archive FAQ question">
            <i class="fas fa-archive" aria-hidden="true"></i>
        </button>
    </form>
@else
    <form
        action="{{ route('admin.faqs_questions.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-faq-question">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="btn-icon btn-icon-unarchive"
            data-label="Unarchive"
            aria-label="Unarchive FAQ question">
            <i class="fas fa-box-open" aria-hidden="true"></i>
        </button>
    </form>
@endif

{{-- Hard delete — only when inactive --}}
<form
    action="{{ route('admin.faqs_questions.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-faq-question">
    @csrf
    @method('DELETE')
    @if($item->is_active)
        <button
            type="button"
            class="btn-icon btn-icon-delete"
            style="opacity:0.38; cursor:not-allowed;"
            data-label="Archive before deleting"
            aria-label="Cannot delete — archive the question first"
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
            aria-label="Delete FAQ question">
            <i class="fas fa-trash" aria-hidden="true"></i>
        </button>
    @endif
</form>