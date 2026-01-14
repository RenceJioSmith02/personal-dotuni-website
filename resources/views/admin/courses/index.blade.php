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



{{-- @section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
@stop


@section('js')
<script src="{{ asset('assets/js/modal.js') }}"></script>

<script>
$(document).ready(function () {

    /* ===============================
       GLOBAL SETUP
    =============================== */

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    const modal = $('#courseModal');
    const form  = $('#courseForm');

    /* ===============================
       DATATABLE
    =============================== */

    $('#coursesTable').DataTable({
        responsive: true,
        autoWidth: false,
        ordering: true,
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: 6 }]
    });

    /* ===============================
       MODAL ANIMATION HANDLERS
    =============================== */

    modal.on('shown.bs.modal', function () {
        $(this).find('input:visible:first').focus();
    });

    modal.on('hide.bs.modal', function () {
        $(this).addClass('modal-closing');
        setTimeout(() => {
            $(this).removeClass('modal-closing');
        }, 300);
    });

    modal.on('hidden.bs.modal', function () {
        $(this).removeClass('modal-success modal-error');
        form.trigger('reset');
    });

    /* ===============================
       ADD COURSE
    =============================== */

    $('#addCourseBtn').on('click', function () {
        $('#courseModalTitle').text('Add Course');
        form.trigger('reset');
        $('#courseFormMethod').val('POST');
        form.attr('action', "{{ route('admin.courses.store') }}");

        modal.modal('show');
    });

    /* ===============================
       EDIT COURSE
    =============================== */

    $('.editCourseBtn').on('click', function () {
        const id = $(this).data('id');

        $.get("{{ url('admin/courses') }}/" + id + "/edit", function (data) {

            $('#courseModalTitle').text('Edit Course');
            form.attr('action', "{{ url('admin/courses') }}/" + id);
            $('#courseFormMethod').val('PUT');

            form.find('[name=code]').val(data.code);
            form.find('[name=title]').val(data.title);
            form.find('[name=description]').val(data.description);
            form.find('[name=units]').val(data.units);
            form.find('[name=prerequisite]').val(data.prerequisite ?? '');

            $('#courseForm [name=is_active]').val(
                data.is_active ? '1' : '0'
            );

            modal.modal('show');
        });
    });

    /* ===============================
       SUBMIT FORM (AJAX)
    =============================== */

    form.on('submit', function (e) {
        e.preventDefault();

        const url    = form.attr('action');
        const method = $('#courseFormMethod').val();

        $.ajax({
            url: url,
            type: method,
            data: form.serialize(),

            success: function (res) {

                // Success animation
                modal.addClass('modal-success');

                setTimeout(() => {
                    modal.modal('hide');
                }, 500);

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    timer: 1800,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    location.reload();
                }, 1900);
            },

            error: function (xhr) {

                modal.addClass('modal-error');

                setTimeout(() => {
                    modal.removeClass('modal-error');
                }, 400);

                let html = '<ul class="text-left">';
                $.each(xhr.responseJSON.errors, function (_, msg) {
                    html += `<li>${msg[0]}</li>`;
                });
                html += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: html
                });
            }
        });
    });

});
</script>

@stop --}}
