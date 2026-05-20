@extends('master')

@section('styles')
<style>
    @media (max-width: 767px) {
        #hero-banner-container,
        .hero-banner-container {
            min-height: 300px !important;
            height: 300px !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
        }

        #home-text-content h1 {
            text-align: center !important;
        }
    }

    @media (min-width: 768px) {
        #hero-banner-container,
        .hero-banner-container {
            min-height: 570px;
        }

        #home-text-content {
            margin-top: 130px;
        }

        .search-form-wrapper-desktop {
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
        <div id="hero-banner-container"
             class="hero-banner-container py-60 ah-container position-relative py-sm-70 py-md-80 py-lg-100"
             style="z-index: 2; background-image: url('{{ asset($backgroundImage ?? 'assets/new_theme/img/banner-1.webp') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div id="map" class="position-absolute w-100 h-100" style="top:0; left:0; z-index: 1; display:none;"></div>

            <div class="row" style="pointer-events: none;">
                <div id="home-text-content"
                     class="col-12 col-md-6 d-flex flex-column justify-content-center"
                     style="pointer-events: auto; position: relative; z-index: 0;">
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
    </section>
@endsection
