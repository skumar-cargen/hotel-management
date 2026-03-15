<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Domain;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CarAndGeneralBlogSeeder extends Seeder
{
    public function run(): void
    {
        $domain = Domain::where('slug', 'car-and-general')->first();
        if (! $domain) {
            $this->command->warn('Domain car-and-general not found. Skipping blog seeder.');
            return;
        }

        $user = User::first();
        if (! $user) {
            $this->command->warn('No user found. Skipping blog seeder.');
            return;
        }

        // ── Create Categories ──
        $categories = [];
        $categoryData = [
            ['name' => 'Travel Tips', 'slug' => 'travel-tips', 'description' => 'Expert advice for making the most of your UAE travels', 'sort_order' => 1],
            ['name' => 'UAE Destinations', 'slug' => 'uae-destinations', 'description' => 'Explore the best destinations across all seven emirates', 'sort_order' => 2],
            ['name' => 'Luxury Stays', 'slug' => 'luxury-stays', 'description' => 'Inside look at the UAE\'s finest hotels and resorts', 'sort_order' => 3],
            ['name' => 'Food & Dining', 'slug' => 'food-dining', 'description' => 'Culinary experiences from world-class restaurants across the UAE', 'sort_order' => 4],
            ['name' => 'Things To Do', 'slug' => 'things-to-do', 'description' => 'Activities, attractions, and hidden gems across the emirates', 'sort_order' => 5],
            ['name' => 'Travel Guides', 'slug' => 'travel-guides', 'description' => 'Comprehensive guides for exploring the UAE and beyond', 'sort_order' => 6],
        ];

        foreach ($categoryData as $cat) {
            $categories[$cat['slug']] = BlogCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }

        // ── Blog Posts ──
        $posts = [
            [
                'title' => 'Abu Dhabi vs Dubai: Which Emirate Should You Visit First?',
                'slug' => 'abu-dhabi-vs-dubai-which-emirate-visit-first',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1200&q=80',
                'excerpt' => 'Two world-class cities, one incredible country. We compare Abu Dhabi and Dubai to help you decide which emirate deserves your first visit — or why you should explore both.',
                'tags' => ['abu dhabi', 'dubai', 'comparison', 'uae', 'travel'],
                'is_featured' => true,
                'days_ago' => 2,
                'view_count' => 6420,
                'content' => '<h2>The Tale of Two Emirates</h2>
<p>The UAE\'s two largest cities couldn\'t be more different in personality, yet both deliver world-class experiences. Abu Dhabi is the refined cultural capital; Dubai is the ambitious city of superlatives. Here\'s how to choose — or better yet, combine both.</p>

<h3>Abu Dhabi: The Cultural Capital</h3>
<p><strong>Best For:</strong> Art lovers, history buffs, families seeking culture, those who prefer a quieter pace.</p>
<ul>
<li><strong>Louvre Abu Dhabi</strong> — A global art museum with a stunning rain-of-light dome</li>
<li><strong>Sheikh Zayed Grand Mosque</strong> — One of the world\'s most beautiful mosques, free to visit</li>
<li><strong>Emirates Palace</strong> — Palatial luxury on a 1.3km private beach</li>
<li><strong>Saadiyat Island</strong> — Natural beaches, golf, and world-class resorts</li>
<li><strong>Yas Island</strong> — Ferrari World, Yas Waterworld, and Warner Bros World</li>
</ul>

<h3>Dubai: The City of Superlatives</h3>
<p><strong>Best For:</strong> Thrill-seekers, shoppers, nightlife lovers, architecture enthusiasts.</p>
<ul>
<li><strong>Burj Khalifa</strong> — The world\'s tallest building with observation decks</li>
<li><strong>Dubai Mall</strong> — Shopping, dining, aquarium, and the Dubai Fountain</li>
<li><strong>Palm Jumeirah</strong> — Iconic man-made island with luxury resorts</li>
<li><strong>Old Dubai</strong> — Gold Souk, Spice Souk, and abra rides across the Creek</li>
<li><strong>Desert Safari</strong> — Dune bashing, camel rides, and BBQ under the stars</li>
</ul>

<h2>Our Verdict</h2>
<p>If it\'s your first time in the UAE, start with Dubai for the wow factor, then add Abu Dhabi for depth and culture. Ideally, spend 4 days in Dubai and 2-3 days in Abu Dhabi. The two cities are just 90 minutes apart by car, making a combined trip effortless.</p>',
            ],
            [
                'title' => 'The Ultimate Desert Resort Experience in Ras Al Khaimah',
                'slug' => 'ultimate-desert-resort-experience-ras-al-khaimah',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=1200&q=80',
                'excerpt' => 'Escape the city and discover the magic of the desert. From private pool villas to camel trekking at sunrise, Ras Al Khaimah\'s desert resorts offer an unforgettable UAE experience.',
                'tags' => ['desert', 'ras al khaimah', 'luxury', 'resort', 'nature'],
                'is_featured' => true,
                'days_ago' => 5,
                'view_count' => 4850,
                'content' => '<h2>Where Luxury Meets the Wild</h2>
<p>Just an hour from Dubai, the deserts of Ras Al Khaimah offer an experience that feels a world away from the glittering skylines. Here, luxury resorts sit within nature reserves, where Arabian oryx roam free and the silence of the dunes is your soundtrack.</p>

<h3>What Makes Desert Resorts Special</h3>
<p>Unlike beach resorts, desert properties offer complete seclusion. Most feature private villas with their own plunge pools, surrounded by nothing but golden sand and native wildlife. It\'s the ultimate digital detox.</p>

<h3>Must-Try Desert Experiences</h3>
<ul>
<li><strong>Sunrise Camel Trek</strong> — Watch the desert come alive in golden light from atop a camel</li>
<li><strong>Falconry</strong> — Learn about the UAE\'s national bird and see these magnificent raptors in action</li>
<li><strong>Dune Bashing</strong> — An adrenaline-pumping ride across the sand dunes in a 4x4</li>
<li><strong>Archery</strong> — Try your hand at traditional Arabian archery against the dune backdrop</li>
<li><strong>Stargazing</strong> — With zero light pollution, the night sky is breathtaking</li>
<li><strong>Nature Walks</strong> — Spot Arabian oryx, gazelles, and desert foxes in the nature reserve</li>
</ul>

<h3>When to Visit</h3>
<p>The best months for a desert stay are October through April when daytime temperatures are pleasant (22-32°C). Winter evenings can be cool, so pack a light jacket for outdoor dining.</p>

<h2>Planning Your Desert Escape</h2>
<p>Book at least 2 nights to truly unwind. Most resorts offer half-board or full-board packages that include activities. Sunset is magic hour — make sure your villa faces west for the full experience.</p>',
            ],
            [
                'title' => 'Sharjah: The UAE\'s Best-Kept Secret for Culture Lovers',
                'slug' => 'sharjah-uae-best-kept-secret-culture-lovers',
                'category' => 'uae-destinations',
                'featured_image_url' => 'https://images.unsplash.com/photo-1578895101408-1a36b834405b?w=1200&q=80',
                'excerpt' => 'While Dubai and Abu Dhabi grab the headlines, Sharjah quietly offers the UAE\'s richest cultural experience. Museums, heritage areas, and beach resorts at a fraction of the price.',
                'tags' => ['sharjah', 'culture', 'museums', 'heritage', 'uae'],
                'is_featured' => true,
                'days_ago' => 8,
                'view_count' => 3280,
                'content' => '<h2>The Cultural Capital of the Arab World</h2>
<p>UNESCO named Sharjah the Cultural Capital of the Arab World in 1998, and the emirate has been living up to that title ever since. With over 20 museums, a thriving art scene, and beautifully preserved heritage areas, Sharjah offers a depth of cultural experience unmatched in the UAE.</p>

<h3>Must-Visit Cultural Sites</h3>
<ul>
<li><strong>Sharjah Art Museum</strong> — The largest art museum in the Gulf region</li>
<li><strong>Heart of Sharjah</strong> — A beautifully restored heritage district with traditional architecture</li>
<li><strong>Sharjah Museum of Islamic Civilisation</strong> — 5,000+ artefacts spanning 1,400 years</li>
<li><strong>Rain Room</strong> — Walk through falling rain without getting wet at Sharjah Art Foundation</li>
<li><strong>Al Noor Island</strong> — A tranquil island with art installations and a butterfly house</li>
</ul>

<h3>Why Stay in Sharjah?</h3>
<p>Beyond culture, Sharjah offers excellent value. Five-star beach resorts here cost 40-60% less than comparable properties in Dubai, yet you\'re only 20 minutes from Dubai\'s attractions. The beaches are less crowded, the atmosphere more relaxed, and the food scene increasingly impressive.</p>

<h3>The Sharjah Corniche</h3>
<p>The stunning waterfront promenade stretches along the Arabian Gulf, lined with parks, playgrounds, and cafes. Evening walks here are a local tradition, with the sunset over the water providing a spectacular daily show.</p>

<h2>Getting There</h2>
<p>Sharjah is 15 minutes from Dubai International Airport by car. Many visitors base themselves in Sharjah and day-trip to Dubai, enjoying the best of both worlds at a fraction of the cost.</p>',
            ],
            [
                'title' => 'A Food Lover\'s Guide to the UAE: 15 Dishes You Must Try',
                'slug' => 'food-lovers-guide-uae-15-dishes-must-try',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80',
                'excerpt' => 'From Abu Dhabi\'s fine dining to Sharjah\'s street food, the UAE is a culinary paradise. Here are 15 dishes every food lover needs to try across the emirates.',
                'tags' => ['food', 'cuisine', 'uae', 'restaurants', 'dining'],
                'is_featured' => true,
                'days_ago' => 12,
                'view_count' => 5670,
                'content' => '<h2>A Melting Pot of Flavours</h2>
<p>The UAE\'s diverse population has created one of the world\'s most exciting food scenes. From traditional Emirati dishes to global cuisines, here are the must-try dishes across the emirates.</p>

<h3>Traditional Emirati</h3>
<ol>
<li><strong>Al Harees</strong> — A slow-cooked wheat and meat porridge, especially popular during Ramadan. Try it at Mezlai in Emirates Palace, Abu Dhabi.</li>
<li><strong>Al Machboos</strong> — The UAE\'s national dish: spiced rice with meat, dried lime, and aromatic spices. Every emirate has its own version.</li>
<li><strong>Luqaimat</strong> — Golden fried dumplings drizzled with date syrup. The perfect sweet treat at any heritage restaurant.</li>
<li><strong>Balaleet</strong> — Sweet vermicelli topped with a saffron omelette. A beloved Emirati breakfast staple.</li>
</ol>

<h3>Street Food Favourites</h3>
<ol start="5">
<li><strong>Shawarma</strong> — The UAE version is legendary. Al Mallah in Dubai and Aroos Damascus in Sharjah are institutions.</li>
<li><strong>Manakish</strong> — Za\'atar-topped flatbread, the ultimate Middle Eastern breakfast. Found at bakeries everywhere.</li>
<li><strong>Karak Chai</strong> — The UAE\'s unofficial national drink: strong tea with cardamom, saffron, and evaporated milk. Just 1 AED at most cafeterias.</li>
<li><strong>Falafel Wrap</strong> — Crispy, herbaceous, and served with tahini in warm bread. Budget perfection.</li>
</ol>

<h3>Fine Dining Highlights</h3>
<ol start="9">
<li><strong>Wagyu at Nusr-Et</strong> — Salt Bae\'s empire started in Dubai. The gold-wrapped tomahawk is pure theatre.</li>
<li><strong>Sushi at Zuma</strong> — Modern Japanese izakaya dining at its finest, in Dubai\'s DIFC.</li>
<li><strong>Seafood at Bu Qtair</strong> — A legendary no-frills fish shack near Jumeira. The masala prawns are unforgettable.</li>
<li><strong>Lebanese Mezze</strong> — The UAE has incredible Lebanese restaurants. Al Hallab and Em Sherif are standouts.</li>
</ol>

<h3>Sweet Endings</h3>
<ol start="13">
<li><strong>Kunafa</strong> — Crispy shredded pastry with molten cheese, soaked in sweet syrup. Addictive.</li>
<li><strong>Camel Milk Ice Cream</strong> — A uniquely Emirati treat. Try it at Al Nassma stores across the UAE.</li>
<li><strong>Date Pudding</strong> — Sticky toffee meets Emirati dates. The perfect end to any meal.</li>
</ol>',
            ],
            [
                'title' => 'Saadiyat Island: Where Art Meets the Arabian Gulf',
                'slug' => 'saadiyat-island-art-meets-arabian-gulf',
                'category' => 'uae-destinations',
                'featured_image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80',
                'excerpt' => 'Home to the Louvre Abu Dhabi, natural beaches, and world-class resorts — Saadiyat Island is the UAE\'s most cultured island escape. Here\'s your complete guide.',
                'tags' => ['saadiyat', 'abu dhabi', 'louvre', 'art', 'beach'],
                'is_featured' => false,
                'days_ago' => 16,
                'view_count' => 2940,
                'content' => '<h2>The Cultural Island</h2>
<p>Saadiyat Island is Abu Dhabi\'s crown jewel — a natural island that seamlessly blends world-class culture with pristine beaches and luxury hospitality. It\'s unlike anywhere else in the UAE.</p>

<h3>The Louvre Abu Dhabi</h3>
<p>The iconic museum, designed by Jean Nouvel, is worth the trip alone. Its "rain of light" dome creates a mesmerising play of sunlight and shadow over galleries housing masterpieces from around the world. Allow at least 3 hours for a proper visit.</p>

<h3>Natural Beaches</h3>
<p>Unlike many UAE beaches, Saadiyat\'s are natural — no land reclamation, no imported sand. The result is a stunning 9km stretch of pristine coastline with turquoise waters. Hawksbill turtles nest here between April and July.</p>

<h3>Where to Stay</h3>
<p>The island\'s resorts are designed to complement the natural environment. Low-rise architecture, eco-conscious design, and direct beach access define the experience. Wake up, walk to the beach, spend the afternoon at the Louvre — it\'s a perfectly balanced island escape.</p>

<h3>Golf on the Gulf</h3>
<p>Saadiyat Beach Golf Club offers an 18-hole championship course designed by Gary Player. Several holes run alongside the ocean, making it one of the most scenic courses in the Middle East.</p>

<h2>Getting There</h2>
<p>Saadiyat Island is just 20 minutes from Abu Dhabi city centre and 75 minutes from Dubai. A dedicated bridge connects the island to the mainland.</p>',
            ],
            [
                'title' => 'The Best Spa & Wellness Retreats Across the UAE',
                'slug' => 'best-spa-wellness-retreats-uae',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1600011689032-8b628b8a8747?w=1200&q=80',
                'excerpt' => 'From traditional hammams to cutting-edge wellness centres, the UAE offers some of the world\'s most luxurious spa experiences. Here are the ones worth booking.',
                'tags' => ['spa', 'wellness', 'luxury', 'hammam', 'relaxation'],
                'is_featured' => false,
                'days_ago' => 20,
                'view_count' => 2150,
                'content' => '<h2>Wellness Across the Emirates</h2>
<p>The UAE has become a global wellness destination, with hotel spas that rival the best in the world. From ancient Arabian treatments to modern holistic programmes, here\'s where to find the ultimate relaxation.</p>

<h3>Abu Dhabi Highlights</h3>
<p><strong>Emirates Palace Spa:</strong> A 2,400 sqm oasis offering traditional hammam rituals, gold facials, and treatments using local ingredients like camel milk and desert rose. The setting is as palatial as the hotel itself.</p>
<p><strong>Four Seasons Abu Dhabi Spa:</strong> Sleek, modern wellness with a focus on result-driven treatments. The rooftop relaxation terrace with skyline views is exceptional.</p>

<h3>Desert Wellness</h3>
<p><strong>Ritz-Carlton Al Wadi Spa:</strong> Set within a desert nature reserve, treatments here draw on ancient Arabian wellness traditions. The outdoor treatment pavilions overlooking the dunes are magical. Try the desert sand body scrub followed by an argan oil massage.</p>

<h3>Sharjah Escapes</h3>
<p><strong>Sheraton Sharjah Shine Spa:</strong> A hidden gem with excellent therapists and prices significantly lower than Dubai or Abu Dhabi. The beachfront setting adds to the relaxation.</p>

<h3>Ras Al Khaimah</h3>
<p><strong>Waldorf Astoria Spa:</strong> 12 treatment rooms, a Turkish hammam, and therapists trained in both Eastern and Western techniques. The post-treatment relaxation room overlooks the Arabian Gulf.</p>

<h2>Tips for Booking</h2>
<ul>
<li>Weekday appointments are typically 20-30% cheaper</li>
<li>Many hotels offer spa day passes including pool and beach access</li>
<li>Ask about couples\' packages for a romantic experience</li>
<li>Book at least a week in advance for weekend slots</li>
</ul>',
            ],
            [
                'title' => '7 Family-Friendly Activities Beyond the Hotels',
                'slug' => '7-family-friendly-activities-beyond-hotels',
                'category' => 'things-to-do',
                'featured_image_url' => 'https://images.unsplash.com/photo-1602002418816-5c0aeef426aa?w=1200&q=80',
                'excerpt' => 'Travelling with kids in the UAE? These family-friendly activities across Abu Dhabi, Dubai, Sharjah, and Ras Al Khaimah will keep everyone entertained.',
                'tags' => ['family', 'kids', 'activities', 'uae', 'adventure'],
                'is_featured' => false,
                'days_ago' => 24,
                'view_count' => 3890,
                'content' => '<h2>Fun for the Whole Family</h2>
<p>The UAE is a paradise for families. Beyond the incredible hotel facilities, there\'s an extraordinary range of activities across the emirates that will create memories to last a lifetime.</p>

<h3>1. Ferrari World — Abu Dhabi</h3>
<p>The world\'s largest indoor theme park, home to Formula Rossa — the fastest roller coaster on the planet at 240 km/h. Younger kids love the Junior Grand Prix and the Speedway racing school.</p>

<h3>2. Sharjah Aquarium & Maritime Museum</h3>
<p>A brilliant combo for curious kids. The aquarium showcases Arabian Gulf marine life, while the maritime museum tells the story of the UAE\'s pearl diving heritage. Interactive and affordable.</p>

<h3>3. Jebel Jais Zipline — Ras Al Khaimah</h3>
<p>The world\'s longest zipline at 2.83km, soaring over the Hajar Mountains at speeds up to 150 km/h. Children aged 12+ can ride (younger kids can try the shorter Jais Sky Tour).</p>

<h3>4. Yas Waterworld — Abu Dhabi</h3>
<p>43 rides and slides, including the world\'s first hydromagnetic-powered waterslide. The pearl diving experience teaches kids about Emirati heritage while having a blast.</p>

<h3>5. Dubai Miracle Garden</h3>
<p>150 million flowers arranged into incredible structures, including a full-size A380 aircraft covered in flowers. Open October to April, it\'s a magical experience for all ages.</p>

<h3>6. Al Wadi Nature Reserve — Ras Al Khaimah</h3>
<p>Spot Arabian oryx, gazelles, and desert foxes in their natural habitat. Guided nature walks and camel rides make it educational and exciting. Much more authentic than a zoo.</p>

<h3>7. Sharjah Discovery Centre</h3>
<p>An interactive science museum designed specifically for children aged 3-12. Hands-on exhibits about the human body, aviation, water, and construction. Budget-friendly and air-conditioned — perfect for hot days.</p>',
            ],
            [
                'title' => 'Weekend Getaway: 48 Hours in Ras Al Khaimah',
                'slug' => 'weekend-getaway-48-hours-ras-al-khaimah',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1542401886-65d6c61db217?w=1200&q=80',
                'excerpt' => 'Mountains, beaches, desert, and luxury — all within one emirate. Here\'s how to spend the perfect weekend in Ras Al Khaimah, the UAE\'s adventure capital.',
                'tags' => ['ras al khaimah', 'weekend', 'getaway', 'adventure', 'mountains'],
                'is_featured' => false,
                'days_ago' => 28,
                'view_count' => 2560,
                'content' => '<h2>The Adventure Emirate</h2>
<p>Ras Al Khaimah is the UAE\'s best-kept secret — a stunning emirate where the Hajar Mountains meet the Arabian Gulf, and where luxury resorts sit beside ancient forts. It\'s just 45 minutes from Dubai but feels like another world.</p>

<h3>Day 1: Beach & Golf</h3>
<p><strong>Morning:</strong> Start with breakfast at your beachfront resort in Al Hamra Village. The private beach is pristine and uncrowded.</p>
<p><strong>Midday:</strong> Play a round at Al Hamra Golf Club or Tower Links, both championship courses with stunning views.</p>
<p><strong>Afternoon:</strong> Relax by the pool or explore the Al Hamra Marina.</p>
<p><strong>Evening:</strong> Sunset dinner at a beachfront restaurant. The views of the Arabian Gulf are spectacular.</p>

<h3>Day 2: Mountains & Desert</h3>
<p><strong>Morning:</strong> Drive up Jebel Jais, the UAE\'s highest peak (1,934m). Try the world\'s longest zipline or simply enjoy the mountain views and cooler temperatures.</p>
<p><strong>Afternoon:</strong> Visit Dhayah Fort, a hilltop fortress with panoramic views, or explore the old town of RAK city.</p>
<p><strong>Evening:</strong> Check into a desert resort at Al Wadi Nature Reserve. Enjoy a sunset camel trek followed by dinner under the stars.</p>

<h2>Getting There</h2>
<p>RAK is 45 minutes from Dubai by car (no tolls), 90 minutes from Abu Dhabi. The emirate also has its own airport with growing international connections.</p>

<h2>Best Time to Visit</h2>
<p>October to April offers the most pleasant weather. December and January are ideal for outdoor activities in the mountains and desert.</p>',
            ],
            [
                'title' => 'Rooftop Pools with the Best Views in the UAE',
                'slug' => 'rooftop-pools-best-views-uae',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?w=1200&q=80',
                'excerpt' => 'Nothing says UAE luxury like an infinity pool with skyline views. Here are the most stunning rooftop pools across Abu Dhabi, Dubai, and beyond.',
                'tags' => ['pool', 'rooftop', 'luxury', 'views', 'hotels'],
                'is_featured' => false,
                'days_ago' => 33,
                'view_count' => 4120,
                'content' => '<h2>Sky-High Swimming</h2>
<p>The UAE takes rooftop pools to another level — literally. From infinity edges that seem to merge with the skyline to temperature-controlled oases high above the desert, these pools are worth booking a hotel stay for.</p>

<h3>Abu Dhabi</h3>
<p><strong>Four Seasons Abu Dhabi:</strong> The rooftop infinity pool on Al Maryah Island offers 360-degree views of the Abu Dhabi skyline. The sunset hour here is extraordinary, with the city lights reflecting off the water as day turns to night.</p>

<h3>Dubai</h3>
<p><strong>Address Downtown:</strong> Perhaps the most Instagrammed pool in Dubai — the infinity pool seemingly flows into the Burj Khalifa. Evening swims with the illuminated skyline are magical.</p>
<p><strong>FIVE Palm Jumeirah:</strong> The rooftop pool here doubles as one of Dubai\'s most vibrant social scenes. Beach club vibes with panoramic views of the Dubai Marina skyline.</p>

<h3>Sharjah</h3>
<p><strong>Hilton Sharjah:</strong> The rooftop infinity pool overlooking Al Khan lagoon is a hidden gem. Peaceful, uncrowded, and offering beautiful views of both the lagoon and the sea.</p>

<h3>Ras Al Khaimah</h3>
<p><strong>Waldorf Astoria:</strong> The main infinity pool stretches towards the Arabian Gulf, surrounded by palm trees and attended by excellent staff. It\'s relaxation perfected.</p>

<h2>Pool Day Tips</h2>
<ul>
<li>Most hotel pools allow non-guest day passes (250-500 AED)</li>
<li>Weekdays are significantly quieter than weekends</li>
<li>Book a cabana for shade and premium service</li>
<li>Evening swims (after 5 PM) offer cooler temperatures and stunning lighting</li>
</ul>',
            ],
            [
                'title' => 'First-Timer\'s Complete Guide to the UAE: 2026 Edition',
                'slug' => 'first-timers-complete-guide-uae-2026',
                'category' => 'travel-tips',
                'featured_image_url' => 'https://images.unsplash.com/photo-1470723710355-95304d8aece4?w=1200&q=80',
                'excerpt' => 'Everything you need to know before your first trip to the UAE — from visa requirements and cultural etiquette to the best time to visit and money-saving tips.',
                'tags' => ['uae', 'guide', 'first time', 'travel', 'tips'],
                'is_featured' => false,
                'days_ago' => 38,
                'view_count' => 7240,
                'content' => '<h2>Welcome to the United Arab Emirates</h2>
<p>The UAE is a federation of seven emirates, each with its own character. Whether you\'re drawn by desert adventures, beach luxury, cultural treasures, or futuristic cityscapes, this guide covers everything you need for an incredible first visit.</p>

<h3>When to Visit</h3>
<p>The UAE has two seasons: hot (May–September, 38-48°C) and perfect (October–April, 20-32°C). The peak tourist season is December through February. For the best hotel deals, visit in March–April or October–November.</p>

<h3>Visa & Entry</h3>
<p>Citizens of over 80 countries receive visa-on-arrival for 30-90 days. All visitors need a passport valid for at least 6 months. Check the latest requirements on the UAE government portal.</p>

<h3>Currency & Costs</h3>
<p>The UAE dirham (AED) is pegged to the US dollar at 3.67 AED = 1 USD. Cards are accepted almost everywhere. Budget travellers can manage on 300-500 AED/day; luxury travellers typically spend 1,500-3,000 AED/day.</p>

<h3>Getting Around</h3>
<ul>
<li><strong>Between Emirates:</strong> Rental cars are affordable (120-200 AED/day) and roads are excellent. Intercity buses are available but slow.</li>
<li><strong>Within Dubai:</strong> Metro, taxis, and ride-hailing apps (Careem, Uber)</li>
<li><strong>Within Abu Dhabi:</strong> Taxis and ride-hailing are the most practical options</li>
</ul>

<h3>Cultural Etiquette</h3>
<ul>
<li>Dress modestly in public places (shoulders and knees covered in malls and heritage sites)</li>
<li>Alcohol is only available in licensed venues (hotels, certain restaurants)</li>
<li>During Ramadan, eating and drinking in public during daylight hours is restricted</li>
<li>Photography — always ask before photographing local people</li>
<li>Friday is the holy day; many businesses have different hours</li>
</ul>

<h3>Must-Have Apps</h3>
<ul>
<li><strong>Careem/Uber</strong> — Ride-hailing</li>
<li><strong>Talabat/Deliveroo</strong> — Food delivery</li>
<li><strong>Google Maps</strong> — Navigation (works perfectly across the UAE)</li>
<li><strong>Visit Abu Dhabi / Visit Dubai</strong> — Official tourism apps with deals and guides</li>
</ul>',
            ],
        ];

        foreach ($posts as $postData) {
            $category = $categories[$postData['category']];

            // Download featured image
            $imagePath = null;
            if (! empty($postData['featured_image_url'])) {
                try {
                    $imageContents = file_get_contents($postData['featured_image_url']);
                    if ($imageContents) {
                        $filename = 'blog/' . Str::random(40) . '.jpg';
                        Storage::disk('public')->put($filename, $imageContents);
                        $imagePath = $filename;
                    }
                } catch (\Exception $e) {
                    $this->command->warn("Failed to download image for: {$postData['title']}");
                }
            }

            $post = BlogPost::updateOrCreate(
                ['slug' => $postData['slug']],
                [
                    'title' => $postData['title'],
                    'blog_category_id' => $category->id,
                    'user_id' => $user->id,
                    'content' => $postData['content'],
                    'excerpt' => $postData['excerpt'],
                    'featured_image' => $imagePath,
                    'tags' => $postData['tags'],
                    'status' => 'published',
                    'published_at' => Carbon::now()->subDays($postData['days_ago']),
                    'is_featured' => $postData['is_featured'],
                    'is_active' => true,
                    'view_count' => $postData['view_count'],
                    'meta_title' => $postData['title'] . ' | Car & General Hotels',
                    'meta_description' => $postData['excerpt'],
                ]
            );

            // Attach to domain
            $post->domains()->syncWithoutDetaching([$domain->id]);
        }

        $this->command->info('Seeded ' . count($posts) . ' blog posts with ' . count($categories) . ' categories for car-and-general domain.');
    }
}
