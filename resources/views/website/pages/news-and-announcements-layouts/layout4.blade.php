{{--
  Layout 4: Article body only — no images.
  content-description = article_body (the only layout where body is the main content).
--}}
@extends('layouts.website')

@section('title', ($item['title'] ?? 'News') . ' | CLSU DOT-Uni')

@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement-layout.css') }}" />
  <style>
    .main-content { background:#fff; border-radius:6px; overflow:hidden; display:flex; flex-direction:column; position:relative; }
    .main-content-body { padding:30px; }
    .content-title { color:#0f7c2e; text-align:center; margin:0 0 10px; font-size:22px; font-weight:700; line-height:1.4; }
    .content-date { font-size:12px; color:#777; display:block; text-align:center; margin-bottom:20px; }
    .content-badge-inline {
      display:inline-block;
      background:#ffd400; color:#000;
      font-weight:bold; font-size:12px;
      padding:3px 12px; margin-bottom:12px;
    }
    .content-description { line-height:1.8; color:#333; font-size:14px; text-align:justify; }
    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:20px; }
  </style>
@endpush

@section('content')
  <section class="content-section">
    <div class="content-grid">

      <div class="main-content">
        @include('website.partials.announcement-download-btn', ['item' => $item])

        <div class="main-content-body">
          <span class="content-badge-inline">{{ strtoupper($item['type']) }}</span>
          <h2 class="content-title">{{ $item['title'] }}</h2>
          <span class="content-date">{{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}</span>

          {{-- Layout 4 uses article_body as the main content --}}
          <div class="content-description">
            {!! nl2br(e($item['body'] ?? $item['description'])) !!}
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