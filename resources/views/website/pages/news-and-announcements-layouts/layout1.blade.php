{{-- Layout 1: Alternating image-left / image-right rows --}}
@extends('layouts.website')

@section('title', ($item['title'] ?? 'News') . ' | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />

  <style>
    .main-content { background:#fff; border-radius:6px; overflow:hidden; }
    .main-content-body { padding:20px; }

    .content-row {
      overflow:hidden;
      margin-bottom:20px;
      padding-bottom:20px;
      border-bottom:1px solid #eee;
    }
    .content-row:last-of-type { border-bottom:none; margin-bottom:0; }

    .content-image-wrapper {
      position:relative;
      width:45%;
      float:left;
      margin:0 20px 10px 0;
    }
    .content-row.reverse .content-image-wrapper {
      float:right;
      margin:0 0 10px 20px;
    }
    .content-image-wrapper img {
      width:100%;
      height:220px;
      object-fit:cover;
      display:block;
    }

    .content-badge {
      position:absolute;
      bottom:15px;
      left:0;
      background:#ffd400;
      color:#000;
      font-weight:bold;
      font-size:13px;
      padding:6px 18px;
    }

    .meta-row { font-size:12px; color:#777; margin-bottom:8px; }

    .content-title {
      color:#0f7c2e;
      font-size:17px;
      font-weight:700;
      line-height:1.4;
      margin-bottom:10px;
    }

    .content-description {
      font-size:13px;
      color:#333;
      line-height:1.7;
      text-align:justify;
    }

    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; clear:both; }

    @media (max-width:768px) {
      .content-image-wrapper,
      .content-row.reverse .content-image-wrapper {
        float:none; width:100%; margin:0 0 15px 0;
      }
    }
  </style>
@endpush

@section('content')

  <section class="content-section">
    <div class="content-grid">

      <!-- ================= MAIN CONTENT ================= -->
      <div class="main-content">

        {{-- Floating download button (announcement + non-image assets only) --}}
        @include('website.partials.announcement-download-btn', ['item' => $item])

        <div class="main-content-body">

          @php
            $imageSrc = $item['image']
              ? asset('storage/' . $item['image'])
              : asset('assets/system_images/placeholder.jpg');

            $assets = $item['assets'] ?? collect();
            // Additional images beyond the thumbnail
            $extraImages = $assets->where('is_thumbnail', false)->values();
          @endphp

          <!-- ROW 1: Thumbnail LEFT, body text RIGHT -->
          <div class="content-row">
            <div class="content-image-wrapper">
              <img src="{{ $imageSrc }}" alt="{{ $item['title'] }}">
              <span class="content-badge">{{ strtoupper($item['type']) }}</span>
            </div>

            <div class="meta-row">
              {{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}
            </div>

            <h2 class="content-title">{{ $item['title'] }}</h2>

            <p class="content-description">
              {!! nl2br(e($item['body'] ?? $item['description'])) !!}
            </p>
          </div>

          {{-- Extra image rows (alternating) --}}
          @foreach($extraImages as $index => $attachment)
            @php
              $asset    = $attachment->asset ?? $attachment;
              $extraSrc = $asset->storage_path
                ? asset('storage/' . $asset->storage_path)
                : asset('assets/system_images/placeholder.jpg');
              $rowClass = $index % 2 === 0 ? 'reverse' : '';
            @endphp

            <div class="content-row {{ $rowClass }}">
              <div class="content-image-wrapper">
                <img src="{{ $extraSrc }}" alt="{{ $attachment->caption ?? '' }}">
              </div>
              @if($attachment->caption)
                <p class="content-description">{{ $attachment->caption }}</p>
              @endif
            </div>
          @endforeach

          <!-- TAGS -->
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
