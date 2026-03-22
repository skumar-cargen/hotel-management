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

class AjmanDiscountHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Ajman Discount Hotels data...');

        $domain = Domain::where('slug', 'ajman-discount-hotels')->firstOrFail();

        $locations = $this->seedLocations($domain);
        $hotels = $this->seedHotels($domain, $locations);
        $this->seedHotelImages($hotels);
        $this->seedRoomTypes($hotels);
        $this->seedReviews($domain, $hotels);

        foreach ($hotels as $hotel) {
            $minPrice = $hotel->roomTypes()->where('is_active', true)->min('base_price');
            if ($minPrice) {
                $hotel->update(['min_price' => $minPrice]);
            }
        }

        $this->command->info('Ajman Discount Hotels data seeded successfully!');
    }

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Ajman Corniche',
                'slug' => 'ajman-corniche',
                'city' => 'Ajman',
                'country' => 'UAE',
                'description' => 'Ajman Corniche is the emirate\'s stunning beachfront promenade along the Arabian Gulf, lined with five-star resorts offering pristine white sand and crystal-clear waters.',
                'short_description' => 'Premium beachfront with five-star resorts',
                'latitude' => 25.4052,
                'longitude' => 55.4445,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Ajman Corniche Hotels — Beach Resorts on the Arabian Gulf',
                'meta_description' => 'Book beachfront hotels on Ajman Corniche at discount prices.',
            ],
            [
                'name' => 'Al Nuaimia',
                'slug' => 'al-nuaimia',
                'city' => 'Ajman',
                'country' => 'UAE',
                'description' => 'Al Nuaimia is Ajman\'s main commercial district, home to City Centre Ajman mall and numerous restaurants.',
                'short_description' => 'Commercial hub with mall and dining',
                'latitude' => 25.3970,
                'longitude' => 55.4590,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Nuaimia Hotels — Central Ajman Accommodation',
                'meta_description' => 'Book hotels in Al Nuaimia, Ajman.',
            ],
            [
                'name' => 'Al Rashidiya Ajman',
                'slug' => 'al-rashidiya-ajman',
                'city' => 'Ajman',
                'country' => 'UAE',
                'description' => 'Al Rashidiya is a residential district near the Sharjah border, offering the most budget-friendly accommodation in the UAE.',
                'short_description' => 'Budget-friendly area near Sharjah border',
                'latitude' => 25.3930,
                'longitude' => 55.4680,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Rashidiya Hotels — Budget Ajman Hotels',
                'meta_description' => 'Book budget hotels in Al Rashidiya, Ajman.',
            ],
            [
                'name' => 'Al Jurf',
                'slug' => 'al-jurf',
                'city' => 'Ajman',
                'country' => 'UAE',
                'description' => 'Al Jurf is a commercial zone on the outskirts of Ajman with emerging business hotels at the lowest rates in the emirate.',
                'short_description' => 'Business district with lowest rates',
                'latitude' => 25.3780,
                'longitude' => 55.4750,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Al Jurf Hotels — Business Hotels in Ajman',
                'meta_description' => 'Book business hotels in Al Jurf, Ajman.',
            ],
        ];

        $locations = [];
        foreach ($locationData as $i => $data) {
            $location = Location::updateOrCreate(['slug' => $data['slug']], $data);
            $domain->locations()->syncWithoutDetaching([
                $location->id => ['is_active' => true, 'sort_order' => $i],
            ]);
            $locations[$data['slug']] = $location;
            $this->command->line("  Location: {$location->name}");
        }

        return $locations;
    }

    private function seedHotels(Domain $domain, array $locations): array
    {
        $hotelData = [
            [
                'name' => 'Ajman Saray, a Luxury Collection Resort',
                'location' => 'ajman-corniche',
                'star_rating' => 5,
                'short_description' => 'Luxury beachfront resort on Ajman Corniche with private beach, infinity pool, and Arabian decor',
                'description' => 'Ajman Saray is a stunning beachfront resort on Ajman Corniche. The 205-room resort features Arabian-inspired architecture, a 200-metre private beach, infinity pool, Bab Al Bahr seafood restaurant, and Dreamworks Spa.',
                'address' => 'Sheikh Humaid Bin Rashid Al Nuaimi Street, Ajman Corniche',
                'latitude' => 25.4085,
                'longitude' => 55.4410,
                'phone' => '+971 6 714 2222',
                'email' => 'reservations@ajmansaray.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 6, 7, 8, 9, 10, 13, 14, 15, 16, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Fairmont Ajman',
                'location' => 'ajman-corniche',
                'star_rating' => 5,
                'short_description' => 'Premium beach resort with 252 rooms, private beach, and spectacular Gulf sunset views',
                'description' => 'Fairmont Ajman offers 252 luxurious rooms on a pristine stretch of Ajman Corniche. Highlights include an infinity pool, 300-metre private beach, Spectrum dining, Kiyi Turkish restaurant, and Horizon Lounge with panoramic sunset views.',
                'address' => 'Ajman Corniche Road, Ajman',
                'latitude' => 25.4100,
                'longitude' => 55.4400,
                'phone' => '+971 6 701 5555',
                'email' => 'ajman@fairmont.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 6, 7, 8, 9, 10, 13, 14, 15, 16, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Radisson Blu Hotel Ajman',
                'location' => 'ajman-corniche',
                'star_rating' => 5,
                'short_description' => 'Contemporary beachfront hotel with ocean-view rooms, rooftop bar, and beach club',
                'description' => 'Radisson Blu Hotel Ajman sits on Ajman Corniche with 196 rooms, private beach, large outdoor pool, Larimar seafood restaurant, and the popular Taboo rooftop bar.',
                'address' => 'Corniche Road, Ajman',
                'latitude' => 25.4060,
                'longitude' => 55.4435,
                'phone' => '+971 6 745 3333',
                'email' => 'info.ajman@radissonblu.com',
                'is_beach_access' => true,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 6, 7, 8, 9, 10, 13, 14, 15, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Ramada Hotel & Suites Ajman',
                'location' => 'al-nuaimia',
                'star_rating' => 4,
                'short_description' => 'Family-friendly hotel near City Centre Ajman with pool and multiple dining options',
                'description' => 'Ramada Hotel & Suites Ajman offers 258 rooms walking distance to City Centre Ajman mall. Features outdoor pool, Flavours restaurant, China House Chinese, and kids play area.',
                'address' => 'Sheikh Khalifa Bin Zayed Street, Al Nuaimia, Ajman',
                'latitude' => 25.3975,
                'longitude' => 55.4585,
                'phone' => '+971 6 742 8888',
                'email' => 'reservations@ramadaajman.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 16, 17, 20, 21, 25, 27, 28],
            ],
            [
                'name' => 'Blazon Hotel Ajman',
                'location' => 'al-rashidiya-ajman',
                'star_rating' => 3,
                'short_description' => 'Smart budget hotel with pool and restaurant near Sharjah border',
                'description' => 'Blazon Hotel Ajman is a clean, modern budget hotel with 120 rooms at some of the lowest rates in the UAE. Features outdoor pool, Mezzanine restaurant, and free WiFi.',
                'address' => 'Al Ittihad Street, Al Rashidiya, Ajman',
                'latitude' => 25.3935,
                'longitude' => 55.4675,
                'phone' => '+971 6 740 7777',
                'email' => 'info@blazonhotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 13, 17, 25, 27, 28],
            ],
            [
                'name' => 'Wyndham Garden Ajman Corniche',
                'location' => 'al-jurf',
                'star_rating' => 4,
                'short_description' => 'Modern hotel with beach access, rooftop pool, and competitive corporate rates',
                'description' => 'Wyndham Garden Ajman Corniche offers 188 modern rooms with rooftop pool, Flavours restaurant, gym, and meeting facilities. Beach access via shuttle.',
                'address' => 'Al Jurf Industrial Area, Ajman',
                'latitude' => 25.3785,
                'longitude' => 55.4745,
                'phone' => '+971 6 714 1111',
                'email' => 'reservations@wyndhamajman.com',
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
                    'meta_title' => "{$data['name']} — Book Now | Ajman Discount Hotels",
                    'meta_description' => $data['short_description'] . '. Book with Ajman Discount Hotels.',
                    'cancellation_policy' => 'Free cancellation up to 48 hours before check-in.',
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

    private function seedHotelImages(array $hotels): void
    {
        $imagesByHotel = [
            [
                ['url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Ajman Saray beachfront resort', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxury sea-view suite'],
                ['url' => 'https://images.unsplash.com/photo-1561501900-3701fa6a0864?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Infinity pool Arabian Gulf'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Bab Al Bahr seafood restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80', 'cat' => 'general', 'alt' => 'Private beach at sunset'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Dreamworks Spa treatment room'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Fairmont Ajman resort', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1625244724120-1fd1d34d00f6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Premium Gulf view room'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Resort pool sunset'],
                ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Kiyi Turkish restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1593693397690-362cb9666fc2?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Grand resort lobby'],
                ['url' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Spa wellness centre'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Radisson Blu Hotel Ajman', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Ocean view guest room'],
                ['url' => 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Beachfront pool'],
                ['url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Taboo rooftop bar'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Contemporary hotel lobby'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fitness centre'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Ramada Hotel Ajman', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Family suite'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Flavours restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel reception'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Hotel gym'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Blazon Hotel Ajman', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Clean budget room'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Hotel pool'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Mezzanine restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Modern lobby'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'general', 'alt' => 'Hotel at night'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1529290130-4ca3753253ae?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Wyndham Garden Ajman', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern business room'],
                ['url' => 'https://images.unsplash.com/photo-1578645510447-e20b4311e3ce?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool sea views'],
                ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Flavours international'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Business-style lobby'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Equipped gym'],
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

    private function seedRoomTypes(array $hotels): void
    {
        $roomTemplates = [
            'luxury-beach' => [
                ['name' => 'Superior Sea View', 'bed' => 'King', 'sqm' => 42, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 30],
                ['name' => 'Deluxe Sea View', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 20],
                ['name' => 'Beach Suite', 'bed' => 'King', 'sqm' => 85, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 1800, 'rooms' => 8],
                ['name' => 'Royal Suite', 'bed' => 'King + Queen', 'sqm' => 160, 'guests' => 5, 'adults' => 4, 'children' => 3, 'price' => 4500, 'rooms' => 2],
            ],
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 350, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 500, 'rooms' => 15],
                ['name' => 'Family Suite', 'bed' => 'King + Twin', 'sqm' => 65, 'guests' => 4, 'adults' => 2, 'children' => 3, 'price' => 800, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1100, 'rooms' => 4],
            ],
            'smart-3star' => [
                ['name' => 'Standard Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 180, 'rooms' => 50],
                ['name' => 'Standard Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 180, 'rooms' => 40],
                ['name' => 'Family Room', 'bed' => 'Queen + Single', 'sqm' => 36, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 280, 'rooms' => 15],
            ],
        ];

        $hotelTemplateMap = [
            0 => 'luxury-beach',
            1 => 'luxury-beach',
            2 => 'luxury-beach',
            3 => 'boutique-4star',
            4 => 'smart-3star',
            5 => 'boutique-4star',
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
                    'description' => "Experience our {$room['name']} — {$room['sqm']} sqm with {$room['bed']} bed. Accommodates up to {$room['guests']} guests.",
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

    private function seedReviews(Domain $domain, array $hotels): void
    {
        $reviewData = [
            ['name' => 'Mariam Al Suwaidi', 'email' => 'mariam.s@email.com', 'rating' => 5, 'title' => 'Five-star luxury for the price of a Dubai 3-star', 'comment' => 'We could not believe the rate at Ajman Saray — a Luxury Collection resort for less than a basic Dubai hotel. The private beach was pristine, the infinity pool spectacular.'],
            ['name' => 'Thomas Fischer', 'email' => 'thomas.f@email.com', 'rating' => 5, 'title' => 'Best value beach resort in the UAE', 'comment' => 'Fairmont Ajman is incredible value. We paid AED 350 per night for a sea-view room at a five-star resort. The beach is beautiful, the pools are spotless, and Kiyi restaurant was outstanding.'],
            ['name' => 'Priya Patel', 'email' => 'priya.p@email.com', 'rating' => 4, 'title' => 'Perfect family beach holiday on a budget', 'comment' => 'We brought the whole family and Ajman was perfect. Safe beaches, affordable rooms, and the kids club kept the children happy. Only 30 minutes from Dubai.'],
            ['name' => 'Alexander Novak', 'email' => 'alexander.n@email.com', 'rating' => 5, 'title' => 'Taboo rooftop bar is worth the trip alone', 'comment' => 'The Radisson Blu Ajman was a great find. Clean rooms, beautiful beach, and the Taboo rooftop bar had the best sunset views in the UAE.'],
            ['name' => 'Sarah O\'Connor', 'email' => 'sarah.oc@email.com', 'rating' => 5, 'title' => 'Arabian Gulf sunsets are magical here', 'comment' => 'The sunset from Ajman Corniche is something special — the sky turns gold over the Arabian Gulf with no skyscrapers blocking the view. Paid less than half of what Dubai charges.'],
            ['name' => 'Khalid Al Suwaidi', 'email' => 'khalid.s@email.com', 'rating' => 4, 'title' => 'Great mid-range option for families', 'comment' => 'The Ramada was comfortable and well-priced. Walking distance to City Centre Ajman, clean pool area, and spacious rooms.'],
            ['name' => 'Emma Johansson', 'email' => 'emma.j@email.com', 'rating' => 4, 'title' => 'Budget beach break', 'comment' => 'We booked the cheapest hotel and were pleasantly surprised. Basic but clean room, refreshing pool, and the corniche was a short drive away. For AED 150 per night, cannot complain.'],
            ['name' => 'Mohammed Al Rashidi', 'email' => 'mohammed.r@email.com', 'rating' => 5, 'title' => 'Best kept secret in the UAE', 'comment' => 'Five-star beach resorts, crystal clear Gulf waters, pristine sand, and prices that make you question why anyone stays in Dubai Marina.'],
            ['name' => 'Lisa Chen', 'email' => 'lisa.c@email.com', 'rating' => 5, 'title' => 'Romantic Gulf-side escape', 'comment' => 'My husband surprised me with a weekend at the Fairmont Ajman and it was absolutely perfect. The room overlooked the Gulf, the Italian restaurant was exquisite.'],
            ['name' => 'David Williams', 'email' => 'david.w@email.com', 'rating' => 4, 'title' => 'Affordable luxury on the Gulf', 'comment' => 'The Radisson Blu offered everything we needed — lovely beach, good pool, comfortable room with Gulf views. At these prices, Ajman is an absolute steal.'],
            ['name' => 'Fatima Al Ketbi', 'email' => 'fatima.k@email.com', 'rating' => 5, 'title' => 'Staycation paradise', 'comment' => 'We live in Dubai but regularly escape to Ajman for weekends. We get a suite at the Fairmont for what a standard room costs in Dubai.'],
            ['name' => 'Ahmed Hassan', 'email' => 'ahmed.h@email.com', 'rating' => 5, 'title' => 'The family loved every minute', 'comment' => 'Three nights at the Fairmont Ajman and the family did not want to leave. The kids spent all day between the beach and the pool.'],
        ];

        $reviewIndex = 0;
        foreach ($hotels as $hotel) {
            if ($hotel->reviews()->count() > 0) {
                $this->command->line("  Reviews for {$hotel->name} already exist, skipping");
                continue;
            }

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

                $domain->testimonials()->syncWithoutDetaching([
                    $review->id => ['sort_order' => $reviewIndex],
                ]);

                $reviewIndex++;
            }

            $hotel->update([
                'avg_rating' => round($hotel->reviews()->avg('rating'), 1),
                'total_reviews' => $hotel->reviews()->count(),
            ]);

            $this->command->line("  Reviews: {$hotel->name} — {$count} reviews");
        }
    }

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
