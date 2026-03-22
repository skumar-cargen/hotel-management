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

class BurDubaiHotelsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Bur Dubai Hotels data...');

        $domain = Domain::where('slug', 'bur-dubai-hotels')->firstOrFail();

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

        $this->command->info('Bur Dubai Hotels data seeded successfully!');
    }

    private function seedLocations(Domain $domain): array
    {
        $locationData = [
            [
                'name' => 'Al Fahidi',
                'slug' => 'al-fahidi',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Al Fahidi Historical Neighbourhood is the cultural heart of old Dubai. Narrow sikkas wind between restored wind-tower houses, many now converted into art galleries, cafes, and boutique museums.',
                'short_description' => 'Historic wind-tower district and cultural quarter',
                'latitude' => 25.2636,
                'longitude' => 55.2978,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Al Fahidi Hotels — Heritage Stays in Old Dubai',
                'meta_description' => 'Book hotels in Al Fahidi Historical Neighbourhood, Bur Dubai.',
            ],
            [
                'name' => 'Meena Bazaar',
                'slug' => 'meena-bazaar',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Meena Bazaar is Bur Dubai\'s legendary textile and shopping district with fabric shops, Indian restaurants, and gold jewellery stores.',
                'short_description' => 'Legendary textile and shopping district',
                'latitude' => 25.2620,
                'longitude' => 55.2955,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Meena Bazaar Hotels — Stay in Dubai\'s Shopping Heart',
                'meta_description' => 'Book hotels near Meena Bazaar, Bur Dubai.',
            ],
            [
                'name' => 'Oud Metha',
                'slug' => 'oud-metha',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Oud Metha is home to Wafi Mall, Raffles Hotel, and the historic Lamcy Plaza. The area blends luxury with heritage.',
                'short_description' => 'Home to Wafi Mall and luxury landmarks',
                'latitude' => 25.2400,
                'longitude' => 55.3120,
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Oud Metha Hotels — Luxury Stays Near Wafi Mall',
                'meta_description' => 'Book hotels in Oud Metha, Dubai.',
            ],
            [
                'name' => 'Karama',
                'slug' => 'karama',
                'city' => 'Dubai',
                'country' => 'UAE',
                'description' => 'Al Karama is one of Dubai\'s most diverse and affordable residential areas, known for Karama Market and dozens of international restaurants.',
                'short_description' => 'Diverse, affordable neighbourhood with great dining',
                'latitude' => 25.2470,
                'longitude' => 55.3050,
                'is_active' => true,
                'is_featured' => false,
                'meta_title' => 'Karama Hotels — Budget-Friendly Dubai Stays',
                'meta_description' => 'Book affordable hotels in Karama, Dubai.',
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
                'name' => 'XVA Art Hotel',
                'location' => 'al-fahidi',
                'star_rating' => 4,
                'short_description' => 'Boutique art hotel in a restored heritage house with rooftop gallery and vegetarian cafe',
                'description' => 'XVA Art Hotel is one of Dubai\'s most unique stays — a restored wind-tower house in the heart of Al Fahidi. Each of the 14 rooms is a curated art installation. The rooftop terrace serves vegetarian cuisine with Creek views.',
                'address' => 'Al Fahidi Historical Neighbourhood, Bur Dubai, Dubai',
                'latitude' => 25.2636,
                'longitude' => 55.2975,
                'phone' => '+971 4 353 5383',
                'email' => 'info@xvahotel.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 7, 8, 13, 17, 25, 27, 28],
            ],
            [
                'name' => 'Arabian Courtyard Hotel & Spa',
                'location' => 'al-fahidi',
                'star_rating' => 4,
                'short_description' => 'Heritage-style hotel opposite Dubai Museum with full-service spa and rooftop pool',
                'description' => 'Arabian Courtyard Hotel stands directly opposite the Dubai Museum and Al Fahidi Fort. Features 173 rooms, a Moroccan-inspired spa, rooftop pool, and Sherlock Holmes pub.',
                'address' => 'Al Fahidi Street, opposite Dubai Museum, Bur Dubai',
                'latitude' => 25.2632,
                'longitude' => 55.2985,
                'phone' => '+971 4 351 9111',
                'email' => 'reservations@arabiancourtyard.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 17, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Raffles Dubai',
                'location' => 'oud-metha',
                'star_rating' => 5,
                'short_description' => 'Iconic pyramid-shaped luxury hotel connected to Wafi Mall with botanical garden',
                'description' => 'Raffles Dubai is an architectural icon — a glass pyramid rising above Wafi Mall. The 248-suite hotel offers panoramic views, a lavish botanical garden, Azur restaurant, and the award-winning Raffles Spa.',
                'address' => 'Sheikh Rashid Road, Wafi, Oud Metha, Dubai',
                'latitude' => 25.2310,
                'longitude' => 55.3155,
                'phone' => '+971 4 324 8888',
                'email' => 'dubai@raffles.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 13, 14, 15, 18, 19, 20, 21, 22, 25, 26, 27, 28],
            ],
            [
                'name' => 'Grand Hyatt Dubai',
                'location' => 'oud-metha',
                'star_rating' => 5,
                'short_description' => 'Grand resort-style hotel with 13 restaurants, indoor rainforest, and lagoon pool',
                'description' => 'Grand Hyatt Dubai is a sprawling resort-style hotel with 674 rooms, 13 restaurants, a tropical indoor rainforest lobby, and free-form lagoon pool. Overlooks Creek Park.',
                'address' => 'Sheikh Rashid Road, Oud Metha, Dubai',
                'latitude' => 25.2340,
                'longitude' => 55.3130,
                'phone' => '+971 4 317 1234',
                'email' => 'granddubai@hyatt.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 4, 5, 7, 8, 9, 10, 13, 14, 15, 16, 18, 20, 21, 22, 25, 27, 28],
            ],
            [
                'name' => 'Four Points by Sheraton Bur Dubai',
                'location' => 'meena-bazaar',
                'star_rating' => 4,
                'short_description' => 'Modern business hotel in Bur Dubai\'s shopping heart with rooftop pool',
                'description' => 'Four Points offers 125 modern rooms in the heart of Meena Bazaar. Features Asha\'s restaurant, rooftop pool with old Dubai views, and fitness centre.',
                'address' => 'Khalid Bin Waleed Road, Bur Dubai, Dubai',
                'latitude' => 25.2600,
                'longitude' => 55.2960,
                'phone' => '+971 4 397 7444',
                'email' => 'reservations@fourpointsburdubai.com',
                'is_beach_access' => false,
                'is_family_friendly' => true,
                'is_featured' => false,
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'amenities' => [1, 2, 3, 4, 7, 8, 10, 13, 14, 15, 17, 20, 21, 25, 27, 28],
            ],
            [
                'name' => 'Ibis Styles Dubai Jumeira',
                'location' => 'karama',
                'star_rating' => 3,
                'short_description' => 'Colourful design hotel near Karama with pool, free breakfast, and Metro access',
                'description' => 'Ibis Styles brings playful design to budget travel with 176 colourful rooms, outdoor pool, complimentary breakfast, and 24-hour snack bar. Near ADCB Metro station.',
                'address' => 'Al Karama, near ADCB Metro Station, Dubai',
                'latitude' => 25.2475,
                'longitude' => 55.3048,
                'phone' => '+971 4 324 2424',
                'email' => 'h9576@accor.com',
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
                    'meta_title' => "{$data['name']} — Book Now | Bur Dubai Hotels",
                    'meta_description' => $data['short_description'] . '. Book your stay with Bur Dubai Hotels.',
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
                ['url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'XVA Art Hotel heritage courtyard', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Art-curated guest room'],
                ['url' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=1200&q=80', 'cat' => 'general', 'alt' => 'Heritage courtyard seating'],
                ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Rooftop vegetarian cafe'],
                ['url' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Art gallery bedroom'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'general', 'alt' => 'Wind tower architecture'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Arabian Courtyard Hotel facade', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Arabian-styled room'],
                ['url' => 'https://images.unsplash.com/photo-1580041065738-e72023775cdc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool heritage views'],
                ['url' => 'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Moroccan-inspired spa'],
                ['url' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Traditional lobby'],
                ['url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fitness centre'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1585412727339-54e4bae3b0c9?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Raffles Dubai pyramid at night', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1625244724120-1fd1d34d00f6?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Luxury suite with city views'],
                ['url' => 'https://images.unsplash.com/photo-1561501900-3701fa6a0864?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Resort-style pool'],
                ['url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Azur Mediterranean restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1593693397690-362cb9666fc2?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Botanical garden lobby'],
                ['url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', 'cat' => 'spa', 'alt' => 'Raffles Spa treatment'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Grand Hyatt Dubai resort', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Grand room with park views'],
                ['url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Lagoon-style swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Fine dining restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Tropical rainforest lobby'],
                ['url' => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'State-of-the-art gym'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1606046604972-77cc76aee944?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Four Points Bur Dubai', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Modern guest room'],
                ['url' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Rooftop pool old Dubai views'],
                ['url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Asha\'s restaurant'],
                ['url' => 'https://images.unsplash.com/photo-1560624052-449f5ddf0c31?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Hotel reception area'],
                ['url' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=1200&q=80', 'cat' => 'gym', 'alt' => 'Fitness room'],
            ],
            [
                ['url' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=1200&q=80', 'cat' => 'exterior', 'alt' => 'Ibis Styles colourful exterior', 'primary' => true],
                ['url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=1200&q=80', 'cat' => 'rooms', 'alt' => 'Playful design room'],
                ['url' => 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=1200&q=80', 'cat' => 'pool', 'alt' => 'Outdoor swimming pool'],
                ['url' => 'https://images.unsplash.com/photo-1559329007-40df8a9345d8?w=1200&q=80', 'cat' => 'restaurant', 'alt' => 'Breakfast area'],
                ['url' => 'https://images.unsplash.com/photo-1615460549969-36fa19521a4f?w=1200&q=80', 'cat' => 'lobby', 'alt' => 'Colourful lobby'],
                ['url' => 'https://images.unsplash.com/photo-1564574685150-48e5c5e6e3f8?w=1200&q=80', 'cat' => 'general', 'alt' => 'Hotel exterior night'],
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
            'boutique-4star' => [
                ['name' => 'Superior Room', 'bed' => 'King', 'sqm' => 32, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 450, 'rooms' => 25],
                ['name' => 'Deluxe Room', 'bed' => 'King', 'sqm' => 40, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 650, 'rooms' => 15],
                ['name' => 'Junior Suite', 'bed' => 'King', 'sqm' => 55, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 950, 'rooms' => 8],
                ['name' => 'Executive Suite', 'bed' => 'King + Sofa Bed', 'sqm' => 80, 'guests' => 4, 'adults' => 3, 'children' => 2, 'price' => 1400, 'rooms' => 4],
            ],
            'luxury-urban' => [
                ['name' => 'Classic Room', 'bed' => 'King', 'sqm' => 38, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 900, 'rooms' => 25],
                ['name' => 'Luxury Room', 'bed' => 'King', 'sqm' => 50, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 1400, 'rooms' => 15],
                ['name' => 'Opera Suite', 'bed' => 'King', 'sqm' => 80, 'guests' => 3, 'adults' => 2, 'children' => 2, 'price' => 2500, 'rooms' => 8],
                ['name' => 'Prestige Suite', 'bed' => 'King + Queen', 'sqm' => 140, 'guests' => 5, 'adults' => 4, 'children' => 2, 'price' => 5000, 'rooms' => 3],
            ],
            'smart-3star' => [
                ['name' => 'Rover Room', 'bed' => 'Queen', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 1, 'price' => 280, 'rooms' => 50],
                ['name' => 'Rover Room Twin', 'bed' => 'Twin', 'sqm' => 24, 'guests' => 2, 'adults' => 2, 'children' => 0, 'price' => 280, 'rooms' => 40],
                ['name' => 'Connecting Rooms', 'bed' => 'Queen + Twin', 'sqm' => 48, 'guests' => 4, 'adults' => 4, 'children' => 2, 'price' => 520, 'rooms' => 15],
            ],
        ];

        $hotelTemplateMap = [
            0 => 'boutique-4star',
            1 => 'boutique-4star',
            2 => 'luxury-urban',
            3 => 'luxury-urban',
            4 => 'boutique-4star',
            5 => 'smart-3star',
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
            ['name' => 'Hassan Al Rashid', 'email' => 'hassan.r@email.com', 'rating' => 5, 'title' => 'Sleeping in a living gallery', 'comment' => 'XVA Art Hotel is unlike anything. Each room is a work of art. Waking up surrounded by contemporary UAE art, then breakfast on the rooftop overlooking the wind towers — this is the most authentic Dubai experience.'],
            ['name' => 'Eleanor Wright', 'email' => 'eleanor.w@email.com', 'rating' => 5, 'title' => 'Heritage meets hospitality', 'comment' => 'The Arabian Courtyard is perfectly located opposite the Dubai Museum. We walked to the textile souk every morning. The Moroccan spa was heavenly and the rooftop pool has incredible views of the old district.'],
            ['name' => 'Priya Sharma', 'email' => 'priya.s@email.com', 'rating' => 5, 'title' => 'Raffles is absolute perfection', 'comment' => 'The pyramid building is stunning from outside, but inside it is even more spectacular. Our suite had panoramic views, the botanical garden is magical, and the afternoon tea in the Raffles Salon was the finest ever.'],
            ['name' => 'Michael Torres', 'email' => 'michael.t@email.com', 'rating' => 5, 'title' => 'A resort inside the city', 'comment' => 'Grand Hyatt Dubai is enormous — 13 restaurants, a tropical lobby that feels like a rainforest, and a pool complex that rivals beach resorts. The Creek Park views were spectacular.'],
            ['name' => 'Aisha Al Mansoori', 'email' => 'aisha.m@email.com', 'rating' => 4, 'title' => 'Great value in old Dubai', 'comment' => 'Four Points is perfectly positioned in the heart of Meena Bazaar. We walked to the textile souk, the Gold Souk via abra, and dozens of incredible Indian restaurants. Good rooms at honest prices.'],
            ['name' => 'David Brennan', 'email' => 'david.b@email.com', 'rating' => 4, 'title' => 'Fun budget option in Karama', 'comment' => 'Ibis Styles was a pleasant surprise — colourful design, comfortable beds, and free breakfast. Karama Market is great for bargain shopping. Metro station is close.'],
            ['name' => 'Layla Ahmed', 'email' => 'layla.a@email.com', 'rating' => 5, 'title' => 'Al Fahidi is magical at sunset', 'comment' => 'We specifically chose a hotel in Al Fahidi to experience old Dubai and it was the best decision. The narrow lanes, wind towers, art galleries, and Creek views at sunset — unforgettable.'],
            ['name' => 'Thomas Anderson', 'email' => 'thomas.a@email.com', 'rating' => 5, 'title' => 'Best pool complex in Dubai', 'comment' => 'The Grand Hyatt pool area is like a water park — lagoon pools, waterfalls, and a separate adults-only section. Our kids spent the entire holiday in the pool.'],
            ['name' => 'Fatima Zahra', 'email' => 'fatima.z@email.com', 'rating' => 5, 'title' => 'Boutique art hotel gem', 'comment' => 'If you are tired of generic hotel rooms, XVA is the answer. Only 14 rooms, each decorated by a different artist. The vegetarian cafe on the rooftop is delicious.'],
            ['name' => 'James Sullivan', 'email' => 'james.s@email.com', 'rating' => 4, 'title' => 'Opposite the Dubai Museum', 'comment' => 'Location does not get better than this — walk out the door and you are at the Dubai Museum. The Creek, textile souk, and abra station are all within 5 minutes.'],
            ['name' => 'Maryam Al Hashimi', 'email' => 'maryam.h@email.com', 'rating' => 5, 'title' => 'Afternoon tea at Raffles', 'comment' => 'The Raffles Salon afternoon tea is an institution in Dubai. The suite was gorgeous, the botanical garden is a peaceful oasis, and Wafi Mall is connected directly.'],
            ['name' => 'Roberto Ferreira', 'email' => 'roberto.f@email.com', 'rating' => 5, 'title' => 'The real Dubai experience', 'comment' => 'Bur Dubai is where you feel the real pulse of this city. Our hotel was surrounded by textile merchants, spice traders, and incredible street food. This is Dubai at its finest.'],
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
