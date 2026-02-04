@extends('layouts.admin')

@section('plugins.Datatables', true)
@section('title', 'Courses')

@section('content_header')
    <h1>Courses</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <!-- Add Course -->
        <button
        class="open-modal btn btn-primary"
        data-action="add"
        data-modal="#courseModal"
        data-form="#courseForm"
        data-title="Add Course"
        data-url="/admin/courses">
        Add Course
        </button>

    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="coursesTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Units</th>
                    <th>Prerequisite</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('admin.academic.courses.partials.course-modal')

@stop


@push('js')
    <script>
    $(function () {
        if ($.fn.DataTable.isDataTable('#coursesTable')) {
            $('#coursesTable').DataTable().destroy();
        }

        const table = $('#coursesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            lengthMenu: [10, 20, 50, 100],

            ajax: {
                url: "{{ route('admin.courses.index') }}",
                type: "GET",
                // dataSrc: function (json) {
                //     console.log('Courses returned:', json.data.length);
                //     return json.data;
                // }
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
                { data: "code" },
                { data: "title" },
                { data: "description" },
                { data: "units" },
                { data: "prerequisite" },
                {
                    data: "status",
                    render: (data) =>
                        data
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>'
                },
                {
                    data: "actions",
                    orderable: false,
                    searchable: false
                }
            ]
        });


    });


    $(document).on("submit", ".ajax-delete-course", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr("action");
        const row = form.closest("tr");
        const table = $("#coursesTable").DataTable();

        Swal.fire({
            title: "Delete this course?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#dc3545",
            reverseButtons: true
        }).then((result) => {

            // ✅ IMPORTANT FIX
            if (!result.value) return;

            $.ajax({
                url: url,
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
                error: function (xhr) {
                    Swal.fire({
                        type: "error",
                        title: "Delete failed",
                        text: xhr.responseJSON?.message || "Something went wrong"
                    });
                }
            });
        });
    });


    </script>
@endpush



