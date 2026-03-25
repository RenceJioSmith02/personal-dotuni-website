@extends('layouts.website')

@section('title', 'Home | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/homepage.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/website/faqs.css') }}" />
@endpush

@section('content')

    <!-- SECTION 1 -->
    <section class="hero-section">
        <div class="hero-overlay"></div>

        <div class="container hero-content">
            <div class="row align-items-center">

                <div class="col-lg-6">
                    <h1 class="hero-title">
                        CLSU Distance, Open and Transnational University <br> (DOT-Uni)
                    </h1>

                    <a href="https://cais.oad.clsu2.edu.ph/login" class="btn btn-success hero-btn mt-4">
                        Apply Now
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION 2 -->
    <section class="partners-section" id="partners-section">
        <div class="container">
            <div class="divider"></div>
            <h2 class="section-title">
                PARTNER INSTITUTIONS AND SERVICES
            </h2>

            <div class="partners-grid">
            @foreach($linkages as $linkage)
                <a href="{{ $linkage->url }}" class="partner-card">
                        <img 
                        src="{{ asset('storage/'.optional($linkage->logo)->storage_path) 
                                ?? asset('assets/system_images/placeholder.jpg') }}">
                        <div>
                            <h3>{{ $linkage->title }}</h3>
                            <p>{{ $linkage->description }}</p>
                        </div>
                </a>
            @endforeach
            </div>

        </div>
    </section>

    
    <!-- SECTION 3: LATEST NEWS -->
    <section class="latest-news-section">
        <div class="container">
            <div class="divider"></div>
            <h2 class="section-title">LATEST NEWS</h2>

            <div class="news-grid">

                <!-- Main News Carousel -->
                <div class="main-news swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($mainNews as $news)
                        <a href="{{ route('news.show', ['type' => 'dotuni', 'id' => $news->id]) }}"
                           class="swiper-slide">
                            <img
                            src="{{ optional(optional($news->attachments->first())->asset)->storage_path
                                    ? asset('storage/'.optional(optional($news->attachments->first())->asset)->storage_path)
                                    : asset('assets/system_images/placeholder.jpg') }}">
                            <div class="news-caption">
                                <span class="news-category">NEWS</span>
                                <h3>{{ $news->title }}</h3>
                                <p>{{ $news->seo_description }}</p>

                                <div class="seo-tags">
                                    @if(!empty($news->seo_title))
                                        @foreach(preg_split('/#/', $news->seo_title, -1, PREG_SPLIT_NO_EMPTY) as $tag)
                                            <span>#{{ trim($tag) }}</span>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="card-footer">
                                    <span class="news-date">
                                        {{ \Carbon\Carbon::parse($news->published_at)->format('F d, Y') }}
                                    </span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    <!-- Navigation -->
                    <div class="carousel-btn next"></div>
                    <div class="carousel-btn prev"></div>
                </div>


                <!-- Side News Cards -->
                <div class="side-news">
                    @foreach($sideNews as $news)
                    <a href="{{ route('news.show', ['type' => 'dotuni', 'id' => $news->id]) }}"
                       class="news-card">
                        @php
                            $attachment = $news->attachments->first();
                            $imagePath  = optional($attachment?->asset)->storage_path;
                        @endphp

                        {{-- <img
                        src="{{ $imagePath
                                ? asset('storage/'.$imagePath)
                                : asset('assets/system_images/placeholder.jpg') }}"> --}}

                        <div class="card-content">
                            <h4>{{ $news->title }}</h4>
                            <p>{{ $news->seo_description }}</p>
                            <div class="card-footer">
                                <span class="news-date">
                                    {{ \Carbon\Carbon::parse($news->published_at)->format('F d, Y') }}
                                </span>
                                <span class="read-more">Read More</span>
                            </div>
                        </div>
                    </a>
                    @endforeach

                    <div>
                        <a href="{{ route('website.news') }}" class="btn btn-success view-all-btn mt-3">
                            View All
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- SECTION 4: CLSU NEWS AND UPDATES -->
    <section class="clsu-updates-section">
        <div class="container">
            <div class="divider"></div>
            <h2 class="section-title">CLSU NEWS AND UPDATES</h2>

            <div class="updates-grid">

                <!-- NEWS COLUMN (ClsuNews → external URL) -->
                <div class="updates-col">
                    <div class="updates-header">
                        <span>News</span>
                        <a href="{{ route('website.news') }}">View More</a>
                    </div>

                    <div class="side-news">
                        @foreach($clsuNews as $news)
                        <a href="{{ $news->url ?: '#' }}"
                           target="{{ $news->url ? '_blank' : '_self' }}"
                           class="news-card">
                            <img
                            src="{{ optional($news->thumbnail)->storage_path
                                    ? asset('storage/'.optional($news->thumbnail)->storage_path)
                                    : asset('assets/system_images/placeholder.jpg') }}">
                            <div class="card-content">
                                <h4>{{ $news->title }}</h4>
                                <p>{{ $news->description }}</p>
                                <div class="card-footer">
                                    <span class="news-date">
                                        {{ \Carbon\Carbon::parse($news->created_at)->format('F d, Y') }}
                                    </span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>


                <!-- ANNOUNCEMENTS COLUMN -->
                <div class="updates-col">
                    <div class="updates-header">
                        <span>Announcements</span>
                        <a href="{{ route('website.news') }}">View more</a>
                    </div>

                    <div class="side-news">
                        @foreach($announcements as $announcement)
                        <a href="{{ route('news.show', ['type' => 'announcement', 'id' => $announcement->id]) }}"
                           class="news-card">
                            @php
                                $thumb = $announcement->thumbnail();
                            @endphp
                            <img
                            src="{{ optional($thumb)->storage_path
                                    ? asset('storage/'.optional($thumb)->storage_path)
                                    : asset('assets/system_images/placeholder.jpg') }}">
                            <div class="card-content">
                                <h4>{{ $announcement->title }}</h4>
                                <p>{{ Str::limit($announcement->seo_description, 100) }}</p>
                                <div class="card-footer">
                                    <span class="news-date">
                                        {{ \Carbon\Carbon::parse($announcement->publish_start)->format('F d, Y') }}
                                    </span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- SECTION 5: DOT-UNI FEATURES -->
    <section class="dotuni-features">
        <div class="features-grid">

            <!-- ITEM 1 -->
            <div class="feature-item image-box">
                <img src="{{ asset('assets/system_images/Signage.png') }}" alt="DOT-UNI Signage">
            </div>

            <!-- ITEM 2 -->
            <div class="feature-item text-box first">
                <div class="feature-overlay"></div>
                <div class="feature-content">
                    <h3>About Us</h3>
                    <p>
                        The Central Luzon State University Open University (CLSU-O.U) was formally
                        created on August 29, 1997 through CLSU Board of Regents Resolution No. 50-97
                        aligned to the country's goal of developing quality human resources by
                        democratization of access to quality education.
                    </p>
                    <a href="{{ route('website.pages.about') }}" class="btn btn-success mt-3">Learn More</a>
                </div>
            </div>

            <!-- ITEM 3 -->
            <div class="feature-item text-box second">
                <div class="feature-overlay"></div>
                <div class="feature-content">
                    <h3>Learning Materials</h3>
                    <p>
                        The Open University students are provided with specially packaged printed
                        instructional materials or self-learning modules which they study on their
                        own most of the time.
                    </p>
                    <a href="{{ route('website.pages.about') }}" class="btn btn-success mt-3">Learn More</a>
                </div>
            </div>

            <!-- ITEM 4 -->
            <div class="feature-item image-box">
                <img src="{{ asset('assets/system_images/Exhibit.jpg') }}" alt="DOT-UNI Exhibit">
            </div>

        </div>
    </section>

    <!-- SECTION 6: PROGRAMS -->
    <section class="program-section">
        <div class="divider"></div>
        <h2 class="section-title">PROGRAM COURSES</h2>

        <div class="carousel-wrapper">
            <button class="nav-btn left" id="prevBtn">&#10094;</button>

            <div class="carousel-viewport">
                <div class="carousel-track" id="carousel">
                    @foreach($programs as $program)
                    <div class="card">
                        <img 
                        src="{{ $program->imagePath 
                                ? asset('storage/'.$program->imagePath) 
                                : asset('assets/system_images/placeholder.jpg') }}">
                    </div>
                    @endforeach
                </div>
            </div>

            <button class="nav-btn right" id="nextBtn">&#10095;</button>
        </div>
    </section>

    <!-- SECTION: 7 FAQS -->
    <section class="faq-section">

        <div class="divider"></div>
        <h2 class="section-title">FREQUENTLY ASKED QUESTIONS</h2>

        <div class="faq-container">

            <!-- LEFT SIDE LOGO -->
            <div class="faq-left">
                <img src="{{ asset('assets/system_images/dotuni.png') }}" alt="DOT UNI Logo">
            </div>

            <!-- RIGHT SIDE FAQS -->
            <div class="faq-right">

                <div class="faq-card">

                    @foreach($faqs as $faq)
                    <div class="faq-item-wrapper">
                        <div class="faq-item">
                            <span>{{ $faq->question }}</span>
                        </div>
                        <div class="faq-answer">
                            <ul>
                                <ul>
                                    @forelse($faq->answers as $answer)
                                        <li>{!! $answer->answer !!}</li>
                                    @empty
                                        <li class="no-answer">No answer</li>
                                    @endforelse
                                </ul>
                            </ul>
                        </div>
                    </div>
                    @endforeach

                    <div class="faq-seeall">
                        <a href="{{ route('website.faqs') }}">See all</a>
                    </div>


                </div>

            </div>
        </div>
    </section>

@endsection


@push('js')

    <!-- SECTION 3 JS -->
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <script>
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            navigation: {
                nextEl: '.carousel-btn.next',
                prevEl: '.carousel-btn.prev',
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            slidesPerView: 1,
        });



// SECTION 6 JS - CAROUSEL
const track = document.getElementById("carousel");
const gap = 30;

const originalCards = Array.from(track.querySelectorAll(".card"));
const total = originalCards.length;
const CLONE_SETS = 3;

for (let s = 0; s < CLONE_SETS; s++) {
    originalCards.forEach(card => track.appendChild(card.cloneNode(true)));
}
for (let s = 0; s < CLONE_SETS; s++) {
    [...originalCards].reverse().forEach(card => track.prepend(card.cloneNode(true)));
}

const MID = Math.floor(CLONE_SETS / 2) + 1;
let index = total * MID;
let isAnimating = false;
let autoplayTimer;
let resizeDebounce;

// Card sizes — must match your CSS exactly
const CARD_SIZES = {
    active:   300,
    inactive: 220,
};

function getResponsiveSize() {
    const w = window.innerWidth;
    if (w <= 480) return { active: 180, inactive: 130 };
    if (w <= 768) return { active: 220, inactive: 160 };
    return { active: 300, inactive: 220 };
}

function allCards() {
    return Array.from(track.querySelectorAll(".card"));
}

function getOffset(idx) {
    // Use known CSS sizes — no DOM measurement, always accurate
    const sizes = getResponsiveSize();
    let offset = 0;
    const cards = allCards();
    for (let i = 0; i < idx; i++) {
        const isActive = i === index;
        offset += (isActive ? sizes.active : sizes.inactive) + gap;
    }
    return offset;
}

function updateCarousel(animate = true) {
    const cards = allCards();
    const sizes = getResponsiveSize();

    // Toggle active — triggers grow/shrink CSS transition simultaneously
    cards.forEach((card, i) => card.classList.toggle("active", i === index));

    track.style.transition = animate
        ? "transform .55s cubic-bezier(.4, 0, .2, 1)"
        : "none";

    // Calculate using known final sizes — not measured offsetWidth
    let offset = 0;
    for (let i = 0; i < index; i++) {
        offset += (i === index ? sizes.active : sizes.inactive) + gap;
    }

    const center = track.parentElement.offsetWidth / 2 - sizes.active / 2;
    track.style.transform = `translateX(${center - offset}px)`;
}

track.addEventListener("transitionend", (e) => {
    if (e.target !== track || e.propertyName !== "transform") return;

    isAnimating = false;

    const posInSet     = ((index % total) + total) % total;
    const snappedIndex = total * MID + posInSet;

    if (snappedIndex !== index) {
        index = snappedIndex;
        updateCarousel(false);
    }
});

function navigate(dir) {
    if (isAnimating) return;
    isAnimating = true;
    index += dir;
    updateCarousel(true);
    resetAutoplay();
}

function startAutoplay() {
    autoplayTimer = setInterval(() => {
        if (!isAnimating) navigate(1);
    }, 5000);
}

function resetAutoplay() {
    clearInterval(autoplayTimer);
    startAutoplay();
}

document.getElementById("nextBtn").onclick = () => navigate(1);
document.getElementById("prevBtn").onclick = () => navigate(-1);

window.addEventListener("resize", () => {
    isAnimating = false;
    clearTimeout(resizeDebounce);
    resizeDebounce = setTimeout(() => updateCarousel(false), 100);
});

updateCarousel(false);
startAutoplay();


        // SECTION 7 JS - FAQS
        document.addEventListener('DOMContentLoaded', () => {
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                item.addEventListener('click', () => {
                    // Toggle active class
                    item.classList.toggle('active');

                    // Close other items if you want accordion behavior
                    faqItems.forEach(other => {
                        if (other !== item) {
                            other.classList.remove('active');
                        }
                    });
                });
            });
        });

    </script>

    

@endpush