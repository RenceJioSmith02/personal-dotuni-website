@extends('layouts.admin')

@section('title', 'Program Builder')

@push('css')
<style>
/* ══════════════════════════════════════
   PROGRAM BUILDER — Internal Styles
══════════════════════════════════════ */

/* Hero card */
.pb-hero {
    background: #ffffff;
    border: 1px solid #d8e8d8;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(3,131,3,.07);
    margin-bottom: 20px;
}
.pb-hero-bar {
    height: 4px;
    background: linear-gradient(90deg, #038303, #92d12c);
}
.pb-hero-body {
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 22px;
    flex-wrap: wrap;
}
.pb-hero-img {
    width: 88px;
    height: 88px;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid #d8e8d8;
    flex-shrink: 0;
    background: #f4f8f4;
}
.pb-hero-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.pb-hero-info { flex: 1; min-width: 200px; }
.pb-hero-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 6px;
}
.pb-hero-title-row h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1a2b1a;
    margin: 0;
}
.pb-hero-desc {
    color: #5c7a5c;
    font-size: 14px;
    margin: 0;
    line-height: 1.55;
}

/* Shared card */
.pb-card {
    background: #ffffff;
    border: 1px solid #d8e8d8;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(3,131,3,.07);
    margin-bottom: 20px;
}
.pb-card-header {
    display: flex;
    align-items: center;
    padding: 14px 22px;
    border-bottom: 1px solid #eef5ee;
    gap: 12px;
}
.pb-card-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}
.pb-card-accent {
    display: inline-block;
    width: 3px;
    height: 18px;
    background: #038303;
    border-radius: 2px;
    flex-shrink: 0;
}
.pb-card-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a2b1a;
    margin: 0;
}
.pb-card-subtitle {
    font-size: 13px;
    font-weight: 500;
    color: #5c7a5c;
    margin-left: 4px;
}

/* Add button — same as gallery "Add Image" primary button */
.pb-btn-add {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #038303;
    color: #ffffff !important;
    border: none;
    border-radius: 6px;
    padding: 7px 16px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background .18s;
    flex-shrink: 0;
    /* Explicitly suppress any tooltip pseudo-elements */
}
.pb-btn-add:hover,
.pb-btn-add:focus-visible {
    background: #0f7c2e;
    color: #ffffff !important;
    outline: 3px solid #92d12c;
    outline-offset: 2px;
}
/* Prevent ::after/::before tooltip from btn-icon bleeding in if class is combined */
.pb-btn-add::before,
.pb-btn-add::after { display: none !important; }

/* Tables */
.pb-table-wrap { overflow-x: auto; }
.pb-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 14px;
    min-width: 480px;
}
.pb-table thead tr { background: #f4f8f4; }
.pb-table thead th {
    padding: 12px 16px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #1a2b1a;
    border-bottom: 2px solid #c6ddc6;
    white-space: nowrap;
}
.pb-table thead th.th-center { text-align: center; }
.pb-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #eef5ee;
    color: #1a2b1a;
    vertical-align: middle;
}
.pb-table tbody tr:nth-child(even) { background: #fafcfa; }
.pb-table tbody tr:hover           { background: #f0f8f0; transition: background .12s; }
.pb-table tbody tr:last-child td   { border-bottom: none; }
.pb-table td.td-center { text-align: center; }
.pb-table td.td-muted  { color: #5c7a5c; font-size: 13px; }
.pb-table td.td-bold   { font-weight: 500; }

/* Chips */
.pb-pill-blue {
    background: #e8f1fd;
    color: #153396;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    display: inline-block;
}
.pb-code {
    background: #f4f8f4;
    color: #038303;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 6px;
    border: 1px solid #c6ddc6;
    font-family: monospace;
    letter-spacing: .03em;
    display: inline-block;
}

/* Empty state */
.pb-empty {
    padding: 32px 16px;
    text-align: center;
    color: #5c7a5c;
    font-size: 14px;
}
.pb-empty i {
    font-size: 26px;
    color: #c6ddc6;
    display: block;
    margin-bottom: 8px;
}
</style>
@endpush

@section('content_header')
<h1 style="font-size:22px; font-weight:700; color:#1a2b1a; margin:0 0 6px;">Program Builder</h1>
<ol class="breadcrumb" style="background:transparent; padding:0; margin:0;">
    @foreach($breadcrumbs as $bc)
        @if($bc['url'])
            <li class="breadcrumb-item">
                <a href="{{ $bc['url'] }}" style="color:#038303; font-size:13px; font-weight:500; text-decoration:none;">{{ $bc['name'] }}</a>
            </li>
        @else
            <li class="breadcrumb-item active" style="color:#5c7a5c; font-size:13px;">{{ $bc['name'] }}</li>
        @endif
    @endforeach
</ol>
@stop

@section('content')

{{-- ── HERO ── --}}
<div class="pb-hero">
    <div class="pb-hero-bar"></div>
    <div class="pb-hero-body">
        @if($program->image_url)
        <div class="pb-hero-img">
            <img src="{{ $program->image_url }}" alt="{{ $program->title }}">
        </div>
        @endif
        <div class="pb-hero-info">
            <div class="pb-hero-title-row">
                <h2>{{ $program->title }}</h2>
                <span class="badge-active badge">Active</span>
            </div>
            @if($program->description)
            <p class="pb-hero-desc">{{ $program->description }}</p>
            @endif
        </div>
    </div>
</div>

{{-- ── REQUIREMENTS ── --}}
<div class="pb-card">
    <div class="pb-card-header">
        <div class="pb-card-title-group">
            <span class="pb-card-accent"></span>
            <h3 class="pb-card-title">
                Requirements <span class="pb-card-subtitle">Unit Summary</span>
            </h3>
        </div>
        <button
            class="open-modal pb-btn-add"
            data-action="add"
            data-modal="#addRequirementModal"
            data-form="#addRequirementForm"
            data-title="Add Requirement"
            data-url="{{ route('admin.programs.requirements.store.ajax', $program) }}"
            aria-label="Add requirement">
            <i class="fas fa-plus" aria-hidden="true"></i> Add Requirement
        </button>
    </div>

    <div class="pb-table-wrap">
        <table class="pb-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Required Units</th>
                    <th>MS</th>
                    <th>MPS</th>
                    <th class="th-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($program->requirements as $req)
                <tr id="req-{{ $req->id }}">
                    <td class="td-bold">{{ $req->category->name }}</td>
                    <td><span class="pb-pill-blue">{{ $req->required_units }} units</span></td>
                    <td class="td-muted">{{ $req->ms ?: 0}}</td>
                    <td class="td-muted">{{ $req->mps ?: 0}}</td>
                    <td class="td-center">
                        <button
                            class="btn-icon btn-icon-delete delete-requirement"
                            data-id="{{ $req->id }}"
                            data-label="Delete"
                            aria-label="Delete requirement">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="pb-empty">
                        <i class="fas fa-inbox"></i>
                        No requirements added yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── COURSES ── --}}
<div class="pb-card">
    <div class="pb-card-header">
        <div class="pb-card-title-group">
            <span class="pb-card-accent"></span>
            <h3 class="pb-card-title">
                Program Structure <span class="pb-card-subtitle">Courses</span>
            </h3>
        </div>
        <button
            class="open-modal pb-btn-add"
            data-action="add"
            data-modal="#addCourseModal"
            data-form="#addCourseForm"
            data-title="Add Course"
            data-url="{{ route('admin.programs.courses.store.ajax', $program) }}"
            aria-label="Add course">
            <i class="fas fa-plus" aria-hidden="true"></i> Add Course
        </button>
    </div>

    <div class="pb-table-wrap">
        <table class="pb-table" style="min-width:640px;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Units</th>
                    <th>Prerequisite</th>
                    <th class="th-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($program->programCourses as $pc)
                <tr id="course-{{ $pc->id }}">
                    <td class="td-muted">{{ $pc->category->name }}</td>
                    <td><span class="pb-code">{{ $pc->course->code }}</span></td>
                    <td class="td-bold">{{ $pc->course->title }}</td>
                    <td><span class="pb-pill-blue">{{ $pc->course->units }}</span></td>
                    <td class="td-muted">{{ $pc->course->prerequisite ?? '—' }}</td>
                    <td class="td-center">
                        <button
                            class="btn-icon btn-icon-delete delete-course"
                            data-id="{{ $pc->id }}"
                            data-label="Delete"
                            aria-label="Delete course from program">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="pb-empty">
                        <i class="fas fa-book-open"></i>
                        No courses added yet.
                    </td>
                </tr>
                @endforelse
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
$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    /* ── DELETE REQUIREMENT ── */
    $(document).on('click', '.delete-requirement', function () {
        const id  = $(this).data('id');
        const row = $('#req-' + id);

        Swal.fire({
            title: 'Delete this requirement?',
            text: 'This action cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            confirmButtonColor: '#b42318',
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url: '{{ url("admin/programs/".$program->id."/requirements") }}/' + id + '/ajax',
                type: 'DELETE',
                success: function (res) {
                    Swal.fire({ type: 'success', title: 'Deleted', text: res.message ?? 'Requirement deleted.', timer: 1200, showConfirmButton: false });
                    row.fadeOut(300, function () { $(this).remove(); });
                },
                error: function (xhr) {
                    Swal.fire({ type: 'error', title: 'Error', text: xhr.responseJSON?.message ?? 'Failed to delete requirement.' });
                }
            });
        });
    });

    /* ── DELETE COURSE ── */
    $(document).on('click', '.delete-course', function () {
        const id  = $(this).data('id');
        const row = $('#course-' + id);

        Swal.fire({
            title: 'Delete this course?',
            text: 'This action cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            confirmButtonColor: '#b42318',
            reverseButtons: true
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url: '{{ url("admin/programs/".$program->id."/courses") }}/' + id + '/ajax',
                type: 'DELETE',
                success: function (res) {
                    Swal.fire({ type: 'success', title: 'Deleted', text: res.message ?? 'Course deleted.', timer: 1200, showConfirmButton: false });
                    row.fadeOut(300, function () { $(this).remove(); });
                },
                error: function () {
                    Swal.fire({ type: 'error', title: 'Error', text: 'Failed to delete course.' });
                }
            });
        });
    });

});
</script>
@endpush

