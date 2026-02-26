@extends('layouts.website')

@section('title', 'News and Announcement | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />

  <style>
    /* ===================== LAYOUT 2: MAIN CONTENT ===================== */
    .main-content {
      background: #fff;
      border-radius: 6px;
      overflow: hidden;
    }

    .main-content-body {
      padding: 20px;
    }

    /* Each alternating row */
    .content-row {
      overflow: hidden;
      /* clearfix */
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eee;
    }

    .content-row:last-of-type {
      border-bottom: none;
      margin-bottom: 0;
    }

    /* Image wrapper — float left by default */
    .content-image-wrapper {
      position: relative;
      width: 45%;
      float: left;
      margin: 0 20px 10px 0;
    }

    /* Alternate: image floats right */
    .content-row.reverse .content-image-wrapper {
      float: right;
      margin: 0 0 10px 20px;
    }

    .content-image-wrapper img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      display: block;
    }

    .content-badge {
      position: absolute;
      bottom: 15px;
      left: 0;
      background: #ffd400;
      color: #000;
      font-weight: bold;
      font-size: 13px;
      padding: 6px 18px;
    }

    /* Meta row */
    .meta-row {
      font-size: 12px;
      color: #777;
      margin-bottom: 8px;
    }

    /* Title */
    .content-title {
      color: #0f7c2e;
      font-size: 17px;
      font-weight: 700;
      line-height: 1.4;
      margin-bottom: 10px;
    }

    /* Description */
    .content-description {
      font-size: 13px;
      color: #333;
      line-height: 1.7;
      text-align: justify;
    }

    /* TAGS */
    .content-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 10px;
      clear: both;
    }

    /* Responsive */
    @media (max-width: 768px) {

      .content-image-wrapper,
      .content-row.reverse .content-image-wrapper {
        float: none;
        width: 100%;
        margin: 0 0 15px 0;
      }
    }
  </style>

@endpush

@section('content')

  <section class="content-section">
    <div class="content-grid">

      <!-- ================= MAIN CONTENT (Layout 2) ================= -->
      <div class="main-content">
        <div class="main-content-body">

          <!-- ROW 1: Image LEFT, text RIGHT -->
          <div class="content-row">
            <div class="content-image-wrapper">
              <img src="https://picsum.photos/900/500" alt="Main Content">
              <span class="content-badge">NEWS</span>
            </div>
            <div class="meta-row">November 08, 2025</div>
            <h2 class="content-title">
              CLSU DOT-Uni Breaks New Ground as an Associate Member of the Asian Association of Open Universities (AAOU)
            </h2>
            <p class="content-description">
              Quality Assurance Coordinator from the Distance, Open, and Transnational University (DOT-Uni) presented her
              studies at the 38th Asian Association of Open Universities (AAO) conference, research proposal 2025, October
              20-23, 2025, in Beijing, China.
              Asst. Prof. Maria Celia M. Fernando, Quality Assurance Coordinator of DOT-Uni and faculty member of the
              Department of Communication and Development Studies, College of Arts and Social Sciences, presented her
              research titled "From Access to Impact: CLSU DOT-Uni's Evolution in Distance, Open, and Transnational
              Education."
            </p>
          </div>

          <!-- ROW 2: Text LEFT, Image RIGHT -->
          <div class="content-row reverse">
            <div class="content-image-wrapper">
              <img src="https://picsum.photos/900/500?grayscale" alt="Main Content 2">
              <span class="content-badge">NEWS</span>
            </div>
            <div class="meta-row">November 05, 2025</div>
            <h2 class="content-title">
              CLSU DOT-Uni Breaks New Ground as an Associate Member of the Asian Association of Open Universities (AAOU)
            </h2>
            <p class="content-description">
              Quality Assurance Coordinator from the Distance, Open, and Transnational University (DOT-Uni) presented her
              studies at the 38th Asian Association of Open Universities (AAO) conference, research proposal 2025, October
              20-23, 2025, in Beijing, China.
              Asst. Prof. Maria Celia M. Fernando, Quality Assurance Coordinator of DOT-Uni and faculty member of the
              Department of Communication and Development Studies, College of Arts and Social Sciences, presented her
              research titled "From Access to Impact: CLSU DOT-Uni's Evolution in Distance, Open, and Transnational
              Education."
            </p>
          </div>

          <!-- TAGS -->
          <div class="content-tags">
            <span>#clsudotuni</span>
            <span>#38thAAOUConferenceResearchPresentation</span>
            <span>#TransnationalEducation</span>
            <span>#distanceeducation</span>
            <span>#distancelearning</span>
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
          <a class="content-card" href="#">
            <img src="https://picsum.photos/200/150" alt="Content 1">
            <div class="card-body">
              <h4>CLSU Student Handbook</h4>
              <p>Lorem ipsum dolor sit amet consectetur adipiscing elit</p>
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
          <a class="view-all-btn" href="#">View All Updates</a>
        </div>
      </div>

    </div>
  </section>


@endsection