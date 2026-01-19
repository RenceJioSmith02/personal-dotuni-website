@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'FAQ Questions')

@section('content_header')
<h1>FAQ Questions</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#faqQuestionModal"
            data-form="#faqQuestionForm"
            data-title="Add FAQ Question"
            data-url="{{ route('admin.faqs_questions.store') }}">
            <i class="fas fa-plus"></i> Add Question
        </button>
    </div>

    <div class="card-body">
        <table id="faqQuestionsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $question)
                <tr data-id="{{ $question->id }}">
                    <td>{{ $question->question }}</td>

                    <td class="text-center">
                        {{ $question->sort_order }}
                    </td>

                    <td>
                        {!! $question->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>

                    <td>
                        <button
                            class="open-modal btn btn-sm btn-warning"
                            data-action="edit"
                            data-id="{{ $question->id }}"
                            data-modal="#faqQuestionModal"
                            data-form="#faqQuestionForm"
                            data-title="Edit FAQ Question"
                            data-url="{{ route('admin.faqs_questions.index') }}">
                            Edit
                        </button>

                        <form
                            action="{{ route('admin.faqs_questions.destroy', $question) }}"
                            method="POST"
                            class="d-inline ajax-delete-faq-question">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('admin.faqs_questions.partials.question-modal')

@stop


@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#faqQuestionsTable')) {
        $('#faqQuestionsTable').DataTable().destroy();
    }

    const table = $('#faqQuestionsTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [3] }
        ]
    });

});


/* DELETE FAQ QUESTION (AJAX) */
$(document).on("submit", ".ajax-delete-faq-question", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#faqQuestionsTable").DataTable();

    Swal.fire({
        title: "Delete this FAQ question?",
        text: "This action cannot be undone.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it",
        confirmButtonColor: "#dc3545",
        reverseButtons: true
    }).then((result) => {
        if (!result.value) return;

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                _method: "DELETE"
            },
            success: function (res) {
                Swal.fire({
                    type: "success",
                    title: "Deleted",
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });

                table.row(row).remove().draw(false);
            },
            error: function () {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: "Failed to delete FAQ question."
                });
            }
        });
    });
});
</script>
@endpush
