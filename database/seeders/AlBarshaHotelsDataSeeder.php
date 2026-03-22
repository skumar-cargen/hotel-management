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

class AlBarshaHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Al Barsha Hotels data (locations, hotels, rooms, images, reviews)...');

        $domain = Domain::where('slug', 'al-barsha-hotels')->firstOrFail();

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

        $this->command->info('Al Barsha Hotels data seeded successfully!');
    }

    // ─── Locations ─────────────────────────────────────────────────────

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Al Barsha 1',
                'slug' => 'al-barsha-1',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'The heart of Al Barsha, home to Mall of the Emirates and Ski Dubai. A bustling commercial and residential area with excellent Metro connectivity and diverse dining options.',
                'short_description' => 'Home to Mall of the Emirates and Ski Dubai',
                'latitude' => 25.1171,
                'longitude' => 55.1943,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Barsha 1 Hotels — Stay Near Mall of the Emirates, Dubai',
                'meta_description' => 'Book hotels in Al Barsha 1, Dubai. Walking distance to Mall of the Emirates, Ski Dubai, and the Metro. Great value in a prime location.',
            ],
            [
                'name' => 'Al Barsha South',
                'slug' => 'al-barsha-south',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'A quieter residential neighbourhood south of Sheikh Zayed Road, offering modern apartment hotels and easy access to Dubai Sports City, Motor City, and Arabian Ranches.',
                'short_description' => 'Peaceful residential area with modern hotels',
                'latitude' => 25.0980,
                'longitude' => 55.1900,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Barsha South Hotels — Affordable Stays in South Dubai',
                'meta_description' => 'Stay in Al Barsha South for a quieter Dubai experience. Modern hotels near Dubai Sports City and Arabian Ranches.',
            ],
            [
                'name' => 'Barsha Heights (TECOM)',
                'slug' => 'barsha-heights-tecom',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'Formerly known as TECOM, this business-friendly district is home to Dubai Internet City, Dubai Media City, and Knowledge Village. Popular with business travellers and tech professionals.',
                'short_description' => 'Business hub near Internet City and Media City',
                'latitude' => 25.0950,
                'longitude' => 55.1700,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Barsha Heights Hotels — Business Hotels Near TECOM, Dubai',
                'meta_description' => 'Book hotels in Barsha Heights (TECOM). Close to Dubai Internet City, Media City, and Knowledge Village. Ideal for business travellers.',
            ],
            [
                'name' => 'Al Quoz',
                'slug' => 'al-quoz',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'description' => 'Dubai\'s creative and industrial district, home to Alserkal Avenue art galleries, trendy cafés, and unique boutique accommodations. A neighbourhood on the rise with an artsy, authentic vibe.',
                'short_description' => 'Creative arts district with Alserkal Avenue',
                'latitude' => 25.1400,
                'longitude' => 55.2200,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Quoz Hotels — Stay in Dubai\'s Creative Arts District',
                'meta_description' => 'Discover hotels in Al Quoz, Dubai\'s arts and culture hub. Near Alserkal Avenue galleries, trendy cafés, and unique experiences.',
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
            // ── Al Barsha 1 (4 hotels) ──
            [
                'name' => 'Kempinski Hotel Mall of the Emirates',
                'location' => 'al-barsha-1',
                'star_rating' => 5,
                'short_description' => 'Five-star luxury directly connected to Mall of the Emirates',
                'description' => 'Kempinski Hotel Mall of the Emirates offers the ultimate luxury experience with direct access to one of Dubai\'s largest shopping malls. Featuring 393 spacious rooms and suites with views of the city skyline, the hotel boasts an alpine-themed Aspen restaurant, K Spa & Health Club, and a stunning rooftop pool. Guests can walk directly into Mall of the Emirates for shopping at 630+ stores, Ski Dubai, or VOX Cinemas.',
                'address' => 'Mall of the Emirates, Sheikh Zayed Road, Al Barsha 1, Dubai',
                'latitude' => 25.1178,
                'longitude' => 55.1999,
                'phone' => '+971 4 341 0000',
                'email' => 'reservations.modubai@kempinski.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 19, 22, 26, 27, 28],
            ],
            [
                'name' => 'Sheraton Mall of the Emirates Hotel',
                'location' => 'al-barsha-1',
                'star_rating' => 5,
                'short_description' => 'Elegant Sheraton hotel with direct mall access and rooftop pool',
                'description' => 'Sheraton Mall of the Emirates Hotel, part of the Marriott Bonvoy family, offers premium comfort with direct access to Mall of the Emirates. The 481-room property features The Terrace restaurant, an expansive spa, state-of-the-art fitness centre, and a rooftop pool with panoramic views. Perfectly positioned on Sheikh Zayed Road, it\'s ideal for both business and leisure travellers.',
                'address' => 'Sheikh Zayed Road, Al Barsha 1, Dubai',
                'latitude' => 25.1185,
                'longitude' => 55.2010,
                'phone' => '+971 4 377 2000',
                'email' => 'reservations@sheratonmoe.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'Novotel Al Barsha',
                'location' => 'al-barsha-1',
                'star_rating' => 4,
                'short_description' => 'Modern four-star hotel with pool, gym, and Mall of the Emirates access',
                'description' => 'Novotel Al Barsha offers smart, contemporary accommodation in the heart of Al Barsha 1. With 468 well-appointed rooms, an outdoor pool, fully equipped fitness centre, and multiple dining options, this Accor property is perfect for value-conscious travellers who don\'t want to compromise on quality. Mall of the Emirates and the Metro are a short walk away.',
                'address' => 'Al Barsha 1, Near Mall of the Emirates, Dubai',
                'latitude' => 25.1160,
                'longitude' => 55.1930,
                'phone' => '+971 4 309 5555',
                'email' => 'H8072@accor.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 9, 10, 13, 14, 15, 22, 26, 27, 28],
            ],
            [
                'name' => 'Holiday Inn Al Barsha',
                'location' => 'al-barsha-1',
                'star_rating' => 3,
                'short_description' => 'Reliable IHG hotel with great rates near Mall of the Emirates',
                'description' => 'Holiday Inn Al Barsha delivers the consistent quality and comfort IHG is known for, at competitive rates. Located within walking distance of Mall of the Emirates and Sharaf DG Metro Station, this 300-room hotel features a rooftop pool, gym, and two restaurants. An excellent choice for business travellers and families looking for dependable accommodation in a prime location.',
                'address' => 'Al Barsha 1, Near Sharaf DG Metro, Dubai',
                'latitude' => 25.1155,
                'longitude' => 55.1920,
                'phone' => '+971 4 381 1111',
                'email' => 'info@hibarsha.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 10, 13, 14, 22, 26, 27, 28],
            ],

            // ── Al Barsha South (3 hotels) ──
            [
                'name' => 'Studio One Hotel',
                'location' => 'al-barsha-south',
                'star_rating' => 4,
                'short_description' => 'Trendy boutique hotel with art-inspired design and rooftop pool',
                'description' => 'Studio One Hotel is a stylish boutique property that brings creative energy to Al Barsha South. Inspired by the golden age of Hollywood, the hotel features 131 artfully designed rooms, a stunning rooftop infinity pool, a vibrant all-day dining restaurant, and a trendy lobby lounge. Located near Dubai Studio City, it\'s perfect for creative professionals and design-savvy travellers.',
                'address' => 'Dubai Studio City, Al Barsha South, Dubai',
                'latitude' => 25.0410,
                'longitude' => 55.1870,
                'phone' => '+971 4 581 0000',
                'email' => 'stay@studioonehotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 9, 10, 13, 14, 22, 26, 27],
            ],
            [
                'name' => 'Ghaya Grand Hotel',
                'location' => 'al-barsha-south',
                'star_rating' => 4,
                'short_description' => 'Spacious apartment hotel with kitchen suites and family-friendly amenities',
                'description' => 'Ghaya Grand Hotel offers the space of an apartment with the service of a hotel. Each of its 388 rooms and suites features a kitchenette or full kitchen, making it ideal for extended stays and families. The hotel boasts a large outdoor pool, children\'s play area, gymnasium, and multiple dining options. Conveniently located near Dubai Sports City and Motor City.',
                'address' => 'Al Barsha South 3, Dubai',
                'latitude' => 25.0450,
                'longitude' => 55.1920,
                'phone' => '+971 4 399 8888',
                'email' => 'reservations@ghayagrand.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 17, 22, 23, 24, 26, 27, 28],
            ],
            [
                'name' => 'Rose Park Hotel Al Barsha',
                'location' => 'al-barsha-south',
                'star_rating' => 3,
                'short_description' => 'Budget-friendly hotel with modern rooms and easy mall access',
                'description' => 'Rose Park Hotel Al Barsha offers clean, modern accommodation at attractive rates. With 198 rooms, an outdoor pool, a fitness centre, and an all-day dining restaurant, the hotel provides excellent value for money. A complimentary shuttle runs to Mall of the Emirates and popular beaches, making it a smart base for budget-conscious travellers who still want to experience the best of Dubai.',
                'address' => 'Al Barsha South 2, Near Dubai Autodrome, Dubai',
                'latitude' => 25.0430,
                'longitude' => 55.1900,
                'phone' => '+971 4 322 5678',
                'email' => 'reservations@roseparkhotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 10, 14, 22, 26, 27],
            ],

            // ── Barsha Heights / TECOM (3 hotels) ──
            [
                'name' => 'Media One Hotel',
                'location' => 'barsha-heights-tecom',
                'star_rating' => 4,
                'short_description' => 'Award-winning lifestyle hotel in the heart of Media City',
                'description' => 'Media One Hotel is a vibrant, award-winning lifestyle hotel in the heart of Dubai Media City. Known for its trendy QD\'s rooftop bar, Japanese-inspired Noodle House, and energetic atmosphere, the 580-room hotel attracts media professionals, creatives, and savvy travellers. Features include a stunning pool deck, state-of-the-art gym, and direct access to Dubai Internet City Metro Station.',
                'address' => 'Dubai Media City, Barsha Heights, Dubai',
                'latitude' => 25.0975,
                'longitude' => 55.1580,
                'phone' => '+971 4 427 1000',
                'email' => 'reservations@mediaonehotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 9, 10, 13, 14, 15, 22, 26, 27, 28],
            ],
            [
                'name' => 'Pullman Dubai City Center Deira & Residence',
                'location' => 'barsha-heights-tecom',
                'star_rating' => 5,
                'short_description' => 'Premium Accor hotel with panoramic views and executive lounges',
                'description' => 'Grand Millennium Dubai in Barsha Heights offers five-star hospitality across 500+ rooms and suites with floor-to-ceiling windows offering panoramic city views. The hotel features multiple dining outlets including Toshi — an acclaimed Japanese restaurant — plus a full-service spa, infinity pool, and executive club lounge. It\'s a favourite among business travellers visiting Dubai Internet City and Knowledge Village.',
                'address' => 'Barsha Heights (TECOM), Al Sufouh Road, Dubai',
                'latitude' => 25.0940,
                'longitude' => 55.1650,
                'phone' => '+971 4 429 9999',
                'email' => 'info@grandmillenniumdubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 22, 26, 27, 28],
            ],
            [
                'name' => 'Mövenpick Hotel Apartments Al Barsha',
                'location' => 'barsha-heights-tecom',
                'star_rating' => 4,
                'short_description' => 'Swiss-quality serviced apartments with full kitchens and pool',
                'description' => 'Mövenpick Hotel Apartments Al Barsha offers the legendary Swiss hospitality in spacious, fully equipped apartments. Each of the 220 units features a full kitchen, separate living area, and washer/dryer — perfect for extended stays. The property includes an outdoor pool, sauna, gym, and the signature Nosh restaurant. Complimentary shuttle service to Mall of the Emirates and JBR Beach.',
                'address' => 'Barsha Heights (TECOM), Dubai',
                'latitude' => 25.0960,
                'longitude' => 55.1680,
                'phone' => '+971 4 450 1234',
                'email' => 'hotel.albarsha.apartments@movenpick.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 17, 22, 23, 24, 26, 27, 28],
            ],

            // ── Al Quoz (2 hotels) ──
            [
                'name' => 'Alserkal Arts Hotel',
                'location' => 'al-quoz',
                'star_rating' => 4,
                'short_description' => 'Boutique art hotel steps from Alserkal Avenue galleries',
                'description' => 'Located in the creative heart of Al Quoz, Alserkal Arts Hotel is a design-forward boutique property that celebrates Dubai\'s thriving arts scene. Each of its 85 rooms is inspired by a different UAE artist, with rotating exhibitions in the lobby gallery. The hotel features a courtyard café, rooftop terrace with industrial-chic décor, and a curated art shop. Steps from Alserkal Avenue\'s world-renowned galleries and warehouses.',
                'address' => 'Al Quoz Industrial Area 1, Near Alserkal Avenue, Dubai',
                'latitude' => 25.1420,
                'longitude' => 55.2230,
                'phone' => '+971 4 333 4444',
                'email' => 'stay@alserkalarts.hotel',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '11:00',
                'amenities' => [1, 2, 4, 7, 8, 10, 13, 14, 22, 26, 27],
            ],
            [
                'name' => 'The Warehouse Hotel Al Quoz',
                'location' => 'al-quoz',
                'star_rating' => 3,
                'short_description' => 'Industrial-chic hotel in a converted warehouse with café culture',
                'description' => 'The Warehouse Hotel Al Quoz is a unique conversion of an industrial warehouse into a 120-room boutique hotel. Exposed brick, steel beams, and polished concrete meet modern comfort in this Instagram-worthy property. The ground-floor Grind café serves specialty coffee and all-day brunch, while the courtyard hosts pop-up food markets and live music on weekends. Perfect for creative types and off-the-beaten-path travellers.',
                'address' => 'Al Quoz 3, Near Times Square Center, Dubai',
                'latitude' => 25.1380,
                'longitude' => 55.2180,
                'phone' => '+971 4 333 5555',
                'email' => 'hello@warehousehotelquoz.com',
                'is_beach_access' => false,
                'is_family_friendly' => false,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '11:00',
                'amenities' => [1, 2, 4, 7, 8, 10, 14, 22, 26, 27],
            ],
        ];

        $hotels = [];
        foreach ($hotelData as $i => $data) {
            $locationSlug = $data['location'];
            $location = $locations[$locationSlug] ?? null;

            if (! $location) {
                $this->command->warn("  Skipping {$data['name']} — location '{$locationSlug}' not found");

                continue;
            }

            $amenityIds = $data['amenities'] ?? [];
            unset($data['location'], $data['amenities']);

            $hotel = Hotel::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'location_id' => $location->id,
                    'is_active' => true,
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

    // ─── Hotel Images ──────────────────────────────────────────────────

    private function seedHotelImages(array $hotels): void
    {
        $imageCategories = [
            ['category' => 'exterior', 'keywords' => 'hotel,lobby,luxury'],
            ['category' => 'room', 'keywords' => 'hotel,room,modern'],
            ['category' => 'pool', 'keywords' => 'hotel,pool,rooftop'],
            ['category' => 'dining', 'keywords' => 'restaurant,dining,elegant'],
            ['category' => 'bathroom', 'keywords' => 'hotel,bathroom,marble'],
        ];

        $counter = 300;

        foreach ($hotels as $hotel) {
            if ($hotel->images()->count() >= 3) {
                continue;
            }

            Storage::disk('public')->makeDirectory("hotels/{$hotel->id}");

            foreach ($imageCategories as $j => $img) {
                $url = "https://loremflickr.com/800/600/{$img['keywords']}?lock=" . $counter++;

                $filename = "{$img['category']}-{$j}.jpg";
                $thumbFilename = "{$img['category']}-{$j}-thumb.jpg";
                $storagePath = "hotels/{$hotel->id}/{$filename}";
                $thumbStoragePath = "hotels/{$hotel->id}/{$thumbFilename}";
                $fullPath = Storage::disk('public')->path($storagePath);
                $thumbFullPath = Storage::disk('public')->path($thumbStoragePath);

                $this->downloadHotelImage($fullPath, $url);
                $this->downloadHotelImage($thumbFullPath, str_replace('800/600', '400/300', $url));

                HotelImage::updateOrCreate(
                    ['hotel_id' => $hotel->id, 'sort_order' => $j],
                    [
                        'image_path' => $storagePath,
                        'thumbnail_path' => $thumbStoragePath,
                        'category' => $img['category'],
                        'alt_text' => "{$hotel->name} — " . ucfirst($img['category']),
                        'is_primary' => $j === 0,
                    ]
                );
            }
        }

        $this->command->line('  Hotel images seeded');
    }

    // ─── Room Types ────────────────────────────────────────────────────

    private function seedRoomTypes(array $hotels): void
    {
        $roomTemplates = [
            ['name' => 'Standard Room', 'base_price' => 350, 'max_guests' => 2, 'beds' => '1 King or 2 Twin', 'size' => 28],
            ['name' => 'Deluxe Room', 'base_price' => 550, 'max_guests' => 2, 'beds' => '1 King', 'size' => 36],
            ['name' => 'Superior Suite', 'base_price' => 850, 'max_guests' => 3, 'beds' => '1 King + Sofa Bed', 'size' => 52],
            ['name' => 'Executive Suite', 'base_price' => 1200, 'max_guests' => 3, 'beds' => '1 King + Living Area', 'size' => 68],
            ['name' => 'Family Room', 'base_price' => 650, 'max_guests' => 4, 'beds' => '2 Double Beds', 'size' => 42],
        ];

        foreach ($hotels as $hotel) {
            if ($hotel->roomTypes()->count() > 0) {
                continue;
            }

            $priceMultiplier = match (true) {
                $hotel->star_rating >= 5 => 1.8,
                $hotel->star_rating >= 4 => 1.2,
                default => 1.0,
            };

            $numRooms = match (true) {
                $hotel->star_rating >= 5 => 5,
                $hotel->star_rating >= 4 => 4,
                default => 3,
            };

            foreach (array_slice($roomTemplates, 0, $numRooms) as $j => $tpl) {
                $price = round($tpl['base_price'] * $priceMultiplier, -1);

                RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => $tpl['name'],
                    'slug' => Str::slug($tpl['name']) . '-' . $hotel->id,
                    'description' => "Comfortable {$tpl['name']} at {$hotel->name} with modern amenities and {$tpl['beds']}.",
                    'base_price' => $price,
                    'max_guests' => $tpl['max_guests'],
                    'bed_type' => $tpl['beds'],
                    'room_size_sqm' => $tpl['size'],
                    'is_active' => true,
                    'sort_order' => $j,
                ]);
            }
        }

        $this->command->line('  Room types seeded');
    }

    // ─── Reviews ───────────────────────────────────────────────────────

    private function seedReviews(Domain $domain, array $hotels): void
    {
        $reviewerPool = [
            ['name' => 'Ahmed Al-Rashid', 'country' => 'UAE'],
            ['name' => 'Sarah Johnson', 'country' => 'United Kingdom'],
            ['name' => 'Marco Rossi', 'country' => 'Italy'],
            ['name' => 'Fatima Hassan', 'country' => 'Jordan'],
            ['name' => 'John Mitchell', 'country' => 'United States'],
            ['name' => 'Priya Sharma', 'country' => 'India'],
            ['name' => 'Hans Weber', 'country' => 'Germany'],
            ['name' => 'Yuki Tanaka', 'country' => 'Japan'],
            ['name' => 'Maria Santos', 'country' => 'Brazil'],
            ['name' => 'Chen Wei', 'country' => 'China'],
            ['name' => 'Olga Petrova', 'country' => 'Russia'],
            ['name' => 'Mohamed Ali', 'country' => 'Egypt'],
            ['name' => 'Emma Williams', 'country' => 'Australia'],
            ['name' => 'Ravi Patel', 'country' => 'India'],
            ['name' => 'Sophie Dubois', 'country' => 'France'],
            ['name' => 'Khalid Al-Mansoori', 'country' => 'UAE'],
        ];

        $comments = [
            'Excellent location near Mall of the Emirates. Very convenient for shopping and dining. Staff were incredibly helpful.',
            'Great value for money in Al Barsha. The room was clean, modern, and well-equipped. Would definitely come back.',
            'Perfect base for exploring Dubai. The Metro station is just a short walk away. Breakfast buffet was outstanding.',
            'Loved the rooftop pool with city views. Room was spacious and the bed was extremely comfortable.',
            'Professional and friendly staff. The hotel arranged airport transfer which was very convenient.',
            'Stayed for a business trip — excellent WiFi, quiet rooms, and the business centre was well-equipped.',
            'Family-friendly hotel with great amenities. Kids loved the pool and the restaurant had good options for children.',
            'Beautiful modern design and great attention to detail. The lobby lounge had amazing coffee.',
            'Super clean and well-maintained. The gym was modern and had everything I needed.',
            'Walking distance to Mall of the Emirates was a huge plus. We saved so much on transportation.',
            'The restaurant served authentic international cuisine. Breakfast had both Arabic and Western options.',
            'Quiet room despite being in a busy area. Blackout curtains were perfect for sleeping.',
            'Excellent concierge service — they helped us book desert safari and dinner cruise tickets.',
            'Room service was prompt and the food quality was consistent. Late check-out was accommodated.',
            'The spa was a wonderful surprise. Had a great massage after a long day of sightseeing.',
            'Check-in was smooth and fast. They upgraded our room which was a lovely gesture.',
        ];

        $reviewerIndex = 0;

        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() >= 4) {
                continue;
            }

            $numReviews = rand(4, 6);

            for ($r = 0; $r < $numReviews; $r++) {
                $reviewer = $reviewerPool[$reviewerIndex % count($reviewerPool)];
                $comment = $comments[$reviewerIndex % count($comments)];
                $rating = collect([4.0, 4.2, 4.5, 4.6, 4.8, 5.0])->random();

                Review::create([
                    'hotel_id' => $hotel->id,
                    'guest_name' => $reviewer['name'],
                    'rating' => $rating,
                    'title' => 'Great stay at ' . $hotel->name,
                    'comment' => $comment,
                    'is_approved' => true,
                ]);

                $reviewerIndex++;
            }
        }

        // Link reviews as testimonials
        $reviews = Review::whereIn('hotel_id', collect($hotels)->pluck('id'))
            ->where('rating', '>=', 4.5)
            ->inRandomOrder()
            ->limit(12)
            ->get();

        foreach ($reviews as $i => $review) {
            $domain->testimonials()->syncWithoutDetaching([
                $review->id => ['sort_order' => $i],
            ]);
        }

        $this->command->line("  Reviews seeded & {$reviews->count()} testimonials linked");
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
