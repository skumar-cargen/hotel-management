<?php

namespace Tests\Unit\Services;

use App\Models\Domain;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\PricingRule;
use App\Models\RoomType;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = app(PricingService::class);
    }

    public function test_get_display_price_returns_base_price_when_no_rules(): void
    {
        $location = Location::factory()->create();
        $hotel = Hotel::factory()->create([
            'location_id' => $location->id,
            'star_rating' => 4,
        ]);
        $roomType = RoomType::factory()->create([
            'hotel_id' => $hotel->id,
            'base_price' => 500.00,
        ]);

        $displayPrice = $this->pricingService->getDisplayPrice($roomType);

        $this->assertEquals(500.00, $displayPrice);
    }

    public function test_calculate_applies_vat(): void
    {
        $location = Location::factory()->create();
        $hotel = Hotel::factory()->create([
            'location_id' => $location->id,
            'star_rating' => 3,
        ]);
        $roomType = RoomType::factory()->create([
            'hotel_id' => $hotel->id,
            'base_price' => 1000.00,
        ]);

        $checkIn = Carbon::tomorrow();
        $checkOut = Carbon::tomorrow()->addDays(2);

        $breakdown = $this->pricingService->calculate($roomType, $checkIn, $checkOut);

        // 2 nights at 1000 = 2000 subtotal
        $this->assertEquals(2000.00, $breakdown->subtotal);

        // VAT at 5% of 2000 = 100
        $this->assertEquals(5.0, $breakdown->taxPercentage);
        $this->assertEquals(100.00, $breakdown->taxAmount);

        // Tourism fee for 3-star: 10 per room per night = 10 * 1 * 2 = 20
        $this->assertEquals(20.00, $breakdown->tourismFee);

        // Total: 2000 + 100 + 20 = 2120
        $this->assertEquals(2120.00, $breakdown->totalAmount);
    }

    public function test_get_display_price_applies_domain_markup(): void
    {
        $domain = Domain::factory()->create();
        $location = Location::factory()->create();
        $hotel = Hotel::factory()->create([
            'location_id' => $location->id,
            'star_rating' => 4,
        ]);
        $roomType = RoomType::factory()->create([
            'hotel_id' => $hotel->id,
            'base_price' => 1000.00,
        ]);

        // Create a domain markup pricing rule: +10% markup
        PricingRule::create([
            'name' => 'Test Domain Markup',
            'type' => 'domain_markup',
            'domain_id' => $domain->id,
            'adjustment_type' => 'percentage',
            'adjustment_value' => 10.00,
            'priority' => 100,
            'is_active' => true,
        ]);

        $displayPrice = $this->pricingService->getDisplayPrice($roomType, $domain);

        // 1000 + 10% = 1100
        $this->assertEquals(1100.00, $displayPrice);
    }

    public function test_get_display_price_applies_fixed_amount_rule(): void
    {
        $domain = Domain::factory()->create();
        $location = Location::factory()->create();
        $hotel = Hotel::factory()->create([
            'location_id' => $location->id,
            'star_rating' => 5,
        ]);
        $roomType = RoomType::factory()->create([
            'hotel_id' => $hotel->id,
            'base_price' => 500.00,
        ]);

        // Create a fixed amount pricing rule: +50 AED
        PricingRule::create([
            'name' => 'Test Fixed Markup',
            'type' => 'domain_markup',
            'domain_id' => $domain->id,
            'adjustment_type' => 'fixed_amount',
            'adjustment_value' => 50.00,
            'priority' => 100,
            'is_active' => true,
        ]);

        $displayPrice = $this->pricingService->getDisplayPrice($roomType, $domain);

        // 500 + 50 = 550
        $this->assertEquals(550.00, $displayPrice);
    }

    public function test_calculate_returns_correct_num_nights(): void
    {
        $location = Location::factory()->create();
        $hotel = Hotel::factory()->create([
            'location_id' => $location->id,
            'star_rating' => 4,
        ]);
        $roomType = RoomType::factory()->create([
            'hotel_id' => $hotel->id,
            'base_price' => 300.00,
        ]);

        $checkIn = Carbon::tomorrow();
        $checkOut = Carbon::tomorrow()->addDays(5);

        $breakdown = $this->pricingService->calculate($roomType, $checkIn, $checkOut);

        $this->assertEquals(5, $breakdown->numNights);
        $this->assertEquals(1, $breakdown->numRooms);
        $this->assertEquals(300.00, $breakdown->basePrice);
    }
}
