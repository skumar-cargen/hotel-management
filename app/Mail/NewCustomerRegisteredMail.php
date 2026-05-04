<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCustomerRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public ?Domain $domain = null,
        public ?string $registrationMethod = 'email',
        public ?string $ipAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        $domainLabel = $this->domain?->name ? " ({$this->domain->name})" : '';

        // From email = SMTP authenticated user (guaranteed accepted by the SMTP
        // server — no "553 Sender address rejected" mismatch). The domain's
        // public-facing `email` is shown inside the email body/footer instead.
        // From name = domain name for branding (e.g. "Dubai Hotel Resorts").
        $fromEmail = $this->domain->smtp_username ?: $this->domain->email;
        $fromName = $this->domain->name;

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: "New customer registered{$domainLabel}: {$this->customer->email}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-customer-registered',
            with: [
                'customer' => $this->customer,
                'domain' => $this->domain,
                'registrationMethod' => $this->registrationMethod,
                'ipAddress' => $this->ipAddress,
                'domainName' => $this->domain?->name ?? config('app.name'),
                'domainAddress' => $this->domain?->address,
                'domainPhone' => $this->domain?->phone,
                'domainEmail' => $this->domain?->email,
            ],
        );
    }
}
