@extends('layouts.website')

@section('title', 'Gallery | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/gallery.css') }}" />
@endpush


@section('content')

<section class="gallery-section">

  <div class="gallery-header">
    <div class="divider"></div>
    <h2 class="section-title">GALLERY</h2>
  </div>

  <div class="gallery-grid">

    <!-- GALLERY ITEM -->
    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">

        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div>

  </div>

  <!-- PAGINATION -->
  <div class="gallery-pagination">
    <button class="page-btn disabled">Prev</button>
    <button class="page-btn active">1</button>
    <button class="page-btn">2</button>
    <button class="page-btn">3</button>
    <button class="page-btn">Next</button>
  </div>

</section>


@endsection

