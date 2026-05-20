<?php

namespace App\Http\Controllers;

use App\Jobs\CreateBookingDocs;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Booker;
use App\Models\FlightDetail;
use App\Models\RateCityGroup;
use App\Models\RateCityGroupVehicleRate;
use App\Models\RateVehicle;
use App\Models\RateVehicleCity;
use App\Models\ReturnService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class BookingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session()->has('_token')) {
                return redirect()->route('booking');
            }
            return $next($request);
        })->except(['showForm', 'BookNow', 'AirportTransfer', 'ThankYou', 'handlePointToPoint', 'handleHourlyHire', 'userLogin']);
    }

    public function userLogin($id, $price)
    {
        $vehicle = Vehicle::findOrFail($id);
        $pickup = session('pickup_location');
        $dropoff = session('dropoff_location');
        $stops = json_decode(session('stops', '[]'), true);
        $hours = session('select_hours');
        $serviceType = session('service_type');

        if ($serviceType === 'hourlyHire') {
            $result = $this->calculateDistanceWithStops(
                $pickup,
                null,
                $stops ?? [],
                $vehicle->base_fare,
                $vehicle->base_hourly_fare,
                $vehicle->per_km_rate,
                $hours
            );
        } else {
            $distance = session('route_distance_km');
            if (!$distance) {
                $distance = $this->computeRouteDistance($pickup, $dropoff, $stops ?? []);
                session(['route_distance_km' => $distance]);
            }
            $result = $this->calculateDistanceBasedPrice($vehicle, (float)$distance);
        }

        $calculatedPrice = is_array($result) && isset($result['price']) ? $result['price'] : (float) $price;
        $basePrice = $calculatedPrice;
        $returnPrice = session('return_price', 0);
        $insidePickupFee = session('inside_pickup_fee', 0);
        $final = (session('return_service') && $returnPrice)
            ? ($basePrice + $returnPrice + $insidePickupFee)
            : ($basePrice + $insidePickupFee);

        session([
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->vehicle_name ?? null,
            'price' => $calculatedPrice,
            'calculated_price' => $calculatedPrice,
            'breakdown_data' => $result,
            'final_price' => $final,
        ]);

        // Already authenticated users have no reason to see the
        // "Login or Continue as Guest" screen. Submitting either form
        // here would hit a `guest`-only route and bounce them out of
        // the booking flow, so skip straight to passenger info.
        if (Auth::check()) {
            $user = Auth::user();
            session()->put('booker_first_name', $user->first_name);
            session()->put('booker_last_name', $user->last_name);
            session()->put('booker_email', $user->email);

            return redirect()->route('submit.passenger.info');
        }

        return view('booking.user_login', [
            'step' => 3,
            'seo' => [
                'title' => 'Login or Continue as Guest | Dallas Limo And Black Cars',
                'description' => 'Sign in to your account or continue as a guest to complete your luxury car service booking in Dallas.',
                'keywords' => 'Dallas black car booking, luxury car service login, airport transfer booking Dallas',
                'og_title' => 'Login or Continue as Guest | Dallas Limo And Black Cars',
                'og_description' => 'Sign in to your account or continue as a guest to complete your luxury car service booking in Dallas.',
                'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
            ]
        ]);
    }

    // Show the form for Point to Point or Hourly Hire
    public function showForm(Request $request)
    {
        if ($request->edit) {
            session(['edit' => 1]);
        }

        // A completed booking leaves this flag set; the home route is outside
        // checkBookingCompletion, so it never got cleared. Allow a new search
        // without the next booking URL being blocked or fully flushed.
        if (session('booking_completed')) {
            session()->forget('booking_completed');
        }

        $seo = [
            'title' => 'Dallas Limo And Black Cars | Luxury Chauffeur and Airport Car Service',
            'description' => 'Experience reliable Dallas Limo And Black Cars for airport transfers, corporate travel & luxury rides. 24/7 chauffeurs, clean fleet & on-time service across DFW.',
            'keywords' => 'Dallas Limo And Black Cars, Dallas Black Car Service, Black car service near me, Luxury Car Service Dallas',
            'og_title' => 'Dallas Limo And Black Cars',
            'og_description' => 'Experience reliable Dallas Limo And Black Cars for airport transfers, corporate travel & luxury rides. 24/7 chauffeurs, clean fleet & on-time service across DFW.',
            'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png'),
        ];

        return view('pages.home', [
            'backgroundImage' => 'assets/new_theme/img/banner-1.webp',
            'seo' => $seo
        ]);
    }

    // Handle Point to Point form submission
    public function handlePointToPoint(Request $request)
    {
        Log::info('booking.point_to_point.enter', [
            'method' => $request->method(),
            'full_url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'secure' => $request->secure(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'has_csrf_token' => $request->has('_token'),
            'session_key_count' => $request->hasSession() ? count($request->session()->all()) : 0,
        ]);

        session([
            'booking_completed' => false
        ]);
        // If it's a GET (i.e., user clicked back)
        if ($request->isMethod('get')) {
            $sessionData = session()->only([
                'pickup_location',
                'dropoff_location',
                'pickup_date',
                'pickup_time',
                'stops',
                'is_airport'
            ]);

            if (empty($sessionData['pickup_location']) || empty($sessionData['dropoff_location'])) {
                Log::warning('booking.point_to_point.get_redirect_home_missing_session', [
                    'session_id' => $request->session()->getId(),
                    'pickup_empty' => empty($sessionData['pickup_location']),
                    'dropoff_empty' => empty($sessionData['dropoff_location']),
                    'session_snapshot_keys' => array_keys(session()->all()),
                ]);

                return redirect()->route('booking'); // or wherever the user should be
            }

            Log::info('booking.point_to_point.get_ok_show_confirmation', [
                'session_id' => $request->session()->getId(),
            ]);

            $vehicles = Vehicle::with(['carSeat'])->get();
            $stops = json_decode($sessionData['stops'] ?? '[]', true);
            $distance = $this->computeRouteDistance($sessionData['pickup_location'], $sessionData['dropoff_location'], $stops);
            session(['route_distance_km' => $distance]);
            $distanceData = [];
            foreach ($vehicles as $vehicle) {
                $distanceData[$vehicle->id] = $this->calculateDistanceBasedPrice($vehicle, (float)$distance);
            }

            return view('booking.confirmation', [
                'step' => 2,
                'data' => $vehicles,
                'distance' => $distanceData,
                'userData' => $sessionData,
                'service_type' => 'pointToPoint',
                'seo' => [
                    'title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                    'description' => 'Choose your luxury vehicle for your Dallas car service. Select from sedans, SUVs, sprinter vans and more.',
                    'keywords' => 'Dallas black car booking, luxury car selection, airport transfer vehicles Dallas',
                    'og_title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                    'og_description' => 'Choose your luxury vehicle for your Dallas car service.',
                    'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
                ]
            ]);
        }

        $pickup_date = null;
        $pickup_time = null;

        if ($request->filled('pickup_datetime')) {
            $pickupDateTime = Carbon::parse($request->pickup_datetime);
            $pickup_date = $pickupDateTime->format('Y-m-d');
            $pickup_time = $pickupDateTime->format('H:i:s');
        } elseif ($request->filled('pickup_date') && $request->filled('pickup_time')) {
            $pickup_date = $request->pickup_date;
            $pickup_time = Carbon::parse($request->pickup_time)->format('H:i:s');
        }

        $return_date = null;
        $return_time = null;
        if ($request->filled('return_datetime_hourly')) {
            $returnDateTime = Carbon::parse($request->return_datetime_hourly);
            $return_date = $returnDateTime->format('Y-m-d');
            $return_time = $returnDateTime->format('H:i:s');
        } elseif ($request->filled('return_date') && $request->filled('return_time')) {
            $return_date = $request->return_date;
            $return_time = Carbon::parse($request->return_time)->format('H:i:s');
        }

        $rules = [
            'pickup_location' => 'required|string',
            'dropoff_location' => 'required|string',
            'stops' => 'nullable|array',
            'stops.*' => 'string',
            'is_airport' => 'int'
        ];

        if (!$request->filled('pickup_datetime')) {
            $rules['pickup_date'] = 'required';
            $rules['pickup_time'] = 'required';
        } else {
            $rules['pickup_datetime'] = 'required';
        }

        Log::info('booking.point_to_point.post_before_validate', [
            'session_id' => $request->session()->getId(),
            'has_pickup_location' => $request->filled('pickup_location'),
            'has_dropoff_location' => $request->filled('dropoff_location'),
            'has_pickup_date' => $request->filled('pickup_date'),
            'has_pickup_time' => $request->filled('pickup_time'),
            'pickup_date_raw' => $request->input('pickup_date'),
            'pickup_time_raw' => $request->input('pickup_time'),
            'is_airport_raw' => $request->input('is_airport'),
            'parsed_pickup_date' => $pickup_date,
            'parsed_pickup_time' => $pickup_time,
        ]);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('booking.point_to_point.validation_failed', [
                'session_id' => $request->session()->getId(),
                'errors' => $validator->errors()->toArray(),
                'input_keys' => array_keys($request->except(['password', '_token'])),
            ]);

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        $data['pickup_date'] = $pickup_date;
        $data['pickup_time'] = $pickup_time;

        $vehicles = Vehicle::with(['carSeat'])->get();
        $stops = $data['stops'] ?? [];
        $distance = $this->computeRouteDistance($data['pickup_location'], $data['dropoff_location'], $stops);
        session(['route_distance_km' => $distance]);
        $distanceData = [];
        foreach ($vehicles as $vehicle) {
            $distanceData[$vehicle->id] = $this->calculateDistanceBasedPrice($vehicle, (float)$distance);
        }

        $return_datetime = $request->return_datetime_hourly
            ?: (($return_date && $return_time) ? $return_date . ' ' . $return_time : null);

        session([
            'pickup_location' => $data['pickup_location'],
            'dropoff_location' => $data['dropoff_location'],
            'is_airport' => $data['is_airport'],
            'pickup_date' => $data['pickup_date'],
            'pickup_time' => $data['pickup_time'],
            'return_date' => $return_date,
            'return_time' => $return_time,
            'round_trip' => $request->round_trip,
            'return_datetime' => $return_datetime,
            'select_hours' => null,
            'stops' => json_encode($data['stops'] ?? []),
            'service_type' => 'pointToPoint',
        ]);

        Log::info('booking.point_to_point.post_success', [
            'session_id' => $request->session()->getId(),
            'route_distance_km' => $distance,
            'pickup_date' => $data['pickup_date'],
            'pickup_time' => $data['pickup_time'],
        ]);

        return view('booking.confirmation', [
            'step' => 2,
            'data' => $vehicles,
            'distance' => $distanceData, // mapped per vehicle
            'userData' => $data,
            'service_type' => 'pointToPoint',
            'seo' => [
                'title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                'description' => 'Choose your luxury vehicle for your Dallas car service. Select from sedans, SUVs, sprinter vans and more.',
                'keywords' => 'Dallas black car booking, luxury car selection, airport transfer vehicles Dallas',
                'og_title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                'og_description' => 'Choose your luxury vehicle for your Dallas car service.',
                'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
            ]
        ]);
    }

    // Handle Hourly Hire form submission
    public function handleHourlyHire(Request $request)
    {
        session()->forget('round_trip');
        session([
            'booking_completed' => false
        ]);

        if ($request->isMethod('get')) {
            $sessionData = session()->only([
                'pickup_location',
                'select_hours',
                'pickup_date',
                'pickup_time',
                'stops',
            ]);

            if (empty($sessionData['pickup_location']) || empty($sessionData['select_hours'])) {
                return redirect()->route('booking');
            }

            $vehicles = Vehicle::with(['carSeat'])->get();
            $stops = json_decode($sessionData['stops'] ?? '[]', true);
            $distanceData = [];

            foreach ($vehicles as $vehicle) {
                $distanceData[$vehicle->id] = $this->calculateDistanceWithStops(
                    $sessionData['pickup_location'],
                    null,
                    $stops ?? [],
                    $vehicle->base_fare,
                    $vehicle->base_hourly_fare,
                    $vehicle->per_km_rate,
                    (int) $sessionData['select_hours']
                );
            }

            return view('booking.confirmation', [
                'step' => 2,
                'data' => $vehicles,
                'distance' => $distanceData,
                'userData' => [
                    'pickup_location_hourly' => $sessionData['pickup_location'],
                    'select_hours' => (int) $sessionData['select_hours'],
                    'pickup_date' => $sessionData['pickup_date'],
                    'pickup_time' => $sessionData['pickup_time'],
                    'stops' => $stops ?? [],
                ],
                'service_type' => 'hourlyHire',
                'seo' => [
                    'title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                    'description' => 'Choose your luxury vehicle for your Dallas car service. Select from sedans, SUVs, sprinter vans and more.',
                    'keywords' => 'Dallas black car booking, luxury car selection, airport transfer vehicles Dallas',
                    'og_title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                    'og_description' => 'Choose your luxury vehicle for your Dallas car service.',
                    'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
                ]
            ]);
        }

        $validator = Validator::make($request->all(), [
            'pickup_location_hourly' => 'required|string',
            'select_hours' => 'required|integer|min:1|max:24',
            'stops' => 'nullable|array',
            'stops.*' => 'string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pickupDateTime = null;
        if ($request->filled('pickup_datetime_hourly')) {
            try {
                $pickupDateTime = Carbon::parse($request->input('pickup_datetime_hourly'));
            } catch (\Exception $e) {
                $pickupDateTime = null;
            }
        } elseif ($request->filled('pickup_date') && $request->filled('pickup_time')) {
            try {
                $pickupDateTime = Carbon::parse($request->input('pickup_date') . ' ' . $request->input('pickup_time'));
            } catch (\Exception $e) {
                $pickupDateTime = null;
            }
        }

        if (!$pickupDateTime) {
            return redirect()->back()->withErrors(['pickup_datetime_hourly' => 'Please provide a valid pickup date and time.'])->withInput();
        }

        $data = $validator->validated();
        $data['pickup_date'] = $pickupDateTime->format('Y-m-d');
        $data['pickup_time'] = $pickupDateTime->format('H:i');

        $vehicles = Vehicle::with(['carSeat'])->get();

        $distanceData = [];
        foreach ($vehicles as $vehicle) {
            $baseFare = $vehicle->base_fare;
            $hourlyFare = $vehicle->base_hourly_fare;
            $perKmRate = $vehicle->per_km_rate;

            $distanceData[$vehicle->id] = $this->calculateDistanceWithStops(
                $data['pickup_location_hourly'],
                null,
                $data['stops'] ?? [],
                $baseFare,
                $hourlyFare,
                $perKmRate,
                $data['select_hours']
            );
        }

        session([
            'pickup_location' => $data['pickup_location_hourly'],
            'dropoff_location' => null,
            'select_hours' => $data['select_hours'],
            'pickup_date' => $data['pickup_date'],
            'pickup_time' => $data['pickup_time'],
            'stops' => json_encode($data['stops'] ?? []),
            'service_type' => 'hourlyHire'
        ]);

        return view('booking.confirmation', [
            'step' => 2,
            'data' => $vehicles,
            'distance' => $distanceData,
            'userData' => $data,
            'service_type' => 'hourlyHire',
            'seo' => [
                'title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                'description' => 'Choose your luxury vehicle for your Dallas car service. Select from sedans, SUVs, sprinter vans and more.',
                'keywords' => 'Dallas black car booking, luxury car selection, airport transfer vehicles Dallas',
                'og_title' => 'Select Your Vehicle | Dallas Limo And Black Cars',
                'og_description' => 'Choose your luxury vehicle for your Dallas car service.',
                'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
            ]
        ]);
    }


    // Helper function to simulate distance calculation
    // private function calculateDistance($pickup, $dropoff = null,array $stops)
    // {
    //     $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
    //         'origins' => $pickup,
    //         'destinations' => $dropoff,
    //         'key' => "AIzaSyBtkrakOJ7xvcL2FIF5XbhCV1PIaNKz4zQ",
    //     ]);
    //     $data = $response->json();
    //     if ($data['status'] !== 'OK' || empty($data['rows'][0]['elements'][0]['distance'])) {
    //         return ['error' => 'Distance calculation failed'];
    //     }


    //     $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
    //     $distanceKm = $distanceMeters / 1000;
    //     $baseFare = 5;
    //     $perKmRate = 1.5;
    //     $price = $baseFare + ($distanceKm * $perKmRate);

    //     return [
    //         'distance_km' => round($distanceKm, 2),
    //         'price' => round($price, 2),
    //     ];
    // }

    private function calculateDistanceWithStops($pickup, $dropoff, array $stops, $baseFare, $hourlyFare, $perKmRate, $hours = null)
    {
        if ($dropoff == null) {
            // Hourly booking only (no dropoff or stops)
            $price =  ($hourlyFare * $hours);

            return [
                'distance_km' => 0,
                'price' => round($price, 2),
                'baseFare' => $baseFare,
                'hourlyFare' => $hourlyFare,
                'hours' => $hours,
                'type' => 'Hourly'
            ];
        }

        $locations = array_filter([$pickup, ...$stops, $dropoff]);
        $totalDistance = 0;

        for ($i = 0; $i < count($locations) - 1; $i++) {
            $origin = $locations[$i];
            $destination = $locations[$i + 1];

            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => config('services.google_maps.api_key'),
            ]);

            $data = $response->json();

            if ($data['status'] !== 'OK' || empty($data['rows'][0]['elements'][0]['distance'])) {
                return ['error' => 'Distance calculation failed between ' . $origin . ' and ' . $destination];
            }

            $segmentDistance = $data['rows'][0]['elements'][0]['distance']['value']; // in meters
            $totalDistance += $segmentDistance;
        }

        $distanceMiles = $totalDistance / 1609.34; //miles
        // echo $baseFare ."+".($distanceMiles .'*'. $perKmRate);
        // exit();
        $price = $baseFare + ($distanceMiles * $perKmRate);
        $totalPrice = $price + ($hours ? ($hourlyFare * $hours) : 0);

        return [
            'distance_km' => round($distanceMiles, 2), // Legacy key, actually returns miles
            'distance_miles' => round($distanceMiles, 2),
            'price' => round($totalPrice, 2),
            'baseFare' => $baseFare,
            'hourlyFare' => $hourlyFare,
            'perKmRate' => $perKmRate,
            'hours' => $hours,
            'type' => !empty($dropoff) ? 'PointToPoint' : 'Hourly'
        ];
    }
    private function computeRouteDistance($pickup, $dropoff, array $stops)
    {
        $locations = array_filter([$pickup, ...$stops, $dropoff]);
        $total = 0;
        for ($i = 0; $i < count($locations) - 1; $i++) {
            $origin = $locations[$i];
            $destination = $locations[$i + 1];
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => config('services.google_maps.api_key'),
            ]);
            $data = $response->json();
            if ($data['status'] !== 'OK' || empty($data['rows'][0]['elements'][0]['distance'])) {
                return 0;
            }
            $segment = $data['rows'][0]['elements'][0]['distance']['value'];
            $total += $segment;
        }
        return $total / 1609.34;
    }

    public function calculateReturnTrip(Request $request)
    {
        $pickup = $request->input('pickup_location');
        $dropoff = $request->input('dropoff_location');
        $vehicleId = $request->input('vehicle_id');

        // Validate input
        if (!$pickup || !$dropoff || !$vehicleId) {
            return response()->json(['success' => false, 'message' => 'Missing required data']);
        }

        $vehicle = Vehicle::findOrFail($vehicleId);
        $distanceData = $this->calculateDistanceWithStops(
            $pickup,
            $dropoff,
            $stops ?? [],
            $vehicle->base_fare,
            null,
            $vehicle->per_km_rate,
            $vehicle->base_hourly_fare
        );
        $computed = $this->calculateDistanceBasedPrice($vehicle, (float)$distanceData['distance_km']);
        $baseFare = $computed['baseFare'];
        $perKmRate = $computed['perKmRate'];
        $price = $computed['price'];

        // Store in session (for use in Blade later)
        session([
            'return_base_fare' => $baseFare,
            'return_per_km_rate' => $perKmRate,
            'return_km' => round($distanceData['distance_km'], 2),
            'return_price' => round($price, 2),
            'return_breakdown_data' => [
                'distance_km' => round($computed['distance_km'], 2),
                'distance_miles' => round($computed['distance_miles'], 2),
                'price' => round($computed['price'], 2),
                'baseFare' => $computed['baseFare'],
                'perKmRate' => $computed['perKmRate'],
                'type' => 'PointToPoint'
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return trip calculated',
            'data' => session('return_breakdown_data')
        ]);
    }

    public function showAll()
    {
        $vehicles = Vehicle::with(['carSeat'])->get();
        return response()->json($vehicles);
    }

    public function saveBookingFormSession(Request $request)
    {
        $formType = $request->input('form_type', '');
        $expectsJson = $request->ajax() || $request->wantsJson();

        if ($formType === 'guest_info') {
            $sanitizedNumber = preg_replace('/[^\d+]/', '', trim($request->input('number', '') ?? ''));
            session([
                'first_name' => $request->input('first_name', ''),
                'last_name' => $request->input('last_name', ''),
                'email' => $request->input('email', ''),
                'number' => $sanitizedNumber ?: $request->input('number', ''),
            ]);
            session()->put('guest', [
                'first_name' => $request->input('first_name', ''),
                'last_name' => $request->input('last_name', ''),
                'email' => $request->input('email', ''),
                'number' => $sanitizedNumber ?: $request->input('number', ''),
            ]);
        } elseif ($formType === 'booking_detail') {
            session([
                'pickup_flight_details' => $request->input('pickup_flight_details', ''),
                'flight_number' => $request->input('flight_number', ''),
                'meet_option' => $request->input('meet_option', 'none') ?: null,
                'no_flight_info' => $request->has('no_flight_info') ? 1 : 0,
                'note' => $request->input('note', ''),
                'return_flight_details' => $request->input('return_flight_details', ''),
                'return_flight_number' => $request->input('return_flight_number', ''),
                'return_no_flight_info' => $request->has('return_no_flight_info') ? 1 : 0,
            ]);
            if ($request->input('meet_option') === 'none') {
                session(['meet_option' => null]);
            }
        } elseif ($formType === 'ride_info_point_to_point') {
            $validator = Validator::make($request->all(), [
                'pickup_location' => 'required|string',
                'dropoff_location' => 'required|string',
                'pickup_date' => 'required|date',
                'pickup_time' => 'required',
                'return_date' => 'nullable|date',
                'return_time' => 'nullable',
                'stops' => 'nullable|array',
                'stops.*' => 'string',
                'is_airport' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                if ($expectsJson) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();
            $pickupTime = Carbon::parse($validated['pickup_time'])->format('H:i:s');
            $returnTime = !empty($validated['return_time'])
                ? Carbon::parse($validated['return_time'])->format('H:i:s')
                : null;

            session([
                'booking_completed' => false,
                'pickup_location' => $validated['pickup_location'],
                'dropoff_location' => $validated['dropoff_location'],
                'is_airport' => (int) ($validated['is_airport'] ?? 0),
                'pickup_date' => $validated['pickup_date'],
                'pickup_time' => $pickupTime,
                'return_date' => $validated['return_date'] ?? null,
                'return_time' => $returnTime,
                'round_trip' => $request->boolean('round_trip') ? 'on' : null,
                'return_datetime' => (!empty($validated['return_date']) && $returnTime)
                    ? $validated['return_date'] . ' ' . $returnTime
                    : null,
                'select_hours' => null,
                'stops' => json_encode($validated['stops'] ?? []),
                'service_type' => 'pointToPoint',
            ]);

            if (!$expectsJson) {
                return redirect()->route('booking.pointToPoint');
            }
        } elseif ($formType === 'ride_info_hourly') {
            $validator = Validator::make($request->all(), [
                'pickup_location_hourly' => 'required|string',
                'select_hours' => 'required|integer|min:1|max:24',
                'pickup_date' => 'required|date',
                'pickup_time' => 'required',
                'stops' => 'nullable|array',
                'stops.*' => 'string',
            ]);

            if ($validator->fails()) {
                if ($expectsJson) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            session()->forget('round_trip');
            session([
                'booking_completed' => false,
                'pickup_location' => $validated['pickup_location_hourly'],
                'dropoff_location' => null,
                'select_hours' => (int) $validated['select_hours'],
                'pickup_date' => $validated['pickup_date'],
                'pickup_time' => Carbon::parse($validated['pickup_time'])->format('H:i'),
                'stops' => json_encode($validated['stops'] ?? []),
                'service_type' => 'hourlyHire',
            ]);

            if (!$expectsJson) {
                return redirect()->route('booking.hourlyHire');
            }
        }

        return response()->json(['success' => true]);
    }

    public function submitPassengerInfo(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            session()->put('booker_first_name', $user->first_name);
            session()->put('booker_last_name', $user->last_name);
            session()->put('booker_email', $user->email);
        }
        // First check for required session data before any other processing
        if (!session('pickup_location') || !session('pickup_date')) {
            // If AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your session has expired. Please refresh the page and try again.',
                    'redirect' => route('booking')
                ], 419); // 419 is for CSRF token mismatch/expired
            }

            // For regular form submission
            return redirect()->route('booking');
        }

        if ($request->isMethod('get')) {
            $vehicles_all = Vehicle::with('carSeat')->get();
            $vehicles = Vehicle::where("id", session('vehicle_id'))->with('carSeat')->get();

            $pickupLocation = session('pickup_location');
            $dropoffLocation = session('dropoff_location');
            $stops = json_decode(session('stops', '[]'), true);

            $distance = session('route_distance_km');
            if (!$distance) {
                $distance = $this->computeRouteDistance($pickupLocation, $dropoffLocation, $stops);
                session(['route_distance_km' => $distance]);
            }

            $distanceData = [];
            foreach ($vehicles_all as $vehicle) {
                $distanceData[$vehicle->id] = $this->calculateDistanceBasedPrice($vehicle, (float)$distance);
            }

            $selectedId = session('vehicle_id');
            $airports = \App\Models\Airport::orderBy('id')->get();
            if ($selectedId && isset($distanceData[$selectedId]) && empty($distanceData[$selectedId]['error'])) {
                $basePrice = (float)($distanceData[$selectedId]['price'] ?? 0);
                $insidePickupFee = (float)session('inside_pickup_fee', 0);

                session([
                    'calculated_price' => $basePrice,
                    'breakdown_data' => $distanceData[$selectedId],
                ]);

                $pickupLocation = session('pickup_location');
                $dropoffLocation = session('dropoff_location');
                $isRoundTrip = session('round_trip') == 'on';

                $final = $basePrice + $insidePickupFee;

                if ($isRoundTrip && $pickupLocation && $dropoffLocation) {
                    $selectedVehicle = Vehicle::find($selectedId);
                    if ($selectedVehicle) {
                        $returnData = $this->calculateDistanceWithStops(
                            $dropoffLocation,
                            $pickupLocation,
                            [],
                            $selectedVehicle->base_fare,
                            null,
                            $selectedVehicle->per_km_rate,
                            $selectedVehicle->base_hourly_fare
                        );

                        if (empty($returnData['error'])) {
                            $computed = $this->calculateDistanceBasedPrice($selectedVehicle, (float)$returnData['distance_km']);
                            $returnPrice = (float)($computed['price'] ?? 0);
                            session([
                                'return_price' => $returnPrice,
                                'return_base_fare' => $computed['baseFare'] ?? $selectedVehicle->base_fare,
                                'return_per_km_rate' => $computed['perKmRate'] ?? $selectedVehicle->per_km_rate,
                                'return_km' => $computed['distance_km'] ?? $returnData['distance_km'],
                            ]);
                            $final = $basePrice + $returnPrice + $insidePickupFee;
                        }
                    }
                }

                session(['final_price' => $final]);
            }

            $guest = session('guest', []);
            return view('booking.booking_detail', [
                'step' => 4,
                'id' => session('vehicle_id'),
                'data' => (object)[
                    'first_name' => session('first_name') ?? $guest['first_name'] ?? null,
                    'last_name' => session('last_name') ?? $guest['last_name'] ?? null,
                    'email' => session('email') ?? $guest['email'] ?? null,
                    'number' => session('number') ?? $guest['number'] ?? null,
                    'bookingForSomeoneElse' => session('bookingForSomeoneElse'),
                    'booker_first_name' => session('booker_first_name'),
                    'booker_last_name' => session('booker_last_name'),
                    'booker_number' => session('booker_number'),
                    'booker_email' => session('booker_email'),
                ],
                'vehicles' => $vehicles,
                'vehicles_all' => $vehicles_all,
                'distance' => $distanceData,
                'airports' => $airports,
                'seo' => [
                    'title' => 'Booking Details | Dallas Limo And Black Cars',
                    'description' => 'Complete your booking details for Dallas luxury car service. Add passenger information and special requests.',
                    'keywords' => 'Dallas black car booking, luxury car service booking, airport transfer booking Dallas',
                    'og_title' => 'Booking Details | Dallas Limo And Black Cars',
                    'og_description' => 'Complete your booking details for Dallas luxury car service.',
                    'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
                ]
            ]);
        }

        // Rest of your POST handling code...
        $rules = array_merge([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string',
            'number' => 'required|string',
            'bookingForSomeoneElse' => 'nullable|boolean',
        ], $request->bookingForSomeoneElse ? [
            'booker_first_name' => 'required|string',
            'booker_last_name' => 'required|string',
            'booker_number' => 'required|string',
            'booker_email' => 'required|email',
        ] : []);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Log the error or return a response for debugging
            // dd($validator->errors());
            // Log::error('Validation failed', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sanitizedNumber = preg_replace('/[^\d+]/', '', trim($request->number ?? ''));
        $sanitizedBookerNumber = $request->bookingForSomeoneElse
            ? preg_replace('/[^\d+]/', '', trim($request->booker_number ?? ''))
            : null;

        if ($request->bookingForSomeoneElse) {
            session([
                'bookingForSomeoneElse' => true,
                'booker_first_name' => $request->booker_first_name,
                'booker_last_name' => $request->booker_last_name,
                'booker_number' => $sanitizedBookerNumber ?: $request->booker_number,
                'booker_email' => $request->booker_email,
            ]);
        } else {
            session([
                'bookingForSomeoneElse' => false,
            ]);
        }

        $vehicles = Vehicle::where("id", session('vehicle_id'))->with(['carSeat'])->get();

        session([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'number' => $sanitizedNumber ?: $request->number,
            "bookingForSomeoneElse" => $request->bookingForSomeoneElse ?? false
        ]);

        $vehicles_all = Vehicle::with(['carSeat'])->get();

        $distanceData = [];
        $pickupLocation = session('pickup_location');
        $dropoffLocation = session('dropoff_location');
        $stops = json_decode(session('stops', '[]'), true);
        $distance = session('route_distance_km');
        if (!$distance) {
            $distance = $this->computeRouteDistance($pickupLocation, $dropoffLocation, $stops);
            session(['route_distance_km' => $distance]);
        }
        foreach ($vehicles_all as $vehicle) {
            $price = $vehicle->base_fare + ((float)$distance * $vehicle->per_km_rate);
            $distanceData[$vehicle->id] = [
                'distance_km' => round((float)$distance, 2),
                'price' => round($price, 2),
                'baseFare' => $vehicle->base_fare,
                'hourlyFare' => null,
                'perKmRate' => $vehicle->per_km_rate,
                'hours' => null,
                'type' => 'PointToPoint'
            ];
        }


        $airports = \App\Models\Airport::orderBy('id')->get();

        return view('booking.booking_detail', [
            'step' => 4,
            'id' => session('vehicle_id'),
            "data" => $request,
            'vehicles' => $vehicles,
            'vehicles_all' => $vehicles_all,
            'distance' => $distanceData,
            'airports' => $airports,
            'seo' => [
                'title' => 'Booking Details | Dallas Limo And Black Cars',
                'description' => 'Complete your booking details for Dallas luxury car service. Add passenger information and special requests.',
                'keywords' => 'Dallas black car booking, luxury car service booking, airport transfer booking Dallas',
                'og_title' => 'Booking Details | Dallas Limo And Black Cars',
                'og_description' => 'Complete your booking details for Dallas luxury car service.',
                'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
            ]
        ]);
    }

    public function bookRide(Request $request)
    {

        $user = null;
        $cards = [];
        // Validate request fields
        $validated = $request->validate([
            'pickup_flight_details' => 'nullable|string|max:255',
            'flight_number' => 'nullable|string|max:50',
            'meet_option' => 'nullable|string|in:curbside,inside,none',
            'inside_pickup_fee' => 'nullable|numeric',
            'no_flight_info' => 'nullable',
            'return-service' => 'nullable',
            'note' => 'nullable|string|max:500',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        if (auth()->check()) {
            $user = auth()->user();
            $cardsList = PaymentMethod::all([
                'customer' => $user->stripe_customer_id,
                'type' => 'card',
            ]);
            $cards = $cardsList->data ?? [];
        }

        if (isset($validated['meet_option']) && $validated['meet_option'] === 'none') {
            $validated['meet_option'] = null;
        }

        // Normalize checkbox values (Laravel treats unchecked boxes as missing)
        $validated['return_service'] = $request->has('return-service');
        $validated['no_flight_info'] = $request->has('no_flight_info');

        $validated['meet_option'] = $validated['meet_option'] ?? null;
        // Optional: Ensure inside_pickup_fee is accurate
        $validated['inside_pickup_fee'] = 0;

        // Store data in session
        session([
            // Original values
            'pickup_flight_details' => $validated['pickup_flight_details'] ?? '',
            'flight_number' => $validated['flight_number'] ?? '',
            'meet_option' => $validated['meet_option'] ?? null,
            'inside_pickup_fee' => $validated['inside_pickup_fee'] ?? 0,
            'no_flight_info' => $validated['no_flight_info'] ?? 1,
            'return_service' => $validated['return_service'],
            'note' => $validated['note'] ?? null,

            // Additional return_* values from the request
            'return_pickup_location' => $request->input('return_pickup_location'),
            'return_dropoff_location' => $request->input('return_dropoff_location'),
            'return_pickup_date' => $request->input('return_pickup_date'),
            'return_pickup_time' => $request->input('return_pickup_time'),
            'return_flight_number' => $request->input('return_flight_number'),
            'return_flight_details' => $request->input('return_flight_details'),
            'return_no_flight_info' => $request->has('return_no_flight_info'),
            'return_vehicle_id' => $request->input('vehicle_id'),
        ]);

        // Optional: recalculate total with fee
        $basePrice = session('calculated_price', 0);
        $returnPrice = session('return_price', 0); // Fetch return price if available
        $insidePickupFee = $validated['inside_pickup_fee'] ?? 0;

        if ((session('round_trip') == 'on' && $returnPrice) || ($request->input('return_pickup_location') && $request->has('return-service'))) {
            $total = $basePrice + $returnPrice + $insidePickupFee;
        } else {
            $total = $basePrice + $insidePickupFee;
        }

        session(['final_price' => $total]);

        // Redirect to payment view (or wherever step 5 is)
        return view('booking.payment', [
            'step' => 5,
            'cards' => $cards,
            'seo' => [
                'title' => 'Payment | Dallas Limo And Black Cars',
                'description' => 'Complete your payment securely for Dallas luxury car service.',
                'keywords' => 'Dallas black car payment, luxury car service payment, airport transfer payment Dallas',
                'og_title' => 'Payment | Dallas Limo And Black Cars',
                'og_description' => 'Complete your payment securely for Dallas luxury car service.',
                'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
            ]
        ]);
    }
    private function generateUniqueBookingId(): string
    {
        do {
            $id = 'pm_' . str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Booking::where('booking_id', $id)->exists());

        return $id;
    }
    //
    public function completeBook(Request $request)
    {
        if (!session('pickup_location') || !session('pickup_date')) {
            return redirect()->route('booking');
        }

        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $user = auth()->user();
            $guest = session('guest', []);

            // Prepare booking session data
            $pickup_location     = session('pickup_location');
            $dropoff_location    = session('dropoff_location');
            $pickup_date         = session('pickup_date');
            $pickup_time         = session('pickup_time');
            $return_date         = session('return_date');
            $return_time         = session('return_time');
            $first_name          = $user->first_name ?? ($guest['first_name'] ?? null);
            $last_name           = $user->last_name  ?? ($guest['last_name'] ?? null);
            $email               = $user->email      ?? ($guest['email'] ?? null);
            $number              = $user->phone      ?? ($guest['number'] ?? null);
            $selected_price      = session('final_price', session('calculated_price'));
            $vehicle_id          = session('vehicle_id');
            $vehicle_name        = session('vehicle_name');
            $isBookingForOthers  = session('bookingForSomeoneElse') ?? false;
            $booker_first_name   = session('booker_first_name') ?? null;
            $booker_last_name    = session('booker_last_name') ?? null;
            $booker_number       = session('booker_number') ?? null;
            $booker_email        = session('booker_email') ?? null;
            $hours               = session('select_hours') ?? null;

            $flight_details = null;

            // -------------------------
            // Stripe Customer Handling
            // -------------------------
            $stripeCustomerId = null;

            if ($user) {
                // Logged-in user
                if (!$user->stripe_customer_id) {
                    $customer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->first_name . ' ' . $user->last_name,
                    ]);
                    $user->update(['stripe_customer_id' => $customer->id]);
                    $stripeCustomerId = $customer->id;
                } else {
                    $stripeCustomerId = $user->stripe_customer_id;
                }
            } else {
                // Guest user
                if (!session('stripe_customer_id')) {
                    $customer = \Stripe\Customer::create([
                        'email' => $guest['email'],
                        'name' => $guest['first_name'] . ' ' . $guest['last_name'],
                    ]);
                    session(['stripe_customer_id' => $customer->id]);
                    $stripeCustomerId = $customer->id;
                } else {
                    $stripeCustomerId = session('stripe_customer_id');
                }
            }

            try {
                // Attach PaymentMethod to Customer
                \Stripe\PaymentMethod::retrieve($request->payment_method_id)->attach([
                    'customer' => $stripeCustomerId,
                ]);

                // -------------------------
                // Create PaymentIntent
                // -------------------------
                $amountInCents = (int) round(((float) $selected_price) * 100);
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $amountInCents,
                    'currency' => 'usd',
                    'customer' => $stripeCustomerId,
                    'payment_method' => $request->payment_method_id,
                    'capture_method' => 'manual',
                    'off_session' => true,
                    'confirm' => true,
                ]);
            } catch (\Stripe\Exception\CardException $e) {
                // Log the Stripe card error
                \Log::error('Stripe Card Error: ' . $e->getError()->message);
                // Redirect back with user-friendly message
                return redirect()->back()->with('error', 'Card error: ' . $e->getError()->message);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Log any API-related errors
                \Log::error('Stripe API Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Stripe API error: ' . $e->getMessage());
            } catch (\Exception $e) {
                // Log general errors
                \Log::error('General Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
            }


            $transactionId = $paymentIntent->id;

            if ($paymentIntent->status === 'requires_action' && $paymentIntent->next_action->type === 'use_stripe_sdk') {
                return redirect()->back()->with('error', 'Payment requires additional authentication.');
            }

            // Manual capture: success state is `requires_capture` (authorized, not yet charged in Stripe)
            if (!in_array($paymentIntent->status, ['requires_capture', 'succeeded'], true)) {
                return redirect()->back()->with(
                    'error',
                    'Payment could not be authorized. Please try again or use another card. (Status: ' . $paymentIntent->status . ')'
                );
            }

            $paymentStatusLabel = $paymentIntent->status === 'succeeded' ? 'Paid' : 'Authorized';

            // -------------------------
            // Booking ID Generation
            // -------------------------
            $latestBooking = Booking::orderBy('id', 'desc')->first();
            $lastNumericId = 41100;

            if ($latestBooking && preg_match('/pm_(\d+)/', $latestBooking->booking_id, $matches)) {
                $lastNumericId = (int)$matches[1] + 1;
            }

            // Only create a Booker row when the booking is for someone else AND we
            // actually have the booker's contact info. The `booker` table requires
            // all four columns NOT NULL, so creating one with missing data fails.
            $booker = null;
            if ($isBookingForOthers
                && !empty($booker_first_name)
                && !empty($booker_last_name)
                && !empty($booker_email)
                && !empty($booker_number)
            ) {
                $booker = Booker::create([
                    'first_name' => $booker_first_name,
                    'last_name'  => $booker_last_name,
                    'email'      => $booker_email,
                    'phone_number' => $booker_number,
                ]);
            }

            $customBookingId = 'pm_' . $lastNumericId;

            // -------------------------
            // Return Service Handling
            // -------------------------
            $returnServiceId = null;
            if (session('return_service')) {
                $returnPickupDate = session('return_date') ? $this->safeFormatDate(session('return_date')) : null;
                $returnPickupTime = session('return_time') ? $this->safeFormatTime(session('return_time')) : now()->format('H:i:s');
                $returnService = ReturnService::create([
                    'vehicle_id' => $vehicle_id,
                    'pickup_location' => session('dropoff_location'),
                    'dropoff_location' => session('pickup_location'),
                    'pickup_date' => $returnPickupDate ?? now()->format('Y-m-d'),
                    'pickup_time' => $returnPickupTime,
                ]);
                $returnServiceId = $returnService->id;
            }

            // -------------------------
            // Create Booking
            // -------------------------
            $pickupDateYmd = $this->safeFormatDate($pickup_date);
            $isRoundTrip = session('round_trip') ? true : false;
            $returnDateYmd = $isRoundTrip ? $this->safeFormatDate($return_date) : null;

            if (!$pickupDateYmd) {
                return redirect()->back()->with('error', 'Invalid pickup date');
            }
            if ($isRoundTrip && !$returnDateYmd) {
                return redirect()->back()->with('error', 'Invalid return date');
            }

            $pickupTimeHis = $this->safeFormatTime($pickup_time);
            $returnTimeHis = $isRoundTrip ? $this->safeFormatTime($return_time) : null;

            if (!$pickupTimeHis) {
                return redirect()->back()->with('error', 'Invalid pickup time');
            }
            if ($isRoundTrip && !$returnTimeHis) {
                return redirect()->back()->with('error', 'Invalid return time');
            }

            $booking = Booking::create([
                'booker_id' => $booker ? $booker->id : null,
                'booking_id' => $customBookingId,
                'user_id' => $user ? $user->id : null,
                'vehicle_id' => $vehicle_id,
                'pickup_location' => $pickup_location,
                'dropoff_location' => $dropoff_location,
                'pickup_date' => $pickupDateYmd,
                'pickup_time' => $pickupTimeHis,
                'return_date' => $returnDateYmd,
                'return_time' => $returnTimeHis,
                'total_price' => $selected_price,
                'payment_status' => $paymentStatusLabel,
                'return_service_id' => $returnServiceId,
                'round_trip' => session('round_trip') ? 1 : 0,
                'note' => session('note') ?? null,
            ]);

            // Payment record
            $booking->payments()->create([
                'payment_method' => "card",
                'payment_status' => $paymentStatusLabel,
                'transaction_id' => $transactionId,
                'amount' => $selected_price,
            ]);

            // Passenger record
            $passenger = $booking->passengers()->create([
                'first_name' => $isBookingForOthers ? $guest['first_name'] ?? $first_name : $first_name,
                'last_name' => $isBookingForOthers ? $guest['last_name'] ?? $last_name : $last_name,
                'email' => $isBookingForOthers ? $guest['email'] ?? $email : $email,
                'phone_number' => $isBookingForOthers ? $guest['number'] ?? $number : $number,
                'is_booking_for_others' => $isBookingForOthers,
                'booker_id' => $booker ? $booker->id : null,
            ]);

            // Breakdown
            $breakdownData = session('breakdown_data');
            if ($breakdownData && is_array($breakdownData)) {
                $booking->breakdown()->create([
                    'booking_id' => $booking->id,
                    'base_fare' => $breakdownData['baseFare'] ?? null,
                    'per_km_rate' => $breakdownData['perKmRate'] ?? null,
                    'total_kms' => $breakdownData['distance_km'] ?? null,
                    'hourly_fare' => $breakdownData['hourlyFare'] ?? null,
                    'total_hours' => $hours,
                    'return_base_fare' => session('return_base_fare') ?? null,
                    'return_per_km_rate' => session('return_per_km_rate') ?? null,
                    'return_total_kms' => session('return_km') ?? null,
                ]);
            }

            // Flight details
            if (session()->has('pickup_flight_details') || session()->has('flight_number') || session('is_airport')) {
                $flight_details = [
                    'passenger_id' => $passenger->id,
                    'pickup_flight_details' => session('pickup_flight_details'),
                    'flight_number' => session('flight_number'),
                    'meet_option' => session('meet_option'),
                    'no_flight_info' => session('no_flight_info', false),
                    'inside_pickup_fee' => 0.00,
                ];
                FlightDetail::create($flight_details);
            }

            // Dispatch booking documents
            $bookingData = [
                'booking_id' => $customBookingId,
                'isBookingForOthers' => $isBookingForOthers,
                'booker_first_name' => $booker_first_name,
                'booker_last_name' => $booker_last_name,
                'booker_number' => $booker_number,
                'booker_email' => $booker_email,
                'passenger_name' => ($first_name . ' ' . $last_name),
                'email' => $email,
                'phone' => $number,
                'pickup_location' => $pickup_location,
                'dropoff_location' => $dropoff_location,
                'hours' => $hours,
                'pickup_date' => $pickupDateYmd,
                'pickup_time' => $pickupTimeHis,
                'return_date' => $returnDateYmd,
                'return_time' => $returnTimeHis,
                'is_round_trip' => $isRoundTrip,
                'vehicle_type' => $vehicle_name ?? 'Standard',
                'passengers' => 1,
                'total_amount' => $selected_price,
                'payment_status' => $paymentStatusLabel,
                'special_instructions' => session('note') ?? null,
                'flight_details' => $flight_details,
            ];

            // Run synchronously so admin/booker receive mail without a queue worker
            // (the job still implements job structure for tests / future queue use).
            // Isolated try/catch: PDF/mail failures MUST NOT block the thank-you
            // redirect or roll back the already-saved booking.
            try {
                CreateBookingDocs::dispatchSync($bookingData, $customBookingId);
            } catch (\Throwable $e) {
                \Log::error('Booking docs dispatch failed (booking still saved): ' . $e->getMessage(), [
                    'booking_id' => $customBookingId,
                    'file' => $e->getFile() . ':' . $e->getLine(),
                ]);
            }

            // Clear session
            session()->forget([
                'pickup_location',
                'dropoff_location',
                'pickup_date',
                'pickup_time',
                'vehicle_id',
                'vehicle_name',
                'final_price',
                'calculated_price',
                'guest',
                'return_service',
                'round_trip',
                'note',
                'breakdown_data',
                'return_base_fare',
                'return_per_km_rate',
                'return_km'
            ]);

            session([
                'booking_completed' => true,
                'booking_id' => $customBookingId,
            ]);

            return redirect()->route('thankyou');
        } catch (\Stripe\Exception\CardException $e) {
            \Log::error('completeBook Stripe CardException: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getError()->message);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('completeBook Stripe ApiErrorException: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Stripe API error: ' . $e->getMessage());
        } catch (\Throwable $e) {
            \Log::error('completeBook fatal: ' . $e->getMessage(), [
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 2000),
            ]);

            // If we already created the booking, do NOT bounce the user back to
            // the payment page (that would risk a double-charge). Send them to
            // thank-you with the booking_id we just stored.
            if (!empty($customBookingId) && \App\Models\Booking::where('booking_id', $customBookingId)->exists()) {
                session([
                    'booking_completed' => true,
                    'booking_id' => $customBookingId,
                ]);
                return redirect()->route('thankyou');
            }

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function ThankYou()
    {
        $bookingId = session('booking_id');

        if (empty($bookingId)) {
            return redirect()->route('booking');
        }

        $booking = Booking::with(['vehicle', 'passengers', 'booker'])
            ->where('booking_id', $bookingId)
            ->first();

        if (!$booking) {
            \Log::warning('ThankYou: booking not found for session booking_id', [
                'booking_id' => $bookingId,
            ]);
            return redirect()->route('booking');
        }

        $travelInfo = null;

        try {
            if (empty($booking->dropoff_location)) {
                $hours = $booking->total_hours ?? 0;
                $hourlyRate = $booking->vehicle->base_hourly_fare ?? 0;
                $travelInfo = [
                    'type' => 'hourly',
                    'hours' => $hours,
                    'fare' => $hourlyRate * $hours,
                ];
            } else {
                $distance = $this->getDistanceBetweenAddresses($booking->pickup_location, $booking->dropoff_location);

                if ($distance !== null && $booking->vehicle) {
                    $baseFare = $booking->vehicle->base_fare ?? 0;
                    $perKmRate = $booking->vehicle->per_km_rate ?? 0;
                    $travelInfo = [
                        'type' => 'point_to_point',
                        'distance' => $distance,
                        'fare' => $baseFare + ($distance * $perKmRate),
                    ];
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('ThankYou: travelInfo computation failed: ' . $e->getMessage());
            $travelInfo = null;
        }

        return view('booking.thankyou', [
            'booking' => $booking,
            'travelInfo' => $travelInfo,
            'seo' => [
                'title' => 'Thank You for Your Booking | Dallas Limo And Black Cars',
                'description' => 'Thank you for booking with Dallas Limo And Black Cars. Your luxury transportation has been confirmed. Check your email for booking details.',
                'keywords' => 'Dallas black car booking, luxury car service confirmation, airport transfer booking Dallas',
                'og_title' => 'Thank You for Your Booking | Dallas Limo And Black Cars',
                'og_description' => 'Thank you for booking with Dallas Limo And Black Cars. Your luxury transportation has been confirmed.',
                'og_image' => asset('new_assets/assets/black-car-service-dallas-logo.png')
            ]
        ]);
    }
    private function safeFormatDate($date): ?string
    {
        if (!$date) {
            return null;
        }
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
    private function safeFormatTime($time): ?string
    {
        if (!$time) {
            return null;
        }
        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
    private function getDistanceBetweenAddresses(string $origin, string $destination): ?float
    {
        $apiKey = config('services.google_maps.api_key');

        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination,
            'key' => $apiKey,
            'units' => 'metric',
        ]);

        if ($response->ok()) {
            $data = $response->json();

            if (
                isset($data['status'], $data['rows'][0]['elements'][0]['status']) &&
                $data['status'] === 'OK' &&
                $data['rows'][0]['elements'][0]['status'] === 'OK'
            ) {
                $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                $distanceMiles = $distanceMeters / 1609.34; // Convert meters to miles
                return round($distanceMiles, 2);
            }
        }

        return null; // Return null if something went wrong
    }
    // public function completeBook(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'payment_method_id' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }
    //     try {
    //         $pickup_location = session('pickup_location');
    //         $dropoff_location = session('dropoff_location');
    //         $pickup_date = session('pickup_date');
    //         $pickup_time = session('pickup_time');
    //         $first_name = session('first_name');
    //         $last_name = session('last_name');
    //         $email = session('email');
    //         $number = session('number');
    //         $selected_price = session('selected_price');
    //         $selected_distance = session('selected_distance');
    //         $vehicle_id = session('vehicle_id');

    //         \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    //         $paymentIntent = \Stripe\PaymentIntent::create([
    //             'amount' => session('selected_price') * 100, // Stripe requires amount in cents
    //             'currency' => 'usd',
    //             'payment_method' => $request->payment_method_id,
    //             'confirmation_method' => 'manual',
    //             'confirm' => true,
    //         ]);
    //         $transactionId = $paymentIntent->id;
    //         // Payment succeeded
    //         if ($paymentIntent->status === 'requires_action' && $paymentIntent->next_action->type === 'use_stripe_sdk') {
    //             return response()->json([
    //                 'requires_action' => true,
    //                 'payment_intent_client_secret' => $paymentIntent->client_secret
    //             ]);
    //         }



    //         $booking = Booking::create([
    //             'user_id' => Auth::id(), // or null for guest
    //             'vehicle_id' => $vehicle_id,
    //             'pickup_location' => $pickup_location,
    //             'dropoff_location' => $dropoff_location,
    //             'pickup_date' => $pickup_date,
    //             'pickup_time' => $pickup_time,
    //             'total_price' => $selected_price,
    //             'payment_status' => "Paid",
    //         ]);

    //         $booking->payments()->create([
    //             'payment_method' => "card",
    //             'payment_status' => "Paid",
    //             'transaction_id' => $transactionId,
    //             'amount' => $selected_price,
    //         ]);

    //         $booking->passengers()->create([
    //             'first_name' => $first_name,
    //             'last_name' => $last_name,
    //             'email' => $email,
    //             'phone_number' => $number,
    //             'booker_first_name' => session('bookingForSomeoneElse') ? session('booker_first_name') : session('first_name'),
    //             'booker_last_name' => session('bookingForSomeoneElse') ? session('booker_last_name') : session('last_name'),
    //             'booker_email' => session('bookingForSomeoneElse') ? session('booker_email') : session('email'),
    //             'booker_number' => session('bookingForSomeoneElse') ? session('booker_number') : session('number'),
    //             'is_booking_for_others' => session('bookingForSomeoneElse') ? true : false,

    //         ]);
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Booking created successfully',
    //             'booking_id' => $booking->id,
    //         ]);
    //     } catch (\Stripe\Exception\CardException $e) {
    //         return response()->json(['success' => false, 'message' => $e->getError()->message], 400);
    //     }
    // }
    public function saveReturnService(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'return_pickup_location' => 'required|string',
                'return_dropoff_location' => 'required|string',
                'return_pickup_date' => 'required|date_format:Y-m-d',
                'return_pickup_time' => 'required|date_format:H:i',
                'return_flight_number' => 'nullable|string',
                'return_flight_details' => 'nullable|string',
                'return_no_flight_info' => 'nullable|boolean',
                'return_vehicle_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Store return service data in session
            session([
                'return_service' => 1,
                'return_pickup_location' => $request->return_pickup_location,
                'return_dropoff_location' => $request->return_dropoff_location,
                'return_pickup_date' => $request->return_pickup_date,
                'return_pickup_time' => $request->return_pickup_time,
                'return_flight_number' => $request->return_flight_number,
                'return_flight_details' => $request->return_flight_details,
                'return_no_flight_info' => $request->has('return_no_flight_info') ? 1 : 0,
                'return_vehicle_id' => $request->return_vehicle_id,
                'return_price' => $request->input('return_price', 0),
                'return_base_fare' => $request->input('return_base_fare', 0),
                'return_per_km_rate' => $request->input('return_per_km_rate', 0),
                'return_km' => $request->input('return_km', 0),
            ]);

            // Get vehicle name for display
            $vehicleName = null;
            if ($request->return_vehicle_id) {
                $vehicle = Vehicle::find($request->return_vehicle_id);
                $vehicleName = $vehicle ? $vehicle->vehicle_name : null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Return service saved successfully',
                'data' => [
                    'return_pickup_location' => $request->return_pickup_location,
                    'return_dropoff_location' => $request->return_dropoff_location,
                    'return_pickup_date' => $request->return_pickup_date,
                    'return_pickup_time' => $request->return_pickup_time,
                    'return_flight_number' => $request->return_flight_number,
                    'return_flight_details' => $request->return_flight_details,
                    'return_no_flight_info' => $request->has('return_no_flight_info') ? 1 : 0,
                    'return_vehicle_id' => $request->return_vehicle_id,
                    'vehicle_name' => $vehicleName,
                    'return_price' => $request->input('return_price', 0),
                    'return_base_fare' => $request->input('return_base_fare', 0),
                    'return_per_km_rate' => $request->input('return_per_km_rate', 0),
                    'return_km' => $request->input('return_km', 0),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving return service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calculateDistanceRate(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'distance_rates' => 'array',
            'waiting_time_rates' => 'array',
            'gratuity' => 'nullable|numeric',
            'base_rate' => 'nullable|numeric',
        ]);

        $payload = [
            'distance_rates' => json_encode(self::normalizeRates($validated['distance_rates'] ?? [])),
            'waiting_time_rates' => json_encode(self::normalizeTimeRates($validated['waiting_time_rates'] ?? [])),
            'gratuity' => $validated['gratuity'] ?? 0,
            'base_rate' => $validated['base_rate'] ?? 0,
        ];

        $rate = RateVehicle::where('vehicle_id', $validated['vehicle_id'])->first();
        if ($rate) {
            $rate->fill($payload)->save();
        } else {
            $rate = RateVehicle::create(array_merge(['vehicle_id' => $validated['vehicle_id']], $payload));
        }

        return response()->json([
            'message' => 'Rate created',
            'id' => $rate->id,
        ], 201);
    }

    private static function normalizeRates(array $rates): array
    {
        return array_map(function ($item) {
            return [
                'distance' => (string)($item['distance'] ?? ''),
                'rate' => (float)($item['rate'] ?? 0),
                '_id' => (string)($item['_id'] ?? Str::lower(Str::random(24))),
            ];
        }, $rates);
    }

    private static function normalizeTimeRates(array $rates): array
    {
        return array_map(function ($item) {
            return [
                'time' => (string)($item['time'] ?? ''),
                'rate' => (float)($item['rate'] ?? 0),
                '_id' => (string)($item['_id'] ?? Str::lower(Str::random(24))),
            ];
        }, $rates);
    }

    private function calculateDistanceBasedPrice($vehicle, float $distanceMiles): array
    {
        $pickupLocation = session('return_pickup_location') ?: session('pickup_location');

        $groupVehicleRate = RateCityGroup::vehicleRateForPickup($vehicle, $pickupLocation);
        $rateModel = $groupVehicleRate ?: RateVehicle::where('vehicle_id', $vehicle->id)->first();

        return RateCityGroupVehicleRate::pointToPointBreakdown($rateModel, $vehicle, $distanceMiles);
    }
}
