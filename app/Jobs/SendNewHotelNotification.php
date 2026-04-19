<?php

namespace App\Jobs;

use App\Mail\NewHotelMail;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewHotelNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 120;

    public function __construct(
        public Hotel $hotel,
        public array $domainIds,
    ) {
        $this->onQueue('newsletters');
    }

    public function handle(): void
    {
        $this->hotel->loadMissing(['location', 'amenities']);

        $domains = Domain::whereIn('id', $this->domainIds)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();

        $totalSent = 0;

        foreach ($domains as $domain) {
            NewsletterSubscriber::where('domain_id', $domain->id)
                ->where('is_active', true)
                ->chunkById(50, function ($subscribers) use ($domain, &$totalSent) {
                    foreach ($subscribers as $subscriber) {
                        Mail::to($subscriber->email)
                            ->send(new NewHotelMail($this->hotel, $domain));
                        $totalSent++;
                    }
                });
        }

        Log::info('New hotel newsletter sent', [
            'hotel_id' => $this->hotel->id,
            'hotel_name' => $this->hotel->name,
            'domains_count' => $domains->count(),
            'total_sent' => $totalSent,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send new hotel newsletter', [
            'hotel_id' => $this->hotel->id,
            'hotel_name' => $this->hotel->name,
            'error' => $exception->getMessage(),
        ]);
    }
}
