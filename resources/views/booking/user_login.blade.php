@extends('master')
@section('content')

@php
$step = 3;
@endphp

@include('partials.bookig-top_area')

<style>
    .login-btn:focus {
        border: none !important;
    }

    .passenger-info-container .floating-bordered-input input.form-control,
    .passenger-info-container .floating-bordered-input input[type="text"],
    .passenger-info-container .floating-bordered-input input[type="email"],
    .passenger-info-container .floating-bordered-input input[type="password"],
    .passenger-info-container .floating-bordered-input input[type="tel"] {
        min-height: 34px !important;
        height: 34px !important;
        padding: 7px 10px 16px !important;
        font-size: 14px;
        line-height: 1.4;
        box-sizing: border-box;
    }

    .passenger-info-container .floating-bordered-input .floating-label {
        padding-bottom: 0 !important;
    }

    .floating-bordered-input {
        position: relative;
        border: 1px solid #C4C4C4;
        border-radius: 4px;
        padding: 4px 12px 5px !important;
        padding-bottom: 0px !important;
        background: #fff;
    }

    .passenger-info-container {
        max-width: 1160px;
        margin: 0 auto;
        padding: 50px 15px 100px 15px;
        width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
    }


.container.step-wrapper.md-py-3 {
    max-width: 1160px;
    margin: 0px auto;
    width: 100%;
    padding: 0 0 0 0 !important;
}



.stepper::before {
   
    width: 102%;
}

    .info-card {
        background: #fff;
        border-radius: 8px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 500;
        color: #1E1E1E;
        margin-bottom: 18px;
    }

    .form-row-custom {
        display: flex;
        gap: 12px;
        margin-bottom: 0;
    }

    .form-row-custom>div {
        flex: 1;
        min-width: 0;
    }

    .input-group-container {
        margin-bottom: 12px;
    }

    .input-group-container label {
        display: block;
        font-size: 13px;
        color: #666;
        margin-bottom: 5px;
        font-weight: 400;
    }

    .input-group-container input {
        width: 100%;
        padding: 5px 10px;
        min-height: 34px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .input-group-container input:focus {
        outline: none;
        border-color: #e52c43;
    }

    .continue-btn {
        width: 100%;
        padding: 11px;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        border: none;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }

    #continue_right {
        width: 100%;
        padding: 11px;
        background: linear-gradient(90deg, #e52c43, #ff6c00);
        border: none;
        color: #fff;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .benefits-section {
        background: #fff;
        border-radius: 8px;
    }

    .benefits-title {
        font-size: 16px;
        font-weight: 500;
        color: #1E1E1E;
        margin-bottom: 12px;
    }

    .benefit-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;
        color: #666;
        font-size: 13px;
    }

    .benefit-item i {
        color: #e52c43;
        margin-right: 8px;
        margin-top: 2px;
        font-size: 14px;
        flex-shrink: 0;
    }

    .login-section {
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid #e0e0e0;
    }

    .login-title {
        font-size: 16px;
        font-weight: 500;
        color: #1E1E1E;
        margin-bottom: 12px;
    }

    .login-btn {
        width: 100%;
        padding: 11px;
        background: #e52c43;
        border: none;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .login-btn:hover {
        background: #145570;
    }

    .cta-button {
        width: 100%;
        margin: 12px auto 0;
        display: block;
        font-weight: 600;
        letter-spacing: 2px;
    }

    @media (min-width: 992px) {
        .loginguestrow {
            margin-top: 30px;
        }

        .col-divider {
            border-left: 1px solid #e0e0e0;
        }

        .col-divider .benefits-section {
            padding-left: 35px;
        }

        .info-card {
            padding-right: 35px;
        }
    }

    @media (max-width: 768px) {
        .form-row-custom {
            flex-direction: column;
            gap: 0;
        }

        .info-card,
        .benefits-section {
            padding: 20px;
        }

        .passenger-info-container {
            padding-bottom: 20px;
            padding-top: 0px !important;
        }
    }

    .d-flex.align-items-start.justify-content-between {
        display: none !important;
    }

    .phone-input-wrapper .iti {
        width: 100%;
        min-width: 0;
    }

    .phone-input-wrapper {
        min-width: 0;
    }

    .phone-input-wrapper .iti__selected-flag {
        background: none !important;
        padding: 1px 6px 0 8px !important;
    }

    .phone-input-wrapper .iti {
        flex-wrap: nowrap;
        display: flex !important;
    }

    .phone-input-wrapper .iti__flag-container {
        flex-shrink: 0;
    }

    .phone-input-wrapper .iti input.form-control,
    .phone-input-wrapper .iti .iti__input,
    .phone-input-wrapper .floating-bordered-input .iti input {
        flex: 1;
        padding: 7px 8px !important;
        padding-left: 50px !important;
        margin: 0 !important;
        line-height: 1.4;
        font-size: 14px;
        min-height: 50px !important;
        height: 34px !important;
        box-sizing: border-box;
        border: none !important;
        min-width: 0;
    }

    .phone-field-wrapper {
        margin-bottom: 1rem;
    }

    .phone-field-wrapper .phone-error {
        display: block;
        min-height: 20px;
    }

    .phone-field-wrapper .phone-error:not(:empty) {
        display: block;
        margin-top: 0.25rem;
    }

    .phone-field-wrapper .phone-error:empty {
        display: none;
    }

    .iti__country-list {
        z-index: 9999 !important;
    }
</style>

<div class="passenger-info-container">
    <div class="row loginguestrow">
        <!-- Left Column: Guest Form -->
        <div class="mb-4 col-lg-6">
            <div class="info-card">
                <h2 class="section-title">Continue as Guest</h2>
                <form id="passengerForm" method="POST" action="/login">
                    @csrf
                    @method('POST')
                    <input type="text" name="login_type" value="booking" hidden>
                    <!-- Email -->
                    <div class="floating-bordered-input position-relative">
                        <span class="floating-label">Email address *</span>
                        <input type="email" id="guest_email" name="email" value="{{ old('email', session('email') ?? data_get(session('guest'), 'email', '')) }}"
                            class="form-control" placeholder=" " autocomplete="email" required>
                        <div class="mt-1 text-danger small" id="error_email"></div>
                    </div>

                    <!-- First Name & Last Name -->
                    <div class="form-row-custom">
                        <div>
                            <div class="floating-bordered-input position-relative">
                                <span class="floating-label">First name *</span>
                                <input type="text" id="first_name" name="first_name"
                                    value="{{ old('first_name', session('first_name') ?? data_get(session('guest'), 'first_name', '')) }}" class="form-control"
                                    placeholder=" " autocomplete="given-name" required>
                                <div class="mt-1 text-danger small" id="error_first_name"></div>
                                @error('first_name')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div>
                            <div class="floating-bordered-input position-relative">
                                <span class="floating-label">Last name *</span>
                                <input type="text" id="last_name" name="last_name"
                                    value="{{ old('last_name', session('last_name') ?? data_get(session('guest'), 'last_name', '')) }}" class="form-control"
                                    placeholder=" " autocomplete="family-name" required>
                                <div class="mt-1 text-danger small" id="error_last_name"></div>
                                @error('last_name')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="phone-field-wrapper">
                        <div class="floating-bordered-input position-relative phone-input-wrapper">
                            <span class="floating-label">Phone *</span>
                            <input type="tel" id="number" value="{{ old('number', session('number') ?? data_get(session('guest'), 'number', '')) }}"
                                class="form-control phone-with-country" placeholder=" " autocomplete="tel" required>
                            <input type="hidden" name="number" id="number_full">
                        </div>
                        <div class="phone-error text-danger small mt-1" id="error_number">@error('number'){{ $message
                            }}@enderror</div>
                    </div>

                    <!-- Hidden fields for booking for someone else -->
                    <input type="hidden" name="bookingForSomeoneElse" value="0">
                    <input type="hidden" id="booker_first_name" name="booker_first_name" value="">
                    <input type="hidden" id="booker_last_name" name="booker_last_name" value="">
                    <input type="hidden" id="booker_email" name="booker_email" value="">
                    <input type="hidden" id="booker_number" name="booker_number" value="">
                    <input type="text" name="type" value="guest" hidden>

                    <button type="submit" class="continue-btn btn btn-primary btn-uniform w-100 cta-button"
                        style="text-transform: uppercase;">CONTINUE AS
                        GUEST</button>
                </form>
            </div>
        </div>

        <!-- Right Column: Login/Account Benefits -->
        <div class="mb-4 col-lg-6 col-divider">
            <div class="benefits-section">
                <h2 class="section-title">Login or Create account</h2>

                <!-- Login Form -->
                <form id="loginForm" method="{{ auth()->check() ? 'GET' :  'POST' }}"
                    action="{{ auth()->check() ? '/submit-passengerInfo' : '/login' }}">
                    @csrf

                    <input type="text" name="login_type" value="booking" hidden>
                    @if (auth()->check())
                    {{-- CASE 1: User is Logged In (Authenticated) --}}
                    <h2>Welcome back, {{ auth()->user()->first_name }}!</h2>
                    @else
                    {{-- CASE 2: User is NOT Logged In (Unauthenticated) - Show full login/registration inputs --}}

                    <div class="floating-bordered-input position-relative">
                        <span class="floating-label">Email address</span>
                        <input type="email" id="email_login" name="email" value="{{ old('email') }}"
                            class="form-control" placeholder=" " required autofocus>
                        @error('email')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="register-now">
                        <div class="form-row-custom">
                            <div>
                                <div class="floating-bordered-input position-relative">
                                    <span class="floating-label">First name *</span>
                                    <input type="text" id="first_name" name="first_name"
                                        value="{{ old('first_name', session('first_name')) }}" class="form-control"
                                        placeholder=" " autocomplete="given-name" required>
                                    <div class="mt-1 text-danger small" id="error_first_name"></div>
                                    @error('first_name')<div class="mt-1 text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <div class="floating-bordered-input position-relative">
                                    <span class="floating-label">Last name *</span>
                                    <input type="text" id="last_name" name="last_name"
                                        value="{{ old('last_name', session('last_name')) }}" class="form-control"
                                        placeholder=" " autocomplete="family-name" required>
                                    <div class="mt-1 text-danger small" id="error_last_name"></div>
                                    @error('last_name')<div class="mt-1 text-danger small">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="phone-field-wrapper">
                            <div class="floating-bordered-input position-relative phone-input-wrapper">
                                <span class="floating-label">Phone *</span>
                                <input type="tel" id="phone" value="{{ old('phone', session('phone')) }}"
                                    class="form-control phone-with-country" placeholder=" " autocomplete="tel" required>
                                <input type="hidden" name="phone" id="phone_full">
                            </div>
                            <div class="phone-error text-danger small mt-1" id="error_phone">@error('phone'){{ $message
                                }}@enderror</div>
                        </div>
                    </div>

                    <div class="floating-bordered-input position-relative login-now">
                        <span class="floating-label">Password *</span>
                        <input type="password" id="password" name="password"
                            value="{{ old('password', session('password')) }}" class="form-control" placeholder=" "
                            autocomplete="current-password" required>
                        <div class="mt-1 text-danger small" id="error_password"></div>
                    </div>

                    @endif

                    <input type="text" name="type" value="real" hidden>

                    <button id="continue_right" type="submit"
                        class="login-btn btn btn-primary btn-uniform w-100 cta-button"
                        style="padding: .575rem .75rem !important; text-transform: uppercase;">Continue</button>
                </form>

                <!-- Benefits Section -->
                <div class="login-section">
                    <h3 class="benefits-title">Why do I need an account?</h3>

                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Book rides even faster using stored account details.</span>
                    </div>

                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Modify trip details.</span>
                    </div>

                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Access invoices and payment receipts.</span>
                    </div>

                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Reporting tools.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itiNumber, itiPhone;
        if (typeof window.intlTelInput !== 'undefined') {
            const numInput = document.querySelector('#number');
            const phoneInput = document.querySelector('#phone');
            const dialCodeMap = [
                { dc: '971', iso2: 'ae', max: 9, nanp: false }, { dc: '966', iso2: 'sa', max: 9, nanp: false },
                { dc: '963', iso2: 'sy', max: 9, nanp: false }, { dc: '962', iso2: 'jo', max: 9, nanp: false },
                { dc: '961', iso2: 'lb', max: 8, nanp: false }, { dc: '91', iso2: 'in', max: 10, nanp: false },
                { dc: '92', iso2: 'pk', max: 11, nanp: false }, { dc: '86', iso2: 'cn', max: 11, nanp: false },
                { dc: '81', iso2: 'jp', max: 10, nanp: false }, { dc: '61', iso2: 'au', max: 9, nanp: false },
                { dc: '52', iso2: 'mx', max: 10, nanp: false }, { dc: '49', iso2: 'de', max: 11, nanp: false },
                { dc: '39', iso2: 'it', max: 10, nanp: false }, { dc: '34', iso2: 'es', max: 9, nanp: false },
                { dc: '33', iso2: 'fr', max: 9, nanp: false }, { dc: '44', iso2: 'gb', max: 11, nanp: false },
                { dc: '7', iso2: 'ru', max: 10, nanp: false }, { dc: '1', iso2: 'us', max: 10, nanp: true }
            ];
            function findCountryAndNational(raw) {
                for (let i = 0; i < dialCodeMap.length; i++) {
                    const { dc, iso2, max, nanp } = dialCodeMap[i];
                    if (raw === dc || (raw.startsWith(dc) && raw.length > dc.length)) {
                        return { dialCode: dc, iso2, national: raw.slice(dc.length).slice(0, max), nanp };
                    }
                }
                return null;
            }
            function formatNational(national, nanp) {
                const d = national.replace(/\D/g, '');
                if (nanp) {
                    if (d.length <= 3) return d ? '(' + d : '';
                    if (d.length <= 6) return '(' + d.slice(0, 3) + ') ' + d.slice(3);
                    return '(' + d.slice(0, 3) + ') ' + d.slice(3, 6) + '-' + d.slice(6, 10);
                }
                return d.replace(/(\d{3})(?=\d)/g, '$1 ').trim() || d;
            }
            function restrictAndFormat(iti, input) {
                const raw = input.value.replace(/\D/g, '');
                const digitsBeforeCursor = (input.value.slice(0, input.selectionStart).replace(/\D/g, '')).length;
                if (!raw) return;
                const parsed = findCountryAndNational(raw);
                if (!parsed) return;
                const { dialCode, iso2, national, nanp } = parsed;
                if (iti.getSelectedCountryData().dialCode !== dialCode) iti.setCountry(iso2);
                const formatted = formatNational(national, nanp);
                const display = '+' + dialCode + (formatted ? ' ' + formatted : '');
                if (input.value !== display) {
                    let newPos = display.length;
                    let digitCount = 0;
                    for (let i = 0; i < display.length; i++) {
                        if (/\d/.test(display[i])) digitCount++;
                        if (digitCount >= digitsBeforeCursor) { newPos = i + 1; break; }
                    }
                    input.value = display;
                    input.setSelectionRange(newPos, newPos);
                }
            }
            function enforcePlusPrefix(input) {
                if (!input) return;
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && input.selectionStart <= 1 && input.selectionEnd <= 1) e.preventDefault();
                    if (e.key === 'Delete' && input.selectionStart === 0 && input.selectionEnd === 0) e.preventDefault();
                });
                input.addEventListener('input', function() {
                    if (!input.value.startsWith('+')) input.value = '+' + (input.value.replace(/^\+?/, '') || '');
                });
            }
            if (numInput) {
                itiNumber = window.intlTelInput(numInput, {
                    initialCountry: 'us',
                    separateDialCode: false,
                    preferredCountries: ['us', 'gb', 'ca', 'pk'],
                    dropdownContainer: document.body,
                    utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                    formatOnDisplay: true
                });
                if (!numInput.value.trim()) itiNumber.setNumber('+1');
                enforcePlusPrefix(numInput);
                numInput.addEventListener('input', function() { restrictAndFormat(itiNumber, numInput); });
                numInput.addEventListener('keyup', function() { restrictAndFormat(itiNumber, numInput); });
                numInput.addEventListener('countrychange', function() { restrictAndFormat(itiNumber, numInput); });
            }
            if (phoneInput) {
                itiPhone = window.intlTelInput(phoneInput, {
                    initialCountry: 'us',
                    separateDialCode: false,
                    preferredCountries: ['us', 'gb', 'ca', 'pk'],
                    dropdownContainer: document.body,
                    utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                    formatOnDisplay: true
                });
                if (!phoneInput.value.trim()) itiPhone.setNumber('+1');
                enforcePlusPrefix(phoneInput);
                phoneInput.addEventListener('input', function() { restrictAndFormat(itiPhone, phoneInput); });
                phoneInput.addEventListener('keyup', function() { restrictAndFormat(itiPhone, phoneInput); });
                phoneInput.addEventListener('countrychange', function() { restrictAndFormat(itiPhone, phoneInput); });
            }
        }

        function validateAndPopulatePhone(formId) {
            const isGuest = formId === 'passengerForm';
            const iti = isGuest ? itiNumber : itiPhone;
            const fullInput = document.getElementById(isGuest ? 'number_full' : 'phone_full');
            const errorEl = document.getElementById(isGuest ? 'error_number' : 'error_phone');
            if (!iti || !fullInput) return true;
            const full = iti.getNumber() || '';
            fullInput.value = full;
            const digitCount = (full.match(/\d/g) || []).length;
            if (!full || digitCount < 10) {
                errorEl.innerText = digitCount > 0 ? 'Enter a valid phone number for the selected country.' : 'This field is required.';
                return false;
            }
            const valid = iti.isValidNumber();
            if (valid === false) {
                if (digitCount >= 10 && digitCount <= 15 && full.startsWith('+')) {
                    errorEl.innerText = '';
                    return true;
                }
                errorEl.innerText = 'Enter a valid phone number for the selected country.';
                return false;
            }
            errorEl.innerText = '';
            return true;
        }

        function capitalizeFirstLetter(el) {
            const start = el.selectionStart, end = el.selectionEnd;
            const val = el.value;
            if (val.length > 0) {
                el.value = val.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
                el.setSelectionRange(Math.min(start, el.value.length), Math.min(end, el.value.length));
            }
        }
        document.querySelectorAll('#passengerForm input[name="first_name"], #passengerForm input[name="last_name"]').forEach(function(input) {
            input.addEventListener('input', function() { capitalizeFirstLetter(this); });
            input.addEventListener('blur', function() { capitalizeFirstLetter(this); });
        });
        document.querySelectorAll('#loginForm input[name="first_name"], #loginForm input[name="last_name"]').forEach(function(input) {
            input.addEventListener('input', function() { capitalizeFirstLetter(this); });
            input.addEventListener('blur', function() { capitalizeFirstLetter(this); });
        });

        const form = document.getElementById('passengerForm');

        form.addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('#passengerForm .text-danger').forEach(el => el.innerHTML = '');

            const requiredFields = ['first_name', 'last_name', 'email'];
            requiredFields.forEach(name => {
                const input = document.querySelector('#passengerForm').querySelector(`[name="${name}"]`);
                const errorEl = document.getElementById(`error_${name}`);
                if (input && !input.value.trim()) {
                    errorEl.innerText = 'This field is required.';
                    isValid = false;
                } else if (name === 'email' && input && !/^\S+@\S+\.\S+$/.test(input.value)) {
                    errorEl.innerText = 'Enter a valid email address.';
                    isValid = false;
                }
            });

            if (!validateAndPopulatePhone('passengerForm')) isValid = false;

            if (!isValid) {
                e.preventDefault();
            } else {
                if (typeof $ !== 'undefined' && $('#loader').length) {
                    $('#loader').show();
                }
            }
        });

        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            if ({{ auth()->check() ? 'true' : 'false' }}) return;
            const btnText = $('.login-btn').text().toLowerCase();
            if (btnText === 'continue') return; // first step, just check email

            e.preventDefault();
            document.querySelectorAll('#loginForm .text-danger').forEach(el => el.innerText = '');

            let isValid = true;
            const email = $('#email_login').val().trim();
            if (!email) {
                $('#loginForm .floating-bordered-input').first().find('.text-danger').first().text('Email is required.');
                isValid = false;
            }

            const isRegisterMode = btnText === 'register';
            const isLoginMode = btnText === 'login';

            if (isRegisterMode) {
                ['first_name','last_name'].forEach(name => {
                    const inp = document.querySelector(`#loginForm [name="${name}"]`);
                    const err = document.getElementById(`error_${name}`);
                    if (inp && err && !inp.value.trim()) {
                        err.innerText = 'This field is required.';
                        isValid = false;
                    }
                });
                if (!validateAndPopulatePhone('loginForm')) isValid = false;
            }

            if ($('.login-now').is(':visible') && !$('#password').val()) {
                $('#error_password').text('Password is required.');
                isValid = false;
            }

            if (isValid) this.submit();
        });

        // Save guest/login form data to session when user navigates away (preserves input on step change)
        function saveGuestFormToSession(callback) {
            const guestForm = document.getElementById('passengerForm');
            const loginForm = document.getElementById('loginForm');
            let email = '', firstName = '', lastName = '', numberVal = '';
            if (guestForm) {
                email = document.getElementById('guest_email')?.value || '';
                firstName = guestForm.querySelector('[name="first_name"]')?.value || '';
                lastName = guestForm.querySelector('[name="last_name"]')?.value || '';
                if (typeof itiNumber !== 'undefined' && itiNumber) {
                    numberVal = itiNumber.getNumber() || document.getElementById('number_full')?.value || '';
                } else {
                    numberVal = document.getElementById('number')?.value || document.getElementById('number_full')?.value || '';
                }
            }
            if (loginForm && (!firstName || !lastName)) {
                const lf = loginForm.querySelector('[name="first_name"]')?.value;
                const ll = loginForm.querySelector('[name="last_name"]')?.value;
                if (lf) firstName = lf;
                if (ll) lastName = ll;
                if (loginForm.querySelector('[name="email"]')) email = email || loginForm.querySelector('[name="email"]').value;
                if (typeof itiPhone !== 'undefined' && itiPhone) {
                    numberVal = numberVal || itiPhone.getNumber() || document.getElementById('phone_full')?.value || '';
                } else {
                    numberVal = numberVal || document.getElementById('phone')?.value || document.getElementById('phone_full')?.value || '';
                }
            }
            const data = new FormData();
            data.append('form_type', 'guest_info');
            data.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || (guestForm?.querySelector('input[name="_token"]')?.value) || '');
            data.append('email', email);
            data.append('first_name', firstName);
            data.append('last_name', lastName);
            data.append('number', numberVal);
            fetch('/save-booking-form-session', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(() => { if (callback) callback(); })
                .catch(() => { if (callback) callback(); });
        }
        let saveDebounce;
        document.querySelectorAll('#passengerForm input[name="email"], #passengerForm input[name="first_name"], #passengerForm input[name="last_name"], #passengerForm #number, #loginForm input[name="email"], #loginForm input[name="first_name"], #loginForm input[name="last_name"], #loginForm #phone').forEach(el => {
            if (el) el.addEventListener('blur', function() {
                clearTimeout(saveDebounce);
                saveDebounce = setTimeout(() => saveGuestFormToSession(), 400);
            });
        });
        document.querySelectorAll('a.trigger-loader[href], a.step[href]').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && !href.startsWith('#')) {
                    e.preventDefault();
                    saveGuestFormToSession(() => { window.location.href = href; });
                }
            });
        });

        $('#continue_right').click(function(e) {
            if($('.login-btn').text().toLowerCase() === 'continue' && !{{ auth()->check() ? 'true' : 'false' }}){
                e.preventDefault();
                let email = $('#email_login').val().trim();
                if (email === '') { alert('Please enter your email address.'); return; }
                $.ajax({
                    url: '/check-email-exists',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ email: email }),
                    success: function(response) {
                        if (response.exists) {
                            $('.register-now').hide();
                            $('.login-now').show();
                            $('.login-btn').text('Login');
                            $('#loginForm').attr('action', '/login');
                            $('#loginForm [name="first_name"], #loginForm [name="last_name"], #loginForm #phone')
                                .prop('required', false)
                                .val('');
                        } else {
                            $('.login-now').show();
                            $('.register-now').show();
                            $('.login-btn').text('Register');
                            $('#loginForm').attr('action', '/register');
                            $('#loginForm [name="first_name"], #loginForm [name="last_name"], #loginForm #phone')
                                .prop('required', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Something went wrong. Please try again.');
                    }
                });
            }
        });
    });
</script>
@endsection