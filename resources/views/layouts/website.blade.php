<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'CLSU DOT-Uni')</title>

    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/cvsm-logo.png') }}" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/website/main.css') }}" />

    @stack('css')
</head>
<body>

    {{-- PAGE HEADER BAR --}}
    @include('website.partials.header')


    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>


    {{-- FOOTER --}}
    @include('website.partials.footer')




    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Scroll to Top Script --}}
    <script>
        window.addEventListener("scroll", function() {
            const btn = document.querySelector(".scroll-top-btn");
            if (window.scrollY > 300) {
                btn.style.opacity = "1";
                btn.style.pointerEvents = "auto";
            } else {
                btn.style.opacity = "0";
                btn.style.pointerEvents = "none";
            }
        });

        function scrollToTop() {
            const start = window.scrollY;
            const duration = 1000;
            let startTime = null;

            function animation(currentTime) {
                if (!startTime) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);
                const ease = progress < 0.5
                    ? 4 * progress * progress * progress
                    : 1 - Math.pow(-2 * progress + 2, 3) / 2;
                window.scrollTo(0, start * (1 - ease));
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }

            requestAnimationFrame(animation);
        }
    </script>


    {{-- SCROLL TOP BUTTON --}}
    <button class="scroll-top-btn" onclick="scrollToTop()">
        <i class="fa-solid fa-chevron-up"></i>  
    </button>

    {{-- DoDOT AI Chatbot --}}
    @include('website.partials.DoDOT.dodot')


    @stack('js')
</body>
</html>
