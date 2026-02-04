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
                    <th>#</th>
                    <th>Question</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

@include('admin.faqs.questions.partials.question-modal')

@stop


@push('js')
<script>

$(function () {

    if ($.fn.DataTable.isDataTable('#faqQuestionsTable')) {
        $('#faqQuestionsTable').DataTable().destroy();
    }

    const table = $('#faqQuestionsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            ulr: "{{ route('admin.faqs_questions.index') }}",
            type: "GET",
            dataSrc: function(json) {
                console.log('Questions returned:', json.data.length);
                return json.data;
            }
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'question' },
            { data: 'sort_order' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false },
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

                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                let message = "Failed to delete FAQ question.";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message; 
                }

                Swal.fire({
                    type: "error", 
                    title: "Error",
                    text: message
                });
            }

        });
    });
});


</script>
@endpush
