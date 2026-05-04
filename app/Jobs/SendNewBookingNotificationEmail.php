<?php

namespace App\Jobs;

use App\Mail\NewBookingNotificationMail;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewBookingNotificationEmail implements ShouldQueue
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
        $recipient = Setting::get('notification_email')
            ?: config('mail.admin_notifications_to')
            ?: 'info@southtravels.com';

        $this->booking->loadMissing(['hotel', 'roomType', 'domain']);

        Mail::to($recipient)->send(new NewBookingNotificationMail($this->booking));

        Log::info('New booking notification sent to admin', [
            'reference' => $this->booking->reference_number,
            'recipient' => $recipient,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send new booking admin notification', [
            'reference' => $this->booking->reference_number,
            'error' => $exception->getMessage(),
        ]);
    }
}
