<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateBookingDocs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public array $bookingData;

    public string $customBookingId;

    

    public function __construct($bookingData, $customBookingId)
    {
        $this->bookingData = $bookingData;
        $this->customBookingId = $customBookingId;
    }

    

    public function handle()
    {
         
        $filePath = null;
        try {
            $pdfsDirectory = public_path('pdfs');

            if (!file_exists($pdfsDirectory)) {
                mkdir($pdfsDirectory, 0777, true);
            }

            $filePath = $pdfsDirectory . '/' . $this->customBookingId . '.pdf';

            $pdf = PDF::loadView('pdfs.booking', ['bookingData' => $this->bookingData]);
            $pdf->save($filePath);
        } catch (\Throwable $e) {
            \Log::error('CreateBookingDocs PDF generation failed: ' . $e->getMessage(), [
                'booking_id' => $this->customBookingId,
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            $filePath = null;  
        }

         
        $recipients = [];

        $customerEmail = $this->bookingData['email'] ?? null;
        if (!empty($customerEmail)) {
            $recipients[] = ['email' => $customerEmail, 'isAdmin' => false, 'isBooker' => false];
        } else {
            \Log::warning('CreateBookingDocs: customer email missing, skipping customer send', [
                'booking_id' => $this->customBookingId,
            ]);
        }

        $adminEmail = config('mail.admin_email');
        if (!empty($adminEmail)) {
            $recipients[] = ['email' => trim($adminEmail), 'isAdmin' => true, 'isBooker' => false];
        } else {
            \Log::warning('Admin email not found in configuration');
        }

        $isForOthers = !empty($this->bookingData['isBookingForOthers']);
        $bookerEmail = $this->bookingData['booker_email'] ?? null;
        if ($isForOthers && !empty($bookerEmail) && $bookerEmail !== $customerEmail) {
            $recipients[] = ['email' => $bookerEmail, 'isAdmin' => false, 'isBooker' => true];
        }

        \Log::info('CreateBookingDocs final recipient list', [
            'booking_id' => $this->customBookingId,
            'recipients' => $recipients,
        ]);

         
        foreach ($recipients as $index => $recipient) {
            try {
                $email = new \App\Mail\Booking(
                    $this->bookingData,
                    $recipient['isAdmin'],
                    $recipient['isBooker']
                );

                Mail::to($recipient['email'])->send($email);
                \Log::info('Booking mail sent', [
                    'booking_id' => $this->customBookingId,
                    'to' => $recipient['email'],
                    'index' => $index + 1,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Booking mail failed: ' . $e->getMessage(), [
                    'booking_id' => $this->customBookingId,
                    'to' => $recipient['email'],
                    'file' => $e->getFile() . ':' . $e->getLine(),
                ]);
            }
        }
    }
}
