@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'FAQ Answers')

@section('content_header')
<h1>FAQ Answers</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#faqAnswerModal"
            data-form="#faqAnswerForm"
            data-title="Add FAQ Answer"
            data-url="{{ route('admin.faqs_answers.store') }}">
            <i class="fas fa-plus"></i> Add Answer
        </button>
    </div>

    <div class="card-body">
        <table id="faqAnswersTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Status</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($answers as $answer)
                <tr data-id="{{ $answer->id }}">
                    <td>
                        {{ $answer->question->question ?? '—' }}
                    </td>

                    <td>
                        {{ Str::limit($answer->answer, 120) }}
                    </td>

                    <td>
                        {!! $answer->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>

                    <td>
                        <button
                            class="open-modal btn btn-sm btn-info"
                            data-action="edit"
                            data-id="{{ $answer->id }}"
                            data-modal="#faqAnswerModal"
                            data-form="#faqAnswerForm"
                            data-title="Edit FAQ Answer"
                            data-url="{{ route('admin.faqs_answers.index') }}">
                            Edit
                        </button>

                        <form
                            action="{{ route('admin.faqs_answers.destroy', $answer) }}"
                            method="POST"
                            class="d-inline ajax-delete-faq-answer">
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

@include('admin.faqs.answers.partials.answer-modal')

@stop


@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#faqAnswersTable')) {
        $('#faqAnswersTable').DataTable().destroy();
    }

    const table = $('#faqAnswersTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [3] }
        ]
    });

});


/* DELETE FAQ ANSWER (AJAX) */
$(document).on("submit", ".ajax-delete-faq-answer", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#faqAnswersTable").DataTable();

    Swal.fire({
        title: "Delete this FAQ answer?",
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
                    text: "Failed to delete FAQ answer."
                });
            }
        });
    });
});
</script>
@endpush
