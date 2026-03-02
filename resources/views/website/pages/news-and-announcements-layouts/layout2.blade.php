{{-- Layout 2: Full-width cover image, title above, body below --}}
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
    .content-image-wrapper img {
      width:100%;
      height:400px;
      object-fit:cover;
    }
    .content-badge {
      position:absolute;
      bottom:15px;
      left:0;
      background:#ffd400;
      color:#000;
      font-weight:bold;
      padding:8px 20px;
    }
    .main-content-body { padding:20px; }
    .content-title {
      color:#0f7c2e;
      text-align:center;
      padding:10px;
      margin:0 0 10px;
    }
    .content-description { margin:15px 0; line-height:1.7; color:#333; }
    .content-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:15px; }
  </style>
@endpush

@section('content')

  <section class="content-section">
    <div class="content-grid">

      <!-- ================= MAIN CONTENT ================= -->
      <div class="main-content">

        {{-- Floating download button (announcement + non-image assets only) --}}
        @include('website.partials.announcement-download-btn', ['item' => $item])

        @php
          $imageSrc = $item['image']
            ? asset('storage/' . $item['image'])
            : asset('assets/system_images/placeholder.jpg');
        @endphp

        <div class="content-image-wrapper">
          <h2 class="content-title">{{ $item['title'] }}</h2>
          <img src="{{ $imageSrc }}" alt="{{ $item['title'] }}">
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
