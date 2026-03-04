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
                    <th width="10">#</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- @include('admin.faqs.answers.partials.answer-modal') --}}

@stop


@push('js')
<script>
$(function () {
    if ($.fn.DataTable.isDataTable('#faqAnswersTable')) {
        $('#faqAnswersTable').DataTable().destroy();
    }

    const table = $('#faqAnswersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            ulr: "{{ route('admin.faqs_answers.index') }}",
            type: "GET",
            dataSrc: function(json) {
                console.log('Answers returned:', json.data.length);
                return json.data;
            }
        },
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                textAlign: "center",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'question' },
            { data: 'answer' },
            { data: 'status', orderable: false, searchable: false },
            {
                data: "created_at",
                render: (data) =>
                    new Date(data).toLocaleString()
            },
            {
                data: "updated_at",
                render: (data) =>
                    new Date(data).toLocaleString()
            },
            { data: 'actions', orderable: false, searchable: false },
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
