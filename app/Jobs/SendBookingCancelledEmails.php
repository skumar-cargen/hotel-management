<?php

namespace App\Jobs;

use App\Mail\BookingCancelledAdminMail;
use App\Mail\BookingCancelledMail;
use App\Models\Booking;
use App\Models\Domain;
use App\Models\Setting;
use App\Traits\SendsViaDomainSmtp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingCancelledEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SendsViaDomainSmtp, SerializesModels;

    public int $tries = 5;

    /**
     * Progressive backoff (seconds) — gives transient DNS / network issues
     * (common on Windows local SMTP) time to recover before the job is
     * marked as permanently failed.
     */
    public function backoff(): array
    {
        return [10, 30, 60, 120, 300];
    }

    public function __construct(
        public Booking $booking,
        public Domain $domain,
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $mailerName = $this->buildDomainMailer($this->domain);

        // 1. Customer notification — sent to the guest's email
        if ($this->booking->guest_email) {
            Mail::mailer($mailerName)
                ->to($this->booking->guest_email)
                ->send(new BookingCancelledMail($this->booking, $this->domain));
        }

        // 2. Admin notification — sent to configured notification recipient
        $adminRecipient = Setting::get('notification_email')
            ?: config('mail.admin_notifications_to')
            ?: $this->domain->email;

        Mail::mailer($mailerName)
            ->to($adminRecipient)
            ->send(new BookingCancelledAdminMail($this->booking, $this->domain));

        Log::info('Booking cancellation emails sent', [
            'booking' => $this->booking->reference_number,
            'domain' => $this->domain->slug,
            'customer' => $this->booking->guest_email,
            'admin' => $adminRecipient,
            'mailer' => $mailerName,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send booking cancellation emails', [
            'booking' => $this->booking->reference_number,
            'domain' => $this->domain?->slug,
            'error' => $exception->getMessage(),
        ]);
    }
}
