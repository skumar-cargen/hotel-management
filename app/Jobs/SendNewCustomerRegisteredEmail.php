<?php

namespace App\Jobs;

use App\Mail\NewCustomerRegisteredMail;
use App\Models\Customer;
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

class SendNewCustomerRegisteredEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SendsViaDomainSmtp, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Customer $customer,
        public ?Domain $domain = null,
        public string $registrationMethod = 'email',
        public ?string $ipAddress = null,
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $mailerName = $this->buildDomainMailer($this->domain);

        $recipient = Setting::get('notification_email')
            ?: config('mail.admin_notifications_to')
            ?: $this->domain->email;

        Mail::mailer($mailerName)->to($recipient)->send(new NewCustomerRegisteredMail(
            $this->customer,
            $this->domain,
            $this->registrationMethod,
            $this->ipAddress,
        ));

        Log::info('New customer registration notification sent', [
            'customer_id' => $this->customer->id,
            'customer_email' => $this->customer->email,
            'domain' => $this->domain->slug,
            'from' => $this->domain->smtp_username ?: $this->domain->email,
            'method' => $this->registrationMethod,
            'recipient' => $recipient,
            'mailer' => $mailerName,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send new customer registration notification', [
            'customer_id' => $this->customer->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
