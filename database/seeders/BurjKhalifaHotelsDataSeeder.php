<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Location;
use App\Models\Review;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BurjKhalifaHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Burj Khalifa Hotels data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'burjkhalifahotelsonline')->firstOrFail();

        $locations = $this->seedLocations($domain);
        $hotels = $this->seedHotels($domain, $locations);
        $this->seedHotelImages($hotels);
        $this->seedRoomTypes($hotels);
        $this->seedReviews($domain, $hotels);

        // Update min_price on each hotel
        foreach ($hotels as $hotel) {
            $minPrice = $hotel->roomTypes()->where('is_active', true)->min('base_price');
            if ($minPrice) {
                $hotel->update(['min_price' => $minPrice]);
            }
        }

        $this->command->info('Burj Khalifa Hotels data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Downtown Dubai',
                'slug' => 'downtown-dubai',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'The crown jewel of Dubai, home to Burj Khalifa, Dubai Mall, and the mesmerising Dubai Fountain. A world-class destination for luxury hotels, fine dining, and iconic attractions.',
                'short_description' => 'Home to Burj Khalifa and Dubai Mall',
                'latitude' => 25.1972,
                'longitude' => 55.2744,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Downtown Dubai Hotels — Stay Near Burj Khalifa & Dubai Mall',
                'meta_description' => 'Book hotels in Downtown Dubai. Walking distance to Burj Khalifa, Dubai Mall, and Dubai Fountain. Luxury stays with iconic skyline views.',
            ],
            [
                'name' => 'Business Bay',
                'slug' => 'business-bay',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'Dubai\'s dynamic commercial waterfront district along the Dubai Canal. A vibrant mix of luxury hotels, fine dining, and stunning views of the Downtown skyline.',
                'short_description' => 'Dubai\'s commercial waterfront district',
                'latitude' => 25.1865,
                'longitude' => 55.2708,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Business Bay Hotels — Waterfront Hotels in Dubai',
                'meta_description' => 'Stay in Business Bay with canal views and easy access to Downtown Dubai. Modern hotels for business and leisure travellers.',
            ],
            [
                'name' => 'DIFC',
                'slug' => 'difc',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'Dubai International Financial Centre — the region\'s leading financial hub. An elegant district with world-class hotels, Michelin-starred restaurants, and Gate Avenue\'s boutique shopping.',
                'short_description' => 'Dubai International Financial Centre',
                'latitude' => 25.2100,
                'longitude' => 55.2790,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'DIFC Hotels — Luxury Hotels in Dubai Financial Centre',
                'meta_description' => 'Book hotels in DIFC, Dubai\'s prestigious financial district. Close to Gate Avenue, Michelin restaurants, and Downtown Dubai.',
            ],
            [
                'name' => 'Dubai Creek',
                'slug' => 'dubai-creek',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'Dubai\'s historic waterfront district where the city began. A charming area with traditional souks, dhow cruises, and some of Dubai\'s most distinctive luxury hotels.',
                'short_description' => 'Historic waterfront district',
                'latitude' => 25.2470,
                'longitude' => 55.3307,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Dubai Creek Hotels — Heritage Hotels in Old Dubai',
                'meta_description' => 'Experience Dubai\'s heritage with hotels along the historic Creek. Near Gold Souk, spice markets, and traditional dhow cruises.',
            ],
        ];

        $locations = [];
        foreach ($locationData as $i => $data) {
            $location = Location::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            $domain->locations()->syncWithoutDetaching([
                $location->id => ['is_active' => true, 'sort_order' => $i],
            ]);

            $locations[$data['slug']] = $location;
            $this->command->line("  Location: {$location->name}");
        }

        return $locations;
    }

    // ─── Hotels ────────────────────────────────────────────────────────

    private function seedHotels(Domain $domain, array $locations): array
    {
        $hotelData = [
            // ── Downtown Dubai (4 hotels) ──
            [
                'name' => 'Armani Hotel Dubai',
                'location' => 'downtown-dubai',
                'star_rating' => 5,
                'short_description' => 'Giorgio Armani\'s first hotel, located inside Burj Khalifa',
                'description' => 'Armani Hotel Dubai occupies the lower floors of Burj Khalifa, the world\'s tallest building. Personally designed by Giorgio Armani, the 160-room hotel exemplifies Italian elegance with minimalist interiors, custom furnishings, and a Lifescape spa. Eight restaurants and lounges offer cuisine from Indian to Mediterranean. Guests enjoy private access to Burj Khalifa\'s observation deck and the exclusive Armani/Priv\u00e9 nightclub.',
                'address' => 'Burj Khalifa, 1 Sheikh Mohammed bin Rashid Blvd, Downtown Dubai',
                'latitude' => 25.1972,
                'longitude' => 55.2744,
                'phone' => '+971 4 888 3888',
                'email' => 'dubai@armanihotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Address Downtown',
                'location' => 'downtown-dubai',
                'star_rating' => 5,
                'short_description' => 'Iconic luxury hotel with Dubai Fountain views and rooftop pool',
                'description' => 'Address Downtown is one of Dubai\'s most recognizable hotels, standing tall beside Dubai Mall with unobstructed views of Burj Khalifa and Dubai Fountain. The 220-room hotel features the acclaimed Zeta restaurant, The Spa at Address, an infinity pool overlooking the Fountain, and a premium shopping arcade. Its central location makes it the perfect base for exploring everything Downtown Dubai has to offer.',
                'address' => 'Sheikh Mohammed bin Rashid Blvd, Downtown Dubai',
                'latitude' => 25.1935,
                'longitude' => 55.2798,
                'phone' => '+971 4 436 8888',
                'email' => 'reservations@addresshotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'Vida Downtown Dubai',
                'location' => 'downtown-dubai',
                'star_rating' => 4,
                'short_description' => 'Trendy lifestyle hotel in the heart of Downtown Dubai',
                'description' => 'Vida Downtown Dubai is a contemporary lifestyle hotel designed for the modern traveller. With 156 stylish rooms, a vibrant lobby caf\u00e9, rooftop pool with skyline views, and a co-working space, it blends work and leisure seamlessly. Located on Sheikh Mohammed bin Rashid Boulevard, guests are steps from Dubai Mall, Souk Al Bahar, and the Fountain boardwalk.',
                'address' => 'Sheikh Mohammed bin Rashid Blvd, Downtown Dubai',
                'latitude' => 25.1910,
                'longitude' => 55.2760,
                'phone' => '+971 4 428 6888',
                'email' => 'info@vidahotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 9, 10, 13, 14, 15, 22, 26, 27, 28],
            ],
            [
                'name' => 'Rove Downtown Dubai',
                'location' => 'downtown-dubai',
                'star_rating' => 3,
                'short_description' => 'Affordable, cheerful hotel with easy access to Burj Khalifa and Dubai Mall',
                'description' => 'Rove Downtown Dubai brings the brand\'s signature fun and value to the heart of Downtown. With 420 compact but cleverly designed rooms, The Daily restaurant, an outdoor pool, a 24-hour gym, and a laundromat, it offers everything the smart traveller needs. Dubai Mall and Burj Khalifa are a short walk away via the climate-controlled skybridge.',
                'address' => 'Financial Center Road, Downtown Dubai',
                'latitude' => 25.2050,
                'longitude' => 55.2730,
                'phone' => '+971 4 561 9999',
                'email' => 'downtown@rfrovehotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 10, 13, 14, 22, 26, 27, 28],
            ],

            // ── Business Bay (3 hotels) ──
            [
                'name' => 'JW Marriott Marquis Dubai',
                'location' => 'business-bay',
                'star_rating' => 5,
                'short_description' => 'Twin-tower landmark and one of the world\'s tallest hotels',
                'description' => 'JW Marriott Marquis Dubai holds the title of one of the world\'s tallest hotels, rising 72 floors in twin towers above Business Bay. The 1,608-room property features 14 restaurants and lounges, including the award-winning Prime68 steakhouse on the 68th floor. The Saray Spa, two outdoor pools, and a state-of-the-art fitness centre round out the amenities. Connected to Business Bay Metro and minutes from Downtown Dubai.',
                'address' => 'Sheikh Zayed Road, Business Bay, Dubai',
                'latitude' => 25.1870,
                'longitude' => 55.2660,
                'phone' => '+971 4 414 0000',
                'email' => 'reservations@jwmarriottmarquisdubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'The Oberoi Dubai',
                'location' => 'business-bay',
                'star_rating' => 5,
                'short_description' => 'Ultra-luxury Indian hospitality with Burj Khalifa views',
                'description' => 'The Oberoi Dubai brings legendary Indian hospitality to the shores of Business Bay\'s canal. All 252 rooms and suites offer floor-to-ceiling views of Burj Khalifa or the canal. The hotel\'s signature restaurant Eleven serves modern European cuisine, while the Oberoi Spa provides bespoke wellness treatments. The rooftop infinity pool overlooks the Downtown skyline, creating one of Dubai\'s most memorable vistas.',
                'address' => 'Al Habtoor City, Business Bay, Dubai',
                'latitude' => 25.1850,
                'longitude' => 55.2620,
                'phone' => '+971 4 444 1444',
                'email' => 'reservations@oberoihotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'Paramount Hotel Dubai',
                'location' => 'business-bay',
                'star_rating' => 4,
                'short_description' => 'Hollywood-themed hotel with cinematic interiors and rooftop pool',
                'description' => 'Paramount Hotel Dubai brings the glamour of Hollywood to Business Bay. Themed around Paramount Pictures\' golden age, the 823-room hotel features movie memorabilia, a screening room, and restaurants inspired by classic films. The rooftop pool offers panoramic views of Burj Khalifa, while the spa and fitness centre provide luxury wellness. A short walk to Dubai Mall via the pedestrian bridge.',
                'address' => 'Business Bay, Dubai',
                'latitude' => 25.1880,
                'longitude' => 55.2730,
                'phone' => '+971 4 246 6666',
                'email' => 'info@paramounthotelsdubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 9, 10, 13, 14, 15, 22, 26, 27, 28],
            ],

            // ── DIFC (3 hotels) ──
            [
                'name' => 'Four Seasons Hotel DIFC',
                'location' => 'difc',
                'star_rating' => 5,
                'short_description' => 'Sophisticated urban retreat in the heart of Dubai\'s financial centre',
                'description' => 'Four Seasons Hotel DIFC is a sophisticated urban retreat set within the prestigious Gate Village. With 106 spacious rooms and suites, the intimate property offers personalised service that larger hotels cannot match. Luna restaurant serves modern Italian cuisine, while the rooftop pool and lounge offer stunning skyline views. DIFC\'s art galleries, Michelin-starred restaurants, and Gate Avenue are steps away.',
                'address' => 'Gate Village, DIFC, Dubai',
                'latitude' => 25.2120,
                'longitude' => 55.2800,
                'phone' => '+971 4 506 0000',
                'email' => 'reservations.dif@fourseasons.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Ritz-Carlton DIFC',
                'location' => 'difc',
                'star_rating' => 5,
                'short_description' => 'Legendary Ritz-Carlton luxury in Dubai\'s premier financial district',
                'description' => 'Ritz-Carlton DIFC delivers the brand\'s legendary service in a sleek, modern tower in the financial district. The 355-room hotel features the award-winning Brasserie Boulud by Daniel Boulud, an opulent spa, outdoor pool, and panoramic views of Downtown Dubai. Connected to Gate Avenue and the DIFC art galleries, it\'s the hotel of choice for discerning business and leisure travellers.',
                'address' => 'DIFC, Dubai',
                'latitude' => 25.2110,
                'longitude' => 55.2780,
                'phone' => '+971 4 372 2222',
                'email' => 'rc.dxbfc.reservations@ritzcarlton.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'DIFC Living Suites',
                'location' => 'difc',
                'star_rating' => 4,
                'short_description' => 'Contemporary serviced suites for extended stays in DIFC',
                'description' => 'DIFC Living Suites offers contemporary serviced apartments designed for extended stays in Dubai\'s financial hub. Each of the 180 suites features a fully equipped kitchen, separate living area, and high-speed WiFi. The property includes a rooftop pool, fitness centre, and business lounge. Directly connected to Gate Avenue and within walking distance of Downtown Dubai and the Metro.',
                'address' => 'DIFC, Sheikh Zayed Road, Dubai',
                'latitude' => 25.2090,
                'longitude' => 55.2770,
                'phone' => '+971 4 506 5000',
                'email' => 'stay@difcliving.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 17, 22, 23, 24, 26, 27, 28],
            ],

            // ── Dubai Creek (2 hotels) ──
            [
                'name' => 'Park Hyatt Dubai',
                'location' => 'dubai-creek',
                'star_rating' => 5,
                'short_description' => 'Mediterranean-inspired resort on the banks of Dubai Creek',
                'description' => 'Park Hyatt Dubai is a serene, Mediterranean-inspired resort set on 100 acres along Dubai Creek. The 225-room property features the iconic Traiteur restaurant, The Thai Kitchen, Amara Spa, three swimming pools, and an 18-hole championship golf course at Dubai Creek Golf Club. With its low-rise architecture and lush gardens, it feels like a world away from the city yet is just 15 minutes from Downtown Dubai.',
                'address' => 'Dubai Creek Golf & Yacht Club, Dubai Creek',
                'latitude' => 25.2330,
                'longitude' => 55.3300,
                'phone' => '+971 4 602 1234',
                'email' => 'dubai.park@hyatt.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Palazzo Versace Dubai',
                'location' => 'dubai-creek',
                'star_rating' => 5,
                'short_description' => 'Opulent Italian palazzo with Versace-designed interiors throughout',
                'description' => 'Palazzo Versace Dubai is one of the world\'s most luxurious hotels, designed entirely by the House of Versace. Every detail — from the custom Versace furnishings to the hand-laid Italian mosaic tiles — exudes opulence. The 215-room palazzo features Enigma restaurant, three pools (including a stunning tiled main pool), Versace Spa, and direct Creek views. A truly unique Dubai experience for those who appreciate the finest things in life.',
                'address' => 'Culture Village, Al Jadaf, Dubai Creek',
                'latitude' => 25.2280,
                'longitude' => 55.3240,
                'phone' => '+971 4 556 8888',
                'email' => 'reservations@palazzoversace.ae',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
        ];

        $hotels = [];
        foreach ($hotelData as $i => $data) {
            $locationSlug = $data['location'];
            $location = $locations[$locationSlug] ?? null;

            if (! $location) {
                $this->command->warn("  Skipping {$data['name']} — location '{$locationSlug}' not found");

                continue;
            }

            $amenityIds = $data['amenities'] ?? [];
            unset($data['location'], $data['amenities']);

            $hotel = Hotel::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'location_id' => $location->id,
                    'is_active' => true,
                ])
            );

            if (count($amenityIds) > 0) {
                $hotel->amenities()->syncWithoutDetaching($amenityIds);
            }

            $domain->hotels()->syncWithoutDetaching([
                $hotel->id => ['is_active' => true, 'sort_order' => $i],
            ]);

            $hotels[] = $hotel;
            $this->command->line("  Hotel: {$hotel->name} ({$hotel->star_rating}★)");
        }

        return $hotels;
    }

    // ─── Hotel Images ──────────────────────────────────────────────────

    private function seedHotelImages(array $hotels): void
    {
        $imageCategories = [
            ['category' => 'exterior', 'keywords' => 'hotel,lobby,luxury'],
            ['category' => 'room', 'keywords' => 'hotel,room,modern'],
            ['category' => 'pool', 'keywords' => 'hotel,pool,rooftop'],
            ['category' => 'dining', 'keywords' => 'restaurant,dining,elegant'],
            ['category' => 'bathroom', 'keywords' => 'hotel,bathroom,marble'],
        ];

        $counter = 500;

        foreach ($hotels as $hotel) {
            if ($hotel->images()->count() >= 3) {
                continue;
            }

            foreach ($imageCategories as $j => $img) {
                $url = "https://loremflickr.com/800/600/{$img['keywords']}?lock=" . $counter++;

                HotelImage::updateOrCreate(
                    ['hotel_id' => $hotel->id, 'sort_order' => $j],
                    [
                        'image_path' => $url,
                        'category' => $img['category'],
                        'alt_text' => "{$hotel->name} — " . ucfirst($img['category']),
                        'is_primary' => $j === 0,
                    ]
                );
            }
        }

        $this->command->line('  Hotel images seeded');
    }

    // ─── Room Types ────────────────────────────────────────────────────

    private function seedRoomTypes(array $hotels): void
    {
        $roomTemplates = [
            ['name' => 'Standard Room', 'base_price' => 350, 'max_guests' => 2, 'beds' => '1 King or 2 Twin', 'size' => 28],
            ['name' => 'Deluxe Room', 'base_price' => 550, 'max_guests' => 2, 'beds' => '1 King', 'size' => 36],
            ['name' => 'Superior Suite', 'base_price' => 850, 'max_guests' => 3, 'beds' => '1 King + Sofa Bed', 'size' => 52],
            ['name' => 'Executive Suite', 'base_price' => 1200, 'max_guests' => 3, 'beds' => '1 King + Living Area', 'size' => 68],
            ['name' => 'Family Room', 'base_price' => 650, 'max_guests' => 4, 'beds' => '2 Double Beds', 'size' => 42],
        ];

        foreach ($hotels as $hotel) {
            if ($hotel->roomTypes()->count() > 0) {
                continue;
            }

            $priceMultiplier = match (true) {
                $hotel->star_rating >= 5 => 1.8,
                $hotel->star_rating >= 4 => 1.2,
                default => 1.0,
            };

            $numRooms = match (true) {
                $hotel->star_rating >= 5 => 5,
                $hotel->star_rating >= 4 => 4,
                default => 3,
            };

            foreach (array_slice($roomTemplates, 0, $numRooms) as $j => $tpl) {
                $price = round($tpl['base_price'] * $priceMultiplier, -1);

                RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => $tpl['name'],
                    'slug' => Str::slug($tpl['name']) . '-' . $hotel->id,
                    'description' => "Comfortable {$tpl['name']} at {$hotel->name} with modern amenities and {$tpl['beds']}.",
                    'base_price' => $price,
                    'max_guests' => $tpl['max_guests'],
                    'bed_type' => $tpl['beds'],
                    'room_size_sqm' => $tpl['size'],
                    'is_active' => true,
                    'sort_order' => $j,
                ]);
            }
        }

        $this->command->line('  Room types seeded');
    }

    // ─── Reviews ───────────────────────────────────────────────────────

    private function seedReviews(Domain $domain, array $hotels): void
    {
        $reviewerPool = [
            ['name' => 'Ahmed Al-Rashid', 'country' => 'UAE'],
            ['name' => 'Sarah Johnson', 'country' => 'United Kingdom'],
            ['name' => 'Marco Rossi', 'country' => 'Italy'],
            ['name' => 'Fatima Hassan', 'country' => 'Jordan'],
            ['name' => 'John Mitchell', 'country' => 'United States'],
            ['name' => 'Priya Sharma', 'country' => 'India'],
            ['name' => 'Hans Weber', 'country' => 'Germany'],
            ['name' => 'Yuki Tanaka', 'country' => 'Japan'],
            ['name' => 'Maria Santos', 'country' => 'Brazil'],
            ['name' => 'Chen Wei', 'country' => 'China'],
            ['name' => 'Olga Petrova', 'country' => 'Russia'],
            ['name' => 'Mohamed Ali', 'country' => 'Egypt'],
            ['name' => 'Emma Williams', 'country' => 'Australia'],
            ['name' => 'Ravi Patel', 'country' => 'India'],
            ['name' => 'Sophie Dubois', 'country' => 'France'],
            ['name' => 'Khalid Al-Mansoori', 'country' => 'UAE'],
        ];

        $comments = [
            'Excellent location near Burj Khalifa. The views from our room were absolutely breathtaking. Staff were incredibly helpful.',
            'Great value for money in Downtown Dubai. The room was clean, modern, and well-equipped. Would definitely come back.',
            'Perfect base for exploring Dubai. Dubai Mall is just a short walk away. Breakfast buffet was outstanding.',
            'Loved the rooftop pool with Burj Khalifa views. Room was spacious and the bed was extremely comfortable.',
            'Professional and friendly staff. The hotel arranged Dubai Fountain viewing which was very convenient.',
            'Stayed for a business trip — excellent WiFi, quiet rooms, and the DIFC business centre was well-equipped.',
            'Family-friendly hotel with great amenities. Kids loved the pool and the restaurant had good options for children.',
            'Beautiful modern design and great attention to detail. The lobby lounge had amazing views of the skyline.',
            'Super clean and well-maintained. The gym was modern and had everything I needed.',
            'Walking distance to Dubai Mall was a huge plus. We saved so much on transportation.',
            'The restaurant served authentic international cuisine. Breakfast had both Arabic and Western options.',
            'Quiet room despite being in a busy area. Blackout curtains were perfect for sleeping.',
            'Excellent concierge service — they helped us book Burj Khalifa At The Top tickets and dinner cruise.',
            'Room service was prompt and the food quality was consistent. Late check-out was accommodated.',
            'The spa was a wonderful surprise. Had a great massage after a long day of sightseeing.',
            'Check-in was smooth and fast. They upgraded our room to a Fountain view which was a lovely gesture.',
        ];

        $reviewerIndex = 0;

        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() >= 4) {
                continue;
            }

            $numReviews = rand(4, 6);

            for ($r = 0; $r < $numReviews; $r++) {
                $reviewer = $reviewerPool[$reviewerIndex % count($reviewerPool)];
                $comment = $comments[$reviewerIndex % count($comments)];
                $rating = collect([4.0, 4.2, 4.5, 4.6, 4.8, 5.0])->random();

                Review::create([
                    'hotel_id' => $hotel->id,
                    'guest_name' => $reviewer['name'],
                    'rating' => $rating,
                    'title' => 'Great stay at ' . $hotel->name,
                    'comment' => $comment,
                    'is_approved' => true,
                ]);

                $reviewerIndex++;
            }
        }

        // Link reviews as testimonials
        $reviews = Review::whereIn('hotel_id', collect($hotels)->pluck('id'))
            ->where('rating', '>=', 4.5)
            ->inRandomOrder()
            ->limit(12)
            ->get();

        foreach ($reviews as $i => $review) {
            $domain->testimonials()->syncWithoutDetaching([
                $review->id => ['sort_order' => $i],
            ]);
        }

        $this->command->line("  Reviews seeded & {$reviews->count()} testimonials linked");
    }
}
