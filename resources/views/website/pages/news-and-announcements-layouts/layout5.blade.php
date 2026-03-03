{{--
  Layout 5: Image carousel at the top, article_body as the description below.
  content-description = article_body (as specified, this layout keeps the body).
  Carousel is built from normalized $item['assets'] image entries.
--}}
@extends('layouts.website')

@section('title', ($item['title'] ?? 'News') . ' | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />
  <style>
    .main-content { background:#fff; border-radius:6px; overflow:hidden; display:flex; flex-direction:column; position:relative; }
    .content-image-wrapper { position:relative; }
    .content-badge { position:absolute; bottom:15px; left:0; background:#ffd400; color:#000; font-weight:bold; padding:8px 20px; z-index:10; }
    .main-content-body { padding:20px; }
    .content-title { color:#0f7c2e; text-align:center; padding:10px; margin:0; font-size:22px; font-weight:700; }
    .content-date { font-size:12px; color:#777; }
    .content-description { margin:15px 0; line-height:1.8; color:#333; font-size:14px; text-align:justify; }
    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:15px; }

    /* ── CAROUSEL ── */
    .carousel { position:relative; width:100%; height:400px; overflow:hidden; }
    .carousel-track { display:flex; height:100%; transition:transform .5s ease; }
    .carousel-slide { min-width:100%; height:100%; flex-shrink:0; }
    .carousel-slide img { width:100%; height:100%; object-fit:cover; display:block; }

    .carousel-btn {
      position:absolute; top:50%; transform:translateY(-50%);
      background:rgba(0,0,0,.45); border:none;
      width:38px; height:38px; border-radius:50%;
      cursor:pointer; z-index:20; padding:0;
    }
    .carousel-btn::before {
      content:''; display:block;
      width:10px; height:10px;
      border-top:2px solid #fff; border-right:2px solid #fff;
      margin:auto;
      position:absolute; top:50%; left:50%;
    }
    .carousel-btn.prev::before { transform:translate(-50%,-50%) rotate(-135deg); }
    .carousel-btn.next::before { transform:translate(-50%,-50%) rotate(45deg); }
    .carousel-btn.prev { left:12px; }
    .carousel-btn.next { right:12px; }
    .carousel-btn:hover { background:rgba(0,0,0,.7); }

    .carousel-dots { position:absolute; bottom:48px; left:50%; transform:translateX(-50%); display:flex; gap:7px; z-index:10; }
    .carousel-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,.5); border:none; cursor:pointer; padding:0; transition:background .3s,transform .3s; }
    .carousel-dot.active { background:#fff; transform:scale(1.3); }

    .carousel-progress { position:absolute; bottom:0; left:0; height:3px; background:#ffd400; width:0%; z-index:10; transition:width linear; }

    /* No-image fallback */
    .carousel-placeholder { width:100%; height:400px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; color:#aaa; font-size:14px; }
  </style>
@endpush

@section('content')
  <section class="content-section">
    <div class="content-grid">

      <div class="main-content">
        @include('website.partials.announcement-download-btn', ['item' => $item])

        @php
          // Build slides from normalized image assets only, sorted by sort_order
          $slides = collect($item['assets'])
              ->filter(fn($a) => $a->kind === 'image' && $a->storage_path)
              ->sortBy('sort_order')
              ->values();
        @endphp

        <div class="content-image-wrapper">
          <h2 class="content-title">{{ $item['title'] }}</h2>

          @if($slides->isNotEmpty())

            <div class="carousel" id="mainCarousel">
              <div class="carousel-track" id="carouselTrack">
                @foreach($slides as $slide)
                  <div class="carousel-slide">
                    <img src="{{ asset('storage/' . $slide->storage_path) }}"
                         alt="{{ $slide->caption ?? $item['title'] }}">
                  </div>
                @endforeach
              </div>

              @if($slides->count() > 1)
                <button class="carousel-btn prev" id="prevBtn" aria-label="Previous"></button>
                <button class="carousel-btn next" id="nextBtn" aria-label="Next"></button>
                <div class="carousel-dots" id="carouselDots"></div>
              @endif

              <div class="carousel-progress" id="carouselProgress"></div>
            </div>

          @else
            <div class="carousel-placeholder">No images available</div>
          @endif

          <span class="content-badge">{{ strtoupper($item['type']) }}</span>
        </div>

        <div class="main-content-body">
          <span class="content-date">{{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}</span>

          {{-- Layout 5 keeps article_body as the description --}}
          @if($item['body'])
            <div class="content-description">
              {!! nl2br(e($item['body'])) !!}
            </div>
          @endif

          @if(!empty($item['tags']))
            <div class="content-tags">
              @foreach($item['tags'] as $tag)<span>#{{ $tag }}</span>@endforeach
            </div>
          @endif
        </div>

      </div>

      @include('website.partials.news-side-content', ['currentType' => $item['type']])

    </div>
  </section>
@endsection

@push('js')
<script>
(function () {
  const track    = document.getElementById('carouselTrack');
  const progress = document.getElementById('carouselProgress');

  if (!track) return;

  const slides   = track.querySelectorAll('.carousel-slide');
  const total    = slides.length;
  const INTERVAL = 5000;

  if (total <= 1) {
    if (progress) progress.style.display = 'none';
    return;
  }

  const dotsWrapper = document.getElementById('carouselDots');
  let current = 0;
  let timer;

  // Build dots
  Array.from(slides).forEach((_, i) => {
    const dot = document.createElement('button');
    dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
    dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
    dot.addEventListener('click', () => goTo(i));
    dotsWrapper.appendChild(dot);
  });

  function updateDots() {
    dotsWrapper.querySelectorAll('.carousel-dot').forEach((d, i) => {
      d.classList.toggle('active', i === current);
    });
  }

  function goTo(index) {
    current = (index + total) % total;
    track.style.transform = 'translateX(-' + (current * 100) + '%)';
    updateDots();
    resetProgress();
  }

  function resetProgress() {
    clearTimeout(timer);
    progress.style.transition = 'none';
    progress.style.width = '0%';
    // Force reflow so the transition reset takes effect
    void progress.offsetWidth;
    progress.style.transition = 'width ' + INTERVAL + 'ms linear';
    progress.style.width = '100%';
    timer = setTimeout(() => goTo(current + 1), INTERVAL);
  }

  document.getElementById('prevBtn').addEventListener('click', () => goTo(current - 1));
  document.getElementById('nextBtn').addEventListener('click', () => goTo(current + 1));

  const carousel = document.getElementById('mainCarousel');
  carousel.addEventListener('mouseenter', () => {
    clearTimeout(timer);
    progress.style.transition = 'none';
    progress.style.width = progress.style.width; // freeze current position
  });
  carousel.addEventListener('mouseleave', () => resetProgress());

  // Swipe support (touch)
  let touchStartX = 0;
  carousel.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
  carousel.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) goTo(diff > 0 ? current + 1 : current - 1);
  });

  resetProgress();
})();
</script>
@endpush