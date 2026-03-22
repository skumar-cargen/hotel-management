<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Location;
use App\Models\Review;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlQusaisDubaiHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Al Qusais Dubai Hotels data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'al-qusais-dubai-hotels')->firstOrFail();

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

        $this->command->info('Al Qusais Dubai Hotels data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Al Qusais',
                'slug' => 'al-qusais',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A well-established residential and commercial district in eastern Dubai, known for affordable living, excellent Metro connectivity, and proximity to Dubai International Airport. Popular with families and business travellers seeking value.',
                'short_description' => 'Affordable district with Metro and airport proximity',
                'latitude' => 25.2700,
                'longitude' => 55.3700,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Qusais Hotels — Affordable Stays in Dubai',
                'meta_description' => 'Book affordable hotels in Al Qusais, Dubai. Well-connected neighbourhood near Dubai Airport with Metro access and great value accommodation.',
            ],
            [
                'name' => 'Al Nahda Dubai',
                'slug' => 'al-nahda-dubai',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A vibrant border district between Dubai and Sharjah, known for budget-friendly hotels, diverse restaurants, and excellent shopping. Al Nahda Pond Park is a popular green oasis in the area.',
                'short_description' => 'Budget-friendly district on Dubai-Sharjah border',
                'latitude' => 25.2935,
                'longitude' => 55.3720,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Nahda Dubai Hotels — Budget Hotels Near Sharjah Border',
                'meta_description' => 'Book budget-friendly hotels in Al Nahda, Dubai. Vibrant district with diverse dining, shopping, and easy access to both Dubai and Sharjah.',
            ],
            [
                'name' => 'Muhaisnah',
                'slug' => 'muhaisnah',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A diverse residential neighbourhood in eastern Dubai. Muhaisnah offers affordable accommodation, Madina Mall shopping, and easy access to Academic City and Silicon Oasis via Emirates Road.',
                'short_description' => 'Residential area near Madina Mall',
                'latitude' => 25.2685,
                'longitude' => 55.3995,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Muhaisnah Hotels — Affordable Stays Near Madina Mall',
                'meta_description' => 'Find affordable hotels in Muhaisnah, Dubai. Residential neighbourhood with Madina Mall shopping and easy Emirates Road access.',
            ],
            [
                'name' => 'Al Twar',
                'slug' => 'al-twar',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A residential district near Dubai International Airport, known for Al Twar Centre, local parks, and easy access to Deira and the city centre. Popular with budget-conscious travellers.',
                'short_description' => 'Residential area near Dubai Airport',
                'latitude' => 25.2580,
                'longitude' => 55.3540,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Twar Hotels — Budget Stays Near Dubai Airport',
                'meta_description' => 'Book budget hotels in Al Twar, Dubai. Residential area near Dubai Airport with easy access to Deira and city centre.',
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
            // ── Al Qusais (3 hotels) ──
            [
                'name' => 'Flora Inn Hotel Dubai Airport',
                'location' => 'al-qusais',
                'star_rating' => 3,
                'short_description' => 'Modern budget hotel near Dubai Airport with pool and free shuttle',
                'description' => 'Flora Inn Hotel offers 105 contemporary rooms just 10 minutes from Dubai International Airport. Features include a rooftop pool, 24-hour restaurant, free airport shuttle, and complimentary high-speed WiFi. Rooms are modern and well-maintained with city views. Al Qusais Metro station is a short walk away.',
                'address' => 'Al Qusais 1, Near Stadium Metro, Dubai',
                'latitude' => 25.2712,
                'longitude' => 55.3688,
                'phone' => '+971 4 602 7777',
                'email' => 'reservations@florainnhotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 17, 25, 27, 28],
            ],
            [
                'name' => 'Lemon Tree Hotel Dubai',
                'location' => 'al-qusais',
                'star_rating' => 3,
                'short_description' => 'Vibrant budget hotel with quirky design and excellent value in Al Qusais',
                'description' => 'Lemon Tree Hotel brings its signature quirky, vibrant style to Al Qusais. With 114 well-appointed rooms, a refreshing rooftop pool, Citrus Café all-day dining, and a fitness centre, it offers outstanding value. Located near Al Qusais Metro with easy access to the airport and city centre.',
                'address' => 'Al Qusais Industrial Area 1, Dubai',
                'latitude' => 25.2695,
                'longitude' => 55.3715,
                'phone' => '+971 4 212 8888',
                'email' => 'reservations@lemontreehotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '11:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 17, 25, 27, 28],
            ],
            [
                'name' => 'Ramada Hotel & Suites Al Qusais',
                'location' => 'al-qusais',
                'star_rating' => 3,
                'short_description' => 'Spacious suites and rooms with kitchenette options near Stadium Metro',
                'description' => 'Ramada Hotel & Suites offers 180 rooms and suites in the heart of Al Qusais. Many rooms feature kitchenettes for self-catering guests. Amenities include a swimming pool, gym, multi-cuisine restaurant, and meeting rooms. Stadium Metro station is walking distance, and the airport is 15 minutes away.',
                'address' => 'Near Stadium Metro Station, Al Qusais, Dubai',
                'latitude' => 25.2720,
                'longitude' => 55.3672,
                'phone' => '+971 4 236 7777',
                'email' => 'info@ramadaalqusais.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 17, 22, 23, 25, 27, 28],
            ],

            // ── Al Nahda Dubai (2 hotels) ──
            [
                'name' => 'Al Nahda Resort & Spa',
                'location' => 'al-nahda-dubai',
                'star_rating' => 4,
                'short_description' => 'Family-friendly resort with waterpark, spa, and lush gardens in Al Nahda',
                'description' => 'Al Nahda Resort & Spa is a hidden gem — a 4-star resort oasis in the heart of Al Nahda. Spread across lush gardens, it features 135 rooms and suites, a mini waterpark, full-service Dreamworks Spa, multiple restaurants, and a children\'s play area. Popular with families seeking resort amenities at city prices.',
                'address' => 'Al Nahda 1, Near Al Nahda Metro, Dubai',
                'latitude' => 25.2940,
                'longitude' => 55.3735,
                'phone' => '+971 4 607 8888',
                'email' => 'reservations@alnahdaresort.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 13, 14, 15, 22, 27, 28],
            ],
            [
                'name' => 'Sun & Sand Hotel Al Qusais',
                'location' => 'al-nahda-dubai',
                'star_rating' => 3,
                'short_description' => 'Clean and comfortable hotel with traditional Arabian hospitality in Al Nahda',
                'description' => 'Sun & Sand Hotel offers 92 comfortable rooms with warm Arabian hospitality. Features include an outdoor pool, Flavours restaurant serving international cuisine, and a small gym. The hotel is known for its friendly staff and excellent breakfast. Walking distance to Sahara Centre Sharjah and Al Nahda Metro.',
                'address' => 'Al Nahda 2, Near Sharjah Border, Dubai',
                'latitude' => 25.2950,
                'longitude' => 55.3745,
                'phone' => '+971 4 298 3444',
                'email' => 'stay@sunandsandhotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 17, 27, 28],
            ],

            // ── Al Twar (1 hotel) ──
            [
                'name' => 'Royal Grand Suite Hotel',
                'location' => 'al-twar',
                'star_rating' => 3,
                'short_description' => 'Budget-friendly hotel with spacious rooms near Dubai Airport and Al Twar Centre',
                'description' => 'Royal Grand Suite Hotel offers 78 well-maintained rooms and suites with free parking, airport transfers, and breakfast. Located in Al Twar near Dubai Airport, the hotel is ideal for short stays and early flights. Features include a small restaurant, gym, and 24-hour front desk.',
                'address' => 'Al Twar 3, Near Al Twar Centre, Dubai',
                'latitude' => 25.2575,
                'longitude' => 55.3555,
                'phone' => '+971 4 263 5555',
                'email' => 'info@royalgranddubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 17, 25, 27, 28],
            ],
        ];

        $hotels = [];

        foreach ($hotelData as $i => $data) {
            $location = $locations[$data['location']];
            $amenityIds = $data['amenities'] ?? [];
            unset($data['location'], $data['amenities']);

            $hotel = Hotel::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'location_id' => $location->id,
                    'is_active' => true,
                    'meta_title' => "{$data['name']} — Book Now | Al Qusais Dubai Hotels",
                    'meta_description' => $data['short_description'] . '. Book your stay at ' . $data['name'] . ' with Al Qusais Dubai Hotels.',
                    'cancellation_policy' => 'Free cancellation up to 48 hours before check-in. Late cancellations will be charged one night\'s stay.',
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

    // ─── Hotel Images (Real Unsplash) ──────────────────────────────────

    private function seedHotelImages(array $hotels): void
    {
        // Real high-quality Unsplash images — curated for each hotel context
        $imagesByHotel = [
            // Flora Inn Hotel Dubai Airport
            [
                ['url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Flora Inn Hotel modern exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern guest room'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => '24-hour hotel restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Clean hotel reception'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fitness room'],
            ],
            // Lemon Tree Hotel Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Lemon Tree Hotel vibrant exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Quirky designed room'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool area'],
                ['url' => 'https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Citrus Café dining'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Colorful hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Hotel fitness centre'],
            ],
            // Ramada Hotel & Suites Al Qusais
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Ramada Hotel building', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Comfortable suite bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Suite kitchenette area'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Hotel swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Multi-cuisine restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel lobby and reception'],
            ],
            // Al Nahda Resort & Spa
            [
                ['url' => 'https://images.unsplash.com/photo-1445991842772-097fea258e7b?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Al Nahda Resort garden view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Resort room with garden view'],
                ['url' => 'https://images.unsplash.com/photo-1519449556851-5720b33024e7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Mini waterpark and pool'],
                ['url' => 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Dreamworks Spa treatment'],
                ['url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Resort dining buffet'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Resort gym'],
            ],
            // Sun & Sand Hotel Al Qusais
            [
                ['url' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Sun & Sand Hotel exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Clean hotel room'],
                ['url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor pool area'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Flavours restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Welcoming hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1578645510447-e20b4311e3ce?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Pool area view'],
            ],
            // Royal Grand Suite Hotel
            [
                ['url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Royal Grand Suite building', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Budget-friendly room'],
                ['url' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Standard double room'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Hotel restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel reception'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Small gym'],
            ],
        ];

        foreach ($hotels as $hi => $hotel) {
            if ($hotel->images()->count() > 0) {
                $this->command->line("  Images for {$hotel->name} already exist, skipping");
                continue;
            }

            Storage::disk('public')->makeDirectory("hotels/{$hotel->id}");

            $images = $imagesByHotel[$hi] ?? [];
            foreach ($images as $ii => $img) {
                $filename = "{$img['cat']}-{$ii}.jpg";
                $thumbFilename = "{$img['cat']}-{$ii}-thumb.jpg";
                $storagePath = "hotels/{$hotel->id}/{$filename}";
                $thumbStoragePath = "hotels/{$hotel->id}/{$thumbFilename}";
                $fullPath = Storage::disk('public')->path($storagePath);
                $thumbFullPath = Storage::disk('public')->path($thumbStoragePath);

                $this->downloadHotelImage($fullPath, $img['url']);
                $this->downloadHotelImage($thumbFullPath, str_replace('w=1200', 'w=400', $img['url']));

                HotelImage::create([
                    'hotel_id' => $hotel->id,
                    'image_path' => $storagePath,
                    'thumbnail_path' => $thumbStoragePath,
                    'category' => $img['cat'],
                    'alt_text' => $img['alt'],
                    'is_primary' => $img['primary'] ?? false,
                    'sort_order' => $ii,
                ]);
            }

            $this->command->line("  Images: {$hotel->name} — " . count($images) . ' images');
        }
    }

    // ─── Room Types ────────────────────────────────────────────────────

    private function seedRoomTypes(array $hotels): void
    {
        $roomTemplates = [
            // 3-star value
            'smart-3star' => [
                ['name' => 'Rover Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 280, 'rooms' => 50],
                ['name' => 'Rover Room Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 280, 'rooms' => 40],
                ['name' => 'Connecting Rover Rooms', 'bed' => 'Queen + Twin', 'sqm' => 48, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 520, 'rooms' => 15],
            ],
            // 4-star boutique
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 450, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1400, 'rooms' => 4],
            ],
        ];

        // Map hotels to room templates
        $hotelTemplateMap = [
            0 => 'smart-3star',      // Flora Inn Hotel Dubai Airport
            1 => 'smart-3star',      // Lemon Tree Hotel Dubai
            2 => 'smart-3star',      // Ramada Hotel & Suites Al Qusais
            3 => 'boutique-4star',   // Al Nahda Resort & Spa
            4 => 'smart-3star',      // Sun & Sand Hotel Al Qusais
            5 => 'smart-3star',      // Royal Grand Suite Hotel
        ];

        foreach ($hotels as $hi => $hotel) {
            if ($hotel->roomTypes()->count() > 0) {
                $this->command->line("  Rooms for {$hotel->name} already exist, skipping");
                continue;
            }

            $templateKey = $hotelTemplateMap[$hi] ?? 'smart-3star';
            $rooms = $roomTemplates[$templateKey];

            foreach ($rooms as $ri => $room) {
                RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => $room['name'],
                    'slug' => Str::slug($room['name']),
                    'description' => "Experience our {$room['name']} — {$room['sqm']} sqm of comfort with {$room['bed']} bed configuration. Accommodates up to {$room['guests']} guests.",
                    'max_guests' => $room['guests'],
                    'max_adults' => $room['adults'],
                    'max_children' => $room['children'],
                    'bed_type' => $room['bed'],
                    'room_size_sqm' => $room['sqm'],
                    'base_price' => $room['price'],
                    'total_rooms' => $room['rooms'],
                    'is_active' => true,
                    'sort_order' => $ri,
                ]);
            }

            $this->command->line("  Rooms: {$hotel->name} — " . count($rooms) . ' room types');
        }
    }

    // ─── Reviews ───────────────────────────────────────────────────────

    private function seedReviews(Domain $domain, array $hotels): void
    {
        $reviewData = [
            ['name' => 'Khalid Al Suwaidi', 'email' => 'khalid.s@email.com', 'rating' => 4, 'title' => 'Great value near the airport', 'comment' => 'Stayed here before an early morning flight and it was perfect. Clean room, friendly staff, and the free airport shuttle saved us a lot of hassle. Breakfast was basic but decent. Would definitely stay again for the price.'],
            ['name' => 'Priya Nair', 'email' => 'priya.n@email.com', 'rating' => 5, 'title' => 'Perfect budget find', 'comment' => 'We were looking for something affordable near Al Qusais Metro and this hotel exceeded expectations. The room was spotless, WiFi was fast, and the staff were incredibly helpful. The rooftop pool was a nice bonus after a long day of sightseeing.'],
            ['name' => 'Mark Thompson', 'email' => 'mark.t@email.com', 'rating' => 4, 'title' => 'Comfortable stay, great location', 'comment' => 'The Metro station is just a few minutes walk, which made getting around Dubai really easy. Room was compact but well-designed with everything you need. The restaurant downstairs serves good food at reasonable prices.'],
            ['name' => 'Amina Begum', 'email' => 'amina.b@email.com', 'rating' => 5, 'title' => 'Family loved the resort', 'comment' => 'The kids absolutely loved the waterpark area and couldn\'t stop talking about it. The spa was heavenly — exactly what I needed after a long flight. Gardens are beautiful and well-maintained. Feels like a resort but at city prices.'],
            ['name' => 'Chen Wei', 'email' => 'chen.w@email.com', 'rating' => 3, 'title' => 'Decent for the price', 'comment' => 'Nothing fancy but everything works. Room was clean, bed was comfortable, and the location is convenient for the airport. The neighbourhood has lots of local restaurants which was a nice change from tourist areas. Good value overall.'],
            ['name' => 'Sarah Mitchell', 'email' => 'sarah.mi@email.com', 'rating' => 5, 'title' => 'Hidden gem in Al Nahda', 'comment' => 'I didn\'t expect to find such a lovely resort in this area. The gardens are like a little oasis. Had a wonderful couples massage at the spa and dinner at their Arabic restaurant was outstanding. Highly recommend for families and couples alike.'],
            ['name' => 'Mohammed Al Hashimi', 'email' => 'mohammed.h@email.com', 'rating' => 4, 'title' => 'Reliable and well-located', 'comment' => 'I stay here regularly for business trips to Dubai. The hotel is consistent — always clean, staff always friendly, and the price is right. Walking distance to Metro and plenty of restaurants nearby. The kitchenette is a real plus for longer stays.'],
            ['name' => 'Lisa Fernandez', 'email' => 'lisa.f@email.com', 'rating' => 4, 'title' => 'Quirky and fun hotel', 'comment' => 'Loved the colourful decor and the fun vibe of this hotel. The Citrus Café had delicious food and great coffee. Pool on the roof was refreshing. Only wish the gym was a bit bigger, but for the price you really can\'t complain.'],
            ['name' => 'Deepak Sharma', 'email' => 'deepak.s@email.com', 'rating' => 5, 'title' => 'Best breakfast in the area', 'comment' => 'The breakfast spread here is amazing for a 3-star hotel. Fresh fruits, hot dishes, and great coffee. The room was simple but very clean. Staff went out of their way to arrange a late check-out for us. Will definitely return.'],
            ['name' => 'Emma Clarke', 'email' => 'emma.c@email.com', 'rating' => 4, 'title' => 'Perfect for a transit stay', 'comment' => 'We had a 12-hour layover and this hotel was ideal. Close to the airport, easy check-in, clean room, and we even had time to use the pool. The free shuttle to the airport was really convenient. Great option for travellers passing through.'],
            ['name' => 'Rashid Al Mansoori', 'email' => 'rashid.m@email.com', 'rating' => 3, 'title' => 'Simple but does the job', 'comment' => 'Basic hotel near Al Twar Centre. Room was fine for a night — clean and quiet. The restaurant is small but the food is decent. Free parking was appreciated. Good for budget travellers who just need a clean bed near the airport.'],
            ['name' => 'Yuki Tanaka', 'email' => 'yuki.t@email.com', 'rating' => 5, 'title' => 'Surprised by the quality', 'comment' => 'For the price we paid, this was incredible value. The room was modern and well-equipped, housekeeping was excellent, and the pool area was lovely. The local area has fantastic Indian and Arabic food. A real discovery.'],
            ['name' => 'Patrick O\'Brien', 'email' => 'patrick.o@email.com', 'rating' => 4, 'title' => 'Solid choice for business', 'comment' => 'Clean rooms, good WiFi, quiet neighbourhood. Meeting rooms were professional and well-equipped. The restaurant serves decent business lunches. Staff are efficient and helpful. Would recommend for business travellers on a budget.'],
            ['name' => 'Fatima Al Zaabi', 'email' => 'fatima.z@email.com', 'rating' => 5, 'title' => 'Wonderful family weekend', 'comment' => 'We spent a weekend at the resort and had a fantastic time. The kids loved the pool, my husband enjoyed the gym, and I had an amazing day at the spa. The gardens are so peaceful — hard to believe you\'re in the middle of Dubai. Already planning our next visit.'],
        ];

        $reviewIndex = 0;
        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() > 0) {
                $this->command->line("  Reviews for {$hotel->name} already exist, skipping");
                continue;
            }

            // 2-3 reviews per hotel
            $count = $hotel->star_rating >= 4 ? 3 : rand(2, 3);

            for ($r = 0; $r < $count; $r++) {
                $data = $reviewData[$reviewIndex % count($reviewData)];
                $review = Review::create([
                    'hotel_id' => $hotel->id,
                    'guest_name' => $data['name'],
                    'guest_email' => $data['email'],
                    'rating' => $data['rating'],
                    'title' => $data['title'],
                    'comment' => $data['comment'],
                    'is_verified' => true,
                    'is_approved' => true,
                ]);

                // Link as testimonial to domain
                $domain->testimonials()->syncWithoutDetaching([
                    $review->id => ['sort_order' => $reviewIndex],
                ]);

                $reviewIndex++;
            }

            // Update hotel avg_rating and total_reviews
            $hotel->update([
                'avg_rating' => round($hotel->reviews()->avg('rating'), 1),
                'total_reviews' => $hotel->reviews()->count(),
            ]);

            $this->command->line("  Reviews: {$hotel->name} — {$count} reviews");
        }
    }

    // ─── Image Download Helper ──────────────────────────────────────────

    private function downloadHotelImage(string $fullPath, string $url): bool
    {
        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
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
                return true;
            }
        } catch (\Exception $e) {
            // Fall through to GD fallback
        }

        if (extension_loaded('gd')) {
            $isThumb = str_contains($fullPath, '-thumb');
            $w = $isThumb ? 400 : 1200;
            $h = $isThumb ? 267 : 800;
            $img = imagecreatetruecolor($w, $h);
            $hash = crc32($url);
            $r1 = abs($hash) % 80 + 80;
            $g1 = abs($hash >> 8) % 80 + 80;
            $b1 = abs($hash >> 16) % 80 + 100;
            for ($y = 0; $y < $h; $y++) {
                $ratio = $y / $h;
                $color = imagecolorallocate(
                    $img,
                    (int) ($r1 + (180 - $r1) * $ratio),
                    (int) ($g1 + (200 - $g1) * $ratio),
                    (int) ($b1 + (220 - $b1) * $ratio)
                );
                imageline($img, 0, $y, $w, $y, $color);
            }
            imagejpeg($img, $fullPath, 90);
            imagedestroy($img);
            return true;
        }

        return false;
    }
}
