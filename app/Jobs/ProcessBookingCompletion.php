<?php

namespace App\Jobs;

use App\Models\Booker;
use App\Models\Booking;
use App\Models\FlightDetail;
use App\Models\ReturnService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBookingCompletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 180;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        $p = $this->payload;
        $customBookingId = $p['custom_booking_id'];

        $payment = $this->authorizeStripePayment($p);

        $p['booking']['payment_status'] = $payment['payment_status_label'];
        $p['payment'] = [
            'payment_method' => 'card',
            'payment_status' => $payment['payment_status_label'],
            'transaction_id' => $payment['transaction_id'],
            'amount' => $p['stripe']['amount'],
        ];
        $p['booking_data_for_email']['payment_status'] = $payment['payment_status_label'];

        $booker = null;
        if (!empty($p['booker'])) {
            $booker = Booker::create($p['booker']);
        }

        $returnServiceId = null;
        if (!empty($p['return_service'])) {
            $returnService = ReturnService::create($p['return_service']);
            $returnServiceId = $returnService->id;
        }

        $bookingFields = $p['booking'];
        $bookingFields['booker_id'] = $booker?->id;
        $bookingFields['return_service_id'] = $returnServiceId;

        $booking = Booking::create($bookingFields);

        $booking->payments()->create($p['payment']);

        $passengerData = $p['passenger'];
        $passengerData['booker_id'] = $booker?->id;
        $passenger = $booking->passengers()->create($passengerData);

        if (!empty($p['breakdown'])) {
            $breakdown = $p['breakdown'];
            $breakdown['booking_id'] = $booking->id;
            $booking->breakdown()->create($breakdown);
        }

        if (!empty($p['flight_details'])) {
            $flight = $p['flight_details'];
            $flight['passenger_id'] = $passenger->id;
            FlightDetail::create($flight);
        }

        $bookingData = $p['booking_data_for_email'];
        $bookingData['booking_id'] = $customBookingId;

        try {
            (new CreateBookingDocs($bookingData, $customBookingId))->handle();
        } catch (\Throwable $e) {
            Log::error('ProcessBookingCompletion: docs/email failed: ' . $e->getMessage(), [
                'booking_id' => $customBookingId,
            ]);
        }

        Log::info('ProcessBookingCompletion: finished', ['booking_id' => $customBookingId]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ProcessBookingCompletion failed permanently', [
            'booking_id' => $this->payload['custom_booking_id'] ?? null,
            'message' => $e->getMessage(),
        ]);
    }

    /**
     * @return array{transaction_id: string, payment_status_label: string}
     */
    private function authorizeStripePayment(array $p): array
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $user = !empty($p['user_id']) ? User::find($p['user_id']) : null;
        $guest = $p['guest'] ?? [];
        $paymentMethodId = $p['stripe']['payment_method_id'];
        $amount = (float) $p['stripe']['amount'];
        $amountInCents = (int) round($amount * 100);

        $stripeCustomerId = $p['stripe_customer_id'] ?? null;
        if (!$stripeCustomerId) {
            $stripeCustomerId = $this->resolveStripeCustomerId($user, $guest);
        }

        if (!$stripeCustomerId) {
            throw new \RuntimeException('Stripe customer could not be resolved for booking ' . $p['custom_booking_id']);
        }

        try {
            \Stripe\PaymentMethod::retrieve($paymentMethodId)->attach([
                'customer' => $stripeCustomerId,
            ]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            if (!str_contains(strtolower($e->getMessage()), 'already been attached')) {
                throw $e;
            }
        }

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amountInCents,
            'currency' => 'usd',
            'customer' => $stripeCustomerId,
            'payment_method' => $paymentMethodId,
            'capture_method' => 'manual',
            'off_session' => true,
            'confirm' => true,
        ]);

        if ($paymentIntent->status === 'requires_action' && ($paymentIntent->next_action->type ?? null) === 'use_stripe_sdk') {
            throw new \RuntimeException('Payment requires additional authentication.');
        }

        if (!in_array($paymentIntent->status, ['requires_capture', 'succeeded'], true)) {
            throw new \RuntimeException('Payment could not be authorized. Status: ' . $paymentIntent->status);
        }

        return [
            'transaction_id' => $paymentIntent->id,
            'payment_status_label' => $paymentIntent->status === 'succeeded' ? 'Paid' : 'Authorized',
        ];
    }

    private function resolveStripeCustomerId(?User $user, array $guest): ?string
    {
        if ($user) {
            if ($user->stripe_customer_id) {
                return $user->stripe_customer_id;
            }

            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name' => trim($user->first_name . ' ' . $user->last_name),
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);

            return $customer->id;
        }

        if (empty($guest['email'])) {
            return null;
        }

        $customer = \Stripe\Customer::create([
            'email' => $guest['email'],
            'name' => trim(($guest['first_name'] ?? '') . ' ' . ($guest['last_name'] ?? '')),
        ]);

        return $customer->id;
    }
}
