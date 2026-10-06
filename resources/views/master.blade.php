<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $seo['title'] ?? 'Book a Ride' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Book your black car or limousine ride online.' }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/site/dallas-black-car-service-favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    @yield('preload')

    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"></noscript>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}">
    @unless (request()->is('/'))
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" media="print" onload="this.media='all'" crossorigin="anonymous" referrerpolicy="no-referrer">
        <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"></noscript>
    @endunless
    @if (request()->is('user-login') || request()->is('user-login/*'))
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/css/intlTelInput.min.css" media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/css/intlTelInput.min.css"></noscript>
    @endif

    @yield('styles')
    @if (request()->is('booking/*') || request()->is('user-login/*') || request()->is('bookRide') || request()->is('passengerInfo') || request()->is('submit-passengerInfo') || request()->is('submit-passengerInfo/*') || request()->is('thank-you'))
        <link rel="stylesheet" href="{{ asset('assets/css/home-brand.css') }}">
    @endif
</head>

<body class="{{ trim((request()->is('/') ? 'home-brand' : '') . ' ' . ((request()->is('booking/*') || request()->is('user-login/*') || request()->is('bookRide') || request()->is('passengerInfo') || request()->is('submit-passengerInfo') || request()->is('submit-passengerInfo/*') || request()->is('thank-you')) ? 'booking-steps' : '')) }}">
    @if (request()->is('/'))
    <header class="py-15 py-lg-20 brand-header">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-6 col-md-3">
                    <div class="logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('assets/img/site/black-car-service-dallas-logo.webp') }}" width="316" height="72"
                                alt="Dallas Black Cars Limo Service" class="img-fluid brand-logo" decoding="async">
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-9">
                    <nav class="custom-navbar navbar navbar-expand-lg p-0 position-static">
                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 18L20 18" stroke="#f3ead7" stroke-width="2" stroke-linecap="round" />
                                <path d="M4 12L20 12" stroke="#f3ead7" stroke-width="2" stroke-linecap="round" />
                                <path d="M4 6L20 6" stroke="#f3ead7" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                        <div class="collapse navbar-collapse ms-auto" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-semibold">
                                <li class="nav-item">
                                    <a class="nav-link active" href="{{ url('/') }}">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">About us</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Our Service
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="#">Airport Transfers</a></li>
                                        <li><a class="dropdown-item" href="#">Chauffeur Service</a></li>
                                        <li><a class="dropdown-item" href="#">Corporate Transportation</a></li>
                                        <li><a class="dropdown-item" href="#">Executive shuttle services</a></li>
                                        <li><a class="dropdown-item" href="#">Luxury van rental</a></li>
                                        <li><a class="dropdown-item" href="#">Private car service</a></li>
                                        <li><a class="dropdown-item" href="#">Private Aviation/FBO</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Book Now</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Our Fleet</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownHelp" role="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Help
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownHelp">
                                        <li><a class="dropdown-item" href="#">Get a quote</a></li>
                                        <li><a class="dropdown-item" href="#">Contact us</a></li>
                                        <li><a class="dropdown-item" href="#">FAQs</a></li>
                                        <li><a class="dropdown-item" href="#">Terms &amp; Conditions</a></li>
                                        <li><a class="dropdown-item" href="#">Privacy Policy</a></li>
                                        <li><a class="dropdown-item" href="#">Cancellation Policy</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    @endif
    <main>
        @yield('content')
    </main>
    @if (request()->is('/'))
    <footer class="footer bg-blue">
        <div class="pt-40 pb-10">
            <div class="container">
                <div class="row footer-nav-list">
                    <div class="col-12">
                        <h4 class="h6 fw-bold mb-10 mb-md-15 text-white">Company</h4>
                        <ul class="footer-nav-list-item list-unstyled mb-30 mb-lg-0">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="#">About us</a></li>
                            <li><a href="#">Book Now</a></li>
                            <li><a href="#">Contact us</a></li>
                            <li><a href="#">Our Fleet</a></li>
                            <li><a href="#">Get A Quote</a></li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <h4 class="h6 fw-bold mb-10 mb-md-15 text-white">Our Service</h4>
                        <ul class="footer-nav-list-item list-unstyled mb-30 mb-lg-0">
                            <li><a href="#">Airport Transfers</a></li>
                            <li><a href="#">Chauffeur Service</a></li>
                            <li><a href="#">Private car service</a></li>
                            <li><a href="#">Luxury Sprinter Service</a></li>
                            <li><a href="#">City-to-city-rides</a></li>
                            <li><a href="#">Limousine service</a></li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <h4 class="h6 fw-bold mb-10 mb-md-15 text-white">Top Cities</h4>
                        <ul class="footer-nav-list-item list-unstyled mb-30 mb-lg-0">
                            <li><a href="#">Allen</a></li>
                            <li><a href="{{ url('/') }}">Dallas</a></li>
                            <li><a href="#">Fort Worth</a></li>
                            <li><a href="#">Frisco</a></li>
                            <li><a href="#">College Station</a></li>
                            <li><a href="#">OKC</a></li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <h4 class="h6 fw-bold mb-10 mb-md-15 text-white">City-to-City Rides</h4>
                        <ul class="footer-nav-list-item list-unstyled mb-30 mb-lg-0">
                            <li><a href="#">Dallas - Austin</a></li>
                            <li><a href="#">Dallas - Houston</a></li>
                            <li><a href="#">Dallas - College Station</a></li>
                            <li><a href="#">Dallas - OKC</a></li>
                            <li><a href="#">Dallas - Tyler</a></li>
                            <li><a href="#">DFW - Waco</a></li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <h4 class="h6 fw-bold mb-10 mb-md-15 text-white">Airports</h4>
                        <ul class="footer-nav-list-item list-unstyled mb-30 mb-lg-0">
                            <li><a href="#">Addison Airport (ADS)</a></li>
                            <li><a href="#">Dallas/Fort Worth Airport (DFW)</a></li>
                            <li><a href="#">Dallas Love Field Airport (DAL)</a></li>
                            <li><a href="#">Dallas Executive Airport (RBD)</a></li>
                            <li><a href="#">Signature Flight Support (DAL)</a></li>
                            <li><a href="#">Waco Regional Airport (ACT)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="t-policy">
            <div class="container py-20 c-policy">
                <a href="#" class="last-p">Cancellation Policy</a>
                <a href="#"> - Terms &amp; Conditions</a>
                <a href="#"> - Privacy Policy</a>
            </div>
        </div>
        <div class="footer-area">
            <div class="container py-20">
                <div class="row align-items-center flex-row-reverse">
                    <div class="col-12 text-center">
                        <p class="font-sm mb-0"><a href="{{ url('/') }}">Dallas Black Limo Service</a> &copy; {{ date('Y') }}. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    @else
    <footer class="footer bg-blue py-20">
        <div class="container text-center">
            <p class="font-sm mb-0 text-white">
                <a href="{{ route('booking') }}" class="text-white text-decoration-none">Book a Ride</a>
                &copy; {{ date('Y') }}. All rights reserved.
            </p>
        </div>
    </footer>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/js/bootstrap-min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}?v={{ filemtime(public_path('assets/js/custom.js')) }}"></script>
    @if (request()->is('user-login') || request()->is('user-login/*'))
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.19/build/js/intlTelInput.min.js" defer></script>
    @endif
    <script>
        (function () {
            function injectMaps() {
                if (window.__gmapsStarted) return;
                if (typeof window.initAutocomplete !== 'function') {
                    setTimeout(injectMaps, 50);
                    return;
                }
                window.__gmapsStarted = true;
                var script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initAutocomplete&loading=async';
                script.async = true;
                document.head.appendChild(script);
            }
            window.loadGoogleMaps = function () {
                window.__gmapsQueued = true;
                injectMaps();
            };
            var nodes = document.querySelectorAll('input[id^="pickup-location"], input[id^="dropoff-location"], input#pickup_address, input#dropoff_address');
            if (!nodes.length) return;
            var prefilled = false;
            nodes.forEach(function (el) {
                if ((el.value || '').trim()) prefilled = true;
                el.addEventListener('focus', window.loadGoogleMaps, { once: true });
                el.addEventListener('pointerdown', window.loadGoogleMaps, { once: true });
            });
            if (prefilled) window.loadGoogleMaps();
        })();
    </script>
    @yield('scripts')
</body>

</html>
