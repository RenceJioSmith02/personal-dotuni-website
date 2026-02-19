@extends('layouts.website')

@section('title', 'E-Resources | CLSU DOT-Uni')

@push('css')

    <style>
        .resource-section {
    background: #f2f2f2;
    padding: 60px 40px;
}

.resource-container {
    max-width: 1200px;
    margin: 0 auto;
}

.resource-list {
    column-count: 2;
    column-gap: 60px;
    padding-left: 20px;
}

.resource-list li {
    break-inside: avoid-column;
    margin-bottom: 10px;
    line-height: 1.6;
}

.no-resource-message {
    background: #ffffff;
    border: 1px dashed #ccc;
    padding: 20px;
    margin-top: 20px;
    text-align: center;
    border-radius: 6px;
    font-size: 15px;
    color: #555;
}


@media (max-width: 700px) {

    .resource-section {
        padding: 40px 20px;
    }

    .resource-list {
        column-count: 1;
        column-gap: 0;
    }

}


    </style>

@endpush

@section('content')


<section class="resource-section">

    <div class="divider"></div>
    <h2 class="section-title">
        E-RESOURCES
    </h2>

    <div class="content">
        <div class="resource-container">

            <ol class="resource-list">

                @forelse($resources as $resource)

                    <li>
                        <a href="{{ $resource->link_url }}"
                        target="_blank"
                        title="{{ $resource->description }}"
                        rel="noopener noreferrer">

                            {{ $resource->name }}

                        </a>
                    </li>

                @empty

                    <div class="no-resource-message">
                        <p>
                            No electronic resources are currently available.
                            Kindly visit this page again later for updates.
                        </p>
                    </div>

                @endforelse


            </ol>


        </div>
    </div>

</section>


@endsection
