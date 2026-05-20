@extends('master')

@section('content')
<div class="booking-step-page-wrap">
    @include('partials.bookig-top_area')
    @include('partials.product_section')
    {{-- @include('partials.proceed_bar') --}}
</div>
<style>
    @media (max-width: 767.98px) {
        .booking-step-page-wrap {
            overflow-x: hidden;
        }
    }
</style>
@endsection