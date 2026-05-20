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

    /**
     * Create a new message instance.
     *
     * @param array|object $bookingData
     * @param bool $isAdmin
     * @return void
     */
    public function __construct($bookingData, $isAdmin = false, $sendToBooker = false)
    {
        $this->bookingData = (array) $bookingData; // Convert to array if it's an object
        $this->isAdmin = $isAdmin;
        $this->sendToBooker = $sendToBooker;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
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

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.booking',
            with: [
                'bookingData' => $this->bookingData,
                'isAdmin' => $this->isAdmin,
                'sendToBooker' => $this->sendToBooker
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
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
