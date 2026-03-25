{{--
  Layout 2: Full-width cover image on top, caption of that image as the description below.
--}}
@extends('layouts.website')

@section('title', ($item['title'] ?? 'News') . ' | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />
  <style>
    .main-content { background:#fff; border-radius:6px; overflow:hidden; display:flex; flex-direction:column; position:relative; }
    .content-image-wrapper { position:relative; }
    .content-image-wrapper img { width:100%; height:400px; object-fit:cover; display:block; }
    .content-badge { position:absolute; bottom:15px; left:0; background:#ffd400; color:#000; font-weight:bold; padding:8px 20px; }
    .main-content-body { padding:20px; }
    .content-title { color:#0f7c2e; text-align:center; padding:10px; margin:0 0 10px; font-size:22px; font-weight:700; }
    .content-date { font-size:12px; color:#777; }
    .content-description { margin:15px 0; line-height:1.7; color:#333; font-size:14px; text-align:justify; }
    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:15px; }
  </style>
@endpush

@section('content')
  <section class="content-section">
    <div class="content-grid">

      <div class="main-content">

        @php
          // Use the cover asset; fallback to thumbnail; fallback to placeholder
          $coverAsset = collect($item['assets'])
              ->filter(fn($a) => $a->kind === 'image')
              ->sortBy('sort_order')
              ->first();

          $imageSrc = $coverAsset
              ? asset('storage/' . $coverAsset->storage_path)
              : asset('assets/system_images/placeholder.jpg');

          // Caption of the cover image = description for this layout
          $caption = $coverAsset?->caption;
        @endphp

        <div class="content-image-wrapper">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <h2 class="content-title">{{ $item['title'] }}</h2>
            @include('website.partials.announcement-download-btn', ['item' => $item])
          </div>
          <img src="{{ $imageSrc }}" alt="{{ $item['title'] }}">
          <span class="content-badge">{{ strtoupper($item['type']) }}</span>
        </div>

        <div class="main-content-body">
          <span class="content-date">{{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}</span>

          @if($caption)
            <div class="content-description">{{ $caption }}</div>
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