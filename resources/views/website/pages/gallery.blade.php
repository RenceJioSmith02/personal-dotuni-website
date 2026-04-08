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

  <div class="gallery-grid" id="gallery-grid"></div>

  <!-- PAGINATION -->
  <div class="website-pagination" id="gallery-pagination"></div>

</section>


<!-- LIGHTBOX -->
<div id="facilityLightbox" class="facility-lightbox">
    <div class="facility-lightbox-overlay" onclick="closeLightbox()"></div>

    <button class="facility-lightbox-close" onclick="closeLightbox()">&#10005;</button>

    <div class="facility-lightbox-content">
        <button class="facility-lightbox-prev" onclick="lightboxNav(-1)">&#10094;</button>

        <img id="lightboxImg" src="" alt="">

        <button class="facility-lightbox-next" onclick="lightboxNav(1)">&#10095;</button>
    </div>

    <span class="facility-lightbox-caption" id="lightboxCaption"></span>
</div>


@endsection


@push('js')
<script>
(function () {

    // ==============================
    // LIGHTBOX
    // ==============================

    let allPhotos = [];
    let currentIndex = 0;

    function collectPhotos() {
        allPhotos = Array.from(document.querySelectorAll('.gallery-preview'));
        allPhotos.forEach((img, i) => {
            img.addEventListener('click', () => openLightbox(i));
        });
    }

    window.openLightbox = function (index) {
        currentIndex = index;
        updateLightbox();
        document.getElementById('facilityLightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function () {
        document.getElementById('facilityLightbox').classList.remove('active');
        document.body.style.overflow = '';
    };

    window.lightboxNav = function (direction) {
        currentIndex = (currentIndex + direction + allPhotos.length) % allPhotos.length;
        updateLightbox();
    };

    function updateLightbox() {
        const img = allPhotos[currentIndex];
        const lightboxImg = document.getElementById('lightboxImg');
        lightboxImg.style.opacity = '0';
        setTimeout(() => {
            lightboxImg.src = img.dataset.src || img.src;
            lightboxImg.alt = img.alt || '';
            document.getElementById('lightboxCaption').textContent = img.alt || '';
            lightboxImg.style.opacity = '1';
        }, 150);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') lightboxNav(-1);
        if (e.key === 'ArrowRight') lightboxNav(1);
    });


    // ==============================
    // GALLERY FETCH & PAGINATION
    // ==============================

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
                            <img src="${imgSrc}" class="gallery-preview" data-src="${imgSrc}" alt="${date}">
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

                // Re-register lightbox listeners after each AJAX load
                collectPhotos();
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
                if (page < 1 || page > window.lastPage) return;
                currentPage = page;
                fetchGallery(page);
            });
            return btn;
        };

        paginationContainer.appendChild(createBtn('Prev', current - 1, current === 1));

        for (let i = 1; i <= last; i++) {
            paginationContainer.appendChild(createBtn(i, i, false, current === i));
        }

        paginationContainer.appendChild(createBtn('Next', current + 1, current === last));
    }

    // Initial fetch
    fetchGallery(currentPage);

})();
</script>
@endpush

