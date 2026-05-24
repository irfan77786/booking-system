<?php

namespace App\Support;

use App\Jobs\ProcessBookingCompletion;
use Illuminate\Support\Facades\Log;

class BookingQueue
{
    /**
     * Queue booking completion (Stripe + DB + email). Returns immediately.
     */
    public static function dispatchProcessBooking(array $payload): void
    {
        ProcessBookingCompletion::dispatch($payload)
            ->onConnection(config('queue.default', 'database'));

        if (self::shouldAutoRunWorker()) {
            self::runWorkerOnceInBackground();
        }

        Log::info('BookingQueue: queued ProcessBookingCompletion', [
            'booking_id' => $payload['custom_booking_id'] ?? null,
        ]);
    }

    public static function shouldAutoRunWorker(): bool
    {
        if (filter_var(env('QUEUE_AUTO_RUN_WORKER', false), FILTER_VALIDATE_BOOL)) {
            return true;
        }

        return app()->environment('local');
    }

    public static function runWorkerOnceInBackground(): void
    {
        $connection = config('queue.default', 'database');
        $php = PHP_BINARY;
        $artisan = base_path('artisan');
        $cmd = escapeshellarg($php) . ' ' . escapeshellarg($artisan)
            . ' queue:work ' . escapeshellarg($connection) . ' --once --timeout=180 --tries=3';

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                pclose(popen('cmd /c start /B ' . $cmd . ' 2>NUL', 'r'));
            } else {
                exec($cmd . ' > /dev/null 2>&1 &');
            }

            Log::info('BookingQueue: started background queue worker', ['connection' => $connection]);
        } catch (\Throwable $e) {
            Log::warning('BookingQueue: could not start background worker: ' . $e->getMessage());
        }
    }
}
