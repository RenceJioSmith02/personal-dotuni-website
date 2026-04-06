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
    <nav class="about-tabs" id="aboutTabs">
        <button class="tab-btn active"
            data-tab="clsu"
            data-banner="{{ asset('assets/system_images/banners/banner1.png') }}">
            CLSU
        </button>
        <button class="tab-btn"
            data-tab="history"
            data-banner="{{ asset('assets/system_images/banners/banner1.png') }}">
            History
        </button>
        <button class="tab-btn"
            data-tab="objectives"
            data-banner="{{ asset('assets/system_images/banners/banner1.png') }}">
            Objectives
        </button>
        <button class="tab-btn"
            data-tab="administration"
            data-banner="{{ asset('assets/system_images/banners/banner1.png') }}">
            Administration
        </button>
        <button class="tab-btn"
            data-tab="departments"
            data-banner="{{ asset('assets/system_images/banners/banner1.png') }}">
            Departments
        </button>
        <button class="tab-btn"
            data-tab="membership"
            data-banner="{{ asset('assets/system_images/banners/banner1.png') }}">
            Membership
        </button>
    </nav>
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

    <div class="tab-panel" id="panel-administration">
        @include('website.pages.about.partials._tab_administration')
    </div>

    <div class="tab-panel" id="panel-departments">
        @include('website.pages.about.partials._tab_departments')
    </div>

    <div class="tab-panel" id="panel-membership">
        @include('website.pages.about.partials._tab_membership')
    </div>

</div>

@endsection

@push('js')
<script>
(function () {
    const hero        = document.getElementById('aboutHero');
    const tabBtns     = document.querySelectorAll('.tab-btn');
    const panels      = document.querySelectorAll('.tab-panel');
    const defaultBg   = "{{ asset('assets/system_images/banners/banner1.png') }}";

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