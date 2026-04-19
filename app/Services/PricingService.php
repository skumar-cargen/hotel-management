<?php

namespace App\Services;

use App\DTOs\PriceBreakdown;
use App\Enums\AdjustmentType;
use App\Enums\PricingRuleType;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\PricingRule;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PricingService
{
    /**
     * UAE VAT percentage.
     */
    protected float $vatPercentage;

    /**
     * Tourism dirham fee per room per night.
     */
    protected array $tourismFeeByStars;

    /**
     * Default tourism fee for unknown star ratings.
     */
    protected float $defaultTourismFee;

    public function __construct()
    {
        $this->vatPercentage = (float) config('pricing.vat_percentage', 5.0);
        $this->tourismFeeByStars = config('pricing.tourism_fee_by_stars', [
            1 => 7,
            2 => 10,
            3 => 10,
            4 => 15,
            5 => 20,
        ]);
        $this->defaultTourismFee = (float) config('pricing.default_tourism_fee', 10);
    }

    /**
     * Calculate the full price breakdown for a booking.
     */
    public function calculate(
        RoomType $roomType,
        Carbon $checkIn,
        Carbon $checkOut,
        int $numRooms = 1,
        ?Domain $domain = null,
    ): PriceBreakdown {
        $hotel = $roomType->hotel;
        $basePrice = (float) $roomType->base_price;
        $numNights = $checkIn->diffInDays($checkOut);

        if ($numNights < 1) {
            $numNights = 1;
        }

        // Build nightly breakdown with pricing rules applied per night
        $nightlyBreakdown = [];
        $adjustments = [];
        $period = CarbonPeriod::create($checkIn, '1 day', $checkOut->copy()->subDay());

        foreach ($period as $date) {
            $nightPrice = $this->getPriceForDate($roomType, $hotel, $date, $basePrice, $domain, $adjustments);
            $nightlyBreakdown[] = [
                'date' => $date->format('Y-m-d'),
                'price' => round($nightPrice, 2),
            ];
        }

        // Deduplicate adjustments by name
        $uniqueAdjustments = [];
        foreach ($adjustments as $adj) {
            $key = $adj['name'] ?? $adj['rule_id'] ?? uniqid();
            if (! isset($uniqueAdjustments[$key])) {
                $uniqueAdjustments[$key] = $adj;
            }
        }
        $adjustments = array_values($uniqueAdjustments);

        // Calculate totals
        $totalNightlySum = array_sum(array_column($nightlyBreakdown, 'price'));
        $avgPerNight = $numNights > 0 ? $totalNightlySum / $numNights : $basePrice;
        $subtotal = round($totalNightlySum * $numRooms, 2);

        // Tax (UAE VAT)
        $taxAmount = round($subtotal * ($this->vatPercentage / 100), 2);

        // Tourism fee per room per night based on hotel star rating
        $starRating = $hotel->star_rating ?? 3;
        $feePerRoomNight = $this->tourismFeeByStars[$starRating] ?? $this->defaultTourismFee;
        $tourismFee = round($feePerRoomNight * $numRooms * $numNights, 2);

        $totalAmount = round($subtotal + $taxAmount + $tourismFee, 2);

        return PriceBreakdown::make([
            'basePrice' => $basePrice,
            'adjustments' => $adjustments,
            'finalPerNight' => round($avgPerNight, 2),
            'nightlyBreakdown' => $nightlyBreakdown,
            'numNights' => $numNights,
            'numRooms' => $numRooms,
            'subtotal' => $subtotal,
            'taxPercentage' => $this->vatPercentage,
            'taxAmount' => $taxAmount,
            'tourismFee' => $tourismFee,
            'serviceCharge' => 0,
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Get the effective price for a specific date, applying all matching rules.
     */
    protected function getPriceForDate(
        RoomType $roomType,
        Hotel $hotel,
        Carbon $date,
        float $basePrice,
        ?Domain $domain,
        array &$adjustments,
    ): float {
        $price = $basePrice;

        // 1. Check room_availability for date-specific override
        $availability = RoomAvailability::where('room_type_id', $roomType->id)
            ->where('date', $date->format('Y-m-d'))
            ->first();

        if ($availability && $availability->price_override !== null) {
            $price = (float) $availability->price_override;
            $adjustments[] = [
                'name' => 'Date override ('.$date->format('M d').')',
                'type' => 'override',
                'value' => $price,
            ];

            return $price;
        }

        // 2. Load applicable pricing rules ordered by priority
        $rules = $this->getApplicableRules($roomType, $hotel, $domain);

        foreach ($rules as $rule) {
            if (! $this->ruleAppliesOnDate($rule, $date)) {
                continue;
            }

            $adjustmentAmount = $this->applyRule($rule, $price);
            if ($adjustmentAmount != 0) {
                $price += $adjustmentAmount;
                $adjustments[] = [
                    'name' => $rule->name,
                    'rule_id' => $rule->id,
                    'type' => $rule->adjustment_type,
                    'value' => $rule->adjustment_value,
                    'amount' => round($adjustmentAmount, 2),
                ];
            }
        }

        return max(0, $price);
    }

    /**
     * Check if a pricing rule applies on a specific date.
     */
    protected function ruleAppliesOnDate(PricingRule $rule, Carbon $date): bool
    {
        // Check date range
        if ($rule->start_date && $date->lt(Carbon::parse($rule->start_date))) {
            return false;
        }
        if ($rule->end_date && $date->gt(Carbon::parse($rule->end_date))) {
            return false;
        }

        // Check day of week
        if ($rule->type === PricingRuleType::DayOfWeek && $rule->days_of_week) {
            $days = is_array($rule->days_of_week) ? $rule->days_of_week : json_decode($rule->days_of_week, true);
            if ($days && ! in_array($date->dayOfWeekIso, $days) && ! in_array(strtolower($date->format('l')), array_map('strtolower', $days))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply a single pricing rule and return the adjustment amount.
     */
    protected function applyRule(PricingRule $rule, float $currentPrice): float
    {
        return match ($rule->adjustment_type) {
            AdjustmentType::Percentage => $currentPrice * ($rule->adjustment_value / 100),
            AdjustmentType::FixedAmount => (float) $rule->adjustment_value,
            default => 0,
        };
    }

    /**
     * Quick price check for a room type (without full breakdown).
     *
     * Applies all matching pricing rules for today's date,
     * including domain_markup, seasonal, date_range, category, and day_of_week.
     */
    public function getDisplayPrice(RoomType $roomType, ?Domain $domain = null): float
    {
        $hotel = $roomType->relationLoaded('hotel') ? $roomType->hotel : $roomType->hotel()->first();
        $basePrice = (float) $roomType->base_price;
        $today = Carbon::today();

        // Check for availability override first
        $availability = RoomAvailability::where('room_type_id', $roomType->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        if ($availability && $availability->price_override !== null) {
            return round((float) $availability->price_override, 2);
        }

        // Load all applicable pricing rules
        $rules = $this->getApplicableRules($roomType, $hotel, $domain);

        $price = $basePrice;

        foreach ($rules as $rule) {
            if (! $this->ruleAppliesOnDate($rule, $today)) {
                continue;
            }

            $adjustmentAmount = $this->applyRule($rule, $price);
            if ($adjustmentAmount != 0) {
                $price += $adjustmentAmount;
            }
        }

        return round(max(0, $price), 2);
    }

    /**
     * Get all applicable pricing rules for a room type, ordered by priority.
     */
    private function getApplicableRules(RoomType $roomType, Hotel $hotel, ?Domain $domain): \Illuminate\Database\Eloquent\Collection
    {
        return PricingRule::where('is_active', true)
            ->where(function ($q) use ($roomType) {
                $q->whereNull('room_type_id')->orWhere('room_type_id', $roomType->id);
            })
            ->where(function ($q) use ($hotel) {
                $q->whereNull('hotel_id')->orWhere('hotel_id', $hotel->id);
            })
            ->where(function ($q) use ($hotel) {
                $q->whereNull('location_id')->orWhere('location_id', $hotel->location_id);
            })
            ->where(function ($q) use ($domain) {
                $q->whereNull('domain_id');
                if ($domain) {
                    $q->orWhere('domain_id', $domain->id);
                }
            })
            ->orderBy('priority', 'desc')
            ->get();
    }
}
