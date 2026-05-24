@extends('master')
@section('content')

@section('styles')
<style>
    .payment-card-option {
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
    }

    #card-errors {
        line-height: 18px !important;
        margin-left: 0;
    }

    .floating-bordered-input {
        position: relative;
        border: 1px solid #C4C4C4;
        border-radius: 4px;
        padding: 12px 15px !important;
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        background: #fff;
    }

    .floating-bordered-input.card-element-wrapper {
        padding: 0 !important;
        display: flex;
        align-items: center;
        min-height: 50px;
    }

    #card-element.form-control {
        height: 50px;
        padding: 12px 15px;
        background: transparent;
        border: none;
        width: 100%;
    }

    .card-element-wrapper #card-element {
        padding: 12px 15px;
        height: 50px;
        min-height: 50px;
    }

    .card-element-wrapper #card-element iframe {
        pointer-events: auto !important;
        width: 100% !important;
        height: 100% !important;
    }

    .card-element-wrapper .__PrivateStripeElement {
        width: 100%;
        height: 100%;
    }

    #payment-form {
        width: 100%;
        margin: 0 auto;
    }

    button {
        padding: 10px 16px;
        background-color: #5469d4;
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
    }

    .input-group-container {
        overflow: hidden;
        width: 100%;
    }

    .hover-black:hover {
        color: black !important;
    }

    .hover-black:focus {
        color: black !important;
    }

    .terms-paragraph {
        margin-bottom: 10px;
    }

    .terms-heading {
        font-size: 16px;
        font-style: normal;
        line-height: 25px;
        font-weight: 700;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        footer.footer.bg-blue {
            display: none !important;
        }
    .mobile-card-methods {
        width: 100% !important;
    }
    
    
    img.img-fluid {
    border-radius: 2px;
}

        .container.step-wrapper.md-py-3 {
            display: none !important;
        }

        .d-md-none.mb-3 {
            margin-bottom: 9px !important;
            margin-top: 10px !important;
        }


        .p-3.mt-3.rounded-lg.shadow-sm.bg-light {
            margin-top: 0px !important;
            padding-top: 0px !important;
        }

        .mobile-card-methods {
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        .mobile-card-methods .payment-methods {
            margin-left: 0 !important;
            max-width: 220px;
        }

        .payment-methods {
            display: flex !important;
            margin-top: 1px !important;
            max-width: 88% !important;
        }

        .container.py-md-5 {

            background: #fff !important;
        }

    }

    @media (min-width: 769px) {
        .mobile-card-methods {
            display: none;
        }
    }

    footer.footer.bg-blue {
        display: block;
    }

    .InputContainer .InputElement {
        position: absolute;
        top: 0;
        font-size: 16px !important;
    }

    .container,
    .container-lg,
    .container-md,
    .container-sm,
    .container-xl,
    .container-xxl {
        max-width: 1171px;
    }

    p.text-muted.small.bottom-tx {
        font-size: 11px;
        margin-top: -10px !important;
        margin-bottom: 11px;
    }

    .mobile-card-methods {
        display: block !important;
        width: 338px;
    }

    a.hover-black {
        text-decoration: underline !important;
    }

    .text-muted {
        color: #1E1E1E !important;
    }

    .p-3.mt-3.rounded-lg.shadow-sm.bg-light {
        background-color: #fafafa !important;
        padding-top: 20px !important;
    }


    .last-page-img {
        display: flex !important;
        margin-top: 0px !important;
        max-width: 73% !important;
        margin-left: 0px !important;
    }
</style>
@endsection

@include('partials.bookig-top_area')

<div class="container py-md-5 only-for-payments">
    <div class="row">
        <div class="col-md-8 payment-for-css">

            <h5 class="mb-2 fw-bold">Payment Information</h5>
            <p class="mb-3 small" style="color: #000;">All transactions are secure and encrypted. Safe and secure
                payments powered by <b>Stripe</b></p>

            <form id="payment-form" method="POST" action="/completeBook">
                @csrf
                <input type="hidden" name="payment_method_id" id="payment_method_id">

                @if(auth()->check())
                <label class="p-3 mb-3 rounded  d-flex align-items-center payment-card-option">
                    <input type="radio" name="payment_method" class="me-2 saved-card-radio" value="" checked>

                    <i class="far fa-credit-card fs-2 me-3"></i>
                    <span>Pay with a new card</span>
                </label>

                @forelse($cards as $card)
                @php
                $brand = strtolower($card->card->brand);
                $holder = $card->billing_details->name ?? '';
                @endphp

                <label class="p-3 mb-3 rounded border d-flex align-items-center payment-card-option">
                    <input type="radio" name="payment_method" class="me-2 saved-card-radio" value="{{ $card->id }}"
                        data-holder="{{ $holder }}">

                    {{-- FONT AWESOME ICON --}}
                    @if($brand === 'visa')
                    <i class="fab fa-cc-visa text-primary fs-2 me-3"></i>
                    @elseif($brand === 'mastercard')
                    <i class="fab fa-cc-mastercard text-danger fs-2 me-3"></i>
                    @elseif($brand === 'amex')
                    <i class="fab fa-cc-amex text-info fs-2 me-3"></i>
                    @elseif($brand === 'discover')
                    <i class="fab fa-cc-discover text-warning fs-2 me-3"></i>
                    @else
                    <i class="far fa-credit-card fs-2 me-3"></i>
                    @endif

                    <span>
                        **** **** **** {{ $card->card->last4 }}
                        ({{ strtoupper($card->card->brand) }})
                        Exp: {{ $card->card->exp_month }}/{{ $card->card->exp_year }}
                    </span>
                </label>
                @empty
                <p class="text-danger">No saved cards found — Please enter card below</p>
                @endforelse
                @endif

                {{-- ✅ FULL NAME + CARD NUMBER (SAME GROUP) --}}
                <div id="new-card-fields">

                    <!-- Full Name -->
                    <div class="mb-3 floating-bordered-input position-relative">
                        <span class="floating-label">Full Name</span>
                        <input type="text" id="card-name" class="form-control" placeholder="Name On Card*" required>
                    </div>

                    <!-- Card Number -->
                    <div class="mb-3 floating-bordered-input position-relative card-element-wrapper">
                        <span class="floating-label">Card Number</span>
                        <div id="card-element" class="form-control"></div>
                    </div>

                    <div class="mobile-card-methods">
                        <p class="text-muted small  bottom-tx">
                            By clicking "BOOK NOW", you agree to our
                            <a href="#" class="hover-black" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop">Terms &amp; Conditions</a>
                        </p>
                        <img src="{{ asset('assets/img/credit-cards.png') }}" alt="Payment methods"
                            class="img-fluid payment-methods last-page-img">
                    </div>

                    <div id="card-errors" class="mb-2 text-danger small"></div>
                </div>



                <div id="card-errors" class="mb-2 text-danger small"></div>

                {{-- ✅ BUTTON --}}
                <button type="submit" id="final-pay-button" class="mt-3 btn btn-primary w-100">
                    BOOK NOW
                </button>

            </form>
        </div>

        @include('booking.right_side_pricing_area')

    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    const errorDiv = document.getElementById('card-errors');
    const cardNameInput = document.getElementById('card-name');
    const hiddenPaymentMethod = document.getElementById('payment_method_id');
    const savedRadios = document.querySelectorAll('.saved-card-radio');
    const newCardFields = document.getElementById('new-card-fields');

    function toggleNewCardFields(show) {
        newCardFields.style.display = show ? 'block' : 'none';

        // Enable/disable input based on visibility
        cardNameInput.required = show;   // required only if showing
        cardNameInput.disabled = !show;  // disable if hidden

        // For Stripe Card Element, you can't set disabled, but hiding is enough
        if (!show) cardNameInput.value = '';
    }



    // ✅ INITIAL STATE: SHOW NEW CARD FIELDS
    toggleNewCardFields(true);

    // ✅ WHEN RADIO CHANGES
    savedRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            hiddenPaymentMethod.value = radio.value;

            if (radio.value) {
                // saved card selected → hide new card
                const holderName = radio.dataset.holder || '';
                cardNameInput.value = holderName;
                toggleNewCardFields(false);
            } else {
                // custom card selected → show new card
                toggleNewCardFields(true);
            }

            errorDiv.innerText = '';
        });
    });


    // ✅ IF USER TYPES MANUALLY → SWITCH TO NEW CARD MODE
    cardNameInput.addEventListener('input', function () {
        savedRadios.forEach(r => r.checked = false);
        hiddenPaymentMethod.value = '';
        toggleNewCardFields(true);
    });

    const payButton = document.getElementById('final-pay-button');

    function setPayButtonLoading(loading) {
        if (!payButton) return;
        payButton.disabled = loading;
        payButton.textContent = loading ? 'Processing…' : 'BOOK NOW';
    }

    // ✅ FINAL PAYMENT HANDLER
form.addEventListener('submit', async function (event) {
    event.preventDefault();
    errorDiv.innerText = '';
    setPayButtonLoading(true);

    // If a saved card is selected, submit form directly
    if (hiddenPaymentMethod.value && hiddenPaymentMethod.value !== '') {
        form.submit();
        return;
    }

    // Otherwise, new card → validate and create PaymentMethod
    const cardholder = cardNameInput.value.trim();
    if (!cardholder) {
        errorDiv.innerText = 'Card holder name is required.';
        setPayButtonLoading(false);
        return;
    }

    const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: card,
        billing_details: {
            name: cardholder
        }
    });

    if (error) {
        errorDiv.innerText = error.message;
        setPayButtonLoading(false);
        return;
    }

    hiddenPaymentMethod.value = paymentMethod.id;
    form.submit();
});

});
</script>

{{-- ✅ TERMS MODAL --}}
<div class="modal fade" id="terms-modal" tabindex="-1" aria-labelledby="terms-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terms-modal-label">Terms & Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Welcome to Dallas Limo and Black Cars! These Terms and Conditions govern your use of this website and
                    our services. By getting access to and the usage of this website and our services, you agree to be
                    bound using those Terms. If you do not agree to those Terms, you can no longer use our offerings or
                    this website. You acknowledge that Dallas Limo and Black Cars has the right to exchange those Terms
                    at any time without notice to you. Continue to check them from time to time for updates.</p>
                <h6>1. Definitions</h6>
                <p><b>Dallas Limo and Black Cars, "we", "our", or "us":</b> Refers to the enterprise, the internet site,
                    the owners, the operators, and/or the associates. <b>"You" or "User":</b> Refers to individuals or
                    entities who get admission to or employ our internet site or offerings.</p>
                <h6>2. Acknowledgment And Agreement To Terms</h6>
                <p>When you use our site or what we do, you say you have looked at and get these rules, and you say yes
                    to them. But if you don't go along with these rules, don't use our site or anything we give you.</p>
                <p>Dallas Limo and Black Cars may change the Terms and Conditions at any time. If you continue to use
                    the services, you accept the new Terms and Conditions.</p>
                <h6>3. Services Offered</h6>
                <p>Dallas Limo and Black Cars is an elite provider of professional chauffeured limousine services,
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
                        canceled. Dallas Limo and Black Cars is not liable for cancellations on your booking due to
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
                    <li>The user must first contact Dallas Limo and Black Cars directly for resolution.</li>
                    <li>If there is no resolution, disputes will be settled through binding arbitration under relevant
                        U.S. law.</li>
                    <li>Arbitration will take place at a mutually agreed-upon location, and the arbitrator's decision
                        will be final.</li>
                    <li>Users waive their right to bring a class action lawsuit relating to the services of Dallas Limo
                        and Black Cars or its terms.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection