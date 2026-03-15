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

class JumeiraBeachHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Jumeira Beach Hotels data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'jumeira-beach-hotels')->firstOrFail();

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

        $this->command->info('Jumeira Beach Hotels data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Palm Jumeirah',
                'slug' => 'palm-jumeirah',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'The world\'s largest man-made island, shaped like a palm tree. Home to iconic resorts, private beaches, and breathtaking views of the Arabian Gulf skyline.',
                'short_description' => 'Iconic island with luxury resorts and private beaches',
                'latitude' => 25.1124,
                'longitude' => 55.1390,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Palm Jumeirah Hotels — Luxury Beachfront Stays in Dubai',
                'meta_description' => 'Book luxury hotels on Palm Jumeirah, Dubai\'s iconic island. Private beaches, world-class resorts, and stunning Arabian Gulf views.',
            ],
            [
                'name' => 'Jumeirah Beach Residence',
                'slug' => 'jumeirah-beach-residence',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A vibrant beachfront community with a 1.7km sandy beach, The Walk promenade, and hundreds of shops and restaurants. The ultimate beach lifestyle destination.',
                'short_description' => 'Vibrant beachfront living with The Walk promenade',
                'latitude' => 25.0780,
                'longitude' => 55.1340,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'JBR Hotels — Beachfront Hotels at Jumeirah Beach Residence',
                'meta_description' => 'Stay at Jumeirah Beach Residence. Beach access, The Walk, dining, and entertainment steps from your hotel.',
            ],
            [
                'name' => 'Dubai Marina',
                'slug' => 'dubai-marina',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A stunning waterfront community with a 3km promenade lined with cafés, restaurants, and luxury yachts. One of Dubai\'s most sought-after neighbourhoods.',
                'short_description' => 'Waterfront district with marina views and nightlife',
                'latitude' => 25.0800,
                'longitude' => 55.1400,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Dubai Marina Hotels — Waterfront Stays with Marina Views',
                'meta_description' => 'Book hotels in Dubai Marina. Waterfront living, yacht views, world-class dining, and easy beach access.',
            ],
            [
                'name' => 'Downtown Dubai',
                'slug' => 'downtown-dubai',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'The heart of Dubai featuring Burj Khalifa, Dubai Mall, and the Dubai Fountain. The city\'s most prestigious address for luxury hotel stays.',
                'short_description' => 'Home of Burj Khalifa and Dubai Mall',
                'latitude' => 25.1972,
                'longitude' => 55.2744,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Downtown Dubai Hotels — Luxury Stays Near Burj Khalifa',
                'meta_description' => 'Stay in Downtown Dubai near Burj Khalifa and Dubai Mall. Premium hotels with fountain views and world-class dining.',
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
            // ── Palm Jumeirah (3 hotels) ──
            [
                'name' => 'Atlantis The Royal',
                'location' => 'palm-jumeirah',
                'star_rating' => 5,
                'short_description' => 'Ultra-luxury resort at the crown of Palm Jumeirah with sky-high infinity pools',
                'description' => 'Rising majestically at the tip of Palm Jumeirah, Atlantis The Royal redefines ultra-luxury hospitality. Featuring avant-garde architecture by Kohn Pedersen Fox, the resort boasts 795 rooms and suites with floor-to-ceiling Arabian Gulf views. Dine at celebrity chef restaurants including Nobu by the Beach and Jaleo by José Andrés. The rooftop infinity pool offers the most iconic skyline view in Dubai, while the underground Aquaventure Waterpark provides endless family entertainment.',
                'address' => 'Crescent Road, Palm Jumeirah, Dubai',
                'latitude' => 25.1310,
                'longitude' => 55.1172,
                'phone' => '+971 4 426 2000',
                'email' => 'reservations@atlantistheroyal.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'One&Only The Palm',
                'location' => 'palm-jumeirah',
                'star_rating' => 5,
                'short_description' => 'Exclusive boutique resort with Moorish-inspired architecture and private marina',
                'description' => 'Nestled on the private western crescent of Palm Jumeirah, One&Only The Palm is an intimate sanctuary of calm. Inspired by Moorish and Andalusian architecture, the resort features just 90 rooms and suites — each with a private balcony overlooking manicured gardens or the Arabian Gulf. The resort\'s private beach stretches along pristine turquoise waters, while Guerlain Spa offers bespoke wellness journeys. Dining includes STAY by Yannick Alléno and the vibrant 101 Dining Lounge.',
                'address' => 'West Crescent, Palm Jumeirah, Dubai',
                'latitude' => 25.1052,
                'longitude' => 55.1248,
                'phone' => '+971 4 440 1010',
                'email' => 'reservations@oneandonlythepalm.com',
                'is_beach_access' => true,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'FIVE Palm Jumeirah',
                'location' => 'palm-jumeirah',
                'star_rating' => 5,
                'short_description' => 'Lifestyle resort with vibrant pool parties and stunning beachfront suites',
                'description' => 'FIVE Palm Jumeirah is where luxury meets lifestyle on one of Dubai\'s most iconic addresses. This vibrant resort features 470 rooms, suites and penthouses with panoramic views of the Dubai Marina skyline and Arabian Gulf. Known for its legendary pool parties and beach club scene, FIVE also offers serene escapes at the Cinq Mondes Spa. Multiple dining concepts including Maiden Shanghai and The Penthouse ensure every palate is satisfied.',
                'address' => 'Palm Jumeirah, No. 1 The Palm, Dubai',
                'latitude' => 25.1135,
                'longitude' => 55.1382,
                'phone' => '+971 4 455 9999',
                'email' => 'hello@fivehotelsandresorts.com',
                'is_beach_access' => true,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 18, 22, 26, 27, 28],
            ],

            // ── Jumeirah Beach Residence (3 hotels) ──
            [
                'name' => 'Rixos Premium Dubai JBR',
                'location' => 'jumeirah-beach-residence',
                'star_rating' => 5,
                'short_description' => 'All-inclusive beachfront resort with direct JBR Beach access and kids club',
                'description' => 'Rixos Premium Dubai offers the only all-inclusive Ultra-Luxury experience on JBR Beach. With 414 spacious rooms and suites featuring contemporary Turkish-inspired design, guests enjoy unlimited dining across seven restaurants, a sprawling private beach, Anjana Spa, and the award-winning Rixy Kids Club. The property sits on The Walk at JBR, putting you steps from hundreds of shops and cafés.',
                'address' => 'The Walk, JBR, Dubai',
                'latitude' => 25.0788,
                'longitude' => 55.1332,
                'phone' => '+971 4 520 0000',
                'email' => 'reservation.premiumdubai@rixos.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 11, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'Sofitel Dubai Jumeirah Beach',
                'location' => 'jumeirah-beach-residence',
                'star_rating' => 5,
                'short_description' => 'French elegance on JBR with rooftop infinity pool and sea views',
                'description' => 'Sofitel Dubai Jumeirah Beach brings French art de vivre to the shores of the Arabian Gulf. This elegant 444-room property features contemporary rooms with floor-to-ceiling windows, a stunning rooftop infinity pool with panoramic sea views, and the signature Plantation Brasserie. Located directly on The Walk at JBR, guests are steps from the beach, Ain Dubai, and Bluewaters Island.',
                'address' => 'The Walk, JBR, Dubai',
                'latitude' => 25.0792,
                'longitude' => 55.1345,
                'phone' => '+971 4 448 4848',
                'email' => 'h6835@sofitel.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'JA Oasis Beach Tower',
                'location' => 'jumeirah-beach-residence',
                'star_rating' => 4,
                'short_description' => 'Spacious serviced apartments on JBR with full kitchen and beach access',
                'description' => 'JA Oasis Beach Tower offers the space and comfort of home with the services of a luxury hotel. Located in the heart of JBR, these fully furnished apartments range from studios to three-bedroom suites, each with a full kitchen, living area, and washing machine. Guests enjoy direct beach access, a temperature-controlled pool, fitness centre, and complimentary shuttle to sister properties.',
                'address' => 'Bahar 2, JBR, Dubai',
                'latitude' => 25.0775,
                'longitude' => 55.1330,
                'phone' => '+971 4 814 5555',
                'email' => 'oasis.beach@jaresorts.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 17, 22, 23, 24, 26, 27, 28],
            ],

            // ── Dubai Marina (3 hotels) ──
            [
                'name' => 'Address Dubai Marina',
                'location' => 'dubai-marina',
                'star_rating' => 5,
                'short_description' => 'Sophisticated waterfront hotel with stunning marina and skyline views',
                'description' => 'Address Dubai Marina is a sophisticated waterfront retreat in the heart of Dubai\'s most dynamic neighbourhood. The 200-room hotel features the signature Address experience — elegant rooms with smart controls, a stunning infinity pool overlooking the marina, The Restaurant for Mediterranean cuisine, and Shades for poolside dining. The hotel connects directly to Dubai Marina Mall and is walking distance to the beach.',
                'address' => 'Dubai Marina, Al Marsa Street, Dubai',
                'latitude' => 25.0767,
                'longitude' => 55.1395,
                'phone' => '+971 4 436 7777',
                'email' => 'dmar.reservations@addresshotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'InterContinental Dubai Marina',
                'location' => 'dubai-marina',
                'star_rating' => 5,
                'short_description' => 'Premium marina-front hotel with yacht club views and waterfront dining',
                'description' => 'InterContinental Dubai Marina offers premium hospitality on the sparkling waterfront of Dubai Marina. With 328 elegantly appointed rooms and suites, guests enjoy panoramic views of superyachts and the Marina skyline. Accents Restaurant & Terrace serves international cuisine with marina views, while the rooftop pool provides a serene escape. Minutes from JBR Beach, Dubai Marina Mall, and the Marina Walk promenade.',
                'address' => 'King Salman Bin Abdulaziz Al Saud St, Dubai Marina',
                'latitude' => 25.0810,
                'longitude' => 55.1412,
                'phone' => '+971 4 446 6777',
                'email' => 'reservations@icdubaimarina.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 16, 18, 20, 21, 25, 27, 28],
            ],
            [
                'name' => 'Marina Byblos Hotel',
                'location' => 'dubai-marina',
                'star_rating' => 4,
                'short_description' => 'Boutique hotel in Dubai Marina with rooftop pool and marina promenade access',
                'description' => 'Marina Byblos Hotel is a stylish boutique property in the heart of Dubai Marina, offering 249 contemporary rooms with floor-to-ceiling views of the marina and city skyline. The rooftop infinity pool is a guest favourite, while Byblos Restaurant serves authentic Lebanese cuisine. Steps from the Marina Walk, Dubai Tram, and a short ride to JBR Beach.',
                'address' => 'Al Gharbi Street, Dubai Marina, Dubai',
                'latitude' => 25.0785,
                'longitude' => 55.1425,
                'phone' => '+971 4 304 3333',
                'email' => 'info@marinabybloshotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 14, 15, 17, 22, 25, 27, 28],
            ],

            // ── Downtown Dubai (3 hotels) ──
            [
                'name' => 'Address Downtown',
                'location' => 'downtown-dubai',
                'star_rating' => 5,
                'short_description' => 'Iconic landmark hotel with direct Burj Khalifa and Dubai Fountain views',
                'description' => 'Address Downtown is the iconic hotel at the heart of Downtown Dubai, connected to Dubai Mall and overlooking the world-famous Dubai Fountain. The 196 luxury rooms and suites feature contemporary Arabian design with floor-to-ceiling Burj Khalifa views. Guests enjoy The Cigar Lounge, Katana Robata Grill, Zeta rooftop bar, and an award-winning spa. The address for unforgettable Dubai experiences.',
                'address' => 'Mohammed Bin Rashid Boulevard, Downtown Dubai',
                'latitude' => 25.1940,
                'longitude' => 55.2795,
                'phone' => '+971 4 436 8888',
                'email' => 'dwtn.reservations@addresshotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 19, 20, 21, 25, 27, 28],
            ],
            [
                'name' => 'Vida Downtown Dubai',
                'location' => 'downtown-dubai',
                'star_rating' => 4,
                'short_description' => 'Contemporary lifestyle hotel with Burj Khalifa views and vibrant social spaces',
                'description' => 'Vida Downtown is a contemporary lifestyle hotel that brings energy and style to Downtown Dubai. With 175 rooms featuring smart technology and minimalist design, the hotel is a favourite among design-conscious travellers. The rooftop pool and bar offer stunning Burj Khalifa views, while the ground-floor café is one of Downtown\'s best. Connected to Dubai Mall via a short walk through the Boulevard.',
                'address' => 'Sheikh Mohammed Bin Rashid Blvd, Downtown Dubai',
                'latitude' => 25.1955,
                'longitude' => 55.2768,
                'phone' => '+971 4 428 6888',
                'email' => 'vida.downtown@addresshotels.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 13, 14, 15, 20, 22, 25, 27, 28],
            ],
            [
                'name' => 'Rove Downtown',
                'location' => 'downtown-dubai',
                'star_rating' => 3,
                'short_description' => 'Smart value hotel steps from Dubai Mall with rooftop pool and Burj Khalifa views',
                'description' => 'Rove Downtown brings smart, affordable hospitality to Dubai\'s most prestigious address. Just steps from Dubai Mall and the Burj Khalifa, this 420-room hotel offers comfortable Rover Rooms with rain showers, smart TVs, and complimentary WiFi. The rooftop pool has unbeatable Burj Khalifa views, The Daily restaurant serves international comfort food, and the 24/7 gym keeps you energised.',
                'address' => 'Financial Center Road, Downtown Dubai',
                'latitude' => 25.2010,
                'longitude' => 55.2738,
                'phone' => '+971 4 561 9999',
                'email' => 'downtown@rfrovehotels.com',
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
                    'meta_title' => "{$data['name']} — Book Now | Jumeira Beach Hotels",
                    'meta_description' => $data['short_description'] . '. Book your stay at ' . $data['name'] . ' with Jumeira Beach Hotels.',
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
            // Atlantis The Royal
            [
                ['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Atlantis The Royal exterior at sunset', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Resort beachfront aerial view'],
                ['url' => 'https://images.unsplash.com/photo-1618773928121-c32f082e2703?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxury suite with ocean view'],
                ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d955de201?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Infinity pool overlooking the gulf'],
                ['url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Fine dining restaurant'],
            ],
            // One&Only The Palm
            [
                ['url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'One&Only resort aerial view', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Resort pool with palm trees'],
                ['url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Elegant bedroom suite'],
                ['url' => 'https://images.unsplash.com/photo-1540541338287-41700207dee6?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Private beach and cabanas'],
                ['url' => 'https://images.unsplash.com/photo-1600011689032-8b628b8a8747?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Luxury spa treatment room'],
                ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Waterfront dining terrace'],
            ],
            // FIVE Palm Jumeirah
            [
                ['url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'FIVE Palm resort exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern luxury room'],
                ['url' => 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Vibrant pool party area'],
                ['url' => 'https://images.unsplash.com/photo-1507652313519-d4e9174996dd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Beach club sunset'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Stylish restaurant interior'],
                ['url' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Penthouse suite living area'],
            ],
            // Rixos Premium Dubai JBR
            [
                ['url' => 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Rixos Premium beachfront', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Premium room with sea view'],
                ['url' => 'https://images.unsplash.com/photo-1519449556851-5720b33024e7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Resort swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1568084680786-a84f91d1153c?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Private beach area'],
                ['url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'All-inclusive dining spread'],
                ['url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbec6d?w=1200&q=80', 'cat' => 'general', 'alt' => 'Beach and Ain Dubai view'],
            ],
            // Sofitel Dubai Jumeirah Beach
            [
                ['url' => 'https://images.unsplash.com/photo-1455587734955-081b22074882?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Sofitel hotel exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'French-inspired luxury room'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop infinity pool'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Elegant brasserie dining'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Stylish hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Bathroom with marble finishes'],
            ],
            // JA Oasis Beach Tower
            [
                ['url' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'JA Oasis Beach Tower exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Spacious apartment living room'],
                ['url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Full kitchen in apartment'],
                ['url' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Comfortable bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80', 'cat' => 'pool', 'alt' => 'JBR Beach access'],
            ],
            // Address Dubai Marina
            [
                ['url' => 'https://images.unsplash.com/photo-1549294413-26f195200c16?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Address Dubai Marina waterfront', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Signature room with marina view'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Contemporary hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1561501900-3701fa6a0864?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Marina-view infinity pool'],
                ['url' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb51f3a?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Restaurant waterfront dining'],
                ['url' => 'https://images.unsplash.com/photo-1590381105924-c72589b9ef3f?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxury bathroom amenities'],
            ],
            // InterContinental Dubai Marina
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'InterContinental Dubai Marina', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Premium marina view room'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool with city views'],
                ['url' => 'https://images.unsplash.com/photo-1600891964092-4316c288032e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Suite bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Accents restaurant interior'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Modern fitness centre'],
            ],
            // Marina Byblos Hotel
            [
                ['url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Marina Byblos Hotel exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern hotel room'],
                ['url' => 'https://images.unsplash.com/photo-1584132905271-512c958d674a?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool panorama'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Boutique hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Byblos Lebanese restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Twin room option'],
            ],
            // Address Downtown
            [
                ['url' => 'https://images.unsplash.com/photo-1470723710355-95304d8aece4?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Address Downtown with Burj Khalifa', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Room with Burj Khalifa view'],
                ['url' => 'https://images.unsplash.com/photo-1563911302283-d2bc129e7570?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Pool with Downtown skyline'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand Downtown lobby'],
                ['url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Rooftop bar dining'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Spa relaxation area'],
            ],
            // Vida Downtown Dubai
            [
                ['url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Vida Downtown modern exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Contemporary Vida room'],
                ['url' => 'https://images.unsplash.com/photo-1610641818989-c2051b5e2cfd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool Burj Khalifa view'],
                ['url' => 'https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Modern café and co-working'],
                ['url' => 'https://images.unsplash.com/photo-1604709177225-055f99402ea3?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Minimalist design room'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fitness centre'],
            ],
            // Rove Downtown
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Rove Downtown exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Smart Rover room'],
                ['url' => 'https://images.unsplash.com/photo-1578645510447-e20b4311e3ce?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool with Burj Khalifa'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'The Daily restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Fun and modern lobby'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => '24/7 fitness room'],
            ],
        ];

        foreach ($hotels as $hi => $hotel) {
            if ($hotel->images()->count() > 0) {
                $this->command->line("  Images for {$hotel->name} already exist, skipping");
                continue;
            }

            $images = $imagesByHotel[$hi] ?? [];
            foreach ($images as $ii => $img) {
                HotelImage::create([
                    'hotel_id' => $hotel->id,
                    'image_path' => $img['url'],
                    'thumbnail_path' => str_replace('w=1200', 'w=400', $img['url']),
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
            // 5-star beach hotels (Palm Jumeirah, JBR)
            'luxury-beach' => [
                ['name' => 'Deluxe Sea View Room', 'bed' => 'King', 'sqm' => 45, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1200, 'rooms' => 20],
                ['name' => 'Premium Suite', 'bed' => 'King', 'sqm' => 75, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2200, 'rooms' => 12],
                ['name' => 'One-Bedroom Beach Suite', 'bed' => 'King', 'sqm' => 95, 'guests' => 4, 'adults' => 2, 'children' => 2, 'price' => 3500, 'rooms' => 8],
                ['name' => 'Royal Penthouse', 'bed' => 'King + Twin', 'sqm' => 200, 'guests' => 6, 'adults' => 4, 'children' => 2, 'price' => 8500, 'rooms' => 3],
            ],
            // 5-star boutique (One&Only, FIVE)
            'luxury-boutique' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 50, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 1800, 'rooms' => 15],
                ['name' => 'Palm Suite', 'bed' => 'King', 'sqm' => 85, 'guests' => 3, 'adults' => 2, 'children' => 1, 'price' => 3200, 'rooms' => 8],
                ['name' => 'Beachfront Villa', 'bed' => 'King + Queen', 'sqm' => 160, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 6500, 'rooms' => 4],
                ['name' => 'Grand Villa with Pool', 'bed' => 'King + Twin + Twin', 'sqm' => 280, 'guests' => 8, 'adults' => 6, 'children' => 4, 'price' => 12000, 'rooms' => 2],
            ],
            // 5-star lifestyle (FIVE Palm)
            'luxury-lifestyle' => [
                ['name' => 'Luxe Room', 'bed' => 'King', 'sqm' => 42, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 950, 'rooms' => 25],
                ['name' => 'FIVE Suite', 'bed' => 'King', 'sqm' => 70, 'guests' => 3, 'adults' => 2, 'children' => 1, 'price' => 1800, 'rooms' => 12],
                ['name' => 'Duplex Penthouse', 'bed' => 'King + Queen', 'sqm' => 150, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 4500, 'rooms' => 5],
                ['name' => 'Ultra Penthouse', 'bed' => 'King + King + Twin', 'sqm' => 300, 'guests' => 8, 'adults' => 6, 'children' => 4, 'price' => 15000, 'rooms' => 2],
            ],
            // 5-star all-inclusive (Rixos)
            'luxury-allinclusive' => [
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1100, 'rooms' => 30],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 1600, 'rooms' => 15],
                ['name' => 'Family Suite', 'bed' => 'King + Twin', 'sqm' => 90, 'guests' => 5, 'adults' => 2, 'children' => 3, 'price' => 2800, 'rooms' => 8],
                ['name' => 'Presidential Suite', 'bed' => 'King + Queen', 'sqm' => 180, 'guests' => 6, 'adults' => 4, 'children' => 2, 'price' => 6000, 'rooms' => 2],
            ],
            // 5-star urban (Sofitel, Address)
            'luxury-urban' => [
                ['name' => 'Classic Room', 'bed' => 'King', 'sqm' => 38, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 900, 'rooms' => 25],
                ['name' => 'Luxury Room', 'bed' => 'King', 'sqm' => 50, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1400, 'rooms' => 15],
                ['name' => 'Opera Suite', 'bed' => 'King', 'sqm' => 80, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2500, 'rooms' => 8],
                ['name' => 'Prestige Suite', 'bed' => 'King + Queen', 'sqm' => 140, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 5000, 'rooms' => 3],
            ],
            // 4-star apartments (JA Oasis)
            'apartment-4star' => [
                ['name' => 'Studio Apartment', 'bed' => 'Queen', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 500, 'rooms' => 30],
                ['name' => 'One-Bedroom Apartment', 'bed' => 'King', 'sqm' => 65, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 750, 'rooms' => 20],
                ['name' => 'Two-Bedroom Apartment', 'bed' => 'King + Twin', 'sqm' => 100, 'guests' => 5, 'adults' => 4, 'children' => 3, 'price' => 1100, 'rooms' => 12],
                ['name' => 'Three-Bedroom Penthouse', 'bed' => 'King + Queen + Twin', 'sqm' => 160, 'guests' => 7, 'adults' => 6, 'children' => 4, 'price' => 1800, 'rooms' => 4],
            ],
            // 4-star boutique (Marina Byblos, Vida)
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 450, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1400, 'rooms' => 4],
            ],
            // 5-star marina (InterContinental, Address Marina)
            'luxury-marina' => [
                ['name' => 'Marina View Room', 'bed' => 'King', 'sqm' => 38, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 850, 'rooms' => 25],
                ['name' => 'Club InterContinental Room', 'bed' => 'King', 'sqm' => 48, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1300, 'rooms' => 15],
                ['name' => 'One-Bedroom Suite', 'bed' => 'King', 'sqm' => 75, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2200, 'rooms' => 8],
                ['name' => 'Ambassador Suite', 'bed' => 'King + Queen', 'sqm' => 130, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 4200, 'rooms' => 3],
            ],
            // 3-star value (Rove)
            'smart-3star' => [
                ['name' => 'Rover Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 280, 'rooms' => 50],
                ['name' => 'Rover Room Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 280, 'rooms' => 40],
                ['name' => 'Connecting Rover Rooms', 'bed' => 'Queen + Twin', 'sqm' => 48, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 520, 'rooms' => 15],
            ],
        ];

        // Map hotels to room templates
        $hotelTemplateMap = [
            0 => 'luxury-beach',         // Atlantis The Royal
            1 => 'luxury-boutique',       // One&Only The Palm
            2 => 'luxury-lifestyle',      // FIVE Palm Jumeirah
            3 => 'luxury-allinclusive',   // Rixos Premium Dubai JBR
            4 => 'luxury-urban',          // Sofitel Dubai Jumeirah Beach
            5 => 'apartment-4star',       // JA Oasis Beach Tower
            6 => 'luxury-marina',         // Address Dubai Marina
            7 => 'luxury-marina',         // InterContinental Dubai Marina
            8 => 'boutique-4star',        // Marina Byblos Hotel
            9 => 'luxury-urban',          // Address Downtown
            10 => 'boutique-4star',       // Vida Downtown Dubai
            11 => 'smart-3star',          // Rove Downtown
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

            $this->command->line("  Rooms: {$hotel->name} — " . count($rooms) . ' room types');
        }
    }

    // ─── Reviews ───────────────────────────────────────────────────────

    private function seedReviews(Domain $domain, array $hotels): void
    {
        $reviewData = [
            ['name' => 'Sarah Al Maktoum', 'email' => 'sarah.m@email.com', 'rating' => 5, 'title' => 'Absolutely incredible experience', 'comment' => 'From the moment we arrived, the service was impeccable. The room had the most stunning view of the Arabian Gulf, and the beach was pristine. The staff went above and beyond to make our anniversary special. Will definitely return!'],
            ['name' => 'James Richardson', 'email' => 'james.r@email.com', 'rating' => 5, 'title' => 'Best hotel in Dubai, hands down', 'comment' => 'I\'ve stayed at many luxury hotels worldwide, but this one truly stands out. The attention to detail, the quality of the restaurants, and the beach — everything was perfect. The concierge arranged a private yacht tour that was unforgettable.'],
            ['name' => 'Fatima Hassan', 'email' => 'fatima.h@email.com', 'rating' => 5, 'title' => 'Perfect family holiday', 'comment' => 'Travelled with three kids and it was seamless. The kids club was amazing — they didn\'t want to leave! The family suite was spacious and beautifully appointed. Beach was clean and safe. Highly recommend for families.'],
            ['name' => 'Oliver Bennett', 'email' => 'oliver.b@email.com', 'rating' => 4, 'title' => 'Stunning location and views', 'comment' => 'The location is unbeatable — waking up to the Gulf views every morning was magical. Room was well-appointed and clean. Restaurant options were excellent. Only minor issue was the wait time at the pool bar during peak hours.'],
            ['name' => 'Aisha Khan', 'email' => 'aisha.k@email.com', 'rating' => 5, 'title' => 'Luxury beyond expectations', 'comment' => 'Every detail was carefully thought out. The spa treatment was the best I\'ve ever had. The sunset from the rooftop pool is something you simply must experience. Staff remembered our names and preferences from day one.'],
            ['name' => 'Michael Chen', 'email' => 'michael.c@email.com', 'rating' => 4, 'title' => 'Great value for Dubai luxury', 'comment' => 'For what you get, the price is very reasonable compared to other 5-star hotels in the area. Room was modern and spacious. The breakfast spread was outstanding — so many options. Would definitely book again.'],
            ['name' => 'Priya Patel', 'email' => 'priya.p@email.com', 'rating' => 5, 'title' => 'A dream come true', 'comment' => 'My husband and I came for our honeymoon and it exceeded all expectations. The room was decorated with rose petals and champagne on arrival. The private beach dinner arranged by the hotel was magical under the stars.'],
            ['name' => 'Thomas Mueller', 'email' => 'thomas.m@email.com', 'rating' => 5, 'title' => 'Business trip turned luxury escape', 'comment' => 'Came for a conference but ended up extending my stay. The business facilities are top-notch, but the pool, spa, and restaurants made me forget I was here for work. The marina views from my room were spectacular.'],
            ['name' => 'Layla Al Rashid', 'email' => 'layla.r@email.com', 'rating' => 5, 'title' => 'Impeccable taste and service', 'comment' => 'The interior design is breathtaking — every corner is Instagram-worthy. The staff are incredibly attentive without being intrusive. The Friday brunch is the best in Dubai. A truly world-class property.'],
            ['name' => 'David Kim', 'email' => 'david.k@email.com', 'rating' => 4, 'title' => 'Modern luxury at its best', 'comment' => 'Clean, contemporary design with all the tech you\'d expect. The rooftop pool with Burj Khalifa views was the highlight. Walking distance to Dubai Mall was convenient. Breakfast could have more Asian options but overall excellent.'],
            ['name' => 'Emma Williams', 'email' => 'emma.w@email.com', 'rating' => 5, 'title' => 'The apartment was perfect', 'comment' => 'We stayed in a two-bedroom apartment and it felt like home but better! Full kitchen, washing machine, and the most comfortable beds. The kids loved the pool and beach. Location on JBR Walk meant we had everything at our doorstep.'],
            ['name' => 'Ahmed Al Nahyan', 'email' => 'ahmed.n@email.com', 'rating' => 5, 'title' => 'Outstanding hospitality', 'comment' => 'This hotel embodies true Arabian hospitality. The suite was magnificent, the spa treatment was world-class, and the private beach was serene. The concierge team arranged everything flawlessly. A true gem on the Palm.'],
            ['name' => 'Sophie Martin', 'email' => 'sophie.m@email.com', 'rating' => 4, 'title' => 'Wonderful beachfront stay', 'comment' => 'The beach access was the main reason we booked and it did not disappoint. Crystal clear water, clean sand, and attentive beach service. The room was comfortable and well-maintained. Great value for a beachfront property.'],
            ['name' => 'Raj Gupta', 'email' => 'raj.g@email.com', 'rating' => 5, 'title' => 'Best views in Dubai Marina', 'comment' => 'The marina view from our room was mesmerising, especially at night with all the lights reflecting on the water. Walking along the marina promenade after dinner was the perfect end to each day. World-class property.'],
            ['name' => 'Charlotte Brown', 'email' => 'charlotte.b@email.com', 'rating' => 4, 'title' => 'Smart stay near Burj Khalifa', 'comment' => 'For the price point, this hotel is unbeatable. The room was compact but cleverly designed with everything you need. The rooftop pool with Burj Khalifa views is worth the stay alone. Great restaurant downstairs too.'],
            ['name' => 'Hassan Mahmoud', 'email' => 'hassan.m@email.com', 'rating' => 5, 'title' => 'Five-star perfection', 'comment' => 'Nothing was too much trouble for the staff. The infinity pool was absolutely stunning, the rooms were immaculate, and the dining options were diverse and all excellent. This is what a true five-star experience should be.'],
            ['name' => 'Isabella Romano', 'email' => 'isabella.r@email.com', 'rating' => 5, 'title' => 'Romantic getaway perfection', 'comment' => 'We celebrated our 10th anniversary here and it was pure magic. The sunset dinner on the beach, couples spa treatment, and champagne upon arrival made it so special. The staff truly care about creating memorable experiences.'],
            ['name' => 'William Taylor', 'email' => 'william.t@email.com', 'rating' => 4, 'title' => 'Excellent marina location', 'comment' => 'Couldn\'t have asked for a better location. Steps from restaurants, the tram, and a short walk to the beach. The hotel itself is well-run with friendly staff. Room was clean and comfortable with great views.'],
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
}