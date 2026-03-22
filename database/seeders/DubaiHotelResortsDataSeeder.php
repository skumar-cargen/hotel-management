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

class DubaiHotelResortsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Dubai Hotel Resorts data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'dubai-hotel-resorts')->firstOrFail();

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

        $this->command->info('Dubai Hotel Resorts data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Jumeirah Beach Road',
                'slug' => 'jumeirah-beach-road',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'Dubai\'s most prestigious coastal strip stretching 14km along the Arabian Gulf. Home to iconic resorts, Burj Al Arab, and pristine public beaches. A destination synonymous with luxury and elegance.',
                'short_description' => 'Iconic coastal strip home to Dubai\'s legendary resorts',
                'latitude' => 25.2048,
                'longitude' => 55.2398,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Jumeirah Beach Road Hotels — Luxury Beachfront Resorts',
                'meta_description' => 'Book luxury resorts on Jumeirah Beach Road, Dubai\'s most iconic coastal boulevard.',
            ],
            [
                'name' => 'Umm Suqeim',
                'slug' => 'umm-suqeim',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A prestigious residential and resort district overlooking the Arabian Gulf, home to Madinat Jumeirah and the iconic Burj Al Arab. Known for stunning sunset views and luxury beach resorts.',
                'short_description' => 'Home of Madinat Jumeirah and Burj Al Arab views',
                'latitude' => 25.1445,
                'longitude' => 55.1850,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Umm Suqeim Hotels — Resorts Near Burj Al Arab',
                'meta_description' => 'Book hotels in Umm Suqeim, Dubai. Luxury resorts near Burj Al Arab and Madinat Jumeirah.',
            ],
            [
                'name' => 'Al Sufouh',
                'slug' => 'al-sufouh',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A premium beachfront district between Dubai Marina and Mall of the Emirates. Home to One&Only Royal Mirage and some of Dubai\'s most exclusive resort properties with pristine private beaches.',
                'short_description' => 'Exclusive beachfront district with premium resorts',
                'latitude' => 25.1004,
                'longitude' => 55.1600,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Sufouh Hotels — Premium Beach Resorts in Dubai',
                'meta_description' => 'Stay at premium beach resorts in Al Sufouh, Dubai. Exclusive properties with private beach access.',
            ],
            [
                'name' => 'Bluewaters Island',
                'slug' => 'bluewaters-island',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A stunning man-made island off the coast of JBR, home to Ain Dubai (the world\'s largest observation wheel) and Caesars Palace. A vibrant entertainment destination with luxury hotels.',
                'short_description' => 'Home of Ain Dubai and Caesars Palace',
                'latitude' => 25.0795,
                'longitude' => 55.1221,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Bluewaters Island Hotels — Luxury Stays Near Ain Dubai',
                'meta_description' => 'Book hotels on Bluewaters Island, Dubai. Stay near Ain Dubai at premium resort properties.',
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
            // ── Jumeirah Beach Road (3 hotels) ──
            [
                'name' => 'Burj Al Arab Jumeirah',
                'location' => 'jumeirah-beach-road',
                'star_rating' => 5,
                'short_description' => 'The world\'s most iconic luxury hotel — a sail-shaped masterpiece on its own island',
                'description' => 'Standing on its own man-made island, Burj Al Arab Jumeirah is the world\'s most luxurious hotel. Every one of its 202 duplex suites offers floor-to-ceiling views of the Arabian Gulf through floor-to-ceiling windows. With a fleet of Rolls-Royces, a helipad restaurant, and the stunning Talise Spa, Burj Al Arab defines ultra-luxury. Dine at Al Muntaha (200m above sea level), enjoy the private beach, or arrive by helicopter.',
                'address' => 'Jumeirah Beach Road, Dubai',
                'latitude' => 25.1412,
                'longitude' => 55.1853,
                'phone' => '+971 4 301 7777',
                'email' => 'reservations@burjalarab.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Jumeirah Beach Hotel',
                'location' => 'jumeirah-beach-road',
                'star_rating' => 5,
                'short_description' => 'Wave-shaped beachfront icon with 618 rooms and Burj Al Arab views',
                'description' => 'Shaped like a breaking wave, Jumeirah Beach Hotel has been a Dubai landmark since 1997. With 618 rooms and suites, a private beach stretching over 900 metres, 21 restaurants and bars, and Wild Wadi Waterpark right next door, it\'s the ultimate family resort. Every room offers stunning views of the Arabian Gulf or Burj Al Arab.',
                'address' => 'Jumeirah Beach Road, Dubai',
                'latitude' => 25.1383,
                'longitude' => 55.1897,
                'phone' => '+971 4 348 0000',
                'email' => 'jbhinfo@jumeirah.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Four Seasons Resort Dubai',
                'location' => 'jumeirah-beach-road',
                'star_rating' => 5,
                'short_description' => 'Refined beachfront elegance with private beach and Mediterranean gardens',
                'description' => 'Four Seasons Resort Dubai at Jumeirah Beach brings refined luxury to Dubai\'s coastline. The 237 rooms and suites feature contemporary design with Arabian touches, overlooking tropical gardens and the Arabian Gulf. Highlights include Sea Fu pan-Asian restaurant, Suq overwater restaurant, and a 1,000sqm spa. The resort\'s private beach offers 350m of pristine sand.',
                'address' => 'Jumeirah Beach Road, Jumeirah 2, Dubai',
                'latitude' => 25.2080,
                'longitude' => 55.2392,
                'phone' => '+971 4 270 7777',
                'email' => 'reservations.dubairesort@fourseasons.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'Bulgari Resort Dubai',
                'location' => 'jumeirah-beach-road',
                'star_rating' => 5,
                'short_description' => 'Italian luxury on a seahorse-shaped island with private marina',
                'description' => 'Set on a seahorse-shaped island just off Jumeirah Beach, Bulgari Resort Dubai is the epitome of Italian luxury. With 101 rooms, 20 hotel villas, and 173 residences, each space showcases Mediterranean materials and artisan craftsmanship. The resort features a 50-berth marina, Bulgari Spa, Il Ristorante by Niko Romito, and a private beach surrounded by tropical gardens.',
                'address' => 'Jumeirah Bay Island, Jumeirah 2, Dubai',
                'latitude' => 25.2034,
                'longitude' => 55.2361,
                'phone' => '+971 4 777 5555',
                'email' => 'dubai.reservations@bulgarihotels.com',
                'is_beach_access' => true,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 18, 22, 26, 27, 28],
            ],

            // ── Umm Suqeim (2 hotels) ──
            [
                'name' => 'Madinat Jumeirah',
                'location' => 'umm-suqeim',
                'star_rating' => 5,
                'short_description' => 'Arabian city resort with waterways, souks, and multiple luxury hotels',
                'description' => 'Madinat Jumeirah is Dubai\'s most authentic resort — a 40-hectare Arabian city with wind towers, 3km of private beach, and enchanting waterways navigated by traditional abra boats. Comprising Jumeirah Al Qasr, Jumeirah Mina A\' Salam, and Jumeirah Dar Al Masyaf, the resort offers over 50 restaurants and the award-winning Talise Spa. The Souk Madinat is a shopper\'s paradise with boutiques and art galleries.',
                'address' => 'Al Sufouh Road, Umm Suqeim 3, Dubai',
                'latitude' => 25.1348,
                'longitude' => 55.1825,
                'phone' => '+971 4 366 8888',
                'email' => 'madinatreservations@jumeirah.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Jumeirah Al Naseem',
                'location' => 'umm-suqeim',
                'star_rating' => 5,
                'short_description' => 'Modern beachfront luxury with sea turtle rehabilitation and Burj Al Arab views',
                'description' => 'The newest jewel in Jumeirah\'s beachfront crown, Al Naseem (meaning "sea breeze" in Arabic) offers 430 rooms and suites with contemporary Arabian design. Highlights include the Dubai Turtle Rehabilitation Project, Rockfish restaurant, Summersalt Beach Club, and unobstructed Burj Al Arab views. Connects seamlessly to Madinat Jumeirah\'s waterways and dining.',
                'address' => 'Jumeirah Beach Road, Umm Suqeim, Dubai',
                'latitude' => 25.1378,
                'longitude' => 55.1862,
                'phone' => '+971 4 366 8888',
                'email' => 'jalnaseem@jumeirah.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],

            // ── Al Sufouh (1 hotel) ──
            [
                'name' => 'One&Only Royal Mirage',
                'location' => 'al-sufouh',
                'star_rating' => 5,
                'short_description' => 'Palatial Moorish-inspired resort with 1km private beach and lush gardens',
                'description' => 'Spread across 65 acres of manicured gardens and 1km of private beach, One&Only Royal Mirage is a palatial retreat inspired by ancient Arabian architecture. Three distinct environments — The Palace, Arabian Court, and Residence & Spa — offer 250 rooms with hand-crafted furnishings. Dining includes ZEST by celebrity chef Greg Malouf and the atmospheric Rooftop Lounge & Terrace. The oriental hammam is unforgettable.',
                'address' => 'King Salman Bin Abdulaziz Al Saud St, Al Sufouh, Dubai',
                'latitude' => 25.0941,
                'longitude' => 55.1524,
                'phone' => '+971 4 399 9999',
                'email' => 'reservations@royalmirage.oneandonlyresorts.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],

            // ── Bluewaters Island (1 hotel) ──
            [
                'name' => 'Caesars Palace Bluewaters Dubai',
                'location' => 'bluewaters-island',
                'star_rating' => 5,
                'short_description' => 'Las Vegas glamour meets Arabian hospitality on Bluewaters Island',
                'description' => 'Bringing the legendary Caesars experience to Dubai, this stunning property on Bluewaters Island offers 301 rooms with views of Ain Dubai and the Arabian Gulf. Features Cove Beach Club, Venus Pool & Bar, celebrity chef restaurants including Gordon Ramsay\'s Hell\'s Kitchen, and Qua Spa. Connected to JBR via a pedestrian bridge.',
                'address' => 'Bluewaters Island, Dubai',
                'latitude' => 25.0800,
                'longitude' => 55.1215,
                'phone' => '+971 4 556 6666',
                'email' => 'reservations@caesarspalacedubai.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
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
                    'meta_title' => "{$data['name']} — Book Now | Dubai Hotel Resorts",
                    'meta_description' => $data['short_description'].'. Book your stay at '.$data['name'].' with Dubai Hotel Resorts.',
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
            // Burj Al Arab Jumeirah
            [
                ['url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Burj Al Arab iconic sail-shaped exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Terrace pool overlooking Arabian Gulf'],
                ['url' => 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Talise Spa luxury treatment room'],
                ['url' => 'https://images.unsplash.com/photo-1562438668-bcf0ca6578f0?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Duplex suite with panoramic sea view'],
                ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Al Muntaha sky-high dining'],
                ['url' => 'https://images.unsplash.com/photo-1593693397690-362cb9666fc2?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand entrance and reception'],
            ],
            // Jumeirah Beach Hotel
            [
                ['url' => 'https://images.unsplash.com/photo-1445991842772-097fea258e7b?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Wave-shaped Jumeirah Beach Hotel', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1540541338287-41700207dee6?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Private beach with Burj Al Arab views'],
                ['url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Sea view guest room'],
                ['url' => 'https://images.unsplash.com/photo-1519449556851-5720b33024e7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Family pool area'],
                ['url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'International buffet dining'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fully equipped fitness centre'],
            ],
            // Four Seasons Resort Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1455587734955-081b22074882?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Four Seasons Resort beachfront', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Elegant garden view room'],
                ['url' => 'https://images.unsplash.com/photo-1561501900-3701fa6a0864?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Infinity pool overlooking the Gulf'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Spa wellness sanctuary'],
                ['url' => 'https://images.unsplash.com/photo-1597764690523-15bea4c581c9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Sea Fu pan-Asian dining'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Sophisticated resort lobby'],
            ],
            // Bulgari Resort Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1585412727339-54e4bae3b0c9?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Bulgari Resort island aerial view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1625244724120-1fd1d34d00f6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Italian-designed luxury suite'],
                ['url' => 'https://images.unsplash.com/photo-1560200353-ce0a76b1d438?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Private marina with yachts'],
                ['url' => 'https://images.unsplash.com/photo-1584015438807-4b3646f9aaa8?w=1200&q=80', 'cat' => 'bathroom', 'alt' => 'Marble bathroom with Bulgari amenities'],
                ['url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Il Ristorante by Niko Romito'],
                ['url' => 'https://images.unsplash.com/photo-1609766857326-18fb58fae779?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Night-lit pool area'],
            ],
            // Madinat Jumeirah
            [
                ['url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Madinat Jumeirah waterways and wind towers', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Talise Spa relaxation area'],
                ['url' => 'https://images.unsplash.com/photo-1618773928121-c32f082e2703?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Arabian-inspired luxury suite'],
                ['url' => 'https://images.unsplash.com/photo-1530521954074-e64f6810b32d?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Private beach at sunset'],
                ['url' => 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Souk Madinat waterfront dining'],
                ['url' => 'https://images.unsplash.com/photo-1559599238-308793637427?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Traditional Arabian lobby corridor'],
            ],
            // Jumeirah Al Naseem
            [
                ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Al Naseem modern resort exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Contemporary room with ocean view'],
                ['url' => 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Summersalt pool and beach club'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Elegant restaurant interior'],
                ['url' => 'https://images.unsplash.com/photo-1546412414-e1885259563a?w=1200&q=80', 'cat' => 'bathroom', 'alt' => 'Marble bathroom with rain shower'],
                ['url' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Modern resort lobby'],
            ],
            // One&Only Royal Mirage
            [
                ['url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Royal Mirage palatial resort view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Pool with Moorish architecture'],
                ['url' => 'https://images.unsplash.com/photo-1600011689032-8b628b8a8747?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Oriental hammam spa'],
                ['url' => 'https://images.unsplash.com/photo-1562778612-e1e0cda9915c?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Palace room with garden view'],
                ['url' => 'https://images.unsplash.com/photo-1570213489059-0aac6626cade?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Rooftop terrace dining'],
                ['url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80', 'cat' => 'pool', 'alt' => '1km private beach stretch'],
            ],
            // Caesars Palace Bluewaters Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Caesars Palace Bluewaters exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxury room with Ain Dubai view'],
                ['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Premium suite bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Venus Pool with sea views'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Gordon Ramsay Hell\'s Kitchen'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'State-of-the-art fitness centre'],
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
            // All hotels are 5-star luxury resorts — use luxury-beach template
            'luxury-beach' => [
                ['name' => 'Deluxe Sea View Room', 'bed' => 'King', 'sqm' => 45, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1200, 'rooms' => 20],
                ['name' => 'Premium Suite', 'bed' => 'King', 'sqm' => 75, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2200, 'rooms' => 12],
                ['name' => 'One-Bedroom Beach Suite', 'bed' => 'King', 'sqm' => 95, 'guests' => 4, 'adults' => 2, 'children' => 2, 'price' => 3500, 'rooms' => 8],
                ['name' => 'Royal Penthouse', 'bed' => 'King + Twin', 'sqm' => 200, 'guests' => 6, 'adults' => 4, 'children' => 2, 'price' => 8500, 'rooms' => 3],
            ],
        ];

        // Map all hotels to luxury-beach template
        $hotelTemplateMap = [
            0 => 'luxury-beach',    // Burj Al Arab Jumeirah
            1 => 'luxury-beach',    // Jumeirah Beach Hotel
            2 => 'luxury-beach',    // Four Seasons Resort Dubai
            3 => 'luxury-beach',    // Bulgari Resort Dubai
            4 => 'luxury-beach',    // Madinat Jumeirah
            5 => 'luxury-beach',    // Jumeirah Al Naseem
            6 => 'luxury-beach',    // One&Only Royal Mirage
            7 => 'luxury-beach',    // Caesars Palace Bluewaters Dubai
        ];

        foreach ($hotels as $hi => $hotel) {
            if ($hotel->roomTypes()->count() > 0) {
                $this->command->line("  Rooms for {$hotel->name} already exist, skipping");

                continue;
            }

            $templateKey = $hotelTemplateMap[$hi] ?? 'luxury-beach';
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
            ['name' => 'Mariam Al Falasi', 'email' => 'mariam.f@email.com', 'rating' => 5, 'title' => 'A resort experience beyond imagination', 'comment' => 'From the moment our car pulled into the resort entrance, we knew this would be extraordinary. The private beach was pristine, the spa treatment was heavenly, and the sunset dinner on the terrace was pure magic. This is what Dubai luxury is all about.'],
            ['name' => 'George Patterson', 'email' => 'george.p@email.com', 'rating' => 5, 'title' => 'The ultimate beachfront escape', 'comment' => 'We\'ve stayed at resorts across the Maldives, Seychelles, and Bali, but this Dubai property genuinely rivals the best. The beach is immaculate, the rooms are palatial, and the service is second to none. Already planning our return trip.'],
            ['name' => 'Noura Al Suwaidi', 'email' => 'noura.s@email.com', 'rating' => 5, 'title' => 'Perfect family resort holiday', 'comment' => 'Brought the whole family — grandparents, kids, everyone — and each of us had the time of our lives. The kids club kept the little ones entertained all day, while we enjoyed the spa and pool. The beach suites gave us plenty of space. Flawless!'],
            ['name' => 'Robert Sinclair', 'email' => 'robert.s@email.com', 'rating' => 4, 'title' => 'World-class resort with stunning views', 'comment' => 'The views from our suite were absolutely breathtaking — waking up to the Arabian Gulf every morning was a dream. The resort grounds are beautifully maintained and the restaurants offer incredible variety. Minor wait times at the beach club on weekends but otherwise perfect.'],
            ['name' => 'Fatima Al Shamsi', 'email' => 'fatima.s@email.com', 'rating' => 5, 'title' => 'Luxury redefined at every turn', 'comment' => 'Every detail in this resort speaks of uncompromising luxury. The marble bathrooms, Egyptian cotton sheets, the attentive butler service — nothing is left to chance. The chef\'s table dinner was a once-in-a-lifetime culinary experience. Simply outstanding.'],
            ['name' => 'Andrew Campbell', 'email' => 'andrew.c@email.com', 'rating' => 4, 'title' => 'Exceptional resort, exceptional beach', 'comment' => 'The private beach alone is worth the stay — crystal clear waters, perfectly groomed sand, and attentive beach butlers. The room was spacious and elegantly furnished. The breakfast buffet had everything you could wish for. Highly recommended.'],
            ['name' => 'Hessa Al Maktoum', 'email' => 'hessa.m@email.com', 'rating' => 5, 'title' => 'Our anniversary was magical', 'comment' => 'My husband surprised me with a beach suite for our anniversary and the resort made it truly unforgettable. Rose petals, champagne, a private dinner on the sand — every romantic detail was perfect. The staff remembered our names and went above and beyond.'],
            ['name' => 'James Mitchell', 'email' => 'james.m@email.com', 'rating' => 5, 'title' => 'Business retreat turned into pure bliss', 'comment' => 'Came for a corporate retreat but the resort completely transformed it into a luxury escape. The meeting facilities are top-notch, but once work was done, the infinity pool, private beach, and world-class dining made us forget we were here for business.'],
            ['name' => 'Amira Khalid', 'email' => 'amira.k@email.com', 'rating' => 5, 'title' => 'The spa alone is worth the trip', 'comment' => 'I\'ve visited spas around the world and the spa at this resort is truly exceptional. The hammam experience, the aromatherapy massage, and the relaxation garden overlooking the Gulf — pure paradise. Combined with the stunning rooms and beach, it\'s perfection.'],
            ['name' => 'Daniel Hughes', 'email' => 'daniel.h@email.com', 'rating' => 4, 'title' => 'Impressive resort with incredible dining', 'comment' => 'The variety and quality of restaurants at this resort is remarkable. From Japanese to Italian to traditional Arabic, every meal was an event. The room was luxurious with a fantastic sea view. The beach and pools were immaculate. Would love a late checkout option.'],
            ['name' => 'Reem Al Qassimi', 'email' => 'reem.q@email.com', 'rating' => 5, 'title' => 'Dubai\'s finest resort — no contest', 'comment' => 'Having lived in Dubai for 15 years, I can confidently say this is the finest resort property in the city. The attention to detail, the quality of materials, the calibre of service — everything is at the very highest level. A true jewel on the coastline.'],
            ['name' => 'Sebastian Wright', 'email' => 'sebastian.w@email.com', 'rating' => 5, 'title' => 'Exceeded every expectation', 'comment' => 'We set our expectations sky-high for this trip and the resort still managed to exceed them. The suite was palatial, the private beach was like a postcard, and the staff anticipated our every need. The sunset views from the terrace bar were absolutely spectacular.'],
            ['name' => 'Layla Hassan', 'email' => 'layla.h@email.com', 'rating' => 5, 'title' => 'A true five-star beachfront paradise', 'comment' => 'From the architecture to the landscaping to the service, every element of this resort is crafted to perfection. Waking up to the sound of waves, spending the day between the pool and the beach, and ending with a gourmet dinner — this is the Dubai resort experience at its best.'],
            ['name' => 'Christopher Evans', 'email' => 'christopher.e@email.com', 'rating' => 4, 'title' => 'Stunning property with great location', 'comment' => 'The location is unbeatable — right on the beach with easy access to Dubai\'s major attractions. The resort itself is beautifully designed with Arabian touches. Rooms are spacious and well-appointed. The pool area gets busy in the afternoon but there\'s always space on the beach.'],
            ['name' => 'Maha Al Ketbi', 'email' => 'maha.k@email.com', 'rating' => 5, 'title' => 'The waterways and architecture are magical', 'comment' => 'Staying at this resort felt like stepping into an Arabian fairy tale. The traditional architecture, the waterways with abra boats, the souk with its boutiques — it\'s like a luxury village by the sea. The rooms are gorgeous and the beach is pristine. An unforgettable experience.'],
            ['name' => 'Victoria Palmer', 'email' => 'victoria.p@email.com', 'rating' => 5, 'title' => 'Pure luxury on the island', 'comment' => 'The island setting gives this resort a truly exclusive feel. The views of Ain Dubai from our room were spectacular, especially at night. The beach club is vibrant yet sophisticated, and the restaurants — especially Hell\'s Kitchen — were outstanding. A unique Dubai experience.'],
            ['name' => 'Omar Al Hashimi', 'email' => 'omar.h@email.com', 'rating' => 5, 'title' => 'Palatial grounds and impeccable service', 'comment' => 'The resort grounds are vast and immaculately maintained — walking through the gardens to the beach feels like a journey through paradise. The Moorish architecture is stunning, the hammam spa is exceptional, and the rooftop dining under the stars was magical.'],
            ['name' => 'Emily Richardson', 'email' => 'emily.r@email.com', 'rating' => 4, 'title' => 'Beautiful resort with world-class facilities', 'comment' => 'Everything about this resort screams quality — from the marble floors to the Egyptian cotton bedding to the gourmet dining. The beach is long and never crowded, the pool is gorgeous, and the gym is state-of-the-art. A truly premium resort experience.'],
        ];

        $reviewIndex = 0;
        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() > 0) {
                $this->command->line("  Reviews for {$hotel->name} already exist, skipping");

                continue;
            }

            // 4-5 reviews per hotel (all are 5-star)
            $count = rand(4, 5);

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
