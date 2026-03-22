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

class DeiraDubaiHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Deira Dubai Hotels data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'deira-dubai-hotels')->firstOrFail();

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

        $this->command->info('Deira Dubai Hotels data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Gold Souk & Al Ras',
                'slug' => 'gold-souk-al-ras',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Home to the world-famous Dubai Gold Souk and Spice Souk, Al Ras is one of Dubai\'s most historic districts. Traditional dhow wharves line the Creek, and abra boats ferry passengers to Bur Dubai. A must-visit for anyone seeking authentic Dubai.',
                'short_description' => 'World-famous Gold Souk and Spice Souk district',
                'latitude' => 25.2682,
                'longitude' => 55.2967,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Gold Souk & Al Ras Hotels — Stay Near Dubai\'s Famous Souks',
                'meta_description' => 'Book hotels near the Gold Souk and Al Ras in Deira, Dubai. Heritage five-star hotels on Dubai Creek with easy access to traditional souks.',
            ],
            [
                'name' => 'Al Rigga',
                'slug' => 'al-rigga',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Deira\'s most vibrant commercial strip, known for diverse restaurants, exchange houses, and a buzzing nightlife scene. Al Rigga Road is lined with hotels and shops, with excellent Metro connectivity at Union and Al Rigga stations.',
                'short_description' => 'Vibrant commercial strip with diverse dining',
                'latitude' => 25.2630,
                'longitude' => 55.3200,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Rigga Hotels — Hotels on Dubai\'s Most Vibrant Strip',
                'meta_description' => 'Book hotels on Al Rigga Road, Deira. Diverse dining, nightlife, and excellent Metro access in the heart of Dubai.',
            ],
            [
                'name' => 'Naif',
                'slug' => 'naif',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A historic neighbourhood centred around Naif Souq, one of Dubai\'s oldest marketplaces. Known for affordable textile shopping, traditional restaurants, and proximity to the Gold Souk. A slice of authentic old Dubai.',
                'short_description' => 'Historic marketplace and traditional Dubai',
                'latitude' => 25.2700,
                'longitude' => 55.3090,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Naif Hotels — Affordable Stays in Historic Deira',
                'meta_description' => 'Book hotels in Naif, Deira. Affordable accommodation near Naif Souq and the Gold Souk in old Dubai.',
            ],
            [
                'name' => 'Port Saeed',
                'slug' => 'port-saeed',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A commercial district along the Creek, home to Deira City Centre mall and several prominent hotels. The Dubai Creek waterfront provides scenic dhow cruise departures and evening promenades.',
                'short_description' => 'Creek-side district near Deira City Centre',
                'latitude' => 25.2550,
                'longitude' => 55.3200,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Port Saeed Hotels — Creek-Side Hotels Near Deira City Centre',
                'meta_description' => 'Book hotels in Port Saeed, Deira. Creek-front properties near Deira City Centre mall with scenic waterfront views.',
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
            // ── Gold Souk & Al Ras (2 hotels) ──
            [
                'name' => 'Hyatt Regency Dubai',
                'location' => 'gold-souk-al-ras',
                'star_rating' => 5,
                'short_description' => 'Iconic Creek-side hotel near Gold Souk with revolving restaurant and waterfront pool',
                'description' => 'Hyatt Regency Dubai has been a Deira landmark since 1980. This 421-room hotel sits directly on Dubai Creek opposite the Gold Souk, offering stunning waterfront views. Highlights include Al Dawaar — Dubai\'s only revolving restaurant, Miyako Japanese cuisine, and a spectacular Creek-side pool. Abra boats to Bur Dubai depart steps away.',
                'address' => 'Deira Corniche, Al Khaleej Road, Deira, Dubai',
                'latitude' => 25.2688,
                'longitude' => 55.2970,
                'phone' => '+971 4 209 1234',
                'email' => 'dubai.regency@hyatt.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Radisson Blu Hotel Dubai Deira Creek',
                'location' => 'gold-souk-al-ras',
                'star_rating' => 5,
                'short_description' => 'Heritage five-star hotel on the Creek with rooftop Chinese restaurant and Creek views',
                'description' => 'Radisson Blu Deira Creek was Dubai\'s first five-star hotel when it opened in 1975. Beautifully modernised, this 276-room landmark retains its heritage charm while offering contemporary comfort. The rooftop Ying Ying Chinese restaurant offers panoramic Creek views, while Fish Market lets guests choose their own catch. A true piece of Dubai history.',
                'address' => 'Baniyas Road, Deira, Dubai',
                'latitude' => 25.2670,
                'longitude' => 55.2980,
                'phone' => '+971 4 222 7171',
                'email' => 'reservations@radissonblu.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 18, 20, 21, 22, 25, 27, 28],
            ],

            // ── Port Saeed (1 hotel) ──
            [
                'name' => 'Hilton Dubai Creek',
                'location' => 'port-saeed',
                'star_rating' => 5,
                'short_description' => 'Award-winning Creek-front hotel designed by Carlos Ott with stunning water views',
                'description' => 'Hilton Dubai Creek is an architectural masterpiece by Carlos Ott, rising elegantly on the banks of Dubai Creek. The 154-room hotel offers panoramic Creek views, the legendary Glasshouse Brasserie, Table 9 by Nick & Scott, a serene spa, and a Creek-side infinity pool. Connected to Deira City Centre mall via a short walk.',
                'address' => 'Baniyas Road, Port Saeed, Deira, Dubai',
                'latitude' => 25.2555,
                'longitude' => 55.3195,
                'phone' => '+971 4 227 1111',
                'email' => 'DXBHI_Reservations@hilton.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 20, 21, 22, 25, 27, 28],
            ],

            // ── Al Rigga (3 hotels) ──
            [
                'name' => 'Sheraton Deira Hotel',
                'location' => 'al-rigga',
                'star_rating' => 4,
                'short_description' => 'Established business hotel on Al Rigga Road with diverse dining and central location',
                'description' => 'Sheraton Deira Hotel is a well-established 260-room property on the vibrant Al Rigga Road. Features include Ashiana Indian restaurant, a traditional English pub, an outdoor pool, and a modern fitness centre. Walking distance to Al Rigga Metro, Gold Souk, and dozens of restaurants. Reliable service and excellent value.',
                'address' => 'Al Rigga Road, Deira, Dubai',
                'latitude' => 25.2635,
                'longitude' => 55.3195,
                'phone' => '+971 4 268 8888',
                'email' => 'reservations@sheratondeira.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Riviera Hotel Dubai',
                'location' => 'al-rigga',
                'star_rating' => 3,
                'short_description' => 'Classic budget hotel on Al Rigga with pool, restaurant, and great Metro access',
                'description' => 'Riviera Hotel Dubai is a classic 90-room budget hotel that has served travellers for decades. Located on Al Rigga Road, it offers an outdoor pool, Riviera restaurant, and comfortable rooms at honest prices. Al Rigga Metro station is a 3-minute walk, and the Gold Souk is a 10-minute stroll along the vibrant streets of Deira.',
                'address' => 'Al Rigga Road, Deira, Dubai',
                'latitude' => 25.2628,
                'longitude' => 55.3208,
                'phone' => '+971 4 222 2131',
                'email' => 'info@rivierahotel.ae',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 17, 25, 27, 28],
            ],
            [
                'name' => 'Carlton Palace Hotel',
                'location' => 'al-rigga',
                'star_rating' => 4,
                'short_description' => 'Modern city hotel with spacious suites, rooftop pool, and vibrant nightlife',
                'description' => 'Carlton Palace Hotel offers 242 rooms and suites in the heart of Deira\'s bustling Al Rigga district. The hotel features spacious rooms, a rooftop pool with city views, Russian and international restaurants, and a popular nightlife scene. Walking distance to Union Metro station and Dubai\'s famous souks.',
                'address' => 'Al Maktoum Road, Deira, Dubai',
                'latitude' => 25.2645,
                'longitude' => 55.3165,
                'phone' => '+971 4 222 7000',
                'email' => 'reservations@carltonpalace.ae',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 22, 25, 27, 28],
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
                    'meta_title' => "{$data['name']} — Book Now | Deira Dubai Hotels",
                    'meta_description' => $data['short_description'].'. Book your stay at '.$data['name'].' with Deira Dubai Hotels.',
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
            // Hyatt Regency Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1549294413-26f195200c16?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Hyatt Regency Creek waterfront view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Creek view guest room'],
                ['url' => 'https://images.unsplash.com/photo-1561501900-3701fa6a0864?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Creek-side swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1570213489059-0aac6626cade?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Al Dawaar revolving restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Hotel spa and wellness'],
            ],
            // Radisson Blu Deira Creek
            [
                ['url' => 'https://images.unsplash.com/photo-1455587734955-081b22074882?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Radisson Blu heritage hotel', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modernised heritage room'],
                ['url' => 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Hotel pool area'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Fish Market restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Heritage-modern lobby'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fitness centre'],
            ],
            // Hilton Dubai Creek
            [
                ['url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Hilton Dubai Creek modern design', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Premium Creek view room'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Creek-side infinity pool'],
                ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Glasshouse Brasserie'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Spa relaxation area'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Architectural lobby design'],
            ],
            // Sheraton Deira
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Sheraton Deira Hotel', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Comfortable business room'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor pool'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Ashiana Indian restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Hotel gym'],
            ],
            // Riviera Hotel Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Riviera Hotel classic exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Clean budget room'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Hotel pool'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Riviera restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel reception'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'general', 'alt' => 'Hotel exterior at night'],
            ],
            // Carlton Palace Hotel
            [
                ['url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Carlton Palace Hotel tower', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Spacious suite bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1578645510447-e20b4311e3ce?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool city views'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'International restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1559599238-308793637427?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Modern hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Hotel fitness room'],
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

            $this->command->line("  Images: {$hotel->name} — ".count($images).' images');
        }
    }

    // ─── Room Types ────────────────────────────────────────────────────

    private function seedRoomTypes(array $hotels): void
    {
        $roomTemplates = [
            // 5-star urban (Hyatt Regency, Radisson Blu, Hilton Creek)
            'luxury-urban' => [
                ['name' => 'Classic Room', 'bed' => 'King', 'sqm' => 38, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 900, 'rooms' => 25],
                ['name' => 'Luxury Room', 'bed' => 'King', 'sqm' => 50, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1400, 'rooms' => 15],
                ['name' => 'Opera Suite', 'bed' => 'King', 'sqm' => 80, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2500, 'rooms' => 8],
                ['name' => 'Prestige Suite', 'bed' => 'King + Queen', 'sqm' => 140, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 5000, 'rooms' => 3],
            ],
            // 4-star boutique (Sheraton Deira, Carlton Palace)
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 450, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1400, 'rooms' => 4],
            ],
            // 3-star value (Riviera)
            'smart-3star' => [
                ['name' => 'Rover Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 280, 'rooms' => 50],
                ['name' => 'Rover Room Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 280, 'rooms' => 40],
                ['name' => 'Connecting Rooms', 'bed' => 'Queen + Twin', 'sqm' => 48, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 520, 'rooms' => 15],
            ],
        ];

        // Map hotels to room templates
        $hotelTemplateMap = [
            0 => 'luxury-urban',      // Hyatt Regency Dubai
            1 => 'luxury-urban',      // Radisson Blu Deira Creek
            2 => 'luxury-urban',      // Hilton Dubai Creek
            3 => 'boutique-4star',    // Sheraton Deira Hotel
            4 => 'smart-3star',       // Riviera Hotel Dubai
            5 => 'boutique-4star',    // Carlton Palace Hotel
        ];

        foreach ($hotels as $hi => $hotel) {
            if ($hotel->roomTypes()->count() > 0) {
                $this->command->line("  Rooms for {$hotel->name} already exist, skipping");

                continue;
            }

            $templateKey = $hotelTemplateMap[$hi] ?? 'luxury-urban';
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

            $this->command->line("  Rooms: {$hotel->name} — ".count($rooms).' room types');
        }
    }

    // ─── Reviews ───────────────────────────────────────────────────────

    private function seedReviews(Domain $domain, array $hotels): void
    {
        $reviewData = [
            ['name' => 'Khalid Al Mualla', 'email' => 'khalid.m@email.com', 'rating' => 5, 'title' => 'The Gold Souk at your doorstep', 'comment' => 'Staying near the Gold Souk was an unforgettable experience. We walked to the souks every morning and took an abra across the Creek in the evening. The hotel staff were incredibly helpful with directions and recommendations. This is the real Dubai.'],
            ['name' => 'Catherine O\'Brien', 'email' => 'catherine.o@email.com', 'rating' => 5, 'title' => 'Creek views that take your breath away', 'comment' => 'Our room overlooked Dubai Creek and the view of the dhows at sunset was magical. The revolving restaurant was a unique experience — you can see the whole of old Dubai in one dinner. Absolutely worth the visit.'],
            ['name' => 'Ramesh Krishnan', 'email' => 'ramesh.k@email.com', 'rating' => 4, 'title' => 'Authentic Deira experience', 'comment' => 'If you want to experience the real Dubai, stay in Deira. The streets are alive with colour and culture. Our hotel was comfortable, well-maintained, and the staff spoke multiple languages. The food options nearby are incredible and very affordable.'],
            ['name' => 'Yuki Tanaka', 'email' => 'yuki.t@email.com', 'rating' => 5, 'title' => 'Heritage hotel with modern comfort', 'comment' => 'This hotel has such history — it was one of the first luxury hotels in Dubai. The renovation is beautiful, keeping the heritage feel while adding modern amenities. The Creek-side pool is serene, and the rooftop restaurant views are spectacular.'],
            ['name' => 'Amina Belhaj', 'email' => 'amina.b@email.com', 'rating' => 5, 'title' => 'Best value five-star in Dubai', 'comment' => 'We paid half of what a similar hotel in Marina would cost, and the experience was just as luxurious. The Creek location is beautiful, the service is warm and personal, and the Gold Souk is literally steps away. Why would anyone stay elsewhere?'],
            ['name' => 'George Papadopoulos', 'email' => 'george.p@email.com', 'rating' => 4, 'title' => 'Great location for exploring old Dubai', 'comment' => 'Deira is the perfect base for exploring Dubai\'s heritage. We visited the Gold Souk, Spice Souk, and took a dhow cruise — all within walking distance. The hotel was clean and comfortable. Al Rigga Road has fantastic restaurants from every cuisine imaginable.'],
            ['name' => 'Fatima Al Zaabi', 'email' => 'fatima.z@email.com', 'rating' => 5, 'title' => 'The Glasshouse Brasserie alone is worth the stay', 'comment' => 'The architecture of this hotel is stunning — Carlos Ott created something special. Our Creek view room was elegant and peaceful. The Glasshouse Brasserie dinner was one of the best meals we had in Dubai. The infinity pool overlooking the Creek is perfection.'],
            ['name' => 'Robert Fischer', 'email' => 'robert.f@email.com', 'rating' => 4, 'title' => 'Reliable business hotel in Deira', 'comment' => 'I stay here every time I\'m in Dubai for business. The location is convenient, the rooms are always clean, and the Indian restaurant downstairs is excellent. Metro is walking distance and the airport is only 10 minutes away. Great value.'],
            ['name' => 'Noor Hassan', 'email' => 'noor.h@email.com', 'rating' => 5, 'title' => 'Perfect family holiday base', 'comment' => 'We chose Deira for its central location and we were not disappointed. The kids loved the pool, and we could walk to the souks and Creek easily. The hotel arranged a private abra tour that was the highlight of our trip. Much more character than the newer areas.'],
            ['name' => 'Elena Petrova', 'email' => 'elena.p@email.com', 'rating' => 5, 'title' => 'Rooftop pool with amazing city views', 'comment' => 'The rooftop pool was a pleasant surprise — watching the sunset over Deira\'s rooftops with the Creek shimmering in the distance was magical. The room was spacious and well-appointed. The nightlife on Al Rigga Road is vibrant and walkable from the hotel.'],
            ['name' => 'Ali Mohammed', 'email' => 'ali.m@email.com', 'rating' => 4, 'title' => 'Budget-friendly with character', 'comment' => 'For the price, this hotel is unbeatable. Yes, it\'s not a brand new property, but it has real character and the location is superb. The pool was refreshing, the restaurant serves good food, and Al Rigga Metro is a 3-minute walk. Perfect for budget travellers.'],
            ['name' => 'Sophie Laurent', 'email' => 'sophie.l@email.com', 'rating' => 5, 'title' => 'Dubai Creek magic', 'comment' => 'We specifically chose a Creek-view room and it was the best decision. Watching the traditional dhows sail past from our window while sipping morning coffee was pure magic. The hotel has maintained its heritage charm beautifully. The Fish Market restaurant is a must-try.'],
            ['name' => 'Vikram Singh', 'email' => 'vikram.s@email.com', 'rating' => 4, 'title' => 'Walking distance to everything', 'comment' => 'The best thing about staying in Deira is that everything is walkable — Gold Souk, Spice Souk, Creek, Metro station, restaurants, and shops. The hotel was comfortable and the staff were very friendly. The area comes alive in the evening with so much to see and do.'],
            ['name' => 'Maria Santos', 'email' => 'maria.s@email.com', 'rating' => 5, 'title' => 'Where old Dubai comes alive', 'comment' => 'Forget the glitzy new areas — Deira is where you feel the soul of Dubai. The narrow souk lanes, the call to prayer, the smell of spices, the shimmer of gold. Our hotel was a perfect blend of comfort and location. We will be back.'],
            ['name' => 'James Wong', 'email' => 'james.w@email.com', 'rating' => 5, 'title' => 'Exceptional dining and Creek views', 'comment' => 'The Ying Ying restaurant on the rooftop has the best Creek panorama in Dubai. We dined there twice during our stay. The hotel rooms are well-modernised while keeping the heritage feel. Location near the Gold Souk is unbeatable for culture lovers.'],
            ['name' => 'Huda Al Shamsi', 'email' => 'huda.s@email.com', 'rating' => 4, 'title' => 'Central location, excellent Metro access', 'comment' => 'Al Rigga is perfectly placed for exploring all of Dubai. Two Metro stations nearby, taxis everywhere, and the airport is 10 minutes away. The hotel was clean and professional. The surrounding restaurants offer cuisine from around the world at very reasonable prices.'],
        ];

        $reviewIndex = 0;
        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() > 0) {
                $this->command->line("  Reviews for {$hotel->name} already exist, skipping");

                continue;
            }

            // 3-5 reviews per hotel
            $count = $hotel->star_rating >= 5 ? rand(4, 5) : rand(3, 4);

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
