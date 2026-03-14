<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Career;
use App\Models\CareerApplication;
use App\Models\Domain;
use App\Models\DomainAnalytics;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PricingRule;
use App\Models\Review;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    private int $imageCounter = 1;

    public function run(): void
    {
        $this->command->info('Seeding test data...');

        $this->ensureStorageDirectories();
        $this->createPlaceholderFallback();

        $domains = $this->seedDomains();
        $locations = $this->seedLocations($domains);
        $hotels = $this->seedHotels($domains, $locations);
        $this->seedHotelImages($hotels);
        $roomTypes = $this->seedRoomTypes($hotels);
        $this->seedRoomAvailability($roomTypes);
        $bookings = $this->seedBookings($domains, $hotels, $roomTypes);
        $this->seedReviews($hotels, $bookings);
        $this->seedPricingRules($domains, $hotels, $locations);
        $this->seedAnalytics($domains);
        $careers = $this->seedCareers($domains);
        $this->seedCareerApplications($careers, $domains);

        $this->command->info('Test data seeded successfully!');
    }

    // ─── Storage Setup ───────────────────────────────────────────────

    private function ensureStorageDirectories(): void
    {
        Storage::disk('public')->makeDirectory('hotels');
        Storage::disk('public')->makeDirectory('locations');
        Storage::disk('public')->makeDirectory('domains');
    }

    private function createPlaceholderFallback(): void
    {
        $path = public_path('images');
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if (! file_exists($path.'/placeholder-hotel.jpg')) {
            $this->generatePlaceholderImage($path.'/placeholder-hotel.jpg', 800, 600, 'No Image');
            $this->command->info('Created placeholder-hotel.jpg');
        }
    }

    // ─── Image Helpers ───────────────────────────────────────────────

    private function downloadImage(string $storagePath, int $width = 800, int $height = 600): string
    {
        $url = "https://picsum.photos/{$width}/{$height}?random=".$this->imageCounter++;

        $fullPath = Storage::disk('public')->path($storagePath);
        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'follow_location' => true,
                    'max_redirects' => 5,
                    'header' => "User-Agent: Mozilla/5.0\r\n",
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $imageData = @file_get_contents($url, false, $context);

            if ($imageData && strlen($imageData) > 1000) {
                file_put_contents($fullPath, $imageData);

                return $storagePath;
            }
        } catch (\Exception $e) {
            // Fall through to GD placeholder
        }

        // GD fallback
        $label = basename(dirname($storagePath));
        $this->generatePlaceholderImage($fullPath, $width, $height, $label);

        return $storagePath;
    }

    private function generatePlaceholderImage(string $path, int $w, int $h, string $label): void
    {
        if (! extension_loaded('gd')) {
            // Create a minimal 1x1 JPEG if GD not available
            file_put_contents($path, base64_decode(
                '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP'.
                str_repeat('A', 50).'//Z'
            ));

            return;
        }

        $img = imagecreatetruecolor($w, $h);

        // Generate a pleasant color based on label hash
        $hash = crc32($label);
        $r = abs($hash) % 80 + 100;
        $g = abs($hash >> 8) % 80 + 100;
        $b = abs($hash >> 16) % 80 + 100;

        $bg = imagecolorallocate($img, $r, $g, $b);
        imagefill($img, 0, 0, $bg);

        $textColor = imagecolorallocate($img, 255, 255, 255);
        $text = strtoupper($label);
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        imagestring($img, $fontSize, ($w - $textWidth) / 2, ($h - $textHeight) / 2, $text, $textColor);

        imagejpeg($img, $path, 85);
        imagedestroy($img);
    }

    // ─── 1. Domains ─────────────────────────────────────────────────

    private function seedDomains(): array
    {
        $this->command->info('  Creating domains...');

        $domainsData = [
            [
                'name' => 'Dubai Luxury Stays',
                'domain' => 'dubailuxurystays.com',
                'slug' => 'dubai-luxury-stays',
                'is_primary' => true,
                'default_currency' => 'AED',
                'meta_title' => 'Dubai Luxury Stays - Premium Hotel Apartments in Dubai',
                'meta_description' => 'Discover handpicked luxury hotel apartments across Dubai. From Marina views to Downtown living, find your perfect stay.',
                'meta_keywords' => 'dubai hotels, luxury apartments, dubai stays, hotel apartments dubai',
                'google_analytics_id' => 'G-TESTDLS001',
            ],
            [
                'name' => 'Dubai Beach Hotels',
                'domain' => 'dubaibeachhotels.com',
                'slug' => 'dubai-beach-hotels',
                'is_primary' => false,
                'default_currency' => 'AED',
                'meta_title' => 'Dubai Beach Hotels - Beachfront Stays in Dubai',
                'meta_description' => 'Find the best beachfront hotels in Dubai. JBR, Palm Jumeirah, and Marina beach hotels with stunning sea views.',
                'meta_keywords' => 'dubai beach hotels, beachfront hotels, jbr hotels, palm jumeirah hotels',
                'google_analytics_id' => 'G-TESTDBH002',
            ],
            [
                'name' => 'Dubai City Apartments',
                'domain' => 'dubaicityapartments.com',
                'slug' => 'dubai-city-apartments',
                'is_primary' => false,
                'default_currency' => 'USD',
                'meta_title' => 'Dubai City Apartments - Urban Living in Dubai',
                'meta_description' => 'Modern city apartments in Dubai\'s most vibrant neighborhoods. Downtown, Business Bay, and DIFC furnished rentals.',
                'meta_keywords' => 'dubai apartments, city apartments, furnished apartments dubai, downtown dubai apartments',
                'google_analytics_id' => 'G-TESTDCA003',
            ],
        ];

        $domains = [];

        foreach ($domainsData as $data) {
            $domain = Domain::create(array_merge($data, [
                'is_active' => true,
                'default_language' => 'en',
                'sitemap_enabled' => true,
            ]));

            $domains[] = $domain;
        }

        return $domains;
    }

    // ─── 2. Locations ────────────────────────────────────────────────

    private function seedLocations(array $domains): array
    {
        $this->command->info('  Creating locations...');

        $locationsData = [
            [
                'name' => 'Downtown Dubai',
                'slug' => 'downtown-dubai',
                'description' => 'The heart of Dubai featuring the iconic Burj Khalifa, Dubai Mall, and the mesmerizing Dubai Fountain. Downtown Dubai is the city\'s most prestigious address, offering world-class dining, shopping, and entertainment within walking distance of luxury hotel apartments.',
                'short_description' => 'Home to Burj Khalifa, Dubai Mall & Dubai Fountain',
                'latitude' => 25.1972,
                'longitude' => 55.2744,
                'is_featured' => true,
            ],
            [
                'name' => 'Dubai Marina',
                'slug' => 'dubai-marina',
                'description' => 'A stunning waterfront community with a 3km promenade lined with cafes, restaurants, and luxury yachts. Dubai Marina is one of the city\'s most sought-after neighborhoods, offering breathtaking views of the marina and easy access to JBR Beach.',
                'short_description' => 'Waterfront living with marina views & vibrant nightlife',
                'latitude' => 25.0805,
                'longitude' => 55.1403,
                'is_featured' => true,
            ],
            [
                'name' => 'Palm Jumeirah',
                'slug' => 'palm-jumeirah',
                'description' => 'The world\'s largest man-made island, shaped like a palm tree. Palm Jumeirah is home to iconic resorts like Atlantis, private beaches, and exclusive residences. A truly unique Dubai experience with panoramic Arabian Gulf views.',
                'short_description' => 'Iconic palm-shaped island with exclusive beachfront resorts',
                'latitude' => 25.1124,
                'longitude' => 55.1390,
                'is_featured' => true,
            ],
            [
                'name' => 'Jumeirah Beach Residence',
                'slug' => 'jumeirah-beach-residence',
                'description' => 'Known locally as JBR, this beachfront community features The Walk — a popular outdoor shopping and dining destination. With direct beach access and a lively atmosphere, JBR is perfect for families and beach lovers.',
                'short_description' => 'Beachfront living with The Walk shopping promenade',
                'latitude' => 25.0782,
                'longitude' => 55.1345,
                'is_featured' => true,
            ],
            [
                'name' => 'Business Bay',
                'slug' => 'business-bay',
                'description' => 'Dubai\'s central business district along the Dubai Water Canal. Business Bay combines modern commercial towers with luxury residential options, trendy restaurants, and excellent connectivity to all parts of the city.',
                'short_description' => 'Dubai\'s thriving business district along the Water Canal',
                'latitude' => 25.1860,
                'longitude' => 55.2680,
                'is_featured' => false,
            ],
            [
                'name' => 'Deira',
                'slug' => 'deira',
                'description' => 'One of Dubai\'s oldest and most culturally rich neighborhoods. Deira is famous for its traditional souks — the Gold Souk, Spice Souk, and Perfume Souk. An authentic taste of old Dubai with modern hotel comforts.',
                'short_description' => 'Historic district with traditional souks & authentic culture',
                'latitude' => 25.2697,
                'longitude' => 55.3095,
                'is_featured' => false,
            ],
        ];

        $locations = [];
        foreach ($locationsData as $i => $data) {
            $imagePath = $this->downloadImage("locations/{$data['slug']}.jpg", 1200, 800);

            $location = Location::create(array_merge($data, [
                'city' => 'Dubai',
                'country' => 'UAE',
                'image_path' => $imagePath,
                'meta_title' => "Hotels in {$data['name']} | Dubai Apartments",
                'meta_description' => $data['short_description'],
                'is_active' => true,
                'sort_order' => $i,
            ]));

            // Attach locations to domains
            foreach ($domains as $di => $domain) {
                $domain->locations()->attach($location->id, [
                    'is_active' => true,
                    'sort_order' => $i,
                ]);
            }

            $locations[] = $location;
        }

        return $locations;
    }

    // ─── 3. Hotels ───────────────────────────────────────────────────

    private function seedHotels(array $domains, array $locations): array
    {
        $this->command->info('  Creating hotels...');

        $hotelsData = [
            // Downtown Dubai (2 hotels)
            [
                'name' => 'The Address Downtown Residences',
                'location_index' => 0,
                'star_rating' => 5,
                'description' => "Experience unparalleled luxury at The Address Downtown Residences, nestled in the heart of Dubai's most prestigious district. Our meticulously designed apartments offer sweeping views of the Burj Khalifa and the glittering Dubai Fountain.\n\nEach residence features contemporary Arabic-inspired interiors, a fully equipped kitchen, and floor-to-ceiling windows that frame the iconic skyline. Guests enjoy access to an infinity pool, state-of-the-art fitness center, and our signature concierge service.\n\nSteps away from Dubai Mall and the DIFC, this is the ultimate address for discerning travelers who demand nothing but the best.",
                'short_description' => 'Ultra-luxury residences with Burj Khalifa views in Downtown Dubai',
                'address' => 'Mohammed Bin Rashid Boulevard, Downtown Dubai',
                'latitude' => 25.1953,
                'longitude' => 55.2790,
                'phone' => '+971 4 123 4567',
                'email' => 'reservations@addressdowntown.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Boulevard Point Apartments',
                'location_index' => 0,
                'star_rating' => 4,
                'description' => "Boulevard Point Apartments offers a premium urban living experience in the center of Downtown Dubai. Located directly on the iconic Mohammed Bin Rashid Boulevard, these modern apartments combine style, comfort, and convenience.\n\nEach apartment is thoughtfully furnished with designer pieces, a modern kitchen, and smart home features. The building provides a rooftop pool, barbecue area, and a landscaped residents' garden.\n\nWhether you're here for business or leisure, Boulevard Point places you at the doorstep of Dubai's finest attractions — Burj Khalifa, Dubai Mall, and the Opera District.",
                'short_description' => 'Stylish apartments on the Boulevard with Fountain views',
                'address' => 'Mohammed Bin Rashid Boulevard, Downtown Dubai',
                'latitude' => 25.1942,
                'longitude' => 55.2801,
                'phone' => '+971 4 234 5678',
                'email' => 'info@boulevardpoint.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
            ],
            // Dubai Marina (2 hotels)
            [
                'name' => 'Marina Terrace Holiday Homes',
                'location_index' => 1,
                'star_rating' => 4,
                'description' => "Marina Terrace Holiday Homes provides a truly memorable stay in one of Dubai's most vibrant waterfront communities. Our collection of apartments offers stunning views of the marina, the sea, or the city skyline.\n\nEvery unit is generously proportioned and equipped with a fully fitted kitchen, washer/dryer, and a private balcony. Residents can enjoy two swimming pools, a fully equipped gym, and direct access to the Marina Walk promenade.\n\nWith Tram and Metro stations just steps away, and JBR Beach a short walk, this is the perfect base for exploring everything Dubai Marina has to offer.",
                'short_description' => 'Waterfront apartments with marina views and beach access',
                'address' => 'Al Marsa Street, Dubai Marina',
                'latitude' => 25.0768,
                'longitude' => 55.1381,
                'phone' => '+971 4 345 6789',
                'email' => 'stay@marinaterrace.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '11:00',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Damac Heights Luxury Suites',
                'location_index' => 1,
                'star_rating' => 5,
                'description' => "Rise above the city at Damac Heights Luxury Suites, an architectural marvel soaring above Dubai Marina. Our premium suites offer panoramic views stretching from the Arabian Gulf to the distant desert horizon.\n\nInteriors are curated by renowned designers with imported marble, bespoke furniture, and Miele appliances. Amenities include a stunning infinity pool on the 70th floor, a private cinema, and 24-hour valet service.\n\nLocated in the heart of Marina, with fine dining, luxury shopping, and the beach all within walking distance, Damac Heights is where luxury meets lifestyle.",
                'short_description' => 'Premium high-rise suites with 70th-floor infinity pool',
                'address' => 'Al Sufouh Road, Dubai Marina',
                'latitude' => 25.0830,
                'longitude' => 55.1410,
                'phone' => '+971 4 456 7890',
                'email' => 'reservations@damacheights.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => true,
                'is_family_friendly' => false,
                'is_featured' => true,
            ],
            // Palm Jumeirah (2 hotels)
            [
                'name' => 'Shoreline Palm Residences',
                'location_index' => 2,
                'star_rating' => 4,
                'description' => "Shoreline Palm Residences invites you to experience island living at its finest on Palm Jumeirah. Set along the trunk of the Palm, these elegant apartments offer private beach access and mesmerizing views of the Arabian Gulf.\n\nEach residence blends Mediterranean-inspired architecture with modern interiors, featuring spacious living areas, a full kitchen, and a large terrace. The community offers landscaped gardens, two swimming pools, a children's play area, and barbecue facilities.\n\nEnjoy easy access to Nakheel Mall, Atlantis Aquaventure, and some of Dubai's best beach clubs — all while returning to your own private beach paradise.",
                'short_description' => 'Island living with private beach on Palm Jumeirah',
                'address' => 'Shoreline Apartments, Palm Jumeirah',
                'latitude' => 25.1180,
                'longitude' => 55.1395,
                'phone' => '+971 4 567 8901',
                'email' => 'bookings@shorelinepalm.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Tiara Emerald Bay Suites',
                'location_index' => 2,
                'star_rating' => 5,
                'description' => "Tiara Emerald Bay Suites represents the pinnacle of Palm Jumeirah living. Positioned on the exclusive crescent of the Palm, every suite commands unobstructed views of the Dubai Marina skyline and the azure waters of the Gulf.\n\nInteriors are adorned with Italian marble, crystal chandeliers, and bespoke furnishings. Guests enjoy a private beach, a residents-only beach club, an Olympic-sized pool, and a world-class spa with hammam.\n\nThis is where Dubai's elite choose to stay — a sanctuary of sophistication mere minutes from the city's finest attractions.",
                'short_description' => 'Exclusive crescent suites with skyline and sea views',
                'address' => 'Crescent Road, Palm Jumeirah',
                'latitude' => 25.1095,
                'longitude' => 55.1480,
                'phone' => '+971 4 678 9012',
                'email' => 'concierge@tiaraemerald.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => true,
                'is_family_friendly' => false,
                'is_featured' => false,
            ],
            // JBR (2 hotels)
            [
                'name' => 'Sadaf JBR Beachfront Apartments',
                'location_index' => 3,
                'star_rating' => 4,
                'description' => "Sadaf JBR Beachfront Apartments puts you right on the sand at one of Dubai's most popular beaches. Step out of your apartment and onto the golden sands of JBR Beach, or stroll along The Walk for world-class dining and entertainment.\n\nOur apartments feature open-plan layouts, modern kitchens, and large balconies overlooking the beach or the iconic Ain Dubai observation wheel. The building offers pool access, a gym, and secure basement parking.\n\nWith Bluewaters Island just across the bridge and Dubai Marina steps away, this is the ideal base for a beach-centered Dubai holiday.",
                'short_description' => 'Direct beach access at JBR with Ain Dubai views',
                'address' => 'The Walk, Jumeirah Beach Residence',
                'latitude' => 25.0790,
                'longitude' => 55.1340,
                'phone' => '+971 4 789 0123',
                'email' => 'hello@sadafjbr.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '11:00',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Bahar Beach Residence',
                'location_index' => 3,
                'star_rating' => 4,
                'description' => "Bahar Beach Residence offers a serene coastal escape with direct access to JBR's pristine beach. These thoughtfully appointed apartments combine casual elegance with all the comforts of home.\n\nEnjoy spacious bedrooms, a modern fully-equipped kitchen, and a generous balcony for al fresco dining. The podium level features a temperature-controlled pool, sun deck, children's pool, and barbecue area.\n\nLocated at the quieter end of JBR yet still within easy walking distance of The Walk's cafes, shops, and attractions, Bahar is where relaxation meets convenience.",
                'short_description' => 'Serene beachside living at the quieter end of JBR',
                'address' => 'Bahar, Jumeirah Beach Residence',
                'latitude' => 25.0770,
                'longitude' => 55.1330,
                'phone' => '+971 4 890 1234',
                'email' => 'stay@baharresidence.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
            // Business Bay (2 hotels)
            [
                'name' => 'Executive Towers Business Bay',
                'location_index' => 4,
                'star_rating' => 4,
                'description' => "Executive Towers is the premier serviced apartment destination in Business Bay. Connected directly to Bay Avenue Mall and overlooking the Dubai Water Canal, these apartments are designed for the modern business traveler.\n\nEach apartment features contemporary furnishings, a dedicated work area, high-speed WiFi, and floor-to-ceiling windows with canal or city views. Facilities include three swimming pools, a spa, and multiple dining options in the podium.\n\nWith direct access to Sheikh Zayed Road, a 5-minute drive to DIFC, and 10 minutes to Dubai Mall, Executive Towers delivers the perfect balance of business and leisure.",
                'short_description' => 'Premium business apartments on the Dubai Water Canal',
                'address' => 'Bay Avenue, Business Bay',
                'latitude' => 25.1870,
                'longitude' => 55.2625,
                'phone' => '+971 4 901 2345',
                'email' => 'bookings@executivetowers.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'The Opus by Omniyat',
                'location_index' => 4,
                'star_rating' => 5,
                'description' => "The Opus by Omniyat is a masterpiece designed by the legendary Zaha Hadid. This architectural icon in Business Bay houses ultra-luxury serviced apartments that redefine modern living.\n\nInteriors flow with organic curves and futuristic design elements. Every apartment features smart home automation, designer kitchen appliances, and curated art pieces. The building houses a rooftop pool with skyline views, a HOVR fitness studio, and the award-winning MAINE eatery.\n\nFor those who appreciate visionary architecture and uncompromising luxury, The Opus is in a league of its own.",
                'short_description' => 'Zaha Hadid-designed ultra-luxury in Business Bay',
                'address' => 'Al Abraj Street, Business Bay',
                'latitude' => 25.1855,
                'longitude' => 55.2700,
                'phone' => '+971 4 012 3456',
                'email' => 'reservations@theopus.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
            ],
            // Deira (2 hotels)
            [
                'name' => 'Al Ghurair Residence Hotel Apartments',
                'location_index' => 5,
                'star_rating' => 3,
                'description' => "Al Ghurair Residence Hotel Apartments offers comfortable and affordable accommodation in the historic heart of Deira. Connected to Al Ghurair Centre — one of Dubai's oldest malls — these apartments provide excellent value for money.\n\nEach unit features traditional Arabian decor touches combined with modern amenities including a kitchenette, flat-screen TV, and complimentary WiFi. The hotel offers a rooftop pool, gym, and a popular international buffet restaurant.\n\nIdeal for culture seekers, budget-conscious travelers, and those wanting to explore Dubai's legendary Gold and Spice Souks, which are just a short walk away.",
                'short_description' => 'Affordable comfort near Gold Souk & Al Ghurair Centre',
                'address' => 'Omar Bin Al Khattab Road, Deira',
                'latitude' => 25.2680,
                'longitude' => 55.3075,
                'phone' => '+971 4 123 6789',
                'email' => 'info@alghurairresidence.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Creek View Heritage Suites',
                'location_index' => 5,
                'star_rating' => 3,
                'description' => "Creek View Heritage Suites blends old-world Arabian charm with modern hotel comforts, overlooking the historic Dubai Creek. Watch the traditional abra boats glide across the creek from your private balcony as the call to prayer echoes from nearby mosques.\n\nThe suites feature warm wooden interiors, handcrafted textiles, and locally inspired artwork alongside modern conveniences. Guests enjoy a rooftop terrace with creek views, a traditional Arabic restaurant, and guided souk tours.\n\nA hidden gem for travelers seeking authentic Dubai experiences beyond the glitz of the new city.",
                'short_description' => 'Heritage charm with Dubai Creek views & souk access',
                'address' => 'Baniyas Road, Deira',
                'latitude' => 25.2710,
                'longitude' => 55.3090,
                'phone' => '+971 4 234 6789',
                'email' => 'welcome@creekviewsuites.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '11:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
        ];

        $amenityIds = Amenity::pluck('id')->toArray();
        $hotels = [];

        foreach ($hotelsData as $i => $data) {
            $locationIndex = $data['location_index'];
            unset($data['location_index']);

            $hotel = Hotel::create(array_merge($data, [
                'slug' => Str::slug($data['name']),
                'location_id' => $locations[$locationIndex]->id,
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in. Late cancellations will be charged one night\'s stay.',
                'is_active' => true,
                'meta_title' => $data['name'].' | Dubai Apartments',
                'meta_description' => $data['short_description'],
                'sort_order' => $i,
            ]));

            // Attach 8-12 random amenities
            $count = rand(8, 12);
            $selected = collect($amenityIds)->shuffle()->take($count)->toArray();
            $hotel->amenities()->attach($selected);

            // Attach hotel to domains — primary gets all, others get location-relevant
            $domains[0]->hotels()->attach($hotel->id, ['is_active' => true, 'sort_order' => $i]);

            // Beach hotels go to domain 2, city hotels to domain 3
            if ($data['is_beach_access']) {
                $domains[1]->hotels()->attach($hotel->id, ['is_active' => true, 'sort_order' => $i]);
            } else {
                $domains[2]->hotels()->attach($hotel->id, ['is_active' => true, 'sort_order' => $i]);
            }

            $hotels[] = $hotel;
        }

        return $hotels;
    }

    // ─── 4. Hotel Images ─────────────────────────────────────────────

    private function seedHotelImages(array $hotels): void
    {
        $this->command->info('  Downloading hotel images (this may take a moment)...');

        $categories = ['exterior', 'lobby', 'rooms', 'rooms', 'pool', 'restaurant', 'general', 'general'];

        foreach ($hotels as $hotel) {
            Storage::disk('public')->makeDirectory("hotels/{$hotel->id}");

            $imageCount = rand(5, 8);

            for ($j = 0; $j < $imageCount; $j++) {
                $category = $categories[$j] ?? 'general';
                $filename = "{$category}-".($j + 1).'.jpg';
                $storagePath = "hotels/{$hotel->id}/{$filename}";

                $imagePath = $this->downloadImage($storagePath, 800, 600);

                HotelImage::create([
                    'hotel_id' => $hotel->id,
                    'category' => $category,
                    'image_path' => $imagePath,
                    'alt_text' => $hotel->name.' - '.ucfirst($category),
                    'caption' => ucfirst($category).' view of '.$hotel->name,
                    'is_primary' => $j === 0,
                    'sort_order' => $j,
                ]);
            }
        }
    }

    // ─── 5. Room Types ───────────────────────────────────────────────

    private function seedRoomTypes(array $hotels): array
    {
        $this->command->info('  Creating room types...');

        $roomTemplates = [
            [
                'name' => 'Studio Apartment',
                'description' => 'A stylish studio apartment featuring a comfortable sleeping area, modern kitchenette, and a clean contemporary bathroom. Ideal for solo travelers or couples looking for a smart, compact space.',
                'max_guests' => 2, 'max_adults' => 2, 'max_children' => 0,
                'bed_type' => 'King', 'room_size_sqm' => 35,
                'base_price_range' => [350, 600], 'total_rooms' => 10,
            ],
            [
                'name' => 'One-Bedroom Apartment',
                'description' => 'A spacious one-bedroom apartment with a separate living area, fully equipped kitchen, and a luxurious bedroom with en-suite bathroom. Perfect for extended stays and couples who value privacy.',
                'max_guests' => 3, 'max_adults' => 2, 'max_children' => 1,
                'bed_type' => 'King', 'room_size_sqm' => 55,
                'base_price_range' => [500, 900], 'total_rooms' => 8,
            ],
            [
                'name' => 'Two-Bedroom Apartment',
                'description' => 'A generous two-bedroom apartment offering a master suite with en-suite, a second bedroom, a full kitchen, and a spacious living and dining area. Ideal for families or groups of friends.',
                'max_guests' => 5, 'max_adults' => 4, 'max_children' => 2,
                'bed_type' => 'King + Twin', 'room_size_sqm' => 90,
                'base_price_range' => [800, 1500], 'total_rooms' => 5,
            ],
            [
                'name' => 'Penthouse Suite',
                'description' => 'An exclusive penthouse suite occupying the top floor with wraparound balcony, panoramic views, three bedrooms, a gourmet kitchen, and a private jacuzzi. The ultimate in luxury apartment living.',
                'max_guests' => 6, 'max_adults' => 6, 'max_children' => 2,
                'bed_type' => 'King + Queen + Twin', 'room_size_sqm' => 180,
                'base_price_range' => [2000, 5000], 'total_rooms' => 2,
            ],
        ];

        $roomAmenityIds = Amenity::whereIn('category', ['Room', 'General'])->pluck('id')->toArray();
        $allRoomTypes = [];

        foreach ($hotels as $hotel) {
            // 3-star hotels get 2-3 room types, 4-5 star get 3-4
            $count = $hotel->star_rating >= 4 ? rand(3, 4) : rand(2, 3);
            $templates = array_slice($roomTemplates, 0, $count);

            foreach ($templates as $j => $tpl) {
                $priceRange = $tpl['base_price_range'];
                // Adjust price by star rating
                $multiplier = match ($hotel->star_rating) {
                    5 => 1.6,
                    4 => 1.0,
                    default => 0.7,
                };

                $basePrice = round(rand($priceRange[0], $priceRange[1]) * $multiplier, 2);
                unset($tpl['base_price_range']);

                $roomType = RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => $tpl['name'],
                    'slug' => Str::slug($tpl['name']),
                    'description' => $tpl['description'],
                    'max_guests' => $tpl['max_guests'],
                    'max_adults' => $tpl['max_adults'],
                    'max_children' => $tpl['max_children'],
                    'bed_type' => $tpl['bed_type'],
                    'room_size_sqm' => $tpl['room_size_sqm'],
                    'base_price' => $basePrice,
                    'total_rooms' => $tpl['total_rooms'],
                    'is_active' => true,
                    'sort_order' => $j,
                ]);

                // Attach 3-5 room amenities
                $selectedAmenities = collect($roomAmenityIds)->shuffle()->take(rand(3, 5))->toArray();
                $roomType->amenities()->attach($selectedAmenities);

                $allRoomTypes[] = $roomType;
            }

            // Update hotel min_price denormalized field
            $minPrice = $hotel->roomTypes()->min('base_price');
            $hotel->update(['min_price' => $minPrice ?? 0]);
        }

        return $allRoomTypes;
    }

    // ─── 6. Room Availability ────────────────────────────────────────

    private function seedRoomAvailability(array $roomTypes): void
    {
        $this->command->info('  Creating room availability (next 30 days)...');

        $today = Carbon::today();
        $records = [];

        foreach ($roomTypes as $roomType) {
            for ($d = 0; $d < 30; $d++) {
                $date = $today->copy()->addDays($d);
                $isWeekend = $date->isWeekend();

                $bookedRooms = rand(0, (int) ceil($roomType->total_rooms * 0.6));
                $priceOverride = null;

                // Weekend price bump ~20%
                if ($isWeekend) {
                    $priceOverride = round($roomType->base_price * 1.2, 2);
                }

                // Random high-demand days
                if (rand(1, 10) === 1) {
                    $priceOverride = round($roomType->base_price * 1.35, 2);
                    $bookedRooms = max($bookedRooms, (int) ceil($roomType->total_rooms * 0.8));
                }

                $records[] = [
                    'room_type_id' => $roomType->id,
                    'date' => $date->toDateString(),
                    'available_rooms' => $roomType->total_rooms - $bookedRooms,
                    'booked_rooms' => $bookedRooms,
                    'price_override' => $priceOverride,
                    'is_closed' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Bulk insert in chunks for performance
        foreach (array_chunk($records, 200) as $chunk) {
            DB::table('room_availability')->insert($chunk);
        }
    }

    // ─── 7. Bookings & Payments ──────────────────────────────────────

    private function seedBookings(array $domains, array $hotels, array $roomTypes): array
    {
        $this->command->info('  Creating bookings & payments...');

        $guests = [
            ['first' => 'Ahmed', 'last' => 'Al Maktoum', 'email' => 'ahmed.maktoum@email.com', 'phone' => '+971501234567', 'nationality' => 'UAE'],
            ['first' => 'Sarah', 'last' => 'Williams', 'email' => 'sarah.w@email.com', 'phone' => '+447911234567', 'nationality' => 'UK'],
            ['first' => 'Rajesh', 'last' => 'Patel', 'email' => 'rajesh.patel@email.com', 'phone' => '+919876543210', 'nationality' => 'India'],
            ['first' => 'John', 'last' => 'Smith', 'email' => 'john.smith@email.com', 'phone' => '+12025551234', 'nationality' => 'US'],
            ['first' => 'Fatima', 'last' => 'Hassan', 'email' => 'fatima.h@email.com', 'phone' => '+966501234567', 'nationality' => 'Saudi Arabia'],
            ['first' => 'Michael', 'last' => 'Johnson', 'email' => 'michael.j@email.com', 'phone' => '+61412345678', 'nationality' => 'Australia'],
            ['first' => 'Elena', 'last' => 'Petrova', 'email' => 'elena.p@email.com', 'phone' => '+79161234567', 'nationality' => 'Russia'],
            ['first' => 'Zhang', 'last' => 'Wei', 'email' => 'zhang.wei@email.com', 'phone' => '+8613912345678', 'nationality' => 'China'],
            ['first' => 'Maria', 'last' => 'Schmidt', 'email' => 'maria.s@email.com', 'phone' => '+4917612345678', 'nationality' => 'Germany'],
            ['first' => 'Ali', 'last' => 'Reza', 'email' => 'ali.reza@email.com', 'phone' => '+989121234567', 'nationality' => 'Iran'],
            ['first' => 'Sophie', 'last' => 'Dupont', 'email' => 'sophie.d@email.com', 'phone' => '+33612345678', 'nationality' => 'France'],
            ['first' => 'Omar', 'last' => 'Khalil', 'email' => 'omar.k@email.com', 'phone' => '+971551234567', 'nationality' => 'UAE'],
            ['first' => 'Priya', 'last' => 'Sharma', 'email' => 'priya.s@email.com', 'phone' => '+919812345678', 'nationality' => 'India'],
            ['first' => 'James', 'last' => 'Anderson', 'email' => 'james.a@email.com', 'phone' => '+442071234567', 'nationality' => 'UK'],
            ['first' => 'Yuki', 'last' => 'Tanaka', 'email' => 'yuki.t@email.com', 'phone' => '+819012345678', 'nationality' => 'Japan'],
        ];

        $statuses = ['confirmed', 'confirmed', 'paid', 'paid', 'paid', 'pending', 'cancelled', 'refunded'];
        $specialRequests = [
            'Late check-in please, arriving around midnight.',
            'High floor preferred with city view.',
            'Early check-in if possible, arriving at 10am.',
            'Need a baby cot in the room.',
            'Celebrating our anniversary — any special arrangement would be appreciated.',
            null,
            null,
            null,
            'Airport transfer required.',
            'Quiet room away from elevator please.',
        ];

        $bookings = [];

        for ($i = 0; $i < 25; $i++) {
            $guest = $guests[$i % count($guests)];
            $hotel = $hotels[array_rand($hotels)];
            $hotelRoomTypes = array_filter($roomTypes, fn ($rt) => $rt->hotel_id === $hotel->id);
            $roomType = $hotelRoomTypes[array_rand($hotelRoomTypes)];
            $domain = $domains[array_rand($domains)];

            $checkIn = Carbon::today()->subDays(rand(0, 60))->addDays(rand(0, 15));
            $numNights = rand(1, 7);
            $checkOut = $checkIn->copy()->addDays($numNights);
            $numAdults = rand(1, $roomType->max_adults);
            $numChildren = rand(0, $roomType->max_children);

            $pricePerNight = $roomType->base_price;
            $subtotal = round($pricePerNight * $numNights, 2);
            $taxPct = 5.0;
            $taxAmount = round($subtotal * $taxPct / 100, 2);
            $tourismFee = round(15.0 * $numNights, 2); // AED 15/night
            $serviceCharge = round($subtotal * 0.10, 2); // 10% service charge
            $totalAmount = round($subtotal + $taxAmount + $tourismFee + $serviceCharge, 2);

            $status = $statuses[array_rand($statuses)];
            $bookedAt = $checkIn->copy()->subDays(rand(3, 30));

            $booking = Booking::create([
                'reference_number' => 'BK-'.strtoupper(Str::random(8)),
                'domain_id' => $domain->id,
                'hotel_id' => $hotel->id,
                'room_type_id' => $roomType->id,
                'guest_first_name' => $guest['first'],
                'guest_last_name' => $guest['last'],
                'guest_email' => $guest['email'],
                'guest_phone' => $guest['phone'],
                'guest_nationality' => $guest['nationality'],
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'num_nights' => $numNights,
                'num_adults' => $numAdults,
                'num_children' => $numChildren,
                'num_rooms' => 1,
                'special_requests' => $specialRequests[array_rand($specialRequests)],
                'room_price_per_night' => $pricePerNight,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_percentage' => $taxPct,
                'tourism_fee' => $tourismFee,
                'service_charge' => $serviceCharge,
                'total_amount' => $totalAmount,
                'currency' => 'AED',
                'status' => $status,
                'cancellation_reason' => $status === 'cancelled' ? 'Plans changed, need to reschedule.' : null,
                'cancelled_at' => $status === 'cancelled' ? $bookedAt->copy()->addDays(1) : null,
                'confirmed_at' => in_array($status, ['confirmed', 'paid']) ? $bookedAt : null,
                'ip_address' => '192.168.1.'.rand(1, 254),
                'booked_at' => $bookedAt,
            ]);

            // Create payment for confirmed/paid/refunded bookings
            if (in_array($status, ['confirmed', 'paid', 'refunded'])) {
                $paymentStatus = match ($status) {
                    'refunded' => 'refunded',
                    default => 'completed',
                };

                Payment::create([
                    'booking_id' => $booking->id,
                    'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
                    'payment_method' => collect(['visa', 'mastercard', 'amex'])->random(),
                    'gateway' => 'mashreq',
                    'amount' => $totalAmount,
                    'currency' => 'AED',
                    'status' => $paymentStatus,
                    'gateway_response' => json_encode([
                        'response_code' => '00',
                        'response_message' => 'Approved',
                        'auth_code' => strtoupper(Str::random(6)),
                    ]),
                    'refund_amount' => $status === 'refunded' ? $totalAmount : null,
                    'refund_transaction_id' => $status === 'refunded' ? 'REF-'.strtoupper(Str::random(12)) : null,
                    'refunded_at' => $status === 'refunded' ? $bookedAt->copy()->addDays(5) : null,
                    'paid_at' => $bookedAt,
                ]);
            }

            $bookings[] = $booking;
        }

        return $bookings;
    }

    // ─── 8. Reviews ──────────────────────────────────────────────────

    private function seedReviews(array $hotels, array $bookings): void
    {
        $this->command->info('  Creating reviews...');

        $reviewTemplates = [
            ['rating' => 5, 'title' => 'Absolutely stunning!', 'comment' => 'We had the most incredible stay. The apartment was immaculate, the views were breathtaking, and the staff went above and beyond. Will definitely be back!'],
            ['rating' => 5, 'title' => 'Best hotel in Dubai', 'comment' => 'From the moment we arrived, everything was perfect. The location is unbeatable, rooms are beautifully designed, and the amenities are top-notch. Highly recommend.'],
            ['rating' => 5, 'title' => 'Exceeded expectations', 'comment' => 'This place is even better than the photos suggest. Spacious rooms, amazing pool, and the check-in process was seamless. A truly luxury experience.'],
            ['rating' => 4, 'title' => 'Great stay, would return', 'comment' => 'Lovely apartment with fantastic views. Kitchen was well-stocked, beds were super comfortable. Only minor note — the gym could use some newer equipment.'],
            ['rating' => 4, 'title' => 'Wonderful location', 'comment' => 'Perfect location for exploring Dubai. The apartment was clean, modern, and had everything we needed. Pool area was great but got crowded on weekends.'],
            ['rating' => 4, 'title' => 'Very comfortable', 'comment' => 'Spacious apartment, clean and well-maintained. The concierge was very helpful with restaurant recommendations. Good value for the area.'],
            ['rating' => 4, 'title' => 'Family-friendly and spacious', 'comment' => 'Traveled with kids and the two-bedroom apartment was perfect. Plenty of space, kitchen saved us on meals, and the pool kept the little ones happy.'],
            ['rating' => 4, 'title' => 'Solid choice for business travel', 'comment' => 'Excellent WiFi, quiet rooms, and great location near DIFC. The apartment had a proper desk and the building was very secure. Will book again for my next trip.'],
            ['rating' => 3, 'title' => 'Good but not great', 'comment' => 'Location was excellent and the apartment itself was fine, but it felt a bit dated compared to newer properties in the area. Housekeeping could be more thorough.'],
            ['rating' => 3, 'title' => 'Decent value', 'comment' => 'For the price, it was acceptable. The room was smaller than expected and the air conditioning was a bit noisy, but the location made up for it.'],
            ['rating' => 5, 'title' => 'Perfect honeymoon spot', 'comment' => 'We spent our honeymoon here and it was magical. The sunset views from our balcony, the beautiful pool, and the romantic restaurants nearby made it unforgettable.'],
            ['rating' => 4, 'title' => 'Clean and modern', 'comment' => 'Really enjoyed our stay. The apartment was spotless and had a modern, stylish design. Great coffee machine in the kitchen. Would definitely recommend to friends.'],
            ['rating' => 5, 'title' => 'Luxury at its finest', 'comment' => 'The penthouse was absolutely spectacular. Wraparound views of the city, a private jacuzzi, and the most comfortable bed I have ever slept in. Worth every dirham.'],
            ['rating' => 4, 'title' => 'Great for a week-long stay', 'comment' => 'Stayed for a full week and it felt like home. Full kitchen, washing machine, and the daily housekeeping kept everything fresh. Good gym in the building too.'],
            ['rating' => 3, 'title' => 'Nice but needs updates', 'comment' => 'The apartment was in a great location and the view was lovely, but the furniture showed its age. Bathroom fixtures needed updating. Staff was friendly though.'],
            ['rating' => 5, 'title' => 'Incredible views!', 'comment' => 'Woke up every morning to the most spectacular sunrise over the city. The floor-to-ceiling windows really make this apartment special. Everything was pristine and modern.'],
            ['rating' => 4, 'title' => 'Quiet and peaceful', 'comment' => 'Despite being in a busy area, the apartment was surprisingly quiet. Good soundproofing, comfortable beds, and the balcony was perfect for morning coffee.'],
            ['rating' => 4, 'title' => 'Will book again', 'comment' => 'Third time staying here and it never disappoints. Consistent quality, friendly reception staff, and the location just cannot be beaten. Already planning my next visit.'],
            ['rating' => 3, 'title' => 'Mixed feelings', 'comment' => 'The apartment itself was beautiful and well-appointed. However, the check-in process was slow and the pool was under maintenance for two of our five days. Disappointing.'],
            ['rating' => 5, 'title' => 'A true gem', 'comment' => 'This hidden gem in Deira offers incredible value. The heritage decor was charming, the creek views were beautiful, and the nearby souks were a highlight of our trip.'],
        ];

        $guestNames = [
            'Sarah Williams', 'Ahmed Al Maktoum', 'Rajesh Patel', 'John Smith',
            'Fatima Hassan', 'Michael Johnson', 'Elena Petrova', 'Zhang Wei',
            'Maria Schmidt', 'Ali Reza', 'Sophie Dupont', 'Omar Khalil',
            'Priya Sharma', 'James Anderson', 'Yuki Tanaka', 'Anna Rossi',
            'Carlos Martinez', 'Linda Chen', 'David Brown', 'Amira Nasser',
        ];

        $paidBookings = array_filter($bookings, fn ($b) => in_array($b->status, ['confirmed', 'paid']));

        for ($i = 0; $i < 20; $i++) {
            $tpl = $reviewTemplates[$i];
            $hotel = $hotels[array_rand($hotels)];
            $guestName = $guestNames[$i];

            // Link some reviews to bookings
            $bookingId = null;
            $isVerified = false;
            if ($i < count($paidBookings) && rand(0, 1)) {
                $booking = array_values($paidBookings)[$i % count($paidBookings)];
                $bookingId = $booking->id;
                $hotel = Hotel::find($booking->hotel_id) ?? $hotel;
                $isVerified = true;
            }

            Review::create([
                'hotel_id' => $hotel->id,
                'booking_id' => $bookingId,
                'guest_name' => $guestName,
                'guest_email' => strtolower(str_replace(' ', '.', $guestName)).'@email.com',
                'rating' => $tpl['rating'],
                'title' => $tpl['title'],
                'comment' => $tpl['comment'],
                'is_verified' => $isVerified,
                'is_approved' => $i < 16, // First 16 approved, last 4 pending
                'admin_reply' => $i < 5 ? 'Thank you for your wonderful review! We look forward to welcoming you back soon.' : null,
                'replied_at' => $i < 5 ? now()->subDays(rand(1, 10)) : null,
            ]);
        }

        // Update denormalized hotel ratings
        foreach ($hotels as $hotel) {
            $hotel->refresh();
            $approved = $hotel->reviews()->where('is_approved', true);
            $hotel->update([
                'avg_rating' => round($approved->avg('rating') ?? 0, 2),
                'total_reviews' => $approved->count(),
            ]);
        }
    }

    // ─── 9. Pricing Rules ────────────────────────────────────────────

    private function seedPricingRules(array $domains, array $hotels, array $locations): void
    {
        $this->command->info('  Creating pricing rules...');

        PricingRule::create([
            'name' => 'Dubai Beach Hotels Markup',
            'type' => 'domain_markup',
            'domain_id' => $domains[1]->id,
            'adjustment_type' => 'percentage',
            'adjustment_value' => 10.00,
            'priority' => 10,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'Dubai City Apartments Discount',
            'type' => 'domain_markup',
            'domain_id' => $domains[2]->id,
            'adjustment_type' => 'percentage',
            'adjustment_value' => -5.00,
            'priority' => 10,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'Summer Season Discount',
            'type' => 'seasonal',
            'adjustment_type' => 'percentage',
            'adjustment_value' => -15.00,
            'start_date' => Carbon::now()->year.'-06-01',
            'end_date' => Carbon::now()->year.'-08-31',
            'priority' => 20,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'New Year Peak Pricing',
            'type' => 'date_range',
            'adjustment_type' => 'percentage',
            'adjustment_value' => 40.00,
            'start_date' => Carbon::now()->year.'-12-20',
            'end_date' => (Carbon::now()->year + 1).'-01-05',
            'priority' => 30,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'Weekend Premium',
            'type' => 'day_of_week',
            'adjustment_type' => 'percentage',
            'adjustment_value' => 15.00,
            'days_of_week' => [5, 6], // Friday, Saturday
            'priority' => 5,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'Palm Jumeirah Premium',
            'type' => 'seasonal',
            'location_id' => $locations[2]->id, // Palm Jumeirah
            'adjustment_type' => 'fixed_amount',
            'adjustment_value' => 100.00,
            'start_date' => Carbon::now()->subMonths(1)->toDateString(),
            'end_date' => Carbon::now()->addMonths(6)->toDateString(),
            'priority' => 15,
            'is_active' => true,
        ]);
    }

    // ─── 10. Analytics ──────────────────────────────────────────────

    private function seedAnalytics(array $domains): void
    {
        $this->command->info('  Creating analytics data...');

        foreach ($domains as $domain) {
            for ($d = 29; $d >= 0; $d--) {
                $date = Carbon::today()->subDays($d);
                $isWeekend = $date->isWeekend();
                $baseViews = $isWeekend ? rand(150, 400) : rand(80, 250);

                DomainAnalytics::create([
                    'domain_id' => $domain->id,
                    'date' => $date->toDateString(),
                    'page_views' => $baseViews,
                    'unique_visitors' => (int) round($baseViews * 0.65),
                    'hotel_clicks' => (int) round($baseViews * rand(20, 40) / 100),
                    'booking_starts' => rand(2, 12),
                    'booking_completions' => rand(0, 5),
                    'revenue' => round(rand(500, 8000) + rand(0, 99) / 100, 2),
                    'top_hotels' => json_encode([
                        ['hotel_id' => rand(1, 12), 'clicks' => rand(10, 50)],
                        ['hotel_id' => rand(1, 12), 'clicks' => rand(5, 30)],
                        ['hotel_id' => rand(1, 12), 'clicks' => rand(1, 20)],
                    ]),
                    'top_locations' => json_encode([
                        ['location_id' => rand(1, 6), 'clicks' => rand(10, 40)],
                        ['location_id' => rand(1, 6), 'clicks' => rand(5, 25)],
                    ]),
                    'traffic_sources' => json_encode([
                        'direct' => rand(20, 40),
                        'google' => rand(30, 50),
                        'social' => rand(5, 20),
                        'referral' => rand(5, 15),
                    ]),
                ]);
            }
        }
    }

    // ─── 11. Careers ──────────────────────────────────────────────────

    private function seedCareers(array $domains): array
    {
        $this->command->info('  Creating careers...');

        $careersData = [
            [
                'title' => 'Senior Laravel Developer',
                'location' => 'Dubai, UAE',
                'job_type' => 'full_time',
                'department' => 'Engineering',
                'about_role' => '<p>We are looking for an experienced Senior Laravel Developer to join our growing engineering team. You will be responsible for building and maintaining our multi-domain hotel booking platform, working with Laravel 12, MySQL, and modern frontend technologies.</p><p>This is a fantastic opportunity to work on a high-traffic platform serving thousands of bookings daily across multiple domains.</p>',
                'responsibilities' => '<ul><li>Design, develop, and maintain scalable Laravel applications</li><li>Build RESTful APIs for React frontend integration</li><li>Optimize database queries and implement caching strategies</li><li>Participate in code reviews and mentor junior developers</li><li>Collaborate with product team to define technical requirements</li><li>Implement automated testing and CI/CD pipelines</li></ul>',
                'requirements' => '<ul><li>5+ years of experience with PHP and Laravel framework</li><li>Strong understanding of MySQL, query optimization, and database design</li><li>Experience with RESTful API development</li><li>Familiarity with Redis, queue systems, and caching</li><li>Knowledge of Git, Docker, and deployment workflows</li><li>Excellent problem-solving and communication skills</li></ul>',
                'what_we_offer' => '<ul><li>Competitive salary (AED 18,000 - 25,000/month)</li><li>Health insurance for you and your family</li><li>Annual flight tickets</li><li>Flexible working hours</li><li>Professional development budget</li><li>Modern office in Business Bay with stunning views</li></ul>',
                'last_apply_date' => now()->addDays(45)->toDateString(),
                'sort_order' => 0,
            ],
            [
                'title' => 'React Frontend Developer',
                'location' => 'Dubai, UAE',
                'job_type' => 'full_time',
                'department' => 'Engineering',
                'about_role' => '<p>Join our frontend team to build beautiful, performant hotel booking experiences using React. You will work closely with our designers and backend team to create seamless user interfaces across our multi-domain platform.</p>',
                'responsibilities' => '<ul><li>Build responsive React components with TypeScript</li><li>Integrate with RESTful APIs and manage application state</li><li>Implement pixel-perfect designs with Tailwind CSS</li><li>Optimize performance and ensure cross-browser compatibility</li><li>Write unit and integration tests</li><li>Collaborate with UX designers on interactive prototypes</li></ul>',
                'requirements' => '<ul><li>3+ years of experience with React and TypeScript</li><li>Proficiency in modern CSS (Tailwind, CSS-in-JS)</li><li>Experience with state management (Redux, Zustand, or similar)</li><li>Familiarity with Next.js or similar SSR frameworks</li><li>Understanding of REST APIs and async data fetching</li><li>Eye for detail and passion for great user experiences</li></ul>',
                'what_we_offer' => '<ul><li>Competitive salary (AED 15,000 - 22,000/month)</li><li>Health insurance coverage</li><li>Annual flight tickets</li><li>Remote work options (2 days/week)</li><li>Learning & conference budget</li><li>Team building activities</li></ul>',
                'last_apply_date' => now()->addDays(30)->toDateString(),
                'sort_order' => 1,
            ],
            [
                'title' => 'Digital Marketing Manager',
                'location' => 'Dubai, UAE',
                'job_type' => 'full_time',
                'department' => 'Marketing',
                'about_role' => '<p>We are seeking a results-driven Digital Marketing Manager to lead our online marketing efforts across 70+ hotel listing domains. You will develop and execute comprehensive digital marketing strategies to drive traffic, bookings, and brand awareness.</p>',
                'responsibilities' => '<ul><li>Plan and execute SEO, SEM, and social media campaigns</li><li>Manage Google Ads and Meta advertising budgets</li><li>Analyze website analytics and optimize conversion funnels</li><li>Develop content marketing strategies for multiple domains</li><li>Manage email marketing campaigns and automation</li><li>Report on KPIs and ROI to leadership</li></ul>',
                'requirements' => '<ul><li>5+ years of digital marketing experience in hospitality/travel</li><li>Expert knowledge of Google Analytics, Google Ads, and Meta Business Suite</li><li>Strong SEO skills (on-page, off-page, technical)</li><li>Experience managing marketing budgets of AED 100K+/month</li><li>Excellent analytical and data-driven decision making</li><li>Strong English communication skills; Arabic is a plus</li></ul>',
                'what_we_offer' => '<ul><li>Competitive salary + performance bonus</li><li>Health insurance</li><li>Annual flight tickets</li><li>Creative freedom to shape marketing strategy</li><li>Growing company with advancement opportunities</li></ul>',
                'last_apply_date' => now()->addDays(20)->toDateString(),
                'sort_order' => 2,
            ],
            [
                'title' => 'Customer Support Executive',
                'location' => 'Dubai, UAE',
                'job_type' => 'full_time',
                'department' => 'Customer Support',
                'about_role' => '<p>We need a friendly and professional Customer Support Executive to handle guest inquiries, booking assistance, and complaint resolution. You will be the first point of contact for our guests across multiple hotel booking platforms.</p>',
                'responsibilities' => '<ul><li>Handle inbound calls, emails, and live chat inquiries</li><li>Assist guests with booking modifications and cancellations</li><li>Resolve complaints and escalate complex issues</li><li>Coordinate with hotels for special guest requests</li><li>Maintain accurate records in the CRM system</li><li>Provide feedback to improve guest experience</li></ul>',
                'requirements' => '<ul><li>2+ years of customer service experience (hospitality preferred)</li><li>Fluent in English; Arabic or Hindi is a strong plus</li><li>Excellent communication and interpersonal skills</li><li>Ability to work rotating shifts including weekends</li><li>Proficiency in MS Office and CRM tools</li><li>Calm and professional demeanor under pressure</li></ul>',
                'what_we_offer' => '<ul><li>Salary: AED 6,000 - 9,000/month + incentives</li><li>Health insurance</li><li>Annual flight tickets</li><li>Career growth opportunities</li><li>Training and development programs</li></ul>',
                'last_apply_date' => now()->addDays(15)->toDateString(),
                'sort_order' => 3,
            ],
            [
                'title' => 'UI/UX Designer',
                'location' => 'Dubai, UAE (Hybrid)',
                'job_type' => 'full_time',
                'department' => 'Design',
                'about_role' => '<p>We are looking for a creative UI/UX Designer to craft intuitive and visually stunning booking experiences. You will design for web and mobile platforms, ensuring our hotel listing sites are both beautiful and user-friendly.</p>',
                'responsibilities' => '<ul><li>Create wireframes, prototypes, and high-fidelity designs in Figma</li><li>Conduct user research and usability testing</li><li>Design responsive layouts for multi-domain hotel platforms</li><li>Collaborate with developers on design implementation</li><li>Maintain and evolve the design system</li><li>Create marketing assets and brand collateral</li></ul>',
                'requirements' => '<ul><li>3+ years of UI/UX design experience</li><li>Expert proficiency in Figma</li><li>Strong portfolio showcasing web and mobile designs</li><li>Understanding of responsive design and accessibility</li><li>Knowledge of design systems and component libraries</li><li>Experience in hospitality or e-commerce industry is a plus</li></ul>',
                'what_we_offer' => '<ul><li>Competitive salary (AED 12,000 - 18,000/month)</li><li>Health insurance</li><li>Hybrid work arrangement (3 office + 2 remote)</li><li>Latest design tools and hardware</li><li>Creative team environment</li></ul>',
                'last_apply_date' => now()->addDays(25)->toDateString(),
                'sort_order' => 4,
            ],
            [
                'title' => 'Content Writer (Hospitality)',
                'location' => 'Dubai, UAE',
                'job_type' => 'part_time',
                'department' => 'Content',
                'about_role' => '<p>We need a talented Content Writer to create compelling hotel descriptions, location guides, blog posts, and SEO content for our portfolio of hotel listing websites. This is a part-time role ideal for experienced writers who want flexibility.</p>',
                'responsibilities' => '<ul><li>Write engaging hotel and location descriptions</li><li>Create SEO-optimized blog content about Dubai travel</li><li>Develop landing page copy for marketing campaigns</li><li>Write email newsletter content</li><li>Proofread and edit content across multiple domains</li></ul>',
                'requirements' => '<ul><li>2+ years of content writing experience</li><li>Excellent English writing skills (native or near-native)</li><li>Understanding of SEO best practices</li><li>Knowledge of Dubai tourism and hospitality industry</li><li>Ability to write in different brand voices</li><li>Portfolio of published writing samples</li></ul>',
                'what_we_offer' => '<ul><li>AED 5,000 - 8,000/month (part-time)</li><li>Flexible working hours</li><li>Work from home options</li><li>Exposure to multiple brands</li></ul>',
                'last_apply_date' => now()->addDays(35)->toDateString(),
                'sort_order' => 5,
            ],
            [
                'title' => 'DevOps Engineer (Intern)',
                'location' => 'Dubai, UAE',
                'job_type' => 'internship',
                'department' => 'Engineering',
                'about_role' => '<p>Great opportunity for aspiring DevOps engineers to gain hands-on experience in a fast-paced environment. You will learn to manage cloud infrastructure, CI/CD pipelines, and monitoring systems for a multi-domain platform.</p>',
                'responsibilities' => '<ul><li>Assist in managing AWS/Digital Ocean infrastructure</li><li>Help set up and maintain CI/CD pipelines</li><li>Monitor server performance and respond to alerts</li><li>Automate deployment and configuration tasks</li><li>Document infrastructure and operational procedures</li></ul>',
                'requirements' => '<ul><li>Currently pursuing or recently completed CS/IT degree</li><li>Basic knowledge of Linux, Docker, and Git</li><li>Familiarity with cloud platforms (AWS, GCP, or Digital Ocean)</li><li>Willingness to learn and take ownership</li><li>Good communication skills</li></ul>',
                'what_we_offer' => '<ul><li>Monthly stipend: AED 3,000 - 4,000</li><li>Mentorship from senior engineers</li><li>Real-world production experience</li><li>Potential full-time offer after internship</li><li>Certificate of completion</li></ul>',
                'last_apply_date' => now()->addDays(40)->toDateString(),
                'sort_order' => 6,
            ],
        ];

        $careers = [];

        foreach ($careersData as $data) {
            $career = Career::create(array_merge($data, [
                'is_active' => true,
            ]));

            // Assign to domains — all careers go to primary domain, some to others
            $career->domains()->attach($domains[0]->id);

            // Engineering/Design roles go to all domains
            if (in_array($data['department'], ['Engineering', 'Design'])) {
                $career->domains()->attach($domains[1]->id);
                $career->domains()->attach($domains[2]->id);
            }
            // Marketing and Content go to domain 2 as well
            elseif (in_array($data['department'], ['Marketing', 'Content'])) {
                $career->domains()->attach($domains[1]->id);
            }

            $careers[] = $career;
        }

        return $careers;
    }

    // ─── 12. Career Applications ──────────────────────────────────────

    private function seedCareerApplications(array $careers, array $domains): void
    {
        $this->command->info('  Creating career applications...');

        $applicants = [
            [
                'name' => 'Muhammad Asif Khan',
                'email' => 'asif.khan@gmail.com',
                'phone' => '+971501234567',
                'cover_letter' => 'I am a passionate Laravel developer with 6 years of experience building scalable web applications. Currently working at a fintech startup in Dubai, I am looking for an opportunity to work on a larger platform. My expertise includes Laravel, MySQL optimization, Redis caching, and API development. I have contributed to open-source Laravel packages and enjoy mentoring junior developers.',
                'status' => 'new',
            ],
            [
                'name' => 'Sarah Thompson',
                'email' => 'sarah.thompson@outlook.com',
                'phone' => '+447911234567',
                'cover_letter' => 'Having worked as a full-stack developer for 4 years in London, I recently relocated to Dubai and am excited about the Senior Laravel Developer position. I bring experience with high-traffic e-commerce platforms, payment gateway integrations, and microservices architecture.',
                'status' => 'reviewed',
            ],
            [
                'name' => 'Ahmed Al Rashidi',
                'email' => 'ahmed.rashidi@hotmail.com',
                'phone' => '+971551234567',
                'cover_letter' => null,
                'status' => 'shortlisted',
            ],
            [
                'name' => 'Priya Nair',
                'email' => 'priya.nair@gmail.com',
                'phone' => '+919876543210',
                'cover_letter' => 'I am a React developer with 4 years of experience, currently based in Bangalore. I specialize in building responsive web applications with React, TypeScript, and Tailwind CSS. I have worked on hotel booking platforms before and understand the domain well.',
                'status' => 'new',
            ],
            [
                'name' => 'Omar Farouk',
                'email' => 'omar.farouk@gmail.com',
                'phone' => '+971509876543',
                'cover_letter' => 'As a React/Next.js developer with 3 years of experience, I am thrilled about the opportunity to build booking experiences at scale. My current role involves building multi-tenant SaaS platforms, which is directly relevant to your multi-domain architecture.',
                'status' => 'new',
            ],
            [
                'name' => 'Jessica Martinez',
                'email' => 'jessica.m@yahoo.com',
                'phone' => '+12025551234',
                'cover_letter' => 'I have 7 years of digital marketing experience, with the last 3 years focused on the hospitality sector in the Middle East. I managed campaigns for Rotana Hotels and currently oversee a monthly ad budget of AED 200K.',
                'status' => 'reviewed',
            ],
            [
                'name' => 'Fatima Al Suwaidi',
                'email' => 'fatima.suwaidi@gmail.com',
                'phone' => '+971561234567',
                'cover_letter' => 'As a UAE national with 3 years of customer service experience at Jumeirah Group, I bring deep knowledge of Dubai hospitality standards and fluency in Arabic, English, and Hindi.',
                'status' => 'shortlisted',
            ],
            [
                'name' => 'Raj Sharma',
                'email' => 'raj.sharma@gmail.com',
                'phone' => '+919812345678',
                'cover_letter' => 'Customer support professional with 4 years experience in the travel industry. Currently handling premium client support at MakeMyTrip. Fluent in English and Hindi.',
                'status' => 'new',
            ],
            [
                'name' => 'Elena Volkov',
                'email' => 'elena.volkov@gmail.com',
                'phone' => '+79161234567',
                'cover_letter' => 'Senior UI/UX designer with 5 years of experience creating beautiful digital products. My portfolio includes work for Booking.com, Wego, and several Dubai-based startups. I am passionate about creating intuitive booking flows that convert.',
                'status' => 'shortlisted',
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@outlook.com',
                'phone' => '+61412345678',
                'cover_letter' => null,
                'status' => 'rejected',
            ],
            [
                'name' => 'Aisha Malik',
                'email' => 'aisha.malik@gmail.com',
                'phone' => '+923001234567',
                'cover_letter' => 'I am a content writer specializing in travel and hospitality. I have written for Lonely Planet, TripAdvisor, and several Dubai lifestyle blogs. I can write SEO-optimized content that engages readers and drives organic traffic.',
                'status' => 'new',
            ],
            [
                'name' => 'Ali Hassan',
                'email' => 'ali.hassan@gmail.com',
                'phone' => '+971507654321',
                'cover_letter' => 'Final-year Computer Science student at AUS with a passion for DevOps. I have completed AWS Solutions Architect certification and have hands-on experience with Docker and Kubernetes through personal projects and hackathons.',
                'status' => 'new',
            ],
        ];

        // Map applicants to specific careers
        $careerMapping = [
            0 => [0, 1, 2],       // Senior Laravel Developer: 3 applicants
            1 => [3, 4],          // React Frontend Developer: 2 applicants
            2 => [5],             // Digital Marketing Manager: 1 applicant
            3 => [6, 7],          // Customer Support Executive: 2 applicants
            4 => [8, 9],          // UI/UX Designer: 2 applicants
            5 => [10],            // Content Writer: 1 applicant
            6 => [11],            // DevOps Intern: 1 applicant
        ];

        foreach ($careerMapping as $careerIndex => $applicantIndexes) {
            if (! isset($careers[$careerIndex])) {
                continue;
            }

            $career = $careers[$careerIndex];
            // Pick the first domain the career belongs to
            $domainId = $career->domains()->first()?->id ?? $domains[0]->id;

            foreach ($applicantIndexes as $appIndex) {
                $applicant = $applicants[$appIndex];

                CareerApplication::create([
                    'career_id' => $career->id,
                    'domain_id' => $domainId,
                    'name' => $applicant['name'],
                    'email' => $applicant['email'],
                    'phone' => $applicant['phone'],
                    'cover_letter' => $applicant['cover_letter'],
                    'resume_path' => 'career-applications/resume-sample-'.($appIndex + 1).'.pdf',
                    'status' => $applicant['status'],
                    'created_at' => now()->subDays(rand(1, 14))->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(0, 7)),
                ]);
            }
        }
    }
}
