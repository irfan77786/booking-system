@extends('master')
@section('content')
    @php
        $step = 4;
    @endphp

    <style>
    .floating-bordered-input {
        position: relative;
        border: 1px solid #C4C4C4;
        border-radius: 4px;
        padding: 12px 15px !important;
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        background: #fff;
    }
    .custom-switch-container{display:flex;align-items:center;gap:10px}
    .switch-wrapper{position:relative;display:inline-block;width:44px;height:24px}
    .switch-wrapper input{opacity:0;width:0;height:0}
    .switch-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#bdbdbd;transition:.2s;border-radius:24px}
    .switch-slider:before{position:absolute;content:"";height:20px;width:20px;left:2px;top:2px;background:#fff;transition:.2s;border-radius:50%}
    .switch-wrapper input:checked + .switch-slider{background:linear-gradient(90deg, #e52c43, #ff6c00)}
    .switch-wrapper input:checked + .switch-slider:before{transform:translateX(20px)}

    #meet-option:invalid,
    #meet-option option[value="none"]:checked {
        color: #999;
    }

    /* Ensure the actual options inside look normal */
    #meet-option option {
        color: #000;
        background-color: #fff;
    }

    /* Ridelux-style custom select */
    .rlx-select {
        position: relative;
        width: 100%;
    }
    
    .container, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
        max-width: 1171px;
    }
 


    .rlx-select .rlx-trigger {
        display: block;
        width: 100%;
        background: transparent;
        border: none;
        border-radius: 0;
        padding: 0 40px 0 0; /* No left icon here */
        margin-top: 0; /* Removed default margin */
        color: #1f2937;
        font-size: 16px;
        line-height: 1.45;
        min-height: 24px;
        text-align: left;
    }

    /* Bordered variant for non-floating contexts */
    .rlx-select.bordered {
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .rlx-select.bordered .rlx-trigger {
        padding: 10px 40px 10px 12px; /* Standard padding */
        min-height: auto; /* Let padding define height or standard input height */
    }
    @media (min-width: 768px) {
        .rlx-select.bordered .rlx-trigger {
            min-height: auto !important; /* Override the desktop min-height for floating inputs */
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }
  .px-3.py-2.bg-white.d-flex.justify-content-between.align-items-center {
    background-color: #fff !important;
}

    .d-md-none.mb-3 {
        margin-bottom: 9px !important;
        margin-top: 10px !important;
 
}

    
    
 

    }

 
    
    
    .rlx-select .rlx-value {
        pointer-events: none;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rlx-select .rlx-arrow {
        color: #6b7280;
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .rlx-select.open .rlx-arrow {
        transform: rotate(180deg);
        transition: transform .18s ease;
    }
    .rlx-select .rlx-list,
    .rlx-select .rlx-list-wrapper {
        position: absolute;
        top: calc(100% + 6px);
        left: -16px;
        right: -16px;
        background: #fff;
        border: 1px solid #e6eaef;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(0,0,0,.12);
        max-height: 360px;
        overflow: hidden;
        opacity: 0;
        transform: translateY(-6px);
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease;
        z-index: 1050;
        display: flex;
        flex-direction: column;
    }
    .rlx-select > .rlx-list {
        overflow-y: auto;
        padding: 6px 0;
        overscroll-behavior: contain;
    }
    .rlx-select .rlx-list-wrapper .rlx-list {
        position: relative;
        max-height: 300px;
        overflow-y: auto;
        padding: 6px 0;
        overscroll-behavior: contain;
    }
    .rlx-select .rlx-search {
        flex-shrink: 0;
        width: 100%;
        padding: 10px 12px;
        border: none;
        border-bottom: 1px solid #e6eaef;
        border-radius: 8px 8px 0 0;
        font-size: 15px;
        outline: none;
        box-sizing: border-box;
    }
    .rlx-select .rlx-search::placeholder {
        color: #9ca3af;
    }
    .rlx-select.open .rlx-list,
    .rlx-select.open .rlx-list-wrapper {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    .rlx-option {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        font-size: 16px;
        line-height: 1.5;
        color: #1f2937;
        cursor: pointer;
    }
    .rlx-option-icon {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        margin-right: 10px;
        color: #6b7280;
    }
    .rlx-option-icon svg {
        width: 100%;
        height: 100%;
        display: block;
    }
    .rlx-option:hover .rlx-option-icon,
    .rlx-option[aria-selected="true"] .rlx-option-icon,
    .rlx-option.selected .rlx-option-icon {
        color: #0ea5e9;
    }
    .rlx-option:hover,
    .rlx-option[aria-selected="true"] {
        background: #eef6fb;
    }
    .rlx-option.selected {
        background: #e9f2fa;
    }
    .rlx-theme {
        border-radius: 4px;
    }
    .rlx-theme:hover,
    .rlx-theme:focus-within {
        box-shadow: 0 6px 16px rgba(0,0,0,.06);
    }
    /* Scrollbar styling for dropdown list */
    .rlx-list::-webkit-scrollbar {
        width: 8px;
    }
    .rlx-list::-webkit-scrollbar-track {
        background: #f1f3f5;
        border-radius: 4px;
    }
    .rlx-list::-webkit-scrollbar-thumb {
        background: #c8d1db;
        border-radius: 8px;
    }
    .rlx-list::-webkit-scrollbar-thumb:hover {
        background: #b4c0cc;
    }

    @media (max-width: 480px) {
        .rlx-select .rlx-trigger {
            margin-top: 10px !important;
            font-size: 15px;
            min-height: 42px;
            padding-left: 0;
        }
        .rlx-option {
            font-size: 15px;
            padding: 10px 14px;
        }
        .additional-info-heading {
            font-size: 20px;
        }
    }

    /* Hide native date/time picker icon (2nd icon) */
    input[type="date"]::-webkit-calendar-picker-indicator,
    input[type="time"]::-webkit-calendar-picker-indicator {
        display: none !important;
        -webkit-appearance: none;
    }
    
    
    
    @media (min-width: 768px) {
        .rlx-select .rlx-trigger {
            min-height: 54px !important;
            padding-top: 0px !important;
            padding-bottom: 0px !important;
            padding-left: 0 !important;
            display: flex;
            align-items: center;
        }
    


    }
    
    
       @media (min-width: 768px) {
        .rlx-select.bordered .rlx-trigger {
            min-height: auto !important; /* Override the desktop min-height for floating inputs */
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }
        

  

    }
    
   
    
    </style>

    @include('partials.bookig-top_area')

    <?php $is_airport = session('is_airport'); ?>

    <div class="container py-md-5">
        <div class="row">
            <div class="px-4 mb-3 col-md-8 mobile-mg-dc">
                <form method="POST" action="/bookRide" class="d-flex flex-column loader-form" id="booking-detail-form">
                    @csrf

                    <input type="hidden" name="vehicle_id" id="hidden-vehicle-id" value="{{ session('vehicle_id') }}">
                    <input type="hidden" name="return_service" id="hidden-return-service"
                        value="{{ session('return_service', 0) }}">
                    <input type="hidden" name="return_pickup_location" id="hidden-return-pickup-location"
                        value="{{ session('return_pickup_location') }}">
                    <input type="hidden" name="return_dropoff_location" id="hidden-return-dropoff-location"
                        value="{{ session('return_dropoff_location') }}">
                    <input type="hidden" name="return_pickup_date" id="hidden-return-pickup-date"
                        value="{{ session('return_pickup_date') }}">
                    <input type="hidden" name="return_pickup_time" id="hidden-return-pickup-time"
                        value="{{ session('return_pickup_time') }}">
                    <input type="hidden" name="return_flight_number" id="hidden-return-flight-number"
                        value="{{ session('return_flight_number') }}">
                    <input type="hidden" name="return_flight_details" id="hidden-return-flight-details"
                        value="{{ session('return_flight_details') }}">
                    <input type="hidden" name="return_no_flight_info" id="hidden-return-no-flight-info"
                        value="{{ session('return_no_flight_info', 0) }}">

                    <div class="mb-4" id="outbound-flight-info-section">
                        <div id="outbound-flight-fields" style="display:none;">
                            <h2 class="mb-3">Flight Information</h2>

                            <!-- Pickup Flight Details -->
                            <div class="mb-3 floating-bordered-input position-relative rlx-theme">
                                <span class="floating-label">Pickup Flight Details</span>
                                <div class="rlx-select rlx-select-searchable" id="rlx-pickup-flight" data-name="pickup_flight_details" data-initial="{{ session('pickup_flight_details') ?? '' }}">
                                    <button type="button" class="rlx-trigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="rlx-value">{{ session('pickup_flight_details') ? session('pickup_flight_details') : 'Select Airline' }}</span>
                                        <svg class="rlx-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.73 9.27a1 1 0 0 1 1.41 0L12 13.12l3.86-3.85a1 1 0 0 1 1.41 1.41l-4.57 4.57a1 1 0 0 1-1.41 0L6.73 10.68a1 1 0 0 1 0-1.41Z" fill="currentColor"/></svg>
                                    </button>
                                    <div class="rlx-list-wrapper">
                                        <input type="text" class="rlx-search" placeholder="Type to search airlines..." autocomplete="off" aria-label="Search airlines" />
                                        <ul class="rlx-list" role="listbox" tabindex="-1">
                                            <li role="option" class="rlx-option {{ session('pickup_flight_details') == '' ? 'selected' : '' }}" aria-selected="{{ session('pickup_flight_details') == '' ? 'true' : 'false' }}" data-value="">
                                                <span class="rlx-option-icon rlx-option-icon--flight" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg></span>
                                                <span class="rlx-option-text">Select Airline</span>
                                            </li>
                                            @foreach($airports as $airport)
                                                @php
                                                    $displayValue = ($airport->iata_code ? $airport->iata_code . ' - ' : '') . $airport->name . ($airport->city ? ' (' . $airport->city . ')' : '');
                                                @endphp
                                                <li role="option"
                                                    class="rlx-option {{ session('pickup_flight_details') == $displayValue ? 'selected' : '' }}"
                                                    aria-selected="{{ session('pickup_flight_details') == $displayValue ? 'true' : 'false' }}"
                                                    data-value="{{ $displayValue }}">
                                                    <span class="rlx-option-icon rlx-option-icon--flight" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg></span>
                                                    <span class="rlx-option-text">{{ $displayValue }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <input type="hidden" name="pickup_flight_details" id="pickup-flight-details" value="{{ session('pickup_flight_details') ?? '' }}">
                                </div>
                            </div>
                            <!-- Flight Number -->
                            <div class="mb-3 floating-bordered-input position-relative">
                            <span class="floating-label">Flight Number</span>
                            <input type="text" id="flight-number" name="flight_number"
                                class="form-control" placeholder=" "
                                value="{{ session('flight_number') ?? '' }}">
                            </div>
                            <!-- Meet Option -->
                            <div class="mb-3 floating-bordered-input position-relative rlx-theme">
                                <span class="floating-label">Meet Option</span>
                                <div class="rlx-select" id="rlx-meet-option" data-name="meet_option" data-initial="{{ session('meet_option') ?? 'none' }}">
                                    <button type="button" class="rlx-trigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="rlx-value">
                                            @if(session('meet_option') === 'curbside') Curbside Pickup
                                            @elseif(session('meet_option') === 'inside') Inside Pickup
                                            @else Select Option
                                            @endif
                                        </span>
                                        <svg class="rlx-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.73 9.27a1 1 0 0 1 1.41 0L12 13.12l3.86-3.85a1 1 0 0 1 1.41 1.41l-4.57 4.57a1 1 0 0 1-1.41 0L6.73 10.68a1 1 0 0 1 0-1.41Z" fill="currentColor"/></svg>
                                    </button>
                                    <ul class="rlx-list" role="listbox" tabindex="-1">
                                        <li role="option" class="rlx-option {{ session('meet_option') === 'none' || session('meet_option') === null ? 'selected' : '' }}" aria-selected="{{ session('meet_option') === 'none' || session('meet_option') === null ? 'true' : 'false' }}" data-value="none">Select Option</li>
                                        <li role="option" class="rlx-option {{ session('meet_option') === 'curbside' ? 'selected' : '' }}" aria-selected="{{ session('meet_option') === 'curbside' ? 'true' : 'false' }}" data-value="curbside">Curbside Pickup</li>
                                        <li role="option" class="rlx-option {{ session('meet_option') === 'inside' ? 'selected' : '' }}" aria-selected="{{ session('meet_option') === 'inside' ? 'true' : 'false' }}" data-value="inside">Inside Pickup</li>
                                    </ul>
                                    <input type="hidden" name="meet_option" id="meet-option" value="{{ session('meet_option') ?? 'none' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Inside Pickup Fee (Hidden) -->
                        <input type="hidden" name="inside_pickup_fee" id="inside-pickup-fee"
                            value="{{ session('inside_pickup_fee') ?? 0 }}">

                        <!-- Flight Info Toggle -->
                        <div class="mt-3 custom-switch-container">
                            <label class="switch-wrapper">
                                <input type="checkbox" id="no-flight-info-checkbox" name="no_flight_info" value="1" {{ (session('no_flight_info', 0) == 1) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </label>
                            <label class="form-check-label" for="no-flight-info-checkbox">
                                I have my flight details
                            </label>
                        </div>
                    </div>

                    <div>
                        <h2 class="mb-3 additional-info-heading">Additional Information (Optional)</h2>

                        <div class="mb-3 floating-bordered-input position-relative">
                            <input id="note" name="note" class="form-control" placeholder=" " rows="2">{{ session('note') ?? '' }}</input>
                        </div>
                    </div>

                    <p class="mt-1 mb-0 text-muted small text-start" style="line-height: 1.2rem;">Enter any special
                        requests or important information for your ride, e.g. child car seats, etc.</p>
                    <div class="mt-4 d-none d-md-flex align-items-center">
                        <button type="submit" class="mr-3 btn btn-outline btn-uniform skip-btn">SKIP</button>
                        <button type="submit" class="btn btn-primary btn-uniform flex-fill">CONTINUE TO PAYMENT</button>
                    </div>
            </div>

            <!-- Right Form Container (summary panel remains unchanged) -->
            @include('booking.right_side_pricing_area')
        </div>
    </div>
    </form>
    <!-- Return Reservation Modal -->
    <div class="modal fade" id="returnReservationModal" tabindex="-1" role="dialog"
        aria-labelledby="returnReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-2 modal-title font-weight-bold" id="returnReservationModalLabel">Return Reservation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 2rem;">&times;</span>
                    </button>
                </div>
                <form id="return-service-form" method="POST" action="/save-return-service"
                    class="loader-form">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6" style="position:relative;">
                                <div id="modal-map"
                                    style="height: 500px; width: 100%; border-radius: 15px; background-color: #f0f0f0;">
                                </div>
                                <div id="route-info-box"
                                    style="
                  position: absolute;
                  bottom: 20px;
                  left: 20px;
                  background: white;
                  color: black;
                  padding: 12px 16px;
                  border-radius: 10px;
                  box-shadow: 0 0 10px rgba(0,0,0,0.2);
                  font-size: 14px;
                  z-index: 999;
                  display: none;
                ">
                                    <div><strong>Distance:</strong> <span id="route-distance">-</span></div>
                                    <div><strong>Duration:</strong> <span id="route-duration">-</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="is_airport_return" id="is-airport_return" value="0">
                                <input type="hidden" name="return_vehicle_id" id="return-vehicle-id" value="">

                                <!-- Pick-up Location -->
                                <div class="mb-3 input-group-container">
                                    <div class="icon-container">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="input-text-container">
                                        <label for="return-pickup-location" class="form-label">From</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                value="{{ session('return_pickup_location') }}"
                                                name="return_pickup_location" id="return-pickup-location"
                                                placeholder="Address, Airport, Hotel..." onfocus="geolocate()" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Drop-off Location -->
                                <div class="mb-3 input-group-container">
                                    <div class="icon-container">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="input-text-container">
                                        <label for="return-dropoff-location" class="form-label">To</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="return_dropoff_location"
                                                id="return-dropoff-location"
                                                value="{{ session('return_dropoff_location') }}"
                                                placeholder="Address, Airport, Hotel..." onfocus="geolocate()" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pickup Date and Time -->
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="mb-3 input-group-container">
                                            <div class="icon-container">
                                                <i class="bi bi-calendar"></i>
                                            </div>
                                            <div class="input-text-container">
                                                <label for="return-pickup-date" class="form-label">Date</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control" name="return_pickup_date"
                                                        id="return-pickup-date"
                                                        value="{{ session('return_pickup_date', '') }}"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3 input-group-container">
                                            <div class="icon-container">
                                                <i class="bi bi-clock"></i>
                                            </div>
                                            <div class="input-text-container">
                                                <label for="return-pickup-time" class="form-label">Time</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control" name="return_pickup_time"
                                                        id="return-pickup-time" value="{{ session('return_pickup_time') }}"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($is_airport)
                                    <div class="mb-4" id="flight-info-section">
                                        <h2 class="mb-3">Return Flight Information</h2>

                                        <div class="row">
                                            <!-- Flight Details -->
                                            <div class="col-md-6 form-group">
                                                <div class="input-group-container">
                                                    <div class="icon-container">
                                                        <i class="bi bi-airplane"></i>
                                                    </div>
                                                    <div class="input-text-container">
                                                        <label for="return-flight-details" class="form-label">Pickup
                                                            Flight Details (Recommended)</label>
                                                        <div class="input-group">
                                                        <div class="rlx-select bordered" id="rlx-return-flight" data-name="return_flight_details" data-initial="{{ session('return_flight_details') ?? '' }}">
                                                            <button type="button" class="rlx-trigger" aria-haspopup="listbox" aria-expanded="false">
                                                                <span class="rlx-value">{{ session('return_flight_details') ? session('return_flight_details') : 'Select Airline' }}</span>
                                                                <svg class="rlx-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.73 9.27a1 1 0 0 1 1.41 0L12 13.12l3.86-3.85a1 1 0 0 1 1.41 1.41l-4.57 4.57a1 1 0 0 1-1.41 0L6.73 10.68a1 1 0 0 1 0-1.41Z" fill="currentColor"/></svg>
                                                            </button>
                                                            <ul class="rlx-list" role="listbox" tabindex="-1">
                                                                <li role="option" class="rlx-option {{ session('return_flight_details') == '' ? 'selected' : '' }}" aria-selected="{{ session('return_flight_details') == '' ? 'true' : 'false' }}" data-value="">
                                                                    <span class="rlx-option-icon rlx-option-icon--flight" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg></span>
                                                                    <span class="rlx-option-text">Select Airline</span>
                                                                </li>
                                                                @foreach($airports as $airport)
                                                                    @php
                                                                        $displayValue = ($airport->iata_code ? $airport->iata_code . ' - ' : '') . $airport->name . ($airport->city ? ' (' . $airport->city . ')' : '');
                                                                    @endphp
                                                                    <li role="option"
                                                                        class="rlx-option {{ session('return_flight_details') == $displayValue ? 'selected' : '' }}"
                                                                        aria-selected="{{ session('return_flight_details') == $displayValue ? 'true' : 'false' }}"
                                                                        data-value="{{ $displayValue }}">
                                                                        <span class="rlx-option-icon rlx-option-icon--flight" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg></span>
                                                                        <span class="rlx-option-text">{{ $displayValue }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                            <input type="hidden" name="return_flight_details" id="return-flight-details" value="{{ session('return_flight_details') ?? '' }}">
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Flight Number -->
                                            <div class="col-md-6 form-group">
                                                <div class="input-group-container">
                                                    <div class="icon-container">
                                                        <i class="bi bi-123"></i>
                                                    </div>
                                                    <div class="input-text-container">
                                                        <label for="return-flight-number" class="form-label">Flight
                                                            Number</label>
                                                        <div class="input-group">
                                                            <input type="text" id="return-flight-number"
                                                                name="return_flight_number"
                                                                class="form-control custom-input-style"
                                                                placeholder="Enter flight number"
                                                                value="{{ session('return_flight_number') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- No Flight Info Checkbox -->
                                        <div class="pl-5 mt-3 form-check d-flex">
                                            <input type="checkbox" class="form-check-input" id="return-no-flight-info"
                                                {{ session('return_no_flight_info') == 1 ? 'checked' : '' }}
                                                name="return_no_flight_info" value="1" style="position:static">
                                            <label class="form-check-label" for="return-no-flight-info">
                                                I do not have my flight details
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                <!-- Vehicle Selection Button -->
                                <button type="button" class="btn btn-primary btn-sm w-100 toggle_vehicleSelect">SELECT
                                    VEHICLE</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mt-4 bg-white wrapper vehicle_container hide col-md-12">
                                @foreach ($vehicles_all as $key => $value)
                                    <div class="row no-gutters">
                                        <div class="col-12">
                                            <div class="p-3 mb-3 text-left vehical-card row text-md-left align-items-center justify-content-center border-bottom"
                                                style="border-color: #8b8b8b;">

                                                <!-- Vehicle Image and Capacity Info -->
                                                <div class="mb-3 col-12 col-md-4 d-flex flex-column align-items-center">
                                                    <img src="{{ 'https://admin.dallasblacklimoservice.com/storage/' . $value->vehicle_image }}"
                                                        alt="Vehicle Image" class="img-fluid rounded-3 vehicle_img"
                                                        style="max-height: 200px; object-fit: cover;">

                                                    <div
                                                        class="mt-3 row justify-content-md-center justify-content-start w-100">
                                                        <div
                                                            class="mb-2 text-left col-4 col-md-6 text-md-center d-flex align-items-md-center justify-content-md-center align-items-start justify-content-start">
                                                            <img src="/image/user.svg" alt="Passengers" class="mr-2"
                                                                style="height:20px;width:20px;">
                                                            <p class="mb-0 small">Max. {{ $value->number_of_passengers }}
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="mb-2 text-left col-4 col-md-6 d-flex text-md-center align-items-md-center justify-content-md-center align-items-start justify-content-start">
                                                            <img src="/image/bag.svg" alt="Luggage" class="mr-2"
                                                                style="height:20px;width:20px;">
                                                            <p class="mb-0 small">Max. {{ $value->luggage_capacity }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Vehicle Details -->
                                                <div class="px-2 mb-3 col-12 col-md-4">
                                                    <h5 class="text-left font-weight-bold text-md-left">
                                                        {{ $value->vehicle_name }}</h5>
                                                    <div
                                                        class="d-flex flex-column align-items-start align-items-md-start feature_items_cont">
                                                        @isset($features)
                                                            @foreach ($features as $feature)
                                                                    <div class="feature-item">
                                                                        <i class="bi {{ $feature['icon'] }} feature-icon"></i>
                                                                        <span class="feature-text">
                                                                            {{ $feature['text'] }}


                                                                    </span>
                                                                    <span>
                                                                        @if (isset($feature['tooltip']))
                                                                            <i class="bi bi-info-circle info-icon"
                                                                                data-tooltip="{{ $feature['tooltip'] }}"></i>
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        @endisset
                                                    </div>
                                                </div>

                                                <!-- Pricing & CTA -->
                                                <div
                                                    class="mb-2 text-left col-12 col-md-4 d-flex flex-column align-items-start align-items-md-end text-md-right">
                                                    @php $vehicleDistance = $distance[$value->id] ?? null; @endphp

                                                    @if ($vehicleDistance && empty($vehicleDistance['error']))
                                                        <div class="car-price font-weight-bold">
                                                            <h4 class="mb-1">
                                                                ${{ number_format($vehicleDistance['price'], 2) }}</h4>
                                                            <br><small>Total Distance:
                                                                {{ number_format($vehicleDistance['distance_km'], 2) }}
                                                                Miles</small>
                                                        </div>
                                                        <div class="mb-2">
                                                            <small class="text-muted font-weight-bold">Includes base fare,
                                                                gratuity & tax</small><br>
                                                            <small class="text-muted">No hidden costs.</small>
                                                        </div>
                                                        <a href="javascript:void(0)"
                                                            data-vehicle-id="{{ $value->id }}"
                                                            class="mt-2 select-vehicle btn btn-primary btn_dark trigger-loader">
                                                            SELECT
                                                        </a>
                                                    @else
                                                        <div class="text-danger font-weight-bold">Fare calculation failed
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="save-return-service">Save Return
                            Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('scripts')
    @include('booking.return_logic')
    <script>
    (function(){
      function initRlxSelect(rootId) {
        const root = document.getElementById(rootId);
        if (!root) return;
        const trigger = root.querySelector('.rlx-trigger');
        const list = root.querySelector('.rlx-list');
        const valueEl = root.querySelector('.rlx-value');
        const hidden = root.querySelector('input[type="hidden"]');
        const searchInput = root.querySelector('.rlx-search');
        const options = Array.from(root.querySelectorAll('.rlx-option'));
        let highlightedIndex = options.findIndex(o => o.classList.contains('selected'));

        function getVisibleOptions() {
          if (!searchInput) return options;
          return options.filter(o => o.style.display !== 'none');
        }
        function getVisibleIndex(opt) {
          const visible = getVisibleOptions();
          const idx = visible.indexOf(opt);
          return idx >= 0 ? idx : 0;
        }
        function getOptionIndex(visibleIndex) {
          const visible = getVisibleOptions();
          const opt = visible[visibleIndex];
          return opt ? options.indexOf(opt) : 0;
        }

        function filterOptions(q) {
          const query = (q || '').toLowerCase().trim();
          options.forEach(o => {
            const text = (o.getAttribute('data-value') || o.textContent || '').toLowerCase();
            o.style.display = query === '' || text.indexOf(query) >= 0 ? '' : 'none';
          });
          const visible = getVisibleOptions();
          highlightedIndex = visible.length ? Math.min(highlightedIndex, visible.length - 1) : 0;
          highlight(highlightedIndex);
        }

        function open() {
          root.classList.add('open');
          trigger.setAttribute('aria-expanded', 'true');
          if (searchInput) {
            searchInput.value = '';
            filterOptions('');
            const selectedOpt = options.find(o => o.classList.contains('selected'));
            highlightedIndex = selectedOpt ? getVisibleIndex(selectedOpt) : 0;
            if (highlightedIndex < 0) highlightedIndex = 0;
            searchInput.focus({ preventScroll: true });
          } else {
            list.focus({ preventScroll: true });
            if (highlightedIndex < 0) highlightedIndex = 0;
          }
          highlight(highlightedIndex);
        }
        function close() {
          root.classList.remove('open');
          trigger.setAttribute('aria-expanded', 'false');
          if (searchInput) searchInput.value = '';
        }
        function selectByIndex(visibleIdx) {
          const visible = getVisibleOptions();
          const opt = visible[visibleIdx];
          if (!opt) return;
          const idx = options.indexOf(opt);
          options.forEach(o => { o.classList.remove('selected'); o.setAttribute('aria-selected','false'); });
          opt.classList.add('selected');
          opt.setAttribute('aria-selected','true');
          highlightedIndex = visibleIdx;
          const val = opt.getAttribute('data-value');
          hidden.value = val;
          valueEl.textContent = opt.textContent.trim();
          const event = new Event('change', { bubbles: true });
          hidden.dispatchEvent(event);
          close();
          trigger.focus();
        }
        function highlight(visibleIdx) {
          const visible = getVisibleOptions();
          if (!visible.length) return;
          highlightedIndex = Math.max(0, Math.min(visible.length - 1, visibleIdx));
          options.forEach((o, idx) => {
            const vIdx = visible.indexOf(o);
            o.tabIndex = vIdx === highlightedIndex ? 0 : -1;
          });
          const toFocus = visible[highlightedIndex];
          // Don't steal focus from search input when user is typing
          if (toFocus && (!searchInput || document.activeElement !== searchInput)) {
            toFocus.focus({ preventScroll: true });
          }
        }

        trigger.addEventListener('click', function(e){
          e.preventDefault();
          if (root.classList.contains('open')) { close(); } else { open(); }
        });
        options.forEach((opt, idx) => {
          opt.addEventListener('click', (e) => {
            e.stopPropagation();
            const visible = getVisibleOptions();
            const vIdx = visible.indexOf(opt);
            selectByIndex(vIdx >= 0 ? vIdx : 0);
          });
          opt.addEventListener('mousemove', () => {
            const visible = getVisibleOptions();
            highlightedIndex = visible.indexOf(opt);
          });
        });
        if (searchInput) {
          searchInput.addEventListener('input', function() { filterOptions(this.value); });
          searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); highlight(highlightedIndex + 1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); highlight(highlightedIndex - 1); }
            else if (e.key === 'Enter') { e.preventDefault(); selectByIndex(highlightedIndex); }
            else if (e.key === 'Escape') { e.preventDefault(); close(); trigger.focus(); }
          });
        }
        document.addEventListener('click', function(e){
          if (!root.contains(e.target)) close();
        });
        root.addEventListener('keydown', function(e){
          if (!root.classList.contains('open')) return;
          if (searchInput && document.activeElement === searchInput) return;
          if (e.key === 'ArrowDown') { e.preventDefault(); highlight(highlightedIndex+1); }
          else if (e.key === 'ArrowUp') { e.preventDefault(); highlight(highlightedIndex-1); }
          else if (e.key === 'Enter') { e.preventDefault(); selectByIndex(highlightedIndex); }
          else if (e.key === 'Escape') { e.preventDefault(); close(); trigger.focus(); }
        });
      }

      document.addEventListener('DOMContentLoaded', function(){
        initRlxSelect('rlx-pickup-flight');
        initRlxSelect('rlx-return-flight');
        initRlxSelect('rlx-meet-option');

        // Optional: Reset UI when flight info checkboxes change
        const noFlightInfoCheckbox = document.getElementById('no-flight-info-checkbox');
        if (noFlightInfoCheckbox) {
            noFlightInfoCheckbox.addEventListener('change', function() {
                if (!this.checked) { // "I have details" unchecked -> Clear
                    resetRlxSelect('rlx-pickup-flight', 'Select Airline', '');
                    resetRlxSelect('rlx-meet-option', 'Select Option', 'none');
                }
            });
        }
        const returnNoFlightInfo = document.getElementById('return-no-flight-info');
        if (returnNoFlightInfo) {
            returnNoFlightInfo.addEventListener('change', function() {
                if (this.checked) { // "I do NOT have details" checked -> Clear
                    resetRlxSelect('rlx-return-flight', 'Select Airline', '');
                }
            });
        }

        function resetRlxSelect(id, defaultText, defaultValue) {
            const root = document.getElementById(id);
            if (!root) return;
            const valueEl = root.querySelector('.rlx-value');
            const hidden = root.querySelector('input[type="hidden"]');
            const options = root.querySelectorAll('.rlx-option');

            valueEl.textContent = defaultText;
            hidden.value = defaultValue;
            options.forEach(o => {
                o.classList.remove('selected');
                o.setAttribute('aria-selected', 'false');
                if (o.getAttribute('data-value') === defaultValue) {
                    o.classList.add('selected');
                    o.setAttribute('aria-selected', 'true');
                }
            });
        }
      });
    })();
    </script>
    <script>
        jQuery(document).ready(function() {
            // Handle flight info toggle
            function toggleFlightInfoFields() {
                const hasFlightDetails = $('#no-flight-info-checkbox').is(':checked');
                const container = $('#outbound-flight-fields');
                if (hasFlightDetails) {
                    container.show();
                    $('input[name="pickup_flight_details"], input[name="flight_number"]').prop('disabled', false);
                    $('#meet-option').prop('disabled', false);
                } else {
                    container.hide();
                    $('input[name="pickup_flight_details"]').val('').prop('disabled', true);
                    $('input[name="flight_number"]').val('').prop('disabled', true);
                    $('#meet-option').val('none').prop('disabled', true);
                }
            }

            // Initialize on page load (restore from session if user navigated back)
            $(document).ready(function() {
                const hasFlightFromSession = {{ (session('no_flight_info', 0) == 1) ? 'true' : 'false' }};
                $('#no-flight-info-checkbox').prop('checked', hasFlightFromSession);
                toggleFlightInfoFields();
                $('#no-flight-info-checkbox').on('change', function() { toggleFlightInfoFields(); });
            });

            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.addEventListener('click', function() {
                    this.showPicker?.();
                });
            });

            const timeInputs = document.querySelectorAll('input[type="time"]');
            timeInputs.forEach(input => {
                input.addEventListener('click', function() {
                    this.showPicker?.();
                });
            });

            // Check if Google Maps API is loaded
            function checkGoogleMapsAPI() {
                if (typeof google === 'undefined' || !google.maps) {
                    console.log('Google Maps API not loaded yet');
                    return false;
                }
                console.log('Google Maps API is loaded');
                return true;
            }

            // Test map initialization
            setTimeout(function() {
                checkGoogleMapsAPI();

                // Test if modal map container exists
                const modalMapContainer = document.getElementById('modal-map');
                if (modalMapContainer) {
                    console.log('Modal map container found:', modalMapContainer);
                    // Add a test message to see if the container is visible
                    modalMapContainer.innerHTML =
                        '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; background-color: #f0f0f0;"><i class="bi bi-geo-alt" style="font-size: 2rem; margin-right: 10px;"></i>Map Container Ready</div>';
                } else {
                    console.log('Modal map container not found');
                }
            }, 1000);

            // Return service toggle behavior
            let returnServiceWasChecked = false;
            const returnServiceCheckbox = document.getElementById('return-service');

            if (returnServiceCheckbox) {
                returnServiceCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        returnServiceWasChecked = true;
                        const pickupFromSession = @json(session('pickup_location'));
                        const dropoffFromSession = @json(session('return_dropoff_location') ?: session('dropoff_location'));
                        document.getElementById('return-pickup-location').value = dropoffFromSession;
                        document.getElementById('return-dropoff-location').value = pickupFromSession;

                        // Show modal first, then initialize map
                        $('#returnReservationModal').modal('show');

                        // Initialize map after modal is shown
                        $('#returnReservationModal').on('shown.bs.modal', function() {
                            if (dropoffFromSession && pickupFromSession) {
                                waitForGoogleMaps(function() {
                                    loadInvertedMap(dropoffFromSession, pickupFromSession);
                                });
                            } else {
                                waitForGoogleMaps(function() {
                                    initializeEmptyModalMap();
                                });
                            }
                        });
                    }
                });
            }

            // Initialize empty modal map
            function initializeEmptyModalMap() {
                const modalMapElement = document.getElementById('modal-map');
                if (modalMapElement && typeof google !== 'undefined' && google.maps) {
                    const defaultCenter = {
                        lat: 32.7767,
                        lng: -96.7970
                    }; // Dallas coordinates
                    const modalMap = new google.maps.Map(modalMapElement, {
                        center: defaultCenter,
                        zoom: 10,
                        styles: typeof brandMapStyle !== 'undefined' ? brandMapStyle : [],
                        disableDefaultUI: true
                    });
                } else {
                    // Show loading message if Google Maps is not loaded
                    modalMapElement.innerHTML =
                        '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; background-color: #f0f0f0;"><i class="bi bi-geo-alt" style="font-size: 2rem; margin-right: 10px;"></i>Loading map...</div>';

                    // Try to initialize again after a delay
                    setTimeout(function() {
                        if (typeof google !== 'undefined' && google.maps) {
                            initializeEmptyModalMap();
                        }
                    }, 500);
                }
            }

            // Wait for Google Maps API to be fully loaded
            function waitForGoogleMaps(callback, maxAttempts = 20) {
                let attempts = 0;
                const checkInterval = setInterval(function() {
                    attempts++;
                    if (typeof google !== 'undefined' && google.maps) {
                        clearInterval(checkInterval);
                        callback();
                    } else if (attempts >= maxAttempts) {
                        clearInterval(checkInterval);
                        console.log('Google Maps API failed to load after maximum attempts');
                    }
                }, 100);
            }

            // Test if modal map container exists
            const modalMapContainer = document.getElementById('modal-map');
            if (modalMapContainer) {
                console.log('Modal map container found:', modalMapContainer);
                // Add a test message to see if the container is visible
                modalMapContainer.innerHTML =
                    '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; background-color: #f0f0f0;"><i class="bi bi-geo-alt" style="font-size: 2rem; margin-right: 10px;"></i>Map Container Ready</div>';
            } else {
                console.log('Modal map container not found');
            }

            // Handle modal close without saving
            $('#returnReservationModal').on('hidden.bs.modal', function() {
                if (returnServiceWasChecked && !returnServiceSaved) {
                    // Revert checkbox if modal was closed without saving
                    returnServiceCheckbox.checked = false;
                    returnServiceWasChecked = false;
                }
            });

            // Ensure map is initialized whenever modal opens
            $('#returnReservationModal').on('shown.bs.modal', function() {
                // Show immediate fallback content
                const modalMapElement = document.getElementById('modal-map');
                if (modalMapElement) {
                    modalMapElement.innerHTML =
                        '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; background-color: #f0f0f0;"><i class="bi bi-arrow-clockwise" style="font-size: 2rem; margin-right: 10px; animation: spin 1s linear infinite;"></i>Updating map...</div>';
                }

                // Small delay to ensure DOM is ready
                setTimeout(function() {
                    const pickupLocation = $('#return-pickup-location').val();
                    const dropoffLocation = $('#return-dropoff-location').val();

                    waitForGoogleMaps(function() {
                        if (pickupLocation && dropoffLocation) {
                            drawMapByAddresses(pickupLocation, dropoffLocation);
                        } else {
                            initializeEmptyModalMap();
                        }
                    });
                }, 100);

                // Add event listeners for location changes
                setupLocationChangeListeners();
            });

            // Setup location change listeners for real-time map updates
            function setupLocationChangeListeners() {
                const pickupInput = document.getElementById('return-pickup-location');
                const dropoffInput = document.getElementById('return-dropoff-location');

                if (pickupInput && dropoffInput) {
                    // Debounce function to avoid too many map updates
                    let debounceTimer;
                    const debounceMapUpdate = function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function() {
                            updateModalMap();
                        }, 500); // Wait 500ms after user stops typing
                    };

                    // Add input event listeners
                    pickupInput.addEventListener('input', debounceMapUpdate);
                    dropoffInput.addEventListener('input', debounceMapUpdate);

                    // Also listen for blur events (when user leaves the field)
                    pickupInput.addEventListener('blur', updateModalMap);
                    dropoffInput.addEventListener('blur', updateModalMap);

                    // Setup Google Places autocomplete for better location handling
                    setupGooglePlacesAutocomplete();
                }
            }

            // Setup Google Places autocomplete for location inputs
            function setupGooglePlacesAutocomplete() {
                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    const pickupInput = document.getElementById('return-pickup-location');
                    const dropoffInput = document.getElementById('return-dropoff-location');

                    if (pickupInput && dropoffInput) {
                        // Create autocomplete for pickup location
                        const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput);
                        pickupAutocomplete.addListener('place_changed', function() {
                            setTimeout(updateModalMap, 100); // Small delay to ensure value is set
                        });

                        // Create autocomplete for dropoff location
                        const dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffInput);
                        dropoffAutocomplete.addListener('place_changed', function() {
                            setTimeout(updateModalMap, 100); // Small delay to ensure value is set
                        });
                    }
                }
            }

            // Update modal map based on current input values
            function updateModalMap() {
                const pickupLocation = $('#return-pickup-location').val().trim();
                const dropoffLocation = $('#return-dropoff-location').val().trim();

                // Show loading indicator
                const modalMapElement = document.getElementById('modal-map');
                if (modalMapElement) {
                    modalMapElement.innerHTML =
                        '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; background-color: #f0f0f0;"><i class="bi bi-arrow-clockwise" style="font-size: 2rem; margin-right: 10px; animation: spin 1s linear infinite;"></i>Updating map...</div>';
                }

                if (pickupLocation && dropoffLocation) {
                    waitForGoogleMaps(function() {
                        drawMapByAddresses(pickupLocation, dropoffLocation);
                    });
                } else if (pickupLocation || dropoffLocation) {
                    // If only one location is filled, show a centered map
                    waitForGoogleMaps(function() {
                        initializeEmptyModalMap();
                    });
                } else {
                    // If no locations, show empty state
                    if (modalMapElement) {
                        modalMapElement.innerHTML =
                            '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; background-color: #f0f0f0;"><i class="bi bi-geo-alt" style="font-size: 2rem; margin-right: 10px;"></i>Enter pickup and dropoff locations to see the route</div>';
                    }
                }
            }

            // Edit return service button
            $('#edit-return-service').on('click', function() {
                // Pre-fill modal with existing data
                $('#return-pickup-location').val(@json(session('return_pickup_location')));
                $('#return-dropoff-location').val(@json(session('return_dropoff_location')));
                $('#return-pickup-date').val(@json(session('return_pickup_date')));
                $('#return-pickup-time').val(@json(session('return_pickup_time')));
                $('#return-flight-number').val(@json(session('return_flight_number')));
                $('#return-flight-details').val(@json(session('return_flight_details')));
                $('#return-no-flight-info').prop('checked', @json(session('return_no_flight_info') == 1));
                $('#return-vehicle-id').val(@json(session('return_vehicle_id')));

                // Mark as editing existing service
                returnServiceWasChecked = true;
                returnServiceSaved = false;

                $('#returnReservationModal').modal('show');

                // Initialize map after modal is shown
                $('#returnReservationModal').on('shown.bs.modal', function() {
                    const pickupLocation = $('#return-pickup-location').val();
                    const dropoffLocation = $('#return-dropoff-location').val();
                    waitForGoogleMaps(function() {
                        if (pickupLocation && dropoffLocation) {
                            drawMapByAddresses(pickupLocation, dropoffLocation);
                        } else {
                            initializeEmptyModalMap();
                        }
                    });
                });
            });

            // Handle return service form submission
            let returnServiceSaved = false;
            $('#return-service-form').on('submit', function(e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);

                // Add return price data if available
                const returnPrice = sessionStorage.getItem('return_price');
                if (returnPrice) {
                    formData.append('return_price', returnPrice);
                    formData.append('return_base_fare', sessionStorage.getItem('return_base_fare') || '0');
                    formData.append('return_per_km_rate', sessionStorage.getItem('return_per_km_rate') ||
                        '0');
                    formData.append('return_km', sessionStorage.getItem('return_km') || '0');
                }

                // Show loading state
                $('#save-return-service').prop('disabled', true).text('Saving...');

                // Submit form via AJAX
                jQuery.ajax({
                    url: '/save-return-service',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            returnServiceSaved = true;

                            // Update the display section with new data
                            updateReturnServiceDisplay(response.data);

                            // Close modal
                            $('#returnReservationModal').modal('hide');

                            // Show success message
                            alert('Return service saved successfully!');
                        } else {
                            alert('Failed to save return service: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Server error occurred while saving return service.');
                    },
                    complete: function() {
                        // Reset button state
                        $('#save-return-service').prop('disabled', false).text(
                            'Save Return Service');
                    }
                });
            });

            // Function to update return service display
            function updateReturnServiceDisplay(data) {
                // Update hidden fields in main form
                $('#hidden-return-service').val(1);
                $('#hidden-return-pickup-location').val(data.return_pickup_location);
                $('#hidden-return-dropoff-location').val(data.return_dropoff_location);
                $('#hidden-return-pickup-date').val(data.return_pickup_date);
                $('#hidden-return-pickup-time').val(data.return_pickup_time);
                $('#hidden-return-flight-number').val(data.return_flight_number);
                $('#hidden-return-flight-details').val(data.return_flight_details);
                $('#hidden-return-no-flight-info').val(data.return_no_flight_info);
                $('#hidden-return-vehicle-id').val(data.return_vehicle_id);

                // If we have return price data, update the pricing display
                if (data.return_price && parseFloat(data.return_price) > 0) {
                    updateReturnTripPricing(data);
                }

                // Calculate return trip price if vehicle is selected and no price data
                if (data.return_vehicle_id && (!data.return_price || parseFloat(data.return_price) <= 0)) {
                    calculateReturnTripPrice(data);
                }

                // Update the top area return summary (if present)
                if ($('#rs-pickup').length) {
                    $('#rs-pickup').text(data.return_pickup_location || '');
                }
                if ($('#rs-dropoff').length) {
                    $('#rs-dropoff').text(data.return_dropoff_location || '');
                }
                if ($('#rs-datetime').length) {
                    const date = data.return_pickup_date ? new Date(data.return_pickup_date) : null;
                    const time = data.return_pickup_time || '';
                    const formattedDate = date ? date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    }) : '';
                    const formattedTime = time ? new Date('2000-01-01T' + time).toLocaleTimeString('en-US', {
                        hour12: false,
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '';
                    $('#rs-datetime').text(`${formattedDate} ${formattedTime}`.trim());
                }
                if ($('#return-vehicle-name').length) {
                    $('#return-vehicle-name').text((data.vehicle_name || '').trim() || 'Not selected');
                }
            }

            // Update return trip pricing display
            function updateReturnTripPricing(data) {
                // Show the return trip section
                jQuery('#return-trip-section').show();

                // Update return trip price
                jQuery('#return-trip-price').text(`$${parseFloat(data.return_price).toFixed(2)}`);

                // Calculate new total trip price
                const outwardPriceText = jQuery('.total-trip-price').text().replace('$', '').trim();
                const outwardPrice = parseFloat(outwardPriceText) || 0;
                const returnTripPrice = parseFloat(data.return_price) || 0;
                const totalPrice = outwardPrice + returnTripPrice;

                // Update total price in DOM
                jQuery('.total-trip-price').text(`$${totalPrice.toFixed(2)}`);

                // Store return price in session storage for form submission
                sessionStorage.setItem('return_price', data.return_price);
                sessionStorage.setItem('return_base_fare', data.return_base_fare || '0');
                sessionStorage.setItem('return_per_km_rate', data.return_per_km_rate || '0');
                sessionStorage.setItem('return_km', data.return_km || '0');
            }

            // Calculate return trip price and update total
            function calculateReturnTripPrice(data) {
                const returnData = {
                    vehicle_id: data.return_vehicle_id,
                    pickup_location: data.return_pickup_location,
                    dropoff_location: data.return_dropoff_location,
                    pickup_date: data.return_pickup_date,
                    pickup_time: data.return_pickup_time,
                    flight_number: data.return_flight_number,
                    flight_details: data.return_flight_details,
                    no_flight_info: data.return_no_flight_info,
                    _token: jQuery('meta[name="csrf-token"]').attr('content')
                };

                jQuery.ajax({
                    url: '/calculate-return-trip',
                    method: 'GET',
                    data: returnData,
                    success: function(response) {
                        if (response.success) {
                            const returnData = response.data;

                            // Show the return trip section
                            jQuery('#return-trip-section').show();

                            // Populate the DOM with return trip data
                            jQuery('#return-trip-price').text(`$${returnData.price}`);

                            // Calculate new total trip price
                            const outwardPriceText = jQuery('.total-trip-price').text().replace('$', '')
                                .trim();
                            const outwardPrice = parseFloat(outwardPriceText) || 0;
                            const returnTripPrice = parseFloat(returnData.price) || 0;
                            const totalPrice = outwardPrice + returnTripPrice;

                            // Update total price in DOM
                            jQuery('.total-trip-price').text(`$${totalPrice.toFixed(2)}`);

                            // Store return price in session for form submission
                            sessionStorage.setItem('return_price', returnData.price);
                            sessionStorage.setItem('return_base_fare', returnData.baseFare);
                            sessionStorage.setItem('return_per_km_rate', returnData.perKmRate);
                            sessionStorage.setItem('return_km', returnData.distance_km);
                        } else {
                            console.log('Return trip calculation failed:', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.log('Server error occurred while calculating return trip price.');
                    }
                });
            }

            // Function to create return service display HTML
            function createReturnServiceDisplayHtml(data) {
                const date = new Date(data.return_pickup_date);
                const time = data.return_pickup_time;
                const formattedDate = date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
                const formattedTime = new Date('2000-01-01T' + time).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit'
                });

                return `
      <div id="return-service-details" class="p-4 mt-4 rounded border" style="background-color: #f8f9fa;">
        <div class="mb-3 d-flex justify-content-between align-items-start">
          <h6 class="mb-0 font-weight-bold">Return Service Details</h6>
          <button type="button" class="btn btn-sm btn-outline-primary" id="edit-return-service">
            <i class="bi bi-pencil"></i> Edit
          </button>
        </div>
        <div class="return-inline">
          <div class="return-item">
            <div class="return-item-label">Pickup Location</div>
            <div class="return-item-value" id="rs-pickup">${data.return_pickup_location}</div>
          </div>
          <div class="return-item">
            <div class="return-item-label">Destination</div>
            <div class="return-item-value" id="rs-dropoff">${data.return_dropoff_location}</div>
          </div>
          <div class="return-item">
            <div class="return-item-label">Pick-Up Date & Time</div>
            <div class="return-item-value" id="rs-datetime">${formattedDate} ${formattedTime}</div>
          </div>
          <div class="return-item">
            <div class="return-item-label">Car Type</div>
            <div class="return-item-value" id="return-vehicle-name">${data.vehicle_name || 'Selected'}</div>
          </div>
        </div>
        ${(data.return_flight_details || data.return_flight_number) ? `
            <div class="pt-3 mt-3 border-top">
              <h6 class="mb-2">Flight Information</h6>
              <div class="row">
                ${data.return_flight_details ? `
            <div class="col-md-6">
              <strong>Flight Details:</strong><br>
              <span class="text-muted">${data.return_flight_details}</span>
            </div>
            ` : ''}
                ${data.return_flight_number ? `
            <div class="col-md-6">
              <strong>Flight Number:</strong><br>
              <span class="text-muted">${data.return_flight_number}</span>
            </div>
            ` : ''}
              </div>
            </div>
            ` : ''}
      </div>
    `;
            }

            // Vehicle selection in modal
            jQuery('.select-vehicle').on('click', function() {
                const vehicleId = jQuery(this).data('vehicle-id');
                const vehicleName = jQuery(this).closest('.vehical-card').find('h5').text();

                // Store selected vehicle
                jQuery('#return-vehicle-id').val(vehicleId);

                // Hide vehicle selection
                jQuery('.vehicle_container').addClass('hide').removeClass('show');

                // Show success message
                alert('Vehicle selected: ' + vehicleName);

                // Trigger price calculation for return trip
                const returnData = {
                    return_vehicle_id: vehicleId,
                    return_pickup_location: jQuery('#return-pickup-location').val(),
                    return_dropoff_location: jQuery('#return-dropoff-location').val(),
                    return_pickup_date: jQuery('#return-pickup-date').val(),
                    return_pickup_time: jQuery('#return-pickup-time').val(),
                    return_flight_number: jQuery('#return-flight-number').val(),
                    return_flight_details: jQuery('#return-flight-details').val(),
                    return_no_flight_info: jQuery('#return-no-flight-info').is(':checked') ? 1 : 0
                };

                calculateReturnTripPrice(returnData);
            });

            // flatpickr("#return-pickup-date", {
            //     dateFormat: "m-d-Y",
            //     altInput: false
            // });

            // // Time Picker
            // flatpickr("#return-pickup-time", {
            //     enableTime: true,
            //     noCalendar: true,
            //     dateFormat: "h:i", // 12hr format with AM/PM
            //     time_24hr: true
            // });

        });
    </script>
    <script>
        // Toggle fake placeholder visibility based on value
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.ph-wrap').forEach(function(wrap) {
                const input = wrap.querySelector('input');
                if (!input) return;
                const sync = () => {
                    if (input.value && String(input.value).trim() !== '') {
                        wrap.classList.add('has-value');
                    } else {
                        wrap.classList.remove('has-value');
                    }
                };
                sync();
                input.addEventListener('input', sync);
                input.addEventListener('change', sync);
                input.addEventListener('blur', sync);
            });

            // 2-hour advance booking restriction for Return Trip
            function enforceBookingRestrictions(dateId, timeId) {
                const dateInput = document.getElementById(dateId);
                const timeInput = document.getElementById(timeId);
                if (!dateInput || !timeInput) return;

                function updateRestrictions() {
                    const now = new Date();
                    // Add 2 hours buffer
                    now.setHours(now.getHours() + 2);

                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const minDateStr = `${year}-${month}-${day}`;

                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const minTimeStr = `${hours}:${minutes}`;

                    // Set min date
                    dateInput.min = minDateStr;

                    // If current date value is invalid or empty, set to min date
                    if (!dateInput.value || dateInput.value < minDateStr) {
                        dateInput.value = minDateStr;
                    }

                    // Time restriction logic
                    if (dateInput.value === minDateStr) {
                        timeInput.min = minTimeStr;
                        // If current time value is invalid or empty, set to min time
                        if (!timeInput.value || timeInput.value < minTimeStr) {
                             timeInput.value = minTimeStr;
                        }
                    } else {
                        timeInput.removeAttribute('min');
                        // If empty, set a default time
                        if (!timeInput.value) {
                            timeInput.value = "12:00";
                        }
                    }
                }

                // Run on load
                updateRestrictions();

                // Run on date change
                dateInput.addEventListener('change', updateRestrictions);

                // Optional: Update periodically to handle time passing while page is open
                setInterval(updateRestrictions, 60000);
            }

            enforceBookingRestrictions('return-pickup-date', 'return-pickup-time');
        });

        // Save booking detail form (flight info, note) to session when user navigates away
        function saveBookingDetailToSession(callback) {
            const form = document.getElementById('booking-detail-form');
            if (!form) { if (callback) callback(); return; }
            const data = new FormData();
            data.append('form_type', 'booking_detail');
            data.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('input[name="_token"]')?.value || '');
            data.append('pickup_flight_details', document.getElementById('pickup-flight-details')?.value || '');
            data.append('flight_number', document.getElementById('flight-number')?.value || '');
            data.append('meet_option', document.getElementById('meet-option')?.value || 'none');
            data.append('note', document.getElementById('note')?.value || '');
            if (document.getElementById('no-flight-info-checkbox')?.checked) data.append('no_flight_info', '1');
            const retFlight = document.getElementById('return-flight-details');
            const retNum = document.getElementById('return-flight-number');
            const retNoInfo = document.getElementById('return-no-flight-info');
            data.append('return_flight_details', retFlight?.value || document.getElementById('hidden-return-flight-details')?.value || '');
            data.append('return_flight_number', retNum?.value || document.getElementById('hidden-return-flight-number')?.value || '');
            if (retNoInfo?.checked) data.append('return_no_flight_info', '1');
            fetch('/save-booking-form-session', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(() => { if (callback) callback(); })
                .catch(() => { if (callback) callback(); });
        }
        let saveDetailDebounce;
        document.querySelectorAll('#booking-detail-form #flight-number, #booking-detail-form #note').forEach(el => {
            if (el) el.addEventListener('blur', function() {
                clearTimeout(saveDetailDebounce);
                saveDetailDebounce = setTimeout(() => saveBookingDetailToSession(), 400);
            });
        });
        document.getElementById('booking-detail-form')?.addEventListener('click', function(e) {
            if (e.target.closest('.rlx-option')) {
                clearTimeout(saveDetailDebounce);
                saveDetailDebounce = setTimeout(() => saveBookingDetailToSession(), 400);
            }
        });
        document.getElementById('no-flight-info-checkbox')?.addEventListener('change', function() {
            clearTimeout(saveDetailDebounce);
            saveDetailDebounce = setTimeout(() => saveBookingDetailToSession(), 400);
        });
        document.querySelectorAll('a.trigger-loader[href], a.step[href]').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && !href.startsWith('#')) {
                    e.preventDefault();
                    saveBookingDetailToSession(() => { window.location.href = href; });
                }
            });
        });
    </script>
    @endsection
@endsection

 