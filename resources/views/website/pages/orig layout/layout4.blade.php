{{-- Layout 4 --}}

@extends('layouts.website')

@section('title', 'News and Announcement | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />

  <style>
    /* MAIN */
    .main-content {
      background: #fff;
      border-radius: 6px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .content-image-wrapper {
      position: relative;
    }

    .content-image-wrapper img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }

    .content-badge {
      position: absolute;
      bottom: 15px;
      left: 0;
      background: #ffd400;
      color: #000;
      font-weight: bold;
      padding: 8px 20px;
    }

    .main-content-body {
      padding: 20px;
    }

    .content-title {
      color: #0f7c2e;
      text-align: center;
      padding: 10px;
      margin: 0;
    }

    .content-description {
      margin: 15px 0;
    }
  </style>
@endpush

@section('content')

  <section class="content-section">
    <div class="content-grid">

      <!-- ================= MAIN CONTENT ================= -->
      <div class="main-content">

        <div class="main-content-body">
          <div class="card-footer">
            <span class="content-date">November 08, 2025</span>
          </div>

          <p class="content-description">
            Quality Assurance Coordinator from DOT-Uni presented studies at the 38th AAOU conference
            transitioning from measuring outputs to tracking transformative learning impact.
          </p>

          <div class="content-tags">
            <span>#clsuDOTUni</span>
            <span>#AAOUConference</span>
            <span>#DistanceEducation</span>
            <span>#TransformativeEducation</span>
            <span>#lifelonglearning</span>
          </div>
        </div>
      </div>

      <!-- ================= SIDE CONTENT ================= -->
      <div class="side-content-container">
        <div class="other-content-header">
          <span>Other Updates</span>
        </div>

        <div class="side-content">
          <!-- CARD -->
          <a class="content-card" href="#">
            <img src="https://picsum.photos/200/150" alt="Content 1">
            <div class="card-body">
              <h4>CLSU Student Handbook</h4>
              <p>
                Lorem ipsum dolor sit amet consectetur adipiscing elit
              </p>
              <div class="card-footer">
                <span class="content-date">Nov 10, 2025</span>
                <span class="read-more">Read More</span>
              </div>
            </div>
          </a>

          <a class="content-card" href="#">
            <img src="https://picsum.photos/200/150" alt="Content 2">
            <div class="card-body">
              <h4>University Announcement</h4>
              <p>Lorem ipsum dolor sit amet consectetur adipiscing elit</p>
              <div class="card-footer">
                <span class="content-date">Nov 8, 2025</span>
                <span class="read-more">Read More</span>
              </div>
            </div>
          </a>

          <a class="content-card" href="#">
            <img src="https://picsum.photos/200/150" alt="Content 3">
            <div class="card-body">
              <h4>Enrollment Guidelines</h4>
              <p>Lorem ipsum dolor sit amet consectetur adipiscing elit</p>
              <div class="card-footer">
                <span class="content-date">Nov 5, 2025</span>
                <span class="read-more">Read More</span>
              </div>
            </div>
          </a>
        </div>

        <div class="btn-wrapper">
          <a class="view-all-btn" href="#">
            View All Updates
          </a>
        </div>
      </div>

    </div>
  </section>

@endsection