{{-- Layout 5 --}}

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

    .content-badge {
      position: absolute;
      bottom: 15px;
      left: 0;
      background: #ffd400;
      color: #000;
      font-weight: bold;
      padding: 8px 20px;
      z-index: 10;
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

    /* ===================== CAROUSEL ===================== */
    .carousel {
      position: relative;
      width: 100%;
      height: 400px;
      overflow: hidden;
    }

    .carousel-track {
      display: flex;
      height: 100%;
      transition: transform 0.5s ease;
    }

    .carousel-slide {
      min-width: 100%;
      height: 100%;
    }

    .carousel-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    /* Arrows */
    .carousel-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0, 0, 0, 0.45);
      border: none;
      width: 38px;
      height: 38px;
      border-radius: 50%;
      cursor: pointer;
      z-index: 10;
      padding: 0;
    }

    /* PERFECTLY CENTERED ARROW */
    .carousel-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 10px;
      height: 10px;
      border-top: 2px solid #fff;
      border-right: 2px solid #fff;
      transform-origin: center;
    }

    /* LEFT */
    .carousel-btn.prev::before {
      transform: translate(-50%, -50%) rotate(-135deg);
    }

    /* RIGHT */
    .carousel-btn.next::before {
      transform: translate(-50%, -50%) rotate(45deg);
    }

    .carousel-btn:hover {
      background: rgba(0, 0, 0, 0.7);
    }

    .carousel-btn.prev {
      left: 12px;
    }

    .carousel-btn.next {
      right: 12px;
    }

    /* Dots */
    .carousel-dots {
      position: absolute;
      bottom: 48px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 7px;
      z-index: 10;
    }

    .carousel-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      border: none;
      cursor: pointer;
      padding: 0;
      transition: background 0.3s, transform 0.3s;
    }

    .carousel-dot.active {
      background: #fff;
      transform: scale(1.3);
    }

    /* Progress bar */
    .carousel-progress {
      position: absolute;
      bottom: 0;
      left: 0;
      height: 3px;
      background: #ffd400;
      width: 0%;
      z-index: 10;
      transition: width linear;
    }
  </style>

@endpush

@section('content')

  <section class="content-section">
    <div class="content-grid">

      <!-- ================= MAIN CONTENT ================= -->
      <div class="main-content">
        <div class="content-image-wrapper">

          <h2 class="content-title">
            CLSU DOT-Uni Breaks New Ground as an Associate Member of the Asian Association of Open Universities
          </h2>

          <!-- CAROUSEL -->
          <div class="carousel" id="mainCarousel">
            <div class="carousel-track" id="carouselTrack">
              <div class="carousel-slide">
                <img src="https://picsum.photos/seed/slide1/900/500" alt="Slide 1">
              </div>
              <div class="carousel-slide">
                <img src="https://picsum.photos/seed/slide2/900/500" alt="Slide 2">
              </div>
              <div class="carousel-slide">
                <img src="https://picsum.photos/seed/slide3/900/500" alt="Slide 3">
              </div>
              <div class="carousel-slide">
                <img src="https://picsum.photos/seed/slide4/900/500" alt="Slide 4">
              </div>
            </div>

            <!-- Arrows -->
            <button class="carousel-btn prev" id="prevBtn"></button>
            <button class="carousel-btn next" id="nextBtn"></button>

            <!-- Dots -->
            <div class="carousel-dots" id="carouselDots"></div>

            <!-- Progress bar -->
            <div class="carousel-progress" id="carouselProgress"></div>
          </div>

          <span class="content-badge">NEWS</span>
        </div>

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

@push('js')
  <script>
    const track = document.getElementById('carouselTrack');
    const dotsWrapper = document.getElementById('carouselDots');
    const progress = document.getElementById('carouselProgress');
    const slides = track.querySelectorAll('.carousel-slide');
    const total = slides.length;
    const INTERVAL = 5000; // 5 seconds

    let current = 0;
    let timer, progressTimer;

    // Build dots
    slides.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
      dot.addEventListener('click', () => goTo(i));
      dotsWrapper.appendChild(dot);
    });

    function getDots() {
      return dotsWrapper.querySelectorAll('.carousel-dot');
    }

    function goTo(index) {
      current = (index + total) % total;
      track.style.transform = `translateX(-${current * 100}%)`;
      getDots().forEach((d, i) => d.classList.toggle('active', i === current));
      resetProgress();
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    // Progress bar
    function resetProgress() {
      clearInterval(progressTimer);
      clearTimeout(timer);

      progress.style.transition = 'none';
      progress.style.width = '0%';

      // Force reflow
      progress.offsetWidth;

      progress.style.transition = `width ${INTERVAL}ms linear`;
      progress.style.width = '100%';

      timer = setTimeout(next, INTERVAL);
    }

    document.getElementById('prevBtn').addEventListener('click', () => { prev(); });
    document.getElementById('nextBtn').addEventListener('click', () => { next(); });

    // Pause on hover
    const carousel = document.getElementById('mainCarousel');
    carousel.addEventListener('mouseenter', () => {
      clearTimeout(timer);
      clearInterval(progressTimer);
      progress.style.transition = 'none';
    });
    carousel.addEventListener('mouseleave', () => resetProgress());

    // Start
    resetProgress();
  </script>
@endpush