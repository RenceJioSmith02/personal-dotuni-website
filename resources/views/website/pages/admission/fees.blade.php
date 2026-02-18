@extends('layouts.website')

@section('title', 'Schedule of School Fees | CLSU DOT-Uni')

@push('css')
<style>

    .schedule-school-fees-section {
        padding: 60px 0;
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

            @forelse($fees as $fee)
                <div class="fee-item" style="margin-bottom: 30px;">
                    <h4 class="content-title">
                        {{ $fee->title ?? 'No Title' }}
                    </h4>

                    @if($fee->asset && !empty($fee->asset->storage_path))
                        <img src="{{ asset('storage/'.$fee->asset->storage_path) }}" alt="{{ $fee->title }}">
                    @else
                        <img src="{{ asset('assets/system_images/placeholder.jpg') }}" alt="{{ $fee->title }}">
                    @endif

                </div>
            @empty
                <p style="text-align:center; color:#999;">
                    No school fees uploaded yet.
                </p>
            @endforelse

        </div>

    </div>
</section>

@endsection
