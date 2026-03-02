@extends('layouts.website')

@section('title', 'News and Announcement | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement.css') }}" />

    <style>
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            animation: drop .4s ease forwards;
        }

        .view-all-wrapper {
            text-align: center;
            margin-top: 3%;
            cursor: pointer;
        }

        .view-all-btn {
            color: var(--gray-100);
            text-decoration: none;
            font-size: 20px;
            border: none;
            background: none;
        }

        @keyframes drop {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')

    <section class="content-section" id="announcement">
        <div class="container">
            <div class="section-header">
                <div class="divider"></div>
                <h2 class="section-title">ANNOUNCEMENTS</h2>
            </div>

            <div class="content-grid">
                @foreach($announcements as $content)
                    @include('website.partials.news-card', ['content' => $content])
                @endforeach
            </div>

            @if($announcementTotal > 8)
                <div class="view-all-wrapper">
                    <button class="view-all-btn toggle-news"
                            data-section="announcement"
                            data-is-full="false">
                        See All
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="content-section" id="dotuni-news">
        <div class="container">
            <div class="section-header">
                <div class="divider"></div>
                <h2 class="section-title">DOTUNI NEWS</h2>
            </div>

            <div class="content-grid">
                @foreach($dotuniNews as $content)
                    @include('website.partials.news-card', ['content' => $content])
                @endforeach
            </div>

            @if($dotuniTotal > 8)
                <div class="view-all-wrapper">
                    <button class="view-all-btn toggle-news"
                            data-section="dotuni"
                            data-is-full="false">
                        See All
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="content-section" id="clsu-news">
        <div class="container">
            <div class="section-header">
                <div class="divider"></div>
                <h2 class="section-title">CLSU NEWS</h2>
            </div>

            <div class="content-grid">
                @foreach($clsuNews as $content)
                    @include('website.partials.news-card', ['content' => $content])
                @endforeach
            </div>

            @if($clsuTotal > 8)
                <div class="view-all-wrapper">
                    <button class="view-all-btn toggle-news"
                            data-section="clsu"
                            data-is-full="false">
                        See All
                    </button>
                </div>
            @endif
        </div>
    </section>

@endsection


@push('js')
<script>
    let sectionData = {};

    document.addEventListener('DOMContentLoaded', function () {
        ['announcement', 'dotuni-news', 'clsu-news'].forEach(section => {
            const grid = document.querySelector(`#${section} .content-grid`);
            if (grid) {
                sectionData[section] = Array.from(grid.children);
            }
        });
    });

    function formatDate(dateStr) {
        try {
            const date = new Date(dateStr);
            if (!date || isNaN(date.getTime())) return 'Invalid date';
            return date.toLocaleDateString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric'
            }).replace(/,/g, '');
        } catch (e) {
            return 'Invalid date';
        }
    }

    function buildHref(item) {
        if (item.type === 'clsu') {
            return item.url || '#';
        }
        const base = item.type === 'announcement'
            ? '/news/announcement/'
            : '/news/dotuni/';
        return base + item.id;
    }

    function buildTarget(item) {
        return item.type === 'clsu' ? '_blank' : '_self';
    }

    document.querySelectorAll('.toggle-news').forEach(btn => {
        btn.addEventListener('click', function () {

            const type = this.dataset.section;

            const sectionId = type === 'announcement'
                ? 'announcement'
                : type === 'dotuni'
                    ? 'dotuni-news'
                    : 'clsu-news';

            const grid = document.querySelector(`#${sectionId} .content-grid`);
            const isFull = this.dataset.isFull === 'true';

            // ── See Less: restore original 8 cards ──
            if (isFull) {
                grid.innerHTML = '';
                if (sectionData[sectionId]) {
                    sectionData[sectionId].forEach(card => {
                        const clone = card.cloneNode(true);
                        clone.classList.add('fade-in');
                        grid.appendChild(clone);
                    });
                }
                this.textContent = 'See All';
                this.dataset.isFull = 'false';
                return;
            }

            // ── See All: fetch remaining cards ──
            this.disabled = true;
            this.textContent = 'Loading...';

            fetch(`/news/load-more/${type}`)
                .then(res => res.json())
                .then(moreData => {

                    grid.innerHTML = '';

                    // Re-render original 8 first
                    if (sectionData[sectionId]) {
                        sectionData[sectionId].forEach(card => {
                            const clone = card.cloneNode(true);
                            clone.classList.add('fade-in');
                            grid.appendChild(clone);
                        });
                    }

                    // Append newly fetched cards
                    moreData.forEach(item => {
                        const imageSrc = item.image
                            ? '/storage/' + item.image
                            : '/assets/system_images/placeholder.jpg';

                        const cardHTML = `
                            <a href="${buildHref(item)}" target="${buildTarget(item)}" class="content-card">
                                <img src="${imageSrc}" alt="${item.title || ''}" loading="lazy">
                                <div class="card-content">
                                    <span class="card-category">${(item.type || 'news').toUpperCase()}</span>
                                    <h4>${item.title || 'No title'}</h4>
                                    <p>${item.description || ''}</p>
                                    <div class="card-footer">
                                        <span class="news-date">${formatDate(item.date)}</span>
                                        <span class="read-more">Read More</span>
                                    </div>
                                </div>
                            </a>
                        `;

                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = cardHTML;
                        const newCard = tempDiv.firstElementChild;
                        if (newCard) {
                            newCard.classList.add('fade-in');
                            grid.appendChild(newCard);
                        }
                    });

                    this.textContent = 'See Less';
                    this.dataset.isFull = 'true';
                })
                .catch(err => {
                    console.error(err);
                })
                .finally(() => {
                    this.disabled = false;
                });
        });
    });
</script>
@endpush

