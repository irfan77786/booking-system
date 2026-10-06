@extends('master')

@php
    $heroImage = asset($backgroundImage ?? 'assets/new_theme/img/banner-1.webp');
    $heroImageMobile = asset('assets/new_theme/img/banner-1-800.webp');
@endphp

@section('preload')
<link rel="preload" as="image" href="{{ $heroImageMobile }}" imagesrcset="{{ $heroImageMobile }} 800w, {{ $heroImage }} 1000w" imagesizes="100vw" fetchpriority="high">
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"></noscript>
<link rel="stylesheet" href="{{ asset('assets/css/home-brand.css') }}?v={{ @filemtime(public_path('assets/css/home-brand.css')) }}">
<style>
    body.home-brand .home-banner-section {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
    }

    body.home-brand #hero-banner-container,
    body.home-brand .hero-banner-container {
        width: 100%;
        max-width: none !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        position: relative;
        z-index: 2;
        overflow: hidden;
    }

    body.home-brand .hero-banner-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        z-index: 0;
        pointer-events: none;
    }

    body.home-brand #hero-banner-container::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1;
        pointer-events: none;
    }

    body.home-brand #hero-banner-container > .container {
        position: relative;
        z-index: 2;
    }

    @media (max-width: 767px) {
        body.home-brand #hero-banner-container,
        body.home-brand .hero-banner-container {
            min-height: 300px !important;
            height: 300px !important;
            padding-top: 40px !important;
            padding-bottom: 40px !important;
            display: flex;
            align-items: center;
        }

        body.home-brand #home-text-content {
            text-align: center !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100%;
            margin-top: 0 !important;
        }

        body.home-brand #home-text-content h1 {
            text-align: center !important;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
    }

    @media (min-width: 768px) {
        body.home-brand #hero-banner-container,
        body.home-brand .hero-banner-container {
            min-height: 570px;
            padding-top: 80px;
            padding-bottom: 80px;
        }

        body.home-brand #home-text-content {
            margin-top: 130px;
        }

        body.home-brand .search-form-wrapper-desktop {
            position: absolute;
            width: 100%;
            z-index: 10;
        }
    }
</style>
@endsection

@section('content')
    <section class="d-md-none">
        <div class="ah-container">
            <div class="search-form-mobile">
                @include('partials.search', ['id_suffix' => '_mobile'])
            </div>
        </div>
    </section>

    <section class="home-banner-section">
        <div id="hero-banner-container" class="hero-banner-container position-relative">
            <img class="hero-banner-img"
                 src="{{ $heroImageMobile }}"
                 srcset="{{ $heroImageMobile }} 800w, {{ $heroImage }} 1000w"
                 sizes="100vw"
                 width="1000"
                 height="523"
                 alt=""
                 fetchpriority="high"
                 decoding="async">
            <div id="map" class="position-absolute w-100 h-100" style="top:0; left:0; z-index: 2; display:none;"></div>

            <div class="container">
                <div class="row" style="pointer-events: none;">
                    <div id="home-text-content"
                         class="col-12 col-md-6 d-flex flex-column justify-content-center"
                         style="pointer-events: auto; position: relative; z-index: 2;">
                        <h1 class="text-white h2 fw-bold mb-15">Dallas Black Car Service</h1>
                        <div class="d-none d-md-block">
                            <p class="text-white font-lg fw-medium mb-30">
                                Book reliable black car transportation for airport transfers, corporate travel, and special events across Dallas-Fort Worth.
                            </p>
                            <p class="text-white font-base d-flex align-items-center mb-30 mb-md-0">
                                Call Now:
                                <a href="tel:+14699612047" class="mx-2 fw-bold font-lg theme-color">+1 469-961-2047</a>
                            </p>
                        </div>
                    </div>
                    <div class="d-none col-12 col-md-6 d-md-block" style="pointer-events: auto; position: relative; z-index: 2;">
                        <div class="search-form-wrapper-desktop">
                            @include('partials.search', ['id_suffix' => ''])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.home_sections')
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper === 'undefined') return;

    if (document.querySelector('.logo-swiper')) {
        new Swiper('.logo-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 80,
            centeredSlides: true,
            loop: true,
            speed: 6000,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
            },
            allowTouchMove: false,
            simulateTouch: false,
            preventClicks: false,
            preventClicksPropagation: false,
            breakpoints: {
                992: { spaceBetween: 120 },
                1200: { spaceBetween: 180 }
            }
        });
    }

    if (document.querySelector('.fleet-swiper')) {
        new Swiper('.fleet-swiper', {
            slidesPerView: 1.15,
            spaceBetween: 10,
            grabCursor: true,
            loop: true,
            speed: 650,
            watchOverflow: true,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: '.fleet-swiper .swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.fleet-swiper .swiper-button-next',
                prevEl: '.fleet-swiper .swiper-button-prev',
            },
            breakpoints: {
                576: {
                    slidesPerView: 1.8,
                    spaceBetween: 12
                },
                768: {
                    slidesPerView: 2.4,
                    spaceBetween: 12
                },
                992: {
                    slidesPerView: 3,
                    spaceBetween: 14
                },
                1200: {
                    slidesPerView: 3,
                    spaceBetween: 16
                }
            }
        });
    }

    if (document.querySelector('.testimonial-swiper')) {
        new Swiper('.testimonial-swiper', {
            slidesPerView: 1,
            centeredSlides: true,
            spaceBetween: 24,
            grabCursor: true,
            speed: 1200,
            loop: true,
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.testimonial-swiper .swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.testimonial-swiper .swiper-button-next',
                prevEl: '.testimonial-swiper .swiper-button-prev',
            },
            breakpoints: {
                768: { slidesPerView: 2, centeredSlides: false }
            }
        });
    }
});
</script>
@endsection
