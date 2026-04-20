<?php

namespace App\Mail;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $domain = $this->booking->domain;
        $hasDomainSmtp = $domain?->smtp_host && $domain?->smtp_username && $domain?->smtp_password;

        // Use domain email only when domain has its own SMTP, otherwise fallback to .env
        $fromEmail = $hasDomainSmtp
            ? ($domain->smtp_username)
            : config('mail.from.address');
        $fromName = $domain?->name ?? config('mail.from.name');

        // When using .env SMTP but domain has an email, add it as reply-to
        $replyTo = (!$hasDomainSmtp && $domain?->email)
            ? [new Address($domain->email, $domain->name ?? '')]
            : [];

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            replyTo: $replyTo,
            subject: 'Booking Confirmation - ' . $this->booking->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking->load(['hotel', 'roomType', 'domain']),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * Use domain-specific SMTP transport when credentials are configured.
     */
    public function build(): static
    {
        $domain = $this->booking->domain;

        if ($domain?->smtp_host && $domain?->smtp_username && $domain?->smtp_password) {
            $scheme = match ($domain->smtp_encryption) {
                'ssl', 'smtps' => 'smtps',
                default => 'smtp',
            };

            $dsn = new Dsn(
                $scheme,
                $domain->smtp_host,
                $domain->smtp_username,
                $domain->smtp_password,
                $domain->smtp_port ?: 587,
            );

            $transport = (new EsmtpTransportFactory)->create($dsn);
            $this->withSymfonyMessage(function ($message) use ($transport) {
                $message->getHeaders()->addTextHeader('X-Transport', 'domain-smtp');
            });

            config([
                'mail.mailers.domain-smtp' => [
                    'transport' => 'smtp',
                    'host' => $domain->smtp_host,
                    'port' => $domain->smtp_port ?: 587,
                    'username' => $domain->smtp_username,
                    'password' => $domain->smtp_password,
                    'scheme' => $scheme === 'smtps' ? 'smtps' : null,
                ],
            ]);

            $this->mailer('domain-smtp');
        }

        return $this;
    }
}
