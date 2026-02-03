<button
    class="open-modal btn btn-sm btn-info"
    data-action="edit"
    data-id="{{ $item->id }}"
    data-modal="#faqAnswerModal"
    data-form="#faqAnswerForm"
    data-title="Edit FAQ Answer"
    data-url="{{ route('admin.faqs_answers.index') }}">
    Edit
</button>

<form
    action="{{ route('admin.faqs_answers.destroy', $item) }}"
    method="POST"
    class="d-inline ajax-delete-faq-answer">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Delete</button>
</form>
