@extends('layouts.website')

@section('title', content: 'Forms | CLSU DOT-Uni')

@push('css')

<style>
    .forms-section {
    background: #f2f2f2;
    padding: 60px 40px;
}

.forms-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px 60px;
}

.form-category {
    break-inside: avoid;
}

.category-title {
    border-left: 4px solid #E8D203;
    padding-left: 10px;
    margin-bottom: 10px;
    font-weight: 600;
}

.form-category ol {
    padding-left: 18px;
}

.form-category li {
    margin-bottom: 6px;
    line-height: 1.6;
}

.form-category li a{
    color: #1e1e1e;
    text-decoration: none;
}


.form-category li a:hover {
    color: blue;
    text-decoration: underline;
}

@media (max-width: 700px) {

    .forms-section {
        padding: 40px 20px;
    }

    .forms-container {
        grid-template-columns: 1fr;
    }

}

</style>

@endpush

@section('content')


<section class="forms-section">

    <div class="divider"></div>
    <h2 class="section-title">
        {{ $type === 'course-prospectus'
            ? 'COURSE PROSPECTUS'
            : 'DOWNLOADABLE FORMS'
        }}
    </h2>

    <div class="content">

        <div class="forms-container">

        @forelse($categories as $category)

            <div class="form-category">

                <h4 class="category-title">
                    {{ $category->name }}
                </h4>

                <ol>

                    @forelse($category->forms->where('is_active', true) as $form)

                        <li>
                            <a href="{{ $form->file_url }}"
                            target="_blank"
                            rel="noopener noreferrer">

                                {{ $form->name }}

                            </a>
                        </li>

                    @empty

                        <li>No forms available.</li>

                    @endforelse

                </ol>

            </div>

        @empty

            <p>No downloadable files available.</p>

        @endforelse

        </div>

    </div>

</section>


@endsection
