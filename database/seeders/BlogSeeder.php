<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BlogSeeder extends Seeder
{
    private int $imageCounter = 1;

    public function run(): void
    {
        $this->command->info('Seeding blog data...');

        $categories = $this->seedCategories();
        $this->seedPosts($categories);

        $this->command->info('Blog data seeded successfully.');
    }

    private function seedCategories(): array
    {
        $items = [
            [
                'name' => 'Travel Tips',
                'slug' => 'travel-tips',
                'description' => 'Expert advice and insider tips for planning your perfect Dubai getaway.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Dubai Attractions',
                'slug' => 'dubai-attractions',
                'description' => 'Explore the best landmarks, theme parks, and must-visit spots in Dubai.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Luxury Living',
                'slug' => 'luxury-living',
                'description' => 'Discover the finest luxury experiences, hotels, and lifestyle in Dubai.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Food & Dining',
                'slug' => 'food-dining',
                'description' => 'From street food to Michelin-starred restaurants — the best dining in Dubai.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Events & Festivals',
                'slug' => 'events-festivals',
                'description' => 'Stay updated on upcoming events, festivals, and seasonal happenings in Dubai.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Neighbourhood Guides',
                'slug' => 'neighbourhood-guides',
                'description' => 'In-depth guides to Dubai\'s most popular neighbourhoods and communities.',
                'sort_order' => 6,
            ],
        ];

        $categories = [];
        foreach ($items as $item) {
            $categories[] = BlogCategory::create($item);
        }

        $this->command->info('  → Created '.count($categories).' blog categories');

        return $categories;
    }

    private function seedPosts(array $categories): void
    {
        $domains = Domain::all();
        $author = User::where('email', 'admin@dubaihotels.com')->first();
        $authorId = $author?->id ?? 1;

        $posts = [
            // ── Travel Tips ──
            [
                'title' => '10 Essential Tips for First-Time Visitors to Dubai',
                'blog_category_id' => $categories[0]->id,
                'excerpt' => 'Planning your first trip to Dubai? From visa requirements to cultural etiquette, here are the top things every first-time visitor should know before landing in the City of Gold.',
                'content' => $this->travelTipsContent1(),
                'tags' => ['first-time', 'travel-guide', 'dubai-tips', 'visa'],
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'view_count' => 1247,
                'meta_title' => '10 Essential Tips for First-Time Visitors to Dubai | Travel Guide',
                'meta_description' => 'Planning your first Dubai trip? Discover essential tips on visa, weather, transport, dress code, and cultural etiquette for an unforgettable visit.',
                'meta_keywords' => 'dubai travel tips, first time dubai, dubai visa, dubai guide',
            ],
            [
                'title' => 'Best Time to Visit Dubai: A Month-by-Month Guide',
                'blog_category_id' => $categories[0]->id,
                'excerpt' => 'Dubai offers something special every month. Discover the ideal time to visit based on weather, events, hotel rates, and crowd levels throughout the year.',
                'content' => $this->travelTipsContent2(),
                'tags' => ['best-time', 'weather', 'planning', 'seasons'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'view_count' => 893,
                'meta_title' => 'Best Time to Visit Dubai — Month-by-Month Weather & Events Guide',
                'meta_description' => 'Find out the best month to visit Dubai. Compare weather, hotel prices, events, and tourist seasons to plan your perfect trip.',
                'meta_keywords' => 'best time dubai, dubai weather, dubai seasons, when to visit dubai',
            ],
            [
                'title' => 'How to Get Around Dubai: Complete Transport Guide',
                'blog_category_id' => $categories[0]->id,
                'excerpt' => 'Metro, taxis, buses, water taxis, and ride-hailing apps — everything you need to know about getting around Dubai easily and affordably.',
                'content' => $this->transportGuideContent(),
                'tags' => ['transport', 'metro', 'taxi', 'getting-around'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(8),
                'view_count' => 654,
                'meta_title' => 'Dubai Transport Guide: Metro, Taxi, Bus & More',
                'meta_description' => 'Complete guide to public transport in Dubai. Learn about the Metro, RTA buses, taxis, water taxis, and ride-hailing apps.',
                'meta_keywords' => 'dubai transport, dubai metro, dubai taxi, nol card',
            ],

            // ── Dubai Attractions ──
            [
                'title' => 'Top 15 Must-Visit Attractions in Dubai for 2026',
                'blog_category_id' => $categories[1]->id,
                'excerpt' => 'From the iconic Burj Khalifa to the stunning Museum of the Future, here are the top attractions you simply cannot miss on your Dubai trip this year.',
                'content' => $this->attractionsContent1(),
                'tags' => ['attractions', 'sightseeing', 'burj-khalifa', 'must-visit'],
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
                'view_count' => 2034,
                'meta_title' => 'Top 15 Dubai Attractions You Must Visit in 2026',
                'meta_description' => 'Explore the best attractions in Dubai including Burj Khalifa, Dubai Mall, Museum of the Future, Palm Jumeirah, and more.',
                'meta_keywords' => 'dubai attractions, things to do dubai, burj khalifa, dubai mall',
            ],
            [
                'title' => 'Desert Safari Dubai: Everything You Need to Know',
                'blog_category_id' => $categories[1]->id,
                'excerpt' => 'A desert safari is the quintessential Dubai experience. Learn about different safari types, what to expect, what to wear, and how to book the best one.',
                'content' => $this->desertSafariContent(),
                'tags' => ['desert-safari', 'adventure', 'outdoor', 'experience'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'view_count' => 1567,
                'meta_title' => 'Desert Safari Dubai: Complete Guide, Prices & Tips',
                'meta_description' => 'Everything you need to know about desert safari in Dubai. Types, prices, what to expect, and tips for the best experience.',
                'meta_keywords' => 'desert safari dubai, dune bashing, camel ride, dubai desert',
            ],
            [
                'title' => 'Exploring Dubai Creek: Where Old Meets New',
                'blog_category_id' => $categories[1]->id,
                'excerpt' => 'Step away from the skyscrapers and discover the historical heart of Dubai. Dubai Creek offers a glimpse into the city\'s trading past with souks, abra rides, and authentic culture.',
                'content' => $this->dubaiCreekContent(),
                'tags' => ['dubai-creek', 'history', 'culture', 'souks'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(14),
                'view_count' => 432,
                'meta_title' => 'Dubai Creek Guide: History, Souks & Abra Rides',
                'meta_description' => 'Explore the historic Dubai Creek — ride an abra, visit gold and spice souks, and discover the cultural heritage of old Dubai.',
                'meta_keywords' => 'dubai creek, gold souk, spice souk, abra ride, old dubai',
            ],

            // ── Luxury Living ──
            [
                'title' => 'The Ultimate Guide to Luxury Hotels in Dubai',
                'blog_category_id' => $categories[2]->id,
                'excerpt' => 'Dubai is home to some of the world\'s most luxurious hotels. From underwater suites to private butler service, discover the finest stays the city has to offer.',
                'content' => $this->luxuryHotelsContent(),
                'tags' => ['luxury', 'hotels', '5-star', 'premium'],
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(1),
                'view_count' => 1890,
                'meta_title' => 'Best Luxury Hotels in Dubai: Ultimate Guide',
                'meta_description' => 'Discover Dubai\'s finest luxury hotels featuring world-class amenities, stunning views, and unparalleled service.',
                'meta_keywords' => 'luxury hotels dubai, 5 star hotels dubai, best hotels dubai',
            ],
            [
                'title' => 'Dubai Marina: Living the Waterfront Dream',
                'blog_category_id' => $categories[2]->id,
                'excerpt' => 'Dubai Marina is one of the most sought-after neighborhoods in the city. Explore the stunning waterfront promenade, dining options, and apartment living.',
                'content' => $this->dubaiMarinaContent(),
                'tags' => ['dubai-marina', 'waterfront', 'lifestyle', 'apartments'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
                'view_count' => 756,
                'meta_title' => 'Dubai Marina Guide: Waterfront Living, Dining & Activities',
                'meta_description' => 'Everything about Dubai Marina — waterfront apartments, restaurants, The Walk, yacht cruises, and why it\'s the best place to stay.',
                'meta_keywords' => 'dubai marina, marina walk, dubai marina apartments, waterfront dubai',
            ],

            // ── Food & Dining ──
            [
                'title' => 'Best Restaurants in Dubai: From Street Food to Fine Dining',
                'blog_category_id' => $categories[3]->id,
                'excerpt' => 'Dubai\'s food scene is as diverse as its population. Explore the best restaurants, hidden gems, and must-try dishes across every budget and cuisine.',
                'content' => $this->restaurantsContent(),
                'tags' => ['restaurants', 'food', 'dining', 'cuisine'],
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(4),
                'view_count' => 1345,
                'meta_title' => 'Best Restaurants in Dubai 2026: Street Food to Fine Dining',
                'meta_description' => 'Discover the best restaurants in Dubai across all budgets. From authentic Emirati cuisine to world-class fine dining.',
                'meta_keywords' => 'best restaurants dubai, dubai food, fine dining dubai, street food dubai',
            ],
            [
                'title' => 'Dubai Street Food Guide: 12 Dishes You Must Try',
                'blog_category_id' => $categories[3]->id,
                'excerpt' => 'Skip the fancy restaurants for a meal and explore Dubai\'s incredible street food scene. From shawarma to luqaimat, these are the dishes that define the city\'s food culture.',
                'content' => $this->streetFoodContent(),
                'tags' => ['street-food', 'budget', 'shawarma', 'local-food'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(12),
                'view_count' => 987,
                'meta_title' => '12 Must-Try Street Foods in Dubai | Budget Food Guide',
                'meta_description' => 'Explore the best street food in Dubai. Must-try dishes including shawarma, falafel, luqaimat, and more affordable eats.',
                'meta_keywords' => 'dubai street food, cheap food dubai, shawarma dubai, local food dubai',
            ],

            // ── Events & Festivals ──
            [
                'title' => 'Dubai Shopping Festival 2026: Dates, Deals & What to Expect',
                'blog_category_id' => $categories[4]->id,
                'excerpt' => 'The Dubai Shopping Festival is one of the biggest retail events in the world. Get the complete guide to dates, mega sales, entertainment, and raffle draws.',
                'content' => $this->shoppingFestivalContent(),
                'tags' => ['dsf', 'shopping', 'festival', 'deals'],
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(6),
                'view_count' => 2456,
                'meta_title' => 'Dubai Shopping Festival 2026: Complete Guide, Dates & Deals',
                'meta_description' => 'Everything about Dubai Shopping Festival 2026 — dates, best deals, entertainment, raffle draws, and tips for the ultimate shopping experience.',
                'meta_keywords' => 'dubai shopping festival, dsf 2026, dubai deals, shopping dubai',
            ],
            [
                'title' => 'Ramadan in Dubai: A Visitor\'s Complete Guide',
                'blog_category_id' => $categories[4]->id,
                'excerpt' => 'Visiting Dubai during Ramadan? Learn about the rules, iftar experiences, special events, and why this holy month is actually a wonderful time to visit.',
                'content' => $this->ramadanGuideContent(),
                'tags' => ['ramadan', 'culture', 'iftar', 'religion'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(15),
                'view_count' => 1123,
                'meta_title' => 'Ramadan in Dubai: Visitor Guide, Iftar & Rules',
                'meta_description' => 'Complete guide to visiting Dubai during Ramadan. Etiquette, iftar experiences, special events, and tips for non-Muslim visitors.',
                'meta_keywords' => 'ramadan dubai, iftar dubai, dubai during ramadan, ramadan rules',
            ],

            // ── Neighbourhood Guides ──
            [
                'title' => 'Downtown Dubai: The Complete Neighbourhood Guide',
                'blog_category_id' => $categories[5]->id,
                'excerpt' => 'Downtown Dubai is the heart of the city — home to Burj Khalifa, Dubai Mall, and the Dubai Fountain. Here\'s everything you need to know about staying and exploring this iconic district.',
                'content' => $this->downtownGuideContent(),
                'tags' => ['downtown', 'burj-khalifa', 'dubai-mall', 'neighbourhood'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(9),
                'view_count' => 876,
                'meta_title' => 'Downtown Dubai Guide: Hotels, Attractions & Things to Do',
                'meta_description' => 'Complete guide to Downtown Dubai — Burj Khalifa, Dubai Mall, Dubai Fountain, best hotels, restaurants, and insider tips.',
                'meta_keywords' => 'downtown dubai, burj khalifa area, dubai mall area, stay downtown dubai',
            ],
            [
                'title' => 'JBR & Bluewaters Island: Sun, Sand, and Entertainment',
                'blog_category_id' => $categories[5]->id,
                'excerpt' => 'Jumeirah Beach Residence and Bluewaters Island offer the perfect blend of beach life, dining, shopping, and entertainment. Discover why this area is a tourist favourite.',
                'content' => $this->jbrGuideContent(),
                'tags' => ['jbr', 'bluewaters', 'beach', 'ain-dubai'],
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(11),
                'view_count' => 645,
                'meta_title' => 'JBR & Bluewaters Island Guide: Beach, Dining & Ain Dubai',
                'meta_description' => 'Explore JBR and Bluewaters Island — The Beach, Ain Dubai, The Walk, best restaurants, and hotel recommendations.',
                'meta_keywords' => 'jbr dubai, bluewaters island, ain dubai, jumeirah beach residence',
            ],

            // ── Draft & Archived posts ──
            [
                'title' => 'Dubai Summer Survival Guide: Beat the Heat',
                'blog_category_id' => $categories[0]->id,
                'excerpt' => 'Visiting Dubai in summer? It doesn\'t have to be unbearable. Here are our best tips for staying cool and making the most of incredible summer deals.',
                'content' => '<h2>Coming Soon</h2><p>This article is currently being written and will be published before summer 2026.</p>',
                'tags' => ['summer', 'heat', 'deals', 'indoor-activities'],
                'is_featured' => false,
                'status' => 'draft',
                'published_at' => null,
                'view_count' => 0,
            ],
            [
                'title' => 'New Year\'s Eve in Dubai: Where to Watch the Fireworks',
                'blog_category_id' => $categories[4]->id,
                'excerpt' => 'Dubai puts on one of the world\'s most spectacular New Year\'s Eve celebrations. Find the best spots to watch the fireworks at Burj Khalifa and beyond.',
                'content' => '<h2>Updated for 2025-2026</h2><p>This article covered the NYE 2025-2026 celebrations and has been archived.</p>',
                'tags' => ['new-years', 'fireworks', 'burj-khalifa', 'celebration'],
                'is_featured' => false,
                'status' => 'archived',
                'published_at' => Carbon::now()->subDays(90),
                'view_count' => 3210,
            ],
        ];

        $domainIds = $domains->pluck('id')->toArray();

        foreach ($posts as $i => $data) {
            $data['user_id'] = $authorId;
            $data['sort_order'] = $i;

            // Download featured image
            $slug = \Illuminate\Support\Str::slug($data['title']);
            $imagePath = $this->downloadImage("blog/{$slug}.jpg", 1200, 630);
            $data['featured_image'] = $imagePath;
            $data['og_image'] = $imagePath;

            $post = BlogPost::create($data);

            // Attach to domains — all published posts go to all domains,
            // draft/archived only to first domain
            if ($data['status'] === 'published') {
                $post->domains()->attach($domainIds);
            } else {
                $post->domains()->attach($domainIds[0] ?? $domainIds);
            }
        }

        $this->command->info('  → Created '.count($posts).' blog posts ('.collect($posts)->where('status', 'published')->count().' published)');
    }

    // ── Content Methods ──

    private function travelTipsContent1(): string
    {
        return <<<'HTML'
<h2>1. Check Your Visa Requirements Early</h2>
<p>Many nationalities receive a visa on arrival in the UAE, but it's essential to verify your specific requirements well in advance. Citizens of GCC countries don't need a visa, while visitors from the US, UK, EU, and many other countries get a 30 or 90-day visa on arrival. For other nationalities, you'll need to apply for a tourist visa through an airline, hotel, or travel agency.</p>

<h2>2. Respect the Local Culture</h2>
<p>Dubai is a cosmopolitan city, but it's important to respect Islamic culture and traditions. Dress modestly when visiting mosques, government buildings, and older parts of the city. Swimwear is fine at the beach and pool, but cover up when you leave these areas. Public displays of affection should be kept to a minimum.</p>

<h2>3. Plan Around the Weather</h2>
<p>Dubai's weather varies dramatically throughout the year. The best time to visit is between November and March when temperatures are pleasant (20-30°C). Summer months (June to September) see temperatures exceeding 45°C, though you'll find incredible hotel deals and almost everything is air-conditioned.</p>

<h2>4. Get a Nol Card for Public Transport</h2>
<p>The Nol card is your key to Dubai's excellent public transport system. It works on the Metro, buses, water buses, and the tram. You can purchase one at any Metro station. The Red and Green Metro lines cover most tourist areas, and it's significantly cheaper than taking taxis everywhere.</p>

<h2>5. Download Essential Apps</h2>
<p>Before you arrive, download these apps: RTA Dubai (public transport), Careem or Uber (ride-hailing), Zomato or Talabat (food delivery), and Visit Dubai (official tourism app). These will make navigating the city much easier.</p>

<h2>6. Cash vs Cards</h2>
<p>Dubai is very card-friendly, and you can pay by card almost everywhere including taxis and small shops. However, carrying some cash in AED is useful for souks, small cafes, and tipping. ATMs are widely available, and the currency (AED) is pegged to the USD at approximately 3.67 AED per dollar.</p>

<h2>7. Stay Hydrated</h2>
<p>The desert climate means you'll need to drink much more water than usual, especially if you're visiting during warmer months. Carry a reusable water bottle — tap water in Dubai is safe to drink, and many malls and hotels have water fountains.</p>

<h2>8. Friday is the Weekend</h2>
<p>The UAE weekend is Saturday and Sunday, but Friday remains an important day. Friday brunch is a beloved Dubai tradition, and many attractions may have different hours. Plan your itinerary accordingly and book Friday brunch well in advance at popular venues.</p>

<h2>9. Haggling is Expected in Souks</h2>
<p>When shopping at traditional souks like the Gold Souk or Spice Souk, haggling is expected and part of the experience. Start at about 50-60% of the asking price and negotiate from there. Be friendly and don't feel pressured — you can always walk away and come back.</p>

<h2>10. Book Attractions in Advance</h2>
<p>Popular attractions like Burj Khalifa's At the Top, Dubai Frame, and the Museum of the Future often sell out, especially during peak season (December-February). Book online in advance to secure your preferred time slot and often save money compared to walk-in prices.</p>
HTML;
    }

    private function travelTipsContent2(): string
    {
        return <<<'HTML'
<h2>Peak Season: November to March</h2>
<p>This is Dubai's golden season. Temperatures hover between 20-30°C, making it perfect for outdoor activities, beach days, and desert safaris. However, this is also when hotel prices are highest and attractions are busiest. The Dubai Shopping Festival (December-January) and Dubai Food Festival (February-March) add extra excitement.</p>

<h3>November</h3>
<p>The weather turns pleasant after the scorching summer. Hotel rates start climbing but aren't at their peak yet. The Formula 1 Abu Dhabi Grand Prix takes place nearby, adding buzz to the region.</p>

<h3>December</h3>
<p>Peak tourist season begins. The Dubai Shopping Festival kicks off, and the city is decorated for the festive season. New Year's Eve at Burj Khalifa is a bucket-list experience. Book everything well in advance.</p>

<h3>January - February</h3>
<p>Perfect weather continues. January sees the end of DSF, while February brings the Dubai Food Festival. Valentine's Day packages are popular. Hotel rates remain high but start to ease toward the end of February.</p>

<h3>March</h3>
<p>The last month of comfortable outdoor weather. It's an excellent time to visit — crowds thin out, prices drop slightly, and the weather is still lovely. Ramadan may fall during this period (dates vary by year).</p>

<h2>Shoulder Season: April and October</h2>
<p>These transitional months offer a great balance of reasonable prices and acceptable weather. April starts getting warm (30-35°C), while October sees the heat begin to break. You'll find better hotel deals and fewer crowds at major attractions.</p>

<h2>Off Season: May to September</h2>
<p>Summer in Dubai means extreme heat (40-50°C) and high humidity. However, this is when you'll find the best hotel deals — luxury 5-star hotels at a fraction of peak-season prices. Dubai Summer Surprises brings incredible shopping deals. Almost everything is air-conditioned, so you can still enjoy indoor attractions, malls, and restaurants.</p>

<h2>Our Recommendation</h2>
<p>For the best overall experience, visit in <strong>November or February</strong>. You'll get great weather without the December-January peak crowds and prices. For budget travellers, <strong>May or September</strong> offer amazing hotel deals if you don't mind limiting outdoor activities to early morning or evening.</p>
HTML;
    }

    private function transportGuideContent(): string
    {
        return <<<'HTML'
<h2>Dubai Metro</h2>
<p>The Dubai Metro is the backbone of public transport. The Red Line runs from Rashidiya to UAE Exchange (covering the airport, Downtown, and Dubai Marina), while the Green Line connects Creek-side areas. Trains run from 5 AM to midnight (until 1 AM on weekends). A single trip costs as little as 4 AED with a Nol card.</p>

<h2>RTA Buses</h2>
<p>Dubai's bus network fills the gaps the Metro doesn't cover. Routes connect residential areas, business districts, and tourist spots. Buses are air-conditioned and comfortable. Use the RTA app to plan routes and check real-time arrival times.</p>

<h2>Taxis</h2>
<p>Dubai taxis are metered, clean, and relatively affordable. The starting fare is 12 AED (5 AED for phone bookings), with a per-kilometre rate of 1.96 AED. For airport pickups, there's a 25 AED surcharge. Taxis are available everywhere, or you can book through the RTA app.</p>

<h2>Ride-Hailing Apps</h2>
<p>Uber and Careem (a regional favourite now owned by Uber) operate throughout Dubai. They often offer competitive rates compared to taxis, especially for longer trips. Both apps are reliable and widely used by residents and tourists alike.</p>

<h2>Dubai Tram</h2>
<p>The Dubai Tram connects Dubai Marina, JBR, and the Palm Jumeirah monorail. It's perfect for getting around the Marina area and connects to the Metro at two stations. Uses the same Nol card as the Metro.</p>

<h2>Water Transport</h2>
<p>For a unique experience, use the traditional abra (water taxi) across Dubai Creek for just 1 AED — one of the best bargains in the city. The RTA also operates modern water buses and ferries connecting various waterfront areas, including a route from Marina to Old Dubai.</p>

<h2>Getting the Nol Card</h2>
<p>Purchase a Silver Nol card for 25 AED (includes 19 AED credit) at any Metro station. Tap in and out on all public transport. You can also use contactless bank cards on the Metro, though the Nol card offers better fare rates.</p>
HTML;
    }

    private function attractionsContent1(): string
    {
        return <<<'HTML'
<h2>1. Burj Khalifa</h2>
<p>Standing at 828 metres, the Burj Khalifa is the world's tallest building and Dubai's most iconic landmark. Visit the observation decks on floors 124-125 (At the Top) or splurge on the premium experience at floor 148 (At the Top SKY). The sunset slot is the most coveted — book weeks in advance.</p>

<h2>2. Museum of the Future</h2>
<p>This architectural marvel on Sheikh Zayed Road is one of the most beautiful buildings ever constructed. Inside, immersive exhibitions explore how technology will shape our world. Allow at least 2-3 hours for the full experience.</p>

<h2>3. Dubai Mall & Dubai Fountain</h2>
<p>More than just a shopping mall, Dubai Mall is an entertainment city. Visit the Dubai Aquarium, ice rink, VR park, and over 1,200 shops. Outside, the Dubai Fountain performs choreographed water shows every 30 minutes from 6 PM — completely free to watch.</p>

<h2>4. Palm Jumeirah</h2>
<p>The world's largest artificial island, shaped like a palm tree. Take the monorail to Atlantis The Royal for jaw-dropping architecture, or visit the Palm West Beach for stunning views of the Dubai Marina skyline.</p>

<h2>5. Dubai Frame</h2>
<p>This 150-metre-tall golden frame offers views of both old and new Dubai. The glass-floor sky bridge connecting the two towers is not for the faint-hearted. It's a fantastic way to understand Dubai's transformation.</p>

<h2>6. Old Dubai & The Souks</h2>
<p>Cross Dubai Creek on a traditional abra to explore the Gold Souk, Spice Souk, and Textile Souk. The Al Fahidi Historical Neighbourhood showcases Dubai's heritage with traditional wind-tower architecture, galleries, and cafes.</p>

<h2>7. Dubai Marina</h2>
<p>A stunning waterfront development with a 7-kilometre walkway. Perfect for evening strolls, dining, and dhow cruises. The Marina Mall and The Walk at JBR are must-visit spots.</p>

<h2>8. Ain Dubai</h2>
<p>The world's largest observation wheel on Bluewaters Island offers breathtaking 360-degree views of the Dubai coastline and city skyline. Choose between observation cabins, dining experiences, or private lounge cabins.</p>

<h2>9. Global Village</h2>
<p>Open from October to April, this multicultural festival park features pavilions from over 90 countries with shopping, food, rides, and live entertainment. It's a full evening experience and incredible value for money.</p>

<h2>10. IMG Worlds of Adventure</h2>
<p>The world's largest indoor theme park features zones themed around Marvel, Cartoon Network, and Lost Valley (dinosaurs). Perfect for families and anyone wanting to escape the heat.</p>

<h2>11. Al Marmoom Desert Conservation Reserve</h2>
<p>Just outside the city, this protected desert area offers cycling tracks, camel riding, wildlife spotting, and the iconic Al Qudra Lakes. Visit at sunrise or sunset for spectacular photo opportunities.</p>

<h2>12. La Mer</h2>
<p>A beachfront destination in Jumeirah with a laid-back vibe. Features a waterpark (Laguna), trendy restaurants, street art, and a cinema on the beach. It's where Dubai's creative community hangs out.</p>

<h2>13. Miracle Garden</h2>
<p>Open from November to May, this garden features over 150 million flowers arranged in stunning displays, including a full-size Emirates A380 covered in flowers. A truly Instagram-worthy experience.</p>

<h2>14. Jumeirah Mosque</h2>
<p>One of the few mosques in Dubai open to non-Muslims, offering guided cultural tours. Learn about Islamic architecture, traditions, and Emirati culture in this beautiful white-and-gold mosque.</p>

<h2>15. Dubai Opera</h2>
<p>A world-class performing arts venue in Downtown Dubai shaped like a dhow (traditional boat). Hosts opera, ballet, concerts, comedy shows, and more throughout the year. Check the schedule and book for a memorable evening.</p>
HTML;
    }

    private function desertSafariContent(): string
    {
        return <<<'HTML'
<h2>Types of Desert Safari</h2>

<h3>Evening Safari (Most Popular)</h3>
<p>Pickup around 3-4 PM, includes dune bashing in a 4x4, sandboarding, camel riding, henna painting, a BBQ dinner at a Bedouin-style camp with belly dancing and tanoura shows. Return by 9 PM. Prices range from 150-350 AED per person.</p>

<h3>Morning Safari</h3>
<p>For early risers, a morning safari starts around 8 AM and focuses on dune bashing, quad biking, and sandboarding. No dinner or entertainment, but you get the desert in beautiful morning light. Great for photographers. Around 200-300 AED.</p>

<h3>Overnight Safari</h3>
<p>The ultimate desert experience. Includes everything in the evening safari plus sleeping under the stars in a traditional camp. Wake up to a sunrise over the dunes and enjoy a light breakfast. From 500 AED per person.</p>

<h3>Premium/VIP Safari</h3>
<p>Smaller groups, luxury vehicles (often vintage Land Rovers), premium dining with white-linen service, and exclusive camp areas. Includes falconry displays and wildlife spotting. From 600-1200 AED per person.</p>

<h2>What to Expect During Dune Bashing</h2>
<p>A professional driver takes you on a thrilling 30-45 minute ride over sand dunes in a 4x4 vehicle. Tyres are partially deflated for better grip. It's an adrenaline-pumping experience — not recommended for pregnant women, those with back problems, or very young children.</p>

<h2>What to Wear</h2>
<ul>
<li>Comfortable, loose clothing</li>
<li>Closed-toe shoes or sandals (not flip-flops for dune bashing)</li>
<li>Sunglasses and sunscreen</li>
<li>Light jacket for cooler evenings (November-February)</li>
</ul>

<h2>Tips for the Best Experience</h2>
<ul>
<li>Book through reputable operators — check recent reviews</li>
<li>Avoid eating a heavy meal before dune bashing</li>
<li>Bring a scarf to protect from sand during windy conditions</li>
<li>Charge your phone — you'll want photos of the sunset over the dunes</li>
<li>If prone to motion sickness, sit in the front seat and look ahead</li>
</ul>
HTML;
    }

    private function dubaiCreekContent(): string
    {
        return <<<'HTML'
<h2>A Journey Through Time</h2>
<p>Before the skyscrapers and mega-malls, Dubai was a small trading port on the banks of a natural saltwater creek. Dubai Creek divided the city into two halves — Deira to the north and Bur Dubai to the south. Today, this historic waterway remains the cultural and historical heart of the city.</p>

<h2>The Abra Experience</h2>
<p>For just 1 AED, you can ride a traditional wooden abra (water taxi) across the Creek. Boats depart every few minutes from both sides, and the crossing takes about 5 minutes. It's one of Dubai's most authentic experiences and the cheapest form of transport in the city.</p>

<h2>The Souks</h2>
<h3>Gold Souk</h3>
<p>Deira's Gold Souk is one of the world's largest gold markets, with over 300 shops displaying an dazzling array of gold, diamonds, and precious stones. Prices are competitive, and haggling is expected. The gold is regulated and certified, so quality is guaranteed.</p>

<h3>Spice Souk</h3>
<p>Just steps from the Gold Souk, the narrow lanes of the Spice Souk overflow with saffron, frankincense, dried fruits, and exotic spices. The aromatic experience is unforgettable, and vendors are happy to explain the uses of each spice.</p>

<h3>Textile Souk</h3>
<p>On the Bur Dubai side, the Textile Souk (also called the Old Souk) offers fabrics from around the world — silk, cotton, pashmina, and more. It's also a great place to find affordable souvenirs and traditional clothing.</p>

<h2>Al Fahidi Historical Neighbourhood</h2>
<p>This beautifully restored neighbourhood features traditional wind-tower architecture from the late 19th century. Wander through narrow lanes to discover art galleries, the Coffee Museum, the XVA Art Hotel, and the Sheikh Mohammed Centre for Cultural Understanding, which offers cultural meals and mosque visits.</p>

<h2>Dubai Museum</h2>
<p>Housed in the 18th-century Al Fahidi Fort, the Dubai Museum offers a fascinating look at the city's rapid transformation. Interactive displays show life before oil was discovered — pearl diving, desert living, and traditional trades.</p>
HTML;
    }

    private function luxuryHotelsContent(): string
    {
        return <<<'HTML'
<h2>What Makes Dubai Hotels Special</h2>
<p>Dubai doesn't just do luxury — it redefines it. The city is home to the world's first and only 7-star hotel, multiple properties with private beaches, underwater restaurants, and in-room technology that would impress even the most seasoned travellers. Here's our guide to the finest hotels in the city.</p>

<h2>Iconic Properties</h2>

<h3>Burj Al Arab Jumeirah</h3>
<p>The sail-shaped icon that put Dubai on the luxury map. Every room is a duplex suite with panoramic sea views. Arrive by Rolls Royce or helicopter, dine at the underwater Al Mahara restaurant, and enjoy a level of service that sets the global standard. Suites start from approximately 7,000 AED per night.</p>

<h3>Atlantis The Royal</h3>
<p>The newest ultra-luxury resort on Palm Jumeirah features architecture that defies imagination. Highlights include sky-high infinity pools, celebrity chef restaurants by Nobu and José Andrés, and the jaw-dropping Royal Mansion suite. The rooftop skypool offers Dubai's most Instagram-worthy view.</p>

<h3>One&Only The Palm</h3>
<p>A serene escape on Palm Jumeirah's crescent, featuring Moorish-inspired architecture, a stunning private beach, and an extraordinary spa. The overwater suites offer complete privacy and direct water access.</p>

<h2>What to Look For</h2>
<ul>
<li><strong>Location:</strong> Downtown for city views and convenience, Palm Jumeirah or JBR for beach access, Dubai Creek for cultural experiences</li>
<li><strong>Private beach:</strong> Not all hotels have one — verify before booking if beach access matters to you</li>
<li><strong>Club lounge access:</strong> Many luxury hotels offer exclusive lounge access with complimentary food and drinks throughout the day</li>
<li><strong>Butler service:</strong> Available at ultra-luxury properties — your personal concierge for the duration of your stay</li>
</ul>

<h2>Best Time to Book</h2>
<p>For the best rates on luxury hotels, book during summer (June-August) when prices can drop by 50-70%. Many properties offer incredible summer packages with spa credits, dining credits, and room upgrades. Peak season (December-January) requires booking 2-3 months in advance for popular properties.</p>
HTML;
    }

    private function dubaiMarinaContent(): string
    {
        return <<<'HTML'
<h2>Why Dubai Marina?</h2>
<p>Dubai Marina is an artificial canal city carved along a 3-kilometre stretch of coastline. It's home to some of Dubai's tallest residential towers, a stunning marina promenade, and a lifestyle that revolves around waterfront living. For tourists, it offers excellent hotel options, world-class dining, and easy access to the beach.</p>

<h2>The Marina Walk</h2>
<p>The 7-kilometre promenade around the marina is perfect for morning jogs, evening strolls, or alfresco dining. Lined with cafes, restaurants, and shops, it comes alive in the evenings with street performers and a buzzing atmosphere. On weekends, you'll find pop-up markets and food trucks.</p>

<h2>Dining Highlights</h2>
<ul>
<li><strong>Pier 7:</strong> A unique building housing 7 different restaurants on 7 floors, each with marina views</li>
<li><strong>Marina Social by Jason Atherton:</strong> Modern European cuisine in an elegant setting</li>
<li><strong>Bussola:</strong> Authentic Italian with outdoor marina-view terrace</li>
<li><strong>Catch by SquarePeg:</strong> Seafood with stunning Ain Dubai views</li>
</ul>

<h2>Activities</h2>
<ul>
<li><strong>Yacht & Dhow Cruises:</strong> Evening dinner cruises from the marina are magical — especially during cooler months</li>
<li><strong>JBR Beach:</strong> Just a short walk from the Marina, The Beach at JBR offers swimming, water sports, and beachfront dining</li>
<li><strong>Skydive Dubai:</strong> The Palm Jumeirah drop zone is right here — experience an tandem skydive with views of the Palm and Marina skyline</li>
<li><strong>Flyboarding & Jet Skiing:</strong> Water sports are available right from the marina jetties</li>
</ul>

<h2>Getting Around</h2>
<p>Dubai Marina is well-connected by the Metro (DMCC and JLT stations), the Dubai Tram (runs through the Marina and connects to the Metro and Palm Jumeirah monorail), and is very walkable. Most hotels, restaurants, and beaches are within walking distance of each other.</p>
HTML;
    }

    private function restaurantsContent(): string
    {
        return <<<'HTML'
<h2>Fine Dining</h2>

<h3>STAY by Yannick Alléno — One&Only The Palm</h3>
<p>French haute cuisine with a modern twist, featuring three Michelin stars. The tasting menu is an extraordinary culinary journey. Dress code is smart elegant, and reservations are essential.</p>

<h3>Nobu Dubai — Atlantis The Royal</h3>
<p>The legendary Nobu Matsuhisa brings his Japanese-Peruvian fusion to one of Dubai's most stunning settings. The black cod miso is a must-order. Incredible views of the Palm from the terrace.</p>

<h3>Al Hadheerah — Bab Al Shams</h3>
<p>An open-air desert restaurant offering a lavish Arabian buffet under the stars. Live cooking stations, traditional entertainment, and a setting that transports you back in time. Perfect for a special evening.</p>

<h2>Mid-Range Gems</h2>

<h3>Arabian Tea House — Al Fahidi</h3>
<p>In the heart of the historic district, this charming restaurant serves authentic Emirati cuisine in a beautiful courtyard setting. Try the chicken machboos and finish with luqaimat. Affordable and unforgettable.</p>

<h3>Ravi Restaurant — Satwa</h3>
<p>A Dubai institution since 1978. This no-frills Pakistani restaurant serves some of the best curry in the city at unbelievably low prices. The butter chicken and fresh naan are legendary. Always busy, never disappointing.</p>

<h3>Bu Qtair — Jumeirah</h3>
<p>A beachside fish shack where you choose your seafood and they fry it fresh. No menu, no reservations, no pretence — just incredibly fresh fish, rice, and their famous spicy sauce. A true Dubai hidden gem.</p>

<h2>Street Food & Budget Eats</h2>

<h3>Al Mallah — Al Dhiyafah Road</h3>
<p>Famous for its shawarma and fresh juices, Al Mallah has been a Satwa staple for decades. The chicken shawarma plate with garlic sauce is the quintessential Dubai street meal.</p>

<h3>Firas Sweets — Multiple Locations</h3>
<p>For the best manakish and kunafa in Dubai. Fresh, affordable, and open late. The cheese manakish with za'atar is a perfect quick breakfast.</p>

<h2>Friday Brunch</h2>
<p>Friday brunch is a Dubai institution. Hotels and restaurants across the city offer lavish all-you-can-eat-and-drink brunches from approximately 200-600 AED per person. Popular options include Bubbalicious at The Westin, Saffron at Atlantis, and Al Dawaar (the city's only revolving restaurant).</p>
HTML;
    }

    private function streetFoodContent(): string
    {
        return <<<'HTML'
<h2>1. Shawarma</h2>
<p>The undisputed king of Dubai street food. Chicken or lamb, wrapped in Arabic bread with pickles, garlic sauce, and tahini. The best shawarma spots are small, unassuming shops in Deira, Al Karama, and Satwa. Expect to pay 5-15 AED — possibly the best meal deal in Dubai.</p>

<h2>2. Manakish</h2>
<p>A Levantine flatbread topped with za'atar (thyme and sesame blend), cheese, or minced meat. Baked fresh in a brick oven and served for breakfast or as a snack. Find the best ones at small bakeries in Bur Dubai and Deira.</p>

<h2>3. Falafel</h2>
<p>Crispy on the outside, soft and herby on the inside — Dubai's falafel comes in wraps or plates with hummus, pickles, and salad. Lebanese bakeries throughout the city serve excellent falafel for under 10 AED.</p>

<h2>4. Luqaimat</h2>
<p>Traditional Emirati sweet dumplings — crispy fried balls drizzled with date syrup and sesame seeds. Find them at local restaurants and during Ramadan food festivals. Sweet, sticky, and absolutely addictive.</p>

<h2>5. Samboosa</h2>
<p>The Gulf version of samosa — triangular pastries filled with spiced meat, cheese, or vegetables. A staple during Ramadan but available year-round at cafeterias and bakeries across the city. 1-3 AED each.</p>

<h2>6. Karak Chai</h2>
<p>Dubai's unofficial national drink. Strong black tea simmered with evaporated milk, cardamom, sugar, and sometimes saffron. Available at virtually every cafeteria for just 1-2 AED. Best enjoyed at small roadside kiosks — the smaller the shop, the better the chai.</p>

<h2>7. Chebab</h2>
<p>Emirati pancakes made with saffron and cardamom, served with date syrup or cream cheese. A traditional breakfast item found at Emirati restaurants and during cultural events.</p>

<h2>8. Kunafa</h2>
<p>A Middle Eastern dessert of shredded phyllo dough layered with cheese, soaked in sweet syrup, and topped with pistachios. Served warm, it's stretchy, crunchy, and sweet all at once. Firas Sweets is famous for it.</p>

<h2>9. Regag Bread</h2>
<p>Paper-thin Emirati bread cooked on a large hot plate, often topped with egg, cheese, or honey. Watch it being made at cultural events or traditional restaurants — the skill involved is mesmerizing.</p>

<h2>10. Tabbouleh & Fattoush</h2>
<p>Fresh, zesty Levantine salads available at nearly every restaurant. Tabbouleh with its parsley-heavy mix and fattoush with crispy pita chips are perfect healthy options in the Dubai heat.</p>

<h2>11. Chicken Tikka</h2>
<p>Marinated and grilled to perfection, available in plates or wraps from the numerous Pakistani and Indian restaurants in Al Karama and Bur Dubai. Served with mint chutney and fresh naan. A filling meal for under 25 AED.</p>

<h2>12. Fresh Juices</h2>
<p>Dubai's juice shops are everywhere, offering freshly squeezed combinations from mango and avocado to more exotic mixes. Try the avocado with honey and nuts, or a simple fresh orange juice — refreshing and healthy. From just 5 AED.</p>
HTML;
    }

    private function shoppingFestivalContent(): string
    {
        return <<<'HTML'
<h2>What is the Dubai Shopping Festival?</h2>
<p>The Dubai Shopping Festival (DSF) is the Middle East's largest shopping and entertainment event, held annually since 1996. Running for approximately one month from late December to late January, DSF transforms the entire city into a shopper's paradise with mega sales, daily raffle draws, fireworks, concerts, and entertainment for all ages.</p>

<h2>Key Highlights for 2026</h2>
<ul>
<li><strong>Mega Sales:</strong> Discounts of 25-75% across malls, boutiques, and the Gold Souk</li>
<li><strong>Daily Raffle Draws:</strong> Win luxury cars, gold, and cash prizes simply by shopping</li>
<li><strong>Fireworks:</strong> Spectacular shows at multiple locations across the city</li>
<li><strong>Concerts:</strong> International and regional artists perform throughout the festival</li>
<li><strong>Market Outside The Box:</strong> A popular outdoor market featuring independent designers and artisans</li>
</ul>

<h2>Best Shopping Destinations During DSF</h2>
<h3>Dubai Mall</h3>
<p>The epicentre of DSF, with over 1,200 stores offering their biggest discounts of the year. Fashion, electronics, jewellery, and more. The 12-Hour Sale events offer up to 90% off at select stores.</p>

<h3>Mall of the Emirates</h3>
<p>Home to over 630 stores plus Ski Dubai. DSF brings exclusive promotions and spend-and-win campaigns throughout the mall.</p>

<h3>Gold Souk</h3>
<p>Gold prices drop during DSF, and the Souk's 300+ shops compete for customers with additional discounts and the famous Gold Raffle — shop for gold and enter draws for even more gold.</p>

<h3>Global Village</h3>
<p>Running alongside DSF, Global Village offers shopping from 90+ countries, street food from around the world, and entertainment that ranges from stunt shows to concerts.</p>

<h2>Tips for Getting the Best Deals</h2>
<ul>
<li>Follow @DSFsocial on Instagram for flash sale announcements</li>
<li>Download the DSF app for deal alerts and raffle entry tracking</li>
<li>Visit malls during weekdays for less crowded shopping</li>
<li>Keep all receipts — many spend-and-win promotions require cumulative spending</li>
<li>Compare prices online before buying electronics — some deals are genuine bargains, others less so</li>
</ul>
HTML;
    }

    private function ramadanGuideContent(): string
    {
        return <<<'HTML'
<h2>Understanding Ramadan</h2>
<p>Ramadan is the holiest month in Islam, during which Muslims fast from dawn to sunset. In Dubai, this means some adjustments to daily life, but the city remains very welcoming to tourists. In fact, many visitors find Ramadan adds a unique cultural dimension to their trip.</p>

<h2>Rules for Visitors</h2>
<ul>
<li><strong>Eating in public:</strong> Out of respect, avoid eating, drinking, or smoking in public areas during daylight hours. Hotels provide screened-off areas for non-fasting guests, and room service operates normally.</li>
<li><strong>Dress code:</strong> Dress a bit more conservatively than usual — shoulders and knees covered in public areas.</li>
<li><strong>Music & Entertainment:</strong> Live music is generally subdued during Ramadan, and nightclubs are closed. Many restaurants and bars still operate but may have adjusted hours.</li>
<li><strong>Working hours:</strong> Government offices and many businesses have reduced hours. Malls typically open later (around 10-11 AM) and close later (around midnight or 1 AM).</li>
</ul>

<h2>The Iftar Experience</h2>
<p>Iftar (the meal at sunset that breaks the fast) is the highlight of Ramadan in Dubai. Hotels and restaurants across the city offer lavish iftar buffets featuring traditional Arabic cuisine, dates, juices, and specialities you won't find the rest of the year. It's a communal, joyful experience — many iftars welcome non-Muslim guests.</p>

<h3>Top Iftar Spots</h3>
<ul>
<li><strong>Asateer Tent, Atlantis:</strong> One of the most popular iftars with a massive spread</li>
<li><strong>Ewaan, Palace Downtown:</strong> Elegant iftar with Burj Khalifa views</li>
<li><strong>Jumeirah Mosque:</strong> Cultural iftar experience with a guided mosque tour</li>
<li><strong>Sheikh Mohammed Centre for Cultural Understanding:</strong> Intimate iftar with Q&A about Islamic culture</li>
</ul>

<h2>Suhoor — The Pre-Dawn Meal</h2>
<p>Suhoor (the meal before dawn) has become a social event in Dubai. Many restaurants and shisha lounges offer special suhoor menus from 9 PM until the early hours. The atmosphere is festive and relaxed — a unique late-night cultural experience.</p>

<h2>Why Visit During Ramadan?</h2>
<ul>
<li>Hotel rates are significantly lower than peak season</li>
<li>Experience authentic Arabian culture and hospitality</li>
<li>Incredible iftar and suhoor dining experiences</li>
<li>Beautifully decorated malls and public spaces</li>
<li>Ramadan Night Markets with special shopping deals</li>
<li>A genuine sense of community and generosity throughout the city</li>
</ul>
HTML;
    }

    private function downtownGuideContent(): string
    {
        return <<<'HTML'
<h2>The Heart of Modern Dubai</h2>
<p>Downtown Dubai is the city's centrepiece — a meticulously planned district that's home to record-breaking landmarks, world-class shopping, and some of the best hotels in the UAE. If this is your first visit to Dubai, staying in Downtown puts you at the centre of everything.</p>

<h2>Key Landmarks</h2>

<h3>Burj Khalifa</h3>
<p>The world's tallest building dominates the Downtown skyline at 828 metres. Visit the observation decks, dine at At.mosphere (the world's highest restaurant), or simply admire it from the ground — it's spectacular from every angle, especially when lit up at night.</p>

<h3>Dubai Mall</h3>
<p>Connected to the Burj Khalifa, Dubai Mall is the world's largest mall by total area. Beyond shopping, it houses the Dubai Aquarium & Underwater Zoo, an Olympic-sized ice rink, a SEGA gaming zone, KidZania, and over 200 food outlets.</p>

<h3>Dubai Fountain</h3>
<p>Set on the 30-acre Burj Khalifa Lake, the Dubai Fountain is the world's largest choreographed fountain system. Free shows run every 30 minutes from 6 PM, with water jets reaching up to 150 metres. For the best view, take an abra ride on the lake (65 AED) or watch from one of the waterfront restaurants.</p>

<h3>Dubai Opera</h3>
<p>A 2,000-seat performing arts centre shaped like a traditional dhow. Hosts world-class opera, ballet, theatre, comedy, and concerts year-round. The building itself is an architectural gem — take a guided tour even if you don't catch a show.</p>

<h2>Where to Eat</h2>
<ul>
<li><strong>At.mosphere:</strong> Fine dining on the 122nd floor of Burj Khalifa</li>
<li><strong>Zuma:</strong> Award-winning contemporary Japanese cuisine</li>
<li><strong>The Cheesecake Factory:</strong> Casual dining with massive portions</li>
<li><strong>Time Out Market:</strong> A curated food hall with Dubai's best independent eateries</li>
<li><strong>Souk Al Bahar:</strong> Waterfront dining with fountain views in a traditional Arabian market setting</li>
</ul>

<h2>Getting There & Around</h2>
<p>Take the Metro to Burj Khalifa/Dubai Mall station (Red Line). The area is very walkable, with air-conditioned skywalks connecting the Metro station to the mall. Taxis and ride-hailing apps are readily available. Parking is plentiful but can be busy on weekends.</p>
HTML;
    }

    private function jbrGuideContent(): string
    {
        return <<<'HTML'
<h2>Jumeirah Beach Residence (JBR)</h2>
<p>JBR is a 1.7-kilometre beachfront community that has become one of Dubai's most popular tourist destinations. The combination of a gorgeous public beach, outdoor shopping at The Walk, beachfront dining, and family entertainment makes it the perfect spot for visitors who want a beach-centric Dubai experience.</p>

<h2>The Beach at JBR</h2>
<p>A beautifully maintained public beach with soft white sand, clear blue water, and all the amenities you need — sun loungers, changing rooms, and lifeguard stations. Behind the beach, The Beach mall offers a mix of retail, dining, and an outdoor cinema. It's free to visit, with lounger rental available.</p>

<h2>The Walk at JBR</h2>
<p>A 1.7-kilometre outdoor promenade running parallel to the beach, lined with shops, restaurants, and cafes. It's the place for people-watching, especially in the evenings when street performers and live musicians add to the atmosphere. On weekends, you'll find pop-up markets and food festivals.</p>

<h2>Bluewaters Island</h2>
<p>Connected to JBR by a pedestrian bridge, Bluewaters Island is a purpose-built entertainment destination. The centrepiece is Ain Dubai — the world's largest and tallest observation wheel at 250 metres. The island also features:</p>
<ul>
<li><strong>Ain Dubai:</strong> 38-minute rotation with observation, dining, and lounge cabins</li>
<li><strong>Ceasars Palace:</strong> A luxury resort with multiple celebrity-chef restaurants</li>
<li><strong>The Wharf:</strong> Waterfront dining with Ain Dubai as the backdrop</li>
<li><strong>Madame Tussauds:</strong> Dubai's first wax museum with regional and international figures</li>
</ul>

<h2>Water Sports & Activities</h2>
<ul>
<li>Jet skiing from JBR beach (from 250 AED for 30 minutes)</li>
<li>Parasailing with views of the Palm and Marina (from 350 AED)</li>
<li>Flyboarding — hover above the water on a jet-powered board</li>
<li>Banana boat rides — fun for families and groups</li>
<li>Paddleboarding — calm waters in the morning are perfect for beginners</li>
</ul>

<h2>Where to Eat</h2>
<ul>
<li><strong>Paul:</strong> French bakery and restaurant with outdoor beach seating</li>
<li><strong>Leen's:</strong> Healthy Lebanese cuisine with a sea-view terrace</li>
<li><strong>Black Tap:</strong> Famous for insane milkshakes and gourmet burgers</li>
<li><strong>Shimmers:</strong> Beach club dining at Jumeirah Mina A'Salam (worth the short drive)</li>
</ul>

<h2>Getting There</h2>
<p>JBR is served by the Dubai Tram (JBR stations 1 and 2), which connects to the Metro at DMCC station. The area is very walkable, and the pedestrian bridge to Bluewaters Island is a pleasant 10-minute stroll. Parking is available but fills up quickly on weekends.</p>
HTML;
    }

    // ── Image Helpers ──

    private function downloadImage(string $storagePath, int $width = 1200, int $height = 630): string
    {
        $url = "https://picsum.photos/{$width}/{$height}?random=".$this->imageCounter++;

        $fullPath = Storage::disk('public')->path($storagePath);
        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
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

                return $storagePath;
            }
        } catch (\Exception $e) {
            // Fall through to GD placeholder
        }

        $label = basename($storagePath, '.jpg');
        $this->generatePlaceholderImage($fullPath, $width, $height, $label);

        return $storagePath;
    }

    private function generatePlaceholderImage(string $path, int $w, int $h, string $label): void
    {
        if (! extension_loaded('gd')) {
            file_put_contents($path, base64_decode(
                '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP'.
                str_repeat('A', 50).'//Z'
            ));

            return;
        }

        $img = imagecreatetruecolor($w, $h);

        $hash = crc32($label);
        $r = abs($hash) % 80 + 100;
        $g = abs($hash >> 8) % 80 + 100;
        $b = abs($hash >> 16) % 80 + 100;

        $bg = imagecolorallocate($img, $r, $g, $b);
        imagefill($img, 0, 0, $bg);

        $textColor = imagecolorallocate($img, 255, 255, 255);
        $text = strtoupper(str_replace('-', ' ', $label));
        if (strlen($text) > 30) {
            $text = substr($text, 0, 30).'...';
        }
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        imagestring($img, $fontSize, ($w - $textWidth) / 2, ($h - $textHeight) / 2, $text, $textColor);

        imagejpeg($img, $path, 85);
        imagedestroy($img);
    }
}
