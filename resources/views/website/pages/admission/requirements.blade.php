@extends('layouts.website')

@section('title', 'Admission Requirements | CLSU DOT-Uni')

@push('css')
<style>
    .admission-requirements-section {
        padding: 60px 40px;
    }

    .admission-requirements-section .content {
        max-width: 800px;
        margin: 0 auto;
    }

    .admission-requirements-section .content-title {
        font-size: 18px;
        text-align: center;
    }

    .requirement-category {
        margin-bottom: 30px;
    }

    .requirement-category h4 {
        font-weight: bold;
        text-align: left;
        border-left: 3px solid #E8D203;
        padding-left: 8px;
        margin-bottom: 10px;
    }

    .requirement-category ul {
        padding-left: 20px;
        list-style-type: disc;
    }
</style>
@endpush

@section('content')

<section class="admission-requirements-section">
    <div class="container">
        <div class="divider"></div>
        <h2 class="section-title">
            INFORMATION FOR PROSPECTIVE STUDENTS
        </h2>

        <div class="content">
            @forelse($categories as $category)
                <div class="requirement-category" id="{{ $category->name }}">
                    <h4>{{ $category->name ?? 'No Category Name' }}</h4>

                    @if($category->items->count())
                        <ul>
                            @foreach($category->items as $item)
                                <li>{{ $item->content }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p style="color:#999;">No requirements added yet.</p>
                    @endif
                </div>
            @empty
                <p style="text-align:center; color:#999;">
                    No admission requirements uploaded yet.
                </p>
            @endforelse
        </div>

    </div>
</section>

@endsection
