@extends('layouts.website')

@section('title', 'Faqs | CLSU DOT-Uni')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/website/homepage.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/website/faqs.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/website/website-pagination.css') }}" />
@endpush

@section('content')

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

                    <div id="faq-list"></div>

                    <div class="website-pagination" id="faq-pagination"></div>

                </div>

            </div>
        </div>
    </section>


@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const faqList = document.getElementById('faq-list');
    const paginationContainer = document.getElementById('faq-pagination');
    let currentPage = 1;

    function fetchFaqs(page = 1) {

        fetch(`{{ route('website.faqData') }}?page=${page}`)
        .then(res => res.json())
        .then(res => {

            window.lastPage = parseInt(res.last_page);
            faqList.innerHTML = '';

            res.data.forEach(faq => {

                let answers = '';

                if(faq.answers.length > 0){

                    faq.answers.forEach(ans => {
                        answers += `<li>${ans.answer}</li>`;
                    });

                }else{

                    answers = `<li class="no-answer">No answer</li>`;

                }

                const html = `
                <div class="faq-item-wrapper">

                    <div class="faq-item">
                        <span>${faq.question}</span>
                    </div>

                    <div class="faq-answer">
                        <ul>${answers}</ul>
                    </div>

                </div>
                `;

                faqList.insertAdjacentHTML('beforeend', html);
            });

            renderPagination(
                parseInt(res.current_page),
                parseInt(res.last_page)
            );
        });
    }

    function renderPagination(current, last) {

        paginationContainer.innerHTML = '';

        const createBtn = (text, page, disabled=false, active=false) => {

            const btn = document.createElement('button');

            btn.className = 'page-btn';
            if(disabled) btn.classList.add('disabled');
            if(active) btn.classList.add('active');

            btn.innerText = text;

            btn.addEventListener('click', () => {

                if(disabled || active) return;
                if(page < 1) return;
                if(page > window.lastPage) return;

                currentPage = page;
                fetchFaqs(page);
            });

            return btn;
        };

        paginationContainer.appendChild(
            createBtn('Prev', current - 1, current === 1)
        );

        for(let i=1;i<=last;i++){
            paginationContainer.appendChild(
                createBtn(i, i, false, current === i)
            );
        }

        paginationContainer.appendChild(
            createBtn('Next', current + 1, current === last)
        );
    }

    fetchFaqs(currentPage);

    // FAQ TOGGLE
    faqList.addEventListener('click', function(e){

        const item = e.target.closest('.faq-item');
        if(!item) return;

        item.classList.toggle('active');

    });

});
</script>
@endpush

