@php
$currentStep = $step ?? 1;
$steps = [
1 => ['label' => 'Ride Info', 'route' => '/?edit=1'],
2 => ['label' => 'Vehicle Class', 'route' => session('service_type') === 'pointToPoint'
? '/booking/point-to-point'
: '/booking/hourly-hire'],
3 => ['label' => 'Passenger Info', 'route' => (session()->has('vehicle_id') && (session()->has('calculated_price') ||
session()->has('price') || ($currentStep >= 3)))
? '/user-login/' . session('vehicle_id') . '/' . (session('calculated_price') ?? session('price'))
: null],
4 => ['label' => 'Booking Detail', 'route' => ($currentStep >= 4 || session()->has('first_name'))
? '/submit-passengerInfo'
: null],
5 => ['label' => 'Payment', 'route' => null]
];
@endphp
<style>
    .pricing_summary_label {
        margin: 0px;
        font-weight: 400;
        line-height: 1.5;
        font-size: 1.2rem;
        color: #000;
    }

    .pricing_summary_price {
        font-weight: 400;
        line-height: 1.5;
        font-size: 1.2rem;
        color: #000;
    }

    .pricing_total_label {
        margin: 0px;
        font-weight: 400;
        font-size: 1.5rem;
        line-height: 1.334;
        color: var(--dark-bg-btn);
    }

    .pricing_total_price {
        margin: 0px;
        font-weight: 400;
        font-size: 1.5rem;
        line-height: 1.334;
        color: var(--dark-bg-btn);
    }

    .total_price_box {
        margin-top: 16px;
    }

    .price-decimal {
        font-size: 0.75em;
        vertical-align: super;

    }

    .payment_method_info_box {
        display: grid;
        justify-content: center;
        text-align: center;

    }

    .payment_method_info_box p {
        margin: 16px 0px 0px;
        font-weight: 400;
        font-size: 1rem;
        line-height: 1.5;
        color: #9e9e9e;
    }

    .cta-button {
        width: 100%;
        margin: 12px auto 0;
        display: block;
        font-weight: 600;
        letter-spacing: 2px;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        border-radius: 4px !important;
    }
</style>
<div class="mb-4 col-md-4" id="pricing-area-wrapper">

    @php
    $breakdown = session('breakdown_data');

    @endphp

    <div class="p-3 mt-3 rounded-lg shadow-sm bg-light">
        <!--<h2 class="mb-3 step-title" style="font-size: 16px">Trip Breakdown</h2>-->
        <!--<h2 class="mb-2 step-title font-weight-bold" style="font-size: 16px">Outward Trip</h2>-->

        @php
        $base = session('calculated_price');
        if ($breakdown && isset($breakdown['hourlyFare'])) {
        $base = $breakdown['hourlyFare'];
        }
        $base = $base ?? 0;
        $price = number_format($base, 2);
        [$whole, $decimal] = explode('.', $price);
        @endphp
        @php
        $isRoundTrip = session('round_trip') == 'on';
        @endphp
        <div class="mb-1 d-flex justify-content-between">
            <span class="pricing_summary_label">{{ $isRoundTrip ? 'Outward Trip' : 'Base Fare' }}</span>
            <span id="trip-price" class="pricing_summary_price">
                ${{ $whole}}<span class="price-decimal">.{{ $decimal }}</span> USD
            </span>
        </div>
        @if($breakdown && isset($breakdown['hours']) && session('select_hours'))
        <div class="mb-1 d-flex justify-content-between">
            <span class="pricing_summary_label">Total Hours</span>
            <span class="pricing_summary_price">{{ $breakdown['hours']? $breakdown['hours' ]: session('select_hours')
                }}</span>
        </div>
        @endif
        <div id="return-trip-section"
            style="{{ (session('round_trip') == 'on' && session('return_price')) ? '' : 'display: none;' }}">
            <hr />
            <!--<h2 class="mb-3 step-title font-weight-bold" style="font-size: 16px">Return Trip</h2>-->

            <!--<div class="mb-1 d-flex justify-content-between">-->
            <!--    <span class="text-muted">Base Fare (Return)</span>-->
            <!--    <span id="return-base-fare">${{ session('return_base_fare') }}</span>-->
            <!--</div>-->
            <!--<div class="mb-1 d-flex justify-content-between">-->
            <!--    <span class="text-muted">Per Mile Rate</span>-->
            <!--    <span id="return-per-km-rate">${{ session('return_per_km_rate') }}</span>-->
            <!--</div>-->
            <!--<div class="mb-1 d-flex justify-content-between">-->
            <!--    <span class="text-muted">Distance in Miles</span>-->
            <!--    <span id="return-distance">{{ session('return_km') }} Miles</span>-->
            <!--</div>-->
            @if(session('return_price'))
            <div class="mb-1 d-flex justify-content-between">
                <span class="pricing_summary_label">Return Trip</span>
                @php
                $price = number_format(session('return_price'), 2);
                [$whole, $decimal] = explode('.', $price);
                @endphp
                <span id="return-trip-price" class="pricing_summary_price">
                    ${{ $whole }}<span class="price-decimal">.{{ $decimal }}</span>
                </span>
            </div>
            @endif

        </div>

        <div class="d-flex justify-content-between total_price_box">
            <span class="pricing_total_label"
                style="background: linear-gradient(90deg, #e52c43, #ff6c00); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Total</span>
            @php
            $base = session('calculated_price');
            if ($base === null && $breakdown && isset($breakdown['hourlyFare'])) {
            $base = $breakdown['hourlyFare'];
            }
            $base = $base ?? 0;
            $return = session('return_price') ?? 0;
            $rawTotal = $base + $return;
            $formattedTotal = number_format($rawTotal, 2);
            [$whole, $decimal] = explode('.', $formattedTotal);
            @endphp
            <span class="pricing_total_price total-trip-price"
                style="background: linear-gradient(90deg, #e52c43, #ff6c00); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">
                ${{ $whole }}<span class="price-decimal">.{{ $decimal }}</span> USD
            </span>
        </div>
        <div class="mt-3 text-center">
            @php $targetForm = $currentStep == 5 ? 'payment-form' : 'booking-detail-form'; @endphp
            <button type="submit" class="btn btn-primary w-100 btn-uniform cta-button" id="submit-button"
                style="width: 100%; text-transform: uppercase; padding: 0.575rem .75rem !important; border-color: unset;"
                form="{{ $targetForm }}">
                @if($currentStep == 5)
                BOOK NOW
                @else
                CONTINUE TO PAYMENT
                @endif
            </button>
        </div>

        @if($currentStep == 5)
        <p class="mt-3 mb-0 text-center text-muted small">
            By clicking "BOOK NOW", you agree to our
            <a href="#" class="hover-black" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Terms &amp;
                Conditions</a>
        </p>
        @endif

        <div class="payment_method_info_box">
            <p class="mt-2 mb-0">Secure payments</p>
            <img src="{{ asset('assets/img/stripe-powered-light.svg') }}" alt="Payment methods"
                class="img-fluid payment-methods" style="margin-left: 30px;">

        </div>
    </div>





    <!--<ul>-->
    <!--    <li>-->
    <!--        <div class="items-center d-flex"><span-->
    <!--                class="text-[#2B3252] mx-1 text-xl ml-[-2.5px]"><svg stroke="currentColor"-->
    <!--                    fill="none" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"-->
    <!--                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round"-->
    <!--                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z">-->
    <!--                    </path>-->
    <!--                </svg></span>-->
    <!--            <p class="text-sm  ml-[5px] pb-0 mb-0" style="font-size: 14px">Tolls will be-->
    <!--                additional if applicable.</p>-->
    <!--        </div>-->
    <!--    </li>-->
    <!--    <li>-->
    <!--        <div class="items-center d-flex"><span-->
    <!--                class="text-[#2B3252] mx-1 text-xl ml-[-2.5px]"><svg stroke="currentColor"-->
    <!--                    fill="none" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"-->
    <!--                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round"-->
    <!--                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z">-->
    <!--                    </path>-->
    <!--                </svg></span>-->
    <!--            <p class="text-sm  ml-[5px] pb-0 mb-0" style="font-size: 14px">Trip Price includes-->
    <!--                base fare, gratuity and tax.</p>-->
    <!--        </div>-->
    <!--    </li>-->
    <!--    <li>-->
    <!--        <div class="items-center d-flex"><span-->
    <!--                class="text-[#2B3252] mx-1 text-xl ml-[-2.5px]"><svg stroke="currentColor"-->
    <!--                    fill="none" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"-->
    <!--                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">-->
    <!--                    <path stroke-linecap="round" stroke-linejoin="round"-->
    <!--                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z">-->
    <!--                    </path>-->
    <!--                </svg></span>-->
    <!--            <p class="text-sm  ml-[5px] pb-0 mb-0" style="font-size: 14px">All transactions-->
    <!--                are safe and secure.</p>-->
    <!--        </div>-->
    <!--    </li>-->
    <!--</ul>-->
</div>
</div>
</div>

{{-- ✅ TERMS MODAL --}}
<div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Terms & Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="margin: 0 0 16px; background-color: #FDFAF3; border: 1px solid #9C844C; border-radius: 12px;">
                    <tr>
                        <td style="padding: 14px; vertical-align: top;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="width: 36px; vertical-align: top; padding-top: 2px;">
                                        <div
                                            style="width: 26px; height: 26px; line-height: 26px; text-align: center; border-radius: 50%; background-color: #9C844C; color: #ffffff; font-size: 15px; font-weight: 700; font-family: Georgia, 'Times New Roman', serif;">
                                            i
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding-left: 8px;">
                                        <p
                                            style="margin: 0 0 8px; font-size: 16px; color: #222; font-weight: 700; line-height: 1.4;">
                                            Important Event Notice - FIFA World Cup 2026 (June 13 - July 15, 2026):
                                        </p>
                                        <p
                                            style="margin: 0; font-size: 15px; color: #333; font-style: italic; line-height: 1.5;">
                                            If this booking falls within the FIFA World Cup 2026 event dates, all rates,
                                            fees, and minimums are subject to change without notice based on event demand,
                                            availability, and operational conditions. You acknowledge and agree that these
                                            rates are not guaranteed until final payment is made in accordance with our
                                            policy. By confirming this reservation, you expressly authorize any rate
                                            adjustments and agree that such changes are not a basis for refund, credit,
                                            cancellation, or chargeback.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p>Welcome to Dallas Black Limo Service Service! These Terms and Conditions govern your use of this website and
                    our services. By getting access to and the usage of this website and our services, you agree to be
                    bound using those Terms. If you do not agree to those Terms, you can no longer use our offerings or
                    this website. You acknowledge that Dallas Black Limo Service Service has the right to exchange those Terms
                    at any time without notice to you. Continue to check them from time to time for updates.</p>
                <h6>1. Definitions</h6>
                <p><b>Dallas Black Limo Service Service, "we", "our", or "us":</b> Refers to the enterprise, the internet site,
                    the owners, the operators, and/or the associates. <b>"You" or "User":</b> Refers to individuals or
                    entities who get admission to or employ our internet site or offerings.</p>
                <h6>2. Acknowledgment And Agreement To Terms</h6>
                <p>When you use our site or what we do, you say you have looked at and get these rules, and you say yes
                    to them. But if you don't go along with these rules, don't use our site or anything we give you.</p>
                <p>Dallas Black Limo Service Service may change the Terms and Conditions at any time. If you continue to use the
                    services, you accept the new Terms and Conditions.</p>
                <h6>3. Services Offered</h6>
                <p>Dallas Black Limo Service Service is an elite provider of professional chauffeured limousine services,
                    specializing in the following areas:</p>
                <ul>
                    <li>Airport Transfers</li>
                    <li>Corporate and Executive Transportation</li>
                    <li>Special Event Services</li>
                </ul>
                <p>Users must confirm all booking details, including the pickup/drop-off points, dates, and times. All
                    booking details and payment confirmations will be sent to users through email or SMS.</p>
                <h6>4. Booking And Payment Policy</h6>
                <ul>
                    <li>Service bookings may be made online or over the phone with a representative.</li>
                    <li>Payment will be required at the time of booking and will be processed securely. The most common
                        method of payment generally accepted is debit/credit cards, and other acceptable methods will be
                        notified to you at the time of booking.</li>
                    <li>After payment is completed, you will receive a confirmation via email or SMS text message
                        containing your booking information.</li>
                    <li><b>Automatic Charges:</b> Payments for service will be automatically charged to the same form of
                        payment one day prior to service. Services associated with your booking that are requested after
                        booking confirmation will be billed separately.</li>
                    <li><b>Declined Payments:</b> Payments that are declined will require you to provide another form of
                        payment. If the alternate form of payment is not given, it is possible your booking may be
                        canceled. Dallas Black Limo Service Service is not liable for cancellations on your booking due to
                        payment issues.</li>
                </ul>
                <h6>5. Social Media/Social Networks</h6>
                <p>We may include social media plugins on our services to allow interaction with our social media
                    profiles. These plugins may collect personal information according to their privacy policies. Please
                    review third-party privacy policies for more details.</p>
                <h6>6. Data Processing During Registered Use and Booking Rides</h6>
                <p>When you book a ride or use our services, we collect personal data such as contact details, payment
                    information, and ride preferences. We use this data to provide services, communicate regarding your
                    service, handle billing, and improve offerings. By using our services, you consent to this data
                    processing.</p>
                <h6>7. Disputes And Arbitration</h6>
                <ul>
                    <li>The user must first contact Dallas Black Limo Service Service directly for resolution.</li>
                    <li>If there is no resolution, disputes will be settled through binding arbitration under relevant
                        U.S. law.</li>
                    <li>Arbitration will take place at a mutually agreed-upon location, and the arbitrator's decision
                        will be final.</li>
                    <li>Users waive their right to bring a class action lawsuit relating to the services of Dallas Black
                        Cars Service or its terms.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>