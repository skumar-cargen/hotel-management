<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainAnalytics extends Model
{
    protected $fillable = [
        'domain_id', 'date', 'page_views', 'unique_visitors',
        'hotel_clicks', 'booking_starts', 'booking_completions',
        'revenue', 'top_hotels', 'top_locations', 'traffic_sources',
        'organic_traffic', 'search_impressions', 'search_clicks',
        'avg_position', 'bounce_rate', 'avg_session_duration',
        'top_keywords', 'top_landing_pages',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'revenue' => 'decimal:2',
            'top_hotels' => 'array',
            'top_locations' => 'array',
            'traffic_sources' => 'array',
            'avg_position' => 'decimal:2',
            'bounce_rate' => 'decimal:2',
            'top_keywords' => 'array',
            'top_landing_pages' => 'array',
        ];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
