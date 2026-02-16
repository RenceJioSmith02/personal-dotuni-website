@extends('layouts.website')

@section('title', 'Admission Requirements | CLSU DOT-Uni')

@push('css')

    <style>
        .admission-requirements-section {
            background: #f2f2f2;
            padding: 60px 40px;
        }

        .admission-requirements-section .content {
            max-width: 800px;
            margin: 0 auto;
        }
        .admission-requirements-section .content-title {
            font-size: 18px;
            text-align: center;
        }

    </style>

@endpush

@section('content')

<section class="admission-requirements-section">
    <div class="container">
        <div class="divider"></div>
        <h2 class="section-title">
            INFORMATION for PROSPECTIVE STUDENTS
        </h2>

        <div class="content">
            <h4 class="content-title">
                The following are the requirements for admission to the undergraduate programs of CLSU:
            </h4>

            <ul>
                <li>Original copy of the Certificate of Junior High School Completion (CJHSC) or its equivalent;</li>
                <li>Original copy of the Certificate of Senior High School Completion (CSHSC) or its equivalent;</li>
                <li>Original copy of the Certificate of Good Moral Character;</li>
                <li>Original copy of the Birth Certificate;</li>
                <li>Original copy of the Medical Certificate;</li>
                <li>Original copy of the NCAE Result Slip;</li>
                <li>Certificate of Residency (if applicable); and</li>
                <li>Other documents as may be required by the University.</li>
            </ul>
        </div>

    </div>
</section>

@endsection
