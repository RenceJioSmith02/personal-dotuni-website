@extends('layouts.website')

@section('title', 'News and Announcement | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/news-and-announcement.css') }}" />

    <style>
        .fade-in{
            opacity:0;
            transform:translateY(30px);
            animation:drop .4s ease forwards;
        }

        @keyframes drop{
            to{
            opacity:1;
            transform:translateY(0);
            }
        }
    </style>
@endpush

@section('content')

    <section class="content-section" id="announcement">

        <div class="container">

        <div class="section-header">
        <h2 class="section-title">ANNOUNCEMENTS</h2>
        </div>

        <div class="content-grid">

            @foreach($announcements as $content)

            @include('website.partials.news-card',['content'=>$content])

            @endforeach

        </div>

        @if($announcementTotal > 8)
        <div class="view-all-wrapper">
        <button class="view-all-btn load-more"
        data-section="announcement">
        See All
        </button>

        @endif

    </section>

    <section class="content-section" id="dotuni-news">

    <div class="container">

    <div class="section-header">
    <h2 class="section-title">DOTUNI NEWS</h2>
    </div>

    <div class="content-grid">

    @foreach($dotuniNews as $content)

    @include('website.partials.news-card',['content'=>$content])

    @endforeach

    </div>

    @if($dotuniTotal > 8)
    <div class="view-all-wrapper">
    <button class="view-all-btn load-more"
    data-section="dotuni">
    See All
    </button>

    @endif

    </section>

    <section class="content-section" id="clsu-news">

    <div class="container">

    <div class="section-header">
    <h2 class="section-title">CLSU NEWS</h2>
    </div>

    <div class="content-grid">

    @foreach($clsuNews as $content)

    @include('website.partials.news-card',['content'=>$content])

    @endforeach

    </div>

    @if($clsuTotal > 8)
    <div class="view-all-wrapper">
    <button class="view-all-btn load-more"
    data-section="clsu">
    See All
    </button>

    @endif
    </section>

@endsection

@push('js')

<script>
function scrollToSection(id){
    document.getElementById(id).scrollIntoView({
        behavior:'smooth'
    });
}



document.querySelectorAll('.load-more').forEach(btn=>{

    btn.addEventListener('click',function(){

        let type=this.dataset.section
        let grid=this.closest('section')
        .querySelector('.content-grid')

        fetch('/news/load-more/'+type)
        .then(res=>res.json())
        .then(data=>{

            data.forEach(card=>{
                let div=document.createElement('div')
                div.innerHTML=card
                div.classList.add('fade-in')
                grid.appendChild(div)
            })

            this.remove()

        })

    })

})

</script>
@endpush