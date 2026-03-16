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
                'description' => 'The heart of Dubai, home to Burj Khalifa, Dubai Mall, and Dubai Fountain. The city\'s most prestigious address featuring world-class hotels, fine dining, and iconic attractions.',
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
                'description' => 'Dubai\'s dynamic commercial waterfront district along the Dubai Canal. A thriving business hub with luxury hotels, waterfront dining, and stunning skyline views.',
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
                'description' => 'Dubai International Financial Centre — the Middle East\'s leading financial hub. Home to world-class hotels, fine dining, and contemporary art galleries in a prestigious setting.',
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
                'description' => 'Dubai\'s historic waterfront district where tradition meets luxury. Home to heritage souks, five-star hotels, and the iconic dhow-lined creek that gave birth to modern Dubai.',
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
                'description' => 'Armani Hotel Dubai occupies the lower floors of Burj Khalifa, the world\'s tallest building. Personally designed by Giorgio Armani, the 160-room hotel exemplifies Italian elegance with minimalist interiors, custom furnishings, and the Armani/SPA. Eight restaurants and lounges offer cuisine from around the world. Guests enjoy private access to Burj Khalifa\'s observation deck and the exclusive Armani/Privé nightclub.',
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
                'short_description' => 'Iconic luxury hotel overlooking Burj Khalifa and Dubai Fountain',
                'description' => 'Address Downtown is one of Dubai\'s most recognizable hotels, standing tall beside Dubai Mall with unobstructed views of Burj Khalifa and Dubai Fountain. The 220-room hotel features a rooftop pool, five acclaimed restaurants, The Spa at Address, and a premium shopping arcade. Its central location makes it the perfect base for exploring everything Downtown Dubai has to offer.',
                'address' => 'Sheikh Mohammed bin Rashid Blvd, Downtown Dubai',
                'latitude' => 25.1938,
                'longitude' => 55.2783,
                'phone' => '+971 4 436 8888',
                'email' => 'info@addresshotels.com',
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
                'short_description' => 'Trendy lifestyle boutique hotel for young professionals',
                'description' => 'Vida Downtown Dubai is a contemporary lifestyle boutique hotel designed for young professionals and modern travellers. With 175 stylish rooms, trendy dining options, a rooftop pool with skyline views, and a vibrant co-working space, it blends work and leisure seamlessly. Located on Sheikh Mohammed bin Rashid Boulevard, guests are steps from Dubai Mall, Souk Al Bahar, and the Fountain boardwalk.',
                'address' => 'Sheikh Mohammed bin Rashid Blvd, Downtown Dubai',
                'latitude' => 25.1905,
                'longitude' => 55.2770,
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
                'short_description' => 'Affordable contemporary hotel near Burj Khalifa and Dubai Mall',
                'description' => 'Rove Downtown Dubai is an affordable contemporary hotel bringing the brand\'s signature fun and value to the heart of Downtown. With 420 compact but cleverly designed rooms, The Daily restaurant, a rooftop pool, a 24-hour gym, and a laundromat, it offers everything the smart traveller needs. Dubai Mall and Burj Khalifa are a short walk away.',
                'address' => 'Financial Centre Road, Downtown Dubai',
                'latitude' => 25.2050,
                'longitude' => 55.2750,
                'phone' => '+971 4 561 9999',
                'email' => 'downtown@rovehotels.com',
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
                'short_description' => 'World\'s tallest hotel rising 355m in twin towers above Business Bay',
                'description' => 'JW Marriott Marquis Dubai holds the title of world\'s tallest hotel at 355 metres, rising 72 floors in twin towers above Business Bay. The 1,608-room property features 14 restaurants and lounges, including the award-winning Prime68 steakhouse on the 68th floor. The Saray Spa, two outdoor pools, and a state-of-the-art fitness centre round out the amenities. Connected to Business Bay Metro and minutes from Downtown Dubai.',
                'address' => 'Sheikh Zayed Road, Business Bay, Dubai',
                'latitude' => 25.1865,
                'longitude' => 55.2708,
                'phone' => '+971 4 414 0000',
                'email' => 'jwmarriott.marquis@marriott.com',
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
                'short_description' => 'Intimate luxury with Indian-inspired spa and fine dining',
                'description' => 'The Oberoi Dubai brings legendary Indian hospitality to the shores of Business Bay\'s canal. All 252 rooms and suites offer floor-to-ceiling views of Burj Khalifa or the canal. The hotel features an Indian-inspired spa with bespoke wellness treatments, fine dining at the signature restaurant, and a rooftop infinity pool overlooking the Downtown skyline, creating one of Dubai\'s most memorable vistas.',
                'address' => 'Al Habtoor City, Business Bay, Dubai',
                'latitude' => 25.1850,
                'longitude' => 55.2680,
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
                'short_description' => 'Hollywood-inspired hotel with cinema-themed interiors',
                'description' => 'Paramount Hotel Dubai brings the glamour of Hollywood to Business Bay. Themed around Paramount Pictures\' golden age, the 823-room hotel features Hollywood-inspired design, cinema-themed interiors, movie memorabilia, a screening room, and restaurants inspired by classic films. The rooftop pool offers panoramic views of Burj Khalifa, while the spa and fitness centre provide luxury wellness.',
                'address' => 'Business Bay, Dubai',
                'latitude' => 25.1880,
                'longitude' => 55.2690,
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
                'short_description' => 'Ultra-luxury urban retreat in Gate Village with MINA Brasserie',
                'description' => 'Four Seasons Hotel DIFC is an ultra-luxury urban retreat set within the prestigious Gate Village. With 106 spacious rooms and suites, the intimate property offers personalised service that larger hotels cannot match. MINA Brasserie serves modern cuisine, while the rooftop pool and lounge offer stunning skyline views. DIFC\'s art galleries, Michelin-starred restaurants, and Gate Avenue are steps away.',
                'address' => 'Gate Village, DIFC, Dubai',
                'latitude' => 25.2100,
                'longitude' => 55.2790,
                'phone' => '+971 4 506 0000',
                'email' => 'reservations.dub@fourseasons.com',
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
                'short_description' => 'Legendary Ritz-Carlton service in Dubai\'s financial district',
                'description' => 'Ritz-Carlton DIFC delivers legendary Ritz service in a sleek, modern tower in the financial district. The 349-room hotel features multiple dining outlets, the Club Lounge, an opulent spa, outdoor pool, and panoramic views of Downtown Dubai. Connected to Gate Avenue and the DIFC art galleries, it\'s the hotel of choice for discerning business and leisure travellers.',
                'address' => 'DIFC, Dubai',
                'latitude' => 25.2110,
                'longitude' => 55.2800,
                'phone' => '+971 4 372 2222',
                'email' => 'rc.dxbdc.reservations@ritzcarlton.com',
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
                'short_description' => 'Extended-stay serviced apartments in DIFC with full kitchens',
                'description' => 'DIFC Living Suites offers extended-stay serviced apartments designed for professionals in Dubai\'s financial hub. Each of the 180 units features a fully equipped kitchen, separate living area, and high-speed WiFi. The property includes a rooftop pool, fitness centre, and business lounge. Directly connected to Gate Avenue and within walking distance of Downtown Dubai and the Metro.',
                'address' => 'Gate Avenue, DIFC, Dubai',
                'latitude' => 25.2095,
                'longitude' => 55.2785,
                'phone' => '+971 4 506 5555',
                'email' => 'info@difcliving.com',
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
                'short_description' => 'Mediterranean-style resort on Dubai Creek with 18-hole golf course',
                'description' => 'Park Hyatt Dubai is a serene, Mediterranean-style resort set on the banks of Dubai Creek. The 225-room property features The Thai Kitchen, Amara Spa, three swimming pools, an 18-hole championship golf course at Dubai Creek Golf Club, and a Creek marina. With its low-rise architecture and lush gardens, it feels like a world away from the city yet is just 15 minutes from Downtown Dubai.',
                'address' => 'Dubai Creek Golf & Yacht Club, Dubai Creek, Dubai',
                'latitude' => 25.2290,
                'longitude' => 55.3330,
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
                'short_description' => 'Italian opulence on Dubai Creek with neo-classical Versace design',
                'description' => 'Palazzo Versace Dubai is one of the world\'s most luxurious hotels, bringing Italian opulence to the banks of Dubai Creek. Every detail — from the neo-classical design and custom Versace furnishings to the hand-laid Italian mosaic tiles — exudes opulence. The 215-room palazzo features the Vanitas restaurant, a Versace boutique spa, three pools, and direct Creek views. A truly unique Dubai experience for those who appreciate the finest things in life.',
                'address' => 'Culture Village, Dubai Creek, Dubai',
                'latitude' => 25.2340,
                'longitude' => 55.3280,
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
            ['name' => 'Khalid Al-Maktoum', 'country' => 'UAE'],
            ['name' => 'James Thompson', 'country' => 'United Kingdom'],
            ['name' => 'Alessandra Bianchi', 'country' => 'Italy'],
            ['name' => 'Noor Al-Hashimi', 'country' => 'Jordan'],
            ['name' => 'Robert Chen', 'country' => 'United States'],
            ['name' => 'Ananya Reddy', 'country' => 'India'],
            ['name' => 'Klaus Mueller', 'country' => 'Germany'],
            ['name' => 'Sakura Watanabe', 'country' => 'Japan'],
            ['name' => 'Isabella Costa', 'country' => 'Brazil'],
            ['name' => 'Li Wei Zhang', 'country' => 'China'],
            ['name' => 'Natasha Ivanova', 'country' => 'Russia'],
            ['name' => 'Omar Farouk', 'country' => 'Egypt'],
            ['name' => 'Charlotte Hughes', 'country' => 'Australia'],
            ['name' => 'Vikram Malhotra', 'country' => 'India'],
            ['name' => 'Camille Laurent', 'country' => 'France'],
            ['name' => 'Sultan Al-Nahyan', 'country' => 'UAE'],
        ];

        $comments = [
            'Breathtaking Burj Khalifa views from our room. The location is absolutely unbeatable — Dubai Mall and Dubai Fountain at your doorstep.',
            'World-class service and impeccable attention to detail. The spa experience was heavenly. Will definitely return.',
            'Perfect base for exploring Downtown Dubai. Walking distance to everything — Dubai Mall, souks, and the best restaurants.',
            'The rooftop pool overlooking the skyline was unforgettable. Room was spacious and beautifully designed.',
            'Outstanding concierge service. They arranged a private dinner cruise on Dubai Creek — magical experience.',
            'Ideal for business travel — DIFC offices within walking distance, excellent meeting facilities, and fast WiFi.',
            'Brought the family and everyone loved it. Kids were amazed by the Dubai Fountain show visible from the room.',
            'Elegant design throughout, from the lobby to the room. Every detail screams luxury and sophistication.',
            'The fitness centre was exceptional — panoramic views while working out. Restaurant breakfast was world-class.',
            'Loved being able to walk to Dubai Mall directly from the hotel. Saved so much time on transport.',
            'Remarkable culinary experiences at the hotel restaurants. The Arabic-fusion dinner was the highlight of our trip.',
            'Incredibly peaceful despite being in the heart of the city. Soundproofing was excellent.',
            'The business centre and meeting rooms were perfectly equipped. Staff helped arrange everything seamlessly.',
            'Room service was impeccable — prompt, beautifully presented, and delicious. Late checkout was a great touch.',
            'The spa offered traditional Arabian treatments that were truly unique. Best massage I\'ve ever had.',
            'Smooth check-in process and a surprise room upgrade. The hospitality here sets a new standard.',
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
