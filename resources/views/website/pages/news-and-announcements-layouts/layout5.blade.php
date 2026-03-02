{{-- Layout 5: Image carousel header, body below --}}
@extends('layouts.website')

@section('title', ($item['title'] ?? 'News') . ' | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />

  <style>
    .main-content {
      background:#fff;
      border-radius:6px;
      overflow:hidden;
      display:flex;
      flex-direction:column;
    }
    .content-image-wrapper { position:relative; }
    .content-badge {
      position:absolute;
      bottom:15px;
      left:0;
      background:#ffd400;
      color:#000;
      font-weight:bold;
      padding:8px 20px;
      z-index:10;
    }
    .main-content-body { padding:20px; }
    .content-title { color:#0f7c2e; text-align:center; padding:10px; margin:0; }
    .content-description { margin:15px 0; line-height:1.7; color:#333; }

    /* ── CAROUSEL ── */
    .carousel { position:relative; width:100%; height:400px; overflow:hidden; }
    .carousel-track { display:flex; height:100%; transition:transform .5s ease; }
    .carousel-slide { min-width:100%; height:100%; }
    .carousel-slide img { width:100%; height:100%; object-fit:cover; display:block; }

    .carousel-btn {
      position:absolute;
      top:50%;
      transform:translateY(-50%);
      background:rgba(0,0,0,.45);
      border:none;
      width:38px;
      height:38px;
      border-radius:50%;
      cursor:pointer;
      z-index:10;
    }
    .carousel-btn::before {
      content:'';
      position:absolute;
      top:50%; left:50%;
      width:10px; height:10px;
      border-top:2px solid #fff;
      border-right:2px solid #fff;
    }
    .carousel-btn.prev::before { transform:translate(-50%,-50%) rotate(-135deg); }
    .carousel-btn.next::before { transform:translate(-50%,-50%) rotate(45deg); }
    .carousel-btn.prev { left:12px; }
    .carousel-btn.next { right:12px; }
    .carousel-btn:hover { background:rgba(0,0,0,.7); }

    .carousel-dots {
      position:absolute;
      bottom:48px; left:50%;
      transform:translateX(-50%);
      display:flex; gap:7px; z-index:10;
    }
    .carousel-dot {
      width:8px; height:8px;
      border-radius:50%;
      background:rgba(255,255,255,.5);
      border:none; cursor:pointer;
      transition:background .3s, transform .3s;
    }
    .carousel-dot.active { background:#fff; transform:scale(1.3); }

    .carousel-progress {
      position:absolute;
      bottom:0; left:0;
      height:3px;
      background:#ffd400;
      width:0%;
      z-index:10;
    }

    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:15px; }
  </style>
@endpush

@section('content')

  <section class="content-section">
    <div class="content-grid">

      <!-- ================= MAIN CONTENT ================= -->
      <div class="main-content">

        @php
          // Gather all images: thumbnail first, then the rest
          $assets    = $item['assets'] ?? collect();
          $mainImage = $item['image'];

          // Build slide list: always at least the thumbnail
          $slides = collect();

          if ($mainImage) {
              $slides->push(['src' => asset('storage/' . $mainImage), 'caption' => '']);
          }

          foreach ($assets as $attachment) {
              $asset = $attachment->asset ?? $attachment;
              if ($asset && $asset->storage_path && $asset->storage_path !== $mainImage) {
                  $slides->push([
                      'src'     => asset('storage/' . $asset->storage_path),
                      'caption' => $attachment->caption ?? '',
                  ]);
              }
          }

          // Fallback if no images at all
          if ($slides->isEmpty()) {
              $slides->push(['src' => asset('assets/system_images/placeholder.jpg'), 'caption' => '']);
          }
        @endphp

        <div class="content-image-wrapper">
          <h2 class="content-title">{{ $item['title'] }}</h2>

          <div class="carousel" id="mainCarousel">
            <div class="carousel-track" id="carouselTrack">
              @foreach($slides as $slide)
                <div class="carousel-slide">
                  <img src="{{ $slide['src'] }}" alt="{{ $slide['caption'] }}">
                </div>
              @endforeach
            </div>

            @if($slides->count() > 1)
              <button class="carousel-btn prev" id="prevBtn"></button>
              <button class="carousel-btn next" id="nextBtn"></button>
              <div class="carousel-dots" id="carouselDots"></div>
            @endif

            <div class="carousel-progress" id="carouselProgress"></div>
          </div>

          <span class="content-badge">{{ strtoupper($item['type']) }}</span>
        </div>

        <div class="main-content-body">
          <div class="card-footer">
            <span class="content-date">
              {{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}
            </span>
          </div>

          <div class="content-description">
            {!! nl2br(e($item['body'] ?? $item['description'])) !!}
          </div>

          @if(!empty($item['tags']))
            <div class="content-tags">
              @foreach($item['tags'] as $tag)
                <span>#{{ $tag }}</span>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      <!-- ================= SIDE CONTENT ================= -->
      @include('website.partials.news-side-content', ['currentType' => $item['type']])

    </div>
  </section>

@endsection

@push('js')
<script>
  const track       = document.getElementById('carouselTrack');
  const dotsWrapper = document.getElementById('carouselDots');
  const progress    = document.getElementById('carouselProgress');

  if (!track) return; // safety

  const slides  = track.querySelectorAll('.carousel-slide');
  const total   = slides.length;
  const INTERVAL = 5000;

  if (total <= 1) {
    // Hide controls when only 1 slide
    document.getElementById('carouselProgress').style.display = 'none';
    // nothing else needed
  } else {
    let current = 0;
    let timer;

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

    function resetProgress() {
      clearTimeout(timer);
      progress.style.transition = 'none';
      progress.style.width = '0%';
      progress.offsetWidth; // reflow
      progress.style.transition = `width ${INTERVAL}ms linear`;
      progress.style.width = '100%';
      timer = setTimeout(() => goTo(current + 1), INTERVAL);
    }

    document.getElementById('prevBtn').addEventListener('click', () => goTo(current - 1));
    document.getElementById('nextBtn').addEventListener('click', () => goTo(current + 1));

    const carousel = document.getElementById('mainCarousel');
    carousel.addEventListener('mouseenter', () => {
      clearTimeout(timer);
      progress.style.transition = 'none';
    });
    carousel.addEventListener('mouseleave', () => resetProgress());

    resetProgress();
  }
</script>
@endpush
