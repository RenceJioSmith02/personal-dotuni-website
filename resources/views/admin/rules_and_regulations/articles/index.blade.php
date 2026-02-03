@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Rule Articles')

@section('content_header')
    <h1>Rule Articles</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

        <!-- Add Article -->
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#articleModal"
            data-form="#articleForm"
            data-title="Add Article"
            data-url="/admin/rule_articles">
            <i class="fas fa-plus mr-1"></i> Add Article
        </button>

    </div>

    <div class="card-body">
        <table id="articlesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Title</th>
                    <th>Sort Order</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- Article Modal --}}
@include('admin.rules_and_regulations.articles.partials.article-modal')
@stop

@push('js')
<script>
$(function () {
    if ($.fn.DataTable.isDataTable('#articlesTable')) {
        $('#articlesTable').DataTable().destroy();
    }

    const table = $('#articlesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.rule_articles.index') }}",
            type: "GET"
        },
        columns: [
            { data: 'number', name: 'number' },
            { data: 'title', name: 'title' },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    
    $(document).on("submit", ".ajax-delete-article", function(e) {
        e.preventDefault();
        const form = $(this), url = form.attr("action"), row = form.closest("tr");

        Swal.fire({
            title: "Delete this article?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#dc3545",
            reverseButtons: true
        }).then(result => {
            if (!result.value) return;

            $.ajax({
                url: url,
                type: "POST",
                data: { _token: $('meta[name="csrf-token"]').attr("content"), _method: "DELETE" },
                success: function(res) {
                    Swal.fire({ type: "success", title: "Deleted", text: res.message, timer: 1200, showConfirmButton: false });
                    table.row(row).remove().draw(false);
                },
                error: function(xhr) {
                    Swal.fire({ type: "error", title: "Delete failed", text: xhr.responseJSON?.message || "Something went wrong" });
                }
            });
        });
    });
});
</script>
@endpush
