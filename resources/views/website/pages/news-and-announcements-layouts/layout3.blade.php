{{--
  Layout 3: Single row — image floated left, caption of that image as the description on the right.
--}}
@extends('layouts.website')

@section('title', ($item['title'] ?? 'News') . ' | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />
  <style>
    .main-content { background:#fff; border-radius:6px; overflow:hidden; position:relative; }
    .main-content-body { padding:20px; }

    .content-row { overflow:hidden; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #eee; }
    .content-row:last-of-type { border-bottom:none; margin-bottom:0; }

    .content-image-wrapper { position:relative; width:45%; float:left; margin:0 20px 10px 0; }
    .content-image-wrapper img { width:100%; height:260px; object-fit:cover; display:block; }

    .content-badge { position:absolute; bottom:15px; left:0; background:#ffd400; color:#000; font-weight:bold; font-size:13px; padding:6px 18px; }

    .meta-row { font-size:12px; color:#777; margin-bottom:8px; }
    .content-title { color:#0f7c2e; font-size:20px; font-weight:700; line-height:1.4; margin-bottom:10px; }
    .content-description { font-size:13px; color:#333; line-height:1.7; text-align:justify; }
    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; clear:both; }

    @media (max-width:768px) {
      .content-image-wrapper { float:none; width:100%; margin:0 0 15px 0; }
    }
  </style>
@endpush

@section('content')
  <section class="content-section">
    <div class="content-grid">

      <div class="main-content">
        @include('website.partials.announcement-download-btn', ['item' => $item])

        <div class="main-content-body">

          @php
            // Use the first image asset
            $coverAsset = collect($item['assets'])
                ->filter(fn($a) => $a->kind === 'image')
                ->sortBy('sort_order')
                ->first();

            $imageSrc = $coverAsset
                ? asset('storage/' . $coverAsset->storage_path)
                : asset('assets/system_images/placeholder.jpg');

            $caption = $coverAsset?->caption;
          @endphp

          <div class="content-row">
            <div class="content-image-wrapper">
              <img src="{{ $imageSrc }}" alt="{{ $item['title'] }}">
              <span class="content-badge">{{ strtoupper($item['type']) }}</span>
            </div>

            <div class="meta-row">{{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}</div>
            <h2 class="content-title">{{ $item['title'] }}</h2>

            @if($caption)
              <p class="content-description">{{ $caption }}</p>
            @endif
          </div>

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