@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'DotUni News')

@section('content_header')
<h1>DotUni News</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#dotuniNewsModal"
            data-form="#dotuniNewsForm"
            data-title="Add DotUni News"
            data-url="{{ route('admin.dotuni_news.store') }}">
            <i class="fas fa-plus"></i> Add News
        </button>
    </div>

    <div class="card-body">
        <table id="dotuniNewsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>SEO Description</th>
                    <th>Status</th>
                    <th>Visibility</th>
                    <th>Published</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- DotUni News Modal --}}
@include('admin.dotuni_news.partials.dotuni-news-modal')

@stop

@push('js')

<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#dotuniNewsTable')) {
        $('#dotuniNewsTable').DataTable().destroy();
    }

    const table = $('#dotuniNewsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.dotuni_news.index') }}",
            type: "GET",
            // dataSrc: function(json) {
            //     console.log('News returned:', json.data.length);
            //     return json.data;
            // }
        },
        columns: [
            { data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'seo_description', name: 'seo_description' },
            { data: 'status', name: 'status' },
            { data: 'visibility', name: 'visibility' },
            { data: 'published_at', name: 'published_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        pageLength: 10,
    });

});

/* DELETE DOTUNI NEWS (AJAX) */
$(document).on("submit", ".ajax-delete-dotuni-news", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#dotuniNewsTable").DataTable();

    Swal.fire({
        title: "Delete this news?",
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
                    text: res.message || "News deleted successfully",
                    timer: 1200,
                    showConfirmButton: false
                });

                table.row(row).remove().draw(false);
            },
            error: function () {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: "Failed to delete news."
                });
            }
        });
    });
});
</script>
@endpush
