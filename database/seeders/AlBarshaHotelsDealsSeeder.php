<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\Domain;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AlBarshaHotelsDealsSeeder extends Seeder
{
    public function run(): void
    {
        $domain = Domain::where('slug', 'al-barsha-hotels')->first();
        if (! $domain) {
            $this->command->warn('Domain al-barsha-hotels not found. Skipping deals seeder.');
            return;
        }

        // Build a lookup of hotel slug => hotel model
        $hotels = $domain->hotels()->get()->keyBy(function ($hotel) {
            return $hotel->slug;
        });

        if ($hotels->isEmpty()) {
            $this->command->warn('No hotels found for al-barsha-hotels domain. Skipping deals seeder.');
            return;
        }

        $today = Carbon::today();

        $deals = [
            [
                'title' => 'Early Bird Discount',
                'slug' => 'al-barsha-early-bird-discount',
                'description' => 'Book at least 30 days in advance and enjoy 20% off the best available rate at any Al Barsha hotel. Plan ahead, pay less — it\'s that simple. Valid across all our properties from budget-friendly stays to five-star luxury.',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(6),
                'hotel_slugs' => $hotels->keys()->all(), // all hotels
            ],
            [
                'title' => 'Summer Escape',
                'slug' => 'al-barsha-summer-escape',
                'description' => 'Beat the heat with our biggest discount of the year. Enjoy 30% off stays at Al Barsha\'s finest five-star hotels this summer. Rooftop pools, world-class spas, and premium dining — all at an unbeatable price. Cool off indoors at Ski Dubai, just steps from your hotel.',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'start_date' => Carbon::create(2026, 6, 1),
                'end_date' => Carbon::create(2026, 8, 31),
                'hotel_slugs' => [
                    'kempinski-hotel-mall-of-the-emirates',
                    'sheraton-mall-of-the-emirates-hotel',
                    'pullman-dubai-city-center-deira-residence',
                ],
            ],
            [
                'title' => 'Long Stay Saver',
                'slug' => 'al-barsha-long-stay-saver',
                'description' => 'Stay 7 nights or more and save AED 200 on your total booking. Our apartment-style hotels offer full kitchens, spacious living areas, and all the comforts of home — perfect for extended business trips, relocations, or long holidays in Dubai.',
                'discount_type' => 'fixed_amount',
                'discount_value' => 200,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(3),
                'hotel_slugs' => [
                    'ghaya-grand-hotel',
                    'movenpick-hotel-apartments-al-barsha',
                ],
            ],
            [
                'title' => 'Weekend Getaway',
                'slug' => 'al-barsha-weekend-getaway',
                'description' => 'Treat yourself to a boutique weekend escape. Enjoy 15% off Friday and Saturday stays at Al Barsha\'s most stylish hotels. Art-inspired interiors, rooftop pools, and Instagram-worthy design — the perfect Dubai weekend awaits.',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(3),
                'hotel_slugs' => [
                    'studio-one-hotel',
                    'alserkal-arts-hotel',
                    'the-warehouse-hotel-al-quoz',
                ],
            ],
            [
                'title' => 'Mall Explorer Package',
                'slug' => 'al-barsha-mall-explorer-package',
                'description' => 'Stay steps from Mall of the Emirates and save AED 100 on your booking. Shop at 630+ stores, ski at Ski Dubai, catch a movie at VOX IMAX, and walk back to your hotel. No taxis needed — the mall is literally on your doorstep.',
                'discount_type' => 'fixed_amount',
                'discount_value' => 100,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(6),
                'hotel_slugs' => [
                    'kempinski-hotel-mall-of-the-emirates',
                    'sheraton-mall-of-the-emirates-hotel',
                    'novotel-al-barsha',
                    'holiday-inn-al-barsha',
                ],
            ],
            [
                'title' => 'Business Travel Deal',
                'slug' => 'al-barsha-business-travel-deal',
                'description' => 'Corporate rates made even better. Enjoy 25% off stays at our Barsha Heights hotels, right in the heart of Dubai\'s tech and media hub. Walk to Internet City, Media City, and Knowledge Village. Includes complimentary high-speed WiFi and business centre access.',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'start_date' => $today,
                'end_date' => $today->copy()->addMonths(3),
                'hotel_slugs' => [
                    'media-one-hotel',
                    'pullman-dubai-city-center-deira-residence',
                    'movenpick-hotel-apartments-al-barsha',
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

        $this->command->info("Seeded {$seededCount} deals for al-barsha-hotels domain.");
    }
}
