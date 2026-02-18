@extends('layouts.website')

@section('title', 'About Us | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/website/website-pagination.css') }}" />
@endpush

@section('content')

<section class="courses-section">
    <div class="container">
        <div class="divider"></div>
        <h2 class="section-title">
            The CLSU DOT-Uni Curricular Offerings
        </h2>

        <div class="courses-grid" id="courses-grid">
            <!-- AJAX LOAD -->
        </div>

        <div class="website-pagination" id="courses-pagination"></div>


        {{-- <div class="courses-grid">

            <!-- Course Card -->
            <div class="course-card">
                <img src="{{ asset('storage/programs/programs-2026-02-09-1050178a.jpg') }}" alt="Teaching Course">
            </div>

        </div> --}}
    </div>
</section>

@endsection

@push('js')

<script>

document.addEventListener('DOMContentLoaded', () => {

    const coursesGrid = document.getElementById('courses-grid');
    const paginationContainer = document.getElementById('courses-pagination');

    let currentPage = 1;

    function fetchCourses(page = 1){

        fetch(`{{ route('website.coursesData') }}?page=${page}`)
        .then(res => res.json())
        .then(res => {

            window.lastPage = parseInt(res.last_page);

            coursesGrid.innerHTML = '';

            res.data.forEach(item => {

                const imgSrc = item.image_url
                    ? item.image_url
                    : '/assets/system_images/placeholder.jpg';

                const html = `
                <a href="#" class="course-card">
                    <img src="${imgSrc}" alt="${item.title}">
                </a>
                `;

                coursesGrid.insertAdjacentHTML('beforeend', html);
            });

            renderPagination(
                parseInt(res.current_page),
                parseInt(res.last_page)
            );
        });
    }

    function renderPagination(current, last) {

        paginationContainer.innerHTML = '';

        const createBtn = (text, page, disabled = false, active = false) => {

            const btn = document.createElement('button');

            btn.className = 'page-btn';

            if (disabled) btn.classList.add('disabled');
            if (active) btn.classList.add('active');

            btn.innerText = text;

            btn.addEventListener('click', () => {

                if (disabled || active) return;

                // Prevent going below 1
                if (page < 1) return;

                // Prevent exceeding last page
                if (page > window.lastPage) return;

                currentPage = page;
                fetchCourses(page);
            });

            return btn;
        };

        // PREV
        paginationContainer.appendChild(
            createBtn('Prev', current - 1, current === 1)
        );

        // INDEXED PAGES
        for (let i = 1; i <= last; i++) {
            paginationContainer.appendChild(
                createBtn(i, i, false, current === i)
            );
        }

        // NEXT
        paginationContainer.appendChild(
            createBtn('Next', current + 1, current === last)
        );
    }


    fetchCourses(currentPage);

});

</script>

@endpush