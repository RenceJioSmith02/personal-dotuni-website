@extends('layouts.admin')

@section('title', 'Program Builder')

@section('content_header')
<h1>Program Builder</h1>
<br>
<ol class="breadcrumb float-sm-left">
    @foreach($breadcrumbs as $bc)
        @if($bc['url'])
            <li class="breadcrumb-item"><a href="{{ $bc['url'] }}">{{ $bc['name'] }}</a></li>
        @else
            <li class="breadcrumb-item active">{{ $bc['name'] }}</li>
        @endif
    @endforeach
</ol>
<br>

@stop

@section('content')

{{-- Program Details --}}
<div class="card mb-3">
    <div class="card-header">
        <h3>{{ $program->title }}</h3>
        <br>
        <p>{{ $program->description }}</p>
    </div>
</div>

{{-- Requirements --}}
<div class="card mb-3">

    <div class="card-header">
        <h3>Requirements (Unit Summary)</h3>
        <button
            class="open-modal btn btn-sm btn-success float-right"
            data-action="add"
            data-modal="#addRequirementModal"
            data-form="#addRequirementForm"
            data-title="Add Requirement"
            data-url="{{ route('admin.programs.requirements.store.ajax', $program) }}"
        >
            Add Requirement
        </button>

    </div>
    <div class="card-body">
        <table class="table table-bordered" id="requirementsTable">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Required Units</th>
                    <th>MS</th>
                    <th>MPS</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($program->requirements as $req)
                <tr id="req-{{ $req->id }}">
                    <td>{{ $req->category->name }}</td>
                    <td>{{ $req->required_units }}</td>
                    <td>{{ $req->ms }}</td>
                    <td>{{ $req->mps }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-requirement" data-id="{{ $req->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Courses --}}
<div class="card mb-3">
    <div class="card-header">
        <h3>Program Structure (Courses)</h3>
        <button
            class="open-modal btn btn-sm btn-success float-right"
            data-action="add"
            data-modal="#addCourseModal"
            data-form="#addCourseForm"
            data-title="Add Course"
            data-url="{{ route('admin.programs.courses.store.ajax', $program) }}"
        >
            Add Course
        </button>

    </div>
    <div class="card-body">
        <table class="table table-bordered" id="coursesTable">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Units</th>
                    <th>Prerequisite</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($program->programCourses as $pc)
                <tr id="course-{{ $pc->id }}">
                    <td>{{ $pc->category->name }}</td>
                    <td>{{ $pc->course->code }}</td>
                    <td>{{ $pc->course->title }}</td>
                    <td>{{ $pc->course->units }}</td>
                    <td>{{ $pc->course->prerequisite }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-course" data-id="{{ $pc->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modals --}}
@include('admin.academic.programs.partials.add-requirement-modal')
@include('admin.academic.programs.partials.add-course-modal')

@stop



@push('js')
<script>
$(document).ready(function(){

    // CSRF setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /* ===========================
       DELETE REQUIREMENT
    =========================== */
    $(document).on("click", ".delete-requirement", function () {

        const btn = $(this);
        const id  = btn.data("id");
        const row = $("#req-" + id);

        Swal.fire({
            title: "Delete this requirement?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            confirmButtonColor: "#dc3545",
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;

            $.ajax({
                url: "{{ url('admin/programs/'.$program->id.'/requirements') }}/" + id + "/ajax",
                type: "DELETE",
                success: function (res) {
                    Swal.fire({
                        type: "success",
                        title: "Deleted",
                        text: res.message ?? "Requirement deleted successfully.",
                        timer: 1200,
                        showConfirmButton: false
                    });

                    row.fadeOut(300, function () {
                        $(this).remove();
                    });
                },
                error: function () {
                    Swal.fire({
                        type: "error",
                        title: "Error",
                        text: "Failed to delete requirement."
                    });
                }
            });
        });
    });

    /* ===========================
       DELETE COURSE
    =========================== */
    $(document).on("click", ".delete-course", function () {

        const btn = $(this);
        const id  = btn.data("id");
        const row = $("#course-" + id);

        Swal.fire({
            title: "Delete this course?",
            text: "This action cannot be undone.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            confirmButtonColor: "#dc3545",
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;

            $.ajax({
                url: "{{ url('admin/programs/'.$program->id.'/courses') }}/" + id + "/ajax",
                type: "DELETE",
                success: function (res) {
                    Swal.fire({
                        type: "success",
                        title: "Deleted",
                        text: res.message ?? "Course deleted successfully.",
                        timer: 1200,
                        showConfirmButton: false
                    });

                    row.fadeOut(300, function () {
                        $(this).remove();
                    });
                },
                error: function () {
                    Swal.fire({
                        type: "error",
                        title: "Error",
                        text: "Failed to delete course."
                    });
                }
            });
        });
    });


});
</script>
@endpush

