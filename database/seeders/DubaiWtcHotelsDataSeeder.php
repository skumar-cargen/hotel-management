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

class DubaiWtcHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Dubai WTC Hotels data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'dubai-wtc-hotels')->firstOrFail();

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

        $this->command->info('Dubai WTC Hotels data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Trade Centre',
                'slug' => 'trade-centre',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Home to the iconic Dubai World Trade Centre, this district is Dubai\'s premier exhibition and conference destination. Major hotels line the area, offering direct access to DWTC events and Sheikh Zayed Road.',
                'short_description' => 'Dubai\'s exhibition and conference hub',
                'latitude' => 25.2285,
                'longitude' => 55.2836,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Trade Centre Hotels — Hotels Near Dubai World Trade Centre',
                'meta_description' => 'Book hotels near Dubai World Trade Centre. Direct DWTC access, conference facilities, and Sheikh Zayed Road convenience.',
            ],
            [
                'name' => 'DIFC',
                'slug' => 'difc',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Dubai International Financial Centre — the Middle East\'s leading financial hub. A prestigious district with world-class restaurants, art galleries (Gate Village), and luxury hotels. Home to over 2,700 financial companies.',
                'short_description' => 'Middle East\'s premier financial district',
                'latitude' => 25.2107,
                'longitude' => 55.2788,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'DIFC Hotels — Luxury Hotels in Dubai Financial Centre',
                'meta_description' => 'Stay in DIFC, Dubai\'s prestigious financial district. Luxury hotels surrounded by fine dining, art galleries, and corporate headquarters.',
            ],
            [
                'name' => 'Sheikh Zayed Road',
                'slug' => 'sheikh-zayed-road',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Dubai\'s main artery, lined with some of the city\'s most iconic skyscrapers and hotels. Excellent Metro connectivity and central location between Downtown Dubai and Dubai Marina.',
                'short_description' => 'Dubai\'s iconic tower-lined highway',
                'latitude' => 25.2193,
                'longitude' => 55.2771,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Sheikh Zayed Road Hotels — Central Dubai Hotels on SZR',
                'meta_description' => 'Book hotels on Sheikh Zayed Road. Iconic skyline, Metro access, and central Dubai location between Downtown and Marina.',
            ],
            [
                'name' => 'Al Satwa',
                'slug' => 'al-satwa',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'A culturally diverse neighbourhood just behind Sheikh Zayed Road. Known for affordable dining, textile shops, and multicultural character. Budget-friendly hotels with easy access to Jumeirah and Downtown.',
                'short_description' => 'Diverse cultural neighbourhood behind SZR',
                'latitude' => 25.2240,
                'longitude' => 55.2670,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Satwa Hotels — Affordable Hotels Near Sheikh Zayed Road',
                'meta_description' => 'Budget-friendly hotels in Al Satwa, Dubai. Diverse neighbourhood with easy access to Jumeirah, Downtown, and Sheikh Zayed Road.',
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
            // ── Trade Centre (4 hotels) ──
            [
                'name' => 'Jumeirah Emirates Towers',
                'location' => 'trade-centre',
                'star_rating' => 5,
                'short_description' => 'Iconic twin towers — Dubai\'s premier business hotel with direct DWTC access',
                'description' => 'Jumeirah Emirates Towers is Dubai\'s landmark business address — two shimmering triangular towers connected by a central boulevard. The hotel tower offers 400 rooms with stunning skyline views, The Rib Room steakhouse, Alta Badia Italian, and the legendary rooftop bar. Direct covered walkway to DWTC. The office tower houses the ruler\'s offices.',
                'address' => 'Sheikh Zayed Road, Trade Centre, Dubai',
                'latitude' => 25.2180,
                'longitude' => 55.2820,
                'phone' => '+971 4 330 0000',
                'email' => 'jetinfo@jumeirah.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 19, 20, 21, 22, 25, 26, 27, 28],
            ],
            [
                'name' => 'The H Dubai',
                'location' => 'trade-centre',
                'star_rating' => 5,
                'short_description' => 'Contemporary design hotel on SZR with rooftop pool and award-winning dining',
                'description' => 'The H Dubai is a bold, contemporary hotel on Sheikh Zayed Road offering 236 rooms with floor-to-ceiling city views. Award-winning restaurants include The Penthouse (Asian fusion) and Eat & Meat steakhouse. The stunning rooftop infinity pool and Mandara Spa complete the experience. Steps from DWTC and World Trade Centre Metro.',
                'address' => 'One Sheikh Zayed Road, Trade Centre, Dubai',
                'latitude' => 25.2270,
                'longitude' => 55.2830,
                'phone' => '+971 4 501 8888',
                'email' => 'reservations@thehdubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Dusit Thani Dubai',
                'location' => 'trade-centre',
                'star_rating' => 5,
                'short_description' => 'Thai-inspired luxury hotel overlooking DWTC with award-winning Benjarong restaurant',
                'description' => 'Dusit Thani Dubai brings gracious Thai hospitality to Sheikh Zayed Road. The 321-room hotel overlooks the World Trade Centre and features Benjarong (Dubai\'s best Thai restaurant), PAX restaurant, the serene DFI Spa, and a dramatic outdoor pool. Direct access to DWTC and WTC Metro station. A long-standing favourite of business travellers.',
                'address' => '133 Sheikh Zayed Road, Trade Centre, Dubai',
                'latitude' => 25.2278,
                'longitude' => 55.2832,
                'phone' => '+971 4 343 3333',
                'email' => 'dthd@dusit.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Novotel World Trade Centre',
                'location' => 'trade-centre',
                'star_rating' => 4,
                'short_description' => 'Modern mid-range hotel connected to DWTC with rooftop pool and all-day dining',
                'description' => 'Novotel World Trade Centre offers 412 modern rooms directly connected to Dubai World Trade Centre via a covered walkway. Features include a rooftop pool with city views, Food Exchange all-day restaurant, a well-equipped gym, and extensive meeting rooms. World Trade Centre Metro is 2 minutes away.',
                'address' => '2nd Zabeel Road, Trade Centre, Dubai',
                'latitude' => 25.2290,
                'longitude' => 55.2838,
                'phone' => '+971 4 332 0000',
                'email' => 'h7835@accor.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 20, 21, 25, 27, 28],
            ],

            // ── Trade Centre continued + ibis ──
            [
                'name' => 'ibis One Central',
                'location' => 'trade-centre',
                'star_rating' => 3,
                'short_description' => 'Smart economy hotel in One Central complex with DWTC connectivity',
                'description' => 'ibis One Central offers 552 smart, compact rooms in the One Central complex adjacent to DWTC. Rooms feature the brand\'s signature Sweet Bed, rain shower, and free WiFi. The Hub restaurant serves international cuisine, and the property connects directly to the World Trade Centre exhibition halls. Best value near DWTC.',
                'address' => 'One Central, Trade Centre, Dubai',
                'latitude' => 25.2298,
                'longitude' => 55.2840,
                'phone' => '+971 4 501 3333',
                'email' => 'h9750@accor.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 17, 25, 27, 28],
            ],

            // ── DIFC (2 hotels) ──
            [
                'name' => 'Ritz-Carlton DIFC',
                'location' => 'difc',
                'star_rating' => 5,
                'short_description' => 'Ultra-luxury boutique hotel in Gate Village with Michelin-quality dining',
                'description' => 'The Ritz-Carlton DIFC is an intimate luxury retreat in the heart of Dubai\'s financial centre. With just 98 rooms, it offers personalized service, a stunning lap pool, and Roberto\'s Italian restaurant. Located in Gate Village, guests are surrounded by art galleries, fine dining, and the energy of the financial district.',
                'address' => 'Gate Village, DIFC, Dubai',
                'latitude' => 25.2115,
                'longitude' => 55.2795,
                'phone' => '+971 4 372 2222',
                'email' => 'rc.dxbfc.reservations@ritzcarlton.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'DIFC Living by Rotana',
                'location' => 'difc',
                'star_rating' => 4,
                'short_description' => 'Contemporary hotel apartments in DIFC with gym, pool, and gourmet grocery',
                'description' => 'DIFC Living by Rotana offers 133 modern serviced apartments ranging from studios to three-bedroom suites, each with a full kitchen and washing machine. Set within DIFC, guests enjoy a pool, gym, and proximity to Gate Village\'s restaurants and galleries. Perfect for extended corporate stays in Dubai\'s financial heart.',
                'address' => 'DIFC, near Gate Village, Dubai',
                'latitude' => 25.2098,
                'longitude' => 55.2780,
                'phone' => '+971 4 321 8888',
                'email' => 'difc.living@rotana.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 17, 22, 23, 24, 25, 27, 28],
            ],

            // ── Sheikh Zayed Road (1 hotel) ──
            [
                'name' => 'Four Points by Sheraton SZR',
                'location' => 'sheikh-zayed-road',
                'star_rating' => 4,
                'short_description' => 'Value-driven business hotel on Sheikh Zayed Road with skyline views',
                'description' => 'Four Points by Sheraton SZR offers 384 comfortable rooms with panoramic Sheikh Zayed Road views. Features include The Eatery restaurant, Best Brews bar, a rooftop pool, and a 24-hour fitness centre. Excellent Metro access and central location make it ideal for business and leisure travellers on a smart budget.',
                'address' => 'Sheikh Zayed Road, near Financial Centre Metro, Dubai',
                'latitude' => 25.2180,
                'longitude' => 55.2768,
                'phone' => '+971 4 354 3333',
                'email' => 'reservations@fourpointsszr.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 25, 27, 28],
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
                    'meta_title' => "{$data['name']} — Book Now | Dubai World Trade Centre Hotels",
                    'meta_description' => $data['short_description'].'. Book your stay at '.$data['name'].' with Dubai World Trade Centre Hotels.',
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
            // Jumeirah Emirates Towers
            [
                ['url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Emirates Towers iconic twin towers', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Executive room with skyline view'],
                ['url' => 'https://images.unsplash.com/photo-1563911302283-d2bc129e7570?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Dramatic outdoor pool'],
                ['url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Rooftop bar with city views'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand tower lobby'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Talise Spa suite'],
            ],
            // The H Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'The H Dubai contemporary tower', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern design room'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop infinity pool'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Penthouse restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Mandara Spa relaxation'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Stylish modern lobby'],
            ],
            // Dusit Thani Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1455587734955-081b22074882?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Dusit Thani Dubai tower', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Thai-inspired guest room'],
                ['url' => 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor pool area'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Benjarong Thai restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand Thai-inspired lobby'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'DFI fitness centre'],
            ],
            // Novotel World Trade Centre
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Novotel World Trade Centre', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern Novotel room'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool with city views'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Food Exchange restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Contemporary lobby'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Well-equipped gym'],
            ],
            // ibis One Central
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'ibis One Central building', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Smart ibis room'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'general', 'alt' => 'One Central complex'],
                ['url' => 'https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Hub restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Modern reception'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Basic fitness room'],
            ],
            // Ritz-Carlton DIFC
            [
                ['url' => 'https://images.unsplash.com/photo-1585412727339-54e4bae3b0c9?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Ritz-Carlton DIFC boutique exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1625244724120-1fd1d34d00f6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxury suite with art collection'],
                ['url' => 'https://images.unsplash.com/photo-1561501900-3701fa6a0864?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Sophisticated lap pool'],
                ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Roberto\'s Italian dining'],
                ['url' => 'https://images.unsplash.com/photo-1593693397690-362cb9666fc2?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Intimate luxury lobby'],
                ['url' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Spa treatment room'],
            ],
            // DIFC Living by Rotana
            [
                ['url' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'DIFC Living building', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Apartment living area'],
                ['url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Full kitchen'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Pool area'],
                ['url' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Master bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Gym facilities'],
            ],
            // Four Points by Sheraton SZR
            [
                ['url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Four Points SZR tower', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Comfortable business room'],
                ['url' => 'https://images.unsplash.com/photo-1578645510447-e20b4311e3ce?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Eatery dining'],
                ['url' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel lobby area'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => '24-hour fitness centre'],
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
            // 5-star urban (Emirates Towers, The H, Dusit Thani, Ritz-Carlton)
            'luxury-urban' => [
                ['name' => 'Classic Room', 'bed' => 'King', 'sqm' => 38, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 900, 'rooms' => 25],
                ['name' => 'Luxury Room', 'bed' => 'King', 'sqm' => 50, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1400, 'rooms' => 15],
                ['name' => 'Opera Suite', 'bed' => 'King', 'sqm' => 80, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2500, 'rooms' => 8],
                ['name' => 'Prestige Suite', 'bed' => 'King + Queen', 'sqm' => 140, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 5000, 'rooms' => 3],
            ],
            // 4-star boutique (Novotel, Four Points, DIFC Living)
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 450, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1400, 'rooms' => 4],
            ],
            // 3-star value (ibis)
            'smart-3star' => [
                ['name' => 'Rover Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 280, 'rooms' => 50],
                ['name' => 'Rover Room Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 280, 'rooms' => 40],
                ['name' => 'Connecting Rover Rooms', 'bed' => 'Queen + Twin', 'sqm' => 48, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 520, 'rooms' => 15],
            ],
        ];

        // Map hotels to room templates
        $hotelTemplateMap = [
            0 => 'luxury-urban',      // Jumeirah Emirates Towers
            1 => 'luxury-urban',      // The H Dubai
            2 => 'luxury-urban',      // Dusit Thani Dubai
            3 => 'boutique-4star',    // Novotel World Trade Centre
            4 => 'smart-3star',       // ibis One Central
            5 => 'luxury-urban',      // Ritz-Carlton DIFC
            6 => 'boutique-4star',    // DIFC Living by Rotana
            7 => 'boutique-4star',    // Four Points by Sheraton SZR
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
            ['name' => 'Alexander Petrov', 'email' => 'alexander.p@email.com', 'rating' => 5, 'title' => 'Perfect for DWTC exhibitions', 'comment' => 'Attended GITEX and the hotel was literally connected to the exhibition halls. Saved so much time compared to colleagues staying further away. Room was spacious, business centre excellent, and the staff understood the needs of conference delegates perfectly.'],
            ['name' => 'Nadia Al Suwaidi', 'email' => 'nadia.s@email.com', 'rating' => 5, 'title' => 'DIFC dining scene is incredible', 'comment' => 'Staying in DIFC was the best decision. Roberto\'s restaurant downstairs, Gate Village galleries during lunch breaks, and the intimate luxury of a small hotel. The Ritz-Carlton service is legendary for good reason. Will request this hotel for every Dubai trip.'],
            ['name' => 'Richard Harrington', 'email' => 'richard.h@email.com', 'rating' => 4, 'title' => 'Excellent business hotel on SZR', 'comment' => 'Great location on Sheikh Zayed Road with Metro station right outside. Room had a stunning view of the city lights. Meeting rooms were well-equipped. The rooftop pool was a welcome retreat after long conference days. Good value for the area.'],
            ['name' => 'Yuki Tanaka', 'email' => 'yuki.t@email.com', 'rating' => 5, 'title' => 'Best Thai restaurant in Dubai', 'comment' => 'Chose this hotel specifically for Benjarong and was not disappointed — best Thai food outside Bangkok. The hotel itself has wonderful Thai-inspired décor and the spa was incredibly relaxing. Direct DWTC walkway made my conference week effortless.'],
            ['name' => 'Maria Gonzalez', 'email' => 'maria.g@email.com', 'rating' => 5, 'title' => 'Corporate stay exceeded expectations', 'comment' => 'My company books us here every year for the annual conference and I look forward to it. The rooms are well-appointed, the executive lounge has excellent refreshments, and the staff remember returning guests. The rooftop bar at sunset is not to be missed.'],
            ['name' => 'James Crawford', 'email' => 'james.c@email.com', 'rating' => 4, 'title' => 'Smart choice for DWTC events', 'comment' => 'Connected directly to the World Trade Centre — couldn\'t be more convenient. Room was modern and clean, the restaurant had good international options, and the pool on the roof was a nice bonus. Metro right outside. Will book again for the next expo.'],
            ['name' => 'Fatima Al Mazrouei', 'email' => 'fatima.m@email.com', 'rating' => 5, 'title' => 'The twin towers are iconic', 'comment' => 'Emirates Towers is simply THE business address in Dubai. The room had floor-to-ceiling views of the city, The Rib Room steak was phenomenal, and the rooftop bar is legendary. The covered walkway to DWTC made conference attendance seamless.'],
            ['name' => 'Henrik Larsson', 'email' => 'henrik.l@email.com', 'rating' => 5, 'title' => 'Boutique luxury in DIFC', 'comment' => 'The Ritz-Carlton DIFC is an absolute gem — only 98 rooms means truly personalized service. The lap pool was serene, Roberto\'s Italian was exceptional, and walking through Gate Village with its art galleries was a highlight. Perfect for discerning business travellers.'],
            ['name' => 'Chen Wei', 'email' => 'chen.w@email.com', 'rating' => 4, 'title' => 'Great value near exhibitions', 'comment' => 'Best budget option near DWTC by far. Clean, compact rooms with everything you need. The Sweet Bed lived up to its name after long exhibition days. Direct connection to DWTC halls was incredibly convenient. Restaurant was decent. Will definitely return.'],
            ['name' => 'Sarah Mitchell', 'email' => 'sarah.mi@email.com', 'rating' => 5, 'title' => 'Contemporary and stylish', 'comment' => 'The H Dubai surprised me with its bold design and excellent restaurants. The Penthouse Asian fusion was outstanding, and the rooftop infinity pool had incredible views. Location on SZR is perfect — close to everything. A refreshing alternative to the traditional business hotels.'],
            ['name' => 'Omar Hassan', 'email' => 'omar.h@email.com', 'rating' => 5, 'title' => 'Perfect apartment for extended stay', 'comment' => 'Spent three weeks at DIFC Living for a project and it was ideal. Full kitchen saved on dining costs, washing machine was essential for a long stay, and being in DIFC meant excellent restaurants within walking distance. Pool and gym were well-maintained. Highly recommend.'],
            ['name' => 'Julia Becker', 'email' => 'julia.b@email.com', 'rating' => 4, 'title' => 'Solid business hotel choice', 'comment' => 'Four Points on SZR is reliable and well-priced. Room was comfortable with great skyline views, the restaurant served a good breakfast, and the rooftop pool was refreshing. Metro access is excellent. Perfect for business travellers who want good value without compromising on quality.'],
            ['name' => 'Khalid Al Maktoum', 'email' => 'khalid.m@email.com', 'rating' => 5, 'title' => 'DWTC convenience is unmatched', 'comment' => 'Attended Arab Health exhibition and being connected to the venue made all the difference. Could pop back to my room during breaks. Hotel facilities were excellent — gym, pool, multiple dining options. The staff were extremely professional and helpful.'],
            ['name' => 'Anna Kowalski', 'email' => 'anna.k@email.com', 'rating' => 5, 'title' => 'Mandara Spa was heavenly', 'comment' => 'After a week of back-to-back meetings, the Mandara Spa was exactly what I needed. The infinity pool at sunset was magical. The H Dubai is a design lover\'s dream — every detail is thoughtful. Great central location too. Will be back!'],
            ['name' => 'David Okafor', 'email' => 'david.o@email.com', 'rating' => 4, 'title' => 'Efficient and well-located', 'comment' => 'Novotel WTC ticks all the boxes for business travel — connected to DWTC, Metro on the doorstep, modern rooms, and reliable dining. The rooftop pool was a pleasant surprise. Meeting rooms were well-equipped for our client presentations. Good mid-range option.'],
            ['name' => 'Lisa Thompson', 'email' => 'lisa.t@email.com', 'rating' => 5, 'title' => 'Gate Village is special', 'comment' => 'Staying in DIFC and exploring Gate Village was a highlight of my Dubai trip. The art galleries, the restaurants, the energy of the financial district — it all felt very exclusive. The hotel was intimate and the service was flawless. A truly unique Dubai experience.'],
            ['name' => 'Raj Krishnamurthy', 'email' => 'raj.k@email.com', 'rating' => 5, 'title' => 'Thai hospitality in Dubai', 'comment' => 'Dusit Thani brings authentic Thai warmth to the business district. The lobby is stunning, rooms are beautifully appointed with Thai touches, and Benjarong restaurant alone is worth the stay. Direct DWTC access made my exhibition week stress-free.'],
            ['name' => 'Sophie Dubois', 'email' => 'sophie.d@email.com', 'rating' => 4, 'title' => 'Great for extended corporate stays', 'comment' => 'The apartment in DIFC was perfect for our month-long project. Full kitchen, comfortable beds, and being in the financial district meant walking to client meetings. The gym and pool were well-maintained. Much better than a standard hotel room for a long stay.'],
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
