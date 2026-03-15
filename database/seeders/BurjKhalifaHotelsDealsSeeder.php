<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\Domain;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BurjKhalifaHotelsDealsSeeder extends Seeder
{
    public function run(): void
    {
        $domain = Domain::where('slug', 'burjkhalifahotelsonline')->first();
        if (! $domain) {
            $this->command->warn('Domain burjkhalifahotelsonline not found. Skipping deals seeder.');
            return;
        }

        // Build a lookup of hotel slug => hotel model
        $hotels = $domain->hotels()->get()->keyBy(function ($hotel) {
            return $hotel->slug;
        });

        if ($hotels->isEmpty()) {
            $this->command->warn('No hotels found for burjkhalifahotelsonline domain. Skipping deals seeder.');
            return;
        }

        $today = Carbon::today();

        $deals = [
            [
                'title' => 'Early Bird Discount',
                'slug' => 'burj-khalifa-early-bird-discount',
                'description' => 'Book at least 30 days in advance and enjoy 20% off the best available rate at any Downtown Dubai hotel. Plan ahead, pay less — it\'s that simple. Valid across all our properties from budget-friendly stays to five-star luxury.',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(6),
                'hotel_slugs' => $hotels->keys()->all(), // all hotels
            ],
            [
                'title' => 'Summer Escape',
                'slug' => 'burj-khalifa-summer-escape',
                'description' => 'Beat the heat with our biggest discount of the year. Enjoy 30% off stays at Downtown Dubai\'s finest five-star hotels this summer. Infinity pools with Burj Khalifa views, world-class spas, and Michelin-starred dining — all at an unbeatable price.',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'start_date' => Carbon::create(2026, 6, 1),
                'end_date' => Carbon::create(2026, 8, 31),
                'hotel_slugs' => [
                    'armani-hotel-dubai',
                    'address-downtown',
                    'jw-marriott-marquis-dubai',
                ],
            ],
            [
                'title' => 'Long Stay Saver',
                'slug' => 'burj-khalifa-long-stay-saver',
                'description' => 'Stay 7 nights or more and save AED 250 on your total booking. Our suite-style hotels offer full kitchens, spacious living areas, and all the comforts of home — perfect for extended business trips, relocations, or long holidays in Dubai.',
                'discount_type' => 'fixed_amount',
                'discount_value' => 250,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(3),
                'hotel_slugs' => [
                    'difc-living-suites',
                    'vida-downtown-dubai',
                ],
            ],
            [
                'title' => 'Weekend Luxury',
                'slug' => 'burj-khalifa-weekend-luxury',
                'description' => 'Treat yourself to a luxury weekend escape. Enjoy 15% off Friday and Saturday stays at Downtown Dubai\'s most stylish boutique hotels. Fountain views, rooftop pools, and Instagram-worthy design — the perfect Dubai weekend awaits.',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(3),
                'hotel_slugs' => [
                    'vida-downtown-dubai',
                    'paramount-hotel-dubai',
                    'the-oberoi-dubai',
                ],
            ],
            [
                'title' => 'Fountain View Package',
                'slug' => 'burj-khalifa-fountain-view-package',
                'description' => 'Stay at a hotel with Dubai Fountain views and save AED 150 on your booking. Watch the world\'s largest choreographed fountain show from your room or the hotel\'s terrace — a truly magical Dubai experience included with every night.',
                'discount_type' => 'fixed_amount',
                'discount_value' => 150,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(6),
                'hotel_slugs' => [
                    'armani-hotel-dubai',
                    'address-downtown',
                    'vida-downtown-dubai',
                    'rove-downtown-dubai',
                ],
            ],
            [
                'title' => 'Business Elite',
                'slug' => 'burj-khalifa-business-elite',
                'description' => 'Corporate rates made even better. Enjoy 25% off stays at our DIFC hotels, right in the heart of Dubai\'s financial centre. Walk to Gate Village, Gate Avenue restaurants, and the region\'s top financial institutions. Includes complimentary high-speed WiFi and business centre access.',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(3),
                'hotel_slugs' => [
                    'four-seasons-hotel-difc',
                    'ritz-carlton-difc',
                    'difc-living-suites',
                ],
            ],
        ];

        $seededCount = 0;

        foreach ($deals as $dealData) {
            $hotelSlugs = $dealData['hotel_slugs'];
            unset($dealData['hotel_slugs']);

            $deal = Deal::updateOrCreate(
                ['slug' => $dealData['slug']],
                array_merge($dealData, ['is_active' => true])
            );

            // Attach to domain
            $deal->domains()->syncWithoutDetaching([$domain->id]);

            // Attach relevant hotels
            $hotelIds = $hotels->filter(fn ($h, $key) => in_array($key, $hotelSlugs))->pluck('id')->all();
            if (count($hotelIds) > 0) {
                $deal->hotels()->syncWithoutDetaching($hotelIds);
            }

            $seededCount++;
            $unit = $deal->discount_type === 'percentage' ? '%' : ' AED';
            $hotelCount = count($hotelIds);
            $this->command->line("  Deal: {$deal->title} ({$deal->discount_value}{$unit} off, {$hotelCount} hotels)");
        }

        $this->command->info("Seeded {$seededCount} deals for burjkhalifahotelsonline domain.");
    }
}
