@extends('layouts.website')

@section('title', 'About Us | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/courses.css') }}" />
@endpush

@section('content')

<section class="courses-section">
    <div class="container">
        <div class="divider"></div>
        <h2 class="section-title">
            The CLSU DOT-Uni Curricular Offerings
        </h2>

        <div class="courses-grid">

            <!-- Course Card -->
            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Teaching Course">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Agribusiness Course">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Education Course">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Entrepreneurship Course">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Information Technology">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Graduate Studies">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Non Formal Education">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Public Administration">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Special Program">
            </div>

            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Short Course">
            </div>

        </div>
    </div>
</section>

@endsection