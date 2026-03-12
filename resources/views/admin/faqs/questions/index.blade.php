@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'FAQ Management')

@section('content_header')
    <div class="card-header">
        <h3 class="card-title-dt">FAQ Management</h3>
        <div class="card-header-actions">
            <button class="open-modal btn btn-primary" data-action="add" data-modal="#faqQuestionModal"
                data-form="#faqQuestionForm" data-title="Add FAQ Question"
                data-url="{{ route('admin.faqs_questions.store') }}">
                <i class="fas fa-plus"></i> Add Question
            </button>
        </div>
    </div>
@stop

@section('content')

<div class="card">
    <div class="card-body">
        <table id="faqTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="30"></th>
                    <th>#</th>
                    <th>Question</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th width="130">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('admin.faqs.questions.partials.question-modal')
@include('admin.faqs.questions.partials.answer-modal')

@stop

@push('js')
<script>
    window.faqAnswerDestroyUrl = "{{ route('admin.faqs_answers.destroy', ':id') }}";
</script>

<script>
$(function () {

    /* ─────────────────────────────────────────
       Track which question row is open so we
       can reopen it after table reloads.
    ───────────────────────────────────────── */
    let openFaqId = null;

    /* ─────────────────────────────────────────
       Init DataTable
    ───────────────────────────────────────── */
    let table;

    if ($.fn.DataTable.isDataTable('#faqTable')) {
        table = $('#faqTable').DataTable();
        table.clear().destroy();
    }

    table = $('#faqTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        scrollCollapse: true,
        scrollX: true,
        ajax: "{{ route('admin.faqs_questions.index') }}",
        columns: [
            {
                className: 'dt-control text-center',
                orderable: false,
                searchable: false,
                data: null,
                defaultContent: '<i class="fas fa-chevron-right" style="color:#038303;font-size:12px;"></i>'
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
            { data: 'created_at', render: data => new Date(data).toLocaleString() },
            { data: 'actions', orderable: false, searchable: false }
        ],
    });

    /* Reopen child row after every draw — fires after row nodes are in DOM */
    table.on('draw', function () {
        if (!openFaqId) return;
        table.rows().every(function () {
            const rowData = this.data();
            if (rowData && String(rowData.id) === String(openFaqId)) {
                const tr   = $(this.node());
                const icon = tr.find('td.dt-control i');
                this.child(renderAnswers(rowData)).show();
                tr.addClass('shown');
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            }
        });
    });


    /* ─────────────────────────────────────────
       Expand / Collapse child row
    ───────────────────────────────────────── */
    $('#faqTable tbody').on('click', 'td.dt-control', function () {
        const tr      = $(this).closest('tr');
        const row     = table.row(tr);
        const icon    = $(this).find('i');
        const rowData = row.data();
        if (!rowData) return;

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            openFaqId = null;
        } else {
            row.child(renderAnswers(rowData)).show();
            tr.addClass('shown');
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            openFaqId = rowData.id;
        }
    });


    /* ─────────────────────────────────────────
       Render answers child row HTML
    ───────────────────────────────────────── */
    function renderAnswers(rowData) {
        const csrf = $('meta[name="csrf-token"]').attr('content');

        let html = `
            <div style="padding:12px 16px; background:#f4f8f4; display: block !important;">
                <div style="margin-bottom:10px;">
                    <button class="btn btn-primary btn-sm add-answer" data-faq="${rowData.id}"
                        style="font-size:13px; font-weight:600; border-radius:6px; padding:6px 14px;">
                        <i class="fas fa-plus mr-1" aria-hidden="true"></i> Add Answer
                    </button>
                </div>
        `;

        if (!rowData.answers || rowData.answers.length === 0) {
            return html + `
                <div style="text-align:center; color:#5c7a5c; padding:16px 0; font-size:14px; background:#fff; border-radius:8px; border:1px solid #d8e8d8;">
                    <i class="fas fa-inbox" style="font-size:22px; color:#c6ddc6; display:block; margin-bottom:6px;"></i>
                    No answers yet.
                </div>
            </div>`;
        }

        html += `
            <table style="width:100%; border-collapse:separate; border-spacing:0; background:#fff; border-radius:9px; border:1px solid #d8e8d8; overflow:hidden; font-size:13px;">
                <thead>
                    <tr style="background:#eaf3ea;">
                        <th style="padding:9px 14px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#1a2b1a; border-bottom:2px solid #c6ddc6; width:50%;">Answer</th>
                        <th style="padding:9px 14px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#1a2b1a; border-bottom:2px solid #c6ddc6;">Status</th>
                        <th style="padding:9px 14px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#1a2b1a; border-bottom:2px solid #c6ddc6; text-align:center; white-space:nowrap;">Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

        rowData.answers.forEach((answer, index) => {
            const isArchived = answer.archived;
            const isActive   = answer.status;
            const rowBg      = index % 2 === 1 ? '#fafcfa' : '#ffffff';

            const archiveBtn = isArchived
                ? `<form action="/admin/faqs-answers/${answer.id}/unarchive" method="POST" class="d-inline ajax-archive-faq-answer">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="btn-icon btn-icon-unarchive" data-label="Unarchive" aria-label="Unarchive answer">
                            <i class="fas fa-box-open" aria-hidden="true"></i>
                        </button>
                    </form>`
                : `<form action="/admin/faqs-answers/${answer.id}/archive" method="POST" class="d-inline ajax-archive-faq-answer">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="btn-icon btn-icon-archive" data-label="Archive" aria-label="Archive answer">
                            <i class="fas fa-archive" aria-hidden="true"></i>
                        </button>
                    </form>`;

            const deleteBtn = isActive
                ? `<form action="/admin/faqs_answers/${answer.id}" method="POST" class="d-inline ajax-delete-faq-answer">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn-icon btn-icon-delete"
                            style="opacity:0.38; cursor:not-allowed;"
                            data-label="Deactivate before deleting"
                            aria-label="Cannot delete — deactivate the answer first"
                            aria-disabled="true" tabindex="-1" disabled>
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                    </form>`
                : `<form action="/admin/faqs_answers/${answer.id}" method="POST" class="d-inline ajax-delete-faq-answer">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn-icon btn-icon-delete"
                            data-label="Delete" aria-label="Delete answer">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                    </form>`;

            html += `
                <tr style="background:${rowBg};">
                    <td style="padding:11px 14px; border-bottom:1px solid #eef5ee; color:#1a2b1a; word-break:break-word; white-space:normal; line-height:1.5;">
                        ${answer.answer}
                    </td>
                    <td style="padding:11px 14px; border-bottom:1px solid #eef5ee; white-space:nowrap;">
                        ${isActive
                            ? '<span class="badge badge-active">Active</span>'
                            : '<span class="badge badge-inactive">Inactive</span>'}
                        ${isArchived
                            ? '<span class="badge badge-archived ml-1">Archived</span>'
                            : ''}
                    </td>
                    <td style="padding:11px 14px; border-bottom:1px solid #eef5ee; text-align:center; white-space:nowrap;">
                        <div style="display:inline-flex; gap:4px; align-items:center;">
                            <button
                                class="open-modal btn-icon btn-icon-edit"
                                data-action="edit"
                                data-id="${answer.id}"
                                data-modal="#faqAnswerModal"
                                data-form="#faqAnswerForm"
                                data-title="Edit FAQ Answer"
                                data-url="{{ route('admin.faqs_answers.index') }}"
                                data-label="Edit"
                                aria-label="Edit answer">
                                <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                            </button>
                            ${archiveBtn}
                            ${deleteBtn}
                        </div>
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        return html;
    }


    /* ─────────────────────────────────────────
       Add Answer button
    ───────────────────────────────────────── */
    $(document).on('click', '.add-answer', function () {
        const faqId = $(this).data('faq');
        $('#faqAnswerForm')[0].reset();
        $('#faqAnswerForm .form-method').val('POST');
        $('#faqAnswerForm').attr('action', "{{ route('admin.faqs_answers.store') }}");
        $('#faqAnswerForm select[name="faq_id"]').val(faqId);
        $('#faqAnswerModal').modal('show');
    });


    /* ─────────────────────────────────────────
       Delete FAQ Question
    ───────────────────────────────────────── */
    $(document).on('submit', '.ajax-delete-faq-question', function (e) {
        e.preventDefault();
        const form = $(this);
        const row  = form.closest('tr');

        Swal.fire({
            title: 'Delete this FAQ question?',
            text: 'This action cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            confirmButtonColor: '#b42318',
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: function (res) {
                    Swal.fire({ type: 'success', title: 'Deleted', text: res.message, timer: 1200, showConfirmButton: false });
                    table.row(row).remove().draw(false);
                },
                error: function (xhr) {
                    Swal.fire({ type: 'error', title: 'Error', text: xhr.responseJSON?.message ?? 'Failed to delete FAQ question.' });
                }
            });
        });
    });


    /* ─────────────────────────────────────────
       Delete FAQ Answer
    ───────────────────────────────────────── */
    $(document).on('submit', '.ajax-delete-faq-answer', function (e) {
        e.preventDefault();
        const form = $(this);

        Swal.fire({
            title: 'Delete this answer?',
            text: 'This action cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            confirmButtonColor: '#b42318',
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: function (res) {
                    Swal.fire({ type: 'success', title: 'Deleted', text: res.message, timer: 1200, showConfirmButton: false });
                    /* Remove the <tr> row then reload — openFaqId keeps child row open */
                    form.closest('tr').fadeOut(200, function () {
                        $(this).remove();
                        table.ajax.reload(null, false);
                    });
                },
                error: function () {
                    Swal.fire({ type: 'error', title: 'Error', text: 'Failed to delete answer.' });
                }
            });
        });
    });


    /* ─────────────────────────────────────────
       Archive / Unarchive FAQ Question
    ───────────────────────────────────────── */
    $(document).on('submit', '.ajax-archive-faq-question', function (e) {
        e.preventDefault();
        const form      = $(this);
        const url       = form.attr('action');
        const isArchive = url.includes('/archive') && !url.includes('/unarchive');

        Swal.fire({
            title: isArchive ? 'Archive this question?' : 'Unarchive this question?',
            text:  isArchive ? 'This will mark the question as inactive and archived.' : 'This will restore the question.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: isArchive ? 'Yes, archive it' : 'Yes, unarchive it',
            confirmButtonColor: isArchive ? '#9a3412' : '#0f766e',
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url, type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'PATCH' },
                success: function (res) {
                    Swal.fire({ type: 'success', title: isArchive ? 'Archived' : 'Unarchived', text: res.message, timer: 1200, showConfirmButton: false });
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire({ type: 'error', title: 'Action failed', text: xhr.responseJSON?.message ?? 'Something went wrong.' });
                }
            });
        });
    });


    /* ─────────────────────────────────────────
       Archive / Unarchive FAQ Answer
    ───────────────────────────────────────── */
    $(document).on('submit', '.ajax-archive-faq-answer', function (e) {
        e.preventDefault();
        const form      = $(this);
        const url       = form.attr('action');
        const isArchive = url.includes('/archive') && !url.includes('/unarchive');

        Swal.fire({
            title: isArchive ? 'Archive this answer?' : 'Unarchive this answer?',
            text:  isArchive ? 'This will mark the answer as inactive and archived.' : 'This will restore the answer.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: isArchive ? 'Yes, archive it' : 'Yes, unarchive it',
            confirmButtonColor: isArchive ? '#9a3412' : '#0f766e',
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url, type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'PATCH' },
                success: function (res) {
                    Swal.fire({ type: 'success', title: isArchive ? 'Archived' : 'Unarchived', text: res.message, timer: 1200, showConfirmButton: false });
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire({ type: 'error', title: 'Action failed', text: xhr.responseJSON?.message ?? 'Something went wrong.' });
                }
            });
        });
    });

});
</script>
@endpush