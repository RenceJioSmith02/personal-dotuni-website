@extends('layouts.website')

@section('title', 'Gallery | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/gallery.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/website/website-pagination.css') }}" />
@endpush


@section('content')

<section class="gallery-section">

  <div class="gallery-header">
    <div class="divider"></div>
    <h2 class="section-title">GALLERY</h2>
  </div>

  <div class="gallery-grid" id="gallery-grid">
    <!-- AJAX content will be loaded here -->
{{-- 
    <div class="gallery-item">
      <img src="{{ asset('storage/gallery/gallery-2026-02-09-5a64df5a.jpg') }}">
        <div class="gallery-overlay">
            <span class="gallery-date">Apr. 12, 2024</span>
        </div>
    </div> --}}

  </div>

  <!-- PAGINATION -->
  <div class="website-pagination" id="gallery-pagination">
      <!-- AJAX pagination buttons -->
  </div>

  <!-- PAGINATION -->
  {{-- <div class="gallery-pagination">
    <button class="page-btn disabled">Prev</button>
    <button class="page-btn active">1</button>
    <button class="page-btn">2</button>
    <button class="page-btn">3</button>
    <button class="page-btn">Next</button>
  </div> --}}

</section>


<!-- LIGHTBOX VIEWER -->
<div id="gallery-lightbox" class="lightbox hidden">

    <span class="lightbox-close">&times;</span>

    <button class="lightbox-arrow left">&#10094;</button>

    <img class="lightbox-image" id="lightbox-image">

    <button class="lightbox-arrow right">&#10095;</button>

</div>


@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {

    const galleryGrid = document.getElementById('gallery-grid');
    const paginationContainer = document.getElementById('gallery-pagination');
    let currentPage = 1;

    function fetchGallery(page = 1) {
        fetch(`{{ route('website.galleryData') }}?page=${page}`)
            .then(res => res.json())
            .then(res => {

                window.lastPage = parseInt(res.last_page);

                galleryGrid.innerHTML = '';

                res.data.forEach(item => {

                    const imgSrc = item.image_url 
                        ? item.image_url 
                        : '/assets/system_images/placeholder.jpg';

                    const date = new Date(item.created_at)
                        .toLocaleDateString('en-US', { 
                            month: 'short', 
                            day: 'numeric', 
                            year: 'numeric' 
                        });

                    const html = `
                    <div class="gallery-item">
                        <img src="${imgSrc}" class="gallery-preview" data-src="${imgSrc}">
                        <div class="gallery-overlay">
                            <span class="gallery-date">${date}</span>
                        </div>
                    </div>
                    `;


                    galleryGrid.insertAdjacentHTML('beforeend', html);
                });

                renderPagination(
                    parseInt(res.current_page),
                    parseInt(res.last_page)
                );
            });
    }


    function renderPagination(current, last) {
        paginationContainer.innerHTML = '';

        const createBtn = (text, page, disabled = false, active = false) => {
            const btn = document.createElement('button');
            btn.className = 'page-btn';
            if (disabled) btn.classList.add('disabled');
            if (active) btn.classList.add('active');
            btn.innerText = text;
            btn.addEventListener('click', () => {

                if (disabled || active) return;

                // Prevent going below 1
                if (page < 1) return;

                // Prevent exceeding last page
                if (page > window.lastPage) return;

                currentPage = page;
                fetchGallery(page);
            });

            return btn;
        };

        // Prev button
        paginationContainer.appendChild(createBtn('Prev', current - 1, current === 1));

        // Page numbers
        for (let i = 1; i <= last; i++) {
            paginationContainer.appendChild(createBtn(i, i, false, current === i));
        }

        // Next button
        paginationContainer.appendChild(createBtn('Next', current + 1, current === last));
    }

    // Initial fetch
    fetchGallery(currentPage);






    // ==============================
    // LIGHTBOX FUNCTIONALITY
    // ==============================

    const lightbox = document.getElementById('gallery-lightbox');
    const lightboxImage = document.getElementById('lightbox-image');

    let galleryImages = [];
    let currentIndex = 0;

    // OPEN LIGHTBOX
    galleryGrid.addEventListener('click', function(e){

        if(!e.target.classList.contains('gallery-preview')) return;

        galleryImages = document.querySelectorAll('.gallery-preview');

        currentIndex = Array.from(galleryImages).indexOf(e.target);

        showImage();

        lightbox.classList.remove('hidden');
    });

    // SHOW IMAGE
    function showImage(){
        lightboxImage.src = galleryImages[currentIndex].dataset.src;
    }

    // CLOSE
    document.querySelector('.lightbox-close')
    .addEventListener('click', () => {
        lightbox.classList.add('hidden');
    });

    // NEXT
    document.querySelector('.lightbox-arrow.right')
    .addEventListener('click', () => {

        currentIndex++;

        if(currentIndex >= galleryImages.length){
            currentIndex = 0;
        }

        showImage();
    });

    // PREV
    document.querySelector('.lightbox-arrow.left')
    .addEventListener('click', () => {

        currentIndex--;

        if(currentIndex < 0){
            currentIndex = galleryImages.length - 1;
        }

        showImage();
    });

    // KEYBOARD SUPPORT
    document.addEventListener('keydown', function(e){

        if(lightbox.classList.contains('hidden')) return;

        if(e.key === 'ArrowRight'){
            document.querySelector('.lightbox-arrow.right').click();
        }

        if(e.key === 'ArrowLeft'){
            document.querySelector('.lightbox-arrow.left').click();
        }

        if(e.key === 'Escape'){
            lightbox.classList.add('hidden');
        }

    });

});

</script>
@endpush

