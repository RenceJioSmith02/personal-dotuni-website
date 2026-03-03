{{--
  Layout 1: One content-row per image asset (alternating left/right).
  Each row = image + its caption as the description.
  The title and date appear only in the FIRST row.
  No separate "first row" — all rows come from $item['assets'] image assets.
--}}
@extends('layouts.website')

@section('title', ($item['title'] ?? 'News') . ' | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />
  <style>
    .main-content { background:#fff; border-radius:6px; overflow:hidden; position:relative; }
    .main-content-body { padding:20px; }

    .content-row {
      overflow:hidden;
      margin-bottom:20px;
      padding-bottom:20px;
      border-bottom:1px solid #eee;
    }
    .content-row:last-of-type { border-bottom:none; margin-bottom:0; }

    /* Even index (0,2,4…) → image left */
    .content-image-wrapper {
      position:relative;
      width:45%;
      float:left;
      margin:0 20px 10px 0;
    }
    /* Odd index (1,3,5…) → image right */
    .content-row.reverse .content-image-wrapper {
      float:right;
      margin:0 0 10px 20px;
    }
    .content-image-wrapper img { width:100%; height:220px; object-fit:cover; display:block; }

    .content-badge {
      position:absolute; bottom:15px; left:0;
      background:#ffd400; color:#000;
      font-weight:bold; font-size:13px; padding:6px 18px;
    }

    .meta-row { font-size:12px; color:#777; margin-bottom:8px; }
    .content-title { color:#0f7c2e; font-size:20px; font-weight:700; line-height:1.4; margin-bottom:10px; }
    .content-description { font-size:13px; color:#333; line-height:1.7; text-align:justify; }
    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; clear:both; }

    @media (max-width:768px) {
      .content-image-wrapper,
      .content-row.reverse .content-image-wrapper { float:none; width:100%; margin:0 0 15px 0; }
    }
  </style>
@endpush

@section('content')
  <section class="content-section">
    <div class="content-grid">

      <div class="main-content">
        @include('website.partials.announcement-download-btn', ['item' => $item])

        <div class="main-content-body">

          {{-- Title + date only on first row --}}
          <div class="meta-row">{{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}</div>
          <h2 class="content-title">{{ $item['title'] }}</h2>
          <br>
          
          @php
            // Only image assets, sorted by sort_order
            $imageAssets = collect($item['assets'])
                ->filter(fn($a) => $a->kind === 'image')
                ->sortBy('sort_order')
                ->values();

            // Fallback: if no images at all, use placeholder
            $hasImages = $imageAssets->isNotEmpty();
          @endphp

          @if($hasImages)

            @foreach($imageAssets as $index => $asset)
              @php
                $src      = asset('storage/' . $asset->storage_path);
                $rowClass = $index % 2 !== 0 ? 'reverse' : '';
              @endphp

              <div class="content-row {{ $rowClass }}">

                <div class="content-image-wrapper">
                  <img src="{{ $src }}" alt="{{ $asset->caption ?? $item['title'] }}">
                  @if($index === 0)
                    <span class="content-badge">{{ strtoupper($item['type']) }}</span>
                  @endif
                </div>

                {{-- Caption is the per-image description --}}
                @if($asset->caption)
                  <p class="content-description">{{ $asset->caption }}</p>
                @endif

              </div>
            @endforeach

          @else

            {{-- No images: show title + date + description only --}}
            <div class="content-row">
              <div class="meta-row">{{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}</div>
              <h2 class="content-title">{{ $item['title'] }}</h2>
              <p class="content-description">{{ $item['description'] }}</p>
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

