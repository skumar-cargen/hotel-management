<?php

namespace App\Traits;

use App\Models\Domain;

trait SendsViaDomainSmtp
{
    /**
     * Register a runtime mailer using the domain's own SMTP credentials and
     * return its name. Throws if domain SMTP is not fully configured —
     * prevents emails from silently going through the .env fallback.
     */
    protected function buildDomainMailer(?Domain $domain): string
    {
        if (! $domain) {
            throw new \RuntimeException('Cannot build domain mailer: no domain provided.');
        }

        if (! $domain->smtp_host || ! $domain->smtp_username || ! $domain->smtp_password || ! $domain->email) {
            throw new \RuntimeException(
                "Domain '{$domain->slug}' has no SMTP configuration. ".
                'Please set SMTP host/port/username/password and the domain email in admin → Domains → Edit page.'
            );
        }

        $name = "domain_{$domain->id}";

        $port = (int) $domain->smtp_port;

        // Encryption: prefer the explicit value; otherwise auto-pick from port.
        // Port 465 → implicit SSL. Port 587/25 → STARTTLS (Symfony key 'tls').
        $encryption = $domain->smtp_encryption ?: ($port === 465 ? 'ssl' : 'tls');

        // EHLO/HELO hostname. Many providers (Hostinger, Office365, Gmail) drop
        // the connection if the client sends a bare/invalid hostname. Use the
        // domain part of the SMTP username (e.g. "dubaihotelresorts.com" from
        // "info@dubaihotelresorts.com"), falling back to MAIL_EHLO_DOMAIN env.
        $localDomain = env('MAIL_EHLO_DOMAIN');
        if (! $localDomain && str_contains($domain->smtp_username, '@')) {
            $localDomain = substr($domain->smtp_username, strpos($domain->smtp_username, '@') + 1);
        }
        if (! $localDomain) {
            $localDomain = $domain->smtp_host;
        }

        // DNS warm-up — PHP on Windows sometimes fails stream_socket_client's
        // internal getaddrinfo on the first call even when DNS is healthy.
        // Calling gethostbyname() first populates the Windows DNS cache so
        // Symfony's subsequent socket connect succeeds.
        @gethostbyname($domain->smtp_host);

        config()->set("mail.mailers.{$name}", [
            'transport' => 'smtp',
            'host' => $domain->smtp_host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $domain->smtp_username,
            'password' => $domain->smtp_password,
            'timeout' => 30,
            'local_domain' => $localDomain,
            'verify_peer' => env('MAIL_VERIFY_PEER', true),
        ]);

        return $name;
    }
}
