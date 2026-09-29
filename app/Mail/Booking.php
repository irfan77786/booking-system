<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Booking extends Mailable
{
    use Queueable, SerializesModels;

    public $bookingData;
    public $isAdmin;
    public $sendToBooker;

    

    public function __construct($bookingData, $isAdmin = false, $sendToBooker = false)
    {
        $this->bookingData = (array) $bookingData;  
        $this->isAdmin = $isAdmin;
        $this->sendToBooker = $sendToBooker;
    }

    

    public function envelope()
    {
        $pickupDate = $this->bookingData['pickup_date'] ?? null;
        $pickupTime = $this->bookingData['pickup_time'] ?? null;

        $pickupDateTime = 'N/A';
        if (!empty($pickupDate)) {
            try {
                $pickupDateTime = \Carbon\Carbon::parse(trim($pickupDate.' '.($pickupTime ?? '')))
                    ->format('F j, Y \a\t g:i A');
            } catch (\Throwable $e) {
                $pickupDateTime = (string) $pickupDate;
            }
        }

        $name = $this->bookingData['customer_name']
            ?? $this->bookingData['passenger_name']
            ?? 'Customer';

        return new Envelope(
            subject: 'Conf#'. ($this->bookingData['booking_id'] ?? '') . ' For ' . $name . ' [' . $pickupDateTime . ']',
        );
    }

    

    public function content()
    {
        $logoPath = public_path('assets/img/site/black-car-service-dallas-logo.webp');
        if (! is_readable($logoPath)) {
            $logoPath = public_path('assets/img/site/black-car-service-dallas-logo.png');
        }

        return new Content(
            view: 'emails.booking',
            with: [
                'bookingData' => $this->bookingData,
                'isAdmin' => $this->isAdmin,
                'sendToBooker' => $this->sendToBooker,
                'logoPath' => is_readable($logoPath) ? $logoPath : null,
                'logoUrl' => config('services.brand_logo_url'),
            ]
        );
    }

    

    public function attachments()
    {
        $bookingId = $this->bookingData['booking_id'] ?? null;
        if (empty($bookingId)) {
            return [];
        }

        $path = public_path('pdfs/'.$bookingId.'.pdf');
        if (!is_readable($path)) {
            return [];
        }

        return [
            Attachment::fromPath($path)
                ->as($bookingId.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
