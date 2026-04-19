<?php

namespace App\Enums;

enum PricingRuleType: string
{
    case DomainMarkup = 'domain_markup';
    case Seasonal = 'seasonal';
    case DateRange = 'date_range';
    case Category = 'category';
    case DayOfWeek = 'day_of_week';

    public function label(): string
    {
        return match ($this) {
            self::DomainMarkup => 'Domain Markup',
            self::Seasonal => 'Seasonal',
            self::DateRange => 'Date Range',
            self::Category => 'Category',
            self::DayOfWeek => 'Day of Week',
        };
    }
}
