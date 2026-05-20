@php
$formatAirport = function($name) {
if (!$name) return $name;
$nameLower = strtolower($name);
if (!str_contains($nameLower, 'airport')) return $name;
if (str_contains($name, 'DFW')) return 'Dallas Fort Worth International Airport';
if (str_contains($name, 'Love Field Airport (DAL)')) return 'Dallas Love Field Airport';
return $name;
};
$currentStep = $step ?? 1;
$steps = [
1 => ['label' => 'Ride Info', 'route' => '/?edit=1'],
2 => ['label' => 'Vehicle Class', 'route' => session('service_type') === 'pointToPoint'
? '/booking/point-to-point'
: '/booking/hourly-hire'],
3 => ['label' => 'Login', 'route' => (session()->has('vehicle_id') && (session()->has('calculated_price') ||
session()->has('price') || ($currentStep >= 3)))
? '/user-login/' . session('vehicle_id') . '/' . (session('calculated_price') ?? session('price'))
: null],
4 => ['label' => 'Booking Detail', 'route' => ($currentStep >= 4 || session()->has('first_name'))
? '/submit-passengerInfo'
: null],
5 => ['label' => 'Payment', 'route' => null]
];
@endphp

<style id="stepper">
    .return-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        /* optional spacing between items */
    }

    .return-item {
        flex: 1 1 auto;
        /* allow items to grow/shrink */
        min-width: 150px;
        /* don’t go too small on mobile */
    }

    /* First two items: cap them at 350px */
    .return-item:nth-child(1),
    .return-item:nth-child(2) {
        flex: 1 1 350px;
        /* preferred width */
        max-width: 300px;
        /* hard cap */
    }

    /* Make them stack on very small screens */
    @media (max-width: 480px) {
        .return-item {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }

    .btn-primary {
        background-color: #1E1E1E;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        gap: 0px;
        flex: 1 1 0;
    }

    .step-label {
        font-size: 0.875rem;
        color: #2c3550;
        margin: 0px;
    }

    .step-header {
        font-size: 0.75rem;
        color: rgb(110, 110, 110);
        font-weight: 400;
        letter-spacing: normal;
    }

    .step-title {
        font-size: 1.5rem;
        color: #1E1E1E;
        font-weight: 400;
        line-height: 1.334em;
    }


    .stepper {
        position: relative;
        gap: 0;
    }

    .stepper::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 26px;
        /* sits between label and dots */
        height: 2px;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
    }

    .step-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        background: #fff;
        display: inline-block;
        position: relative;
        top: 6px;
        /* aligns to the connector line */
    }

    .completed {
        border-color: #c7cbd6;
        background: #c7cbd6;
    }

    .select_car_btn {
        border: none !important;
    }

    .select_car_btn:active {
        background: linear-gradient(90deg, #e52c43, #ff6c00) !important;
    }

    .active {
        border: none !important;
        background: linear-gradient(90deg, #e52c43, #ff6c00) !important;
    }


    .upcoming {
        border-color: #0f121940;
        background: #c7cbd6;
    }

    .step-label-pill {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.875rem;
        line-height: 1;
        color: #000;
        background: transparent;
        border: 1px solid transparent;
    }

    .step-label-pill.is-active {
        color: white;
        font-weight: 600;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        border: none;
    }


    .summary_toggle_container {
        color: #1E1E1E;
        font-size: 1rem;
        line-height: 1.5rem;
    }

    .summary_text {
        margin: 0 0 8px !important;
        line-height: 1.4 !important;
        font-size: 0.8rem !important;
        font-weight: 400 !important;
        color: #646e73 !important;
    }

    .mob_top_summary {
        margin-top: 12px;
    }

    .summary_label {
        margin: 0;
        font-size: 0.75rem;
        line-height: 1.66;
        color: rgb(54 54 54) !important;

    }

    .summary_text {
        margin: 0 0 12px;
        line-height: 1.4;
        font-size: 0.8rem;
        color: black !important;
    }

    .summary-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .summary-label-inline {
        white-space: nowrap;
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.4;
        color: #000 !important;
        font-weight: 600;
    }

    .summary-leader {
        flex: 1;
        height: 0;
        border-bottom: 1px dashed #e0e0e0;
    }

    .summary-value-inline {
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.9rem;
        color: black !important;
    }

    .summary_info_box {
        position: relative;
    }

    .summary_devider {
        height: 20px;
        background: #2b325252;
        max-width: 1px !important;
        padding: 0 !important;
        width: 1px;
    }

    /* Return inline summary (return service) */
    .return-inline {
        display: flex;
        align-items: flex-start;
        width: 100%;
        flex: 1 1 auto;
        flex-wrap: nowrap;
    }

    .return-item {
        flex: 1 1 0;
        max-width: none;
        min-width: 0;
        padding: 0 8px;
    }

    .return-item:first-child {
        padding-left: 0;
    }

    .return-item:last-child {
        padding-right: 0;
    }

    .return-divider {
        width: 1px;
        min-width: 1px;
        height: 28px;
        background: #d1d5db;
        flex-shrink: 0;
        align-self: center;
    }

    .booking-summary-desktop .return-inline {
        flex-wrap: wrap;
        gap: 2px 0;
    }

    .booking-summary-desktop .return-item {
        flex: 0 1 auto;
        min-width: 0;
        max-width: 320px;
        padding: 0 30px;
    }

    .booking-summary-desktop .return-item:first-child {
        padding-left: 0;
    }

    .booking-summary-desktop .return-item:last-child {
        padding-right: 0;
    }

    .booking-summary-desktop .return-item .summary_text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .return-item-label {
        color: #2B3252;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .return-item-label:empty {
        display: none;
        margin-bottom: 0;
    }

    .return-item-value {
        color: #1f2937;
        font-weight: 500;
        font-size: 18px;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width:768px) {
        .return-inline {
            flex-wrap: wrap;
        }

        .return-item {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0;
        }

        .return-divider {
            display: none;
        }




    }

    /* Ensure EDIT button text is white */
    #edit-return-service.btn,
    .btn-primary {
        color: #fff !important;
    }

    /*.summary_info_box::after{*/
    /*    content:"";*/
    /*    position:absolute;*/
    /*    left:0;*/
    /*    top:0;*/
    /*    width:1px;*/
    /*    height:100%;*/
    /*    background-color:#e0e0e0;*/
    /*}*/
    @media screen and (max-width:768px) {
        .stepper {
            gap: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .bg-white {
            background-color: #fff;
        }



        .step-title {
            font-size: 20px !important;

        }

        .d-md-none.mb-3 {
            margin-bottom: -1px !important;
            margin-top: 0px !important;

        }


        .container.step-wrapper.md-py-3 {
            display: none;
        }





        .mob_stepper_container {
            display: flex;
            padding: 0;
            justify-content: space-between;
        }


        .booking_step_container>div {
            padding: 0 !important;
        }

        .booking_step_container .step-label {
            display: none !important;
        }

        .step {
            position: relative;
            flex: 0 0 auto;
        }

        .step-label-pill {
            white-space: nowrap;
        }

        .mob-step-dots {
            display: flex;
            flex-wrap: nowrap !important;
            white-space: nowrap;
            gap: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            align-items: center;
        }

        .mob-step-dots::-webkit-scrollbar {
            display: none;
        }

        .mob-step-dot {
            flex: 0 0 auto;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 2px solid #e5e7eb;
            background: #fff;
            color: #6b7280;
        }

        .mob-step-dot.completed {
            border: none;
            background: linear-gradient(90deg, #e52c43, #ff6c00);
            color: #fff;
        }

        .mob-step-dot.active {
            border: none;
            background: linear-gradient(90deg, #e52c43, #ff6c00);
            color: #fff !important;
        }

        .mob-step-dot.upcoming {
            border-color: #e5e7eb;
            background: #fff;
            color: #9ca3af;
        }

        .mob_top_summary .summary-row {
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 0;
            margin-bottom: 10px;
        }

        .mob_top_summary .summary-label-inline {
            flex: 0 0 100%;
            margin-bottom: 2px;
            font-size: 0.8rem !important;
            font-weight: 600;
            color: #000 !important;
        }

        .mob_top_summary .summary-leader {
            display: none !important;
        }

        .mob_top_summary .summary-value-inline {
            flex: 0 0 100%;
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            font-size: 0.8rem !important;
            color: #6b7280 !important;
        }

        .summary-section-title {
            font-weight: 700;
            color: #1f2937;
        }

        .summary-section-divider {
            height: 0;
            border-bottom: 1px solid #e5e7eb;
            margin: 8px 0;
        }
    }

    /*.step::after{*/
    /*    content: "";*/
    /*    position: absolute;*/
    /*    width: 20px;*/
    /*    height: 2px;*/
    /*    background: rgb(189, 189, 189);*/
    /*    right: 5px;*/
    /*    top: 50%;*/
    /*}*/
    /*.step:last-child::after {*/
    /*content: unset;*/
    /*}*/
    }

    @media screen and (max-width:353px) {
        .step {
            position: relative;
            padding-right: 25px;
        }

        .step::after {
            content: "";
            position: absolute;
            width: 10px;
            height: 2px;
            background: rgb(189, 189, 189);
            right: 5px;
            top: 50%;
        }

    }


    main {
        background: #ebebeb;
    }

    .bg-white {
        background-color: #ebebeb !important;
    }

    .col-12.col-md-12.d-none.d-md-block {
        background: #ebebeb !important;
    }

    @media (max-width: 767.98px) {
        .step-wrapper {
            overflow-x: hidden;
        }
    }
</style>
<div class="container step-wrapper md-py-3">
    <div class="container px-0 mr-auto ml-auto row align-items-center justify-content-between px-md-0 px-sm-0 booking_step_container"
        style="background-color: rgb(250, 250, 250);">
        <div class="col-12 col-md-12 d-none d-md-block">
            <div class="flex-nowrap pt-1 mt-4 stepper d-flex justify-content-start justify-content-md-end w-100">
                @foreach ($steps as $index => $stepData)
                @php
                $isCompleted = ($index < $currentStep); $isActive=($index===$currentStep); $isUpcoming=($index>
                    $currentStep);
                    @endphp

                    @if($stepData['route'])
                    <a href="{{ $stepData['route'] }}" class="text-center step trigger-loader">
                        @else
                        <div class="text-center step disabled-link" style="pointer-events: none;">
                            @endif

                            <div class="step-label-pill {{ $isActive ? 'is-active' : '' }}">{{ $stepData['label'] }}
                            </div>
                            <span
                                class="step-dot @if($isCompleted) completed @elseif($isActive) active @else upcoming @endif"></span>

                            @if($stepData['route'])
                    </a>
                    @else
            </div>
            @endif
            @endforeach

        </div>
    </div>
</div>
</div>

<!-- Mobile Steps: moved above summary -->
<div class="px-3 py-2 d-md-none" style="background-color: rgb(250, 250, 250);">
    <p class="mb-1 step-header">STEP {{ $currentStep }} OF {{ count($steps) }}</p>
    <div class="row">
        <div class="col-6">
            <h5 class="mb-2 step-title">{{ $steps[$currentStep]['label'] }}</h5>
        </div>
        <div class="col-6">
            <div class="mob-step-dots d-flex align-items-center">
                @foreach ($steps as $index => $stepData)
                @php
                $isCompleted = ($index < $currentStep); $isActive=($index===$currentStep); $cls=$isCompleted
                    ? 'completed' : ($isActive ? 'active' : 'upcoming' ); @endphp @if($stepData['route']) <a
                    href="{{ $stepData['route'] }}" class="trigger-loader">
                    <span class="mob-step-dot {{ $cls }}">@if($isCompleted)&#10003;@else{{ $index }}@endif</span>
                    </a>
                    @else
                    <span class="mob-step-dot {{ $cls }}">@if($isCompleted)&#10003;@else{{ $index }}@endif</span>
                    @endif
                    @endforeach
            </div>
        </div>
    </div>
</div>

@unless(request()->is('user-login/*/*'))
<div class="d-md-none mb-3">
    <div class="px-3 py-2 bg-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
        data-bs-target="#mobileRideSummary" aria-expanded="false" style="cursor: pointer;" onclick="toggleCollapse()">
        <h6 class="">Booking Summary</h6>
        <div class="d-flex align-items-center summary_toggle_container">
            <span id="expandText" class="mr-1">Expand</span>
            <svg id="expandArrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                viewBox="0 0 320 512">
                <path
                    d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z" />
            </svg>
        </div>
    </div>

    <div class="collapse" id="mobileRideSummary">
        <div class="px-3 mob_top_summary">
            @php
            $selectedVehicleName = null;
            try {
            $selId = session('vehicle_id');
            if ($selId) {
            $v = \App\Models\Vehicle::find($selId);
            $selectedVehicleName = $v ? $v->vehicle_name : null;
            }
            } catch (\Throwable $e) {
            $selectedVehicleName = null;
            }
            $showReturn = (session('round_trip') == 'on') || session('return_service');
            $returnPickup = session('return_pickup_location') ?? (session('dropoff_location'));
            $returnDropoff = session('return_dropoff_location') ?? (session('pickup_location'));
            $returnDate = session('return_pickup_date');
            $returnTime = session('return_pickup_time');
            $returnDT = session('return_datetime');
            @endphp

            <!--        <div class="mb-2 summary-section-title">Outward Trip</div>-->
            <div class="summary-row">
                <p class="summary-label-inline">Pickup Location</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">{{ $formatAirport(session('pickup_location')) }}</p>
            </div>
            <div class="summary-row">
                <p class="summary-label-inline">{{ session('dropoff_location') ? 'Destination' : 'Selected Hours' }}</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">@if(session('dropoff_location')){{
                    $formatAirport(session('dropoff_location')) }}@else Hours {{ session('select_hours') }} @endif</p>
            </div>
            <div class="summary-row">
                <p class="summary-label-inline">Date & Time</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">@if(session('pickup_date') && session('pickup_time')){{
                    \Carbon\Carbon::parse(session('pickup_date'))->format('D, M jS, Y') }} {{
                    \Carbon\Carbon::parse(session('pickup_time'))->format('h:i A') }}@endif</p>
            </div>
            @if($step > 2)
            <div class="summary-row">
                <p class="summary-label-inline">Car Type</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">{{ $selectedVehicleName ?? 'Sedan' }}</p>
            </div>
            @endif

            @if($showReturn)
            <div class="summary-section-divider"></div>
            <div class="mt-3 mb-2 summary-section-title">Return Trip</div>
            <div class="summary-row">
                <p class="summary-label-inline">Pickup Location</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">{{ $formatAirport($returnPickup) }}</p>
            </div>
            <div class="summary-row">
                <p class="summary-label-inline">Destination</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">{{ $formatAirport($returnDropoff) }}</p>
            </div>
            <div class="summary-row">
                <p class="summary-label-inline">Date & Time</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">
                    @if($returnDate && $returnTime)
                    {{ \Carbon\Carbon::parse($returnDate)->format('D, M jS, Y') }} {{
                    \Carbon\Carbon::parse($returnTime)->format('h:i A') }}
                    @elseif($returnDT)
                    {{ \Carbon\Carbon::parse($returnDT)->format('D, M jS, Y') }} {{
                    \Carbon\Carbon::parse($returnDT)->format('h:i A') }}
                    @endif
                </p>
            </div>
            @if($step > 2)
            <div class="summary-row">
                <p class="summary-label-inline">Car Type</p>
                <span class="summary-leader"></span>
                <p class="summary-value-inline">{{ $selectedVehicleName ?? 'Sedan' }}</p>
            </div>
            @endif
            @endif

            <div class="mt-2">
                <a href="/?edit=1">
                    <button class="px-3 py-1 btn btn-primary btn-sm font-weight-bold"
                        style="font-size: 14px;padding: 5px 8px !important;">EDIT</button>
                </a>
            </div>
        </div>
    </div>
</div>
@endunless

<!-- DESKTOP VIEW (hidden on small devices) -->
<div class="container px-3 py-3 bg-white d-none d-md-block booking-summary-desktop">
    <div class="d-flex align-items-start justify-content-between">
        <div class="return-inline">
            <div class="return-item">
                <p class="summary_label">Pickup Location</p>
                <p class="mb-0 summary_text">
                    @if(session('pickup_location'))
                    {{ $formatAirport(session('pickup_location')) }}
                    @endif
                </p>
                @if(session('round_trip') == 'on' && session('dropoff_location'))
                <p class="mb-0 summary_text">{{ $formatAirport(session('dropoff_location')) }}</p>
                @endif
            </div>
            <div class="return-divider"></div>
            <div class="return-item">
                <p class="summary_label">{{ session('dropoff_location') ? 'Destination' : 'Selected Hours' }}</p>
                <p class="mb-0 summary_text">
                    @if(session('dropoff_location'))
                    {{ $formatAirport(session('dropoff_location')) }}
                    @else
                    Hours {{ session('select_hours') }}
                    @endif
                </p>
                @if(session('round_trip') == 'on' && session('pickup_location') && session('dropoff_location'))
                <p class="mb-0 summary_text">{{ $formatAirport(session('pickup_location')) }}</p>
                @endif
            </div>
            <div class="return-divider"></div>
            <div class="return-item">
                <p class="summary_label">Pick-Up Date & Time</p>
                <p class="mb-0 summary_text">
                    @if(session('pickup_date') && session('pickup_time'))
                    {{ \Carbon\Carbon::parse(session('pickup_date'))->format('D, M jS, Y') }} {{
                    \Carbon\Carbon::parse(session('pickup_time'))->format('h:i A') }}
                    @endif
                </p>
                @if(session('round_trip') == 'on' && session('return_datetime'))
                <p class="mb-0 summary_text">
                    {{ \Carbon\Carbon::parse(session('return_datetime'))->format('D, M jS, Y') }} {{
                    \Carbon\Carbon::parse(session('return_datetime'))->format('h:i A') }}
                </p>
                @endif
            </div>
            <div class="return-divider"></div>
            <div class="return-item">
                <p class="summary_label">Car Type</p>
                <p class="mb-0 summary_text">
                    @php
                    $selectedVehicleName = null;
                    try {
                    $selId = session('vehicle_id');
                    if ($selId) {
                    $v = \App\Models\Vehicle::find($selId);
                    $selectedVehicleName = $v ? $v->vehicle_name : null;
                    }
                    } catch (\Throwable $e) {
                    $selectedVehicleName = null;
                    }
                    @endphp
                    {{ $selectedVehicleName ?? 'Sedan' }}
                </p>
                @if(session('round_trip') == 'on')
                <p class="mb-0 summary_text">{{ $selectedVehicleName ?? 'Sedan' }}</p>
                @endif
            </div>
        </div>
        <div>
            <a href="/?edit=1">
                <button class="mt-2 select_car_btn btn btn_dark btn-primary trigger-loader"
                    style="font-size: 14px;padding: 5px 8px !important;">EDIT</button>
            </a>
        </div>
    </div>
</div>


</div>



<script>
    function toggleCollapse() {
        const expandText = document.getElementById('expandText');
        const expandArrow = document.getElementById('expandArrow');

        // Toggle text and arrow based on collapse state
        if (expandText.innerText === 'Expand') {
            expandText.innerText = 'Collapse';
            expandArrow.style.transform = 'rotate(180deg)'; // Rotate the arrow to point down
        } else {
            expandText.innerText = 'Expand';
            expandArrow.style.transform = 'rotate(0deg)'; // Rotate the arrow back to the original position
        }
    }
</script>