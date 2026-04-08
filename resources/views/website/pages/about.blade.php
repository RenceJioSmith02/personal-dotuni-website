@extends('layouts.website')

@section('title', 'About Us | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/about.css') }}" />
@endpush

@section('content')

{{-- HERO BANNER --}}
<div class="about-hero" id="aboutHero">
    <div class="about-hero-overlay"></div>
    <div class="about-hero-content">
        <h1 class="about-hero-title">ABOUT US</h1>
    </div>
</div>

{{-- TAB NAVIGATION --}}
<div class="about-tabs-wrapper">
    <div class="about-tabs-scroll">
        <nav class="about-tabs" id="aboutTabs">
            <button class="tab-btn active"
                data-tab="clsu"
                data-banner="{{ asset('assets/system_images/about/banners/banner1.png') }}">
                CLSU
            </button>
            <button class="tab-btn"
                data-tab="history"
                data-banner="{{ asset('assets/system_images/about/banners/banner2.png') }}">
                History
            </button>
            <button class="tab-btn"
                data-tab="objectives"
                data-banner="{{ asset('assets/system_images/about/banners/banner3.png') }}">
                Objectives
            </button>
            <button class="tab-btn"
                data-tab="facilities"
                data-banner="{{ asset('assets/system_images/about/banners/banner4.png') }}">
                Facilities
            </button>
            <button class="tab-btn"
                data-tab="administration"
                data-banner="{{ asset('assets/system_images/about/banners/banner5.png') }}">
                Administration
            </button>
            <button class="tab-btn"
                data-tab="departments"
                data-banner="{{ asset('assets/system_images/about/banners/banner6.png') }}">
                Departments
            </button>
            <button class="tab-btn"
                data-tab="membership"
                data-banner="{{ asset('assets/system_images/about/banners/banner7.png') }}">
                Membership
            </button>
            <button class="tab-btn"
                data-tab="contact"
                data-banner="{{ asset('assets/system_images/about/banners/banner8.png') }}">
                Contact Us
            </button>
        </nav>
    </div>
</div>

{{-- TAB CONTENT PANELS --}}
<div class="about-panels">

    <div class="tab-panel active" id="panel-clsu">
        @include('website.pages.about.partials._tab_clsu')
    </div>

    <div class="tab-panel" id="panel-history">
        @include('website.pages.about.partials._tab_history')
    </div>

    <div class="tab-panel" id="panel-objectives">
        @include('website.pages.about.partials._tab_objectives')
    </div>

    <div class="tab-panel" id="panel-facilities">
        @include('website.pages.about.partials._tab_facilities')
    </div>

    <div class="tab-panel" id="panel-administration">
        @include('website.pages.about.partials._tab_administration')
    </div>

    <div class="tab-panel" id="panel-departments">
        @include('website.pages.about.partials._tab_departments')
    </div>

    <div class="tab-panel" id="panel-membership">
        @include('website.pages.about.partials._tab_membership')
    </div>

    <div class="tab-panel" id="panel-contact">
        @include('website.pages.about.partials._tab_contact')
    </div>

</div>

@endsection

@push('js')
<script>
(function () {
    const hero        = document.getElementById('aboutHero');
    const tabBtns     = document.querySelectorAll('.tab-btn');
    const panels      = document.querySelectorAll('.tab-panel');
    const defaultBg   = "{{ asset('assets/system_images/about/banners/banner1.png') }}";

    // Set initial banner
    hero.style.backgroundImage = `url('${defaultBg}')`;

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            const banner = btn.dataset.banner;

            // Switch active tab button
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Switch panel
            panels.forEach(p => p.classList.remove('active'));
            document.getElementById('panel-' + target).classList.add('active');

            // Swap hero banner with fade
            hero.classList.add('banner-fade');
            setTimeout(() => {
                hero.style.backgroundImage = `url('${banner}')`;
                hero.classList.remove('banner-fade');
            }, 250);

            // Scroll to tabs on mobile
            if (window.innerWidth < 768) {
                document.getElementById('aboutTabs').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Highlight active tab on horizontal scroll (mobile)
    const tabsEl = document.getElementById('aboutTabs');
    document.querySelector('.tab-btn.active')?.scrollIntoView({ inline: 'center', behavior: 'smooth' });
})();
</script>
@endpush