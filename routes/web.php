<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Booking wizard (steps 1–5 + Stripe)
Route::middleware('checkBookingCompletion')->group(function () {
    Route::get('/allVehicle/', [BookingController::class, 'showAll']);
    Route::post('/submit-passengerInfo/{id}', [BookingController::class, 'submitPassengerInfo']);
    Route::match(['get', 'post'], '/bookRide', [BookingController::class, 'bookRide']);
    Route::post('/completeBook', [BookingController::class, 'completeBook']);
    Route::get('/calculate-return-trip/', [BookingController::class, 'CalculateReturnTrip']);
    Route::post('/save-return-service', [BookingController::class, 'saveReturnService']);
    Route::get('/booking/start', [BookingController::class, 'showForm'])->name('booking.form');
    Route::match(['get', 'post'], '/booking/point-to-point', [BookingController::class, 'handlePointToPoint'])->name('booking.pointToPoint');
    Route::match(['get', 'post'], '/booking/hourly-hire', [BookingController::class, 'handleHourlyHire'])->name('booking.hourlyHire');
    Route::get('/passengerInfo', [BookingController::class, 'submitPassengerInfo'])->name('passenger.info');
    Route::get('/submit-passengerInfo', [BookingController::class, 'submitPassengerInfo'])->name('submit.passenger.info');
    Route::post('/save-booking-form-session', [BookingController::class, 'saveBookingFormSession'])->name('save.booking.form.session');
});

Route::get('/user-login/{id}/{price}', [BookingController::class, 'userLogin'])->name('user_login');

Route::post('/check-email-exists', [ProfileController::class, 'checkEmailExists'])->name('check.email.exists');

Route::get('/', [BookingController::class, 'showForm'])->name('booking');
Route::get('/thank-you', [BookingController::class, 'ThankYou'])->name('thankyou');

require __DIR__.'/auth.php';
