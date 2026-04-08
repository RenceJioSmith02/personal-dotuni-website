
<div class="about-content-wrapper">
    <div class="content-section">
        <div class="divider"></div>
        <h2 class="section-title">Dean's Office</h2>

        {{-- DEAN'S OFFICE --}}
        <div class="facility-group">
            <div class="facility-photos-grid">
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/deans1.png') }}" alt="Dean's Office">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/deans2.png') }}" alt="Dean's Office">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/deans3.png') }}" alt="Dean's Office">
                </div>
            </div>
        </div>

        <div class="divider"></div>
        <h2 class="section-title">Lobby</h2>
        {{-- LOBBY --}}
        <div class="facility-group">
            <div class="facility-photos-grid">
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/lobby1.png') }}" alt="Lobby">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/lobby2.png') }}" alt="Lobby">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/lobby3.png') }}" alt="Lobby">
                </div>
            </div>
        </div>

        <div class="divider"></div>
        <h2 class="section-title">Workstations</h2>

        {{-- WORKSTATIONS --}}
        <div class="facility-group">
            <div class="facility-photos-grid">
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/workstation1.png') }}" alt="Workstations">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/workstation2.png') }}" alt="Workstations">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/workstation3.png') }}" alt="Workstations">
                </div>
            </div>
        </div>

        <div class="divider"></div>
        <h2 class="section-title">Computer Area</h2>

        {{-- COMPUTER AREA --}}
        <div class="facility-group">
            <div class="facility-photos-grid">
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/com_area1.png') }}" alt="Computer Area">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/com_area2.png') }}" alt="Computer Area">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/com_area3.png') }}" alt="Computer Area">
                </div>
            </div>
        </div>

        <div class="divider"></div>
        <h2 class="section-title">Exhibit Room</h2>

        {{-- EXHIBIT ROOM --}}
        <div class="facility-group">
            <div class="facility-photos-grid">
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/exhibit1.png') }}" alt="Exhibit Room">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/exhibit2.png') }}" alt="Exhibit Room">
                </div>
                <div class="facility-photo">
                    <img src="{{ asset('assets/system_images/about/facilities/exhibit3.png') }}" alt="Exhibit Room">
                </div>
            </div>
        </div>

    </div>


    {{-- LIGHTBOX MODAL --}}
    <div class="facility-lightbox" id="facilityLightbox">
        <div class="facility-lightbox-overlay" onclick="closeLightbox()"></div>
        <div class="facility-lightbox-content">
            <button class="facility-lightbox-close" onclick="closeLightbox()">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <button class="facility-lightbox-prev" onclick="lightboxNav(-1)">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <img src="" alt="" id="lightboxImg">
            <button class="facility-lightbox-next" onclick="lightboxNav(1)">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <div class="facility-lightbox-caption" id="lightboxCaption"></div>
        </div>
    </div>
</div>


@push('js')
<script>
(function () {
    let allPhotos = [];
    let currentIndex = 0;

    // Collect all facility photos on load
    function collectPhotos() {
        allPhotos = Array.from(document.querySelectorAll('.facility-photo img'));
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
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt;
            document.getElementById('lightboxCaption').textContent = img.alt;
            lightboxImg.style.opacity = '1';
        }, 150);
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') lightboxNav(-1);
        if (e.key === 'ArrowRight') lightboxNav(1);
    });

    collectPhotos();
})();
</script>
@endpush