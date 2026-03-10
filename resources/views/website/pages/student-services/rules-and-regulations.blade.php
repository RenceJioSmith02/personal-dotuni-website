@extends('layouts.website')

@section('title', 'Admission Requirements | CLSU DOT-Uni')

@push('css')
<style>
    .document-section {
        background: #f2f2f2;
        padding: 60px 40px;
    }

    .document-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 6px;
    }

    .document-header {
        margin-bottom: 25px;
    }

    .document-article {
        border-left: 3px solid #E8D203; 
        padding-left: 10px;
        margin-bottom: 10px;
    }

    .document-title {
        text-align: center;
    }

    .document-body {
        border-left: 3px solid #1c7c34;
        padding-left: 20px;
        text-align: justify;
        line-height: 1.7;
    }

    .document-body p {
        margin-bottom: 18px;
    }

    .document-list {
        padding-left: 20px;
    }

    .document-list ul {
        list-style-type: disc;
        padding-left: 20px;
        margin-top: 10px;
    }

    .document-list li {
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')

<section class="document-section">

    <div class="divider"></div>
    <h2 class="section-title">
        RULES AND REGULATIONS
    </h2>

    <div class="content">

        @foreach($articles as $article)
        <div class="document-container">

            <div class="document-header">
                <h4 class="document-article">
                    {{ $article->number }}
                </h4>
                <h5 class="document-title">
                    {{ $article->title }}
                </h5>
            </div>

            <div class="document-body">

                {{-- SECTIONS --}}
                @foreach($article->sections as $section)
                    <p>
                        <strong>{{ $section->number }} :</strong>
                        {!! $section->body !!}
                    </p>

                    {{-- SUB SECTIONS --}}
                    @if($section->subSections->count())
                    <ol class="document-list">
                        @foreach($section->subSections as $sub)
                        <li>
                            {!! $sub->body !!}

                            {{-- CLAUSES --}}
                            @if($sub->clauses->count())
                            <ul>
                                @foreach($sub->clauses as $clause)
                                <li>
                                    {!! $clause->body !!}
                                </li>
                                @endforeach
                            </ul>
                            @endif

                        </li>
                        @endforeach
                    </ol>
                    @endif

                @endforeach

            </div>

        </div>
        @endforeach

    </div>

</section>



@endsection
