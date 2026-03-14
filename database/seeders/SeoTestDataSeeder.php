<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Booking;
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

class SeoTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding SEO test data (extra domains + full data)...');

        $this->ensureStorageDirectories();

        $domains = $this->seedDomains();
        $locations = $this->seedLocations($domains);
        $hotels = $this->seedHotels($domains, $locations);
        $this->seedHotelImages($hotels);
        $roomTypes = $this->seedRoomTypes($hotels);
        $this->seedRoomAvailability($roomTypes);
        $bookings = $this->seedBookings($domains, $hotels, $roomTypes);
        $this->seedReviews($hotels, $bookings);
        $this->seedPricingRules($domains, $locations);
        $this->seedAnalyticsWithSeo($domains);
        $this->backfillExistingSeoAnalytics();

        $this->command->info('SEO test data seeded successfully!');
    }

    private function ensureStorageDirectories(): void
    {
        Storage::disk('public')->makeDirectory('hotels');
        Storage::disk('public')->makeDirectory('locations');
    }

    private function generatePlaceholderImage(string $path, int $w, int $h, string $label): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (! extension_loaded('gd')) {
            file_put_contents($path, base64_decode(
                '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP'.str_repeat('A', 50).'//Z'
            ));

            return;
        }

        $img = imagecreatetruecolor($w, $h);
        $hash = crc32($label);
        $bg = imagecolorallocate($img, abs($hash) % 80 + 100, abs($hash >> 8) % 80 + 100, abs($hash >> 16) % 80 + 100);
        imagefill($img, 0, 0, $bg);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        $text = strtoupper(substr($label, 0, 20));
        imagestring($img, 5, ($w - imagefontwidth(5) * strlen($text)) / 2, ($h - imagefontheight(5)) / 2, $text, $textColor);
        imagejpeg($img, $path, 85);
        imagedestroy($img);
    }

    private function makePlaceholder(string $storagePath, int $w = 800, int $h = 600): string
    {
        $fullPath = Storage::disk('public')->path($storagePath);
        $this->generatePlaceholderImage($fullPath, $w, $h, basename(dirname($storagePath)));

        return $storagePath;
    }

    // ─── 1. Domains ─────────────────────────────────────────────────

    private function seedDomains(): array
    {
        $this->command->info('  Creating extra domains...');

        $domainsData = [
            [
                'name' => 'Dubai Marina Hotels',
                'domain' => 'dubaimarinahotels.com',
                'slug' => 'dubai-marina-hotels',
                'default_currency' => 'AED',
                'meta_title' => 'Dubai Marina Hotels - Waterfront Stays in Dubai Marina',
                'meta_description' => 'Premium waterfront hotels in Dubai Marina with stunning marina and sea views. Book luxury apartments and suites.',
                'meta_keywords' => 'dubai marina hotels, marina apartments, waterfront hotels dubai, marina view hotels',
                'google_analytics_id' => 'G-TESTDMH004',
            ],
            [
                'name' => 'Abu Dhabi Stays',
                'domain' => 'abudhabistays.com',
                'slug' => 'abu-dhabi-stays',
                'default_currency' => 'AED',
                'meta_title' => 'Abu Dhabi Stays - Luxury Hotels & Apartments in Abu Dhabi',
                'meta_description' => 'Discover premium hotels and serviced apartments across Abu Dhabi. Corniche, Yas Island, Saadiyat and more.',
                'meta_keywords' => 'abu dhabi hotels, abu dhabi apartments, yas island hotels, saadiyat hotels',
                'google_analytics_id' => 'G-TESTADS005',
            ],
            [
                'name' => 'UAE Budget Rooms',
                'domain' => 'uaebudgetrooms.com',
                'slug' => 'uae-budget-rooms',
                'default_currency' => 'USD',
                'meta_title' => 'UAE Budget Rooms - Affordable Hotels in UAE',
                'meta_description' => 'Affordable hotel rooms and apartments across the UAE. Best value stays in Dubai, Abu Dhabi, and Sharjah.',
                'meta_keywords' => 'budget hotels uae, cheap hotels dubai, affordable apartments uae, budget rooms',
                'google_analytics_id' => 'G-TESTUBR006',
            ],
            [
                'name' => 'Sharjah Hotels Online',
                'domain' => 'sharjahhotelsonline.com',
                'slug' => 'sharjah-hotels-online',
                'default_currency' => 'AED',
                'meta_title' => 'Sharjah Hotels Online - Hotels & Apartments in Sharjah',
                'meta_description' => 'Book the best hotels and serviced apartments in Sharjah. Al Majaz, Al Khan, and University City areas.',
                'meta_keywords' => 'sharjah hotels, sharjah apartments, al majaz hotels, sharjah stays',
                'google_analytics_id' => 'G-TESTSHO007',
            ],
        ];

        $domains = [];
        foreach ($domainsData as $data) {
            $domains[] = Domain::create(array_merge($data, [
                'is_active' => true,
                'is_primary' => false,
                'default_language' => 'en',
                'sitemap_enabled' => true,
            ]));
        }

        return $domains;
    }

    // ─── 2. Locations ────────────────────────────────────────────────

    private function seedLocations(array $domains): array
    {
        $this->command->info('  Creating extra locations...');

        $locationsData = [
            [
                'name' => 'Al Barsha',
                'slug' => 'al-barsha',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Al Barsha is a bustling residential and commercial district in Dubai, known for its proximity to Mall of the Emirates and Ski Dubai. A great base for families and shoppers seeking value with convenience.',
                'short_description' => 'Central hub near Mall of the Emirates & Ski Dubai',
                'latitude' => 25.1130,
                'longitude' => 55.1990,
                'is_featured' => false,
            ],
            [
                'name' => 'Yas Island',
                'slug' => 'yas-island',
                'city' => 'Abu Dhabi',
                'country' => 'UAE',
                'description' => 'Yas Island is Abu Dhabi\'s premier entertainment destination, home to Ferrari World, Yas Waterworld, Warner Bros World, and the Yas Marina F1 Circuit. A world-class leisure island with top hotel options.',
                'short_description' => 'Abu Dhabi\'s entertainment island with theme parks & F1 circuit',
                'latitude' => 24.4887,
                'longitude' => 54.6024,
                'is_featured' => true,
            ],
            [
                'name' => 'Abu Dhabi Corniche',
                'slug' => 'abu-dhabi-corniche',
                'city' => 'Abu Dhabi',
                'country' => 'UAE',
                'description' => 'The Abu Dhabi Corniche stretches 8km along the waterfront, offering pristine public beaches, cycling paths, and stunning views of the Arabian Gulf. Home to some of Abu Dhabi\'s finest 5-star hotels and serviced apartments.',
                'short_description' => 'Iconic 8km waterfront promenade with luxury hotels',
                'latitude' => 24.4631,
                'longitude' => 54.3219,
                'is_featured' => true,
            ],
            [
                'name' => 'Al Majaz Sharjah',
                'slug' => 'al-majaz-sharjah',
                'city' => 'Sharjah',
                'country' => 'UAE',
                'description' => 'Al Majaz is a vibrant waterfront area in the heart of Sharjah, centred around the famous Al Majaz Waterfront with its musical fountain. It offers a blend of leisure, dining, and cultural attractions at an affordable price.',
                'short_description' => 'Sharjah\'s waterfront gem with musical fountain',
                'latitude' => 25.3390,
                'longitude' => 55.3950,
                'is_featured' => false,
            ],
            [
                'name' => 'DIFC',
                'slug' => 'difc',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'The Dubai International Financial Centre is the Middle East\'s leading financial hub. Beyond business, DIFC houses the Gate Village art district, high-end restaurants, and the DIFC Innovation Hub. Premium serviced apartments cater to business travellers.',
                'short_description' => 'Middle East financial hub with art galleries & fine dining',
                'latitude' => 25.2100,
                'longitude' => 55.2787,
                'is_featured' => false,
            ],
        ];

        $locations = [];
        $existingCount = Location::count();

        foreach ($locationsData as $i => $data) {
            $imagePath = $this->makePlaceholder("locations/{$data['slug']}.jpg", 1200, 800);

            $location = Location::create(array_merge($data, [
                'image_path' => $imagePath,
                'meta_title' => "Hotels in {$data['name']} | Dubai Apartments",
                'meta_description' => $data['short_description'],
                'is_active' => true,
                'sort_order' => $existingCount + $i,
            ]));

            // Attach to new domains
            foreach ($domains as $domain) {
                $domain->locations()->attach($location->id, [
                    'is_active' => true,
                    'sort_order' => $existingCount + $i,
                ]);
            }

            $locations[] = $location;
        }

        return $locations;
    }

    // ─── 3. Hotels ───────────────────────────────────────────────────

    private function seedHotels(array $domains, array $locations): array
    {
        $this->command->info('  Creating extra hotels...');

        $hotelsData = [
            // Al Barsha
            [
                'name' => 'Barsha Heights Serviced Apartments',
                'location_index' => 0,
                'star_rating' => 3,
                'description' => "Barsha Heights Serviced Apartments offers comfortable, no-fuss accommodation in one of Dubai's most convenient neighbourhoods. Walking distance from Mall of the Emirates and two Metro stations, this is a budget-friendly pick.\n\nEach apartment includes a kitchenette, dedicated workspace, and high-speed WiFi. Building facilities include a rooftop pool with Burj Al Arab views, a compact gym, and a 24-hour mini mart.",
                'short_description' => 'Budget-friendly apartments near Mall of the Emirates',
                'address' => 'Barsha Heights (TECOM), Al Barsha, Dubai',
                'latitude' => 25.1005,
                'longitude' => 55.1780,
                'phone' => '+971 4 555 0101',
                'email' => 'info@barshaheights.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
            // Yas Island
            [
                'name' => 'Yas Bay Waterfront Residences',
                'location_index' => 1,
                'star_rating' => 5,
                'description' => "Yas Bay Waterfront Residences is a stunning five-star property right on Yas Bay, Abu Dhabi's newest waterfront dining and entertainment destination. With direct views of Etihad Arena and the bay, it's the ultimate entertainment getaway.\n\nResidences feature Italian marble, Gaggenau appliances, and wraparound balconies. Guests enjoy an infinity pool overlooking the bay, a beach club, a state-of-the-art fitness centre, and complimentary theme park shuttle service.",
                'short_description' => 'Five-star waterfront living on Yas Bay with theme park access',
                'address' => 'Yas Bay, Yas Island, Abu Dhabi',
                'latitude' => 24.4650,
                'longitude' => 54.5980,
                'phone' => '+971 2 555 0201',
                'email' => 'reservations@yasbayresidences.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Yas Marina View Hotel',
                'location_index' => 1,
                'star_rating' => 4,
                'description' => "Overlooking the iconic Yas Marina Circuit, this hotel offers motorsport fans a dream stay. Watch F1 cars from your balcony during race season, or enjoy the marina views year-round.\n\nRooms are modern and spacious, with floor-to-ceiling windows. The hotel features a rooftop infinity pool, three restaurants, and exclusive access to Yas Links golf course shuttle.",
                'short_description' => 'F1 circuit views and marina lifestyle on Yas Island',
                'address' => 'Yas Marina, Yas Island, Abu Dhabi',
                'latitude' => 24.4710,
                'longitude' => 54.6110,
                'phone' => '+971 2 555 0202',
                'email' => 'info@yasmarina-hotel.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '11:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
            ],
            // Corniche
            [
                'name' => 'Corniche Towers Luxury Suites',
                'location_index' => 2,
                'star_rating' => 5,
                'description' => "Corniche Towers Luxury Suites stands as one of Abu Dhabi's finest addresses, commanding panoramic views of the Arabian Gulf from every suite. Located on the prestigious Corniche Road, a short drive from the Louvre Abu Dhabi.\n\nSuites feature Hermès amenities, Nespresso machines, and Bang & Olufsen entertainment systems. The building offers a private beach, two temperature-controlled pools, a hammam spa, and a Michelin-starred restaurant.",
                'short_description' => 'Ultra-luxury Corniche suites with private beach access',
                'address' => 'Corniche Road West, Abu Dhabi',
                'latitude' => 24.4585,
                'longitude' => 54.3175,
                'phone' => '+971 2 555 0301',
                'email' => 'concierge@cornichetowers.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => true,
                'is_family_friendly' => false,
                'is_featured' => true,
            ],
            // Al Majaz Sharjah
            [
                'name' => 'Al Majaz Premiere Hotel Apartments',
                'location_index' => 3,
                'star_rating' => 3,
                'description' => "Al Majaz Premiere Hotel Apartments offers exceptional value in the cultural heart of Sharjah. Overlooking the stunning Al Majaz Waterfront and its famous musical fountain, the hotel combines affordability with a prime location.\n\nApartments are spacious and well-maintained, each with a full kitchen and laundry facilities. Guests enjoy a pool, children's play area, and easy access to Sharjah's museums and souks.",
                'short_description' => 'Affordable waterfront apartments in the heart of Sharjah',
                'address' => 'Al Majaz 3, Sharjah',
                'latitude' => 25.3380,
                'longitude' => 55.3920,
                'phone' => '+971 6 555 0401',
                'email' => 'stay@almajazpremiere.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Sharjah Creek View Suites',
                'location_index' => 3,
                'star_rating' => 4,
                'description' => "Sharjah Creek View Suites blends modern comfort with Sharjah's rich cultural heritage. Situated along the beautiful Sharjah Creek, these suites offer tranquil water views and easy access to the Art and Heritage areas.\n\nEach suite features Arabic-inspired decor, a fully equipped kitchen, and a spacious balcony. The hotel boasts an outdoor pool, a traditional Arabic coffee lounge, and a curated art gallery in the lobby.",
                'short_description' => 'Cultural elegance with creek views in Sharjah',
                'address' => 'Al Khan Corniche, Sharjah',
                'latitude' => 25.3260,
                'longitude' => 55.3810,
                'phone' => '+971 6 555 0402',
                'email' => 'info@sharjahcreekview.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
            ],
            // DIFC
            [
                'name' => 'Gate District Executive Residences',
                'location_index' => 4,
                'star_rating' => 5,
                'description' => "Gate District Executive Residences offers the finest business-class accommodation in the heart of DIFC. Ideal for corporate travellers and finance professionals, every detail is designed for productivity and comfort.\n\nResidences feature ergonomic home offices, dual monitors on request, Lavazza coffee systems, and rain showers. The building offers a rooftop lap pool, a members-only cigar lounge, and direct skybridge access to Gate Village art galleries and restaurants.",
                'short_description' => 'Five-star business residences in the heart of DIFC',
                'address' => 'Gate District, DIFC, Dubai',
                'latitude' => 25.2115,
                'longitude' => 55.2805,
                'phone' => '+971 4 555 0501',
                'email' => 'executive@gatedistrict.ae',
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
            ],
        ];

        $amenityIds = Amenity::pluck('id')->toArray();
        $hotels = [];
        $existingSort = Hotel::max('sort_order') ?? 0;

        foreach ($hotelsData as $i => $data) {
            $locationIndex = $data['location_index'];
            unset($data['location_index']);

            $hotel = Hotel::create(array_merge($data, [
                'slug' => Str::slug($data['name']),
                'location_id' => $locations[$locationIndex]->id,
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in. Late cancellations charged one night.',
                'is_active' => true,
                'meta_title' => $data['name'].' | Dubai Apartments',
                'meta_description' => $data['short_description'],
                'sort_order' => $existingSort + $i + 1,
            ]));

            // Attach 8-12 random amenities
            $selected = collect($amenityIds)->shuffle()->take(rand(8, 12))->toArray();
            $hotel->amenities()->attach($selected);

            // Attach to all new domains
            foreach ($domains as $domain) {
                $domain->hotels()->attach($hotel->id, ['is_active' => true, 'sort_order' => $existingSort + $i + 1]);
            }

            $hotels[] = $hotel;
        }

        return $hotels;
    }

    // ─── 4. Hotel Images ─────────────────────────────────────────────

    private function seedHotelImages(array $hotels): void
    {
        $this->command->info('  Creating hotel images (placeholders)...');

        $categories = ['exterior', 'lobby', 'rooms', 'rooms', 'pool', 'restaurant', 'general'];

        foreach ($hotels as $hotel) {
            Storage::disk('public')->makeDirectory("hotels/{$hotel->id}");

            foreach ($categories as $j => $category) {
                $filename = "{$category}-".($j + 1).'.jpg';
                $storagePath = "hotels/{$hotel->id}/{$filename}";
                $imagePath = $this->makePlaceholder($storagePath);

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
            ['name' => 'Studio Apartment', 'max_guests' => 2, 'max_adults' => 2, 'max_children' => 0, 'bed_type' => 'King', 'room_size_sqm' => 35, 'base_range' => [350, 600], 'total_rooms' => 10],
            ['name' => 'One-Bedroom Apartment', 'max_guests' => 3, 'max_adults' => 2, 'max_children' => 1, 'bed_type' => 'King', 'room_size_sqm' => 55, 'base_range' => [500, 900], 'total_rooms' => 8],
            ['name' => 'Two-Bedroom Apartment', 'max_guests' => 5, 'max_adults' => 4, 'max_children' => 2, 'bed_type' => 'King + Twin', 'room_size_sqm' => 90, 'base_range' => [800, 1500], 'total_rooms' => 5],
            ['name' => 'Penthouse Suite', 'max_guests' => 6, 'max_adults' => 6, 'max_children' => 2, 'bed_type' => 'King + Queen + Twin', 'room_size_sqm' => 180, 'base_range' => [2000, 5000], 'total_rooms' => 2],
        ];

        $roomAmenityIds = Amenity::whereIn('category', ['Room', 'General'])->pluck('id')->toArray();
        $allRoomTypes = [];

        foreach ($hotels as $hotel) {
            $count = $hotel->star_rating >= 4 ? rand(3, 4) : rand(2, 3);
            $templates = array_slice($roomTemplates, 0, $count);

            foreach ($templates as $j => $tpl) {
                $multiplier = match ($hotel->star_rating) {
                    5 => 1.6, 4 => 1.0, default => 0.7,
                };
                $basePrice = round(rand($tpl['base_range'][0], $tpl['base_range'][1]) * $multiplier, 2);

                $roomType = RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => $tpl['name'],
                    'slug' => Str::slug($tpl['name']),
                    'description' => "A comfortable {$tpl['name']} at {$hotel->name}. Fully furnished with modern amenities, perfect for up to {$tpl['max_guests']} guests.",
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

                $selectedAmenities = collect($roomAmenityIds)->shuffle()->take(rand(3, 5))->toArray();
                $roomType->amenities()->attach($selectedAmenities);
                $allRoomTypes[] = $roomType;
            }

            $hotel->update(['min_price' => $hotel->roomTypes()->min('base_price') ?? 0]);
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
                $bookedRooms = rand(0, (int) ceil($roomType->total_rooms * 0.6));
                $priceOverride = $date->isWeekend() ? round($roomType->base_price * 1.2, 2) : null;

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

        foreach (array_chunk($records, 200) as $chunk) {
            DB::table('room_availability')->insert($chunk);
        }
    }

    // ─── 7. Bookings & Payments ──────────────────────────────────────

    private function seedBookings(array $domains, array $hotels, array $roomTypes): array
    {
        $this->command->info('  Creating bookings & payments...');

        $guests = [
            ['first' => 'Khalid', 'last' => 'Al Nahyan', 'email' => 'khalid.n@email.com', 'phone' => '+971509876543', 'nationality' => 'UAE'],
            ['first' => 'Emma', 'last' => 'Thompson', 'email' => 'emma.t@email.com', 'phone' => '+447856123456', 'nationality' => 'UK'],
            ['first' => 'Arjun', 'last' => 'Mehta', 'email' => 'arjun.m@email.com', 'phone' => '+919845671234', 'nationality' => 'India'],
            ['first' => 'Laura', 'last' => 'Garcia', 'email' => 'laura.g@email.com', 'phone' => '+34612345678', 'nationality' => 'Spain'],
            ['first' => 'Hassan', 'last' => 'Jaber', 'email' => 'hassan.j@email.com', 'phone' => '+966555123456', 'nationality' => 'Saudi Arabia'],
            ['first' => 'Chen', 'last' => 'Mei Ling', 'email' => 'chen.ml@email.com', 'phone' => '+8613887654321', 'nationality' => 'China'],
            ['first' => 'Oliver', 'last' => 'Mueller', 'email' => 'oliver.m@email.com', 'phone' => '+4915112345678', 'nationality' => 'Germany'],
            ['first' => 'Nadia', 'last' => 'Benali', 'email' => 'nadia.b@email.com', 'phone' => '+212612345678', 'nationality' => 'Morocco'],
            ['first' => 'David', 'last' => 'Kim', 'email' => 'david.k@email.com', 'phone' => '+821012345678', 'nationality' => 'South Korea'],
            ['first' => 'Fatimah', 'last' => 'Al Rashid', 'email' => 'fatimah.r@email.com', 'phone' => '+971561234567', 'nationality' => 'UAE'],
        ];

        $statuses = ['confirmed', 'confirmed', 'paid', 'paid', 'paid', 'pending', 'cancelled', 'refunded'];
        $bookings = [];

        for ($i = 0; $i < 20; $i++) {
            $guest = $guests[$i % count($guests)];
            $hotel = $hotels[array_rand($hotels)];
            $hotelRoomTypes = array_filter($roomTypes, fn ($rt) => $rt->hotel_id === $hotel->id);
            $roomType = $hotelRoomTypes[array_rand($hotelRoomTypes)];
            $domain = $domains[array_rand($domains)];

            $checkIn = Carbon::today()->subDays(rand(0, 45))->addDays(rand(0, 10));
            $numNights = rand(1, 7);
            $checkOut = $checkIn->copy()->addDays($numNights);
            $numAdults = rand(1, $roomType->max_adults);
            $numChildren = rand(0, $roomType->max_children);

            $pricePerNight = $roomType->base_price;
            $subtotal = round($pricePerNight * $numNights, 2);
            $taxAmount = round($subtotal * 0.05, 2);
            $tourismFee = round(15.0 * $numNights, 2);
            $serviceCharge = round($subtotal * 0.10, 2);
            $totalAmount = round($subtotal + $taxAmount + $tourismFee + $serviceCharge, 2);

            $status = $statuses[array_rand($statuses)];
            $bookedAt = $checkIn->copy()->subDays(rand(3, 25));

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
                'room_price_per_night' => $pricePerNight,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_percentage' => 5.0,
                'tourism_fee' => $tourismFee,
                'service_charge' => $serviceCharge,
                'total_amount' => $totalAmount,
                'currency' => 'AED',
                'status' => $status,
                'cancellation_reason' => $status === 'cancelled' ? 'Change of plans.' : null,
                'cancelled_at' => $status === 'cancelled' ? $bookedAt->copy()->addDay() : null,
                'confirmed_at' => in_array($status, ['confirmed', 'paid']) ? $bookedAt : null,
                'ip_address' => '10.0.1.'.rand(1, 254),
                'booked_at' => $bookedAt,
            ]);

            if (in_array($status, ['confirmed', 'paid', 'refunded'])) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
                    'payment_method' => collect(['visa', 'mastercard', 'amex'])->random(),
                    'gateway' => 'mashreq',
                    'amount' => $totalAmount,
                    'currency' => 'AED',
                    'status' => $status === 'refunded' ? 'refunded' : 'completed',
                    'gateway_response' => ['response_code' => '00', 'response_message' => 'Approved', 'auth_code' => strtoupper(Str::random(6))],
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

        $templates = [
            ['rating' => 5, 'title' => 'World-class experience!', 'comment' => 'Everything about this property screamed luxury. The staff anticipated our every need, the room was immaculate, and the views were out of this world.'],
            ['rating' => 5, 'title' => 'Cannot fault a thing', 'comment' => 'From the welcome drink to the farewell, everything was seamless. The pool area is gorgeous and the rooms are huge. Already planning our next visit.'],
            ['rating' => 4, 'title' => 'Excellent value', 'comment' => 'Great bang for your buck. Spacious rooms, helpful staff, and a brilliant location. The breakfast could have more variety, but that is a minor gripe.'],
            ['rating' => 4, 'title' => 'Very impressed', 'comment' => 'Modern, clean, and well-maintained. The kitchen was fully stocked and the bed was incredibly comfortable. Would definitely recommend for families.'],
            ['rating' => 5, 'title' => 'Pure bliss', 'comment' => 'Stayed in the penthouse and it was magical. Private jacuzzi, panoramic views, and the most comfortable bed ever. Worth every penny.'],
            ['rating' => 3, 'title' => 'Room for improvement', 'comment' => 'Location is great and the apartment was clean but the furniture feels tired. The gym equipment needs updating and the pool was too cold.'],
            ['rating' => 4, 'title' => 'Business trip perfection', 'comment' => 'High-speed WiFi, quiet rooms, and the desk setup was actually usable. Close to DIFC and the lobby café has great coffee. Will rebook.'],
            ['rating' => 5, 'title' => 'Hidden gem of Sharjah', 'comment' => 'So much cheaper than Dubai but the quality is just as good. The waterfront views from our apartment were beautiful. Highly recommended for budget travellers.'],
            ['rating' => 4, 'title' => 'Great for kids', 'comment' => 'Kids loved the pool and the proximity to the waterpark. Apartment was large enough for the whole family. Staff were patient and friendly with the children.'],
            ['rating' => 3, 'title' => 'Good but noisy', 'comment' => 'The apartment itself was great, well-furnished and clean. However, road noise was an issue at night. Ask for a higher floor if possible.'],
            ['rating' => 5, 'title' => 'F1 weekend was incredible', 'comment' => 'Booked for the F1 weekend and could see the cars from our balcony! The hotel organised a viewing party which was fantastic. Unforgettable experience.'],
            ['rating' => 4, 'title' => 'Solid 4-star stay', 'comment' => 'Nothing flashy but everything works perfectly. Clean, modern, well-located, and the staff are genuinely helpful. No complaints.'],
        ];

        $paidBookings = array_values(array_filter($bookings, fn ($b) => in_array($b->status, ['confirmed', 'paid'])));

        foreach ($templates as $i => $tpl) {
            $hotel = $hotels[array_rand($hotels)];
            $bookingId = null;
            $isVerified = false;

            if ($i < count($paidBookings) && rand(0, 1)) {
                $booking = $paidBookings[$i % count($paidBookings)];
                $bookingId = $booking->id;
                $hotel = Hotel::find($booking->hotel_id) ?? $hotel;
                $isVerified = true;
            }

            Review::create([
                'hotel_id' => $hotel->id,
                'booking_id' => $bookingId,
                'guest_name' => fake()->name(),
                'guest_email' => fake()->safeEmail(),
                'rating' => $tpl['rating'],
                'title' => $tpl['title'],
                'comment' => $tpl['comment'],
                'is_verified' => $isVerified,
                'is_approved' => $i < 10,
                'admin_reply' => $i < 3 ? 'Thank you for your kind words! We look forward to hosting you again.' : null,
                'replied_at' => $i < 3 ? now()->subDays(rand(1, 7)) : null,
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

    private function seedPricingRules(array $domains, array $locations): void
    {
        $this->command->info('  Creating pricing rules...');

        PricingRule::create([
            'name' => 'Dubai Marina Hotels Premium',
            'type' => 'domain_markup',
            'domain_id' => $domains[0]->id,
            'adjustment_type' => 'percentage',
            'adjustment_value' => 8.00,
            'priority' => 10,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'UAE Budget Rooms Discount',
            'type' => 'domain_markup',
            'domain_id' => $domains[2]->id,
            'adjustment_type' => 'percentage',
            'adjustment_value' => -12.00,
            'priority' => 10,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'Yas Island Event Premium',
            'type' => 'seasonal',
            'location_id' => $locations[1]->id,
            'adjustment_type' => 'fixed_amount',
            'adjustment_value' => 150.00,
            'start_date' => Carbon::now()->subMonth()->toDateString(),
            'end_date' => Carbon::now()->addMonths(3)->toDateString(),
            'priority' => 15,
            'is_active' => true,
        ]);

        PricingRule::create([
            'name' => 'Sharjah Summer Special',
            'type' => 'seasonal',
            'adjustment_type' => 'percentage',
            'adjustment_value' => -20.00,
            'start_date' => Carbon::now()->year.'-06-01',
            'end_date' => Carbon::now()->year.'-09-15',
            'priority' => 20,
            'is_active' => true,
        ]);
    }

    // ─── 10. Analytics WITH SEO Data ─────────────────────────────────

    private function seedAnalyticsWithSeo(array $domains): void
    {
        $this->command->info('  Creating analytics with SEO data for new domains...');

        $keywordPool = [
            ['keyword' => 'dubai hotel apartments', 'base_clicks' => 45, 'base_impressions' => 1200, 'base_position' => 3.5],
            ['keyword' => 'luxury stay dubai', 'base_clicks' => 38, 'base_impressions' => 980, 'base_position' => 4.1],
            ['keyword' => 'dubai marina hotel', 'base_clicks' => 55, 'base_impressions' => 1400, 'base_position' => 2.8],
            ['keyword' => 'cheap hotels dubai', 'base_clicks' => 72, 'base_impressions' => 2200, 'base_position' => 5.2],
            ['keyword' => 'abu dhabi apartments', 'base_clicks' => 30, 'base_impressions' => 850, 'base_position' => 6.1],
            ['keyword' => 'yas island hotel', 'base_clicks' => 25, 'base_impressions' => 700, 'base_position' => 4.8],
            ['keyword' => 'sharjah hotel booking', 'base_clicks' => 18, 'base_impressions' => 600, 'base_position' => 7.3],
            ['keyword' => 'beachfront hotel dubai', 'base_clicks' => 42, 'base_impressions' => 1100, 'base_position' => 3.9],
            ['keyword' => 'serviced apartments uae', 'base_clicks' => 28, 'base_impressions' => 900, 'base_position' => 5.5],
            ['keyword' => 'best hotel deals dubai', 'base_clicks' => 60, 'base_impressions' => 1800, 'base_position' => 4.3],
            ['keyword' => 'palm jumeirah stay', 'base_clicks' => 35, 'base_impressions' => 1050, 'base_position' => 3.2],
            ['keyword' => 'downtown dubai apartment', 'base_clicks' => 48, 'base_impressions' => 1350, 'base_position' => 2.6],
            ['keyword' => 'family hotel dubai', 'base_clicks' => 33, 'base_impressions' => 950, 'base_position' => 5.8],
            ['keyword' => 'business hotel difc', 'base_clicks' => 22, 'base_impressions' => 550, 'base_position' => 6.5],
            ['keyword' => 'weekend getaway uae', 'base_clicks' => 40, 'base_impressions' => 1150, 'base_position' => 4.7],
        ];

        $landingPagePool = [
            ['page' => '/hotels/dubai-marina', 'base_views' => 320, 'base_bounce' => 38.5],
            ['page' => '/hotels/downtown-dubai', 'base_views' => 290, 'base_bounce' => 42.1],
            ['page' => '/hotels/palm-jumeirah', 'base_views' => 250, 'base_bounce' => 35.8],
            ['page' => '/hotels/yas-island', 'base_views' => 180, 'base_bounce' => 44.2],
            ['page' => '/hotels/abu-dhabi-corniche', 'base_views' => 160, 'base_bounce' => 40.6],
            ['page' => '/deals', 'base_views' => 210, 'base_bounce' => 32.4],
            ['page' => '/locations/jbr', 'base_views' => 195, 'base_bounce' => 37.9],
            ['page' => '/hotels/business-bay', 'base_views' => 170, 'base_bounce' => 45.3],
            ['page' => '/', 'base_views' => 450, 'base_bounce' => 48.2],
            ['page' => '/hotels/sharjah', 'base_views' => 120, 'base_bounce' => 41.7],
            ['page' => '/hotels/al-barsha', 'base_views' => 140, 'base_bounce' => 39.5],
            ['page' => '/locations/difc', 'base_views' => 130, 'base_bounce' => 43.8],
        ];

        foreach ($domains as $domainIndex => $domain) {
            for ($d = 29; $d >= 0; $d--) {
                $date = Carbon::today()->subDays($d);
                $isWeekend = $date->isWeekend();
                $baseViews = $isWeekend ? rand(200, 500) : rand(100, 300);

                // SEO metrics — vary by domain (some stronger than others)
                $seoStrength = match ($domainIndex) {
                    0 => 1.3,   // Dubai Marina Hotels — strong SEO
                    1 => 1.0,   // Abu Dhabi Stays — moderate
                    2 => 0.7,   // Budget Rooms — weaker
                    default => 0.5,
                };

                $organicTraffic = (int) round(rand(50, 200) * $seoStrength * ($isWeekend ? 1.2 : 1.0));
                $searchImpressions = (int) round(rand(800, 3000) * $seoStrength);
                $searchClicks = (int) round($searchImpressions * (rand(25, 65) / 1000)); // 2.5%–6.5% CTR
                $avgPosition = round(rand(20, 80) / 10 + (1 - $seoStrength) * 3, 2); // 2.0–11.0
                $bounceRate = round(rand(280, 550) / 10 + (1 - $seoStrength) * 5, 2); // 28%–60%
                $avgSessionDuration = rand(60, 300); // 1–5 minutes

                // Pick 5-8 random keywords for this day with some variance
                $dayKeywordCount = rand(5, 8);
                $dayKeywords = collect($keywordPool)
                    ->shuffle()
                    ->take($dayKeywordCount)
                    ->map(function ($kw) use ($seoStrength) {
                        $variance = rand(70, 130) / 100;

                        return [
                            'keyword' => $kw['keyword'],
                            'clicks' => (int) round($kw['base_clicks'] * $seoStrength * $variance / 5),
                            'impressions' => (int) round($kw['base_impressions'] * $seoStrength * $variance / 5),
                            'position' => round($kw['base_position'] + (1 - $seoStrength) * 2 + (rand(-10, 10) / 10), 1),
                        ];
                    })
                    ->values()
                    ->toArray();

                // Pick 4-6 random landing pages for this day
                $dayPageCount = rand(4, 6);
                $dayLandingPages = collect($landingPagePool)
                    ->shuffle()
                    ->take($dayPageCount)
                    ->map(function ($lp) use ($seoStrength) {
                        $variance = rand(70, 130) / 100;

                        return [
                            'page' => $lp['page'],
                            'views' => (int) round($lp['base_views'] * $seoStrength * $variance / 5),
                            'bounce_rate' => round($lp['base_bounce'] + (rand(-50, 50) / 10), 1),
                        ];
                    })
                    ->values()
                    ->toArray();

                DomainAnalytics::create([
                    'domain_id' => $domain->id,
                    'date' => $date->toDateString(),
                    'page_views' => $baseViews,
                    'unique_visitors' => (int) round($baseViews * 0.65),
                    'hotel_clicks' => (int) round($baseViews * rand(20, 40) / 100),
                    'booking_starts' => rand(2, 15),
                    'booking_completions' => rand(0, 6),
                    'revenue' => round(rand(500, 10000) + rand(0, 99) / 100, 2),
                    'top_hotels' => [
                        ['hotel_id' => rand(1, 20), 'clicks' => rand(10, 50)],
                        ['hotel_id' => rand(1, 20), 'clicks' => rand(5, 30)],
                        ['hotel_id' => rand(1, 20), 'clicks' => rand(1, 20)],
                    ],
                    'top_locations' => [
                        ['location_id' => rand(1, 11), 'clicks' => rand(10, 40)],
                        ['location_id' => rand(1, 11), 'clicks' => rand(5, 25)],
                    ],
                    'traffic_sources' => [
                        'direct' => rand(15, 35),
                        'google' => rand(35, 55),
                        'social' => rand(5, 20),
                        'referral' => rand(5, 15),
                    ],
                    // SEO Fields
                    'organic_traffic' => $organicTraffic,
                    'search_impressions' => $searchImpressions,
                    'search_clicks' => $searchClicks,
                    'avg_position' => $avgPosition,
                    'bounce_rate' => $bounceRate,
                    'avg_session_duration' => $avgSessionDuration,
                    'top_keywords' => $dayKeywords,
                    'top_landing_pages' => $dayLandingPages,
                ]);
            }
        }
    }

    // ─── 11. Backfill SEO data for existing domains ──────────────────

    private function backfillExistingSeoAnalytics(): void
    {
        $this->command->info('  Backfilling SEO data for existing analytics rows...');

        $keywordPool = [
            ['keyword' => 'dubai luxury hotel', 'base_clicks' => 50, 'base_impressions' => 1300, 'base_position' => 2.9],
            ['keyword' => 'hotel apartment dubai', 'base_clicks' => 65, 'base_impressions' => 1800, 'base_position' => 3.1],
            ['keyword' => 'beach hotel dubai', 'base_clicks' => 40, 'base_impressions' => 1100, 'base_position' => 4.5],
            ['keyword' => 'dubai holiday stay', 'base_clicks' => 35, 'base_impressions' => 950, 'base_position' => 5.0],
            ['keyword' => 'furnished apartment dubai', 'base_clicks' => 28, 'base_impressions' => 800, 'base_position' => 4.2],
            ['keyword' => 'dubai penthouse rental', 'base_clicks' => 15, 'base_impressions' => 450, 'base_position' => 6.8],
            ['keyword' => 'jbr hotel apartment', 'base_clicks' => 32, 'base_impressions' => 900, 'base_position' => 3.7],
            ['keyword' => 'downtown dubai hotel', 'base_clicks' => 55, 'base_impressions' => 1500, 'base_position' => 2.4],
            ['keyword' => 'marina view apartment', 'base_clicks' => 38, 'base_impressions' => 1000, 'base_position' => 3.3],
            ['keyword' => 'dubai booking online', 'base_clicks' => 70, 'base_impressions' => 2100, 'base_position' => 4.0],
        ];

        $landingPagePool = [
            ['page' => '/', 'base_views' => 500, 'base_bounce' => 46.0],
            ['page' => '/hotels/marina-terrace', 'base_views' => 280, 'base_bounce' => 35.2],
            ['page' => '/hotels/address-downtown', 'base_views' => 310, 'base_bounce' => 33.8],
            ['page' => '/hotels/palm-shoreline', 'base_views' => 220, 'base_bounce' => 38.4],
            ['page' => '/locations/downtown-dubai', 'base_views' => 260, 'base_bounce' => 40.1],
            ['page' => '/locations/dubai-marina', 'base_views' => 240, 'base_bounce' => 37.6],
            ['page' => '/hotels/damac-heights', 'base_views' => 190, 'base_bounce' => 36.9],
            ['page' => '/deals', 'base_views' => 350, 'base_bounce' => 30.5],
        ];

        // Get existing analytics that have no SEO data
        $existing = DomainAnalytics::where('organic_traffic', 0)
            ->where('search_impressions', 0)
            ->get();

        foreach ($existing as $record) {
            $seoStrength = match (true) {
                $record->domain_id <= 1 => 1.4,
                $record->domain_id <= 2 => 1.1,
                default => 0.8,
            };

            $isWeekend = Carbon::parse($record->date)->isWeekend();
            $organicTraffic = (int) round(rand(60, 220) * $seoStrength * ($isWeekend ? 1.2 : 1.0));
            $searchImpressions = (int) round(rand(900, 3500) * $seoStrength);
            $searchClicks = (int) round($searchImpressions * (rand(30, 70) / 1000));

            $dayKeywords = collect($keywordPool)->shuffle()->take(rand(5, 7))->map(function ($kw) use ($seoStrength) {
                $v = rand(70, 130) / 100;

                return [
                    'keyword' => $kw['keyword'],
                    'clicks' => (int) round($kw['base_clicks'] * $seoStrength * $v / 5),
                    'impressions' => (int) round($kw['base_impressions'] * $seoStrength * $v / 5),
                    'position' => round($kw['base_position'] + (1 - $seoStrength) * 2 + rand(-10, 10) / 10, 1),
                ];
            })->values()->toArray();

            $dayPages = collect($landingPagePool)->shuffle()->take(rand(4, 6))->map(function ($lp) use ($seoStrength) {
                $v = rand(70, 130) / 100;

                return [
                    'page' => $lp['page'],
                    'views' => (int) round($lp['base_views'] * $seoStrength * $v / 5),
                    'bounce_rate' => round($lp['base_bounce'] + rand(-50, 50) / 10, 1),
                ];
            })->values()->toArray();

            $record->update([
                'organic_traffic' => $organicTraffic,
                'search_impressions' => $searchImpressions,
                'search_clicks' => $searchClicks,
                'avg_position' => round(rand(20, 70) / 10 + (1 - $seoStrength) * 2, 2),
                'bounce_rate' => round(rand(300, 520) / 10, 2),
                'avg_session_duration' => rand(80, 280),
                'top_keywords' => $dayKeywords,
                'top_landing_pages' => $dayPages,
            ]);
        }

        $this->command->info("    Backfilled {$existing->count()} existing analytics rows with SEO data.");
    }
}
