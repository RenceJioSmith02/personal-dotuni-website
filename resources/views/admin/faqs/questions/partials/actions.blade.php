<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#faqQuestionModal"
    data-form="#faqQuestionForm"
    data-title="Edit FAQ Question"
    data-url="{{ route('admin.faqs_questions.index') }}">
    Edit
</button>

{{-- ✅ Archive / Unarchive toggle --}}
@if(is_null($item->deleted_at))
    <form
        action="{{ route('admin.faqs_questions.archive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-faq-question">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-warning">Archive</button>
    </form>
@else
    <form
        action="{{ route('admin.faqs_questions.unarchive', $item) }}"
        method="POST"
        class="d-inline ajax-archive-faq-question">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm btn-secondary">Unarchive</button>
    </form>
@endif

{{-- ✅ Hard delete — only enabled when inactive --}}
<form
    action="{{ route('admin.faqs_questions.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-faq-question">
    @csrf
    @method('DELETE')
    <button
        type="submit"
        class="btn btn-sm btn-danger"
        @if($item->is_active)
            disabled
            title="Deactivate the question before deleting"
        @endif>
        Delete
    </button>
</form>