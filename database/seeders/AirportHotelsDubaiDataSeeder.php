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

class AirportHotelsDubaiDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Airport Hotels Dubai data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'airport-hotels-dubai')->firstOrFail();

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

        $this->command->info('Airport Hotels Dubai data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Al Garhoud',
                'slug' => 'al-garhoud',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Dubai\'s premier airport district, home to major hotels, Dubai Tennis Stadium, and the Irish Village. Just 5 minutes from DXB Terminal 1 and 3, with excellent dining and nightlife options.',
                'short_description' => 'Premier airport district 5 minutes from DXB',
                'latitude' => 25.2399,
                'longitude' => 55.3465,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Garhoud Hotels — Premier Airport District Hotels in Dubai',
                'meta_description' => 'Book hotels in Al Garhoud, Dubai\'s premier airport district. Just 5 minutes from DXB with excellent dining and nightlife options.',
            ],
            [
                'name' => 'Dubai Festival City',
                'slug' => 'dubai-festival-city',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A waterfront mixed-use development on the Dubai Creek featuring Festival City Mall, IMAGINE light show, and premium hotels. 10 minutes from the airport with stunning creek views.',
                'short_description' => 'Waterfront mall and hotel district on Dubai Creek',
                'latitude' => 25.2267,
                'longitude' => 55.3536,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Dubai Festival City Hotels — Waterfront Hotels Near DXB Airport',
                'meta_description' => 'Book waterfront hotels at Dubai Festival City. Premium creek-side hotels with mall access, 10 minutes from Dubai International Airport.',
            ],
            [
                'name' => 'Al Rashidiya Dubai',
                'slug' => 'al-rashidiya-dubai',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A residential district adjacent to Dubai Airport offering affordable hotel options, family parks, and easy Metro access. Popular with budget-conscious travellers needing airport proximity.',
                'short_description' => 'Affordable area adjacent to Dubai Airport',
                'latitude' => 25.2374,
                'longitude' => 55.3893,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Rashidiya Hotels — Affordable Stays Near Dubai Airport',
                'meta_description' => 'Find affordable hotels in Al Rashidiya, adjacent to Dubai Airport. Budget-friendly stays with Metro access and family-friendly parks.',
            ],
            [
                'name' => 'Deira Creek',
                'slug' => 'deira-creek',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'The historic heart of Dubai along the Creek waterway. A vibrant area with traditional souks, dhow wharves, and heritage hotels. 10 minutes from the airport with authentic Dubai character.',
                'short_description' => 'Historic waterfront area near Dubai Creek',
                'latitude' => 25.2644,
                'longitude' => 55.3117,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Deira Creek Hotels — Historic Waterfront Hotels in Dubai',
                'meta_description' => 'Book hotels along Deira Creek, the historic heart of Dubai. Traditional souks, dhow wharves, and heritage hotels 10 minutes from the airport.',
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
            // ── Al Garhoud (2 hotels) ──
            [
                'name' => 'Le Méridien Dubai Hotel & Conference Centre',
                'location' => 'al-garhoud',
                'star_rating' => 5,
                'short_description' => 'Iconic airport hotel with 15 restaurants, village-style grounds, and massive pool complex',
                'description' => 'Le Méridien Dubai is the original airport hotel — a sprawling resort-style property just 5 minutes from DXB. With 383 rooms set across landscaped village-style grounds, it offers 15 restaurants and bars (including The Irish Village), three swimming pools, tennis courts, and a comprehensive spa. The conference centre hosts up to 1,000 delegates.',
                'address' => 'Airport Road, Al Garhoud, Dubai',
                'latitude' => 25.2395,
                'longitude' => 55.3450,
                'phone' => '+971 4 217 0000',
                'email' => 'reservations@lemeridien-dubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Millennium Airport Hotel Dubai',
                'location' => 'al-garhoud',
                'star_rating' => 4,
                'short_description' => 'Purpose-built airport hotel with 24-hour check-in and free DXB shuttle',
                'description' => 'Millennium Airport Hotel is purpose-built for travellers, offering 340 rooms with 24-hour check-in/out flexibility. Located steps from the airport, it features complimentary shuttle service, Tiara restaurant, Champions sports bar, a large pool, and meeting facilities. Soundproofed rooms ensure restful sleep despite the proximity to the airport.',
                'address' => 'Casablanca Road, Al Garhoud, Dubai',
                'latitude' => 25.2410,
                'longitude' => 55.3478,
                'phone' => '+971 4 702 8888',
                'email' => 'reservations@millenniumhotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => 'Flexible',
                'check_out_time' => 'Flexible',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 22, 25, 27, 28],
            ],

            // ── Dubai Festival City (1 hotel) ──
            [
                'name' => 'InterContinental Dubai Festival City',
                'location' => 'dubai-festival-city',
                'star_rating' => 5,
                'short_description' => 'Luxury waterfront hotel with stunning Creek views and direct mall access',
                'description' => 'InterContinental Dubai Festival City commands stunning views across Dubai Creek from its 498 luxurious rooms. Connected directly to Festival City Mall, guests enjoy world-class dining at Reflets par Pierre Gagnaire, the award-winning Angsana Spa, and the nightly IMAGINE fountain show. The hotel\'s creek-side infinity pool is one of Dubai\'s best.',
                'address' => 'Dubai Festival City, Dubai Creek, Dubai',
                'latitude' => 25.2275,
                'longitude' => 55.3540,
                'phone' => '+971 4 701 1111',
                'email' => 'reservations@icdubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 20, 21, 22, 25, 27, 28],
            ],

            // ── Deira Creek (1 hotel) ──
            [
                'name' => 'Crowne Plaza Dubai Deira',
                'location' => 'deira-creek',
                'star_rating' => 4,
                'short_description' => 'Creek-front business hotel with excellent meeting facilities and rooftop pool',
                'description' => 'Crowne Plaza Dubai Deira offers 305 modern rooms overlooking Dubai Creek. The hotel features Masala Bazaar Indian restaurant, The Pub English gastropub, a rooftop pool, and extensive conference facilities. Located between the airport and old Dubai, it\'s ideal for business travellers and those exploring Deira\'s famous souks.',
                'address' => 'Salahuddin Road, Deira, Dubai',
                'latitude' => 25.2648,
                'longitude' => 55.3122,
                'phone' => '+971 4 262 5555',
                'email' => 'reservations@cpdubaideira.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 20, 21, 22, 25, 27, 28],
            ],

            // ── Al Rashidiya Dubai (2 hotels) ──
            [
                'name' => 'Marriott Hotel Al Jaddaf',
                'location' => 'al-rashidiya-dubai',
                'star_rating' => 4,
                'short_description' => 'Modern business hotel near Dubai Frame with rooftop pool and creek views',
                'description' => 'Marriott Hotel Al Jaddaf offers 378 rooms with views of Dubai Creek and the city skyline. Features include a stunning rooftop pool, The Market Place restaurant, M Club lounge, and a modern fitness centre. Located near Dubai Frame and Creek Park, with easy access to the airport and Downtown Dubai.',
                'address' => 'Oud Metha Road, Al Jaddaf, Dubai',
                'latitude' => 25.2310,
                'longitude' => 55.3250,
                'phone' => '+971 4 317 3333',
                'email' => 'reservations@marriottalzaddaf.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 10, 13, 14, 15, 17, 20, 21, 25, 27, 28],
            ],
            [
                'name' => 'Premier Inn Dubai Airport',
                'location' => 'al-rashidiya-dubai',
                'star_rating' => 3,
                'short_description' => 'Reliable budget hotel with consistent quality and free airport shuttle',
                'description' => 'Premier Inn Dubai Airport offers 281 comfortable rooms with the brand\'s signature Hypnos beds, power showers, and blackout curtains. The Cookhouse & Pub serves hearty meals, and there\'s a small pool and gym. Free airport shuttle runs every 30 minutes. Outstanding value for airport-area stays.',
                'address' => 'Airport Road, near Rashidiya Metro, Dubai',
                'latitude' => 25.2380,
                'longitude' => 55.3885,
                'phone' => '+971 4 885 7444',
                'email' => 'dubaiairport.pi@premierinn.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 17, 25, 27, 28],
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
                    'meta_title' => "{$data['name']} — Book Now | Airport Hotels Dubai",
                    'meta_description' => $data['short_description'] . '. Book your stay at ' . $data['name'] . ' with Airport Hotels Dubai.',
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
            // Le Méridien Dubai Hotel & Conference Centre
            [
                ['url' => 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Le Méridien Dubai resort grounds', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Renovated guest room'],
                ['url' => 'https://images.unsplash.com/photo-1519449556851-5720b33024e7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Resort-style swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Irish Village outdoor dining'],
                ['url' => 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Full-service spa'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Tennis and fitness facilities'],
            ],
            // Millennium Airport Hotel Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Millennium Airport Hotel exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Soundproofed guest room'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Tiara restaurant dining'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => '24-hour check-in lobby'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Hotel fitness centre'],
            ],
            // InterContinental Dubai Festival City
            [
                ['url' => 'https://images.unsplash.com/photo-1549294413-26f195200c16?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'InterContinental waterfront at sunset', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Creek view luxury room'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Creek-side infinity pool'],
                ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Reflets fine dining'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Angsana Spa sanctuary'],
            ],
            // Crowne Plaza Dubai Deira
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Crowne Plaza Deira Creek view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern business room'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Masala Bazaar dining'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Business hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Gym facilities'],
            ],
            // Marriott Hotel Al Jaddaf
            [
                ['url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Marriott Hotel Al Jaddaf exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'City view room'],
                ['url' => 'https://images.unsplash.com/photo-1578645510447-e20b4311e3ce?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool with creek views'],
                ['url' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb51f3a?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Market Place restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Contemporary hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Modern fitness centre'],
            ],
            // Premier Inn Dubai Airport
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Premier Inn Airport building', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Comfortable Premier room'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Small outdoor pool'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Cookhouse & Pub'],
                ['url' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Reception area'],
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
            // 5-star urban
            'luxury-urban' => [
                ['name' => 'Classic Room', 'bed' => 'King', 'sqm' => 38, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 900, 'rooms' => 25],
                ['name' => 'Luxury Room', 'bed' => 'King', 'sqm' => 50, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1400, 'rooms' => 15],
                ['name' => 'Opera Suite', 'bed' => 'King', 'sqm' => 80, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2500, 'rooms' => 8],
                ['name' => 'Prestige Suite', 'bed' => 'King + Queen', 'sqm' => 140, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 5000, 'rooms' => 3],
            ],
            // 4-star boutique
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 450, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1400, 'rooms' => 4],
            ],
            // 3-star value
            'smart-3star' => [
                ['name' => 'Rover Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 280, 'rooms' => 50],
                ['name' => 'Rover Room Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 280, 'rooms' => 40],
                ['name' => 'Connecting Rover Rooms', 'bed' => 'Queen + Twin', 'sqm' => 48, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 520, 'rooms' => 15],
            ],
        ];

        // Map hotels to room templates
        $hotelTemplateMap = [
            0 => 'luxury-urban',     // Le Méridien Dubai Hotel & Conference Centre
            1 => 'boutique-4star',   // Millennium Airport Hotel Dubai
            2 => 'luxury-urban',     // InterContinental Dubai Festival City
            3 => 'boutique-4star',   // Crowne Plaza Dubai Deira
            4 => 'boutique-4star',   // Marriott Hotel Al Jaddaf
            5 => 'smart-3star',      // Premier Inn Dubai Airport
        ];

        foreach ($hotels as $hi => $hotel) {
            if ($hotel->roomTypes()->count() > 0) {
                $this->command->line("  Rooms for {$hotel->name} already exist, skipping");
                continue;
            }

            $templateKey = $hotelTemplateMap[$hi] ?? 'boutique-4star';
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
            ['name' => 'Richard Henderson', 'email' => 'richard.h@email.com', 'rating' => 5, 'title' => 'The ultimate airport hotel', 'comment' => 'Le Méridien is not just an airport hotel — it\'s a destination. The Irish Village alone is worth the stay. We had an early morning flight and the 5-minute transfer was a lifesaver. The resort grounds are beautiful and the room was absolutely first-class.'],
            ['name' => 'Aisha Al Blooshi', 'email' => 'aisha.b@email.com', 'rating' => 5, 'title' => 'Conference was seamless', 'comment' => 'Organised a 3-day conference here for 200 delegates. The event team was outstanding — everything from AV setup to catering was handled flawlessly. Delegates loved the resort atmosphere and the restaurants gave everyone plenty of variety.'],
            ['name' => 'Marcus Chen', 'email' => 'marcus.c@email.com', 'rating' => 4, 'title' => 'Perfect transit stay', 'comment' => 'Had a 10-hour layover and the 24-hour check-in was incredibly convenient. Room was quiet despite being near the airport — great soundproofing. The shuttle ran like clockwork. Pool was a nice way to relax between flights.'],
            ['name' => 'Sophie Andersen', 'email' => 'sophie.a@email.com', 'rating' => 5, 'title' => 'Creek views took my breath away', 'comment' => 'The view from our room at InterContinental was stunning — watching the dhows sail along the Creek at sunset was magical. The IMAGINE show at night was spectacular. Reflets restaurant deserves its reputation. An incredible hotel.'],
            ['name' => 'Omar Al Rashidi', 'email' => 'omar.r@email.com', 'rating' => 5, 'title' => 'Best pool in Dubai', 'comment' => 'The infinity pool overlooking Dubai Creek is genuinely one of the best hotel pools I\'ve experienced anywhere. The spa was world-class, the rooms were immaculate, and being connected to the mall was so convenient. Worth every dirham.'],
            ['name' => 'Jennifer Park', 'email' => 'jennifer.p@email.com', 'rating' => 4, 'title' => 'Excellent business facilities', 'comment' => 'Stayed for a week while attending meetings. The business centre was well-equipped, WiFi was fast and reliable, and the M Club lounge was a great place to work between meetings. Rooftop pool was perfect for unwinding after long days.'],
            ['name' => 'Hans Mueller', 'email' => 'hans.m@email.com', 'rating' => 4, 'title' => 'Reliable and consistent', 'comment' => 'I stay at Premier Inn whenever I transit through Dubai. The beds are always comfortable, the shower is always great, and the shuttle is always on time. Nothing fancy but everything works perfectly. That\'s exactly what you need near an airport.'],
            ['name' => 'Fatima Al Suwaidi', 'email' => 'fatima.s@email.com', 'rating' => 5, 'title' => 'Family loved the resort feel', 'comment' => 'We stayed at Le Méridien before a family holiday flight and wished we\'d booked more nights! The kids loved the pools, we enjoyed the restaurants, and the whole village atmosphere was wonderful. So much better than a typical airport hotel.'],
            ['name' => 'David Morrison', 'email' => 'david.mo@email.com', 'rating' => 4, 'title' => 'Great souk experience nearby', 'comment' => 'The location near Deira\'s souks was perfect. We explored the Gold Souk and Spice Souk easily from the hotel. Room had nice Creek views, and the rooftop pool was a highlight. Masala Bazaar had excellent Indian food.'],
            ['name' => 'Nadia Khalil', 'email' => 'nadia.k@email.com', 'rating' => 5, 'title' => 'Smooth business trip', 'comment' => 'Everything about this hotel says \'business efficiency\'. Check-in was quick, the room was modern and well-equipped, meeting rooms were professional, and the location between the airport and city centre is ideal. Will be my go-to Dubai hotel.'],
            ['name' => 'Tom Williams', 'email' => 'tom.w@email.com', 'rating' => 5, 'title' => 'Best value near the airport', 'comment' => 'For the price, this hotel is unbeatable near DXB. The Hypnos beds are genuinely comfortable, blackout curtains are essential for early flights, and the Cookhouse serves proper comfort food. The free shuttle makes it completely hassle-free.'],
            ['name' => 'Reem Al Maktoum', 'email' => 'reem.m@email.com', 'rating' => 4, 'title' => 'Festival City is wonderful', 'comment' => 'The location at Festival City is fantastic — connected directly to the mall with the IMAGINE show right outside. The room was spacious and the Creek views were beautiful. Angsana Spa was a lovely treat before our evening flight.'],
            ['name' => 'Carlos Rodriguez', 'email' => 'carlos.r@email.com', 'rating' => 4, 'title' => 'Convenient for early flights', 'comment' => 'Booked specifically for an early morning flight and it was perfect. Quick shuttle to the terminal, comfortable bed, and I actually managed to get a decent sleep. The restaurant was open early for breakfast which was appreciated.'],
            ['name' => 'Emily Watson', 'email' => 'emily.w@email.com', 'rating' => 5, 'title' => 'More than just a stopover', 'comment' => 'We planned a 2-night stopover and it turned into a mini holiday! The Irish Village was so much fun, the pools were gorgeous, and we even explored Deira. Le Méridien transforms what could be a boring airport stay into something special.'],
            ['name' => 'Karim Abdallah', 'email' => 'karim.a@email.com', 'rating' => 4, 'title' => 'Rooftop pool is stunning', 'comment' => 'The rooftop pool at the Marriott offers incredible views of the Creek and the city skyline. Room was clean and modern. The Market Place had a good buffet breakfast. Only suggestion would be to improve the gym equipment. Otherwise, excellent.'],
            ['name' => 'Sarah O\'Connor', 'email' => 'sarah.o@email.com', 'rating' => 5, 'title' => 'Perfect pre-flight pampering', 'comment' => 'Treated myself to the Angsana Spa before a long-haul flight and it was heavenly. The hotel room was luxurious, dinner at the creek-side restaurant was romantic, and checking out the IMAGINE show was a magical way to end the evening.'],
        ];

        $reviewIndex = 0;
        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() > 0) {
                $this->command->line("  Reviews for {$hotel->name} already exist, skipping");
                continue;
            }

            // 2-3 reviews per hotel, 5-star get 3
            $count = $hotel->star_rating >= 5 ? 3 : rand(2, 3);

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
