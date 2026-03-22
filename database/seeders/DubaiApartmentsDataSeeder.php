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

class DubaiApartmentsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Dubai Apartments data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'dubai-apartments')->firstOrFail();

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

        $this->command->info('Dubai Apartments data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Business Bay',
                'slug' => 'business-bay',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'Dubai\'s thriving commercial and residential hub along the Dubai Water Canal. A dynamic district with towering skyscrapers, waterfront promenades, and easy access to Downtown Dubai and DIFC.',
                'short_description' => 'Commercial hub along Dubai Water Canal',
                'latitude' => 25.1850,
                'longitude' => 55.2622,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Business Bay Apartments — Serviced Residences in Dubai\'s Commercial Hub',
                'meta_description' => 'Book serviced apartments in Business Bay, Dubai. Canal views, modern living, and easy access to Downtown Dubai and DIFC.',
            ],
            [
                'name' => 'Jumeirah Lake Towers',
                'slug' => 'jumeirah-lake-towers',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A vibrant mixed-use community of 80 towers surrounding three artificial lakes. JLT offers affordable luxury with excellent dining, retail, and direct Metro access to Dubai Marina and beyond.',
                'short_description' => 'Lakeside towers with Metro access near Dubai Marina',
                'latitude' => 25.0711,
                'longitude' => 55.1430,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'JLT Apartments — Lakeside Hotel Apartments in Jumeirah Lake Towers',
                'meta_description' => 'Stay in Jumeirah Lake Towers. Lakeside living, Metro access, dining, and proximity to Dubai Marina.',
            ],
            [
                'name' => 'Dubai Sports City',
                'slug' => 'dubai-sports-city',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A sports-themed community featuring world-class facilities including a cricket stadium, golf course, and sports academies. Offers spacious and affordable apartments in a family-friendly environment.',
                'short_description' => 'Sports-themed community with affordable apartments',
                'latitude' => 25.0343,
                'longitude' => 55.2332,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Dubai Sports City Apartments — Affordable Family-Friendly Stays',
                'meta_description' => 'Book affordable apartments in Dubai Sports City. Family-friendly community with sports facilities, golf, and spacious living.',
            ],
            [
                'name' => 'Dubai Silicon Oasis',
                'slug' => 'dubai-silicon-oasis',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A free zone technology park with integrated residential living. Known for modern apartments, community amenities, and a growing dining scene. Well connected via Emirates Road.',
                'short_description' => 'Tech park community with modern living',
                'latitude' => 25.1277,
                'longitude' => 55.3756,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Dubai Silicon Oasis Apartments — Modern Living in Tech Hub',
                'meta_description' => 'Stay in Dubai Silicon Oasis. Modern apartments, community amenities, and excellent connectivity in Dubai\'s tech district.',
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
            // ── Business Bay (3 hotels) ──
            [
                'name' => 'Paramount Hotel Midtown',
                'location' => 'business-bay',
                'star_rating' => 4,
                'short_description' => 'Hollywood-themed serviced residences with rooftop pool in Business Bay',
                'description' => 'Paramount Hotel Midtown brings cinematic glamour to Business Bay. The 823 rooms and suites feature Hollywood-inspired design with floor-to-ceiling windows showcasing Dubai skyline views. Amenities include three rooftop pools, a state-of-the-art gym, and multiple dining venues. The apartments come with kitchenettes, making it ideal for extended stays.',
                'address' => 'Business Bay, Al Abraj Street, Dubai',
                'latitude' => 25.1862,
                'longitude' => 55.2601,
                'phone' => '+971 4 246 6666',
                'email' => 'reservations@paramounthotelmidtown.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 22, 23, 24, 25, 27, 28],
            ],
            [
                'name' => 'Pullman Dubai Downtown',
                'location' => 'business-bay',
                'star_rating' => 5,
                'short_description' => 'Contemporary five-star hotel with stunning canal views and rooftop pool',
                'description' => 'Pullman Dubai Downtown offers 354 contemporary rooms overlooking the Dubai Water Canal and Burj Khalifa skyline. The hotel features Vogue Café, a chic rooftop pool and bar, state-of-the-art meeting rooms, and a wellness centre. Studio and one-bedroom suites with kitchenettes are available for long-stay guests.',
                'address' => 'Dubai Water Canal, Business Bay, Dubai',
                'latitude' => 25.1842,
                'longitude' => 55.2648,
                'phone' => '+971 4 523 5555',
                'email' => 'reservations@pullmandowntown.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 20, 21, 25, 27, 28],
            ],
            [
                'name' => 'Mövenpick Hotel Apartments Downtown',
                'location' => 'business-bay',
                'star_rating' => 4,
                'short_description' => 'Spacious Swiss-quality apartments with full kitchens in the heart of Business Bay',
                'description' => 'Mövenpick Hotel Apartments Downtown offers 238 fully furnished apartments ranging from studios to two-bedroom suites, each with a full kitchen, washing machine, and separate living area. Enjoy the rooftop pool with Burj Khalifa views, the all-day dining restaurant, and a well-equipped gym. Perfect for families and long-stay guests who want space and comfort.',
                'address' => 'Sheikh Zayed Road, Business Bay, Dubai',
                'latitude' => 25.1889,
                'longitude' => 55.2585,
                'phone' => '+971 4 450 5000',
                'email' => 'hotel.downtown.dubai@movenpick.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 17, 22, 23, 24, 25, 27, 28],
            ],

            // ── Jumeirah Lake Towers (3 hotels) ──
            [
                'name' => 'Bonnington Jumeirah Lakes Towers',
                'location' => 'jumeirah-lake-towers',
                'star_rating' => 4,
                'short_description' => 'All-suite lakeside hotel with private balconies and lake or golf views',
                'description' => 'Bonnington JLT is an all-suite hotel offering 196 spacious suites with private balconies overlooking the JLT lakes and Emirates Golf Club. Each suite features a separate living area, kitchenette, and work desk. The hotel boasts a stunning outdoor pool, McGettigan\'s Irish pub, and direct DMCC Metro access. An ideal base for business and leisure travellers.',
                'address' => 'Cluster J, Jumeirah Lake Towers, Dubai',
                'latitude' => 25.0728,
                'longitude' => 55.1462,
                'phone' => '+971 4 356 0000',
                'email' => 'reservations@bonningtonjlt.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 22, 25, 27, 28],
            ],
            [
                'name' => 'Millennium Place Marina',
                'location' => 'jumeirah-lake-towers',
                'star_rating' => 4,
                'short_description' => 'Modern tower hotel near Marina with pool, gym, and multiple dining options',
                'description' => 'Millennium Place Marina offers 371 modern rooms and suites with views of Dubai Marina skyline. Features include an outdoor pool, Liwan Restaurant, lobby lounge, and a comprehensive fitness centre. Located near DMCC Metro station with easy access to Dubai Marina Mall and JBR Beach.',
                'address' => 'Cluster A, Jumeirah Lake Towers, Dubai',
                'latitude' => 25.0695,
                'longitude' => 55.1418,
                'phone' => '+971 4 550 8100',
                'email' => 'reservations@millenniumhotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 14, 15, 17, 25, 27, 28],
            ],
            [
                'name' => 'Dusit D2 Kenz Hotel Dubai',
                'location' => 'jumeirah-lake-towers',
                'star_rating' => 4,
                'short_description' => 'Thai-inspired hospitality with spacious rooms and lakefront views in JLT',
                'description' => 'Dusit D2 Kenz brings Thai-inspired hospitality to JLT with 156 elegantly appointed rooms. The Nasimi restaurant serves Thai and international cuisine, while the rooftop pool offers panoramic lake views. Features include Devarana Spa, a modern gym, and direct Metro access. Perfect for both short and extended stays.',
                'address' => 'Cluster S, Jumeirah Lake Towers, Dubai',
                'latitude' => 25.0735,
                'longitude' => 55.1445,
                'phone' => '+971 4 519 0088',
                'email' => 'reservations@dusit.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 22, 25, 27, 28],
            ],

            // ── Dubai Sports City (1 hotel) ──
            [
                'name' => 'Studio One Hotel',
                'location' => 'dubai-sports-city',
                'star_rating' => 4,
                'short_description' => 'Creative lifestyle hotel in Dubai Studio City with pool, gym, and cinema',
                'description' => 'Studio One Hotel is a creative lifestyle hotel with 291 contemporary rooms inspired by the entertainment industry. Features include a rooftop pool and bar, The Matinee restaurant, grab-and-go café, and a state-of-the-art gym. Located in Dubai Studio City with easy access to Dubai Autodrome and Dubai Sports City.',
                'address' => 'Dubai Studio City, Dubai',
                'latitude' => 25.0385,
                'longitude' => 55.2298,
                'phone' => '+971 4 581 3111',
                'email' => 'info@studioonehotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 17, 25, 27, 28],
            ],

            // ── Dubai Silicon Oasis (1 hotel) ──
            [
                'name' => 'Silicon Oasis Suites',
                'location' => 'dubai-silicon-oasis',
                'star_rating' => 3,
                'short_description' => 'Affordable furnished studios and apartments in Dubai Silicon Oasis',
                'description' => 'Silicon Oasis Suites offers modern furnished studios and one-bedroom apartments in the heart of Dubai Silicon Oasis. Each unit features a kitchenette, smart TV, and high-speed WiFi. Community amenities include a swimming pool, gym, BBQ area, and children\'s playground. Great value for tech professionals and families.',
                'address' => 'Dubai Silicon Oasis, Dubai',
                'latitude' => 25.1285,
                'longitude' => 55.3742,
                'phone' => '+971 4 501 2000',
                'email' => 'stay@siliconoasissuites.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 17, 23, 24, 25, 27, 28],
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
                    'meta_title' => "{$data['name']} — Book Now | Dubai Apartments",
                    'meta_description' => $data['short_description'] . '. Book your stay at ' . $data['name'] . ' with Dubai Apartments.',
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
            // Paramount Hotel Midtown
            [
                ['url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Paramount Hotel Midtown tower exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Hollywood-themed apartment living room'],
                ['url' => 'https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern studio with city views'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool with skyline views'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Stylish hotel restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'State-of-the-art fitness centre'],
            ],
            // Pullman Dubai Downtown
            [
                ['url' => 'https://images.unsplash.com/photo-1549294413-26f195200c16?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Pullman Dubai Downtown waterfront', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Contemporary room with canal view'],
                ['url' => 'https://images.unsplash.com/photo-1578645510447-e20b4311e3ce?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Chic rooftop pool and bar'],
                ['url' => 'https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Vogue Café dining'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Modern hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Wellness centre equipment'],
            ],
            // Mövenpick Hotel Apartments Downtown
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Mövenpick Apartments tower', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Spacious apartment living area'],
                ['url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Full kitchen in apartment'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool with Burj Khalifa'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'All-day dining restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Comfortable master bedroom'],
            ],
            // Bonnington Jumeirah Lakes Towers
            [
                ['url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Bonnington JLT lakeside view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Suite with lake view'],
                ['url' => 'https://images.unsplash.com/photo-1584132905271-512c958d674a?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor pool overlooking the lake'],
                ['url' => 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'McGettigan\'s Irish Pub'],
                ['url' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Contemporary hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Modern fitness centre'],
            ],
            // Millennium Place Marina
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Millennium Place Marina exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern room with marina view'],
                ['url' => 'https://images.unsplash.com/photo-1563911302283-d2bc129e7570?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Pool with Dubai Marina skyline'],
                ['url' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb51f3a?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Liwan Restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel reception and lobby'],
                ['url' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Gym with equipment'],
            ],
            // Dusit D2 Kenz Hotel Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Dusit D2 Kenz lakefront exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Thai-inspired guest room'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool panoramic views'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Nasimi Thai restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Devarana Spa treatment room'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Elegant hotel lobby'],
            ],
            // Studio One Hotel
            [
                ['url' => 'https://images.unsplash.com/photo-1585412727339-54e4bae3b0c9?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Studio One Hotel creative exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Entertainment-inspired room'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool and bar'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Matinee restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1604709177225-055f99402ea3?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern studio room'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Hotel gym'],
            ],
            // Silicon Oasis Suites
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Silicon Oasis Suites building', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Furnished studio apartment'],
                ['url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Apartment kitchenette'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Community swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80', 'cat' => 'general', 'alt' => 'Community outdoor area'],
                ['url' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Cozy bedroom'],
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
            // 4-star boutique (Paramount, Bonnington, Millennium Place, Dusit D2, Studio One)
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 450, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1400, 'rooms' => 4],
            ],
            // 5-star urban (Pullman)
            'luxury-urban' => [
                ['name' => 'Classic Room', 'bed' => 'King', 'sqm' => 38, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 900, 'rooms' => 25],
                ['name' => 'Luxury Room', 'bed' => 'King', 'sqm' => 50, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1400, 'rooms' => 15],
                ['name' => 'Opera Suite', 'bed' => 'King', 'sqm' => 80, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2500, 'rooms' => 8],
                ['name' => 'Prestige Suite', 'bed' => 'King + Queen', 'sqm' => 140, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 5000, 'rooms' => 3],
            ],
            // 4-star apartments (Mövenpick)
            'apartment-4star' => [
                ['name' => 'Studio Apartment', 'bed' => 'Queen', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 500, 'rooms' => 30],
                ['name' => 'One-Bedroom Apartment', 'bed' => 'King', 'sqm' => 65, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 750, 'rooms' => 20],
                ['name' => 'Two-Bedroom Apartment', 'bed' => 'King + Twin', 'sqm' => 100, 'guests' => 5, 'adults' => 4, 'children' => 3, 'price' => 1100, 'rooms' => 12],
                ['name' => 'Three-Bedroom Penthouse', 'bed' => 'King + Queen + Twin', 'sqm' => 160, 'guests' => 7, 'adults' => 6, 'children' => 4, 'price' => 1800, 'rooms' => 4],
            ],
            // 3-star value (Silicon Oasis Suites)
            'smart-3star' => [
                ['name' => 'Rover Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 280, 'rooms' => 50],
                ['name' => 'Rover Room Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 280, 'rooms' => 40],
                ['name' => 'Connecting Rover Rooms', 'bed' => 'Queen + Twin', 'sqm' => 48, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 520, 'rooms' => 15],
            ],
        ];

        // Map hotels to room templates
        $hotelTemplateMap = [
            0 => 'boutique-4star',     // Paramount Hotel Midtown
            1 => 'luxury-urban',       // Pullman Dubai Downtown
            2 => 'apartment-4star',    // Mövenpick Hotel Apartments Downtown
            3 => 'boutique-4star',     // Bonnington Jumeirah Lakes Towers
            4 => 'boutique-4star',     // Millennium Place Marina
            5 => 'boutique-4star',     // Dusit D2 Kenz Hotel Dubai
            6 => 'boutique-4star',     // Studio One Hotel
            7 => 'smart-3star',        // Silicon Oasis Suites
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
            ['name' => 'Mariam Al Suwaidi', 'email' => 'mariam.s@email.com', 'rating' => 5, 'title' => 'Felt like home from day one', 'comment' => 'We relocated to Dubai for my husband\'s work and this apartment hotel was the perfect transition. Full kitchen meant we could cook our own meals, the washing machine was a lifesaver with two kids, and the living space was generous. Staff treated us like family throughout our three-month stay.'],
            ['name' => 'Daniel Cooper', 'email' => 'daniel.c@email.com', 'rating' => 5, 'title' => 'Best extended stay in Dubai', 'comment' => 'Spent six weeks here on a project and it was far superior to any hotel room. Having a proper kitchen and living area made all the difference. The pool was great for unwinding after work, and the location in Business Bay meant everything was within reach.'],
            ['name' => 'Noura Al Falasi', 'email' => 'noura.f@email.com', 'rating' => 5, 'title' => 'Spacious apartment, excellent service', 'comment' => 'The two-bedroom apartment was perfect for our family. Full kitchen, separate living room, and even a washing machine. The children loved the pool and the staff were incredibly helpful arranging school transport. Best hotel apartment experience in Dubai.'],
            ['name' => 'Robert Fischer', 'email' => 'robert.f@email.com', 'rating' => 4, 'title' => 'Great value for the space', 'comment' => 'You get so much more space here than a regular hotel for a similar price. The kitchenette was well-equipped and the views from the balcony were stunning. Pool area was clean and well-maintained. Only wish there were more dining options on-site.'],
            ['name' => 'Amina Khalifa', 'email' => 'amina.k@email.com', 'rating' => 5, 'title' => 'Perfect for families', 'comment' => 'The apartment-style layout meant the children had their own space and we could maintain our routine with the full kitchen. The neighbourhood had great restaurants and the Metro was walking distance. We will absolutely return for our next Dubai visit.'],
            ['name' => 'Patrick O\'Brien', 'email' => 'patrick.o@email.com', 'rating' => 4, 'title' => 'Excellent long-stay rates', 'comment' => 'Negotiated a fantastic monthly rate for my three-month assignment. The apartment was well-furnished, WiFi was reliable for remote work, and the gym kept me sane. The concierge helped set up a local phone and navigate the area. Highly recommend for business travellers.'],
            ['name' => 'Fatima Al Hashimi', 'email' => 'fatima.ah@email.com', 'rating' => 5, 'title' => 'Like having your own flat in Dubai', 'comment' => 'Everything you need for real living — not just a hotel stay. Cooked dinner every night in the proper kitchen, did laundry in our own machine, and the kids had space to play. The building amenities (pool, gym, playground) were all top-notch.'],
            ['name' => 'Stefan Müller', 'email' => 'stefan.m@email.com', 'rating' => 5, 'title' => 'Modern, clean, and perfectly located', 'comment' => 'The apartment was spotless and modern with high-quality appliances. Business Bay location is unbeatable — walking distance to the canal promenade, restaurants, and Downtown. The rooftop pool with Burj Khalifa views was the cherry on top.'],
            ['name' => 'Hessa Al Maktoum', 'email' => 'hessa.m@email.com', 'rating' => 5, 'title' => 'Best kitchen facilities in Dubai hotels', 'comment' => 'Finally a hotel apartment where the kitchen is actually functional! Full-size fridge, oven, dishwasher, and proper cooking utensils. We hosted a small dinner for friends in the spacious living room. The weekly housekeeping was thorough and professional.'],
            ['name' => 'James Stewart', 'email' => 'james.s@email.com', 'rating' => 4, 'title' => 'Superb lakeside living', 'comment' => 'The JLT location was perfect — lakes on your doorstep, cafés everywhere, and the Metro right there. The suite was spacious with a proper sitting area and kitchenette. Loved having breakfast on the balcony overlooking the lake every morning.'],
            ['name' => 'Reem Al Qasimi', 'email' => 'reem.q@email.com', 'rating' => 5, 'title' => 'Ideal for a young family', 'comment' => 'Moved here while our villa was being renovated and ended up staying four months. The apartment was perfect — safe building, playground for kids, pool, and a supermarket within walking distance. The management was responsive to every request.'],
            ['name' => 'Maria Santos', 'email' => 'maria.s@email.com', 'rating' => 4, 'title' => 'Clean and comfortable with great views', 'comment' => 'Booked a studio for a month while on assignment. The space was well-designed with everything I needed. The gym was excellent and the pool area was relaxing. Good value for money compared to other options in the area.'],
            ['name' => 'Khalid Al Rashid', 'email' => 'khalid.r@email.com', 'rating' => 5, 'title' => 'Outstanding apartment experience', 'comment' => 'This is how hotel apartments should be done. Thoughtful design, quality furnishings, and genuine space to live. The concierge arranged grocery delivery before we even arrived. The canal walk right outside was perfect for evening strolls.'],
            ['name' => 'Sophie Anderson', 'email' => 'sophie.a@email.com', 'rating' => 5, 'title' => 'Perfect work-from-home setup', 'comment' => 'Working remotely from Dubai for two months and this apartment was ideal. Excellent WiFi, a proper desk area, and a kitchen to prepare my own meals. The building amenities kept me active and the location had everything I needed.'],
            ['name' => 'Omar Hussain', 'email' => 'omar.h@email.com', 'rating' => 4, 'title' => 'Affordable luxury for sports fans', 'comment' => 'Stayed in Sports City for the cricket season and the hotel was brilliant. Affordable rates, comfortable rooms, and the stadium was literally around the corner. The rooftop pool and restaurant were great additions.'],
            ['name' => 'Elena Petrova', 'email' => 'elena.p@email.com', 'rating' => 5, 'title' => 'Home away from home — truly', 'comment' => 'After comparing dozens of options, this apartment hotel stood out for genuine livability. The kitchen had everything, the bed was hotel-quality comfortable, and the living area felt like a real home. The neighbourhood had local shops and restaurants that made us feel like residents.'],
            ['name' => 'Ahmad Al Dhaheri', 'email' => 'ahmad.d@email.com', 'rating' => 4, 'title' => 'Smart choice for tech professionals', 'comment' => 'Silicon Oasis is the perfect spot for anyone in tech. The apartment was modern and affordable, the WiFi was blazing fast, and the community had everything — gym, pool, shops, and restaurants. Great value for the monthly rate.'],
            ['name' => 'Laura Mitchell', 'email' => 'laura.m@email.com', 'rating' => 5, 'title' => 'Exceeded all expectations', 'comment' => 'We booked for two weeks and extended to six. The apartment was beautifully furnished, the kitchenette was perfect for family meals, and the staff remembered our names. The flexible booking made extending seamless. Can\'t recommend enough.'],
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
