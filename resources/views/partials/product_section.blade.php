@php
$features = [
['text' => 'Real-time updates', 'icon' => 'bi-airplane-fill'],
[
'text' => 'Free 30-minute',
'icon' => 'bi-clock-fill',
'tooltip' => 'Extra waiting time will be charged after the free waiting period as follows: Sedan: $1.00 per minute, SUV
and Business SUV: $1.50 per minute, Sprinter and Stretch Limo: $2.00 per minute.'
],
['text' => 'Cancellation policy', 'icon' => 'bi-x-circle-fill'],
['text' => 'bottled water', 'icon' => 'fa-solid fa-bottle-water'],
];

$desktopHiddenFeatureTexts = ['bottled water'];
$desktopFeatures = array_values(array_filter($features, function ($feature) use ($desktopHiddenFeatureTexts) {
return !in_array($feature['text'], $desktopHiddenFeatureTexts, true);
}));
@endphp

<style>
    .bi-chevron-down::before {
        font-size: 12px !important;
        font-weight: bold;
    }

    .collapseCardBody {
        padding: 0 0rem 0px !important;
        border: none !important;
        margin-left: 0.2rem;
        margin-right: 0rem;
        width: calc(100% + 0rem);
        box-sizing: border-box;
    }

    .feature-section {
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none !important;
        white-space: nowrap;
    }

    .btn-primary {
        background: linear-gradient(90deg, #e52c43, #ff6c00) !important;
        border: none !important;
        box-shadow: none !important;

    }

    .vehical-card {
        border: 1.5px solid #ccc;
        border-radius: 8px;
        cursor: pointer;
        padding: 24px 23px 18px 15px;
        margin-bottom: 10px;
        position: relative;
        transition: border-color 0.3s ease, background-color 0.3s ease;
        background-color: #fff;
        overflow: hidden;
    }

    .vehical-card:hover {
        border-color: #e52c43;
    }

    .vehical-card.selected {
        border-color: #ff6c00 !important;
        background-color: #fff7f2 !important;
    }

    .vehical-card.selected .collapseCardBody,
    .vehical-card.selected .feature_items_cont {
        background-color: #fff7f2 !important;
    }

    .tick-overlay {
        position: absolute;
        right: 20px;
        top: 18px;
        font-size: 1.5rem;
        display: block;
    }

    .tick-overlay i {
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .tick-overlay .tick-filled {
        display: none;
    }

    .vehical-card.selected .tick-overlay .tick-circle {
        display: none;
    }

    .vehical-card.selected .tick-overlay .tick-filled {
        display: inline-block;
    }

    .vehicle-continue-wrap {
        align-self: start;
    }

    .vehicle-continue-btn {
        white-space: nowrap;
        padding: 10px 20px;
        font-size: 0.9rem;
        min-width: 185px;
    }

    .vehicle-continue-btn.disabled,
    .vehicle-continue-btn[aria-disabled="true"] {
        opacity: 0.6;
        pointer-events: none;
        cursor: not-allowed;
    }

    /* ==== Vehicle Image ==== */
    .vehicle_img {
        max-height: 100%;
        object-fit: contain;
        width: 190px;
    }

    .best-value-badge {
        position: absolute;
        top: 0px;
        left: 8px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0px 6px;
        background: #fff;
        border-radius: 999px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        font-size: 10px;
        font-weight: 600;
        color: #333;
    }

    .best-value-badge i {
        color: #e52c43;
        font-size: 1rem;
    }

    .best-value-badge span {
        color: #1a1a1a;
    }

    /* ==== Vehicle Info Section ==== */
    .vehicle-info {
        flex-grow: 1;
        margin-left: 1rem;
        min-width: 220px;
        margin-left: 33px !important;
    }

    .vehicle-info>.vehicle-name {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1E1E1E;
    }

    .vehicle-info>.vehicle-description {
        font-size: 0.85rem;
        color: #555;
        margin-top: 0.2rem;
    }

    /* ==== Passengers and Luggage Info ==== */
    .pass-luggage-info {
        display: flex;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: #222;
    }

    .pass-luggage-info>div {
        display: flex;
        align-items: center;
        margin-right: 1.5rem;
    }

    .pass-luggage-info i {
        margin-right: 0.3rem;
        font-size: 1rem;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    /* ==== Features Section: row1 = 2 items, row2 = 2 items + Continue on same line ==== */
    .feature_items_cont.feature-items-grid {
        /* display: flex; */
        flex-wrap: wrap;
        padding-right: 0px;
        margin-top: 0px;
        /* margin-left: 48px !important; */
        font-size: 0.85rem;
        color: #444;
    }

    .feature-items-grid {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 12px 40px;
        margin-top: 15px;
        margin-left: 0px !important;
        font-size: 0.85rem;
        color: #444;
    }

    .feature-column {
        display: flex;
        flex-direction: row;
        gap: 12px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }


    .feature-icon {
        font-size: 1rem;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .feature-text {
        margin: 0;
        font-weight: 400;
        font-size: 13px !important;
        line-height: 1.43;
        color: #1E1E1E !important;
    }

    .info-icon {
        font-size: 0.9rem;
        color: #6c757d;
        margin-left: 0.4rem;
        cursor: pointer;
        position: relative;
    }

    /* ==== Tooltip styling ==== */
    .info-icon::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%;
        /* above the icon */
        left: auto;
        right: 0;
        transform: none;
        background-color: #64000c;
        color: #fff;
        -webkit-text-fill-color: initial;
        /* Ensure text is visible */
        padding: 6px 10px;
        border-radius: 4px;
        white-space: normal;
        font-size: 0.75rem;
        max-width: 220px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
        z-index: 9999;
        text-align: center;
        width: max-content;
    }

    .info-icon:hover::after {
        opacity: 1;
    }

    /* ==== Pricing Section ==== */
    .car-price {
        font-weight: 700;
        font-size: 1.3rem;
        color: #000;
    }

    .price-gratuity {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #1E1E1E;
    }

    .price-gratuity i {
        color: #28a745;
        font-size: 1rem;
    }

    .car-price small {
        vertical-align: top;
        color: #555;
        font-size: 0.85rem;
        margin-left: 0.1rem;
    }

    /* ==== Pricing description ==== */
    .price-desc {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .price-desc i {
        margin-right: 0.3rem;
    }

    /* ==== Select Button ==== */
    .vehical-card .select_car_btn {
        display: none !important;
    }

    .vehical-card .bi {
        vertical-align: middle;
    }

    /* Ensure the expand arrow can rotate and animate smoothly */
    .featureExpandArrow {
        display: inline-block;
        transition: transform 0.2s ease;
        font-size: 12px;
    }

    .select_car_btn:hover,
    .select_car_btn:focus {
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        color: #fff !important;
        text-decoration: none;
    }

    /* ==== Responsive adjustments ==== */
    @media (max-width: 767.98px) {
        .vehical-card {
            padding: 0.75rem;
            position: relative;
        }

        .bg-white {
            background-color: #fff !important;
        }

        .pass-luggage-info i {
            font-size: 13px !important;

        }

        .px-3.mob_top_summary {
            background: #fff;
            margin-top: 0px;
            padding-top: 5px;
            padding-bottom: 18px;
        }

        .vehical-card .d-flex {
            flex-wrap: nowrap !important;
            align-items: center;
        }

        .vehicle-img-container {
            width: auto;
            flex: 0 0 auto;
        }

        .vehicle_img {
            max-height: 90px;
            width: 160px;
        }

        .vehicle-info {
            min-width: 0 !important;
            margin: 0 8px !important;
            flex: 1 1 auto;
        }

        .vehicle-info>.vehicle-name {
            font-size: 13px;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .vehicle-info>.vehicle-description {
            display: none;
        }

        .pass-luggage-info {
            display: block;
            margin-top: 0.1rem;
            margin-bottom: 0;
            font-size: 0.8rem;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .pass-luggage-info>div {
            margin-right: 0.75rem;
        }

        .car-price-container {
            position: relative;
            right: auto;
            top: auto;
            transform: none;
            flex: 0 0 auto;
            min-width: 88px !important;
            max-width: 29%;
            text-align: right !important;
            margin-top: 0;
            white-space: normal;
            /* allow wrapping inside price container */
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            /* keep wrapped lines right-aligned */
        }

        .car-price h4 {
            font-size: 0.95rem;
            margin-bottom: 0.25rem !important;
        }

        .pricing_summary_price {
            font-size: 1.25rem;
        }

        .car-price .pricing_summary_price {
            white-space: nowrap;
            /* keep numeric part on one line; USD may wrap */
        }

        .car-price-container .btn {
            display: inline-block !important;
            padding: 0.2rem 0.5rem;
            font-size: 0.72rem;
            line-height: 1.2;
            margin-top: 2px;
        }

        .price-gratuity {
            font-size: 14px;
            gap: 4px;
            white-space: nowrap;
        }

        .price-gratuity i {
            font-size: 0.82rem;
        }

        .feature_items_cont {
            margin-left: 0 !important;
            padding-right: 0;
            width: 100%;
        }

        .feature-items-grid {
            flex-wrap: wrap;
        }

        .feature-items-grid>.feature-item {
            flex: 0 0 50%;
            box-sizing: border-box;
        }

        .feature-row-2 {
            grid-template-columns: 1fr 1fr auto;
        }

        .tick-overlay {
            display: none !important;
        }

        .vehical-card.selected {
            border-color: #ccc !important;
            background-color: #fff !important;
        }
    }

    /* ==== Legacy / Old classes unrelated to vehicle cards - untouched ==== */
    .side_section .card {
        box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 1px -1px,
            rgba(0, 0, 0, 0.14) 0px 1px 1px 0px,
            rgba(0, 0, 0, 0.12) 0px 1px 3px 0px;
        padding-top: 24px;
        padding-bottom: 24px;
        padding-left: 16px;
        padding-right: 16px;
        background-color: rgb(250, 250, 250);
        transition: box-shadow 300ms cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 16px;
    }

    .side_section .card .card-body {
        padding: 0;
    }

    .side_section .card .card-body .card-text {
        margin: 12px 0px 0px;
        font-family: Mukta, sans-serif;
        font-weight: 400;
        font-size: 0.875rem;
        line-height: 1.43;
    }

    .text-primary {
        color: var(--dark-txt) !important;
    }

    .side_section .card .card-body .card-title {
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.5;
        margin: 0;
    }

    .side_section .card .card-body a.mail_side {
        margin-top: 4px;
        margin-bottom: 12px !important;
    }

    .side_section .card .card-body a.number_side {
        margin-bottom: 0 !important;
        font-weight: 500;
        line-height: 1.75;
        font-size: 21px;
        letter-spacing: normal;
        margin-top: 0px;
    }

    .side_section .card .card-body .call_heading {
        font-size: 1rem;
        line-height: 1.5;
        margin-top: 20px;
        margin-bottom: 0;
        display: flex;
        gap: 8px;
    }

    .feaures_ul {
        margin-top: 16px;
        margin-bottom: 0;
    }

    .feaures_ul li {
        font-weight: 400;
        font-size: 16px;
        line-height: 1.5;
        margin: 0px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .side_section .card .card-body .card-title.hassle_free {
        margin-bottom: 12px;
    }

    .mc-6 {
        margin-top: 24px;
    }

    @media (min-width: 992px) {
        .side_section {
            position: sticky;
            top: 24px;
        }
    }

    .side_section .card {
        border: 0 !important;
        background: linear-gradient(180deg, #ffffff 0%, #f7fbfd 100%);
        outline: 1px solid rgba(229, 44, 67, 0.12);
    }

    .side_section .card+.card {
        margin-top: 16px;
    }

    .side_section .card .card-title i {
        background: linear-gradient(90deg, #e52c43, #ff6c00) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        display: inline-block;
    }

    .side_section .card hr {
        border: 0;
        height: 1px;
        background: linear-gradient(90deg, rgba(229, 44, 67, 0.25), rgba(255, 108, 0, 0.05));
        margin: 30px 0 14px 0;
    }

    .feaures_ul li {
        margin-bottom: 8px;
        color: #1E1E1E;
    }

    .feaures_ul li i {
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .side_section .card .card-text {
        color: #2b2b2b;
    }

    .side_section a.mail_side,
    .side_section a.number_side {
        transition: color 0.2s ease, text-decoration-color 0.2s ease;
    }

    .side_section a.mail_side:hover,
    .side_section a.number_side:hover {
        color: #e52c43 !important;
        text-decoration: underline;
        text-decoration-thickness: 1.5px;
    }

    .side_section .card:nth-of-type(1) {
        background: linear-gradient(180deg, #ffffff 0%, #fff7f0 100%);
    }

    .side_section .card:nth-of-type(2) {
        background: linear-gradient(180deg, #ffffff 0%, #fff7f0 100%)
    }

    .side_section .card:nth-of-type(3) {
        background: linear-gradient(180deg, #ffffff 0%, #fff7f0 100%)
    }

    /* ================= BACKDROP & SHEET ================= */

    #mbs-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1040;
        opacity: 0;
        transition: opacity .2s ease;
    }

    #mbs-sheet {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        background: #fff;
        z-index: 1050;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.2);
        transform: translateY(100%);
        transition: transform .2s ease;
        max-height: 85vh;
        overflow-x: hidden;
    }

    /* ================= HEADER ================= */

    .mbs-header {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0px 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    .mbs-title {
        font-weight: 700;
        font-size: 16px;
    }

    .mbs-close {
        border: 0;
        background: transparent;
        font-size: 30px;
        line-height: 1;
    }

    /* ================= CONTENT ================= */

    .mbs-content {
        padding: 12px 16px 0;
        overflow-y: auto;
        max-height: calc(85vh - 54px);
    }

    .mbs-content .row {
        margin-left: 0;
        margin-right: 0;
    }

    .mbs-hero img {
        width: 100%;
        border-radius: 8px;
    }

    .mbs-info {
        margin-top: 8px;
    }

    .mbs-top {
        align-items: flex-start;
        margin-bottom: 8px;
    }

    /* ================= META ================= */

    .mbs-meta {
        display: inline-flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .mbs-meta .mbs-line {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .mbs-meta i {
        font-size: 1.2rem;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .mbs-line {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1E1E1E;
        margin-bottom: 6px;
    }

    /* ================= TITLES ================= */

    .mbs-title-row {
        margin-top: 8px;
    }

    .mbs-content-title {
        font-weight: 700;
        margin: 8px 0 4px;
    }

    .mbs-desc {
        font-size: .9rem;
        color: #555;
    }

    /* ================= FEATURES (NEW & FIXED) ================= */

    .mbs-features {
        margin-top: 14px;
        margin-left: -16px;
        margin-right: -16px;
        width: calc(100% + 32px);
    }

    .mbs-features-title {
        font-weight: 700;
        margin-bottom: 8px;
        padding-left: 16px;
        padding-right: 16px;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .mbs-features-box {
        background: #e6e6e6;
        border-radius: 0;
        padding: 12px 16px;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }

    /* FIXED GRID */
    .mbs-features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px 12px;
    }

    /* FEATURE ITEM */
    .mbs-feature {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mbs-feature span {
        font-size: 0.85rem;
        color: #1E1E1E;
        line-height: 1.2;
    }

    /* ICON (MATCH RIGHT CARD) */
    .mbs-feature i {
        width: 28px;
        height: 28px;
        min-width: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        /* white circle */
        border-radius: 50%;
        /* makes it round */
        color: #111;
        /* icon color */
        font-size: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        /* subtle depth */
    }

    /* INFO ICON */
    .mbs-feature .info-icon {
        margin-left: auto;
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* ================= FOOTER ================= */

    .mbs-footer {
        position: sticky;
        bottom: 0;
        background: #fff;
        padding: 16px 0px;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
    }

    .mbs-footer-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .mbs-gratuity {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .mbs-gratuity i {
        color: #28a745;
        font-size: 1rem;
    }

    .mbs-price-number {
        font-weight: 700;
        font-size: 1.25rem;
        color: #000;
        font-family: "Inter", sans-serif;
    }

    /* ================= ACTIONS ================= */

    .mbs-actions-row {
        display: flex;
        gap: 12px;
    }

    .mbs-back {
        flex: 1;
        background: #fff;
        border: 1px solid #ccc;
        color: #999;
        font-weight: 700;
        border-radius: 4px;
        padding: 10px 0;
        font-size: 12px;
        text-transform: uppercase;
    }

    .mbs-back:hover {
        background: #f0f0f0;
        color: #333;
    }

    .mbs-select {
        flex: 2;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        border: none;
        color: #fff;
        font-weight: 700;
        border-radius: 4px;
        padding: 10px 0;
        font-size: 12px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    /* ================= DESKTOP HIDE ================= */

    @media (min-width: 768px) {

        #mbs-backdrop,
        #mbs-sheet {
            display: none !important;
        }
    }

    @media (max-width: 767.98px) {
        .booking-product-section-wrap {
            overflow-x: hidden;
        }
    }


    .container,
    .container-lg,
    .container-md,
    .container-sm,
    .container-xl,
    .container-xxl {
        max-width: 1172px;
    }


    a.btn.btn-primary.vehicle-continue-btn {
        border-radius: 4px;
    }

    .cta-button {
        border-radius: 4px !important;
    }

    .info-card {
        border-radius: 4px !important;
    }

    .benefits-section {
        background: #fff;
        border-radius: 6px !important;
    }

    .hourly-min {
        color: red;
        font-size: 25px;
    }
</style>



<div class="booking-product-section-wrap">
    <div class="container pr-0 pl-0">

        <div class="px-2">
            <div class="row">
                <div class="col-12 col-md-9 col-lg-8 car-section order-1 order-md-2">
                    @foreach ($data as $key => $value)
                    <div class="row no-gutters">
                        <div class="col-12">
                            @php
                            $isHourlyHire = (session('service_type') === 'hourlyHire') || (($service_type ?? null) ===
                            'hourlyHire');
                            $selectedHours = (int) session('select_hours', 0);
                            $passengerCount = (int) ($value['number_of_passengers'] ?? 0);
                            $vehicleName = trim($value['vehicle_name'] ?? '');
                            $requiredMinHours = 0;
                            if ($isHourlyHire) {
                            if ($vehicleName === 'Party Bus 20' && $passengerCount === 20) {
                            $requiredMinHours = 5;
                            } else {
                            $requiredMinHours = $passengerCount <= 6 ? 3 : ($passengerCount <=27 ? 4 : 5); } }
                                $requiresMoreHours=$requiredMinHours> 0 && $selectedHours < $requiredMinHours; @endphp
                                    <div class="vehical-card selectable-card" data-id="{{ $value['id'] }}"
                                    data-requires-more-hours="{{ $requiresMoreHours ? '1' : '0' }}"
                                    data-required-hours="{{ $requiredMinHours }}">
                                    <div class="mb-2 d-flex align-items-center justify-content-between w-100">
                                        <!-- Vehicle Image -->
                                        <div class="vehicle-img-container d-flex align-items-center">
                                            <img src="{{ 'https://admin.dallasblacklimoservice.com/storage/' . $value['vehicle_image'] }}"
                                                alt="Vehicle Image" class="img-fluid rounded-3 vehicle_img">
                                        </div>

                                        <!-- Vehicle Info -->
                                        <div class="mx-3 vehicle-info flex-grow-1">
                                            <h5 class="vehicle-name">{{ $value['vehicle_name'] }}</h5>
                                            <div class="pass-luggage-info">
                                                <div><i class="bi bi-people-fill"></i> Max. {{
                                                    $value['number_of_passengers'] }}</div>
                                                <div><i class="bi bi-bag-fill"></i> Max. {{ $value['luggage_capacity']
                                                    }}</div>
                                            </div>
                                            <h6 class="vehicle-description">{{ $value['description'] ?? 'No description
                                                available' }}</h6>
                                        </div>

                                        <!-- Price -->
                                        <div class="mr-2 text-right car-price-container">
                                            @php $vehicleDistance = $distance[$value['id']] ?? null; @endphp
                                            @if($vehicleDistance && empty($vehicleDistance['error']))
                                            <div class="car-price">
                                                @if($requiresMoreHours)
                                                <h4 class="mt-4 mb-1">
                                                    <span class="pricing_summary_price hourly-min">Min. {{
                                                        $requiredMinHours }} Hour</span>
                                                </h4>
                                                @else
                                                @php
                                                $price = number_format($vehicleDistance['price'], 2);
                                                [$whole, $decimal] = explode('.', $price);
                                                @endphp
                                                <h4 class="mt-4 mb-1">
                                                    <span class="pricing_summary_price">${{ $whole }}<span
                                                            class="price-decimal">.{{ $decimal }}</span></span>
                                                </h4>
                                                @endif
                                            </div>
                                            @unless($requiresMoreHours)
                                            <div class="price-gratuity">
                                                <i class="bi bi-check-circle-fill"></i>
                                                <span>Inclusive Price.</span>
                                            </div>
                                            @endunless
                                            @else
                                            <div class="text-danger font-weight-bold">Fare calculation failed</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="collapse" id="collapse-{{ $value['id'] }}">
                                        <div class="card card-body collapseCardBody">
                                            @php
                                            $continuePrice = ($vehicleDistance && empty($vehicleDistance['error'] ??
                                            null)) ? number_format($vehicleDistance['price'], 2) : '0';
                                            @endphp
                                            <hr class="feature-grid-hr">
                                            <div class="feature_items_cont feature-items-grid">


                                                @foreach ($desktopFeatures as $feature)
                                                <div class="feature-item">
                                                    <i class="bi {{ $feature['icon'] }} feature-icon"></i>
                                                    <span class="feature-text">{{ $feature['text'] }}</span>
                                                    @if (isset($feature['tooltip']))
                                                    <i class="bi bi-info-circle info-icon"
                                                        data-tooltip="{{ $feature['tooltip'] }}"></i>
                                                    @endif
                                                </div>
                                                @endforeach
                                                <div class="vehicle-continue-wrap">
                                                    <a href="{{ $requiresMoreHours ? 'javascript:void(0)' : '/user-login/' . $value['id'] . '/' . $continuePrice }}"
                                                        class="btn btn-primary vehicle-continue-btn {{ $requiresMoreHours ? 'disabled' : '' }}"
                                                        aria-disabled="{{ $requiresMoreHours ? 'true' : 'false' }}"
                                                        onclick="{{ $requiresMoreHours ? 'event.preventDefault(); event.stopPropagation(); return false;' : 'event.stopPropagation()' }}">
                                                        Continue
                                                    </a>
                                                </div>


                                            </div>

                                        </div>
                                    </div>
                                    <!-- Tick icon -->
                                    <div class="tick-overlay">
                                        <i class="bi bi-circle tick-circle"></i>
                                        <i class="bi bi-check-circle-fill tick-filled"></i>
                                    </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mb-4 col-12 col-md-3 col-lg-4 side_section order-2 order-md-1">
                <!-- Help Card -->
                <!-- Perks + Payments + Support Combined Card -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title hassle_free text-dark">Stress-Free Travel</h6>
                        <hr>
                        <ul class="list-unstyled small feaures_ul">
                            <li><i class="mr-2 bi bi-check-circle-fill"></i>All fares include tolls and gratuity</li>
                            <li><i class="mr-2 bi bi-check-circle-fill"></i>Timely reliable arrivals guaranteed</li>
                            <li><i class="mr-2 bi bi-check-circle-fill"></i>Courteous professional drivers</li>
                            <li><i class="mr-2 bi bi-check-circle-fill"></i>Simple all-inclusive price system</li>
                            <li><i class="mr-2 bi bi-check-circle-fill"></i>Premium luxury vehicles provided</li>
                        </ul>

                        <hr>
                        <h6 class="card-title">Secure payments</h6>
                        <img src="{{ asset('assets/img/credit-cards.png') }}" alt="Payment methods"
                            class="img-fluid step-tow-p payment-methods">
                        <hr>
                        <h6 class="card-title text-dark"><i class="bi bi-chat-left-text-fill"></i>Email Support</h6>
                        <p class="card-text">Reach us anytime for quick assistance.</p>
                        <a href="mailto:info@dallasblacklimoservice.com"
                            class="mail_side d-block text-decoration-none small text-dark">info@dallasblacklimoservice.com</a>
                        <hr>
                        <p class="mb-0 call_heading text-dark"><i class="bi bi-telephone-fill"></i>Call Support</p>
                        <p class="mb-0">
                            <a href="tel:+14699612047"
                                class="number_side d-block text-decoration-none text-dark">+1 469-961-2047</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="mbs-backdrop" style="display:none"></div>
<div id="mbs-sheet" style="display:none">
    <div class="mbs-header">
        <button type="button" class="mbs-close">×</button>
    </div>
    <div class="mbs-content">
        <div class="mbs-top">
            <div class="mbs-hero position-relative">
                <span class="best-value-badge"> <i style="font-size: 13px;" class="bi bi-tag-fill"></i> <span>Best
                        Value</span> </span>
                <img src="" alt="" />
            </div>
        </div>
        <div class="flex-wrap mbs-title-row d-flex align-items-center justify-content-between">
            <h5 class="mbs-content-title"></h5>
            <div class="mr-2 mbs-meta">
                <span class="mbs-line"><i class="bi bi-people-fill"></i><span
                        class="mbs-passengers-count"></span></span>
                <span class="mbs-line"><i class="bi bi-bag-fill"></i><span class="mbs-luggage-count"></span></span>
            </div>
        </div>
        <p class="mbs-desc" style="display:none"></p>
        <div class="mbs-features">
            <div class="mbs-features-title">Included</div>
            <div class="mbs-features-box">
                <div class="mbs-features-grid">
                    @foreach($features as $f)
                    <div class="mbs-feature">
                        <i class=" {{ $f['icon'] }}"></i>
                        <span>{{ $f['text'] }}</span>

                        @if(isset($f['tooltip']))
                        <i class="bi bi-info-circle info-icon" data-tooltip="{{ $f['tooltip'] }}"></i>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mbs-footer">
            <div class="mbs-footer-info">
                <div class="mbs-gratuity">
                    <i class="bi bi-check-circle-fill"></i> Price includes taxes, tolls & tip.
                </div>
                <h4 class="mbs-price-number tt"></h4>
            </div>
            <div class="mbs-actions-row">
                <button type="button" class="btn mbs-back">BACK</button>
                <button type="button" class="btn mbs-select">SELECT VEHICLE <i
                        class="bi bi-caret-right-fill"></i></button>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleFeatureCollapse(event) {
        if(event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const trigger = event.currentTarget;
        const card = trigger.closest('.vehical-card') || (trigger.classList && trigger.classList.contains('vehical-card') ? trigger : null);
        if (!card) return;

        const targetId = 'collapse-' + card.getAttribute('data-id');
        const target = document.getElementById(targetId);
        if (!target) return;

        const expandArrow = card.querySelector('.featureExpandArrow');
        const allCards = document.querySelectorAll('.selectable-card');

        // Use aria-expanded as the source of truth for the desired state
        const isExpanded = card.getAttribute('aria-expanded') === 'true';
        const nextState = !isExpanded;

        if (nextState) {
            allCards.forEach(function(otherCard) {
                if (otherCard === card) return;

                otherCard.setAttribute('aria-expanded', 'false');
                const otherArrow = otherCard.querySelector('.featureExpandArrow');
                if (otherArrow) otherArrow.style.transform = 'rotate(0deg)';

                const otherTargetId = 'collapse-' + otherCard.getAttribute('data-id');
                const otherTarget = document.getElementById(otherTargetId);
                if (!otherTarget) return;

                if (typeof $ !== 'undefined' && $(otherTarget).collapse) {
                    $(otherTarget).collapse('hide');
                } else {
                    otherTarget.classList.remove('show');
                    otherTarget.style.display = 'none';
                }
            });
        }

        // Update aria-expanded immediately
        card.setAttribute('aria-expanded', nextState ? 'true' : 'false');

        // Update UI immediately
        if (nextState) {
             if (expandArrow) expandArrow.style.transform = 'rotate(180deg)';
        } else {
             if (expandArrow) expandArrow.style.transform = 'rotate(0deg)';
        }

        // Perform toggle
        if (typeof $ !== 'undefined' && $(target).collapse) {
             $(target).collapse('toggle');
        } else {
             if (isExpanded) { // If was expanded, now closing
                 target.classList.remove('show');
                 target.style.display = 'none';
             } else { // If was collapsed, now opening
                 target.classList.add('show');
                 target.style.display = 'block';
             }
        }
    }
    (function(){
        var backdrop = document.getElementById('mbs-backdrop');
        var sheet = document.getElementById('mbs-sheet');
        var content = document.querySelector('#mbs-sheet .mbs-content');
        var titleEl = content.querySelector('.mbs-content-title');
        var step = parseInt("{{ $step ?? 2 }}");

        // Handle Card Clicks: anywhere on card toggles features (except Continue button)
        var cards = document.querySelectorAll('.selectable-card');
        cards.forEach(function(card){
            if (!card.hasAttribute('aria-expanded')) {
                card.setAttribute('aria-expanded', 'false');
            }

            card.addEventListener('click', function(e){
                // Continue button has its own link – do not toggle
                if(e.target.closest('.vehicle-continue-wrap') || e.target.closest('.vehicle-continue-btn')) return;
                // If clicking inside collapse content, don't toggle again
                if(e.target.closest('.collapse') || e.target.closest('.collapseCardBody')) return;

                // If mobile, open bottom sheet
                if(window.innerWidth < 768) {
                    openForCard(card);
                    return;
                }

                // Desktop: clicking card toggles features
                var fakeEvent = { preventDefault: function(){}, stopPropagation: function(){}, currentTarget: card };
                toggleFeatureCollapse(fakeEvent);

                // Visual selection
                document.querySelectorAll('.selectable-card').forEach(function(c){ c.classList.remove('selected'); });
                card.classList.add('selected');
            });
        });

        function closeSheet(){
            sheet.style.transform = 'translateY(100%)';
            setTimeout(function(){ sheet.style.display = 'none'; }, 200);
            backdrop.style.opacity = '0';
            setTimeout(function(){ backdrop.style.display = 'none'; }, 200);
        }
        function openForCard(card){
            var name = (card.querySelector('.vehicle-name')||{}).textContent||'';
            var img = (card.querySelector('.vehicle_img')||{}).src||'';
            var passengers = (card.querySelector('.pass-luggage-info > div:nth-child(1)')||{}).textContent||'';
            var luggage = (card.querySelector('.pass-luggage-info > div:nth-child(2)')||{}).textContent||'';
            var desc = (card.querySelector('.vehicle-description')||{}).textContent||'';
            var priceText = ((card.querySelector('.car-price h4')||{}).textContent)||'';

            titleEl.textContent = name;

            var heroImg = content.querySelector('.mbs-hero img');
            if (heroImg) { heroImg.src = img; heroImg.alt = name; }

            var passengersCountMatch = (passengers || '').match(/[0-9]+/);
            var passengersCount = passengersCountMatch ? passengersCountMatch[0] : '';
            var passengersCountEl = sheet.querySelector('.mbs-passengers-count');
            if (passengersCountEl) { passengersCountEl.textContent = passengersCount; }

            var badgeEl = content.querySelector('.best-value-badge');
            if (badgeEl) { badgeEl.style.display = (parseInt(passengersCount, 10) <= 6) ? '' : 'none'; }

            var luggageCountMatch = (luggage || '').match(/[0-9]+/);
            var luggageCount = luggageCountMatch ? luggageCountMatch[0] : '';
            var luggageCountEl = sheet.querySelector('.mbs-luggage-count');
            if (luggageCountEl) { luggageCountEl.textContent = luggageCount; }

            var descEl = content.querySelector('.mbs-desc');
            if (descEl) {
                if (desc) { descEl.textContent = desc; descEl.style.display = ''; }
                else { descEl.textContent = ''; descEl.style.display = 'none'; }
            }

            var priceNumEl = content.querySelector('.mbs-price-number');
            if (priceNumEl) { priceNumEl.textContent = priceText; }

            var requiresMoreHours = card.dataset.requiresMoreHours === '1';
            var requiredHours = card.dataset.requiredHours || '5';

            backdrop.style.display = 'block';
            sheet.style.display = 'block';
            requestAnimationFrame(function(){
                backdrop.style.opacity = '1';
                sheet.style.transform = 'translateY(0)';
            });

            var backBtn = content.querySelector('.mbs-back');
            if(backBtn) {
                backBtn.onclick = closeSheet;
            }

            var selectBtn = content.querySelector('.mbs-select');
            if (selectBtn) {
                if (requiresMoreHours) {
                    selectBtn.disabled = true;
                    selectBtn.style.opacity = '0.6';
                    selectBtn.style.cursor = 'not-allowed';
                    selectBtn.innerHTML = 'Min. ' + requiredHours + ' hrs';
                } else {
                    selectBtn.disabled = false;
                    selectBtn.style.opacity = '';
                    selectBtn.style.cursor = '';
                    selectBtn.innerHTML = 'SELECT VEHICLE <i class="bi bi-caret-right-fill"></i>';
                }

                selectBtn.onclick = function(){
                    if (requiresMoreHours) {
                        return;
                    }
                    var id = card.dataset.id;
                    var priceEl = card.querySelector('.pricing_summary_price');
                    var priceMatch = (priceEl && (priceEl.textContent || '')).match(/[0-9]+(?:\.[0-9]+)?/);
                    var price = priceMatch ? priceMatch[0] : null;
                    if (step === 2 && id && price) {
                        window.location.href = '/user-login/' + id + '/' + price;
                        return;
                    }
                    document.querySelectorAll('.selectable-card').forEach(function(c){ c.classList.remove('selected'); });
                    card.classList.add('selected');
                    closeSheet();
                };
            }
        }
        var closeBtn = document.querySelector('#mbs-sheet .mbs-close');
        if(closeBtn){ closeBtn.addEventListener('click', closeSheet); }
        if(backdrop){ backdrop.addEventListener('click', closeSheet); }
        window.openVehicleBottomSheet = function(card){ openForCard(card); };
    })();
</script>