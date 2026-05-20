let autocompletePickup, autocompleteDropoff, autocompletePickupHourly;
let map, directionsService, directionsRenderer;
let pickupMarker = null,
    dropoffMarker = null; // Global marker references
let distanceInKm = 0;
let stopInputs = [];
let stopAutocompletes = []; // store autocomplete instances
let animationPath = [];
let animationProgress = 0;
let animationId = null;

const options = {
    fields: ["address_components", "geometry", "icon", "name"],
    strictBounds: true,
    componentRestrictions: { country: "us" },
    types: ["geocode", "establishment"],
};

/** Single-point fitBounds zooms extremely tight on phones; use explicit zoom instead. */
function isHomeSearchMapMobileViewport() {
    return typeof $ !== "undefined" && $(window).width() < 768;
}

function getHomeSearchMapSinglePlaceZoom() {
    return isHomeSearchMapMobileViewport() ? 10 : 12;
}

function geolocate() {}

function resetMap() {
    const mapElement = document.getElementById("map");
    $(window).width() < 768 ? $(".mobile-hero").show() : $(".web-hero").show();
    if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }
    animationPath = [];
    animationProgress = 0;

    if (window.carMarker) {
        try {
            window.carMarker.setMap(null);
        } catch (e) {}
        window.carMarker = null;
    }
    window.lastCarPosition = null;

    if (pickupMarker) {
        try {
            pickupMarker.setMap(null);
        } catch (e) {}
        pickupMarker = null;
    }
    if (dropoffMarker) {
        try {
            dropoffMarker.setMap(null);
        } catch (e) {}
        dropoffMarker = null;
    }

    if (directionsRenderer) {
        try {
            directionsRenderer.setMap(null);
        } catch (e) {}
        directionsRenderer = null;
    }
    directionsService = null;

    if (map) {
        try {
            google.maps.event.clearInstanceListeners(map);
        } catch (e) {}
        map = null;
    }

    if (mapElement) {
        mapElement.innerHTML = '<div class="map-overlay"></div>';
        mapElement.style.removeProperty("background-image");
        mapElement.style.removeProperty("background-size");
        mapElement.style.removeProperty("background-position");
        mapElement.style.removeProperty("background-repeat");
        mapElement.style.removeProperty("display");
    }

    // Show the header text content again
    $("#home-text-content").css("visibility", "visible");
    $("#home-text-content").css("pointer-events", "auto");

    pickupPlacePoint = null;
    dropoffPlacePoint = null;

    const infoBox = document.getElementById("route-info-box");
    if (infoBox) infoBox.style.display = "none";

    if (typeof $ !== "undefined") {
        try {
            $("#hide_on_map").show();
        } catch (e) {}
    }
}

window.resetMap = resetMap;

const dateInputs = document.querySelectorAll('input[type="date"]');
dateInputs.forEach((input) => {
    input.addEventListener("click", function () {
        this.showPicker?.();
    });
});

const timeInputs = document.querySelectorAll('input[type="time"]');
timeInputs.forEach((input) => {
    input.addEventListener("click", function () {
        this.showPicker?.();
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const $pickup = $(
        "#pickup-location, #pickup-location_mobile, #pickup-location_form",
    );
    const $dropoff = $(
        "#dropoff-location, #dropoff-location_mobile, #dropoff-location_form",
    );
    const $hourly = $(
        "#pickup-location-hourly, #pickup-location-hourly_mobile, #pickup-location-hourly_form",
    );
    const map = document.getElementById("map");
    const mapOverlay = document.querySelector(".map-overlay");
    let locChangeTimer;

    // Only react to actual user input (not focus/blur). Avoid 'change' to prevent blur-triggered logic.
    $pickup.add($dropoff).on("input", function () {
        clearTimeout(locChangeTimer);
        locChangeTimer = setTimeout(function () {
            onLocationChanged();
        }, 200);
    });

    $(
        "#pickup-location, #dropoff-location, #pickup-location_mobile, #dropoff-location_mobile, #pickup-location_form, #dropoff-location_form",
    ).on("keyup", function () {
        const activePickup =
            $("#pickup-location").val() ||
            $("#pickup-location_mobile").val() ||
            $("#pickup-location_form").val() ||
            "";
        const activeDropoff =
            $("#dropoff-location").val() ||
            $("#dropoff-location_mobile").val() ||
            $("#dropoff-location_form").val() ||
            "";
        const pickupEmpty = activePickup.trim() === "";
        const dropoffEmpty = activeDropoff.trim() === "";

        // When both fields are empty → show image overlay, but don't re-create it if it already exists
        if (pickupEmpty && dropoffEmpty) {
            pickupPlacePoint = null;
            dropoffPlacePoint = null;
            resetMap();
        } else {
            // When at least one field has value → show real map
            if (map) {
                const overlay = map.querySelector(".map-overlay");
                if (overlay) overlay.remove(); // cleanly remove overlay if present

                // Remove fallback image styles
                map.style.removeProperty("background-image");
                map.style.removeProperty("background-size");
                map.style.removeProperty("background-position");
                map.style.removeProperty("background-repeat");
            }
        }
    });

    // Hourly: show map when field has value (but don't hide heading text yet)
    $(
        "#pickup-location-hourly, #pickup-location-hourly_mobile, #pickup-location-hourly_form",
    ).on("keyup", function () {
        const hourlyVal = $(this).val().trim();
        if (hourlyVal === "") {
            resetMap();
        } else {
            // Show map without hiding text
            const mapElement = document.getElementById("map");
            if (mapElement) {
                const overlay = mapElement.querySelector(".map-overlay");
                if (overlay) overlay.remove();
                mapElement.style.removeProperty("background-image");
                mapElement.style.removeProperty("background-size");
                mapElement.style.removeProperty("background-position");
                mapElement.style.removeProperty("background-repeat");
                mapElement.style.display = "block";
                $("#hide_on_map").hide();
                // Do not hide home-text-content here; let initMap handle it when place is selected
            }
        }
    });

    // Hourly pickup: when field is cleared, remove the map
    $hourly.on("input keyup", function () {
        const hourlyEmpty = $hourly.val().trim() === "";
        if (hourlyEmpty) {
            resetMap();
        }
    });
});

function triggerPlaceChangedIfPrefilled() {
    setTimeout(() => {
        try {
            const geocoder = new google.maps.Geocoder();

            const pickupInput = document.getElementById("pickup-location");
            const dropoffInput = document.getElementById("dropoff-location");

            const pickupAddress = pickupInput?.value?.trim();
            const dropoffAddress = dropoffInput?.value?.trim();

            if (!pickupAddress && !dropoffAddress) return; // No need to do anything if both are empty

            const geocodePromise = (address) => {
                return new Promise((resolve) => {
                    if (!address) {
                        resolve(null);
                    } else {
                        geocoder.geocode({ address }, (results, status) => {
                            if (status === "OK" && results[0]) {
                                resolve(results[0]);
                            } else {
                                console.warn(
                                    `Geocode failed for address "${address}":`,
                                    status,
                                );
                                resolve(null);
                            }
                        });
                    }
                });
            };

            Promise.all([
                geocodePromise(pickupAddress),
                geocodePromise(dropoffAddress),
            ]).then(([pickupPlace, dropoffPlace]) => {
                pickupPlacePoint = pickupPlace;
                dropoffPlacePoint = dropoffPlace;

                if (pickupPlace) {
                    const isAirport =
                        pickupPlace.formatted_address
                            .toLowerCase()
                            .includes("airport") ||
                        pickupPlace.address_components?.some(
                            (c) =>
                                c.long_name.toLowerCase().includes("airport") ||
                                c.short_name.toLowerCase().includes("airport"),
                        );
                    document.getElementById("is-airport").value = isAirport
                        ? "1"
                        : "0";
                } else {
                    document.getElementById("is-airport").value = "0";
                }

                document.getElementById("map").style.display = "block";
                // Hide header text when map is shown via prefill
                if (document.getElementById("home-text-content")) {
                    document.getElementById(
                        "home-text-content",
                    ).style.visibility = "hidden";
                    document.getElementById(
                        "home-text-content",
                    ).style.pointerEvents = "none";
                }

                initMap(pickupPlace, dropoffPlace); // Show whatever location is available
            });
        } catch (exception) {}
    }, 1);
}

// Improved date input handling for mobile devices
document.addEventListener("DOMContentLoaded", function () {
    const isIOS =
        /iPad|iPhone|iPod/.test(navigator.userAgent) ||
        (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1);

    const dateInputs = document.querySelectorAll('input[type="date"]');
    const dateDisplays = document.querySelectorAll(".date-display");

    // Format date for display
    function formatDateForDisplay(dateString) {
        if (!dateString) return "";
        const options = {
            weekday: "short",
            year: "numeric",
            month: "short",
            day: "numeric",
        };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }

    // Initialize date displays
    dateDisplays.forEach((display, index) => {
        if (dateInputs[index] && dateInputs[index].value) {
            display.value = formatDateForDisplay(dateInputs[index].value);
        }
    });

    // Handle date changes
    dateInputs.forEach((input, index) => {
        if (isIOS) {
            // For iOS, we'll use the native date picker but with better handling
            input.addEventListener("change", function () {
                if (dateDisplays[index]) {
                    dateDisplays[index].value = formatDateForDisplay(
                        this.value,
                    );
                }
            });

            // Make the entire date display area clickable on iOS
            if (dateDisplays[index]) {
                dateDisplays[index].addEventListener("click", function (e) {
                    e.preventDefault();
                    input.focus();
                    // Trigger the date picker
                    input.showPicker ? input.showPicker() : input.click();
                });

                // Make sure the display is visible on iOS
                dateDisplays[index].style.pointerEvents = "auto";
                dateDisplays[index].style.backgroundColor = "#fff";
            }
        } else {
            // For non-iOS devices, keep the original behavior
            input.addEventListener("change", function () {
                if (dateDisplays[index]) {
                    dateDisplays[index].value = formatDateForDisplay(
                        this.value,
                    );
                }
            });
        }
    });
});

function getBootstrapIconForPlace(place = {}) {
    const types = place.types || [];
    const name = place.name?.toLowerCase() || "";
    const mainText =
        place.structured_formatting?.main_text?.toLowerCase() || "";
    const secondaryText =
        place.structured_formatting?.secondary_text?.toLowerCase() || "";
    const description = place.description?.toLowerCase() || "";
    const termsText = (place.terms || [])
        .map((term) => term?.value || "")
        .join(" ")
        .toLowerCase();
    const addressComponents = place.address_components || [];

    // Check types for airport
    if (types.includes("airport")) {
        return "bi-airplane";
    }

    // Check name for airport
    if (name.includes("airport")) {
        return "bi-airplane";
    }

    // Check address components for airport
    for (const comp of addressComponents) {
        const longName = comp.long_name.toLowerCase();
        const shortName = comp.short_name.toLowerCase();
        if (longName.includes("airport") || shortName.includes("airport")) {
            return "bi-airplane";
        }
    }

    // Check for hotel/lodging suggestions
    const hotelKeywords = [
        "hotel",
        "motel",
        "resort",
        "inn",
        "suites",
        "suite",
        "lodging",
    ];
    if (types.includes("lodging") || types.includes("hotel")) {
        return "bi-building";
    }
    const searchablePlaceText = [
        name,
        mainText,
        secondaryText,
        description,
        termsText,
        types.join(" ").toLowerCase(),
    ]
        .filter(Boolean)
        .join(" ");
    if (
        hotelKeywords.some((keyword) => searchablePlaceText.includes(keyword))
    ) {
        return "bi-building";
    }

    // Default icon for everything else
    return "bi-geo-alt";
}

function getPlaceIconSvg(place = {}) {
    const iconClass = getBootstrapIconForPlace(place);

    if (iconClass === "bi-airplane") {
        const getAirplaneIcon = (s = "18px", c = "black") =>
            `<svg width="${s}" height="${s}" viewBox="0 0 128 128" fill="${c}" xmlns="http://www.w3.org/2000/svg"><path d="M119.7,18.2c7.8-7.8-3-17.9-10.7-10.3L80.7,36.3L15.8,19.2L5,30l53.5,28.2L36.8,79.8L20,77.7l-8.6,8.6l19.1,10l10,19.1l8.6-8.6l-2-16.7l21.6-21.6l27.6,53.2l10.8-10.8L90.8,47.2L119.7,18.2z"/></svg>`;

        // CHANGE THIS LINE: Add the parentheses to execute the function
        return getAirplaneIcon();
    }

    if (iconClass === "bi-building") {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M14 14V1.5a.5.5 0 0 0-.757-.429L10 3 6.757 1.071A.5.5 0 0 0 6 1.5V4L2.757 2.071A.5.5 0 0 0 2 2.5V14H1v1h14v-1zM3 3.383l2 1.2V14H3zm3 1.2 3-1.8V14H6zm4 0 3-1.8V14h-3z"/><path d="M4 6h1v1H4zm0 2h1v1H4zm0 2h1v1H4zm3-4h1v1H7zm0 2h1v1H7zm0 2h1v1H7zm3-4h1v1h-1zm0 2h1v1h-1zm0 2h1v1h-1z"/></svg>';
    }

    // This part is already returning a string, so it works fine.
    return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a5.53 5.53 0 0 0-5.5 5.5C2.5 9.086 6.6 15 8 15s5.5-5.914 5.5-9.5A5.53 5.53 0 0 0 8 0m0 3a2.5 2.5 0 1 1 0 5A2.5 2.5 0 0 1 8 3"/></svg>';
}

function getLocationAutocompleteWrapper(suggestionsContainer) {
    return suggestionsContainer?.closest(
        ".floating-bordered-input, .position-relative",
    );
}

function showLocationSuggestions(suggestionsContainer) {
    if (!suggestionsContainer) return;
    suggestionsContainer.style.display = "block";
    getLocationAutocompleteWrapper(suggestionsContainer)?.classList.add(
        "location-autocomplete-open",
    );
}

function hideLocationSuggestions(suggestionsContainer) {
    if (!suggestionsContainer) return;
    suggestionsContainer.style.display = "none";
    getLocationAutocompleteWrapper(suggestionsContainer)?.classList.remove(
        "location-autocomplete-open",
    );
}

function setupCustomAutocomplete(
    inputId,
    suggestionsListId,
    hiddenAirportFieldId,
    onSelectCallback = null,
) {
    const input = document.getElementById(inputId);
    const suggestionsContainer = document.getElementById(suggestionsListId);
    const hiddenAirport =
        hiddenAirportFieldId != null && hiddenAirportFieldId !== ""
            ? document.getElementById(hiddenAirportFieldId)
            : null;
    let autocompleteService = new google.maps.places.AutocompleteService();
    let placesService = new google.maps.places.PlacesService(
        document.createElement("div"),
    );
    let debounceTimer;

    // Function to handle place selection
    function selectPlace(place, displayText) {
        input.value = displayText || place.formatted_address || place.name;
        hideLocationSuggestions(suggestionsContainer);

        // Check if it's an airport
        let isAirport = false;
        if (place.name && place.name.toLowerCase().includes("airport"))
            isAirport = true;
        if (place.types && place.types.includes("airport")) isAirport = true;
        if (place.address_components) {
            for (const comp of place.address_components) {
                if (
                    comp.types.includes("airport") ||
                    (comp.long_name &&
                        comp.long_name.toLowerCase().includes("airport")) ||
                    (comp.short_name &&
                        comp.short_name.toLowerCase().includes("airport"))
                ) {
                    isAirport = true;
                    break;
                }
            }
        }

        if (hiddenAirport) hiddenAirport.value = isAirport ? "1" : "0";
        if (onSelectCallback) onSelectCallback(place);
    }

    if (!input || !suggestionsContainer) return;
    // Handle input with debounce
    input.addEventListener("input", function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            hideLocationSuggestions(suggestionsContainer);
            return;
        }

        debounceTimer = setTimeout(() => {
            autocompleteService.getPlacePredictions(
                {
                    input: query,
                    types: ["geocode", "establishment"],
                    componentRestrictions: { country: "us" },
                },
                (predictions, status) => {
                    if (
                        status !== google.maps.places.PlacesServiceStatus.OK ||
                        !predictions
                    ) {
                        hideLocationSuggestions(suggestionsContainer);
                        return;
                    }

                    // Clear previous suggestions
                    suggestionsContainer.innerHTML = "";

                    // Add new suggestions
                    predictions.forEach((prediction) => {
                        const item = document.createElement("div");
                        item.className = "suggestion-item";
                        item.innerHTML = `
              <span class="suggestion-icon">${getPlaceIconSvg(prediction)}</span>
              <div>
                <span class="main-text">${prediction.structured_formatting.main_text}</span>
                <span class="sub-text">${prediction.structured_formatting.secondary_text}</span>
              </div>
            `;

                        item.addEventListener("click", () => {
                            // Get place details when a suggestion is clicked
                            placesService.getDetails(
                                {
                                    placeId: prediction.place_id,
                                    fields: [
                                        "formatted_address",
                                        "name",
                                        "address_components",
                                        "types",
                                        "geometry",
                                    ],
                                },
                                (place, status) => {
                                    if (
                                        status ===
                                        google.maps.places.PlacesServiceStatus
                                            .OK
                                    ) {
                                        const main =
                                            prediction.structured_formatting
                                                .main_text;
                                        const sub =
                                            prediction.structured_formatting
                                                .secondary_text || "";
                                        selectPlace(
                                            place,
                                            sub ? `${main}, ${sub}` : main,
                                        );
                                    }
                                },
                            );
                        });

                        suggestionsContainer.appendChild(item);
                    });

                    showLocationSuggestions(suggestionsContainer);
                },
            );
        }, 500);
    });

    // Hide suggestions when clicking outside
    document.addEventListener("click", (e) => {
        if (
            !input.contains(e.target) &&
            !suggestionsContainer.contains(e.target)
        ) {
            hideLocationSuggestions(suggestionsContainer);
        }
    });

    // Handle keyboard navigation
    input.addEventListener("keydown", (e) => {
        const visibleItems =
            suggestionsContainer.querySelectorAll(".suggestion-item");
        const activeItem = document.activeElement;
        let activeIndex = Array.from(visibleItems).indexOf(activeItem);

        if (e.key === "ArrowDown") {
            e.preventDefault();
            const nextIndex = (activeIndex + 1) % visibleItems.length;
            visibleItems[nextIndex].focus();
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            const prevIndex =
                (activeIndex - 1 + visibleItems.length) % visibleItems.length;
            if (prevIndex === visibleItems.length - 1) {
                input.focus();
            } else {
                visibleItems[prevIndex].focus();
            }
        } else if (
            e.key === "Enter" &&
            activeItem.classList.contains("suggestion-item")
        ) {
            e.preventDefault();
            activeItem.click();
        }
    });
}

let pickupPlacePoint = null;
let dropoffPlacePoint = null;

function handlePointToPointUpdate() {
    if (pickupPlacePoint || dropoffPlacePoint) {
        document.getElementById("map").style.display = "block";
        initMap(pickupPlacePoint, dropoffPlacePoint);
    }
}

function initAutocomplete() {
    // Initialize autocomplete for all location inputs

    // Desktop / Default
    setupCustomAutocomplete(
        "pickup-location",
        "pickup-suggestions",
        "is-airport",
        function (place) {
            pickupPlacePoint = place;
            // Sync with mobile
            $("#pickup-location_mobile").val($("#pickup-location").val());
            $("#is-airport_mobile").val($("#is-airport").val());
            handlePointToPointUpdate();
            if (typeof updateMapWidth === "function") updateMapWidth();
        },
    );

    setupCustomAutocomplete(
        "dropoff-location",
        "dropoff-suggestions",
        "is-airport-dropoff",
        function (place) {
            dropoffPlacePoint = place;
            // Sync with mobile
            $("#dropoff-location_mobile").val($("#dropoff-location").val());
            handlePointToPointUpdate();
        },
    );

    setupCustomAutocomplete(
        "pickup-location-hourly",
        "pickup-location-hourly-suggestions",
        "is-airport_hourly",
        function (place) {
            initMap(place, null);
        },
    );

    // Mobile
    setupCustomAutocomplete(
        "pickup-location_mobile",
        "pickup-suggestions_mobile",
        "is-airport_mobile",
        function (place) {
            pickupPlacePoint = place;
            // Sync with desktop
            $("#pickup-location").val($("#pickup-location_mobile").val());
            $("#is-airport").val($("#is-airport_mobile").val());
            handlePointToPointUpdate();
        },
    );

    setupCustomAutocomplete(
        "dropoff-location_mobile",
        "dropoff-suggestions_mobile",
        "is-airport-dropoff_mobile",
        function (place) {
            dropoffPlacePoint = place;
            // Sync with desktop
            $("#dropoff-location").val($("#dropoff-location_mobile").val());
            handlePointToPointUpdate();
        },
    );

    setupCustomAutocomplete(
        "pickup-location-hourly_mobile",
        "pickup-location-hourly-suggestions_mobile",
        "is-airport_hourly_mobile",
        function (place) {
            initMap(place, null);
        },
    );

    // Form (Service Pages)
    setupCustomAutocomplete(
        "pickup-location_form",
        "pickup-suggestions_form",
        "is-airport_form",
        function (place) {
            pickupPlacePoint = place;
            handlePointToPointUpdate();
            if (typeof updateMapWidth === "function") updateMapWidth();
        },
    );

    setupCustomAutocomplete(
        "dropoff-location_form",
        "dropoff-suggestions_form",
        "is-airport-dropoff_form",
        function (place) {
            dropoffPlacePoint = place;
            handlePointToPointUpdate();
        },
    );

    setupCustomAutocomplete(
        "pickup-location-hourly_form",
        "pickup-location-hourly-suggestions_form",
        "is-airport_hourly_form",
        function (place) {
            initMap(place, null);
        },
    );

    // Hero (if exists)
    setupCustomAutocomplete(
        "pickup-location_hero",
        "pickup-suggestions_hero",
        "is-airport_hero",
        function (place) {
            pickupPlacePoint = place;
            handlePointToPointUpdate();
        },
    );

    setupCustomAutocomplete(
        "dropoff-location_hero",
        "dropoff-suggestions_hero",
        "is-airport-dropoff_hero",
        function (place) {
            dropoffPlacePoint = place;
            handlePointToPointUpdate();
        },
    );

    setupCustomAutocomplete("pickup_address", "quote-pickup-suggestions", null);
    setupCustomAutocomplete("dropoff_address", "quote-dropoff-suggestions", null);

    // Add window resize listener for map width if needed
    if (typeof updateMapWidth === "function") {
        window.addEventListener("resize", updateMapWidth);
    }
}

// Compatibility script for old map logic
function updateMapWidth() {
    const map = document.getElementById("map");
    if (!map) return;
    // logic is handled by CSS mostly in new template
}

// function initializeStopAutocomplete(formId) {
//     const stopContainer = document.querySelectorAll(`#${formId} .stop-location-input`);
//     stopInputs = stopInputs.concat(Array.from(stopContainer)); // Store the inputs globally
//     stopInputs.forEach(function (input) {
//         const autocomplete = new google.maps.places.Autocomplete(input, { types: ['geocode'] });
//         input.addEventListener('change', calculateRoute);  // Recalculate route when stop changes
//     });
// }
function initializeStopAutocomplete(formId) {
    const stopContainer = document.querySelectorAll(
        `#${formId} .stop-location-input`,
    );
    Array.from(stopContainer).forEach((input) => {
        if (!stopInputs.includes(input)) {
            stopInputs.push(input);

            const autocomplete = new google.maps.places.Autocomplete(
                input,
                options,
            );
            autocomplete.inputElement = input; // Store reference to input
            stopAutocompletes.push(autocomplete);

            // Add event listener if you want to update map on stop change
            autocomplete.addListener("place_changed", () => {
                calculateRoute();
            });
        }
    });
}

// Handle pickup and dropoff location changes with smooth transitions
function onLocationChanged() {
    const $pickup = $("#pickup-location").length
        ? $("#pickup-location")
        : $("#pickup-location_mobile").length
          ? $("#pickup-location_mobile")
          : $("#pickup-location_form");
    const $dropoff = $("#dropoff-location").length
        ? $("#dropoff-location")
        : $("#dropoff-location_mobile").length
          ? $("#dropoff-location_mobile")
          : $("#dropoff-location_form");
    const mapElement = document.getElementById("map");
    const pickupVal = $pickup.val() ? $pickup.val().trim() : "";
    const dropoffVal = $dropoff.val() ? $dropoff.val().trim() : "";
    const zoomLevel = getHomeSearchMapSinglePlaceZoom();
    const animationDuration = 500; // ms

    // Clear directions if either field is empty
    if ((pickupVal === "" || dropoffVal === "") && directionsRenderer) {
        $(".below-map").addClass("main-hero");
        directionsRenderer.setMap(null);
        directionsRenderer = null;
    }

    // Handle map display based on which fields have values
    if (pickupVal === "" && dropoffVal === "") {
        // Both fields are empty - hide the map
        if (mapElement) {
            // Fade out effect
            $(mapElement).fadeOut(animationDuration);
            $("#hide_on_map").fadeIn(animationDuration);
            // Show header text
            $("#home-text-content").css("visibility", "visible");
            $("#home-text-content").css("pointer-events", "auto");
        }
    } else if (map) {
        // Ensure map is visible
        if (mapElement.style.display === "none") {
            mapElement.style.display = "block";
            $("#hide_on_map").hide();
            $(mapElement).hide().fadeIn(animationDuration);
            // Hide header text
            $("#home-text-content").css("visibility", "hidden");
            $("#home-text-content").css("pointer-events", "none");
        }

        // Animate to the appropriate marker
        let targetPosition = null;
        if (pickupVal !== "" && pickupMarker) {
            targetPosition = pickupMarker.getPosition();
        } else if (dropoffVal !== "" && dropoffMarker) {
            targetPosition = dropoffMarker.getPosition();
        }

        if (targetPosition) {
            // Smooth pan and zoom
            map.panTo(targetPosition);
            map.setZoom(zoomLevel);
        }
    }

    // Clean up markers if their corresponding input is empty
    if (pickupVal === "" && pickupMarker) {
        // Fade out marker before removing
        const markerElement = document.querySelector(
            'img[src*="' + pickupMarker.getIcon().url.split(",")[1] + '"]',
        );
        if (markerElement) {
            $(markerElement)
                .parent()
                .fadeOut(animationDuration, function () {
                    if (pickupMarker) {
                        pickupMarker.setMap(null);
                        pickupMarker = null;
                    }
                });
        } else if (pickupMarker) {
            pickupMarker.setMap(null);
            pickupMarker = null;
        }
    }

    if (dropoffVal === "" && dropoffMarker) {
        // Fade out marker before removing
        const markerElement = document.querySelector(
            'img[src*="' + dropoffMarker.getIcon().url.split(",")[1] + '"]',
        );
        if (markerElement) {
            $(markerElement)
                .parent()
                .fadeOut(animationDuration, function () {
                    if (dropoffMarker) {
                        dropoffMarker.setMap(null);
                        dropoffMarker = null;
                    }
                });
        } else if (dropoffMarker) {
            dropoffMarker.setMap(null);
            dropoffMarker = null;
        }
    }
}

// Initialize Google Map with Directions service
// Function to animate the car along the route
function animateCar() {
    if (animationPath.length === 0 || animationProgress >= 1) {
        animationProgress = 0;
        cancelAnimationFrame(animationId);
        return;
    }

    // Increase progress (slower movement)
    const speed = 0.000005; // Reduced from 0.001 to 0.0005 for slower movement
    animationProgress += speed;
    if (animationProgress > 1) {
        animationProgress = 1;
    }

    // Get current position along the path
    const path = animationPath;
    const pathLength = google.maps.geometry.spherical.computeLength(path);
    let distance = pathLength * animationProgress;

    // Find the current position
    let currentPosition = { lat: 0, lng: 0 };
    for (let i = 0; i < path.length - 1; i++) {
        const from = path[i];
        const to = path[i + 1];
        const segmentLength =
            google.maps.geometry.spherical.computeDistanceBetween(from, to);

        if (distance <= segmentLength) {
            // Calculate position within this segment
            const heading = google.maps.geometry.spherical.computeHeading(
                from,
                to,
            );
            currentPosition = google.maps.geometry.spherical.computeOffset(
                from,
                distance,
                heading,
            );
            break;
        }
        distance -= segmentLength;
    }

    // Update car marker position
    if (window.carMarker) {
        carMarker.setPosition(currentPosition);

        // Calculate rotation
        if (window.lastCarPosition) {
            const heading = google.maps.geometry.spherical.computeHeading(
                window.lastCarPosition,
                currentPosition,
            );
            carMarker.setIcon({
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 5,
                rotation: heading,
                fillColor: "#4285F4",
                fillOpacity: 1,
                strokeWeight: 2,
                strokeColor: "white",
            });
        }
        window.lastCarPosition = currentPosition;
    }

    // Add a small delay to control frame rate
    if (animationProgress < 1) {
        setTimeout(() => {
            animationId = requestAnimationFrame(animateCar);
        }, 20); // ~50 FPS (1000ms / 50 = 20ms per frame)
    }
}

function initMap(pickupPlace, dropoffPlace) {
    const hasPickup = pickupPlace?.geometry?.location;
    const hasDropoff = dropoffPlace?.geometry?.location;
    const mapElement = document.getElementById("map");

    if (!hasPickup && !hasDropoff) {
        console.warn("No valid pickup or dropoff locations provided.");
        $(mapElement).fadeOut(300);
        $("#hide_on_map").fadeIn(300);
        return;
    }

    if (mapElement) {
        const overlay = mapElement.querySelector(".map-overlay");
        if (overlay) overlay.remove();
        $(window).width() < 768
            ? $(".mobile-hero").hide()
            : $(".web-hero").hide();
        mapElement.style.removeProperty("background-image");
        mapElement.style.removeProperty("background-size");
        mapElement.style.removeProperty("background-position");
        mapElement.style.removeProperty("background-repeat");
        $(".below-map").removeClass("main-hero");
    }

    const mapStyle = [
        {
            featureType: "all",
            elementType: "geometry.fill",
            stylers: [
                { color: "#FFFBF8" }, // Almost white with a tiny hint of orange/cream
            ],
        },
        {
            featureType: "all",
            elementType: "geometry.stroke",
            stylers: [
                { color: "#FEECE2" }, // Very pale peach border
                { weight: 0.5 },
            ],
        },
        {
            featureType: "water",
            elementType: "geometry.fill",
            stylers: [
                { color: "#FDE0D2" }, // Soft, very light orange-tinted water
            ],
        },
        {
            featureType: "road",
            elementType: "geometry.fill",
            stylers: [
                { color: "#ffffff" }, // Pure white roads to make them "clean"
            ],
        },
        {
            featureType: "road.highway",
            elementType: "geometry.fill",
            stylers: [
                { color: "#FFD9C2" }, // Subtle, light orange for main roads
            ],
        },
        {
            featureType: "poi",
            elementType: "geometry.fill",
            stylers: [{ color: "#FFF0E8" }],
        },
        {
            featureType: "poi.park",
            elementType: "geometry.fill",
            stylers: [
                { color: "#F2F5F0" }, // Keeping parks slightly neutral/green-tinted for clarity
            ],
        },
        {
            featureType: "administrative",
            elementType: "labels.text.fill",
            stylers: [
                { color: "#8C6D62" }, // Soft brown-grey text (easier on the eyes than black)
                { weight: 0.4 },
            ],
        },
        {
            featureType: "water",
            elementType: "labels.text.fill",
            stylers: [{ color: "#B39286" }],
        },
    ];

    const mapCenter = hasPickup
        ? pickupPlace.geometry.location
        : dropoffPlace.geometry.location;

    // Reuse global map instance or create new one
    if (!map) {
        map = new google.maps.Map(mapElement, {
            center: mapCenter,
            zoom: getHomeSearchMapSinglePlaceZoom(),
            styles: mapStyle,
            disableDefaultUI: true,
            zoomControl: true,
            gestureHandling: "cooperative",
        });
    } else {
        // Smoothly update the map center if needed
        const currentCenter = map.getCenter();
        if (
            currentCenter &&
            mapCenter &&
            (currentCenter.lat() !== mapCenter.lat() ||
                currentCenter.lng() !== mapCenter.lng())
        ) {
            map.panTo(mapCenter);
        }
    }

    // Initialize global directions service and renderer if not already done
    if (!directionsService) {
        directionsService = new google.maps.DirectionsService();
    }
    if (!directionsRenderer) {
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: "#e52c43", // Lighter shade of #e52c43
                strokeOpacity: 0.9,
                strokeWeight: 6,
            },
        });
    } else {
        // Clear previous directions
        directionsRenderer.setDirections({ routes: [] });
        directionsRenderer.setMap(map);
    }

    // Smoothly update or create markers
    const updateOrCreateMarker = (place, isPickup) => {
        const marker = isPickup ? pickupMarker : dropoffMarker;
        const position = isPickup
            ? pickupPlace.geometry.location
            : dropoffPlace.geometry.location;

        if (marker) {
            // Smoothly move existing marker
            marker.setPosition(position);
            return marker;
        } else {
            // Create new marker with fade-in effect
            const newMarker = new google.maps.Marker({
                position: position,
                map: map,
                icon: createCustomMarker(isPickup ? "#e52c43" : "#FF6C00", ""), // Removed 'A' and 'B' labels
                animation: google.maps.Animation.DROP,
            });

            // Store reference
            if (isPickup) {
                pickupMarker = newMarker;
            } else {
                dropoffMarker = newMarker;
            }

            return newMarker;
        }
    };

    // Clear markers that should no longer be shown
    if (!hasPickup && pickupMarker) {
        const markerElement = document.querySelector(
            'img[src*="' + pickupMarker.getIcon().url.split(",")[1] + '"]',
        );
        if (markerElement) {
            $(markerElement)
                .parent()
                .fadeOut(300, () => {
                    if (pickupMarker) {
                        pickupMarker.setMap(null);
                        pickupMarker = null;
                    }
                });
        } else if (pickupMarker) {
            pickupMarker.setMap(null);
            pickupMarker = null;
        }
    }

    if (!hasDropoff && dropoffMarker) {
        const markerElement = document.querySelector(
            'img[src*="' + dropoffMarker.getIcon().url.split(",")[1] + '"]',
        );
        if (markerElement) {
            $(markerElement)
                .parent()
                .fadeOut(300, () => {
                    if (dropoffMarker) {
                        dropoffMarker.setMap(null);
                        dropoffMarker = null;
                    }
                });
        } else if (dropoffMarker) {
            dropoffMarker.setMap(null);
            dropoffMarker = null;
        }
    }

    // Function to create a custom marker with centered label
    function createCustomMarker() {
        const svg = `<svg viewBox="0 0 466.583 466.582" width="34px" height="34px" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="rgb(229,44,67)"/><stop offset="100%" stop-color="rgb(255,108,0)"/></linearGradient></defs><path fill="url(#g)" d="M233.292,0c-85.1,0-154.334,69.234-154.334,154.333c0,34.275,21.887,90.155,66.908,170.834 c31.846,57.063,63.168,104.643,64.484,106.64l22.942,34.775l22.941-34.774c1.317-1.998,32.641-49.577,64.483-106.64 c45.023-80.68,66.908-136.559,66.908-170.834C387.625,69.234,318.391,0,233.292,0z M233.292,233.291c-44.182,0-80-35.817-80-80 s35.818-80,80-80c44.182,0,80,35.817,80,80S277.473,233.291,233.292,233.291z"/></svg>`;
        return {
            url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(32, 32),
        };
    }

    // Update or create markers with smooth transitions
    if (hasPickup) {
        updateOrCreateMarker(pickupPlace, true);
    }

    if (hasDropoff) {
        updateOrCreateMarker(dropoffPlace, false);
    }

    // Calculate bounds to show all markers
    const bounds = new google.maps.LatLngBounds();
    if (hasPickup) bounds.extend(pickupPlace.geometry.location);
    if (hasDropoff) bounds.extend(dropoffPlace.geometry.location);

    // Only fit bounds when both endpoints exist; one-point bounds make fitBounds zoom to max on mobile.
    if (!bounds.isEmpty()) {
        if (hasPickup && hasDropoff) {
            const padding = isHomeSearchMapMobileViewport() ? 130 : 100;
            map.fitBounds(bounds, {
                padding: {
                    top: padding,
                    right: padding,
                    bottom: padding,
                    left: padding,
                },
            });
        } else {
            const center = hasPickup
                ? pickupPlace.geometry.location
                : dropoffPlace.geometry.location;
            map.panTo(center);
            map.setZoom(getHomeSearchMapSinglePlaceZoom());
        }
    }

    if (hasPickup && hasDropoff) {
        const request = {
            origin: pickupPlace.geometry.location,
            destination: dropoffPlace.geometry.location,
            travelMode: "DRIVING",
        };

        directionsService.route(request, function (response, status) {
            if (status === "OK") {
                directionsRenderer.setDirections(response);
                calculateDistance(pickupPlace, dropoffPlace);
            } else {
                console.warn("Directions API Error:", status);
            }
        });
    }
}

// Add a stop location (dynamically create stop inputs)
function addStop(formId) {
    const stopContainer = document.getElementById(`${formId}-stop-container`);
    const stopInput = document.createElement("div");
    stopInput.classList.add("mb-3");
    stopInput.innerHTML = `
        <label for="stop-location" class="form-label">Stop Location</label>
        <div class="input-group">
            <input type="text" class="form-control stop-location-input" placeholder="Enter Stop Location" required>
            <span class="input-group-text remove-stop-btn" onclick="removeStop(this, '${formId}')">🗑️</span>
        </div>`;
    stopContainer.appendChild(stopInput);

    // Reinitialize autocomplete for the new stop input
    initializeStopAutocomplete(formId);
    // Trigger route recalculation after adding a new stop
    calculateRoute();
}

// Remove a stop location (dynamically remove stop inputs)
function removeStop(element, formId) {
    // const stopContainer = document.getElementById(`${formId}-stop-container`);
    // stopContainer.removeChild(element.parentElement.parentElement);
    const stopContainer = document.getElementById(`${formId}-stop-container`);
    const stopDiv = element.parentElement.parentElement;
    const input = stopDiv.querySelector("input");

    // Remove input from stopInputs and corresponding autocomplete
    const index = stopInputs.indexOf(input);
    if (index !== -1) {
        stopInputs.splice(index, 1); // Remove input from inputs array
        stopAutocompletes.splice(index, 1); // Remove corresponding autocomplete instance
    }

    // Remove the stop input from DOM
    stopContainer.removeChild(stopDiv);
    // Trigger route recalculation after removing a stop
    calculateRoute();
}

// Function to calculate the route and re-render the map
function calculateRoute(swapped = false) {
    // Get the current input values
    const pickupInput = document.getElementById("pickup-location");
    const dropoffInput = document.getElementById("dropoff-location");

    console.log("js file : " + pickupInput.value, dropoffInput.value);
    // Check if we were called with place objects directly
    if (
        arguments.length === 2 &&
        arguments[0] &&
        arguments[1] &&
        arguments[0].geometry &&
        arguments[1].geometry
    ) {
        // We were called with place objects directly
        const pickupPlace = arguments[0];
        const dropoffPlace = arguments[1];
        return renderRoute(pickupPlace, dropoffPlace, []);
    }

    // If autocomplete objects aren't initialized yet, initialize them
    if (!window.autocompletePickup || !window.autocompleteDropoff) {
        if (window.google && google.maps && google.maps.places) {
            window.autocompletePickup = new google.maps.places.Autocomplete(
                pickupInput,
            );
            window.autocompleteDropoff = new google.maps.places.Autocomplete(
                dropoffInput,
            );
        } else {
            console.error("Google Maps JavaScript API not loaded");
            return;
        }
    }

    // If swapped parameter is true, swap the values in the input fields
    if (swapped) {
        const temp = pickupInput.value;
        pickupInput.value = dropoffInput.value;
        dropoffInput.value = temp;

        // Trigger place_changed event to update the autocomplete places
        if (window.autocompletePickup)
            google.maps.event.trigger(
                window.autocompletePickup,
                "place_changed",
            );
        if (window.autocompleteDropoff)
            google.maps.event.trigger(
                window.autocompleteDropoff,
                "place_changed",
            );
    }

    // Get the places from autocomplete
    const pickupPlace = window.autocompletePickup.getPlace();
    const dropoffPlace = window.autocompleteDropoff.getPlace();

    if (
        !pickupPlace ||
        !dropoffPlace ||
        !pickupPlace.geometry ||
        !dropoffPlace.geometry
    ) {
        return;
    }

    const waypoints = [];

    // Handle any stop locations if they exist
    if (window.stopAutocompletes) {
        stopAutocompletes.forEach((ac) => {
            const stopPlace = ac.getPlace();
            if (stopPlace && stopPlace.geometry) {
                waypoints.push({
                    location: stopPlace.geometry.location,
                    stopover: true,
                });
            }
        });
    }

    // Make the request to the Directions API with waypoints (stops)
    const request = {
        origin: pickupPlace.geometry.location,
        destination: dropoffPlace.geometry.location,
        waypoints: waypoints,
        travelMode: "DRIVING",
        optimizeWaypoints: true,
    };

    // Initialize the map if not already done
    if (!window.map) {
        // Create a new map instance
        const mapElement = document.getElementById("map");
        if (mapElement) {
            window.map = new google.maps.Map(mapElement, {
                zoom: 12,
                center: { lat: 32.7767, lng: -96.797 }, // Default to Dallas
                gestureHandling: "cooperative",
                styles: [
                    {
                        featureType: "all",
                        elementType: "geometry.fill",
                        stylers: [{ color: "#f8faff" }],
                    },
                    {
                        featureType: "road",
                        elementType: "geometry.fill",
                        stylers: [{ color: "#ffffff" }],
                    },
                    {
                        featureType: "water",
                        elementType: "geometry.fill",
                        stylers: [{ color: "#e6f0ff" }],
                    },
                ],
            });
        }
    }

    // Ensure we have a valid map instance
    if (!window.map) {
        console.error("Failed to initialize map");
        return;
    }

    // Show the map if it was hidden
    const mapDisplayElement = document.getElementById("map");
    if (mapDisplayElement) {
        mapDisplayElement.style.display = "block";
        $("#home-text-content").css("visibility", "hidden");
        $("#home-text-content").css("pointer-events", "none");
    }

    // Get and display the route
    window.directionsService =
        window.directionsService || new google.maps.DirectionsService();

    window.directionsService.route(request, function (response, status) {
        if (status === "OK") {
            // Initialize or update directions renderer
            if (!window.directionsRenderer) {
                window.directionsRenderer = new google.maps.DirectionsRenderer({
                    map: window.map,
                    suppressMarkers: true,
                    polylineOptions: {
                        strokeColor: "#e52c43",
                        strokeWeight: 4,
                        strokeOpacity: 0.8,
                    },
                });
            }
            window.directionsRenderer.setDirections(response);

            // Update markers with the correct positions
            if (window.pickupMarker) window.pickupMarker.setMap(null);
            if (window.dropoffMarker) window.dropoffMarker.setMap(null);

            // Add custom markers
            const route = response.routes[0];
            const bounds = new google.maps.LatLngBounds();

            // Add pickup marker
            window.pickupMarker = new google.maps.Marker({
                position: pickupPlace.geometry.location,
                map: window.map,
                icon: {
                    url:
                        "data:image/svg+xml;charset=UTF-8," +
                        encodeURIComponent(
                            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="%23e52c43" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>',
                        ),
                    scaledSize: new google.maps.Size(32, 32),
                    anchor: new google.maps.Point(12, 12),
                },
                title: "Pickup: " + pickupInput.value,
            });

            // Add dropoff marker
            window.dropoffMarker = new google.maps.Marker({
                position: dropoffPlace.geometry.location,
                map: window.map,
                icon: {
                    url:
                        "data:image/svg+xml;charset=UTF-8," +
                        encodeURIComponent(
                            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="%23e52c43" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
                        ),
                    scaledSize: new google.maps.Size(32, 32),
                    anchor: new google.maps.Point(12, 12),
                },
                title: "Dropoff: " + dropoffInput.value,
            });

            // Extend bounds to include all points
            bounds.extend(pickupPlace.geometry.location);
            bounds.extend(dropoffPlace.geometry.location);
            waypoints.forEach((waypoint) => bounds.extend(waypoint.location));

            // Fit map to bounds with padding
            window.map.fitBounds(bounds, {
                top: 50,
                right: 50,
                bottom: 50,
                left: 50,
            });
        } else {
            console.error("Directions request failed due to " + status);
        }
    });

    // Recalculate the distance
    calculateDistance(pickupPlace, dropoffPlace, waypoints);
}

// Function to calculate distance when form is submitted
function calculateDistance(pickupPlace, dropoffPlace, callback) {
    const distanceService = new google.maps.DistanceMatrixService();
    distanceService.getDistanceMatrix(
        {
            origins: [pickupPlace.geometry.location],
            destinations: [dropoffPlace.geometry.location],
            travelMode: "DRIVING",
            unitSystem: google.maps.UnitSystem.IMPERIAL, // Ensures miles are returned
        },
        function (response, status) {
            if (status === "OK") {
                const element = response.rows[0].elements[0];

                if (element.status === "OK") {
                    const distanceText = element.distance.text; // e.g., '8.4 mi'
                    const durationText = element.duration.text; // e.g., '45 mins'

                    // Show in map info box
                    const infoBox = document.getElementById("route-info-box");
                    const distEl = document.getElementById("route-distance");
                    const durEl = document.getElementById("route-duration");

                    if (infoBox && distEl && durEl) {
                        distEl.textContent = distanceText;
                        durEl.textContent = durationText;
                        infoBox.style.display = "block";
                    }

                    // Optional callback
                    if (typeof callback === "function") {
                        callback(distanceText, durationText);
                    }
                } else {
                    console.error(
                        "DistanceMatrix element error:",
                        element.status,
                    );
                }
            } else {
                console.error("DistanceMatrixService error:", status);
            }
        },
    );
}

// Form validation before submission
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll("input[required], select[required]");
    for (const input of inputs) {
        if (!input.value) {
            alert("Please fill in all required fields.");
            return false;
        }
    }
    return true;
}
var searchForm = document.querySelector(".search-form");
if (searchForm) {
    searchForm.addEventListener("submit", async function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get latest places from pickup & dropoff autocompletes
        const geocoder = new google.maps.Geocoder();
        const pickupInput = document.getElementById("pickup-location");
        const dropoffInput = document.getElementById("dropoff-location");
        const pickupAddress = pickupInput.value.trim();
        const dropoffAddress = dropoffInput.value.trim();
        let pickupPlace = autocompletePickup?.getPlace?.();
        let dropoffPlace = autocompleteDropoff?.getPlace?.();

        // Fallback if autocomplete has no place (e.g., form is prefilled)
        if (!pickupPlace && pickupAddress) {
            pickupPlace = await new Promise((resolve) => {
                geocoder.geocode(
                    { address: pickupAddress },
                    (results, status) => {
                        resolve(status === "OK" ? results[0] : null);
                    },
                );
            });
        }

        if (!dropoffPlace && dropoffAddress) {
            dropoffPlace = await new Promise((resolve) => {
                geocoder.geocode(
                    { address: dropoffAddress },
                    (results, status) => {
                        resolve(status === "OK" ? results[0] : null);
                    },
                );
            });
        }

        if (!pickupPlace || !dropoffPlace) {
            alert("Please select valid pickup and dropoff locations.");
            return;
        }

        // Collect stop text values (input values, not Autocomplete objects)
        const stopAddresses = stopAutocompletes
            .map((auto, index) => {
                const input = auto.inputElement; // We'll store this manually when creating each Autocomplete
                const value = input.value.trim();
                if (!value) {
                    alert(
                        `Please enter a valid stop address for Stop #${index + 1}`,
                    );
                }
                return value;
            })
            .filter(Boolean); // Remove empty or invalid entries

        const pointToPointForm = document.querySelector("#pointToPoint");
        // Clear any existing hidden inputs
        // const existingHiddenStops = form.querySelectorAll('[name^="stop_"]');
        // existingHiddenStops.forEach(input => input.remove());

        // Append new hidden input fields for each stop
        stopAddresses.forEach((address, index) => {
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "stops[]"; // Set the name dynamically
            hiddenInput.value = address; // Set the stop address as the value
            pointToPointForm.appendChild(hiddenInput);
        });

        event.target.submit();
    });
}

const hourForm = document.querySelector("#hourForm");
if (hourForm) {
    document
        .querySelector("#hourForm")
        .addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form submission

            // Get latest places from pickup & dropoff autocompletes
            // const pickupPlace = autocompletePickup.getPlace();
            // const dropoffPlace = autocompleteDropoff.getPlace();

            // if (!pickupPlace || !dropoffPlace) {
            //     alert('Please select valid pickup and dropoff locations.');
            //     return;
            // }

            // Collect stop text values (input values, not Autocomplete objects)
            const stopAddresses = stopAutocompletes
                .map((auto, index) => {
                    const input = auto.inputElement; // We'll store this manually when creating each Autocomplete
                    const value = input.value.trim();
                    if (!value) {
                        alert(
                            `Please enter a valid stop address for Stop #${index + 1}`,
                        );
                    }
                    return value;
                })
                .filter(Boolean); // Remove empty or invalid entries

            const hourFormEl = document.querySelector("#hourForm");
            // Clear any existing hidden inputs
            // const existingHiddenStops = form.querySelectorAll('[name^="stop_"]');
            // existingHiddenStops.forEach(input => input.remove());

            // Append new hidden input fields for each stop
            stopAddresses.forEach((address, index) => {
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "stops[]"; // Set the name dynamically
                hiddenInput.value = address; // Set the stop address as the value
                hourFormEl.appendChild(hiddenInput);
            });

            event.target.submit();
        });
}

const inputIds = [
    "pickup-location",
    "pickup-location-hourly",
    "dropoff-location",
    "pickup-location_mobile",
    "pickup-location-hourly_mobile",
    "dropoff-location_mobile",
    "pickup-location_form",
    "pickup-location-hourly_form",
    "dropoff-location_form",
];

function positionAutocomplete(input) {
    const pacContainer = document.querySelector(".pac-container");
    const group = input.closest(".input-group");

    if (pacContainer && group) {
        const rect = group.getBoundingClientRect();

        // Use requestAnimationFrame for smoother positioning
        requestAnimationFrame(() => {
            pacContainer.style.position = "absolute";
            pacContainer.style.width = rect.width + "px";
            pacContainer.style.left = rect.left + window.scrollX + "px";
            pacContainer.style.top = rect.bottom + window.scrollY + "px";
            pacContainer.style.zIndex = "9999";
        });
    }
}

function setupAutocompleteFix() {
    inputIds.forEach((id) => {
        const input = document.getElementById(id);
        if (!input) return;

        // Reposition on any relevant event
        ["focus", "input", "keydown", "click"].forEach((eventName) => {
            input.addEventListener(eventName, () => {
                setTimeout(() => positionAutocomplete(input), 0);
            });
        });

        // Observe DOM to catch when .pac-container is added
        const observer = new MutationObserver(() => {
            const pacContainer = document.querySelector(".pac-container");
            if (document.activeElement === input && pacContainer) {
                positionAutocomplete(input);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    });
}

window.addEventListener("load", setupAutocompleteFix);
// Run when the page fully loads
window.addEventListener("load", triggerPlaceChangedIfPrefilled);

// ALSO run when user comes back using browser back/forward buttons
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        // true if coming from bfcache (back-forward cache)
        triggerPlaceChangedIfPrefilled();
    }
});

// Initialize Material Date Time Picker
$(document).ready(function () {
    // Return Trip Checkbox Logic - Moved outside to ensure it works regardless of datepicker
    function toggleReturnTrip() {
        if ($('input[name="round_trip"]').is(":checked")) {
            $(".return-trip").show();
            $(".point-button").addClass("mt-4");
        } else {
            $(".return-trip").hide();
            $(".point-button").removeClass("mt-4");
        }
    }

    // Initial check
    toggleReturnTrip();

    // Handle change event
    $('input[name="round_trip"]').on("change", function () {
        toggleReturnTrip();
    });

    // Sync Mobile and Desktop Inputs
    function syncInputs(id) {
        const desktopId = "#" + id;
        const mobileId = "#" + id + "_mobile";

        function sync(source, target) {
            const val = $(source).val();
            const type = $(source).attr("type");

            if (type === "checkbox") {
                const checked = $(source).is(":checked");
                if ($(target).is(":checked") !== checked) {
                    $(target).prop("checked", checked).trigger("change");
                }
            } else {
                if ($(target).val() !== val) {
                    $(target).val(val).trigger("input").trigger("change");
                }
            }
        }

        $(desktopId).on("input change keyup", function () {
            sync(desktopId, mobileId);
        });
        $(mobileId).on("input change keyup", function () {
            sync(mobileId, desktopId);
        });
    }

    const idsToSync = [
        "pickup-location",
        "dropoff-location",
        "pickup-datetime",
        "round-trip",
        "return-datetime-hourly",
        "pickup-location-hourly",
        "select-hours",
        "pickup-datetime-hourly",
    ];

    idsToSync.forEach(syncInputs);

    if ($.fn.bootstrapMaterialDatePicker) {
        $(
            "#pickup-datetime, #pickup-datetime-hourly, #return-datetime-hourly, #pickup-datetime_mobile, #pickup-datetime-hourly_mobile, #return-datetime-hourly_mobile",
        ).bootstrapMaterialDatePicker({
            format: "ddd, MMM Do, YYYY h:mm A",
            minDate: new Date(),
            lang: "en",
            weekStart: 0,
            shortTime: true,
            cancelText: "Back",
            okText: "OK",
            switchOnClick: true,
        });

        // Ensure clicking the icon triggers the input
        $(".input-icon-left").on("click", function () {
            $(this).next("input").focus();
        });
    } else {
        console.error("bootstrapMaterialDatePicker plugin is not loaded");
    }

    /** Home banner / search forms: today + time must be at least 2 hours from now */
    function localTodayYmd() {
        var n = new Date();
        return (
            n.getFullYear() +
            "-" +
            String(n.getMonth() + 1).padStart(2, "0") +
            "-" +
            String(n.getDate()).padStart(2, "0")
        );
    }

    function formPostsBookingSession(form) {
        var a = (form.getAttribute("action") || "").trim();
        return a.indexOf("save-booking-form-session") !== -1;
    }

    $(document).on("submit", "form", function (e) {
        var form = this;
        if (!formPostsBookingSession(form)) {
            return;
        }

        var $form = $(form);
        var $date = $form.find('input[name="pickup_date"]');
        var $time = $form.find('input[name="pickup_time"]');
        if (!$date.length || !$time.length) {
            return;
        }

        var timeEl = $time[0];
        var dateVal = ($date.val() || "").trim();
        var timeVal = ($time.val() || "").trim();
        if (!dateVal || !timeVal) {
            return;
        }

        if (dateVal !== localTodayYmd()) {
            timeEl.setCustomValidity("");
            return;
        }

        var parts = timeVal.split(":");
        var hh = parseInt(parts[0], 10);
        var mm = parseInt(parts[1] !== undefined ? parts[1] : "0", 10);
        if (isNaN(hh) || isNaN(mm)) {
            return;
        }

        var dp = dateVal.split("-");
        if (dp.length !== 3) {
            return;
        }
        var pick = new Date(
            parseInt(dp[0], 10),
            parseInt(dp[1], 10) - 1,
            parseInt(dp[2], 10),
            hh,
            mm,
            0,
            0,
        );
        if (isNaN(pick.getTime())) {
            return;
        }

        var earliest = new Date(Date.now() + 2 * 60 * 60 * 1000);

        if (pick.getTime() < earliest.getTime()) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var msg = "For today, pick-up must be at least 2 hours from the current time.";
            timeEl.setCustomValidity(msg);
            timeEl.focus({ preventScroll: true });
            if (typeof timeEl.reportValidity === "function") {
                timeEl.reportValidity();
            } else {
                window.alert(msg);
            }
            return false;
        }

        timeEl.setCustomValidity("");
    });

    $(document).on(
        "change input",
        'form input[name="pickup_date"], form input[name="pickup_time"]',
        function () {
            var f = this.form;
            if (!f || !formPostsBookingSession(f)) {
                return;
            }
            var t = f.querySelector('input[name="pickup_time"]');
            if (t) {
                t.setCustomValidity("");
            }
        },
    );
});
