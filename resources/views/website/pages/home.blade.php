@extends('layouts.website')

@section('title', 'Home | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/homepage.css') }}" />
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

                <!-- Card 1 -->
                <div class="partner-card">
                    <img src="{{ asset('storage/linkages/linkages-2026-02-09-6a1a4bcc.jpg') }}" alt="Asian Association">
                    <h3>Asian Association of Open Universities</h3>
                    <p>Associate Member</p>
                </div>

                <!-- Card 2 -->
                <div class="partner-card">
                    <img src="{{ asset('storage/linkages/linkages-2026-02-09-6a1a4bcc.jpg') }}" alt="University of Liverpool">
                    <h3>University of Liverpool</h3>
                </div>

                <!-- Card 3 -->
                <div class="partner-card">
                    <img src="{{ asset('storage/linkages/linkages-2026-02-09-6a1a4bcc.jpg') }}" alt="British Council">
                    <h3>British Council</h3>
                </div>

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

                        <!-- Slide 1 -->
                        <a href="#" class="swiper-slide">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="Main News 1" />
                            <div class="news-caption">
                                <span class="news-category">NEWS</span>
                                <h3>DOT-Uni QA Coordinator Highlights Research on Distance Learning at AAOU Conference</h3>
                                <p>Quality Assurance Coordinator from the Distance Open and Transnational University</p>

                                <div class="seo-tags">
                                    <span>#DistanceLearning</span>
                                    <span>#AAOU2026</span>
                                    <span>#Research</span>
                                </div>


                                <div class="card-footer">
                                    <span class="news-date">February 12, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>

                        <!-- Slide 2 -->
                        <a href="#" class="swiper-slide">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="Main News 2" />
                            <div class="news-caption">
                                <span class="news-category">NEWS</span>
                                <h3>Another News Title Here</h3>
                                <p>Short description for second news item...</p>

                                <div class="seo-tags">
                                    <span>#DistanceLearning</span>
                                    <span>#AAOU2026</span>
                                    <span>#Research</span>
                                </div>

                                <div class="card-footer">
                                    <span class="news-date">February 12, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>

                        <!-- Add more slides as needed -->

                    </div>

                    <!-- Navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>


                <!-- Side News Cards -->
                <div class="side-news">
                    <a href="#" class="news-card">
                        <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                        <div class="card-content">
                            <h4>CLSU Student Handbook Quality Assurance Coordinator from the Distance Open and Transnational University (DOT-Uni)</h4>
                            <p>
                                Quality Assurance Coordinator from the Distance Open and Transnational University (DOT-Uni)
                                presented her studies at the 38th Asian Association of Open Universities (AAOU) Conference
                            </p>

                            <div class="card-footer">
                                <span class="news-date">February 10, 2026</span>
                                <span class="read-more">Read More</span>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="news-card">
                        <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                        <div class="card-content">
                            <h4>CLSU Student Handbook</h4>
                            <p>
                                Quality Assurance Coordinator from the Distance Open and Transnational University (DOT-Uni)
                                presented her studies at the 38th Asian Association of Open Universities (AAOU) Conference
                            </p>

                            <div class="card-footer">
                                <span class="news-date">February 10, 2026</span>
                                <span class="read-more">Read More</span>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="news-card">
                        <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                        <div class="card-content">
                            <h4>CLSU Student Handbook</h4>
                            <p>
                                Quality Assurance Coordinator from the Distance Open and Transnational University (DOT-Uni)
                                presented her studies at the 38th Asian Association of Open Universities (AAOU) Conference
                            </p>

                            <div class="card-footer">
                                <span class="news-date">February 10, 2026</span>
                                <span class="read-more">Read More</span>
                            </div>
                        </div>
                    </a>

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
                        <a href="#" class="news-card">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                            <div class="card-content">
                                <h4>CLSU Student Handbook </h4>
                                <p>
                                    Quality Assurance Coordinator from the Distance Open and Transnational University
                                    (DOT-Uni) presented her studies at the 38th Asian Association of Open Universities
                                    (AAOU) Conference
                                </p>

                                <div class="card-footer">
                                    <span class="news-date">February 10, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="news-card">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                            <div class="card-content">
                                <h4>CLSU Student Handbook</h4>

                                <p>
                                    Quality Assurance Coordinator from the Distance Open and Transnational University
                                    (DOT-Uni) presented her studies at the 38th Asian Association of Open Universities
                                    (AAOU) Conference
                                </p>

                                <div class="card-footer">
                                    <span class="news-date">February 10, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="news-card">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                            <div class="card-content">
                                <h4>CLSU Student Handbook</h4>
                                <p>
                                    Quality Assurance Coordinator from the Distance Open and Transnational University
                                    (DOT-Uni) presented her studies at the 38th Asian Association of Open Universities
                                    (AAOU) Conference
                                </p>

                                <div class="card-footer">
                                    <span class="news-date">February 10, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>


                <!-- ANNOUNCEMENTS COLUMN -->
                <div class="updates-col">
                    <div class="updates-header">
                        <span>Announcements</span>
                        <a href="#">View more</a>
                    </div>

                    <div class="side-news">
                        <a href="#" class="news-card">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                            <div class="card-content">
                                <h4>CLSU Student Handbook</h4>
                                <p>
                                    Quality Assurance Coordinator from the Distance Open and Transnational University
                                    (DOT-Uni) presented her studies at the 38th Asian Association of Open Universities
                                    (AAOU) Conference
                                </p>

                                <div class="card-footer">
                                    <span class="news-date">February 10, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="news-card">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                            <div class="card-content">
                                <h4>CLSU Student Handbook</h4>
                                <p>
                                    Quality Assurance Coordinator from the Distance Open and Transnational University
                                    (DOT-Uni) presented her studies at the 38th Asian Association of Open Universities
                                    (AAOU) Conference
                                </p>

                                <div class="card-footer">
                                    <span class="news-date">February 10, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="news-card">
                            <img src="{{ asset('storage/dotuni_news/dotuni_news-2026-02-09-bd6c48f1.png') }}" alt="CLSU Student Handbook" />
                            <div class="card-content">
                                <h4>CLSU Student Handbook</h4>
                                <p>
                                    Quality Assurance Coordinator from the Distance Open and Transnational University
                                    (DOT-Uni) presented her studies at the 38th Asian Association of Open Universities
                                    (AAOU) Conference
                                </p>

                                <div class="card-footer">
                                    <span class="news-date">February 10, 2026</span>
                                    <span class="read-more">Read More</span>
                                </div>
                            </div>
                        </a>
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
                    <div class="card"><img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}"></div>
                    <div class="card"><img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}"></div>
                    <div class="card"><img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}"></div>
                    <div class="card"><img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}"></div>
                    <div class="card"><img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}"></div>
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

                    <div class="faq-item">
                        <span>1. What are the offered programs?</span>
                    </div>

                    <div class="faq-item">
                        <span>2. What are the list of requirements for admission in the DOT-Uni Curricular Programs?</span>
                    </div>

                    <div class="faq-item">
                        <span>3. What are the steps to be admitted?</span>
                    </div>

                    <div class="faq-item">
                        <span>4. How do I contact DOT-Uni?</span>
                    </div>

                    <div class="faq-item">
                        <span>5. Where can I request for TOR, COG or CAV?</span>
                    </div>

                    <div class="faq-seeall">
                        See all
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
    </script>

@endpush