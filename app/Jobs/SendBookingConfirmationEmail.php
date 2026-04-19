<?php

namespace App\Jobs;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public Booking $booking)
    {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $this->booking->loadMissing(['hotel', 'roomType', 'domain']);

        Mail::to($this->booking->guest_email)
            ->send(new BookingConfirmationMail($this->booking));

        Log::info('Booking confirmation email sent', [
            'reference' => $this->booking->reference_number,
            'email' => $this->booking->guest_email,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send booking confirmation email', [
            'reference' => $this->booking->reference_number,
            'error' => $exception->getMessage(),
        ]);
    }
}
