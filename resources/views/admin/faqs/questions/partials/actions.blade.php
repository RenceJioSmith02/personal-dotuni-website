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

<form
    action="{{ route('admin.faqs_questions.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-faq-question">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Delete</button>
</form>
