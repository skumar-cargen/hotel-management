<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Location;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MassHotelSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding new locations...');
        $locationMap = $this->seedLocations();

        $this->command->info('Seeding 100 hotels with room types...');
        $this->seedHotels($locationMap);

        $this->command->info('Done! Updating hotel min_price...');
        $this->updateMinPrices();
    }

    private function seedLocations(): array
    {
        $newLocations = [
            ['name' => 'Al Sufouh', 'slug' => 'al-sufouh', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'Upscale coastal district home to Media City and Internet City, with stunning beach views and modern towers.', 'short_description' => 'Coastal district near Media City', 'latitude' => 25.1010, 'longitude' => 55.1700, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Jumeirah Village Circle', 'slug' => 'jumeirah-village-circle', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'A popular community offering affordable apartments with parks, retail, and easy highway access to all of Dubai.', 'short_description' => 'Affordable community hub', 'latitude' => 25.0600, 'longitude' => 55.2100, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Dubai Hills', 'slug' => 'dubai-hills', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'Master-planned community with an 18-hole golf course, Dubai Hills Mall, and family-friendly green spaces.', 'short_description' => 'Golf course community with mall', 'latitude' => 25.1277, 'longitude' => 55.2453, 'is_active' => true, 'is_featured' => true],
            ['name' => 'Dubai Creek Harbour', 'slug' => 'dubai-creek-harbour', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'Waterfront mega-development at the historic Dubai Creek, featuring the future Dubai Creek Tower and vibrant retail.', 'short_description' => 'Waterfront mega-development', 'latitude' => 25.1950, 'longitude' => 55.3450, 'is_active' => true, 'is_featured' => true],
            ['name' => 'Al Jadaf', 'slug' => 'al-jadaf', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'Historic dhow-building district turned modern cultural hub, near the Dubai Design District and Culture Village.', 'short_description' => 'Cultural hub near D3', 'latitude' => 25.2070, 'longitude' => 55.3290, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Motor City', 'slug' => 'motor-city', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'Motorsport-themed community near Dubai Autodrome, offering affordable living with parks and retail options.', 'short_description' => 'Motorsport-themed community', 'latitude' => 25.0459, 'longitude' => 55.2360, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Mirdif', 'slug' => 'mirdif', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'Family-friendly residential area near the airport with Mirdif City Centre mall and Mushrif Park.', 'short_description' => 'Family suburb near airport', 'latitude' => 25.2253, 'longitude' => 55.4175, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Saadiyat Island', 'slug' => 'saadiyat-island', 'city' => 'Abu Dhabi', 'country' => 'UAE', 'description' => 'Cultural island home to Louvre Abu Dhabi, pristine white-sand beaches, and world-class resorts.', 'short_description' => 'Cultural island with Louvre', 'latitude' => 24.5400, 'longitude' => 54.4300, 'is_active' => true, 'is_featured' => true],
            ['name' => 'Al Reem Island', 'slug' => 'al-reem-island', 'city' => 'Abu Dhabi', 'country' => 'UAE', 'description' => 'Modern island community with waterfront towers, retail, dining, and stunning skyline views of Abu Dhabi.', 'short_description' => 'Modern waterfront island', 'latitude' => 24.4950, 'longitude' => 54.4050, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Khalifa City', 'slug' => 'khalifa-city', 'city' => 'Abu Dhabi', 'country' => 'UAE', 'description' => 'Sprawling suburban area near Abu Dhabi airport with family villas, malls, and easy access to Yas Island.', 'short_description' => 'Suburban area near airport', 'latitude' => 24.4200, 'longitude' => 54.5800, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Al Khan', 'slug' => 'al-khan', 'city' => 'Sharjah', 'country' => 'UAE', 'description' => 'Waterfront district with Al Khan Lagoon, Sharjah Aquarium, and budget-friendly beachfront hotels.', 'short_description' => 'Waterfront with lagoon views', 'latitude' => 25.3300, 'longitude' => 55.3900, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Al Nahda Sharjah', 'slug' => 'al-nahda-sharjah', 'city' => 'Sharjah', 'country' => 'UAE', 'description' => 'Bustling border area between Sharjah and Dubai with shopping malls, restaurants, and affordable accommodation.', 'short_description' => 'Sharjah-Dubai border hub', 'latitude' => 25.3053, 'longitude' => 55.3744, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Ajman Corniche', 'slug' => 'ajman-corniche', 'city' => 'Ajman', 'country' => 'UAE', 'description' => 'Beautiful waterfront promenade with white-sand beaches, affordable resorts, and stunning Arabian Gulf views.', 'short_description' => 'Beachfront corniche promenade', 'latitude' => 25.4111, 'longitude' => 55.4353, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Al Hamra Village', 'slug' => 'al-hamra-village', 'city' => 'Ras Al Khaimah', 'country' => 'UAE', 'description' => 'Luxury resort community with a championship golf course, private beach, marina, and Manar Mall.', 'short_description' => 'Luxury resort with golf course', 'latitude' => 25.7134, 'longitude' => 55.7811, 'is_active' => true, 'is_featured' => true],
            ['name' => 'RAK City Centre', 'slug' => 'rak-city-centre', 'city' => 'Ras Al Khaimah', 'country' => 'UAE', 'description' => 'The heart of Ras Al Khaimah with the historic Old Town, National Museum, and traditional souks.', 'short_description' => 'Historic city centre', 'latitude' => 25.7895, 'longitude' => 55.9432, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Fujairah Beach', 'slug' => 'fujairah-beach', 'city' => 'Fujairah', 'country' => 'UAE', 'description' => 'East coast beach destination with clear waters, diving spots, and the dramatic Hajar Mountains backdrop.', 'short_description' => 'East coast beach escape', 'latitude' => 25.1288, 'longitude' => 56.3264, 'is_active' => true, 'is_featured' => true],
            ['name' => 'Al Ain Oasis', 'slug' => 'al-ain-oasis', 'city' => 'Al Ain', 'country' => 'UAE', 'description' => 'UNESCO-listed oasis city known as the Garden City, with Al Ain Zoo, Jebel Hafeet, and heritage sites.', 'short_description' => 'Garden City oasis', 'latitude' => 24.2075, 'longitude' => 55.7447, 'is_active' => true, 'is_featured' => false],
            ['name' => 'Jumeirah', 'slug' => 'jumeirah', 'city' => 'Dubai', 'country' => 'UAE', 'description' => 'Prestigious coastal residential area with Jumeirah Beach, La Mer, and Burj Al Arab views.', 'short_description' => 'Prestigious beachfront district', 'latitude' => 25.2048, 'longitude' => 55.2708, 'is_active' => true, 'is_featured' => true],
        ];

        $map = [];
        foreach ($newLocations as $loc) {
            $location = Location::firstOrCreate(['slug' => $loc['slug']], $loc);
            $map[$loc['slug']] = $location->id;

            // Attach to relevant domains
            $domainIds = $this->domainIdsForLocation($loc['city']);
            $location->domains()->syncWithoutDetaching(
                collect($domainIds)->mapWithKeys(fn ($id) => [$id => ['is_active' => true, 'sort_order' => 0]])->toArray()
            );
        }

        // Also map existing locations
        foreach (Location::all() as $loc) {
            $map[$loc->slug] = $loc->id;
        }

        return $map;
    }

    private function domainIdsForLocation(string $city): array
    {
        return match ($city) {
            'Dubai' => [1, 4, 6, 8], // LuxeStay, Budget, Luxury, City Apartments
            'Abu Dhabi' => [1, 3, 4], // LuxeStay, Abu Dhabi Stays, Budget
            'Sharjah' => [1, 4, 5],   // LuxeStay, Budget, Sharjah
            'Ajman' => [1, 4],
            'Ras Al Khaimah' => [1, 4],
            'Fujairah' => [1, 4, 7],  // LuxeStay, Budget, Beach
            'Al Ain' => [1, 3, 4],
            default => [1, 4],
        };
    }

    private function seedHotels(array $locationMap): void
    {
        $hotels = [
            // --- DOWNTOWN DUBAI (7) ---
            ['name' => 'Burj Residences', 'location' => 'downtown-dubai', 'star' => 5, 'short' => 'Ultra-luxury residences at the foot of Burj Khalifa', 'desc' => 'Experience the pinnacle of luxury living at Burj Residences. Located directly at the base of Burj Khalifa, these premium apartments offer unmatched views of the Dubai Fountain, direct access to Dubai Mall, and world-class concierge service. Each residence features Italian marble, floor-to-ceiling windows, and smart home technology.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [1200, 1800, 2800, 4500], 'domains' => [1, 6, 8]],
            ['name' => 'Opera Grand Apartments', 'location' => 'downtown-dubai', 'star' => 4, 'short' => 'Elegant apartments overlooking Dubai Opera', 'desc' => 'Sophistication meets comfort at Opera Grand. Overlooking the stunning Dubai Opera and with views of Burj Khalifa, these serviced apartments feature contemporary design, a rooftop infinity pool, and gourmet dining options. Walking distance to Dubai Mall and the vibrant Downtown Boulevard.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [650, 900, 1400], 'domains' => [1, 6, 8]],
            ['name' => 'Emaar Square Suites', 'location' => 'downtown-dubai', 'star' => 4, 'short' => 'Modern suites in the heart of Emaar Square', 'desc' => 'Centrally positioned within Emaar Square, these contemporary suites offer the perfect blend of business and leisure. With direct access to Dubai Mall, state-of-the-art fitness facilities, and panoramic city views, Emaar Square Suites is ideal for both short and extended stays.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [550, 800, 1200], 'domains' => [1, 4, 8]],
            ['name' => 'Vida Downtown Studio Hotel', 'location' => 'downtown-dubai', 'star' => 4, 'short' => 'Trendy lifestyle studios on the Boulevard', 'desc' => 'A hip, lifestyle-driven hotel on the iconic Downtown Boulevard. Vida Downtown offers stylish studios and rooms with quirky design touches, a vibrant pool deck, and multiple dining concepts. Perfect for young professionals and design-conscious travellers seeking a central location.', 'beach' => false, 'family' => false, 'featured' => true, 'price' => [480, 700], 'domains' => [1, 6, 8]],
            ['name' => 'South Ridge Tower Hotel', 'location' => 'downtown-dubai', 'star' => 3, 'short' => 'Affordable tower apartments with fountain views', 'desc' => 'Great value accommodation in Downtown Dubai. South Ridge offers fully furnished apartments with balconies overlooking the Dubai Fountain. Residents enjoy access to a gym, pool, and are just minutes from Dubai Mall and the Metro station.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [320, 480, 700], 'domains' => [1, 4, 8]],
            ['name' => 'Claren Tower Residences', 'location' => 'downtown-dubai', 'star' => 4, 'short' => 'Premium residences with Burj Khalifa views', 'desc' => 'Claren Tower Residences offer spacious, elegantly designed apartments in the heart of Downtown Dubai. With unobstructed Burj Khalifa views, a landscaped podium pool, children\'s play area, and concierge services, it\'s perfect for families and business travellers alike.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [600, 900, 1500, 2200], 'domains' => [1, 6, 8]],
            ['name' => 'Downtown Dorra Bay Hotel', 'location' => 'downtown-dubai', 'star' => 3, 'short' => 'Budget-friendly hotel near Downtown Boulevard', 'desc' => 'An excellent budget option in the Downtown area. Dorra Bay Hotel offers clean, comfortable rooms with modern amenities, a rooftop pool, and easy access to the Metro. Perfect for travellers wanting to explore Downtown Dubai without breaking the bank.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [250, 380], 'domains' => [4, 8]],

            // --- DUBAI MARINA (7) ---
            ['name' => 'Marina Gate Luxury Apartments', 'location' => 'dubai-marina', 'star' => 5, 'short' => 'Premium waterfront living in Marina Gate', 'desc' => 'The finest address in Dubai Marina. Marina Gate offers ultra-luxurious apartments with floor-to-ceiling marina views, private balconies, Gaggenau appliances, and access to an exclusive residents-only beach club. The triple-tower complex features a sky bridge pool, spa, and concierge.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [900, 1400, 2200, 3500], 'domains' => [1, 2, 6]],
            ['name' => 'Cayan Tower Suites', 'location' => 'dubai-marina', 'star' => 4, 'short' => 'Iconic twisted tower suites with marina views', 'desc' => 'Stay in Dubai\'s famous twisting tower. Cayan Tower Suites offer unique architecture with every floor rotated 1.2 degrees, creating breathtaking panoramic views. Modern interiors, infinity pool, and direct Marina Walk access make this a truly unforgettable stay.', 'beach' => false, 'family' => false, 'featured' => true, 'price' => [700, 1000, 1600], 'domains' => [1, 2, 6]],
            ['name' => 'Princess Tower Hotel Apartments', 'location' => 'dubai-marina', 'star' => 4, 'short' => 'Apartments in one of the world\'s tallest residential towers', 'desc' => 'Experience life at the top in Princess Tower, one of the tallest residential buildings in the world. Fully furnished apartments offer stunning views of the Arabian Gulf, Palm Jumeirah, and the Marina skyline. Facilities include a pool, gym, and direct beach access.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [550, 800, 1300, 2000], 'domains' => [1, 2, 6, 7]],
            ['name' => 'Botanica Marina Studio Hotel', 'location' => 'dubai-marina', 'star' => 3, 'short' => 'Cozy boutique studios steps from Marina Walk', 'desc' => 'A charming boutique property offering compact but beautifully designed studios. Located just steps from Marina Walk dining and the Marina Mall, Botanica is perfect for solo travellers and couples seeking a vibrant neighbourhood base.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [300, 450], 'domains' => [2, 4, 8]],
            ['name' => 'Marina Wharf Hotel', 'location' => 'dubai-marina', 'star' => 4, 'short' => 'Waterfront hotel with yacht club views', 'desc' => 'Positioned on the Marina waterfront with direct views of the yacht club, Marina Wharf Hotel offers elegantly appointed rooms and suites. Guests enjoy a rooftop pool, multiple restaurants, and complimentary shuttle service to JBR Beach and Mall of the Emirates.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [500, 750, 1100], 'domains' => [1, 2, 6]],
            ['name' => 'Silverene Towers Residence', 'location' => 'dubai-marina', 'star' => 3, 'short' => 'Modern twin-tower apartments in Marina', 'desc' => 'Affordable elegance in the heart of Dubai Marina. Silverene\'s twin towers offer spacious apartments with contemporary finishes, a stunning infinity pool on the podium level, and walking distance to the Marina Metro, Marina Mall, and waterfront dining.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [350, 500, 750], 'domains' => [2, 4, 8]],
            ['name' => 'No. 9 Marina Tower', 'location' => 'dubai-marina', 'star' => 5, 'short' => 'Exclusive boutique tower in Marina', 'desc' => 'An exclusive address for the discerning guest. No. 9 offers only premium suites with bespoke interiors, personal butler service, and a private residents\' lounge. The rooftop sky bar offers 360-degree views of the Marina, Palm, and Arabian Gulf.', 'beach' => false, 'family' => false, 'featured' => true, 'price' => [1500, 2200, 3800], 'domains' => [1, 2, 6]],

            // --- PALM JUMEIRAH (6) ---
            ['name' => 'Azure Palm Residence', 'location' => 'palm-jumeirah', 'star' => 5, 'short' => 'Beachfront villa-style suites on the Palm', 'desc' => 'Private beachfront luxury on Palm Jumeirah. Azure Palm Residence offers spacious villa-style suites with private plunge pools, direct beach access, and panoramic views of the Arabian Gulf and Dubai skyline. A truly exclusive island retreat.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [2000, 3500, 5000], 'domains' => [1, 6, 7]],
            ['name' => 'Palm Views West Hotel', 'location' => 'palm-jumeirah', 'star' => 4, 'short' => 'Stunning views from the Palm trunk', 'desc' => 'Enjoy panoramic views of the Palm fronds and Atlantis from Palm Views West. These well-appointed apartments sit on the trunk of Palm Jumeirah with easy access to the Monorail, Nakheel Mall, and The Pointe dining and entertainment district.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [700, 1000, 1600], 'domains' => [1, 6, 7]],
            ['name' => 'Oceana Residence & Spa', 'location' => 'palm-jumeirah', 'star' => 5, 'short' => 'Beachfront resort-style living on the crescent', 'desc' => 'Resort living at its finest. Oceana sits on the Palm crescent with 500m of private beach, three pools, a world-class spa, and tennis courts. The spacious apartments feature premium finishes and uninterrupted sea views from every room.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [1100, 1800, 2800], 'domains' => [1, 6, 7]],
            ['name' => 'Golden Mile Apartments', 'location' => 'palm-jumeirah', 'star' => 3, 'short' => 'Affordable Palm living with retail at your doorstep', 'desc' => 'The most accessible address on Palm Jumeirah. Golden Mile offers comfortable apartments above a bustling galleria of shops and restaurants. Beach access, pools, and the Palm Monorail station are all within walking distance.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [400, 600, 900], 'domains' => [1, 4, 7]],
            ['name' => 'Club Vista Mare Hotel', 'location' => 'palm-jumeirah', 'star' => 4, 'short' => 'Beachfront boutique hotel at The Pointe', 'desc' => 'A stylish boutique hotel at the tip of Palm Jumeirah near The Pointe. Club Vista Mare offers chic rooms with Atlantis views, rooftop dining, a beachside pool, and walking distance to over 80 restaurants and entertainment venues.', 'beach' => true, 'family' => false, 'featured' => false, 'price' => [650, 950, 1400], 'domains' => [1, 6, 7]],
            ['name' => 'Palma Residences Holiday Homes', 'location' => 'palm-jumeirah', 'star' => 4, 'short' => 'Spacious holiday homes on the Palm fronds', 'desc' => 'Experience island living in these spacious holiday homes on the Palm fronds. Each townhouse features a private garden, shared pool, beach access, and stunning views. Perfect for families and groups seeking space and privacy.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [800, 1200, 1800, 2500], 'domains' => [1, 6, 7]],

            // --- JBR (5) ---
            ['name' => 'Rimal Beachfront Suites', 'location' => 'jumeirah-beach-residence', 'star' => 4, 'short' => 'Direct beach access suites at JBR', 'desc' => 'Prime beachfront position in JBR. Rimal offers fully serviced suites with direct access to The Beach at JBR, Ain Dubai views, and the vibrant JBR Walk. Facilities include multiple pools, gym, kids\' play area, and 24-hour security.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [600, 900, 1400], 'domains' => [1, 2, 6, 7]],
            ['name' => 'Shams JBR Hotel', 'location' => 'jumeirah-beach-residence', 'star' => 4, 'short' => 'Sun-drenched apartments with sea views', 'desc' => 'Bright, sun-filled apartments in the Shams cluster of JBR. Every unit enjoys generous natural light, sea or marina views, and access to the famous JBR beach. The vibrant Walk dining strip is at your doorstep.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [500, 750, 1200], 'domains' => [1, 2, 7]],
            ['name' => 'Murjan JBR Apartments', 'location' => 'jumeirah-beach-residence', 'star' => 3, 'short' => 'Value apartments in JBR Murjan cluster', 'desc' => 'Great value beachfront accommodation in JBR\'s Murjan cluster. Clean, comfortable apartments with pool access, nearby beach, and all the dining and shopping of JBR Walk. An excellent choice for budget-conscious beach lovers.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [350, 500, 800], 'domains' => [2, 4, 7]],
            ['name' => 'JBR Walk Hotel & Spa', 'location' => 'jumeirah-beach-residence', 'star' => 5, 'short' => 'Five-star beachfront hotel on JBR Walk', 'desc' => 'The definitive JBR luxury experience. This five-star hotel features opulent rooms with Ain Dubai and sea views, a full-service spa, three restaurants, infinity pool, and private beach cabanas. Steps from The Beach shopping and Bluewaters Island.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [1000, 1500, 2500], 'domains' => [1, 6, 7]],
            ['name' => 'Amwaj Suites JBR', 'location' => 'jumeirah-beach-residence', 'star' => 4, 'short' => 'Family suites with ocean views', 'desc' => 'Spacious family-friendly suites in JBR\'s Amwaj cluster. These well-equipped apartments feature separate living areas, full kitchens, and stunning Arabian Gulf views. Direct beach access, multiple pools, and kids\' facilities make it ideal for families.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [550, 850, 1300, 1800], 'domains' => [1, 2, 7]],

            // --- BUSINESS BAY (6) ---
            ['name' => 'Bay Square Serviced Apartments', 'location' => 'business-bay', 'star' => 3, 'short' => 'Affordable apartments in Bay Square', 'desc' => 'Smart, affordable accommodation in the heart of Business Bay. Bay Square offers modern apartments surrounded by cafes, restaurants, and retail. Easy Metro access and walking distance to Downtown Dubai make it perfect for business and leisure.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [280, 420, 650], 'domains' => [4, 8]],
            ['name' => 'Paramount Tower Hotel', 'location' => 'business-bay', 'star' => 5, 'short' => 'Hollywood-inspired luxury in Business Bay', 'desc' => 'A cinematic experience in every stay. Paramount Tower Hotel brings Hollywood glamour to Dubai with movie-themed suites, a rooftop cinema, three signature restaurants, and a stunning infinity pool overlooking the Dubai Canal and Burj Khalifa.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [900, 1400, 2200, 3500], 'domains' => [1, 6, 8]],
            ['name' => 'Canal View Tower Hotel', 'location' => 'business-bay', 'star' => 4, 'short' => 'Dubai Water Canal views', 'desc' => 'Enjoy stunning views of the Dubai Water Canal from these contemporary serviced apartments. Canal View Tower features a landscaped pool deck, modern gym, business centre, and easy access to both Downtown and DIFC.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [450, 700, 1100], 'domains' => [1, 6, 8]],
            ['name' => 'Majesty Bay Tower', 'location' => 'business-bay', 'star' => 4, 'short' => 'Executive tower with skyline views', 'desc' => 'Premium executive accommodation in Business Bay. Majesty Tower offers sleek, modern apartments with Burj Khalifa views, a stylish lobby lounge, rooftop pool, and meeting facilities. Ideal for corporate travellers and professionals.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [500, 780, 1200], 'domains' => [1, 6, 8]],
            ['name' => 'Damac Maison Bay\'s Edge', 'location' => 'business-bay', 'star' => 4, 'short' => 'Premium Damac property on the canal', 'desc' => 'Damac Maison Bay\'s Edge offers hotel-style living with the space of an apartment. Positioned along the Dubai Canal with panoramic views, residents enjoy daily housekeeping, concierge, a lagoon-style pool, and Damac\'s signature service.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [550, 850, 1300], 'domains' => [1, 6, 8]],
            ['name' => 'Merano Business Bay Hotel', 'location' => 'business-bay', 'star' => 3, 'short' => 'Budget business hotel near Metro', 'desc' => 'No-nonsense business accommodation at a fair price. Merano offers clean, modern rooms with work desks, high-speed WiFi, meeting rooms, and a cafe. Located near the Business Bay Metro station with easy access to DIFC and Downtown.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [220, 350], 'domains' => [4, 8]],

            // --- AL SUFOUH (4) ---
            ['name' => 'Media One Hotel Apartments', 'location' => 'al-sufouh', 'star' => 4, 'short' => 'Trendy hotel in Media City', 'desc' => 'The social hub of Media City. Media One combines trendy design with comfort, featuring a famous rooftop pool and bar, multiple dining concepts, and a vibrant social scene. Perfect for media professionals and young travellers seeking energy and style.', 'beach' => false, 'family' => false, 'featured' => true, 'price' => [450, 650, 1000], 'domains' => [1, 6, 8]],
            ['name' => 'Sufouh Gardens Hotel', 'location' => 'al-sufouh', 'star' => 3, 'short' => 'Garden apartments near Knowledge Village', 'desc' => 'A peaceful retreat surrounded by landscaped gardens in Al Sufouh. These comfortable apartments offer excellent value with pool access, gym, and proximity to Knowledge Village, Internet City, and the beach.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [280, 400, 600], 'domains' => [4, 8]],
            ['name' => 'Kempinski Residence Sufouh', 'location' => 'al-sufouh', 'star' => 5, 'short' => 'Ultra-luxury beachfront residence', 'desc' => 'Unparalleled luxury on Al Sufouh Beach. The Kempinski Residence offers palatial suites with private beach cabanas, butler service, a European spa, and multiple gourmet restaurants. Direct views of Burj Al Arab complete the experience.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [1800, 2800, 4200], 'domains' => [1, 6, 7]],
            ['name' => 'Studio M Al Sufouh', 'location' => 'al-sufouh', 'star' => 3, 'short' => 'Smart studios for digital nomads', 'desc' => 'Designed for the modern worker. Studio M offers compact, well-designed studios with high-speed WiFi, co-working spaces, a rooftop pool, and pod-style sleeping areas. Close to Internet City and Media City free zones.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [200, 320], 'domains' => [4, 8]],

            // --- JVC (4) ---
            ['name' => 'Bloom Heights Hotel', 'location' => 'jumeirah-village-circle', 'star' => 3, 'short' => 'Modern tower apartments in JVC', 'desc' => 'Contemporary apartments in the heart of JVC. Bloom Heights offers spacious units with balconies, a resort-style pool, gym, and children\'s play area. With easy access to Al Khail Road and Hessa Street, all of Dubai is within reach.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [200, 320, 480], 'domains' => [4, 8]],
            ['name' => 'Circle Mall Residence', 'location' => 'jumeirah-village-circle', 'star' => 3, 'short' => 'Apartments above Circle Mall', 'desc' => 'Convenience redefined with a mall at your doorstep. Circle Mall Residence offers comfortable apartments directly above JVC\'s main shopping destination. Supermarkets, dining, cinema, and more are just an elevator ride away.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [180, 280, 420], 'domains' => [4, 8]],
            ['name' => 'Ghalia JVC Suites', 'location' => 'jumeirah-village-circle', 'star' => 4, 'short' => 'Premium suites in JVC', 'desc' => 'A premium address in JVC. Ghalia Suites offer hotel-quality finishes in spacious apartments with a landscaped pool, modern gym, and dedicated parking. Competitive pricing makes luxury accessible in this growing community.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [350, 520, 780], 'domains' => [1, 4, 8]],
            ['name' => 'Aria JVC Residence', 'location' => 'jumeirah-village-circle', 'star' => 2, 'short' => 'Budget-friendly studios in JVC', 'desc' => 'The most affordable option in JVC. Aria offers clean, basic studios and apartments perfect for budget travellers and long-stay guests. Communal pool, basic gym, and free parking included.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [120, 180], 'domains' => [4]],

            // --- DUBAI HILLS (4) ---
            ['name' => 'Dubai Hills Golf Residence', 'location' => 'dubai-hills', 'star' => 5, 'short' => 'Luxury residences on the golf course', 'desc' => 'Wake up to fairway views. Dubai Hills Golf Residence offers premium apartments and villas overlooking the 18-hole championship golf course. Residents enjoy a private clubhouse, spa, and are steps from Dubai Hills Mall and parks.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [800, 1200, 2000, 3200], 'domains' => [1, 6, 8]],
            ['name' => 'Collective 2.0 Hotel', 'location' => 'dubai-hills', 'star' => 3, 'short' => 'Trendy community hotel in Dubai Hills', 'desc' => 'A modern, community-focused hotel in Dubai Hills. Collective 2.0 offers stylish rooms and studios with co-working spaces, a social pool, and regular community events. Close to Dubai Hills Mall and the park.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [280, 420], 'domains' => [4, 8]],
            ['name' => 'Park Ridge Apartments', 'location' => 'dubai-hills', 'star' => 4, 'short' => 'Park-view apartments in Dubai Hills', 'desc' => 'Surrounded by lush parkland, Park Ridge offers spacious apartments with green views, a children\'s pool, jogging tracks, and cycling paths at your doorstep. Dubai Hills Mall is a 5-minute walk away.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [450, 680, 1000], 'domains' => [1, 8]],
            ['name' => 'Maple Townhouses Hotel', 'location' => 'dubai-hills', 'star' => 4, 'short' => 'Townhouse-style hotel living', 'desc' => 'Experience townhouse living as a guest. These beautifully furnished 3 and 4-bedroom townhouses offer private gardens, community pools, and the peaceful Dubai Hills lifestyle. Ideal for families seeking space and tranquillity.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [700, 1100, 1600], 'domains' => [1, 6]],

            // --- DUBAI CREEK HARBOUR (3) ---
            ['name' => 'Creek Tower Residences', 'location' => 'dubai-creek-harbour', 'star' => 4, 'short' => 'Modern waterfront apartments at Creek Harbour', 'desc' => 'Be part of Dubai\'s next iconic destination. Creek Tower Residences offer modern apartments with creek and skyline views, a harbour-front promenade, retail, dining, and proximity to the future Dubai Creek Tower — set to surpass Burj Khalifa.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [500, 750, 1200], 'domains' => [1, 6, 8]],
            ['name' => 'Harbour Gate Hotel', 'location' => 'dubai-creek-harbour', 'star' => 5, 'short' => 'Premium harbour hotel with skyline views', 'desc' => 'A landmark hotel at Dubai Creek Harbour. Harbour Gate offers premium rooms and suites with floor-to-ceiling windows framing the Dubai skyline and Ras Al Khor wildlife sanctuary. Features a rooftop pool, spa, and multiple dining options.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [800, 1300, 2000], 'domains' => [1, 6]],
            ['name' => 'Creek Beach Apartments', 'location' => 'dubai-creek-harbour', 'star' => 3, 'short' => 'Affordable apartments near Creek Beach', 'desc' => 'Enjoy beach living away from the tourist crowds. Creek Beach Apartments offer comfortable units near the man-made Creek Beach with its lagoon, water sports, and dining. Great value for a waterfront lifestyle.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [300, 450, 680], 'domains' => [4, 7, 8]],

            // --- DEIRA (5) ---
            ['name' => 'Gold Souk Heritage Hotel', 'location' => 'deira', 'star' => 3, 'short' => 'Heritage hotel near the Gold Souk', 'desc' => 'Step into old Dubai. This charming heritage-style hotel sits steps from the famous Gold Souk, Spice Souk, and Dubai Creek abra stations. Traditional Arabian decor meets modern comfort, offering an authentic cultural experience.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [180, 280, 420], 'domains' => [1, 4, 8]],
            ['name' => 'Deira Waterfront Suites', 'location' => 'deira', 'star' => 4, 'short' => 'Creek-view suites in Deira', 'desc' => 'Watch traditional dhows sail by from your balcony. Deira Waterfront Suites offer spacious accommodation with stunning Creek views, modern amenities, and walking distance to Deira\'s vibrant souks and multicultural dining scene.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [350, 520, 800], 'domains' => [1, 4, 8]],
            ['name' => 'Hyatt Regency Creek Heights', 'location' => 'deira', 'star' => 5, 'short' => 'Five-star hotel on Dubai Creek', 'desc' => 'Iconic five-star hospitality on Dubai Creek. Hyatt Regency offers luxurious rooms with creek or city views, a stunning rooftop pool, world-class dining including award-winning restaurants, and a full-service spa.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [700, 1100, 1800], 'domains' => [1, 6]],
            ['name' => 'City Star Deira Hotel', 'location' => 'deira', 'star' => 2, 'short' => 'Budget rooms near Deira City Centre', 'desc' => 'Clean, affordable accommodation near Deira City Centre Mall and Metro station. City Star is perfect for budget travellers wanting easy access to shopping, the airport (10 mins), and old Dubai attractions.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [120, 180], 'domains' => [4]],
            ['name' => 'Riviera Deira Hotel', 'location' => 'deira', 'star' => 3, 'short' => 'Mid-range hotel near airport', 'desc' => 'Practical accommodation close to Dubai International Airport. Riviera offers comfortable rooms, free airport shuttle, a pool, and restaurant. An excellent choice for transit stays and business travellers visiting Deira\'s commercial district.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [200, 320], 'domains' => [4, 8]],

            // --- DIFC (3) ---
            ['name' => 'DIFC Living Tower', 'location' => 'difc', 'star' => 5, 'short' => 'Premium apartments in the financial district', 'desc' => 'Prestige address in Dubai\'s Wall Street. DIFC Living Tower offers executive apartments surrounded by world-class dining (Gate Village restaurants), art galleries, and financial institutions. Premium finishes, concierge, and valet parking included.', 'beach' => false, 'family' => false, 'featured' => true, 'price' => [900, 1400, 2200], 'domains' => [1, 6, 8]],
            ['name' => 'Index Tower Residences', 'location' => 'difc', 'star' => 4, 'short' => 'Iconic rotating tower in DIFC', 'desc' => 'Stay in one of Dubai\'s most architecturally significant buildings. Index Tower\'s rotating design offers ever-changing views. Spacious apartments with high-end finishes, a sky lounge, and the best of DIFC dining at your feet.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [650, 1000, 1500], 'domains' => [1, 6, 8]],
            ['name' => 'Gate Avenue Suites', 'location' => 'difc', 'star' => 4, 'short' => 'Modern suites above Gate Avenue', 'desc' => 'Live above one of Dubai\'s most exclusive retail and dining destinations. Gate Avenue Suites offer contemporary apartments with direct access to designer boutiques, gourmet restaurants, and DIFC\'s vibrant arts scene.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [550, 850, 1300], 'domains' => [1, 6, 8]],

            // --- JUMEIRAH (4) ---
            ['name' => 'La Mer Beach Hotel', 'location' => 'jumeirah', 'star' => 4, 'short' => 'Beachfront boutique hotel at La Mer', 'desc' => 'Sun, sand, and style at La Mer. This boutique beachfront hotel offers chic rooms with sea views, direct access to La Mer\'s waterpark, dining strip, and beach. A vibrant, Instagram-worthy destination for modern travellers.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [600, 900, 1400], 'domains' => [1, 6, 7]],
            ['name' => 'Jumeirah Heritage House', 'location' => 'jumeirah', 'star' => 3, 'short' => 'Traditional-style guesthouse', 'desc' => 'A charming guesthouse that celebrates Jumeirah\'s heritage. Traditional Arabian architecture houses modern, comfortable rooms. Guests enjoy a courtyard garden, rooftop terrace, and easy access to Jumeirah Beach and City Walk.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [280, 420], 'domains' => [1, 4]],
            ['name' => 'Pearl Jumeirah Hotel', 'location' => 'jumeirah', 'star' => 5, 'short' => 'Luxury hotel on Pearl Jumeirah Island', 'desc' => 'An island sanctuary. Pearl Jumeirah Hotel offers exclusive waterfront luxury with private beach, infinity pools, award-winning spa, and panoramic views of the Burj Al Arab. The ultimate Jumeirah experience.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [1500, 2400, 3800], 'domains' => [1, 6, 7]],
            ['name' => 'City Walk Residences', 'location' => 'jumeirah', 'star' => 4, 'short' => 'Urban lifestyle apartments at City Walk', 'desc' => 'The best of urban living. City Walk Residences offer stylish apartments surrounded by designer boutiques, gourmet restaurants, and entertainment venues. A rooftop pool, gym, and concierge complete the lifestyle experience.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [500, 780, 1200], 'domains' => [1, 6, 8]],

            // --- SAADIYAT ISLAND (4) ---
            ['name' => 'Saadiyat Beach Residence', 'location' => 'saadiyat-island', 'star' => 5, 'short' => 'Pristine beachfront on Saadiyat', 'desc' => 'Wake up to pristine white-sand beaches and turquoise waters. Saadiyat Beach Residence offers luxurious apartments with direct beach access, a world-class spa, and proximity to Louvre Abu Dhabi and upcoming Guggenheim Abu Dhabi.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [1000, 1600, 2500], 'domains' => [1, 3, 6]],
            ['name' => 'Mamsha Al Saadiyat Hotel', 'location' => 'saadiyat-island', 'star' => 4, 'short' => 'Beachfront apartments on Mamsha promenade', 'desc' => 'Beachfront living on the Mamsha Al Saadiyat promenade. These modern apartments offer floor-to-ceiling windows with sea views, a community beach club, pools, and walking distance to cultural attractions.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [600, 900, 1400], 'domains' => [1, 3]],
            ['name' => 'Louvre View Suites', 'location' => 'saadiyat-island', 'star' => 4, 'short' => 'Suites with Louvre Abu Dhabi views', 'desc' => 'Art meets accommodation. These elegant suites overlook the architectural marvel of Louvre Abu Dhabi. Guests enjoy cultural excursions, a curated art programme, beachfront dining, and Saadiyat\'s natural beauty.', 'beach' => true, 'family' => false, 'featured' => false, 'price' => [700, 1100], 'domains' => [1, 3, 6]],
            ['name' => 'Nudra Island Resort', 'location' => 'saadiyat-island', 'star' => 5, 'short' => 'Eco-luxury island resort', 'desc' => 'A sustainable luxury resort on Saadiyat\'s nature reserve. Nudra offers eco-designed villas with mangrove views, organic dining, kayaking, and wildlife encounters. A unique blend of luxury and environmental consciousness.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [1400, 2200, 3500], 'domains' => [1, 3, 6]],

            // --- AL REEM ISLAND (3) ---
            ['name' => 'Sun & Sky Towers', 'location' => 'al-reem-island', 'star' => 3, 'short' => 'Affordable towers with skyline views', 'desc' => 'Budget-friendly waterfront living in Abu Dhabi. Sun & Sky Towers offer comfortable apartments with stunning views of the Abu Dhabi skyline, pool facilities, and easy access to Reem Mall and the city centre.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [220, 350, 500], 'domains' => [3, 4]],
            ['name' => 'Shams Abu Dhabi Residence', 'location' => 'al-reem-island', 'star' => 4, 'short' => 'Premium waterfront residence', 'desc' => 'Premium island living in Abu Dhabi. Shams Residence offers spacious apartments with marina and sea views, a beach club, infinity pool, and direct access to Reem Mall and Shams boutique retail.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [450, 700, 1100], 'domains' => [1, 3]],
            ['name' => 'Gate Towers Hotel', 'location' => 'al-reem-island', 'star' => 4, 'short' => 'Iconic gate-shaped towers on Reem', 'desc' => 'Stay in one of Abu Dhabi\'s most recognisable landmarks. The Gate Towers offer spacious apartments with dual views of the Gulf and city, a podium retail level, pool, and gym.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [400, 620, 950], 'domains' => [1, 3, 4]],

            // --- KHALIFA CITY (2) ---
            ['name' => 'Masdar City Eco Hotel', 'location' => 'khalifa-city', 'star' => 4, 'short' => 'Sustainable hotel in Masdar City', 'desc' => 'The world\'s most sustainable hotel. Located in Masdar City, this eco-hotel is powered by solar energy, features zero-waste dining, and offers modern rooms with smart climate control. Easy access to Yas Island and Abu Dhabi airport.', 'beach' => false, 'family' => false, 'featured' => true, 'price' => [400, 600, 900], 'domains' => [1, 3]],
            ['name' => 'Khalifa Park View Hotel', 'location' => 'khalifa-city', 'star' => 3, 'short' => 'Family hotel near Khalifa Park', 'desc' => 'A family-friendly hotel near the beautiful Khalifa Park. Comfortable rooms, outdoor pool, kids\' play area, and restaurant. Close to Abu Dhabi airport and Yas Island attractions.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [200, 320, 480], 'domains' => [3, 4]],

            // --- YAS ISLAND (4) ---
            ['name' => 'Yas Theme Park Resort', 'location' => 'yas-island', 'star' => 4, 'short' => 'Resort adjacent to Ferrari World & Warner Bros', 'desc' => 'The ultimate theme park hotel. Enjoy direct access to Ferrari World, Warner Bros World, and Yas Waterworld. Family rooms and suites with theme park views, multiple pools, kids\' club, and complimentary shuttle to all Yas attractions.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [500, 800, 1200, 1800], 'domains' => [1, 3]],
            ['name' => 'Yas Golf & Spa Resort', 'location' => 'yas-island', 'star' => 5, 'short' => 'Luxury golf resort on Yas Links', 'desc' => 'A golfer\'s paradise. This five-star resort overlooks Yas Links, one of the region\'s top courses. Luxurious rooms, a world-class spa, beachfront dining, and proximity to Yas Marina make it the ultimate Yas Island experience.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [900, 1400, 2200], 'domains' => [1, 3, 6]],
            ['name' => 'Yas Bay Boardwalk Hotel', 'location' => 'yas-island', 'star' => 3, 'short' => 'Casual hotel on Yas Bay', 'desc' => 'Casual, fun accommodation on the Yas Bay waterfront. Modern rooms with bay views, rooftop pool, and walking distance to Etihad Arena, restaurants, and entertainment venues. Great value for Yas Island visitors.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [280, 420], 'domains' => [3, 4]],
            ['name' => 'Circuit View Apartments Yas', 'location' => 'yas-island', 'star' => 3, 'short' => 'Apartments overlooking Yas Marina Circuit', 'desc' => 'Watch F1 cars from your balcony during race season. These modern apartments overlook the iconic Yas Marina Circuit and feature pools, gym, and easy access to all Yas Island attractions.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [250, 380, 550], 'domains' => [3, 4]],

            // --- ABU DHABI CORNICHE (3) ---
            ['name' => 'Corniche Panorama Hotel', 'location' => 'abu-dhabi-corniche', 'star' => 4, 'short' => 'Panoramic Corniche and Gulf views', 'desc' => 'Commanding views of the Abu Dhabi Corniche and Arabian Gulf. This contemporary hotel offers spacious rooms, a beachfront pool, spa, and is walking distance to the Corniche promenade, beaches, and Al Hosn cultural site.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [500, 780, 1200], 'domains' => [1, 3]],
            ['name' => 'Al Hosn Heritage Hotel', 'location' => 'abu-dhabi-corniche', 'star' => 5, 'short' => 'Heritage luxury near Qasr Al Hosn', 'desc' => 'Where heritage meets luxury. This five-star hotel near Qasr Al Hosn blends traditional Emirati architecture with modern luxury. Features include a courtyard garden, heritage-themed spa, rooftop restaurant with Corniche views, and cultural experiences.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [800, 1300, 2000], 'domains' => [1, 3, 6]],
            ['name' => 'Corniche Budget Inn', 'location' => 'abu-dhabi-corniche', 'star' => 2, 'short' => 'Budget hotel near the Corniche', 'desc' => 'Clean, basic accommodation steps from the Corniche beach. Perfect for budget travellers wanting to explore Abu Dhabi\'s waterfront, Heritage Village, and downtown attractions without spending a fortune.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [130, 200], 'domains' => [4]],

            // --- AL MAJAZ SHARJAH (3) ---
            ['name' => 'Al Majaz Waterfront Hotel', 'location' => 'al-majaz-sharjah', 'star' => 4, 'short' => 'Waterfront hotel on Al Majaz lagoon', 'desc' => 'Beautiful location on the Al Majaz Waterfront. This hotel offers rooms with lagoon views, the famous Sharjah Fountain light show from your window, a pool, and walking access to Al Qasba entertainment district.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [300, 480, 720], 'domains' => [1, 5]],
            ['name' => 'Sharjah Art Hotel', 'location' => 'al-majaz-sharjah', 'star' => 3, 'short' => 'Art-themed hotel near museums', 'desc' => 'For culture lovers. This art-themed hotel is decorated with works by local artists and is walking distance from Sharjah Art Museum, Calligraphy Museum, and the Heritage Area. Complimentary cultural walking tours available.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [180, 280], 'domains' => [4, 5]],
            ['name' => 'Blue Souk Residence', 'location' => 'al-majaz-sharjah', 'star' => 3, 'short' => 'Apartments near the Blue Souk', 'desc' => 'Comfortable apartments near Sharjah\'s iconic Blue Souk. Great for shopping enthusiasts and budget travellers, with easy access to the Corniche, Al Qasba canal, and Sharjah\'s museums and heritage sites.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [150, 240, 380], 'domains' => [4, 5]],

            // --- AL KHAN SHARJAH (3) ---
            ['name' => 'Khan Beach Resort', 'location' => 'al-khan', 'star' => 4, 'short' => 'Beach resort on Al Khan lagoon', 'desc' => 'Sharjah\'s best beach resort. Khan Beach Resort offers rooms with lagoon or sea views, a private beach, two pools, water sports, and proximity to Sharjah Aquarium and Al Noor Island. Half the price of Dubai beach hotels.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [350, 550, 850], 'domains' => [1, 5, 7]],
            ['name' => 'Al Khan Lagoon Hotel', 'location' => 'al-khan', 'star' => 3, 'short' => 'Budget lagoon-view hotel', 'desc' => 'Affordable waterfront accommodation in Sharjah. Lagoon Hotel offers clean rooms with lagoon views, a pool, and easy access to Sharjah Aquarium, Al Noor Island, and the Dubai border (10 minutes).', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [160, 250, 380], 'domains' => [4, 5]],
            ['name' => 'Coral Bay Sharjah', 'location' => 'al-khan', 'star' => 3, 'short' => 'Family-friendly beach hotel', 'desc' => 'A family favourite in Sharjah. Coral Bay offers rooms with sea views, a kids\' pool, playground, family dining, and beach access. Budget-friendly rates make it popular for family getaways from Dubai.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [200, 320, 500], 'domains' => [4, 5, 7]],

            // --- AL NAHDA SHARJAH (2) ---
            ['name' => 'Nahda Mall Suites', 'location' => 'al-nahda-sharjah', 'star' => 3, 'short' => 'Apartments near Sahara Centre', 'desc' => 'Convenient location near Sahara Centre mall and the Dubai-Sharjah border. Nahda Mall Suites offer spacious apartments with full kitchens, pool, gym, and easy access to both cities via Al Nahda Road.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [150, 230, 350], 'domains' => [4, 5]],
            ['name' => 'Safeer Al Nahda Hotel', 'location' => 'al-nahda-sharjah', 'star' => 2, 'short' => 'Budget hotel on Al Nahda Road', 'desc' => 'No-frills accommodation on the busy Al Nahda Road. Basic but clean rooms with free parking and WiFi. Popular with business travellers and those seeking the cheapest option near the Dubai-Sharjah border.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [100, 150], 'domains' => [4, 5]],

            // --- AJMAN CORNICHE (3) ---
            ['name' => 'Ajman Beach Resort', 'location' => 'ajman-corniche', 'star' => 4, 'short' => 'Beachfront resort on Ajman Corniche', 'desc' => 'Luxury beach resort at a fraction of Dubai prices. Ajman Beach Resort offers rooms with Arabian Gulf views, a private beach, three pools, spa, and water sports. A peaceful getaway just 30 minutes from Dubai.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [350, 550, 850], 'domains' => [1, 4, 7]],
            ['name' => 'Corniche Tower Ajman', 'location' => 'ajman-corniche', 'star' => 3, 'short' => 'Tower apartments on Ajman Corniche', 'desc' => 'Spacious apartments with stunning Corniche views at incredibly affordable rates. Corniche Tower offers a pool, gym, and beach access. Ideal for families and long-stay guests seeking value and sea views.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [180, 280, 420], 'domains' => [4]],
            ['name' => 'Ajman Downtown Hotel', 'location' => 'ajman-corniche', 'star' => 2, 'short' => 'Budget hotel in Ajman centre', 'desc' => 'The most affordable option in Ajman. Simple, clean rooms in the city centre with free parking and WiFi. Walking distance to Ajman Museum, Gold Souk, and the Corniche beach.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [90, 140], 'domains' => [4]],

            // --- RAK (4) ---
            ['name' => 'Al Hamra Palace Beach Resort', 'location' => 'al-hamra-village', 'star' => 5, 'short' => 'Luxury beach resort with golf', 'desc' => 'A world-class beach resort in Ras Al Khaimah. Al Hamra Palace offers opulent rooms, a championship golf course, private beach, marina, multiple restaurants, and a luxury spa. Mountain backdrop meets seafront luxury.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [700, 1100, 1800, 2800], 'domains' => [1, 6, 7]],
            ['name' => 'Al Hamra Marina Apartments', 'location' => 'al-hamra-village', 'star' => 3, 'short' => 'Marina-view apartments', 'desc' => 'Affordable marina-front apartments in the Al Hamra Village community. Pool, gym, beach access, and Manar Mall nearby. Perfect for families and golfers seeking value accommodation in RAK.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [200, 320, 480], 'domains' => [4, 7]],
            ['name' => 'Jebel Jais Mountain Lodge', 'location' => 'rak-city-centre', 'star' => 4, 'short' => 'Mountain lodge near UAE\'s highest peak', 'desc' => 'A unique mountain retreat near Jebel Jais, the UAE\'s highest peak. This eco-lodge offers rooms with dramatic mountain views, adventure activities (zip line, hiking, via ferrata), stargazing, and a spa using local ingredients.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [450, 700, 1100], 'domains' => [1, 4]],
            ['name' => 'RAK Heritage Inn', 'location' => 'rak-city-centre', 'star' => 2, 'short' => 'Budget inn in RAK Old Town', 'desc' => 'Authentic old-town accommodation near RAK National Museum and the traditional souk. Simple rooms with Arabian hospitality, rooftop terrace, and free parking. The most affordable way to explore Ras Al Khaimah.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [100, 160], 'domains' => [4]],

            // --- FUJAIRAH (3) ---
            ['name' => 'Fujairah Beach Resort & Spa', 'location' => 'fujairah-beach', 'star' => 5, 'short' => 'Five-star beach resort on the east coast', 'desc' => 'Escape to the Indian Ocean. This five-star resort offers pristine beach, diving centre, infinity pool with mountain views, luxury spa, and multiple restaurants. Dramatic Hajar Mountains provide a stunning backdrop.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [800, 1200, 2000], 'domains' => [1, 6, 7]],
            ['name' => 'Sandy Beach Hotel Fujairah', 'location' => 'fujairah-beach', 'star' => 3, 'short' => 'Mid-range beach hotel with diving', 'desc' => 'Popular with divers and snorkellers. Sandy Beach Hotel offers direct access to Fujairah\'s clear waters and coral reefs. Comfortable rooms, beach, pool, dive centre, and water sports. Great value east coast escape.', 'beach' => true, 'family' => true, 'featured' => false, 'price' => [250, 380, 550], 'domains' => [4, 7]],
            ['name' => 'Dibba Bay Resort', 'location' => 'fujairah-beach', 'star' => 4, 'short' => 'Secluded bay resort in Dibba', 'desc' => 'A hidden gem on Dibba Bay. This boutique resort offers eco-lodges and glamping tents with direct beach access, kayaking, dhow cruises, snorkelling, and mountain hiking. Perfect for adventure seekers and nature lovers.', 'beach' => true, 'family' => true, 'featured' => true, 'price' => [500, 800, 1200], 'domains' => [1, 7]],

            // --- AL AIN (3) ---
            ['name' => 'Jebel Hafeet Mountain Hotel', 'location' => 'al-ain-oasis', 'star' => 4, 'short' => 'Mountain hotel on Jebel Hafeet', 'desc' => 'Perched on the slopes of Jebel Hafeet, this hotel offers breathtaking mountain and desert views. Features include hot spring pools, desert excursions, camel treks, and stargazing experiences. A truly unique UAE destination.', 'beach' => false, 'family' => true, 'featured' => true, 'price' => [400, 650, 1000], 'domains' => [1, 3]],
            ['name' => 'Al Ain Oasis Hotel', 'location' => 'al-ain-oasis', 'star' => 3, 'short' => 'Heritage hotel near the UNESCO oasis', 'desc' => 'A charming hotel near the UNESCO World Heritage Al Ain Oasis. Traditional design, peaceful gardens, pool, and cultural activities. Walking distance to Al Ain Museum, Palace Museum, and the Livestock Souk.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [200, 320, 480], 'domains' => [3, 4]],
            ['name' => 'Al Ain Budget Lodge', 'location' => 'al-ain-oasis', 'star' => 2, 'short' => 'Affordable lodge in Garden City', 'desc' => 'Clean, basic accommodation in Al Ain city centre. Free parking, WiFi, and a small cafe. Good base for exploring Al Ain Zoo, Jebel Hafeet, and the Green Mubazzarah hot springs.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [80, 130], 'domains' => [4]],

            // --- MIRDIF (2) ---
            ['name' => 'Mirdif City Centre Hotel', 'location' => 'mirdif', 'star' => 3, 'short' => 'Family hotel near the mall and airport', 'desc' => 'Convenient family accommodation near Mirdif City Centre mall and DXB airport. Spacious rooms, pool, kids\' play area, and restaurant. Popular with families visiting Dubai Parks and the airport area.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [200, 320, 480], 'domains' => [4, 8]],
            ['name' => 'Mushrif Park View Hotel', 'location' => 'mirdif', 'star' => 3, 'short' => 'Hotel overlooking Mushrif Park', 'desc' => 'Peaceful retreat overlooking Mushrif National Park. Comfortable rooms, garden terrace, pool, and easy access to the park\'s cycling tracks, BBQ areas, and equestrian centre. Family-friendly and great value.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [180, 280], 'domains' => [4, 8]],

            // --- MOTOR CITY (2) ---
            ['name' => 'Autodrome Hotel', 'location' => 'motor-city', 'star' => 3, 'short' => 'Hotel by Dubai Autodrome', 'desc' => 'For motorsport fans. The Autodrome Hotel overlooks the Dubai Autodrome circuit and offers racing experiences, a karting track, and driving simulators. Modern rooms with a pool, gym, and sports bar.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [200, 320], 'domains' => [4, 8]],
            ['name' => 'Green Community Apartments', 'location' => 'motor-city', 'star' => 3, 'short' => 'Spacious apartments in green surroundings', 'desc' => 'Spacious apartments in a lush green community. Large pools, tennis courts, parks, and a community centre. Excellent value for families and long-stay guests wanting space and greenery.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [180, 280, 420], 'domains' => [4, 8]],

            // --- AL JADAF (2) ---
            ['name' => 'Design District Hotel', 'location' => 'al-jadaf', 'star' => 4, 'short' => 'Boutique hotel in Dubai Design District', 'desc' => 'A design-forward boutique hotel in d3 (Dubai Design District). Each floor is designed by a different Emirati artist. Features include a gallery café, creative co-working spaces, a rooftop pool, and proximity to Dubai Creek.', 'beach' => false, 'family' => false, 'featured' => true, 'price' => [450, 680, 1000], 'domains' => [1, 6, 8]],
            ['name' => 'Creek Side Budget Hotel', 'location' => 'al-jadaf', 'star' => 2, 'short' => 'Budget hotel near Dubai Creek', 'desc' => 'Affordable accommodation near the Al Jadaf Metro and Dubai Creek. Basic but clean rooms with free parking and WiFi. Good base for exploring Culture Village, Ras Al Khor sanctuary, and old Dubai.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [110, 170], 'domains' => [4, 8]],

            // --- AL BARSHA (3) ---
            ['name' => 'Barsha Heights Panorama Hotel', 'location' => 'al-barsha', 'star' => 4, 'short' => 'Skyline-view hotel near MOE', 'desc' => 'Sweeping city views from Barsha Heights (TECOM). Modern hotel with rooftop pool, multiple restaurants, and walking distance to Mall of the Emirates and Ski Dubai. Popular with business and leisure travellers.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [350, 520, 800], 'domains' => [1, 4, 8]],
            ['name' => 'MOE Gateway Apartments', 'location' => 'al-barsha', 'star' => 3, 'short' => 'Budget apartments near Mall of Emirates', 'desc' => 'Excellent value apartments steps from Mall of the Emirates and the Metro. Fully furnished with kitchenettes, pool, and gym. Popular with families wanting affordable access to Ski Dubai and top shopping.', 'beach' => false, 'family' => true, 'featured' => false, 'price' => [220, 340, 500], 'domains' => [4, 8]],
            ['name' => 'Al Barsha Grand Hotel', 'location' => 'al-barsha', 'star' => 3, 'short' => 'Mid-range hotel near Metro', 'desc' => 'Practical, comfortable accommodation near Al Barsha Metro station. Clean rooms, restaurant, pool, and free parking. Central location between Marina, Downtown, and JBR makes it ideal for exploring all of Dubai.', 'beach' => false, 'family' => false, 'featured' => false, 'price' => [180, 280], 'domains' => [4, 8]],
        ];

        $amenityPool = range(1, 28);
        $roomTemplates = $this->roomTemplates();

        foreach ($hotels as $i => $h) {
            $locationId = $locationMap[$h['location']] ?? null;
            if (! $locationId) {
                $this->command->warn("Location {$h['location']} not found, skipping {$h['name']}");
                continue;
            }

            $slug = Str::slug($h['name']);
            if (Hotel::where('slug', $slug)->exists()) {
                continue;
            }

            $hotel = Hotel::create([
                'name' => $h['name'],
                'slug' => $slug,
                'location_id' => $locationId,
                'star_rating' => $h['star'],
                'description' => $h['desc'],
                'short_description' => $h['short'],
                'address' => $this->generateAddress($h['location']),
                'latitude' => null,
                'longitude' => null,
                'phone' => '+971' . rand(2, 7) . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                'email' => 'info@' . $slug . '.ae',
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in. Late cancellations charged one night.',
                'is_beach_access' => $h['beach'],
                'is_family_friendly' => $h['family'],
                'is_active' => true,
                'is_featured' => $h['featured'],
                'meta_title' => $h['name'] . ' | Dubai Apartments',
                'meta_description' => $h['short'],
                'sort_order' => 0,
                'avg_rating' => round(rand(35, 49) / 10, 1),
                'total_reviews' => rand(5, 350),
            ]);

            // Attach domains
            $hotel->domains()->syncWithoutDetaching(
                collect($h['domains'])->mapWithKeys(fn ($id) => [$id => ['is_active' => true, 'sort_order' => 0]])->toArray()
            );

            // Attach amenities (8-16 random)
            $count = rand(8, 16);
            $selected = collect($amenityPool)->shuffle()->take($count)->toArray();
            // Always include basics
            $selected = array_unique(array_merge([1, 2, 3, 4, 28], $selected)); // WiFi, AC, Elevator, Reception, TV
            if ($h['beach']) $selected[] = 10; // Beach Access
            $hotel->amenities()->syncWithoutDetaching(array_unique($selected));

            // Create room types based on price tiers
            $templates = $this->pickRoomTemplates($h['star'], count($h['price']));
            foreach ($templates as $j => $tmpl) {
                $price = $h['price'][$j] ?? $h['price'][0];
                RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => $tmpl['name'],
                    'slug' => Str::slug($tmpl['name']),
                    'description' => $tmpl['desc'],
                    'max_guests' => $tmpl['guests'],
                    'max_adults' => $tmpl['adults'],
                    'max_children' => $tmpl['children'],
                    'bed_type' => $tmpl['bed'],
                    'room_size_sqm' => $tmpl['size'],
                    'base_price' => $price,
                    'total_rooms' => rand(5, 30),
                    'is_active' => true,
                    'sort_order' => $j,
                ]);
            }

            // Update min_price
            $hotel->update(['min_price' => $hotel->roomTypes()->min('base_price')]);

            if (($i + 1) % 20 === 0) {
                $this->command->info('  ... created ' . ($i + 1) . ' hotels');
            }
        }
    }

    private function generateAddress(string $locationSlug): string
    {
        $streets = ['Street', 'Road', 'Boulevard', 'Avenue', 'Lane', 'Crescent', 'Way'];
        $num = rand(1, 99);
        $street = $streets[array_rand($streets)];
        $area = str_replace('-', ' ', ucwords($locationSlug, '-'));
        return "{$num} {$area} {$street}, UAE";
    }

    private function pickRoomTemplates(int $stars, int $count): array
    {
        $all = $this->roomTemplates();

        if ($stars >= 5) {
            $pool = array_filter($all, fn ($r) => in_array($r['tier'], ['standard', 'deluxe', 'suite', 'penthouse']));
        } elseif ($stars >= 4) {
            $pool = array_filter($all, fn ($r) => in_array($r['tier'], ['standard', 'deluxe', 'suite', 'family']));
        } elseif ($stars >= 3) {
            $pool = array_filter($all, fn ($r) => in_array($r['tier'], ['standard', 'deluxe', 'family', 'studio']));
        } else {
            $pool = array_filter($all, fn ($r) => in_array($r['tier'], ['standard', 'studio']));
        }

        $pool = array_values($pool);
        shuffle($pool);
        return array_slice($pool, 0, $count);
    }

    private function roomTemplates(): array
    {
        return [
            ['tier' => 'studio', 'name' => 'Studio', 'desc' => 'Compact studio with kitchenette, work desk, and modern bathroom. Ideal for solo travellers.', 'guests' => 2, 'adults' => 2, 'children' => 0, 'bed' => 'Double', 'size' => 28],
            ['tier' => 'standard', 'name' => 'Standard Room', 'desc' => 'Comfortable room with queen bed, ensuite bathroom, flat-screen TV, and city views.', 'guests' => 2, 'adults' => 2, 'children' => 1, 'bed' => 'Queen', 'size' => 32],
            ['tier' => 'standard', 'name' => 'Superior Room', 'desc' => 'Spacious room with king bed, sitting area, large bathroom with rain shower, and premium amenities.', 'guests' => 3, 'adults' => 2, 'children' => 1, 'bed' => 'King', 'size' => 38],
            ['tier' => 'standard', 'name' => 'Twin Room', 'desc' => 'Room with two single beds, work desk, and ensuite bathroom. Perfect for friends or colleagues travelling together.', 'guests' => 2, 'adults' => 2, 'children' => 0, 'bed' => 'Twin', 'size' => 30],
            ['tier' => 'deluxe', 'name' => 'Deluxe Room', 'desc' => 'Premium room with king bed, separate sitting area, luxury bathroom with bathtub and rain shower, and panoramic views.', 'guests' => 3, 'adults' => 2, 'children' => 1, 'bed' => 'King', 'size' => 45],
            ['tier' => 'deluxe', 'name' => 'Deluxe Sea View', 'desc' => 'Spacious deluxe room with stunning sea views, king bed, balcony, premium minibar, and Nespresso machine.', 'guests' => 3, 'adults' => 2, 'children' => 1, 'bed' => 'King', 'size' => 48],
            ['tier' => 'deluxe', 'name' => 'One-Bedroom Apartment', 'desc' => 'Full apartment with separate bedroom, living room, fully equipped kitchen, washer/dryer, and balcony.', 'guests' => 3, 'adults' => 2, 'children' => 1, 'bed' => 'King', 'size' => 65],
            ['tier' => 'suite', 'name' => 'Junior Suite', 'desc' => 'Elegant suite with king bed, spacious living area, walk-in closet, luxury bathroom, and premium views.', 'guests' => 3, 'adults' => 2, 'children' => 1, 'bed' => 'King', 'size' => 55],
            ['tier' => 'suite', 'name' => 'Executive Suite', 'desc' => 'Two-room suite with separate bedroom and living room, dining area, executive desk, and lounge access.', 'guests' => 4, 'adults' => 2, 'children' => 2, 'bed' => 'King', 'size' => 75],
            ['tier' => 'suite', 'name' => 'Two-Bedroom Suite', 'desc' => 'Luxurious two-bedroom suite with full living room, dining area, kitchen, and stunning views. Perfect for families.', 'guests' => 6, 'adults' => 4, 'children' => 2, 'bed' => 'King + Twin', 'size' => 110],
            ['tier' => 'family', 'name' => 'Family Room', 'desc' => 'Spacious family room with king bed and bunk beds, children\'s amenities, connecting door option, and extra storage.', 'guests' => 4, 'adults' => 2, 'children' => 2, 'bed' => 'King + Bunk', 'size' => 50],
            ['tier' => 'family', 'name' => 'Two-Bedroom Apartment', 'desc' => 'Spacious apartment with two bedrooms, full kitchen, living/dining room, washer/dryer, and family-friendly amenities.', 'guests' => 5, 'adults' => 4, 'children' => 2, 'bed' => 'King + Twin', 'size' => 95],
            ['tier' => 'penthouse', 'name' => 'Penthouse Suite', 'desc' => 'The ultimate in luxury. Sprawling penthouse with panoramic floor-to-ceiling views, private terrace, jacuzzi, butler service, and bespoke furnishings.', 'guests' => 4, 'adults' => 2, 'children' => 2, 'bed' => 'King', 'size' => 150],
            ['tier' => 'penthouse', 'name' => 'Presidential Suite', 'desc' => 'Three-bedroom palatial suite with private living room, dining room, study, and wrap-around terrace. Full butler service and private check-in.', 'guests' => 6, 'adults' => 4, 'children' => 2, 'bed' => 'King + King + Twin', 'size' => 200],
        ];
    }

    private function updateMinPrices(): void
    {
        Hotel::all()->each(function ($hotel) {
            $minPrice = $hotel->roomTypes()->min('base_price');
            if ($minPrice) {
                $hotel->update(['min_price' => $minPrice]);
            }
        });
    }
}
