@php
$isHourly = session('service_type') === 'hourlyHire';
$tabSuffix = $id_suffix ?? '';
@endphp
<div class="search-tab-wrap">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" style="padding-bottom: 8px;">
        <li class="nav-item" style="flex: 1">
            <a class="nav-link {{ !$isHourly ? 'active' : 'inactive-tab' }} text-center pt-0 sformlink"
                style="font-size: 12px !important; font-weight: 600;" data-bs-toggle="tab"
                href="#place{{ $tabSuffix }}">POINT TO POINT</a>
        </li>
        <li class="nav-item" style="flex: 1">
            <a class="nav-link {{ $isHourly ? 'active' : 'inactive-tab' }} text-center pt-0 sformlink"
                style="font-size: 12px !important; font-weight: 600;" data-bs-toggle="tab"
                href="#event{{ $tabSuffix }}">HOURLY</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Point to Point -->
        <div class="tab-pane container p-0 {{ !$isHourly ? 'active show' : '' }}" id="place{{ $tabSuffix }}">
            <div class="search-form-box">
                <form class="ride-info-form" action="/save-booking-form-session" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="ride_info_point_to_point">
                    <input type="hidden" name="is_airport" id="is-airport{{ $tabSuffix }}"
                        value="{{ session('is_airport') ?? 0 }}">

                    <!-- Pick-up Location -->
                    <div class="floating-bordered-input position-relative">
                        <span class="floating-label">Pick-up Location</span>
                        <span class="input-icon-left"><svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"
                                fill="#000000">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill="#757575"
                                        d="M32,0C18.746,0,8,10.746,8,24c0,5.219,1.711,10.008,4.555,13.93c0.051,0.094,0.059,0.199,0.117,0.289l16,24 C29.414,63.332,30.664,64,32,64s2.586-0.668,3.328-1.781l16-24c0.059-0.09,0.066-0.195,0.117-0.289C54.289,34.008,56,29.219,56,24 C56,10.746,45.254,0,32,0z M32,32c-4.418,0-8-3.582-8-8s3.582-8,8-8s8,3.582,8,8S36.418,32,32,32z">
                                    </path>
                                </g>
                            </svg></span>

                        <input type="text" name="pickup_location" id="pickup-location{{ $tabSuffix }}"
                            class="form-control" value="{{ session('pickup_location') }}" placeholder=" " required
                            autocomplete="off">
                        <span id="swap-locations{{ $tabSuffix }}" class="swap-locations"
                            style="cursor: pointer; position: absolute; right: 0; top: 27%; z-index: 1; background: white; padding: 0 10px;">
                            <svg width="18px" height="18px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" transform="rotate(270)">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M16 3.93a.75.75 0 0 1 1.177-.617l4.432 3.069a.75.75 0 0 1 0 1.233l-4.432 3.069A.75.75 0 0 1 16 10.067V8H4a1 1 0 0 1 0-2h12V3.93zm-9.177 9.383A.75.75 0 0 1 8 13.93V16h12a1 1 0 1 1 0 2H8v2.067a.75.75 0 0 1-1.177.617l-4.432-3.069a.75.75 0 0 1 0-1.233l4.432-3.069z"
                                        fill="#757575"></path>
                                </g>
                            </svg>
                        </span>
                        <!-- Suggestions -->
                        <div id="pickup-suggestions{{ $tabSuffix }}" class="location-suggestions"></div>
                    </div>


                    <!-- Drop-off Location -->
                    <div class="floating-bordered-input position-relative">
                        <span class="floating-label">Destination</span>
                        <span class="input-icon-left"><svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"
                                fill="#000000">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill="#757575"
                                        d="M32,0C18.746,0,8,10.746,8,24c0,5.219,1.711,10.008,4.555,13.93c0.051,0.094,0.059,0.199,0.117,0.289l16,24 C29.414,63.332,30.664,64,32,64s2.586-0.668,3.328-1.781l16-24c0.059-0.09,0.066-0.195,0.117-0.289C54.289,34.008,56,29.219,56,24 C56,10.746,45.254,0,32,0z M32,32c-4.418,0-8-3.582-8-8s3.582-8,8-8s8,3.582,8,8S36.418,32,32,32z">
                                    </path>
                                </g>
                            </svg></span>

                        <input type="text" name="dropoff_location" id="dropoff-location{{ $tabSuffix }}"
                            class="form-control" value="{{ session('dropoff_location') }}" placeholder=" " required
                            autocomplete="off">

                        <!-- Suggestions -->
                        <div id="dropoff-suggestions{{ $tabSuffix }}" class="location-suggestions"></div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="mb-1 floating-bordered-input position-relative">
                                <span class="floating-label">Pick-up Date</span>
                                <span class="input-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 384 432">
                                        <path fill="currentColor"
                                            d="M299 240v107H192V240h107zM277 5h43v43h21q18 0 30.5 12.5T384 91v298q0 18-12.5 30.5T341 432H43q-18 0-30.5-12.5T0 389V91q0-18 12.5-30.5T43 48h21V5h43v43h170V5zm64 384V155H43v234h298z" />
                                    </svg>
                                </span>
                                <input type="date" id="pickup-date{{ $tabSuffix }}" name="pickup_date"
                                    class="form-control" value="{{ session('pickup_date') ?? '' }}" required>
                                @error('pickup_date')
                                <div class="mt-1 text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-1 floating-bordered-input position-relative">
                                <span class="floating-label">Pick-up Time</span>
                                <span class="input-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                </span>
                                <input type="time" id="pickup-time{{ $tabSuffix }}" name="pickup_time"
                                    class="form-control" value="{{ session('pickup_time') ?? '' }}" required>
                                @error('pickup_time')
                                <div class="mt-1 text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-1 d-flex align-items-center">
                        <div class="form-check me-2">
                            <input type="checkbox" name="round_trip" id="round-trip{{ $tabSuffix }}"
                                class="form-check-input"
                                style="height: 18px; width: 18px; cursor: pointer; margin-top: 10px; border: 0.13rem solid #6e6e6e; border-radius: 2px !important;"
                                @session('round_trip') checked @endsession>
                            <label for="round-trip{{ $tabSuffix }}" class="mb-2 ml-2 form-check-label ms-2"
                                style="cursor: pointer; font-size: 14px; margin-top: 0.4rem; color: black !important; font-weight: 400">
                                Add a return Trip
                            </label>
                        </div>
                    </div>

                    <div class="return-trip" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="mb-1 floating-bordered-input position-relative">
                                    <span class="floating-label">Return Trip Pick-up Date</span>
                                    <span class="input-icon-left">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 384 432">
                                            <path fill="currentColor"
                                                d="M299 240v107H192V240h107zM277 5h43v43h21q18 0 30.5 12.5T384 91v298q0 18-12.5 30.5T341 432H43q-18 0-30.5-12.5T0 389V91q0-18 12.5-30.5T43 48h21V5h43v43h170V5zm64 384V155H43v234h298z" />
                                        </svg>
                                    </span>
                                    <input type="date" id="return-date{{ $tabSuffix }}" name="return_date"
                                        class="form-control" value="{{ session('return_date') ?? '' }}" placeholder=" ">
                                    @error('return_date')
                                    <div class="mt-1 text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1 floating-bordered-input position-relative">
                                    <span class="floating-label">Return Trip Pick-up Time</span>
                                    <span class="input-icon-left">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                    </span>
                                    <input type="time" id="return-time{{ $tabSuffix }}" name="return_time"
                                        class="form-control"
                                        value="{{ session('return_time') ? \Carbon\Carbon::parse(session('return_time'))->format('H:i') : '' }}"
                                        placeholder=" ">
                                    @error('return_time')
                                    <div class="mt-1 text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .input-icon-right {
                            position: absolute;
                            right: 15px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: #757575;
                        }

                        @supports (-webkit-overflow-scrolling: touch) {
                            .swap-locations {
                                transform: rotate(90deg);
                                transform-origin: 50% 50%;
                                display: inline-block;
                                -webkit-transform: rotate(90deg);
                                /* iOS-specific */
                            }
                        }
                    </style>

                    <button type="submit" class="btn btn-primary w-100 search_btn point-button"
                        style="text-transform: uppercase; letter-spacing: 2px; border-radius: 4px; font-size: 0.8rem;line-height: 2;">
                        Get My Prices
                        <i class="fa-solid fa-arrow-right" style="font-size: 14px; margin: 2px;"></i>
                    </button>

                </form>
            </div>
        </div>

        <!-- Hourly Hire -->
        <div class="tab-pane container p-0 {{ $isHourly ? 'active show' : '' }}" id="event{{ $tabSuffix }}">
            <div class="search-form-box">
                <form class="ride-info-form" action="/save-booking-form-session" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="ride_info_hourly">
                    <!-- Pick-up Location (Hourly) -->
                    <div class="floating-bordered-input position-relative">
                        <span class="floating-label">Pick-up Location</span>
                        <span class="input-icon-left"><svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px"
                                viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"
                                fill="#000000">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path fill="#757575"
                                        d="M32,0C18.746,0,8,10.746,8,24c0,5.219,1.711,10.008,4.555,13.93c0.051,0.094,0.059,0.199,0.117,0.289l16,24 C29.414,63.332,30.664,64,32,64s2.586-0.668,3.328-1.781l16-24c0.059-0.09,0.066-0.195,0.117-0.289C54.289,34.008,56,29.219,56,24 C56,10.746,45.254,0,32,0z M32,32c-4.418,0-8-3.582-8-8s3.582-8,8-8s8,3.582,8,8S36.418,32,32,32z">
                                    </path>
                                </g>
                            </svg></span>

                        <input type="text" name="pickup_location_hourly" id="pickup-location-hourly{{ $tabSuffix }}"
                            class="form-control" value="{{ session('pickup_location', '') }}" placeholder=" " required
                            autocomplete="off">

                        <!-- Suggestions -->
                        <div id="pickup-location-hourly-suggestions{{ $tabSuffix }}" class="location-suggestions"></div>
                    </div>


                    <!-- Select Hours -->
                    <div class="floating-bordered-input position-relative rlx-theme">
                        <span class="floating-label">Select Duration</span>
                        <span class="input-icon-left"><i class="bi bi-clock-fill"></i></span>

                        <div class="rlx-select" id="rlx-hours{{ $tabSuffix }}" data-name="select_hours"
                            data-initial="{{ session('select_hours') ?? '' }}">
                            <button type="button" class="rlx-trigger" aria-haspopup="listbox" aria-expanded="false">
                                <span class="rlx-value">{{ session('select_hours') ? session('select_hours').'
                                    hour'.(session('select_hours') > 1 ? 's' : '') : '' }}</span>
                                <svg class="rlx-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    aria-hidden="true">
                                    <path
                                        d="M6.73 9.27a1 1 0 0 1 1.41 0L12 13.12l3.86-3.85a1 1 0 0 1 1.41 1.41l-4.57 4.57a1 1 0 0 1-1.41 0L6.73 10.68a1 1 0 0 1 0-1.41Z"
                                        fill="currentColor" />
                                </svg>
                            </button>
                            <ul class="rlx-list" role="listbox" tabindex="-1">
                                @foreach (range(3, 12) as $hour)
                                <li role="option"
                                    class="rlx-option {{ session('select_hours') == $hour ? 'selected' : '' }}"
                                    aria-selected="{{ session('select_hours') == $hour ? 'true' : 'false' }}"
                                    data-value="{{ $hour }}">
                                    {{ $hour }} hour{{ $hour > 1 ? 's' : '' }}
                                </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="select_hours" value="{{ session('select_hours') ?? '' }}">
                        </div>
                    </div>


                    <div class="row g-2">
                        <div class="col-6">
                            <div class="mb-1 floating-bordered-input position-relative">
                                <span class="floating-label">Pick-up Date</span>
                                <span class="input-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 384 432">
                                        <path fill="currentColor"
                                            d="M299 240v107H192V240h107zM277 5h43v43h21q18 0 30.5 12.5T384 91v298q0 18-12.5 30.5T341 432H43q-18 0-30.5-12.5T0 389V91q0-18 12.5-30.5T43 48h21V5h43v43h170V5zm64 384V155H43v234h298z" />
                                    </svg>
                                </span>
                                <input type="date" name="pickup_date" id="pickup-date-hourly{{ $tabSuffix }}"
                                    class="form-control" value="{{ session('pickup_date') ?? '' }}" required>
                                @error('pickup_date')
                                <div class="mt-1 text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-1 floating-bordered-input position-relative">
                                <span class="floating-label">Pick-up Time</span>
                                <span class="input-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                </span>
                                <input type="time" name="pickup_time" id="pickup-time-hourly{{ $tabSuffix }}"
                                    class="form-control" value="{{ session('pickup_time') ?? '' }}" required>
                                @error('pickup_time')
                                <div class="mt-1 text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary w-100 search_btn point-button"
                        style="text-transform: uppercase; letter-spacing: 2px; border-radius: 4px; font-size: 0.8rem;line-height: 2;">
                        Get My Prices
                        <i class="fa-solid fa-arrow-right" style="font-size: 14px; margin: 2px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Ridelux-style custom select */
    .rlx-select {
        position: relative;
        width: 100%;
    }

    .rlx-select .rlx-trigger {
        display: block;
        position: relative;
        width: 100%;
        background: transparent;
        border: none;
        border-radius: 0;
        padding: 0 44px 0 44px;
        margin-top: 10px;
        color: #1f2937;
        font-size: 16px;
        line-height: 1.45;
        min-height: 42px;
        text-align: left;
    }

    .floating-bordered-input .rlx-select .rlx-trigger {
        margin-top: 10px;
    }

    /* Keep duration trigger content vertically centered */
    .floating-bordered-input.rlx-theme .rlx-select .rlx-trigger {
        display: flex;
        align-items: center;
        min-height: 46px;
        padding: 0 36px 0 0 !important;
        padding-bottom: 0 !important;
    }

    .rlx-select .rlx-value {
        pointer-events: none;
    }

    .rlx-select .rlx-arrow {
        color: #6b7280;
        position: absolute;
        right: 8px !important;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* Move duration dropdown arrow to input's far right edge */
    .floating-bordered-input.rlx-theme .rlx-select .rlx-arrow {
        right: -28px !important;
    }

    .rlx-select.open .rlx-arrow {
        transform: translateY(-50%) rotate(180deg);
        transition: transform .18s ease;
    }

    .rlx-select .rlx-list {
        position: absolute;
        top: calc(100% + 6px);
        left: -40px;
        right: -40px;
        background: #fff;
        border: 1px solid #e6eaef;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .12);
        max-height: 360px;
        overflow-y: auto;
        padding: 6px 0;
        opacity: 0;
        transform: translateY(-6px);
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease;
        z-index: 1050;
        overscroll-behavior: contain;
    }

    .rlx-select.open .rlx-list {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .rlx-option {
        padding: 12px 16px;
        font-size: 16px;
        line-height: 1.5;
        color: #1f2937;
        cursor: pointer;
    }

    .rlx-option:hover,
    .rlx-option[aria-selected="true"] {
        background: #eef6fb;
    }

    .rlx-option.selected {
        background: #e9f2fa;
    }

    /* icon alignment inside floating input */
    .floating-bordered-input .input-icon-left {
        font-size: 18px;
        color: #6b7280;
    }

    .rlx-theme {
        border-radius: 4px;
    }

    .rlx-theme:hover,
    .rlx-theme:focus-within {
        box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
    }

    /* Scrollbar styling for dropdown list */
    .rlx-list::-webkit-scrollbar {
        width: 6px;
    }

    .rlx-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .rlx-list::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 20px;
    }

    .rlx-list::-webkit-scrollbar-thumb:hover {
        background: #b4c0cc;
    }

    /* Hide native date/time picker icon (2nd icon) */
    input[type="date"]::-webkit-calendar-picker-indicator,
    input[type="time"]::-webkit-calendar-picker-indicator {
        display: none !important;
        -webkit-appearance: none;
    }
</style>

<script>
    // Accessible custom select for hours
(function(){
  function initRlxSelect(rootId) {
    const root = document.getElementById(rootId);
    if (!root) return;
    const trigger = root.querySelector('.rlx-trigger');
    const list = root.querySelector('.rlx-list');
    const valueEl = root.querySelector('.rlx-value');
    const hidden = root.querySelector('input[type="hidden"]');
    const options = Array.from(root.querySelectorAll('.rlx-option'));
    let highlightedIndex = options.findIndex(o => o.classList.contains('selected'));

    function open() {
      root.classList.add('open');
      trigger.setAttribute('aria-expanded', 'true');
      list.focus({ preventScroll: true });
      if (highlightedIndex < 0) highlightedIndex = 0;
      highlight(highlightedIndex);
    }
    function close() {
      root.classList.remove('open');
      trigger.setAttribute('aria-expanded', 'false');
    }
    function selectByIndex(i) {
      const opt = options[i];
      if (!opt) return;
      options.forEach(o => { o.classList.remove('selected'); o.setAttribute('aria-selected','false'); });
      opt.classList.add('selected');
      opt.setAttribute('aria-selected','true');
      highlightedIndex = i;
      const val = opt.getAttribute('data-value');
      hidden.value = val;
      valueEl.textContent = opt.textContent.trim();
      close();
      trigger.focus();
    }
    function highlight(i) {
      highlightedIndex = Math.max(0, Math.min(options.length-1, i));
      options.forEach((o, idx) => {
        o.tabIndex = idx === highlightedIndex ? 0 : -1;
      });
      options[highlightedIndex]?.focus({ preventScroll: true });
    }

    trigger.addEventListener('click', function(e){
      e.preventDefault();
      if (root.classList.contains('open')) { close(); } else { open(); }
    });
    options.forEach((opt, idx) => {
      opt.addEventListener('click', () => selectByIndex(idx));
      opt.addEventListener('mousemove', () => { highlightedIndex = idx; });
    });
    document.addEventListener('click', function(e){
      if (!root.contains(e.target)) close();
    });
    root.addEventListener('keydown', function(e){
      if (!root.classList.contains('open')) return;
      if (e.key === 'ArrowDown') { e.preventDefault(); highlight(highlightedIndex+1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); highlight(highlightedIndex-1); }
      else if (e.key === 'Enter') { e.preventDefault(); selectByIndex(highlightedIndex); }
      else if (e.key === 'Escape') { e.preventDefault(); close(); trigger.focus(); }
    });
  }
  document.addEventListener('DOMContentLoaded', function(){
    initRlxSelect('rlx-hours{{ $tabSuffix }}');
  });
})();
</script>