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

                    <a href="#" class="btn btn-success hero-btn mt-4">
                        Apply Now
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION 2 -->
    <section class="partners-section">
        <div class="container">
            <div class="divider"></div>
            <h2 class="section-title">
                PARTNER INSTITUTIONS AND SERVICES
            </h2>

            <div class="partners-grid">
            @foreach($linkages as $linkage)
                <div class="partner-card">
                    <img 
                    src="{{ asset('storage/'.optional($linkage->logo)->storage_path) 
                            ?? asset('assets/system_images/placeholder.jpg') }}">
                    <h3>{{ $linkage->title }}</h3>
                    <p>{{ $linkage->description }}</p>
                </div>
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
                        <a href="#" class="swiper-slide">
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
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>


                <!-- Side News Cards -->
                <div class="side-news">
                    @foreach($sideNews as $news)
                    <a href="#" class="news-card">
                        @php
                            $attachment = $news->attachments->first();
                            $imagePath = optional($attachment?->asset)->storage_path;
                        @endphp

                        <img 
                        src="{{ $imagePath 
                                ? asset('storage/'.$imagePath) 
                                : asset('assets/system_images/placeholder.jpg') }}">

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

                    <a href="#">
                        <button class="btn btn-success view-all-btn mt-3">
                            View All News
                        </button>
                    </a>
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

                <!-- NEWS COLUMN -->
                <div class="updates-col">
                    <div class="updates-header">
                        <span>News</span>
                        <a href="#">View More</a>
                    </div>

                    <div class="side-news">
                        @foreach($clsuNews as $news)
                        <a href="#" class="news-card">
                            <img 
                            src="{{ $news->imagePath 
                                    ? asset('storage/'.$news->imagePath) 
                                    : asset('assets/system_images/placeholder.jpg') }}">
                            <div class="card-content">
                                <h4>{{ $news->title }}</h4>
                                <p>{{ $news->description }}</p>
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
                </div>


                <!-- ANNOUNCEMENTS COLUMN -->
                <div class="updates-col">
                    <div class="updates-header">
                        <span>Announcements</span>
                        <a href="#">View more</a>
                    </div>

                    <div class="side-news">
                        @foreach($announcements as $announcement)
                        <a href="#" class="news-card">
                            <img 
                            src="{{ $announcement->assets->first()
                                ? asset('storage/'.$announcement->assets->first()->storage_path)
                                : asset('assets/system_images/placeholder.jpg') }}">
                            <div class="card-content">
                                <h4>{{ $announcement->title }}</h4>
                                <p>{{ Str::limit($announcement->article_body,100) }}</p>

                                <div class="card-footer">
                                    <span class="news-date">
                                        {{ \Carbon\Carbon::parse($announcement->published_at)->format('F d, Y') }}
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
            <div class="feature-item text-box">
                <h3>About Us</h3>
                <p>
                    The Central Luzon State University Open University (CLSU-O.U) was formally
                    created on August 29, 1997 through CLSU Board of Regents Resolution No. 50-97
                    aligned to the country's goal of developing quality human resources by
                    democratization of access to quality education.
                </p>
                <a href="#" class="btn btn-success mt-3">Learn More</a>
            </div>

            <!-- ITEM 3 -->
            <div class="feature-item text-box">
                <h3>Learning Materials</h3>
                <p>
                    The Open University students are provided with specially packaged printed
                    instructional materials or self-learning modules which they study on their
                    own most of the time.
                </p>
                <a href="#" class="btn btn-success mt-3">Learn More</a>
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
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            slidesPerView: 1,
        });


        //  SECTION 6 JS - CAROUSEL
        const track = document.getElementById("carousel");
        const cards = document.querySelectorAll(".card");
        let index = Math.floor(cards.length / 2);

        function updateCarousel() {
            cards.forEach((card, i) => {
                card.classList.toggle("active", i === index);
            });

            let offset = 0;
            const gap = 30;

            for (let i = 0; i < index; i++) {
                offset += cards[i].offsetWidth + gap;
            }

            const activeWidth = cards[index].offsetWidth;
            const center = track.parentElement.offsetWidth / 2 - activeWidth / 2;

            track.style.transform = `translateX(${center - offset}px)`;
        }

        document.getElementById("nextBtn").onclick = () => {
            index = (index + 1) % cards.length;
            updateCarousel();
        };

        document.getElementById("prevBtn").onclick = () => {
            index = (index - 1 + cards.length) % cards.length;
            updateCarousel();
        };

        window.addEventListener("resize", updateCarousel);
        updateCarousel();


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