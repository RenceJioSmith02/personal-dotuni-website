@extends('adminlte::page')

@section('title', 'Program Builder')

@section('content_header')
<h1>Program Builder: {{ $program->title }}</h1>
@stop

@section('content')

{{-- Requirements --}}
<div class="card mb-3">
    <div class="card-header">
        <h3>Requirements (Unit Summary)</h3>
        <button class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#addRequirementModal">Add Requirement</button>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="requirementsTable">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Required Units</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($program->requirements as $req)
                <tr id="req-{{ $req->id }}">
                    <td>{{ $req->category->name }}</td>
                    <td>{{ $req->required_units }}</td>
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
        <button class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#addCourseModal">Add Course</button>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="coursesTable">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Units</th>
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
@include('admin.programs.partials.add-requirement-modal')
@include('admin.programs.partials.add-course-modal')

@stop

@section('js')
<script>
$(document).ready(function(){

    // CSRF token for AJAX
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    // Delete Requirement
    $('.delete-requirement').click(function(){
        let id = $(this).data('id');
        if(confirm('Delete this requirement?')){
            $.ajax({
                url: "{{ url('admin/programs/'.$program->id.'/requirements') }}/" + id + "/ajax",
                type: 'DELETE',
                success: function(resp){
                    $('#req-' + id).remove();
                }
            });
        }
    });

    // Delete Course
    $('.delete-course').click(function(){
        let id = $(this).data('id');
        if(confirm('Delete this course?')){
            $.ajax({
                url: "{{ url('admin/programs/'.$program->id.'/courses') }}/" + id + "/ajax",
                type: 'DELETE',
                success: function(resp){
                    $('#course-' + id).remove();
                }
            });
        }
    });

    // Add Requirement Form
    $('#addRequirementForm').submit(function(e){
        e.preventDefault();
        $.post("{{ route('admin.programs.requirements.store.ajax', $program) }}", 
            $(this).serialize(), 
            function(resp){
            // reload table or append row dynamically
            location.reload();
        });
    });

    $('#addCourseForm').submit(function(e){
        e.preventDefault();
        $.post("{{ route('admin.programs.courses.store.ajax', $program) }}", 
        $(this).serialize(), 
        function(resp){
            location.reload();
        });
    });


    

});
</script>
@stop
