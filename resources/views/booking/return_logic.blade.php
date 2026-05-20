 <style>
     .pac-container {
  z-index: 10000 !important;
}
 </style>   
<script>
// Add a stop location (dynamically create stop inputs)
function geolocate() {

}
jQuery(document).ready(function($) {
    $('.toggle_vehicleSelect').click(function(){
        $('.vehicle_container').removeClass('hide');
        $('.vehicle_container').addClass('show');
    });

});
const brandMapStyle = [
//   {
//     featureType: 'road',
//     elementType: 'geometry',
//     stylers: [{ color: '#243763' }] // custom road color
//   },
//   {
//     featureType: 'road',
//     elementType: 'labels.text.fill',
//     stylers: [{ color: '#ffffff' }] // custom road label text
//   },
  {
    elementType: 'labels.text.stroke',
    stylers: [{ color: '#1a2a4f' }] // custom stroke for labels (e.g. pins)
  },
  {
    elementType: 'labels.text.fill',
    stylers: [{ color: '#ffffff' }] // custom fill for labels (e.g. pins)
  }
];

let directionsService, directionsRenderer;
let autocompletePickup, autocompleteDropoff;
let stopInputs = [];
let stopAutocompletes = [];

function initMapWithRoute(pickupPlace, dropoffPlace, waypoints = []) {
    if (!pickupPlace || !dropoffPlace) return;

    $('#hide_on_map').hide();

    const map = new google.maps.Map(document.getElementById('map'), {
        center: pickupPlace.geometry.location,
        zoom: 12,
        styles: brandMapStyle,
        disableDefaultUI: true // optional: cleaner look
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: false,
        polylineOptions: {
            strokeColor: '#f85a14',
            strokeWeight: 5
        }
    });

    const request = {
        origin: pickupPlace.geometry.location,
        destination: dropoffPlace.geometry.location,
        waypoints: waypoints,
        travelMode: 'DRIVING'
    };

    directionsService.route(request, function (response, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(response);
        } else {
            alert('Route error: ' + status);
        }
    });
}

function calculateDistance(pickupPlace, dropoffPlace, callback) {
    const distanceService = new google.maps.DistanceMatrixService();
    distanceService.getDistanceMatrix({
        origins: [pickupPlace.geometry.location],
        destinations: [dropoffPlace.geometry.location],
        travelMode: 'DRIVING',
        unitSystem: google.maps.UnitSystem.IMPERIAL
    }, function (response, status) {
        if (status === 'OK') {
            const element = response.rows[0].elements[0];
            const distanceText = element.distance.text;
            const durationText = element.duration.text;

            console.log('Distance:', distanceText);
            console.log('Duration:', durationText);

            const infoBox = document.getElementById('route-info-box');
            if (infoBox) {
                document.getElementById('route-distance').textContent = distanceText;
                document.getElementById('route-duration').textContent = durationText;
                infoBox.style.display = 'block';
            }

            if (typeof callback === 'function') {
                callback(distanceText, durationText);
            }
        } else {
            alert('Distance calculation failed: ' + status);
        }
    });
}

function calculateRoute() {
    const pickupPlace = autocompletePickup.getPlace();
    const dropoffPlace = autocompleteDropoff.getPlace();
    if (!pickupPlace || !dropoffPlace) return;

    const waypoints = [];
    stopAutocompletes.forEach(ac => {
        const stopPlace = ac.getPlace();
        if (stopPlace && stopPlace.geometry) {
            waypoints.push({
                location: stopPlace.geometry.location,
                stopover: true
            });
        }
    });

    initMapWithRoute(pickupPlace, dropoffPlace, waypoints);
    calculateDistance(pickupPlace, dropoffPlace);
}

function onLocationChanged() {
    const pickupPlace = autocompletePickup.getPlace();
    const dropoffPlace = autocompleteDropoff.getPlace();
    if (pickupPlace && dropoffPlace) {
        document.getElementById('map').style.display = 'block';
        initMapWithRoute(pickupPlace, dropoffPlace);
        calculateDistance(pickupPlace, dropoffPlace);
    }
}

function addStop() {
    const stopContainer = document.getElementById('stop-container');
    const stopInput = document.createElement("div");
    stopInput.classList.add("mb-3");
    stopInput.innerHTML = `
        <label for="stop-location" class="form-label">Stop Location</label>
        <div class="input-group">
            <input type="text" class="form-control stop-location-input" placeholder="Enter Stop Location" required>
            <span class="input-group-text remove-stop-btn" onclick="removeStop(this)">🗑️</span>
        </div>`;
    stopContainer.appendChild(stopInput);
    initializeStopAutocomplete();
    calculateRoute();
}

function removeStop(element) {
    const stopContainer = document.getElementById('stop-container');
    const stopDiv = element.parentElement.parentElement;
    const input = stopDiv.querySelector('input');
    const index = stopInputs.indexOf(input);
    if (index !== -1) {
        stopInputs.splice(index, 1);
        stopAutocompletes.splice(index, 1);
    }
    stopContainer.removeChild(stopDiv);
    calculateRoute();
}

function initializeStopAutocomplete() {
    const stopContainer = document.querySelectorAll(`.stop-location-input`);
    Array.from(stopContainer).forEach(input => {
        if (!stopInputs.includes(input)) {
            stopInputs.push(input);
            const autocomplete = new google.maps.places.Autocomplete(input, { types: ['geocode'] });
            autocomplete.inputElement = input;
            stopAutocompletes.push(autocomplete);
            autocomplete.addListener('place_changed', () => {
                calculateRoute();
            });
        }
    });
}

function initAutocomplete() {
    const input1 = document.getElementById('return-pickup-location');
    const input2 = document.getElementById('return-dropoff-location');
    const options = {
        fields: ["address_components", "geometry", "icon", "name"],
        strictBounds: false,
        types: ['geocode', 'establishment']
    };

    autocompletePickup = new google.maps.places.Autocomplete(input1, options);
    autocompleteDropoff = new google.maps.places.Autocomplete(input2, options);

    autocompletePickup.addListener('place_changed', onLocationChanged);
    autocompleteDropoff.addListener('place_changed', onLocationChanged);

    initializeStopAutocomplete();

    autocompletePickup.addListener('place_changed', () => {
        const place = autocompletePickup.getPlace();
        let isAirport = false;
        if (place.name && place.name.toLowerCase().includes('airport')) isAirport = true;
        if (place.address_components) {
            for (const component of place.address_components) {
                if (component.types.includes('airport') ||
                    component.long_name.toLowerCase().includes('airport') ||
                    component.short_name.toLowerCase().includes('airport')) {
                    isAirport = true;
                    break;
                }
            }
        }
        document.getElementById('is-airport').value = isAirport ? '1' : '0';
    });
}

function loadInvertedMap(originalPickup, originalDropoff) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: originalDropoff }, function(results1, status1) {
        if (status1 === 'OK') {
            geocoder.geocode({ address: originalPickup }, function(results2, status2) {
                if (status2 === 'OK') {
                    const invertedPickup = results1[0].geometry.location;
                    const invertedDropoff = results2[0].geometry.location;
                    const modalMap = new google.maps.Map(document.getElementById('modal-map'), {
                        center: invertedPickup,
                          zoom: 12,
        styles: brandMapStyle,
        disableDefaultUI: true // optional: cleaner look
                    });
       
                    const directionsService = new google.maps.DirectionsService();
                    const directionsRenderer = new google.maps.DirectionsRenderer({
                        map: modalMap,
                        polylineOptions: {
                            strokeColor: '#f85a14',
                            strokeWeight: 5
                        }
                    });
                    const request = {
                        origin: invertedPickup,
                        destination: invertedDropoff,
                        travelMode: 'DRIVING'
                    };
                    directionsService.route(request, function(response, status) {
                        if (status === 'OK') {
                            directionsRenderer.setDirections(response);
                        } else {
                            alert('Could not display route: ' + status);
                        }
                    });
                } else {
                    alert('Geocode failed: ' + status2);
                }
            });
        } else {
            alert('Geocode failed: ' + status1);
        }
    });
}

jQuery(document).ready(function($) {
    $('.toggle_vehicleSelect').click(function(){
        $('.vehicle_container').removeClass('hide').addClass('show');
    });

    const checkbox = document.getElementById('no-flight-info-checkbox');
    const flightFields = document.getElementById('flight-fields');
    if (checkbox && flightFields) {
        checkbox.addEventListener('change', function () {
            flightFields.style.display = this.checked ? 'none' : 'flex';
        });
    }

    const meetOption = document.getElementById('meet-option');
    const insidePickupFee = document.getElementById('inside-pickup-fee');
    if (meetOption) {
        meetOption.addEventListener('change', function () {
            insidePickupFee.value = this.value === 'inside' ? 30 : 0;
        });
    }

    const returnServiceCheckbox = document.getElementById('return-service');
    if (returnServiceCheckbox) {
        returnServiceCheckbox.addEventListener('change', function () {
            if (this.checked) {
                const pickupFromSession = @json(session('pickup_location'));
                const dropoffFromSession = @json(session('dropoff_location'));
                document.getElementById('return-pickup-location').value = dropoffFromSession;
                document.getElementById('return-dropoff-location').value = pickupFromSession;
                loadInvertedMap(dropoffFromSession, pickupFromSession);
                $('#returnReservationModal').modal('show');
            }
        });
    }
});

        
        </script>
