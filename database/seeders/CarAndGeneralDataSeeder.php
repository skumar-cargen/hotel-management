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

class CarAndGeneralDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Car & General Hotels data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'car-and-general')->firstOrFail();

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

        $this->command->info('Car & General Hotels data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Abu Dhabi Corniche',
                'slug' => 'abu-dhabi-corniche',
                'city' => 'Abu Dhabi',
                'country' => 'United Arab Emirates',
                'description' => 'The stunning 8-kilometre waterfront stretch along the capital\'s coastline, featuring pristine beaches, lush gardens, and panoramic views of the Arabian Gulf. Home to Abu Dhabi\'s most iconic hotels and cultural landmarks.',
                'short_description' => 'Abu Dhabi\'s iconic waterfront promenade',
                'latitude' => 24.4539,
                'longitude' => 54.3773,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Abu Dhabi Corniche Hotels — Luxury Waterfront Stays',
                'meta_description' => 'Book luxury hotels on Abu Dhabi Corniche. Stunning waterfront properties with Arabian Gulf views and proximity to cultural landmarks.',
            ],
            [
                'name' => 'Saadiyat Island',
                'slug' => 'saadiyat-island',
                'city' => 'Abu Dhabi',
                'country' => 'United Arab Emirates',
                'description' => 'Abu Dhabi\'s cultural jewel, home to the Louvre Abu Dhabi, pristine natural beaches, and world-class resorts. A sanctuary where art, nature, and luxury converge on the Arabian Gulf.',
                'short_description' => 'Cultural island with natural beaches and museums',
                'latitude' => 24.5474,
                'longitude' => 54.4350,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Saadiyat Island Hotels — Beach Resorts Near Louvre Abu Dhabi',
                'meta_description' => 'Stay on Saadiyat Island, Abu Dhabi. Natural beaches, world-class resorts, and proximity to the Louvre Abu Dhabi museum.',
            ],
            [
                'name' => 'Al Maryah Island',
                'slug' => 'al-maryah-island',
                'city' => 'Abu Dhabi',
                'country' => 'United Arab Emirates',
                'description' => 'Abu Dhabi\'s premier business and lifestyle destination, featuring The Galleria luxury mall, Cleveland Clinic, and sophisticated waterfront dining. The island blends corporate excellence with upscale living.',
                'short_description' => 'Abu Dhabi\'s financial and lifestyle hub',
                'latitude' => 24.5020,
                'longitude' => 54.3942,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Maryah Island Hotels — Business & Luxury Stays in Abu Dhabi',
                'meta_description' => 'Book hotels on Al Maryah Island, Abu Dhabi\'s business district. Premium hotels near The Galleria mall and waterfront dining.',
            ],
            [
                'name' => 'Sharjah Corniche',
                'slug' => 'sharjah-corniche',
                'city' => 'Sharjah',
                'country' => 'United Arab Emirates',
                'description' => 'The cultural capital of the UAE meets stunning coastline along Sharjah\'s Corniche. A beautiful palm-lined waterfront with views of the Arabian Gulf, museums, and heritage sites.',
                'short_description' => 'Cultural waterfront in the UAE\'s art capital',
                'latitude' => 25.3463,
                'longitude' => 55.3878,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Sharjah Corniche Hotels — Beachfront Stays in the Cultural Capital',
                'meta_description' => 'Stay on Sharjah Corniche. Beach resorts with Arabian Gulf views in the UAE\'s Cultural Capital, close to museums and heritage sites.',
            ],
            [
                'name' => 'Al Khan',
                'slug' => 'al-khan',
                'city' => 'Sharjah',
                'country' => 'United Arab Emirates',
                'description' => 'A charming coastal neighbourhood in Sharjah, known for its lagoon, Sharjah Aquarium, and family-friendly beaches. Al Khan offers a relaxed seaside atmosphere with easy access to both Sharjah and Dubai.',
                'short_description' => 'Family-friendly lagoon and beach area',
                'latitude' => 25.3260,
                'longitude' => 55.3780,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Khan Hotels — Family Beach Hotels in Sharjah',
                'meta_description' => 'Book family-friendly hotels in Al Khan, Sharjah. Lagoon views, beach access, and proximity to Sharjah Aquarium.',
            ],
            [
                'name' => 'Al Hamra Village',
                'slug' => 'al-hamra-village',
                'city' => 'Ras Al Khaimah',
                'country' => 'United Arab Emirates',
                'description' => 'A prestigious beachfront community in Ras Al Khaimah featuring an 18-hole championship golf course, marina, and luxury resorts. Al Hamra Village combines beach lifestyle with world-class leisure facilities.',
                'short_description' => 'Golf and beach resort community',
                'latitude' => 25.7020,
                'longitude' => 55.7840,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Hamra Village Hotels — Beach & Golf Resorts in Ras Al Khaimah',
                'meta_description' => 'Stay at Al Hamra Village, Ras Al Khaimah. Luxury beach resorts with championship golf, marina, and Arabian Gulf views.',
            ],
            [
                'name' => 'Al Wadi Desert',
                'slug' => 'al-wadi-desert',
                'city' => 'Ras Al Khaimah',
                'country' => 'United Arab Emirates',
                'description' => 'A protected nature reserve in the heart of the desert, offering an exclusive wilderness experience. Al Wadi features dramatic dunes, indigenous wildlife, and world-class desert resorts that blend luxury with nature.',
                'short_description' => 'Luxury desert nature reserve',
                'latitude' => 25.8310,
                'longitude' => 55.9530,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Wadi Desert Hotels — Luxury Desert Resorts in Ras Al Khaimah',
                'meta_description' => 'Experience luxury desert stays at Al Wadi, Ras Al Khaimah. Private villas in a nature reserve with dune views and wildlife.',
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

        // Also load reused Dubai locations into the map
        $reusedSlugs = ['palm-jumeirah', 'downtown-dubai'];
        foreach ($reusedSlugs as $slug) {
            $location = Location::where('slug', $slug)->first();
            if ($location) {
                $locations[$slug] = $location;
                $domain->locations()->syncWithoutDetaching([
                    $location->id => ['is_active' => true, 'sort_order' => count($locations)],
                ]);
                $this->command->line("  Location (reused): {$location->name}");
            }
        }

        return $locations;
    }

    // ─── Hotels ────────────────────────────────────────────────────────

    private function seedHotels(Domain $domain, array $locations): array
    {
        $hotelData = [
            // ── Abu Dhabi Corniche (1 hotel) ──
            [
                'name' => 'Emirates Palace Mandarin Oriental',
                'location' => 'abu-dhabi-corniche',
                'star_rating' => 5,
                'short_description' => 'Iconic palatial resort on Abu Dhabi\'s Corniche with 1.3km private beach',
                'description' => 'Rising majestically on Abu Dhabi\'s Corniche, Emirates Palace Mandarin Oriental is a palatial masterpiece spanning 100 hectares of pristine gardens and 1.3 kilometres of private beach. The resort features 394 opulent suites adorned with gold and mother-of-pearl, two rooftop helipads, and a marina. Dine at award-winning restaurants including Hakkasan and Mezlai, the only Emirati fine dining restaurant in Abu Dhabi. The Palace Spa offers traditional hammam rituals alongside modern wellness treatments.',
                'address' => 'West Corniche Road, Abu Dhabi',
                'latitude' => 24.4615,
                'longitude' => 54.3174,
                'phone' => '+971 2 690 9000',
                'email' => 'moauh-reservations@mohg.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],

            // ── Saadiyat Island (1 hotel) ──
            [
                'name' => 'Saadiyat Rotana Resort & Villas',
                'location' => 'saadiyat-island',
                'star_rating' => 5,
                'short_description' => 'Beachfront resort on Saadiyat Island with natural beach and nature reserve views',
                'description' => 'Saadiyat Rotana Resort & Villas is a stunning beachfront retreat on Abu Dhabi\'s most prestigious island. Set on a pristine natural beach, the resort offers 327 rooms, suites, and private villas, many with direct beach access. The Bodyline Spa provides rejuvenating treatments, while five restaurants serve cuisines from around the world. The resort overlooks the Saadiyat Beach Golf Club and is minutes from the Louvre Abu Dhabi.',
                'address' => 'Saadiyat Island, Abu Dhabi',
                'latitude' => 24.5410,
                'longitude' => 54.4282,
                'phone' => '+971 2 697 0000',
                'email' => 'saadiyat.resort@rotana.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],

            // ── Al Maryah Island (1 hotel) ──
            [
                'name' => 'Four Seasons Hotel Abu Dhabi',
                'location' => 'al-maryah-island',
                'star_rating' => 5,
                'short_description' => 'Sophisticated waterfront hotel on Al Maryah Island with infinity pool and fine dining',
                'description' => 'Four Seasons Hotel Abu Dhabi at Al Maryah Island is a contemporary urban resort offering refined luxury in the capital\'s financial district. The hotel features 200 elegantly appointed rooms and suites with floor-to-ceiling views of the Abu Dhabi skyline and Al Maryah Island promenade. The rooftop infinity pool offers stunning panoramic views, while Café Milano and Butcher & Still provide world-class dining. Connected to The Galleria luxury mall.',
                'address' => 'Al Maryah Island, Abu Dhabi',
                'latitude' => 24.5030,
                'longitude' => 54.3910,
                'phone' => '+971 2 333 2222',
                'email' => 'reservations.abudhabi@fourseasons.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 16, 18, 19, 22, 27, 28],
            ],

            // ── Sharjah Corniche (1 hotel) ──
            [
                'name' => 'Sheraton Sharjah Beach Resort & Spa',
                'location' => 'sharjah-corniche',
                'star_rating' => 5,
                'short_description' => 'Beachfront resort with private beach, multiple pools, and family-friendly facilities',
                'description' => 'Sheraton Sharjah Beach Resort & Spa is a premier beachfront destination on the shores of the Arabian Gulf. The resort features 258 rooms and suites with sea or garden views, a 200-metre private beach, three swimming pools including a dedicated children\'s pool, and the rejuvenating Shine Spa. Six restaurants and bars offer diverse dining from Italian to international buffet. Perfectly located near Sharjah\'s cultural attractions.',
                'address' => 'Al Muntazah Street, Sharjah',
                'latitude' => 25.3520,
                'longitude' => 55.3850,
                'phone' => '+971 6 532 3232',
                'email' => 'reservations.sharjah@sheraton.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],

            // ── Al Khan (1 hotel) ──
            [
                'name' => 'Hilton Sharjah',
                'location' => 'al-khan',
                'star_rating' => 5,
                'short_description' => 'Modern waterfront hotel on Al Khan lagoon with panoramic views and rooftop pool',
                'description' => 'Hilton Sharjah is a sleek modern hotel overlooking Al Khan lagoon and the Arabian Gulf. The hotel features 255 stylish rooms and suites with lagoon or sea views, a stunning rooftop infinity pool, the eforea spa, and five dining outlets including Bab Al Bahr for Middle Eastern cuisine. Located near Sharjah Aquarium and a short drive from both Sharjah and Dubai city centres.',
                'address' => 'Al Khan Corniche, Sharjah',
                'latitude' => 25.3280,
                'longitude' => 55.3810,
                'phone' => '+971 6 519 1111',
                'email' => 'sharjah.reservations@hilton.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 16, 18, 22, 27, 28],
            ],

            // ── Al Hamra Village (1 hotel) ──
            [
                'name' => 'Waldorf Astoria Ras Al Khaimah',
                'location' => 'al-hamra-village',
                'star_rating' => 5,
                'short_description' => 'Elegant beachfront resort with championship golf course and luxurious spa',
                'description' => 'Waldorf Astoria Ras Al Khaimah is an elegant beachfront resort nestled along a pristine stretch of Arabian Gulf coastline. The resort boasts 346 lavishly appointed rooms and suites, many with private balconies overlooking the sea or the 18-hole championship golf course. The award-winning spa offers bespoke treatments in 12 treatment rooms. Lexington Grill and UMI provide fine dining, while the 350-metre private beach and infinity pool offer pure relaxation.',
                'address' => 'Al Hamra Village, Ras Al Khaimah',
                'latitude' => 25.7035,
                'longitude' => 55.7870,
                'phone' => '+971 7 203 5555',
                'email' => 'rak.info@waldorfastoria.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],

            // ── Al Wadi Desert (1 hotel) ──
            [
                'name' => 'The Ritz-Carlton Al Wadi Desert',
                'location' => 'al-wadi-desert',
                'star_rating' => 5,
                'short_description' => 'Exclusive desert retreat with private pool villas in a nature reserve',
                'description' => 'The Ritz-Carlton Al Wadi Desert is an extraordinary desert retreat set within a 500-hectare protected nature reserve. Each of the 101 private villas features its own temperature-controlled pool, outdoor terrace, and uninterrupted views of the golden dunes. The resort offers camel trekking, falconry, archery, and nature walks to spot Arabian oryx and gazelles. The spa draws on ancient Arabian wellness traditions, while Farmhouse and Moorish restaurants serve exceptional cuisine.',
                'address' => 'Al Wadi Nature Reserve, Ras Al Khaimah',
                'latitude' => 25.8340,
                'longitude' => 55.9560,
                'phone' => '+971 7 206 7777',
                'email' => 'rc.rktrz.reservations@ritzcarlton.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 12, 13, 14, 15, 16, 18, 19, 22, 27, 28],
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
                    'meta_title' => "{$data['name']} — Book Now | Car & General Hotels",
                    'meta_description' => $data['short_description'] . '. Book your stay at ' . $data['name'] . ' with Car & General Hotels.',
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

        // Link shared existing Dubai hotels
        $sharedSlugs = [
            'atlantis-the-royal',
            'oneonly-the-palm',
            'five-palm-jumeirah',
            'address-downtown',
            'vida-downtown-dubai',
        ];

        $sharedHotels = Hotel::whereIn('slug', $sharedSlugs)->get();
        foreach ($sharedHotels as $sharedHotel) {
            $domain->hotels()->syncWithoutDetaching([
                $sharedHotel->id => ['is_active' => true, 'sort_order' => count($hotels)],
            ]);
            $this->command->line("  Hotel (shared): {$sharedHotel->name}");
        }

        return $hotels;
    }

    // ─── Hotel Images (Real Unsplash) ──────────────────────────────────

    private function seedHotelImages(array $hotels): void
    {
        $imagesByHotel = [
            // Emirates Palace Mandarin Oriental
            [
                ['url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Emirates Palace exterior at golden hour', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand palace lobby with gold dome'],
                ['url' => 'https://images.unsplash.com/photo-1618773928121-c32f082e2703?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Palatial suite with Arabian Gulf view'],
                ['url' => 'https://images.unsplash.com/photo-1540541338287-41700207dee6?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Private beach and palace gardens'],
                ['url' => 'https://images.unsplash.com/photo-1600011689032-8b628b8a8747?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Traditional hammam spa'],
                ['url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Hakkasan fine dining'],
            ],
            // Saadiyat Rotana Resort & Villas
            [
                ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Saadiyat Rotana beachfront aerial view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Resort pool with palm trees'],
                ['url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Beachfront villa bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Natural Saadiyat beach'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Bodyline Spa treatment room'],
                ['url' => 'https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Beachside dining restaurant'],
            ],
            // Four Seasons Hotel Abu Dhabi
            [
                ['url' => 'https://images.unsplash.com/photo-1549294413-26f195200c16?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Four Seasons Abu Dhabi waterfront', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxury room with skyline view'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop infinity pool'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Elegant hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Café Milano Italian dining'],
                ['url' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Marble bathroom suite'],
            ],
            // Sheraton Sharjah Beach Resort & Spa
            [
                ['url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Sheraton Sharjah resort aerial view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Sea view room'],
                ['url' => 'https://images.unsplash.com/photo-1519449556851-5720b33024e7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Multiple pool complex'],
                ['url' => 'https://images.unsplash.com/photo-1568084680786-a84f91d1153c?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Private beach with cabanas'],
                ['url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'International buffet restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbec6d?w=1200&q=80', 'cat' => 'general', 'alt' => 'Resort gardens and beach'],
            ],
            // Hilton Sharjah
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Hilton Sharjah modern exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Lagoon view room'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop infinity pool'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Contemporary hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Bab Al Bahr restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fitness centre'],
            ],
            // Waldorf Astoria Ras Al Khaimah
            [
                ['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Waldorf Astoria beachfront at sunset', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxurious suite with sea view'],
                ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d955de201?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Infinity pool overlooking the gulf'],
                ['url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Elegant resort lobby'],
                ['url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Lexington Grill fine dining'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Award-winning spa'],
            ],
            // The Ritz-Carlton Al Wadi Desert
            [
                ['url' => 'https://images.unsplash.com/photo-1542401886-65d6c61db217?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Desert villa among golden dunes', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Private desert villa interior'],
                ['url' => 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Private plunge pool with dune views'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Farmhouse restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1600011689032-8b628b8a8747?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Arabian wellness spa'],
                ['url' => 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=1200&q=80', 'cat' => 'general', 'alt' => 'Desert nature reserve with oryx'],
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
            // Palatial resort (Emirates Palace)
            'palatial-resort' => [
                ['name' => 'Coral Room', 'bed' => 'King', 'sqm' => 55, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1800, 'rooms' => 20],
                ['name' => 'Pearl Suite', 'bed' => 'King', 'sqm' => 100, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 4500, 'rooms' => 12],
                ['name' => 'Khaleej Suite', 'bed' => 'King + Twin', 'sqm' => 170, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 8500, 'rooms' => 6],
                ['name' => 'Palace Suite', 'bed' => 'King + King', 'sqm' => 350, 'guests' => 8, 'adults' => 6, 'children' => 4, 'price' => 18000, 'rooms' => 2],
            ],
            // Beach resort (Saadiyat Rotana, Sheraton Sharjah)
            'beach-resort' => [
                ['name' => 'Classic Sea View Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 750, 'rooms' => 25],
                ['name' => 'Premium Suite', 'bed' => 'King', 'sqm' => 68, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 1500, 'rooms' => 12],
                ['name' => 'Beach Villa', 'bed' => 'King + Twin', 'sqm' => 120, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 3200, 'rooms' => 6],
                ['name' => 'Presidential Suite', 'bed' => 'King + Queen', 'sqm' => 200, 'guests' => 6, 'adults' => 4, 'children' => 2, 'price' => 5500, 'rooms' => 2],
            ],
            // Urban luxury (Four Seasons Abu Dhabi)
            'urban-luxury' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 45, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1100, 'rooms' => 20],
                ['name' => 'Deluxe Suite', 'bed' => 'King', 'sqm' => 72, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2200, 'rooms' => 12],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 105, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 3800, 'rooms' => 6],
                ['name' => 'Royal Suite', 'bed' => 'King + King', 'sqm' => 190, 'guests' => 6, 'adults' => 4, 'children' => 2, 'price' => 6500, 'rooms' => 2],
            ],
            // Modern beach (Hilton Sharjah)
            'modern-beach' => [
                ['name' => 'Guest Room', 'bed' => 'King', 'sqm' => 35, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 550, 'rooms' => 30],
                ['name' => 'Deluxe Lagoon View', 'bed' => 'King', 'sqm' => 42, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 800, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 60, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 1200, 'rooms' => 8],
                ['name' => 'King Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 90, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1900, 'rooms' => 4],
            ],
            // Golf resort (Waldorf Astoria RAK)
            'golf-resort' => [
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 42, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 900, 'rooms' => 25],
                ['name' => 'Grand Deluxe Suite', 'bed' => 'King', 'sqm' => 72, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 1800, 'rooms' => 12],
                ['name' => 'Family Suite', 'bed' => 'King + Twin', 'sqm' => 110, 'guests' => 5, 'adults' => 4, 'children' => 3, 'price' => 3200, 'rooms' => 6],
                ['name' => 'Imperial Suite', 'bed' => 'King + Queen', 'sqm' => 200, 'guests' => 6, 'adults' => 4, 'children' => 2, 'price' => 5500, 'rooms' => 2],
            ],
            // Desert resort (Ritz-Carlton Al Wadi)
            'desert-resort' => [
                ['name' => 'Al Rimal Pool Villa', 'bed' => 'King', 'sqm' => 100, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 2500, 'rooms' => 15],
                ['name' => 'Al Khaimah Tented Pool Villa', 'bed' => 'King', 'sqm' => 135, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 4500, 'rooms' => 8],
                ['name' => 'Al Sahari Two-Bedroom Villa', 'bed' => 'King + Twin', 'sqm' => 200, 'guests' => 5, 'adults' => 4, 'children' => 3, 'price' => 7500, 'rooms' => 4],
                ['name' => 'Al Wadi Presidential Villa', 'bed' => 'King + King + Twin', 'sqm' => 400, 'guests' => 8, 'adults' => 6, 'children' => 4, 'price' => 12000, 'rooms' => 2],
            ],
        ];

        // Map hotels to room templates
        $hotelTemplateMap = [
            0 => 'palatial-resort',   // Emirates Palace Mandarin Oriental
            1 => 'beach-resort',      // Saadiyat Rotana Resort & Villas
            2 => 'urban-luxury',      // Four Seasons Hotel Abu Dhabi
            3 => 'beach-resort',      // Sheraton Sharjah Beach Resort & Spa
            4 => 'modern-beach',      // Hilton Sharjah
            5 => 'golf-resort',       // Waldorf Astoria Ras Al Khaimah
            6 => 'desert-resort',     // The Ritz-Carlton Al Wadi Desert
        ];

        foreach ($hotels as $hi => $hotel) {
            if ($hotel->roomTypes()->count() > 0) {
                $this->command->line("  Rooms for {$hotel->name} already exist, skipping");
                continue;
            }

            $templateKey = $hotelTemplateMap[$hi] ?? 'urban-luxury';
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
            // Emirates Palace reviews
            ['name' => 'Khalid Al Falasi', 'email' => 'khalid.f@email.com', 'rating' => 5, 'title' => 'A palace fit for royalty', 'comment' => 'The Emirates Palace is nothing short of breathtaking. The golden domes, the 1.3km private beach, and the palatial suite took our breath away. The staff treated us like royalty from arrival to departure. Hakkasan dinner was outstanding. A once-in-a-lifetime experience.'],
            ['name' => 'Victoria Chambers', 'email' => 'victoria.c@email.com', 'rating' => 5, 'title' => 'Most magnificent hotel I\'ve ever stayed in', 'comment' => 'Stayed for our anniversary and it exceeded every expectation. The Palace Suite was unreal — marble floors, gold accents, and a private terrace overlooking the Gulf. The spa hammam treatment was heavenly. Worth every dirham.'],
            ['name' => 'Omar Al Suwaidi', 'email' => 'omar.s@email.com', 'rating' => 5, 'title' => 'True Emirati grandeur', 'comment' => 'As a local, I\'m proud to recommend Emirates Palace to every visitor. Mezlai restaurant is the finest Emirati dining in the country. The gardens are pristine and the beach is spectacular. World-class in every way.'],

            // Saadiyat Rotana reviews
            ['name' => 'Rachel Thompson', 'email' => 'rachel.t@email.com', 'rating' => 5, 'title' => 'Perfect island escape', 'comment' => 'Saadiyat Island is magical — pristine natural beach with turquoise water. The resort is beautiful with excellent facilities. We visited the Louvre Abu Dhabi which is a short drive away. The kids loved the pool and the kids club. Wonderful family holiday.'],
            ['name' => 'Youssef Mansour', 'email' => 'youssef.m@email.com', 'rating' => 4, 'title' => 'Beautiful beachfront resort', 'comment' => 'The natural beach is the star of this resort — so different from artificial beaches in the UAE. Rooms were clean and comfortable with lovely views. Restaurant options were good though could be more varied. Would definitely return.'],
            ['name' => 'Emma Whitfield', 'email' => 'emma.wh@email.com', 'rating' => 5, 'title' => 'Serenity and culture combined', 'comment' => 'What a special place. Woke up each morning to the sound of waves on a natural beach. Spent afternoons at the Louvre and evenings watching the sunset from the resort. The spa was exceptional. Pure bliss.'],

            // Four Seasons Abu Dhabi reviews
            ['name' => 'Li Wei', 'email' => 'li.w@email.com', 'rating' => 5, 'title' => 'Four Seasons excellence in Abu Dhabi', 'comment' => 'Impeccable service as expected from Four Seasons. The rooftop pool has the most stunning views of the Abu Dhabi skyline. Café Milano was exceptional — best Italian food we had in the UAE. Connected to The Galleria for shopping. Perfect business trip turned leisure.'],
            ['name' => 'Alexandra Petrov', 'email' => 'alexandra.p@email.com', 'rating' => 4, 'title' => 'Sophisticated urban retreat', 'comment' => 'A beautiful modern hotel with all the Four Seasons touches. The room was sleek and the views were beautiful. Butcher & Still is an outstanding steakhouse. Only reason for 4 stars is no beach access, but the pool more than compensates.'],
            ['name' => 'Tariq Al Hashemi', 'email' => 'tariq.h@email.com', 'rating' => 5, 'title' => 'Best city hotel in Abu Dhabi', 'comment' => 'If you want the finest city hotel experience in the capital, look no further. The suite was immaculate, the fitness centre world-class, and every staff member was genuinely warm and professional. Will be my Abu Dhabi home from now on.'],

            // Sheraton Sharjah reviews
            ['name' => 'Sandra Mitchell', 'email' => 'sandra.m@email.com', 'rating' => 5, 'title' => 'Wonderful family beach resort', 'comment' => 'We were pleasantly surprised by how fantastic Sheraton Sharjah is. The private beach is gorgeous, the kids had three pools to choose from, and the buffet restaurant had something for everyone. Much better value than Dubai. We\'ll be back!'],
            ['name' => 'Abdullah Qasim', 'email' => 'abdullah.q@email.com', 'rating' => 4, 'title' => 'Great beach resort, great value', 'comment' => 'Excellent beachfront property in Sharjah. The rooms were spacious and well-maintained. The Shine Spa was relaxing. Close to Sharjah\'s museums and cultural sites. Great alternative to the expensive Dubai beach hotels. Solid 4 stars.'],
            ['name' => 'Jennifer Park', 'email' => 'jennifer.p@email.com', 'rating' => 5, 'title' => 'Hidden gem in Sharjah', 'comment' => 'Don\'t overlook Sharjah for your UAE holiday! This resort has everything — beautiful beach, multiple pools, spa, great restaurants. The cultural attractions are a bonus. Staff were incredibly friendly and attentive. Highly recommend.'],

            // Hilton Sharjah reviews
            ['name' => 'Mark Stevens', 'email' => 'mark.s@email.com', 'rating' => 4, 'title' => 'Modern and well-located', 'comment' => 'Hilton Sharjah is a great option for both business and leisure. The lagoon views from our room were beautiful. The rooftop pool was a nice surprise. Bab Al Bahr restaurant served excellent Middle Eastern food. Easy access to both Sharjah and Dubai.'],
            ['name' => 'Nadia Younis', 'email' => 'nadia.y@email.com', 'rating' => 4, 'title' => 'Excellent business hotel with leisure perks', 'comment' => 'Came for work but enjoyed it as a mini holiday. The lagoon view room was lovely. The spa was a nice touch after long meetings. The aquarium nearby is great if you have a free afternoon. Good value for a 5-star property.'],

            // Waldorf Astoria RAK reviews
            ['name' => 'Richard Crawford', 'email' => 'richard.c@email.com', 'rating' => 5, 'title' => 'Pure elegance by the sea', 'comment' => 'Waldorf Astoria RAK is absolute perfection. The beach stretches forever, the rooms are beautifully appointed, and Lexington Grill serves the best steak we\'ve had in the UAE. The golf course is championship quality. We extended our stay by two nights — couldn\'t leave!'],
            ['name' => 'Fatima Al Zaabi', 'email' => 'fatima.z@email.com', 'rating' => 5, 'title' => 'Best resort in Ras Al Khaimah', 'comment' => 'Everything about this resort screams luxury without being pretentious. The spa is world-class, the beach is pristine, and the staff remember your name and preferences. UMI restaurant serves incredible Japanese cuisine. A true Waldorf experience.'],
            ['name' => 'Peter Schmidt', 'email' => 'peter.s@email.com', 'rating' => 4, 'title' => 'Excellent golf and beach getaway', 'comment' => 'Came primarily for the golf and was not disappointed — the Al Hamra course is fantastic. But the resort itself won us over completely. Beautiful rooms, great pool, and the beach was uncrowded and clean. Will return with the family next time.'],

            // Ritz-Carlton Al Wadi reviews
            ['name' => 'Catherine Dubois', 'email' => 'catherine.d@email.com', 'rating' => 5, 'title' => 'A desert dream come true', 'comment' => 'The Ritz-Carlton Al Wadi is unlike any hotel experience I\'ve ever had. Our private villa with plunge pool surrounded by golden dunes was surreal. We went camel trekking at sunrise, spotted Arabian oryx, and dined under the stars. The spa\'s Arabian hammam was extraordinary. Magical.'],
            ['name' => 'Robert Anderson', 'email' => 'robert.a@email.com', 'rating' => 5, 'title' => 'Bucket list desert experience', 'comment' => 'I\'ve stayed at desert resorts around the world, but Al Wadi is special. The nature reserve setting is genuine — we saw gazelles from our villa terrace. The Farmhouse restaurant is outstanding. The falconry experience was incredible. Worth every penny.'],
            ['name' => 'Mariam Al Muhairi', 'email' => 'mariam.m@email.com', 'rating' => 5, 'title' => 'Authentic Arabian luxury', 'comment' => 'This is what authentic UAE luxury feels like. Private villa, private pool, desert silence broken only by birdsong. The archery and falconry activities were brilliant. The Moorish restaurant\'s lamb shank was the best I\'ve ever tasted. An unforgettable retreat.'],
        ];

        $reviewIndex = 0;
        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() > 0) {
                $this->command->line("  Reviews for {$hotel->name} already exist, skipping");
                continue;
            }

            // 3-5 reviews per hotel
            $count = $hotel->star_rating >= 5 ? rand(3, 4) : rand(2, 3);

            for ($r = 0; $r < $count; $r++) {
                if ($reviewIndex >= count($reviewData)) {
                    break;
                }

                $data = $reviewData[$reviewIndex];
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
