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
                    <th>Code</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Units</th>
                    <th>Prerequisite</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>{{ $course->code }}</td>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->description }}</td>
                    <td>{{ $course->units }}</td>
                    <td>{{ $course->prerequisite }}</td>
                    <td>
                        {!! $course->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>' !!}
                    </td>
                    <td>
                        <button
                        class="open-modal btn btn-sm btn-info"
                        data-action="edit"
                        data-modal="#courseModal"
                        data-form="#courseForm"
                        data-title="Edit Course"
                        data-url="/admin/courses"
                        data-id="{{ $course->id }}">
                        Edit
                        </button>

                        <form action="{{ route('admin.courses.destroy', $course) }}"
                            method="POST"
                            class="d-inline ajax-delete-course">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
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

@include('admin.courses.partials.course-modal')

@stop


@push('js')
    <script>
    $(function () {
        if ($.fn.DataTable.isDataTable('#coursesTable')) {
            $('#coursesTable').DataTable().destroy();
        }

        $('#coursesTable').DataTable({
            responsive: true,
            autoWidth: false,
            ordering: true,
            pageLength: 10,
            columnDefs: [{ orderable: false, targets: 6 }]
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



