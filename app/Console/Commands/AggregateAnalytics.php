<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\DomainAnalytics;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AggregateAnalytics extends Command
{
    protected $signature = 'analytics:aggregate {--date= : Date to aggregate (defaults to today)}';

    protected $description = 'Flush cached analytics data to database and aggregate daily stats';

    public function handle(): int
    {
        $date = $this->option('date') ?? now()->toDateString();

        $this->info("Aggregating analytics for {$date}...");

        // Flush any cached page view data to DB
        $domains = Domain::where('is_active', true)->get();
        $flushed = 0;

        foreach ($domains as $domain) {
            $cacheKey = "analytics:{$domain->id}:{$date}";
            $stats = Cache::get($cacheKey);

            if ($stats) {
                DomainAnalytics::updateOrCreate(
                    ['domain_id' => $domain->id, 'date' => $date],
                    $stats,
                );
                Cache::forget($cacheKey);
                $flushed++;
            }
        }

        $this->info("Flushed cached data for {$flushed} domains.");

        // Update top locations from hotel clicks
        $analytics = DomainAnalytics::where('date', $date)->get();
        foreach ($analytics as $record) {
            $this->aggregateTopLocations($record);
        }

        $this->info('Analytics aggregation complete.');

        return self::SUCCESS;
    }

    protected function aggregateTopLocations(DomainAnalytics $record): void
    {
        $topHotels = $record->top_hotels ?? [];
        if (empty($topHotels)) {
            return;
        }

        $hotelIds = array_keys($topHotels);
        $hotels = \App\Models\Hotel::whereIn('id', $hotelIds)->with('location')->get();

        $locationCounts = [];
        foreach ($hotels as $hotel) {
            if ($hotel->location) {
                $locId = (string) $hotel->location->id;
                $locationCounts[$locId] = ($locationCounts[$locId] ?? 0) + ($topHotels[(string) $hotel->id] ?? 0);
            }
        }

        arsort($locationCounts);
        $record->update(['top_locations' => array_slice($locationCounts, 0, 10, true)]);
    }
}
