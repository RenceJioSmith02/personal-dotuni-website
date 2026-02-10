@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'FAQ Management')

@section('content_header')
<h1>FAQ Management</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <button class="open-modal btn btn-primary" data-action="add" data-modal="#faqQuestionModal"
            data-form="#faqQuestionForm" data-title="Add FAQ Question"
            data-url="{{ route('admin.faqs_questions.store') }}">
            <i class="fas fa-plus"></i> Add Question
        </button>
    </div>

    <div class="card-body">
        <table id="faqTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="30"></th>
                    <th>#</th>
                    <th>Question</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('admin.faqs.questions.partials.question-modal')
@include('admin.faqs.answers.partials.answer-modal')

@stop

@push('js')

    <script>
        window.faqAnswerDestroyUrl = "{{ route('admin.faqs_answers.destroy', ':id') }}";
    </script>

    <script>

        $(function () {

            /* -----------------------------
               Initialize FAQ Questions Table
            ----------------------------- */
            let table;

            if ($.fn.DataTable.isDataTable('#faqTable')) {
                table = $('#faqTable').DataTable();
                table.clear().destroy();
            }

            table = $('#faqTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('admin.faqs_questions.index') }}",
                columns: [
                    {
                        className: 'dt-control text-center',
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '<i class="fas fa-chevron-right"></i>'
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: (data, type, row, meta) =>
                            meta.row + meta.settings._iDisplayStart + 1
                    },
                    { data: 'question' },
                    { data: 'status', orderable: false, searchable: false },
                    {
                        data: 'created_at',
                        render: data => new Date(data).toLocaleString()
                    },
                    { data: 'actions', orderable: false, searchable: false }
                ]
            });


            /* -----------------------------
               Expand / Collapse Answers
            ----------------------------- */
            $('#faqTable tbody').on('click', 'td.dt-control', function () {
                const tr = $(this).closest('tr');
                const row = table.row(tr);
                const icon = $(this).find('i');

                const rowData = row.data();
                if (!rowData) return;

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                } else {
                    row.child(renderAnswers(rowData)).show();
                    tr.addClass('shown');
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                }
            });

            /* -----------------------------
               Render Answers in Child Row
            ----------------------------- */
            function renderAnswers(rowData) {
                let html = `
            <div class="p-2">
                <button
                    class="btn btn-sm btn-primary mb-2 add-answer"
                    data-faq="${rowData.id}">
                    <i class="fas fa-plus"></i> Add Answer
                </button>
        `;

                if (!rowData.answers || rowData.answers.length === 0) {
                    return html + `<div class="text-muted">No answers yet.</div></div>`;
                }

                html += `<ul class="list-group">`;

                rowData.answers.forEach(answer => {
                    html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        ${answer.answer}
                        <div class="mt-1">
                            ${answer.status
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>'}
                        </div>
                    </div>

                    <div class="d-flex">
                        <!-- EDIT (old modal system) -->
                        <button
                            class="open-modal btn btn-sm btn-info mr-1"
                            data-action="edit"
                            data-id="${answer.id}"
                            data-modal="#faqAnswerModal"
                            data-form="#faqAnswerForm"
                            data-title="Edit FAQ Answer"
                            data-url="{{ route('admin.faqs_answers.index') }}">
                            Edit
                        </button>

                        <!-- DELETE (old ajax-delete form) -->
                        <form
                            action="/admin/faqs_answers/${answer.id}"
                            method="POST"
                            class="d-inline ajax-delete-faq-answer">
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </li>
            `;
                });

                return html + `</ul></div>`;
            }

            /* -----------------------------
               Add Answer Button
            ----------------------------- */
            $(document).on('click', '.add-answer', function () {
                const faqId = $(this).data('faq');
                $('#faqAnswerForm')[0].reset();
                $('#faqAnswerForm .form-method').val('POST');
                $('#faqAnswerForm').attr('action', "{{ route('admin.faqs_answers.store') }}");
                $('#faqAnswerForm select[name="faq_id"]').val(faqId);
                $('#faqAnswerModal').modal('show');
            });

            /* -----------------------------
               Delete FAQ Question
            ----------------------------- */
            $(document).on("submit", ".ajax-delete-faq-question", function (e) {
                e.preventDefault();

                const form = $(this);
                const row = form.closest("tr");
                const table = $("#faqTable").DataTable();

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

                            table
                            .row(row)
                            .remove()
                            .draw(false);
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

            /* -----------------------------
               Delete FAQ Answer
               (Remove <li> directly)
            ----------------------------- */
            $(document).on("submit", ".ajax-delete-faq-answer", function (e) {
                e.preventDefault();

                const form = $(this);
                const row = form.closest("tr");
                const table = $("#faqTable").DataTable();

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

                            form.closest("li").fadeOut(200, function () {
                                $(this).remove();
                            });

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

            /* -----------------------------
               Edit FAQ Answer
               (Prefill Modal)
            ----------------------------- */
            $(document).on('click', '.edit-answer', function () {
                const answerId = $(this).data('id');
                const faqId = $(this).data('faq');

                $.get(`/admin/faqs-answers/${answerId}/edit`, function (res) {
                    $('#faqAnswerForm')[0].reset();
                    $('#faqAnswerForm .form-method').val('PUT');
                    $('#faqAnswerForm').attr('action', `/admin/faqs-answers/${answerId}`);
                    $('#faqAnswerForm select[name="faq_id"]').val(faqId);
                    $('#faqAnswerForm textarea[name="answer"]').val(res.answer);
                    $('#faqAnswerForm input[name="is_active"]').prop('checked', res.is_active);
                    $('#faqAnswerModal').modal('show');
                });
            });

        });


    </script>
@endpush