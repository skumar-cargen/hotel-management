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

class JumeiraBeachBlogSeeder extends Seeder
{
    public function run(): void
    {
        $domain = Domain::where('slug', 'jumeira-beach-hotels')->first();
        if (! $domain) {
            $this->command->warn('Domain jumeira-beach-hotels not found. Skipping blog seeder.');
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
            ['name' => 'Travel Tips', 'slug' => 'travel-tips', 'description' => 'Expert advice for making the most of your Dubai travels', 'sort_order' => 1],
            ['name' => 'Beach Life', 'slug' => 'beach-life', 'description' => 'Everything about Dubai\'s stunning beachfront lifestyle', 'sort_order' => 2],
            ['name' => 'Luxury Stays', 'slug' => 'luxury-stays', 'description' => 'Inside look at Dubai\'s finest hotels and resorts', 'sort_order' => 3],
            ['name' => 'Food & Dining', 'slug' => 'food-dining', 'description' => 'Culinary experiences from world-class restaurants', 'sort_order' => 4],
            ['name' => 'Things To Do', 'slug' => 'things-to-do', 'description' => 'Activities, attractions, and hidden gems in Dubai', 'sort_order' => 5],
            ['name' => 'Travel Guides', 'slug' => 'travel-guides', 'description' => 'Comprehensive guides for exploring Dubai and beyond', 'sort_order' => 6],
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
                'title' => '10 Hidden Gems Along Jumeira Beach You Need to Visit',
                'slug' => '10-hidden-gems-jumeira-beach',
                'category' => 'beach-life',
                'featured_image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80',
                'excerpt' => 'Beyond the famous landmarks lies a coastline full of surprises. From secret coves to tucked-away cafes, discover the hidden treasures of Jumeira Beach that most tourists never find.',
                'tags' => ['jumeira', 'beach', 'hidden gems', 'dubai'],
                'is_featured' => true,
                'days_ago' => 2,
                'view_count' => 4820,
                'content' => '<h2>Beyond the Tourist Trail</h2>
<p>Jumeira Beach stretches for kilometres along Dubai\'s coastline, and while most visitors flock to the well-known spots, the real magic lies in the lesser-known corners of this stunning shoreline.</p>

<h3>1. The Quiet Cove Near Kite Beach</h3>
<p>Just south of the bustling Kite Beach area, there\'s a peaceful stretch of sand that locals have kept to themselves for years. The water here is crystal clear, and the lack of crowds makes it perfect for a morning swim or sunset meditation.</p>

<h3>2. The Fisherman\'s Wharf</h3>
<p>Early risers can catch traditional fishermen bringing in their daily haul at this small dock near Jumeirah 1. It\'s a fascinating glimpse into Dubai\'s maritime heritage, and you can often buy the freshest seafood directly from the boats.</p>

<h3>3. The Hidden Art Walk</h3>
<p>Along the promenade between La Mer and Jumeira Open Beach, street artists have created an ever-changing gallery of murals and installations. Each visit reveals something new.</p>

<h3>4. Sunset Point Rock Formation</h3>
<p>A natural rock formation that creates the perfect frame for sunset photography. Located between the public beaches, it\'s a spot that even many residents don\'t know about.</p>

<h3>5. The Beachside Book Exchange</h3>
<p>A charming little free library housed in a weatherproof box near the jogging track. Take a book, leave a book — it\'s become a beloved community tradition.</p>

<h2>Making the Most of Your Visit</h2>
<p>The best time to explore these hidden gems is early morning or late afternoon. The light is magical, the temperatures are pleasant, and you\'ll have many of these spots almost entirely to yourself.</p>

<p>Pack light, bring plenty of water, and don\'t forget your camera — you\'ll want to capture every moment of these special places along one of the world\'s most beautiful coastlines.</p>',
            ],
            [
                'title' => 'The Ultimate Guide to Dubai\'s Best Rooftop Restaurants',
                'slug' => 'dubai-best-rooftop-restaurants-guide',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80',
                'excerpt' => 'Dine among the stars at Dubai\'s most spectacular rooftop restaurants. From Michelin-starred cuisine to casual sunset cocktails, here are the elevated dining experiences you can\'t miss.',
                'tags' => ['restaurants', 'rooftop', 'dining', 'dubai', 'food'],
                'is_featured' => true,
                'days_ago' => 5,
                'view_count' => 3650,
                'content' => '<h2>Elevate Your Dining Experience</h2>
<p>Dubai\'s skyline is legendary, and what better way to enjoy it than from a rooftop restaurant? The city offers an incredible variety of elevated dining experiences, from intimate fine-dining venues to lively rooftop bars with panoramic views.</p>

<h3>At.mosphere — Burj Khalifa</h3>
<p>Perched on the 122nd floor of the world\'s tallest building, At.mosphere offers an unparalleled dining experience. The modern European menu is exquisite, but it\'s the views that truly take your breath away. Book a window table for sunset — it\'s an experience you\'ll never forget.</p>

<h3>Tresind Studio — DIFC</h3>
<p>This Michelin-starred restaurant brings innovative Indian cuisine to new heights, both literally and figuratively. Chef Himanshu Saini\'s tasting menu is a journey through India\'s diverse culinary landscape, reimagined with modern techniques.</p>

<h3>CE LA VI — Address Sky View</h3>
<p>With 360-degree views of Downtown Dubai, CE LA VI combines Southeast Asian cuisine with an electric atmosphere. The infinity pool adds to the vibe, and the weekend brunches are legendary.</p>

<h3>Nobu Dubai — Atlantis The Palm</h3>
<p>Celebrity chef Nobu Matsuhisa\'s Dubai outpost offers his signature Japanese-Peruvian fusion with stunning views of the Arabian Gulf. The Black Cod Miso is a must-order.</p>

<h2>Pro Tips for Rooftop Dining</h2>
<ul>
<li>Always make reservations, especially for sunset slots</li>
<li>Dress code is typically smart casual to formal</li>
<li>Visit during winter months (November–March) for the best outdoor weather</li>
<li>Ask for a terrace table when booking</li>
</ul>',
            ],
            [
                'title' => 'Why Jumeira Beach Hotels Offer the Best Value in Dubai',
                'slug' => 'jumeira-beach-hotels-best-value-dubai',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1200&q=80',
                'excerpt' => 'Think luxury has to break the bank? Think again. Discover why Jumeira Beach hotels deliver world-class experiences at prices that might surprise you.',
                'tags' => ['hotels', 'value', 'jumeira', 'luxury', 'budget'],
                'is_featured' => true,
                'days_ago' => 8,
                'view_count' => 5210,
                'content' => '<h2>Luxury Without the Premium Price Tag</h2>
<p>When most people think of Dubai hotels, they imagine astronomical price tags. But the Jumeira Beach area has quietly become one of the best-value luxury destinations in the city, offering five-star experiences at surprisingly accessible rates.</p>

<h3>Location, Location, Location</h3>
<p>Hotels along Jumeira Beach sit in a sweet spot — close enough to the city\'s major attractions for easy access, yet far enough from the Downtown premium to offer better rates. You get the beach, the views, and the luxury without the inflated prices.</p>

<h3>All-Inclusive Packages</h3>
<p>Many Jumeira Beach properties offer all-inclusive packages that include meals, beach access, pool facilities, and even spa credits. When you factor in everything that\'s included, the value becomes extraordinary.</p>

<h3>Seasonal Deals You Won\'t Believe</h3>
<p>During summer months and shoulder seasons, some of the finest properties along the beach drop their rates by 40-60%. Smart travellers who can handle a bit of heat are rewarded with incredible deals on properties that charge premium rates in winter.</p>

<h3>What Makes These Hotels Special</h3>
<ul>
<li>Direct beach access — no need for expensive beach club fees</li>
<li>Multiple dining options within walking distance</li>
<li>World-class spa facilities</li>
<li>Stunning sunset views included with every room</li>
<li>Proximity to La Mer, Kite Beach, and JBR</li>
</ul>

<p>The next time you\'re planning a Dubai trip, look beyond the headline-grabbing mega-resorts. The real value — and often the better experience — can be found right here along Jumeira Beach.</p>',
            ],
            [
                'title' => 'A First-Timer\'s Complete Guide to Dubai: 2026 Edition',
                'slug' => 'first-timers-guide-dubai-2026',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1200&q=80',
                'excerpt' => 'Everything you need to know before your first trip to Dubai — from visa requirements and cultural tips to the best neighbourhoods for every type of traveller.',
                'tags' => ['dubai', 'guide', 'first time', 'travel', 'tips'],
                'is_featured' => true,
                'days_ago' => 12,
                'view_count' => 8930,
                'content' => '<h2>Welcome to the City of Gold</h2>
<p>Dubai is a city that defies expectations at every turn. From record-breaking architecture to ancient souks, from desert adventures to pristine beaches — it\'s a destination that truly has something for everyone.</p>

<h3>When to Visit</h3>
<p>The best time to visit Dubai is between November and March when temperatures are pleasant (20-30°C). Summer months (June-August) are extremely hot but offer incredible hotel deals and fewer crowds at attractions.</p>

<h3>Visa & Entry</h3>
<p>Citizens of over 80 countries receive visa-on-arrival for 30-90 days. Check with your local UAE embassy for the latest requirements. All visitors need a passport valid for at least 6 months.</p>

<h3>Getting Around</h3>
<p>Dubai\'s metro is clean, efficient, and covers major tourist areas. Taxis and ride-hailing apps (Careem, Uber) are affordable. For the full experience, try the water taxis (abras) across Dubai Creek — just 1 AED per trip!</p>

<h3>Must-Do Experiences</h3>
<ol>
<li><strong>Burj Khalifa</strong> — Book the sunset time slot for the best experience</li>
<li><strong>Desert Safari</strong> — An unforgettable adventure with dune bashing and BBQ dinner</li>
<li><strong>Dubai Mall & Aquarium</strong> — World\'s largest mall with an indoor aquarium</li>
<li><strong>Gold & Spice Souks</strong> — Traditional markets in historic Deira</li>
<li><strong>Jumeira Beach</strong> — Relax on pristine white sand with skyline views</li>
<li><strong>Dubai Marina Walk</strong> — Evening stroll with restaurants and yacht views</li>
</ol>

<h3>Cultural Tips</h3>
<ul>
<li>Dress modestly when visiting malls, souks, and religious sites</li>
<li>Friday is the holy day — many places have different hours</li>
<li>Alcohol is only served in licensed venues (hotels, restaurants)</li>
<li>Photography — always ask before photographing local people</li>
<li>Tipping is appreciated but not mandatory (10-15%)</li>
</ul>

<h3>Budget Tips</h3>
<p>Dubai can be experienced on any budget. Street food in Deira and Karama is delicious and affordable. Free beaches, the Dubai Fountain show, and walking through the souks cost nothing. Many museums offer free or low-cost entry.</p>',
            ],
            [
                'title' => 'Sunrise Yoga to Sunset Surfing: 24 Hours of Beach Activities',
                'slug' => 'sunrise-yoga-sunset-surfing-beach-activities',
                'category' => 'things-to-do',
                'featured_image_url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&q=80',
                'excerpt' => 'From dawn to dusk, Jumeira Beach is alive with activity. Here\'s how to fill an unforgettable 24 hours with the best water sports, wellness, and beach experiences.',
                'tags' => ['activities', 'beach', 'yoga', 'surfing', 'water sports'],
                'is_featured' => false,
                'days_ago' => 15,
                'view_count' => 2340,
                'content' => '<h2>Your Perfect Beach Day, Planned</h2>
<p>The beaches along Jumeira aren\'t just for sunbathing. From the first light of dawn to the final glow of sunset, there\'s an incredible range of activities waiting for you.</p>

<h3>6:00 AM — Sunrise Yoga</h3>
<p>Start your day with a yoga session on the sand as the sun rises over the Arabian Gulf. Several studios offer free beach yoga classes, and there\'s something magical about saluting the sun as it actually appears over the water.</p>

<h3>8:00 AM — Morning Swim</h3>
<p>The water is at its calmest in the early morning — perfect for a refreshing swim. The gentle waves and warm water make it ideal for all swimming levels.</p>

<h3>10:00 AM — Paddleboarding</h3>
<p>Stand-up paddleboarding is huge in Dubai, and the calm morning waters along Jumeira are perfect for beginners. Several rental shops line the beach, with boards available from just 50 AED per hour.</p>

<h3>12:00 PM — Beach Club Brunch</h3>
<p>Recharge at one of the beachside restaurants. Many offer incredible lunch deals with pool access included. It\'s the perfect midday break from the sun.</p>

<h3>3:00 PM — Kite Surfing</h3>
<p>As the afternoon breeze picks up, Kite Beach comes alive with colourful kites dotting the sky. Whether you\'re learning or experienced, the conditions here are world-class.</p>

<h3>5:30 PM — Sunset Beach Walk</h3>
<p>The golden hour along Jumeira Beach is absolutely stunning. Walk the promenade, watch the sky turn orange and pink behind the Burj Al Arab, and soak in the magic of a Dubai sunset.</p>

<h3>7:00 PM — Beachside Dinner</h3>
<p>End your perfect day with dinner at one of the many beachfront restaurants. The sound of waves, the gentle breeze, and fresh seafood — it doesn\'t get better than this.</p>',
            ],
            [
                'title' => 'The Best Spa Experiences at Dubai Beach Hotels',
                'slug' => 'best-spa-experiences-dubai-beach-hotels',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbec6d?w=1200&q=80',
                'excerpt' => 'Indulge in world-class spa treatments with ocean views. We review the top spa experiences at Dubai\'s premier beachfront hotels.',
                'tags' => ['spa', 'wellness', 'luxury', 'hotels', 'relaxation'],
                'is_featured' => false,
                'days_ago' => 18,
                'view_count' => 1890,
                'content' => '<h2>Relax, Rejuvenate, Repeat</h2>
<p>Dubai\'s beachfront hotels are home to some of the world\'s most luxurious spas. From traditional hammam rituals to cutting-edge wellness treatments, here are the experiences that will leave you feeling completely renewed.</p>

<h3>Traditional Arabian Hammam</h3>
<p>Experience centuries-old bathing rituals in stunning surroundings. The traditional hammam involves steam, scrubbing with black soap, and a relaxing massage. Several beach hotels offer authentic hammam experiences with modern luxury touches.</p>

<h3>Ocean-View Treatment Rooms</h3>
<p>Imagine a deep tissue massage while watching the waves through floor-to-ceiling windows. Many premium beach hotels now offer treatment rooms with direct ocean views, adding a natural element of serenity to your spa experience.</p>

<h3>Couples\' Retreat Packages</h3>
<p>For a romantic experience, several hotels offer couples\' spa packages that include side-by-side treatments, private jacuzzi time, and champagne. Perfect for honeymoons or anniversaries.</p>

<h3>Wellness Programs</h3>
<p>Beyond single treatments, many hotels now offer multi-day wellness programs that combine spa treatments with yoga, meditation, nutrition counselling, and fitness sessions. It\'s a complete reset for body and mind.</p>

<h2>What to Expect</h2>
<ul>
<li>Prices typically range from 300-1500 AED per treatment</li>
<li>Book in advance, especially for weekend appointments</li>
<li>Most spas offer day passes that include pool and beach access</li>
<li>Look for weekday specials and package deals for the best value</li>
</ul>',
            ],
            [
                'title' => 'Street Food Trail: Where Locals Eat Near Jumeira Beach',
                'slug' => 'street-food-trail-locals-eat-jumeira',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80',
                'excerpt' => 'Skip the hotel restaurants and eat where the locals do. Our street food trail reveals the most delicious and affordable eats within minutes of Jumeira Beach.',
                'tags' => ['street food', 'local food', 'cheap eats', 'jumeira', 'dubai'],
                'is_featured' => false,
                'days_ago' => 22,
                'view_count' => 3120,
                'content' => '<h2>Taste the Real Dubai</h2>
<p>While Dubai\'s hotel restaurants are world-class, some of the best food in the city is found at humble street-side eateries and small cafes. Here\'s your guide to the tastiest and most affordable spots near Jumeira Beach.</p>

<h3>Ravi Restaurant — Pakistani Perfection</h3>
<p>A Dubai institution since 1978. The butter chicken, dal, and fresh naan are legendary. Expect to spend under 30 AED for a filling meal. It\'s always packed — that\'s how you know it\'s good.</p>

<h3>Al Mallah — Shawarma Heaven</h3>
<p>Located on Al Dhiyafa Road, Al Mallah serves what many consider the best shawarma in Dubai. The mixed shawarma plate with fresh juices is the perfect post-beach meal. Budget: 25-40 AED.</p>

<h3>Bu Qtair — The Legendary Fish Shack</h3>
<p>This unassuming roadside fish restaurant near Jumeirah has been serving the freshest fried fish and prawns for decades. No menu — just point at what you want. The masala prawns are unforgettable.</p>

<h3>Salt — Gourmet Burgers from a Truck</h3>
<p>This iconic food truck on Kite Beach serves the most Instagram-worthy sliders in Dubai. The wagyu sliders and Lotus Biscoff shake are cult favourites. Open from sunset — the queue is worth it.</p>

<h3>Comptoir 102 — Healthy Beach Cafe</h3>
<p>For the health-conscious beachgoer, this charming cafe on Jumeira Beach Road serves organic, gluten-free, and vegan options in a beautifully designed space. Their acai bowls are perfection.</p>

<h2>Tips for Street Food Exploration</h2>
<ul>
<li>Most street food spots are cash-only, though this is changing</li>
<li>Evenings (7-10 PM) are the best time for the full experience</li>
<li>Don\'t be shy about trying new things — the variety is incredible</li>
<li>Carry water — some dishes are spicier than expected!</li>
</ul>',
            ],
            [
                'title' => 'How to Plan the Perfect Dubai Beach Wedding',
                'slug' => 'plan-perfect-dubai-beach-wedding',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1200&q=80',
                'excerpt' => 'Say "I do" with your toes in the sand and the Arabian Gulf as your backdrop. Everything you need to know about planning a dream beach wedding in Dubai.',
                'tags' => ['wedding', 'beach', 'planning', 'dubai', 'romance'],
                'is_featured' => false,
                'days_ago' => 25,
                'view_count' => 1560,
                'content' => '<h2>Your Dream Beach Wedding Awaits</h2>
<p>Dubai\'s beachfront hotels offer some of the most spectacular wedding venues in the world. Imagine exchanging vows as the sun sets over the Arabian Gulf, with the iconic skyline as your backdrop.</p>

<h3>Choosing Your Venue</h3>
<p>Most beachfront hotels along Jumeira offer dedicated wedding packages. Key factors to consider: guest capacity, indoor backup options (in case of weather), catering quality, and accommodation for guests.</p>

<h3>Best Time of Year</h3>
<p>The ideal months for a beach wedding in Dubai are November through March. The weather is perfect — warm days, cool evenings, and minimal chance of rain. Sunset ceremonies are particularly magical during December and January.</p>

<h3>Legal Requirements</h3>
<p>Dubai welcomes destination weddings from all nationalities. You\'ll need to arrange the legal ceremony separately (at a court or consulate), while the beach event serves as your celebration. Many hotels have coordinators who can guide you through the process.</p>

<h3>Budget Planning</h3>
<p>Beach weddings in Dubai range from intimate affairs (50,000 AED) to lavish celebrations (500,000+ AED). Most hotels offer tiered packages that include venue, catering, decorations, and coordination.</p>

<h3>What\'s Typically Included</h3>
<ul>
<li>Beach ceremony setup with chairs, arch, and aisle</li>
<li>Reception dinner (buffet or plated service)</li>
<li>Floral arrangements and decorations</li>
<li>Wedding cake</li>
<li>Sound system and lighting</li>
<li>Bridal suite for the couple</li>
<li>Group room rates for guests</li>
</ul>

<p>Start planning at least 6-12 months in advance for the best venue availability and rates.</p>',
            ],
            [
                'title' => 'Dubai Marina vs JBR: Which Neighbourhood Should You Stay In?',
                'slug' => 'dubai-marina-vs-jbr-neighbourhood-guide',
                'category' => 'travel-tips',
                'featured_image_url' => 'https://images.unsplash.com/photo-1528702748617-c64d49f918af?w=1200&q=80',
                'excerpt' => 'Two of Dubai\'s most popular beachside neighbourhoods compared. We break down the pros, cons, and vibes of each to help you pick the perfect base.',
                'tags' => ['marina', 'jbr', 'neighbourhood', 'comparison', 'dubai'],
                'is_featured' => false,
                'days_ago' => 30,
                'view_count' => 4150,
                'content' => '<h2>The Great Debate: Marina or JBR?</h2>
<p>Both Dubai Marina and Jumeirah Beach Residence (JBR) are beloved by tourists and residents alike. They\'re right next to each other geographically, but each has its own distinct personality.</p>

<h3>Dubai Marina</h3>
<p><strong>The Vibe:</strong> Urban waterfront living. Think Manhattan meets Miami. The marina is lined with yachts, surrounded by soaring towers, and buzzing with restaurants and cafes.</p>
<p><strong>Best For:</strong> Nightlife lovers, yacht enthusiasts, foodies who want variety, photographers who love cityscapes.</p>
<p><strong>Pros:</strong></p>
<ul>
<li>Marina Walk is one of Dubai\'s best promenades</li>
<li>Huge variety of restaurants at every price point</li>
<li>Metro station nearby for easy city access</li>
<li>Vibrant nightlife scene</li>
</ul>
<p><strong>Cons:</strong> No direct beach access (need to walk to JBR beach), can feel very urban, gets crowded on weekends.</p>

<h3>JBR (Jumeirah Beach Residence)</h3>
<p><strong>The Vibe:</strong> Beach holiday with urban convenience. It\'s more relaxed, more family-friendly, and the beach is right there.</p>
<p><strong>Best For:</strong> Families, beach lovers, couples seeking relaxation, anyone who wants to wake up and walk straight to the sand.</p>
<p><strong>Pros:</strong></p>
<ul>
<li>Direct access to one of Dubai\'s best public beaches</li>
<li>The Walk at JBR — outdoor shopping and dining strip</li>
<li>More relaxed atmosphere</li>
<li>Bluewaters Island (Ain Dubai) accessible by bridge</li>
</ul>
<p><strong>Cons:</strong> Fewer nightlife options, can feel touristy, slightly further from metro.</p>

<h2>Our Verdict</h2>
<p>Choose Marina if you want energy, nightlife, and a city vibe. Choose JBR if the beach is your priority and you prefer a more laid-back atmosphere. Either way, they\'re close enough to walk between — so you get the best of both worlds.</p>',
            ],
            [
                'title' => 'Family-Friendly Beach Hotels: Where Kids Are VIPs',
                'slug' => 'family-friendly-beach-hotels-kids-vip',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1602002418816-5c0aeef426aa?w=1200&q=80',
                'excerpt' => 'Travelling with kids doesn\'t mean sacrificing luxury. These Jumeira Beach hotels roll out the red carpet for families with amazing kids\' clubs, pools, and activities.',
                'tags' => ['family', 'kids', 'hotels', 'beach', 'activities'],
                'is_featured' => false,
                'days_ago' => 35,
                'view_count' => 2780,
                'content' => '<h2>Luxury for the Whole Family</h2>
<p>Gone are the days when luxury hotels were adults-only territory. Dubai\'s beachfront properties have embraced family travel with incredible kids\' facilities that keep little ones entertained while parents relax.</p>

<h3>What to Look For</h3>
<p>The best family beach hotels offer dedicated kids\' clubs with professional staff, children\'s pools with slides and splash zones, family suites with separate sleeping areas, and child-friendly dining options.</p>

<h3>Kids\' Club Excellence</h3>
<p>Top properties offer supervised activity programmes for children aged 4-12, including arts and crafts, sports, movie nights, and even junior cooking classes. Many clubs are complimentary for hotel guests.</p>

<h3>Pool Paradise</h3>
<p>Look for hotels with dedicated children\'s pools — the best ones feature slides, water cannons, lazy rivers, and splash pads. Some properties have up to 5 different pools, ensuring there\'s a perfect spot for every family member.</p>

<h3>Beach Safety</h3>
<p>All major beach hotels have lifeguards on duty, and many offer sheltered beach areas for families. Some provide complimentary beach toys, float rentals, and even kids\' beach tents.</p>

<h3>Family Dining Tips</h3>
<ul>
<li>Most hotels offer kids-eat-free deals during certain hours</li>
<li>Buffet restaurants are usually the most family-friendly option</li>
<li>Room service with kids\' menus means stress-free evenings</li>
<li>Many poolside cafes offer healthy kids\' options</li>
</ul>

<p>Pro tip: Book during school holidays for the best kids\' programming, but shoulder seasons for the best rates. Many hotels run special family packages that include theme park tickets and other extras.</p>',
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
                    'meta_title' => $postData['title'] . ' | Jumeira Beach Hotels',
                    'meta_description' => $postData['excerpt'],
                ]
            );

            // Attach to domain
            $post->domains()->syncWithoutDetaching([$domain->id]);
        }

        $this->command->info('Seeded ' . count($posts) . ' blog posts with ' . count($categories) . ' categories for jumeira-beach-hotels domain.');
    }
}
