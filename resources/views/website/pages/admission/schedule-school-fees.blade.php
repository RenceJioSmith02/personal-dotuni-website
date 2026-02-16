@extends('layouts.website')

@section('title', 'Schedule of School Fees | CLSU DOT-Uni')

@push('css')
<style>
    .schedule-school-fees-section {
        background: #f2f2f2;
        padding: 60px 40px;
    }

    .schedule-school-fees-section .content {
        max-width: 800px;
        margin: 0 auto;
    }

    .schedule-school-fees-section .content-title {
        font-size: 18px;
        text-align: left;
        padding: 4px 10px;
        border-left: #E8D203 3px solid;
    }

    /* Responsive Image */
    .schedule-school-fees-section img {
        display: block;
        width: 100%; /* make image fill container width */
        height: auto; /* maintain aspect ratio */
        max-width: 100%;
        vertical-align: middle;
        border-style: none;
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .schedule-school-fees-section {
            padding: 40px 20px;
        }

        .schedule-school-fees-section .content-title {
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')

<section class="schedule-school-fees-section">
    <div class="container">
        <div class="divider"></div>
        <h2 class="section-title">
            SCHEDULE OF SCHOOL FEES
        </h2>

        <div class="content">
            <h4 class="content-title">
                The following are the requirements for admission to the undergraduate programs of CLSU:
            </h4>

            <img src="{{ asset('test_images/fees.png') }}" alt="School Fees Schedule">
        </div>

    </div>
</section>

@endsection
