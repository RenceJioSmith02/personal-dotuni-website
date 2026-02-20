@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Announcements')

@section('content_header')
<h1>Announcements</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <button
            class="open-modal btn btn-primary"
            data-action="add"
            data-modal="#announcementModal"
            data-form="#announcementForm"
            data-title="Add Announcement"
            data-url="{{ route('admin.announcements.store') }}">
            <i class="fas fa-plus"></i> Add Announcement
        </button>
    </div>

    <div class="card-body">
        <table id="announcementsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Title</th>
                    <th>File</th>
                    <th>SEO Description</th>
                    <th>Layout</th>
                    <th>Visibility</th>
                    <th>Publish Window</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

        </table>
    </div>
</div>

{{-- Announcement Modal --}}
@include('admin.clsu.announcement.partials.announcement-modal')

@stop

@push('js')
<script>
$(function () {

    if ($.fn.DataTable.isDataTable('#announcementsTable')) {
        $('#announcementsTable').DataTable().destroy();
    }

    const table = $('#announcementsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 20, 50, 100],
        ajax: {
            url: "{{ route('admin.announcements.index') }}",
            type: "GET",
            // dataSrc: function(json) {
            //     console.log('Announcements returned:', json.data.length);
            //     return json.data;
            // }
        },
        columnDefs: [
        {
                targets: [2],
                render: function(data){
                    return data;
                }
            }
        ],
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                textAlign: 'center',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'title' },
            { data: 'file', orderable:false, searchable:false },
            { data: 'seo_description' },
            { data: 'layout' },
            { data: 'visibility' },
            { data: 'publish_window' },
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
            { data: 'actions', orderable: false, searchable: false }
        ]
    });


});

/* DELETE ANNOUNCEMENT (AJAX) */
$(document).on("submit", ".ajax-delete-announcement", function (e) {
    e.preventDefault();

    const form = $(this);
    const row = form.closest("tr");
    const table = $("#announcementsTable").DataTable();

    Swal.fire({
        title: "Delete this announcement?",
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
                    text: res.message || "Announcement deleted successfully",
                    timer: 1200,
                    showConfirmButton: false
                });

                table.row(row).remove().draw(false);
            },
            error: function () {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: "Failed to delete announcement."
                });
            }
        });
    });
});
</script>
@endpush
