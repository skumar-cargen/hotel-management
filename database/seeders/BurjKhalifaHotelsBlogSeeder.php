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

class BurjKhalifaHotelsBlogSeeder extends Seeder
{
    public function run(): void
    {
        $domain = Domain::where('slug', 'burjkhalifahotelsonline')->first();
        if (! $domain) {
            $this->command->warn('Domain burjkhalifahotelsonline not found. Skipping blog seeder.');
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
            ['name' => 'Urban Life', 'slug' => 'urban-life', 'description' => 'Discover the vibrant city lifestyle in Dubai\'s urban neighbourhoods', 'sort_order' => 2],
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
                'title' => 'Top 10 Burj Khalifa Viewing Spots You Haven\'t Discovered',
                'slug' => 'top-10-burj-khalifa-viewing-spots',
                'category' => 'things-to-do',
                'featured_image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1200&q=80',
                'excerpt' => 'Everyone takes the same photo of Burj Khalifa. Here are 10 secret spots that will give you a completely unique perspective of the world\'s tallest building.',
                'tags' => ['burj khalifa', 'downtown dubai', 'photography', 'views', 'dubai'],
                'is_featured' => true,
                'days_ago' => 3,
                'view_count' => 6250,
                'content' => '<h2>See the World\'s Tallest Building Like Nobody Else</h2>
<p>At 828 metres, Burj Khalifa dominates the Dubai skyline from virtually every angle. But while millions of visitors snap the same predictable photos from the Dubai Mall entrance or the fountain boardwalk, a handful of secret vantage points offer views that are truly extraordinary. Here are ten spots that will give you a completely unique perspective.</p>

<h3>1. Souk Al Bahar Terrace</h3>
<p>Tucked behind the tourist crowds, the upper terrace of Souk Al Bahar offers a reflection-pool framing of Burj Khalifa that few visitors discover. The traditional Arabian architecture of the souk creates a stunning contrast with the ultra-modern tower. Visit at golden hour for the best light, when the tower glows amber against the deepening sky.</p>

<h3>2. Business Bay Canal Walk</h3>
<p>The Dubai Water Canal promenade in Business Bay provides a completely different angle on Burj Khalifa. Walking south along the canal, you\'ll find the tower framed between modern Business Bay skyscrapers, reflected perfectly in the still canal water. Early morning is ideal when the canal is mirror-calm and the light is soft.</p>

<h3>3. DIFC Rooftop Bars</h3>
<p>Several rooftop venues in the Dubai International Financial Centre offer elevated views of Burj Khalifa from the south-west. The tower appears against a backdrop of the wider Downtown skyline, giving a sense of scale that ground-level views simply cannot match. Try the rooftop at Gate Village for a particularly dramatic perspective.</p>

<h3>4. Al Khail Road Bridge</h3>
<p>This elevated road bridge offers one of the most cinematic views in Dubai. Burj Khalifa rises directly ahead as you drive or walk across, with the entire Downtown cluster spread before you. At night, the illuminated tower against the dark sky is absolutely breathtaking. Note: this is best accessed by car or taxi.</p>

<h3>5. Palace Downtown Boardwalk</h3>
<p>The boardwalk that runs behind the Palace Downtown hotel is quieter than the main fountain promenade. From here, you get Burj Khalifa framed by palm trees and the elegant low-rise architecture of the Palace, creating a more intimate and romantic composition.</p>

<h3>6. Dubai Design District (d3)</h3>
<p>The creative district south of Downtown offers a fresh perspective. From the d3 waterfront, Burj Khalifa appears across the creek extension, with the developing Dubai Creek Harbour providing an interesting foreground. It\'s a view that tells the story of Dubai\'s constant evolution.</p>

<h3>7. Ras Al Khor Wildlife Sanctuary</h3>
<p>For something truly unique, visit the flamingo hides at Ras Al Khor. Through the viewing windows, you can photograph flamingos with Burj Khalifa rising in the background — a juxtaposition of nature and engineering that perfectly captures Dubai\'s character.</p>

<h3>8. The Dubai Frame</h3>
<p>From the top of the Dubai Frame in Zabeel Park, Burj Khalifa appears in the context of the entire modern Dubai skyline. The Frame itself creates a literal picture frame for the view, and the glass-floor walkway adds an extra thrill to the experience.</p>

<h3>9. Meydan Bridge</h3>
<p>The bridge leading to Meydan Racecourse offers a sweeping panoramic view of Downtown Dubai with Burj Khalifa as the centrepiece. At night during the racing season, the illuminated racecourse adds another layer of spectacle to the scene.</p>

<h3>10. Al Safa Park Hill</h3>
<p>The gentle hills in Al Safa Park provide an elevated green foreground for Burj Khalifa photos. It\'s a peaceful, uncrowded spot that locals love for evening walks. The tower appears in the distance, rising above a canopy of trees — a surprisingly serene composition in this fast-paced city.</p>

<h2>Photography Tips</h2>
<ul>
<li>Golden hour (30 minutes before sunset) offers the warmest light on Burj Khalifa\'s steel facade</li>
<li>Blue hour (20-30 minutes after sunset) is when the tower\'s LED lights are most vivid against the twilight sky</li>
<li>Use a wide-angle lens from close distances and a telephoto from farther spots to compress the skyline</li>
<li>Reflections in water work best on calm, windless evenings</li>
</ul>',
            ],
            [
                'title' => 'Downtown Dubai Neighbourhood Guide: Beyond the Skyline',
                'slug' => 'downtown-dubai-neighbourhood-guide',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1582672060674-bc2bd808a8b5?w=1200&q=80',
                'excerpt' => 'Downtown Dubai is more than Burj Khalifa and Dubai Mall. Discover the local secrets, hidden caf\u00e9s, art spaces, and quiet corners of this iconic district.',
                'tags' => ['downtown dubai', 'neighbourhood guide', 'local tips', 'dubai', 'travel'],
                'is_featured' => true,
                'days_ago' => 7,
                'view_count' => 5800,
                'content' => '<h2>The Soul Behind the Skyline</h2>
<p>Ask most visitors about Downtown Dubai and they\'ll mention Burj Khalifa, Dubai Mall, and the fountains. But this district has a depth and character that goes far beyond its headline attractions. Here\'s your guide to the Downtown that locals know and love.</p>

<h3>The Dubai Fountain Boardwalk</h3>
<p>Yes, everyone watches the fountain show. But the 272-metre floating boardwalk that extends into the Burj Khalifa Lake is a different experience entirely. For just 20 AED, you walk out over the water and stand metres away from the jets as they erupt around you. The sound, the mist on your face, the sheer power of the water — it\'s visceral in a way that watching from shore simply isn\'t. Shows run every 30 minutes from 6 PM, but the 9 PM and 9:30 PM shows tend to feature the most dramatic choreography.</p>

<h3>Souk Al Bahar: The Hidden Market</h3>
<p>Connected to Dubai Mall but worlds apart in atmosphere, Souk Al Bahar is a traditional Arabian marketplace reimagined for the modern era. The architecture features wind towers, stone corridors, and wooden lattice screens. Beyond the souvenir shops on the ground floor, the upper levels house some of Downtown\'s best restaurants — with terrace seating overlooking the fountain lake. It\'s where residents come to escape the mall\'s intensity.</p>

<h3>The Opera District</h3>
<p>The area surrounding Dubai Opera has quietly become Downtown\'s cultural heart. The opera house itself is an architectural marvel — a glass-sailed dhow that hosts ballet, opera, concerts, and comedy. But the surrounding streets have blossomed into a dining and lifestyle destination. Walk along Mohammed Bin Rashid Boulevard in the evening and you\'ll find a European-style promenade lined with cafes, restaurants, and artisan boutiques.</p>

<h3>Boulevard Restaurants and Cafes</h3>
<p>Emaar Boulevard is Downtown\'s social spine — a tree-lined avenue that curves through the district from Dubai Mall to the Business Bay crossing. The ground-floor retail spaces host an eclectic mix of dining options: Lebanese mezze bars, Italian trattorias, Japanese ramen shops, and specialty coffee roasters. On cooler evenings, the outdoor terraces fill with residents enjoying the boulevard\'s relaxed pace. Look for the smaller, independently owned cafes between the larger chains — they\'re often the best.</p>

<h3>Dubai Opera and the Arts</h3>
<p>Beyond the main auditorium, Dubai Opera hosts an outdoor events programme that brings the forecourt to life. Free concerts, art installations, and cultural festivals are common, particularly during the Dubai Shopping Festival and Dubai Arts Season. The nearby Burj Park also hosts pop-up events, food festivals, and outdoor cinema screenings throughout the cooler months.</p>

<h3>Quiet Corners Most Visitors Miss</h3>
<p>Behind the glittering facades, Downtown Dubai has pockets of tranquillity that few tourists find. The landscaped gardens behind the Address Residences offer shaded walking paths with fountain views. The small park between the Boulevard towers is a favourite of local families. And the early morning hours — before 9 AM — transform the normally busy fountain promenade into a peaceful lakeside walk.</p>

<h2>Getting Around Downtown</h2>
<ul>
<li><strong>On foot:</strong> Downtown is surprisingly walkable — most attractions are within a 15-minute walk of each other</li>
<li><strong>Dubai Metro:</strong> Burj Khalifa/Dubai Mall Station (Red Line) puts you right at the entrance</li>
<li><strong>Dubai Trolley:</strong> A heritage-style streetcar runs along Mohammed Bin Rashid Boulevard</li>
<li><strong>Taxis and ride-hailing:</strong> Readily available at all hotel entrances and mall exits</li>
</ul>',
            ],
            [
                'title' => 'Best Restaurants with Dubai Fountain Views',
                'slug' => 'best-restaurants-dubai-fountain-views',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80',
                'excerpt' => 'Watching the Dubai Fountain show while dining is one of Dubai\'s quintessential experiences. Here are the restaurants that do it best \u2014 from fine dining to casual terraces.',
                'tags' => ['dubai fountain', 'restaurants', 'dining', 'downtown dubai', 'views'],
                'is_featured' => true,
                'days_ago' => 10,
                'view_count' => 4920,
                'content' => '<h2>Dinner and a Show Like No Other</h2>
<p>The Dubai Fountain is the world\'s largest choreographed fountain system, sending water jets up to 150 metres into the air against the backdrop of Burj Khalifa. Watching the show while enjoying a meal is one of those quintessential Dubai experiences that lives up to the hype. But not all fountain-view restaurants are created equal. Here\'s our curated guide to the best.</p>

<h3>Fine Dining with Front-Row Views</h3>

<h4>At.mosphere \u2014 Burj Khalifa</h4>
<p>Perched on the 122nd floor of Burj Khalifa, At.mosphere doesn\'t just overlook the fountain — it looks down on it from 442 metres above. The perspective is otherworldly: the fountain appears as a miniature light show far below, with the entire Dubai skyline stretching to the horizon. The modern European cuisine matches the setting in ambition and quality. Expect to spend 800-1,500 AED per person, and book at least two weeks in advance for a window table.</p>

<h4>Armani Ristorante \u2014 Armani Hotel</h4>
<p>Located in the Armani Hotel inside Burj Khalifa, this Italian restaurant designed by Giorgio Armani himself offers a more intimate fountain experience. The terrace tables are at lake level, close enough to feel the mist from the jets. The northern Italian menu is refined and elegant — much like the decor. The lobster risotto is exceptional. Budget 600-1,000 AED per couple.</p>

<h3>Souk Al Bahar Terraces</h3>

<h4>Mango Tree Thai Bistro</h4>
<p>The outdoor terrace at Mango Tree is one of the best-positioned tables in all of Downtown. You sit directly across the lake from Burj Khalifa, with the fountain performing metres away. The Thai cuisine is authentic and well-executed — try the green curry and pad thai. At 200-350 AED for two, it\'s one of the most affordable fountain-view dining experiences.</p>

<h4>Tribes Carnivore</h4>
<p>A South African steakhouse with one of the largest terraces on Souk Al Bahar. The flame-grilled meats are excellent, and the portions are generous. It\'s a lively, social atmosphere that\'s perfect for groups. The terrace fills quickly in the evening, so arrive before 7 PM or book ahead.</p>

<h3>Address Downtown Restaurants</h3>

<h4>Zeta \u2014 Address Downtown</h4>
<p>The Address Downtown\'s signature restaurant sits right on the fountain promenade. The modern Asian menu blends Japanese, Thai, and Chinese flavours with finesse. The outdoor lounge area is the place to be — low seating, atmospheric lighting, and the fountain show as your backdrop. It\'s a favourite for special occasions.</p>

<h3>Casual Terrace Dining</h3>

<h4>The Boulevard Cafes</h4>
<p>Several cafes along Emaar Boulevard offer partial fountain views at a fraction of lakeside prices. While the view isn\'t as dramatic, you can hear the music and see the water jets between the buildings. Grab a shisha and Arabic coffee for under 100 AED and enjoy a relaxed evening.</p>

<h2>Tips for Fountain-View Dining</h2>
<ul>
<li><strong>Book early:</strong> Request an outdoor terrace table and specify "fountain view" when reserving</li>
<li><strong>Timing:</strong> Shows run every 30 minutes from 6 PM to 11 PM; plan your main course around the show times</li>
<li><strong>Best season:</strong> October to April, when the outdoor terrace weather is perfect</li>
<li><strong>Photography:</strong> The best fountain photos are taken during the first 15 minutes after sunset when there\'s still colour in the sky</li>
</ul>',
            ],
            [
                'title' => 'Why Downtown Dubai Is the Ultimate Hotel District',
                'slug' => 'why-downtown-dubai-ultimate-hotel-district',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1546412414-e1885259563a?w=1200&q=80',
                'excerpt' => 'From five-star palaces inside Burj Khalifa to affordable boutique stays, Downtown Dubai offers more hotel choices per square kilometre than anywhere else in the city.',
                'tags' => ['downtown dubai', 'hotels', 'luxury', 'accommodation', 'dubai'],
                'is_featured' => true,
                'days_ago' => 14,
                'view_count' => 4100,
                'content' => '<h2>The Centre of Everything</h2>
<p>Downtown Dubai isn\'t just a tourist attraction — it\'s the most concentrated hotel district in the entire UAE. Within a two-square-kilometre area, you\'ll find over 30 hotels ranging from ultra-luxury residences to smart four-star properties. Here\'s why staying in Downtown is the smartest choice for any Dubai visit.</p>

<h3>Unmatched Density of World-Class Hotels</h3>
<p>No other district in Dubai packs so many internationally renowned hotel brands into such a compact space. The Armani Hotel occupies the lower floors of Burj Khalifa itself. The Address Hotels operate three distinct properties within walking distance of each other. The Palace Downtown offers old-world Arabian elegance beside the modern fountain lake. The Vida brand caters to design-conscious millennials, while the Rove hotel brings accessible style to budget travellers. Whatever your taste or budget, Downtown has a hotel that fits.</p>

<h3>Walking Distance to Everything</h3>
<p>This is Downtown\'s greatest advantage over other Dubai hotel districts. From any hotel in the area, you can walk to Burj Khalifa, Dubai Mall, the Dubai Fountain, Souk Al Bahar, Dubai Opera, and dozens of restaurants along the Boulevard — all without needing a taxi. In a city where most attractions require a car, Downtown\'s walkability is genuinely rare and incredibly valuable.</p>

<h3>A Range of Price Points</h3>
<p>Contrary to popular belief, you don\'t need to be a millionaire to stay in Downtown Dubai. While the Armani and Palace properties command premium rates of 2,000-5,000 AED per night, there are excellent options at every price point:</p>
<ul>
<li><strong>Ultra-Luxury (2,000-5,000+ AED):</strong> Armani Hotel, The Address Downtown, Palace Downtown</li>
<li><strong>Premium (1,000-2,000 AED):</strong> Address Boulevard, Vida Downtown, The Address Dubai Mall</li>
<li><strong>Mid-Range (500-1,000 AED):</strong> Rove Downtown, Manzil Downtown, Damac Maison</li>
<li><strong>Serviced Apartments (400-800 AED):</strong> Address Residences, Downtown hotel apartments</li>
</ul>

<h3>Transport Links</h3>
<p>Downtown Dubai is served by the Burj Khalifa/Dubai Mall Metro station on the Red Line, connecting you to the airport (25 minutes), Dubai Marina (15 minutes), and the historic Creek area (20 minutes). Taxis are abundant, ride-hailing apps work seamlessly, and many hotels offer complimentary shuttle services to the beach and other attractions.</p>

<h3>The Atmosphere</h3>
<p>Downtown Dubai comes alive in the evening in a way that no other district quite matches. The fountain shows draw crowds every 30 minutes, the Boulevard fills with diners and strollers, and the illuminated Burj Khalifa creates a backdrop that never gets old. There\'s an energy here — a sense of being at the centre of something extraordinary — that makes every evening feel like an event.</p>

<h2>Our Verdict</h2>
<p>If you\'re visiting Dubai for the first time, Downtown is the obvious choice. If you\'re returning, it\'s still the most convenient and exciting base. The variety of hotels, the walkability, the transport links, and the sheer spectacle of the location make Downtown Dubai the ultimate hotel district in the Middle East.</p>',
            ],
            [
                'title' => 'A First-Timer\'s Guide to Dubai Mall',
                'slug' => 'first-timers-guide-dubai-mall',
                'category' => 'travel-tips',
                'featured_image_url' => 'https://images.unsplash.com/photo-1614624532983-4ce03382d63d?w=1200&q=80',
                'excerpt' => 'With 1,200+ stores spread across 12 million square feet, Dubai Mall can be overwhelming. Here\'s how to navigate it like a pro \u2014 including the free attractions most visitors miss.',
                'tags' => ['dubai mall', 'shopping', 'tips', 'downtown dubai', 'attractions'],
                'is_featured' => false,
                'days_ago' => 18,
                'view_count' => 3850,
                'content' => '<h2>Conquering the World\'s Largest Mall</h2>
<p>Dubai Mall isn\'t just a shopping centre — it\'s a small city. With over 1,200 stores, 200 restaurants, an aquarium, an ice rink, a waterfall, a dinosaur skeleton, and direct access to Burj Khalifa, it\'s entirely possible to spend an entire day here without seeing everything. Here\'s how to navigate it like a local.</p>

<h3>The Layout: Understanding the Zones</h3>
<p>Dubai Mall is divided into several distinct zones, each with its own character. The Fashion Avenue wing houses luxury brands — Chanel, Dior, Louis Vuitton — in a marble-and-gold setting that feels like a high-end department store. The main concourse is home to high-street brands and the mall\'s major attractions. The Souk section has jewellery, perfume, and traditional Arabic goods. And the Village section caters to families with a dedicated children\'s zone. Pick your zone based on your interests and save hours of aimless wandering.</p>

<h3>Free Attractions You Shouldn\'t Miss</h3>

<h4>The Dubai Aquarium Viewing Panel</h4>
<p>The massive viewing panel on the ground floor of Dubai Mall is free to enjoy. At 32.88 metres wide and 8.3 metres high, it\'s one of the largest acrylic panels in the world, containing 10 million litres of water and over 33,000 aquatic animals. The paid underwater tunnel experience is worth it, but the free viewing panel alone is spectacular — especially when the divers enter the tank for feeding time.</p>

<h4>The Waterfall</h4>
<p>Spanning four storeys, the Human Waterfall is one of Dubai Mall\'s most photographed features. Life-sized fiberglass sculptures of divers appear to plunge through a cascading water curtain. It\'s located near the Souk section — look up from the ground floor for the most dramatic view.</p>

<h4>The Dinosaur Skeleton</h4>
<p>A 155-million-year-old Diplodocus skeleton stands in the main concourse. Discovered in Wyoming, USA, "Dubai Dino" is 24 metres long and 7.6 metres tall. It\'s completely free to view and makes for a surreal photo opportunity.</p>

<h4>Dubai Fountain Show</h4>
<p>Exit through the lower ground floor waterfront exit to reach the Dubai Fountain boardwalk. Shows run every 30 minutes from 6 PM and are completely free to watch from the promenade. Arrive 10 minutes early for a front-row spot along the railing.</p>

<h3>Food Courts and Dining</h3>
<p>Dubai Mall has multiple food courts spread across different levels. The main food court on the second floor offers the widest variety — over 50 outlets covering every cuisine imaginable. For something more upscale, the restaurants along the waterfront promenade combine good food with fountain views. The Fashion Avenue food hall is a newer, less crowded alternative with premium casual dining options.</p>

<h3>Practical Navigation Tips</h3>
<ul>
<li><strong>Download the Dubai Mall app</strong> — the interactive map is essential for finding specific stores</li>
<li><strong>Enter from the Metro link</strong> — the covered walkway from the Metro station deposits you at the centre of the mall</li>
<li><strong>Wear comfortable shoes</strong> — you\'ll walk 5-10 km without realising it</li>
<li><strong>Visit weekday mornings</strong> for the quietest experience; avoid Friday evenings when the mall is at peak capacity</li>
<li><strong>Use the valet parking</strong> if driving — the car parks are enormous and confusing</li>
<li><strong>Tourist tax refund:</strong> Spend over 300 AED in a single store and you can reclaim the 5% VAT at the airport</li>
</ul>',
            ],
            [
                'title' => '48 Hours in Downtown Dubai: Art, Culture & Fine Dining',
                'slug' => '48-hours-downtown-dubai-art-culture-dining',
                'category' => 'things-to-do',
                'featured_image_url' => 'https://images.unsplash.com/photo-1597659840241-37e2b7349c3d?w=1200&q=80',
                'excerpt' => 'Two days, one iconic district. Here\'s the perfect itinerary for experiencing the best of Downtown Dubai\'s art, culture, and culinary scene.',
                'tags' => ['downtown dubai', 'itinerary', 'art', 'culture', 'dining', 'dubai'],
                'is_featured' => false,
                'days_ago' => 22,
                'view_count' => 3200,
                'content' => '<h2>The Perfect Downtown Dubai Itinerary</h2>
<p>Downtown Dubai packs an extraordinary amount of culture, cuisine, and spectacle into a compact area. With 48 hours, you can experience the district\'s greatest hits while uncovering the quieter pleasures that most visitors miss entirely. Here\'s your day-by-day guide.</p>

<h3>Day 1 — Morning: At the Top and Beyond</h3>
<p>Start early with the "At the Top" experience at Burj Khalifa. Book the 8:30 AM or 9:00 AM slot for the shortest queues and the best morning light. The standard ticket (Level 124/125) is excellent value at 169 AED, but the premium "At the Top SKY" (Level 148) at 399 AED is worth the splurge for the dedicated lounge and 555-metre views. Allow 90 minutes for the full experience.</p>

<h3>Day 1 — Afternoon: Culture and Art</h3>
<p>Walk to the DIFC area (20 minutes on foot or a quick taxi ride) for an afternoon immersed in art. Gate Village is home to some of the Middle East\'s most important contemporary art galleries, including Opera Gallery, Ayyam Gallery, and Custot Gallery. Entrance is free, and the quality rivals galleries in London or New York. Break for lunch at one of Gate Avenue\'s restaurants — La Petite Maison serves some of the best French food in Dubai.</p>

<h3>Day 1 — Evening: Fountain Show and Fine Dining</h3>
<p>Return to Downtown for the evening fountain shows. Walk the Burj Khalifa Lake promenade and watch at least two shows (they run every 30 minutes from 6 PM). Each show features different music and choreography, so no two are alike. Book dinner at one of the Souk Al Bahar terrace restaurants — Mango Tree or Tribes — for fountain-view dining without the five-star price tag.</p>

<h3>Day 2 — Morning: Dubai Opera and the Boulevard</h3>
<p>Begin day two at Dubai Opera, even if you\'re not catching a show. The building itself is worth seeing — designed to resemble a traditional dhow, it\'s one of Dubai\'s most striking modern structures. Check the daytime programme for guided tours or exhibitions. Then stroll Mohammed Bin Rashid Boulevard, stopping at the specialty coffee shops and artisan bakeries that line the street. The croissants at Hamptons Cafe are exceptional.</p>

<h3>Day 2 — Afternoon: Dubai Mall Deep Dive</h3>
<p>Dedicate the afternoon to Dubai Mall, but go beyond the shopping. Visit the Dubai Aquarium and Underwater Zoo (the full experience, not just the free viewing panel). Spend time at VR Park for immersive virtual reality experiences. Browse the flagship stores at Fashion Avenue. And don\'t miss the Human Waterfall, the dinosaur skeleton, and the Olympic-sized ice rink — they\'re all free to view.</p>

<h3>Day 2 — Evening: Sunset and Celebration</h3>
<p>For your final evening, head to At.mosphere lounge on Level 122 of Burj Khalifa for sunset cocktails — the view as the city transitions from golden hour to a sea of lights is unforgettable. Then descend for a farewell dinner at Thiptara, the Thai restaurant at The Palace Downtown, where you can dine lakeside with the fountain show as your entertainment. It\'s the perfect finale to 48 hours in Dubai\'s most spectacular district.</p>

<h2>Budget Summary</h2>
<ul>
<li><strong>Burj Khalifa At the Top:</strong> 169-399 AED per person</li>
<li><strong>DIFC Art Galleries:</strong> Free</li>
<li><strong>Dubai Fountain:</strong> Free (boardwalk 20 AED)</li>
<li><strong>Dubai Aquarium:</strong> 135-270 AED per person</li>
<li><strong>Dining (2 days):</strong> 400-1,200 AED per person depending on choices</li>
</ul>',
            ],
            [
                'title' => 'Business Traveller\'s Guide to DIFC Hotels',
                'slug' => 'business-travellers-guide-difc-hotels',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80',
                'excerpt' => 'DIFC is the business heart of Dubai. Here\'s everything you need to know about staying in the financial district \u2014 from the best hotels to after-work dining spots.',
                'tags' => ['difc', 'business travel', 'hotels', 'dubai', 'financial centre'],
                'is_featured' => false,
                'days_ago' => 26,
                'view_count' => 2900,
                'content' => '<h2>Where Business Meets Five-Star Hospitality</h2>
<p>The Dubai International Financial Centre is the Middle East\'s leading financial hub, home to over 2,500 companies including global banks, hedge funds, and professional services firms. For business travellers, staying in or near DIFC means being at the heart of the action. Here\'s your complete guide.</p>

<h3>Gate Village: The DIFC Hub</h3>
<p>Gate Village is the social and cultural centre of DIFC — a pedestrianised cluster of buildings connected by walkways and plazas. It houses art galleries, restaurants, cafes, and retail boutiques. More importantly for business travellers, it\'s where many client meetings happen over coffee or lunch. Knowing your way around Gate Village is essential for anyone doing business in Dubai\'s financial sector.</p>

<h3>The Hotels</h3>

<h4>Four Seasons Dubai DIFC</h4>
<p>The premium choice for the financial district. The Four Seasons sits right in Gate Village, meaning you can walk to meetings without stepping outside. The rooms are spacious and impeccably designed, the spa is one of the best in the city, and Luna — the rooftop bar — is where DIFC deals are often celebrated. Executive rooms start at 1,800 AED per night, but the club lounge access and seamless business facilities justify the price.</p>

<h4>Ritz-Carlton DIFC</h4>
<p>The Ritz-Carlton brings its trademark understated luxury to the financial district. The hotel is connected to Gate Village via a covered walkway, and its meeting rooms and boardroom facilities are excellent. The spa, pool deck, and signature restaurant make it a complete destination. Rooms from 1,500 AED include access to the club lounge with complimentary breakfast and evening canapés.</p>

<h4>Waldorf Astoria DIFC</h4>
<p>Slightly newer to the DIFC hotel scene, the Waldorf Astoria offers Art Deco-inspired elegance with modern business amenities. The rooftop pool offers views of Downtown Dubai, and the Bull & Bear steakhouse is already a DIFC dining institution. Ideal for travellers who want luxury with personality.</p>

<h3>Gate Avenue: After-Work Dining</h3>
<p>Gate Avenue is DIFC\'s dining and lifestyle strip, and it comes alive after 6 PM when the offices empty. Here are the spots you need to know:</p>
<ul>
<li><strong>La Petite Maison:</strong> The Dubai outpost of the Nice-based French restaurant is a power-lunch and dinner institution. Book ahead.</li>
<li><strong>Zuma:</strong> Japanese izakaya dining that\'s been a DIFC staple for over a decade. The robata grill and sushi bar are exceptional.</li>
<li><strong>Roberto\'s:</strong> A lively Italian restaurant with a buzzing bar area. Great for client entertainment.</li>
<li><strong>Bull & Bear:</strong> A classic steakhouse at the Waldorf Astoria. The dry-aged cuts are worth the premium.</li>
</ul>

<h3>After-Work Bars</h3>
<p>DIFC has one of Dubai\'s most sophisticated bar scenes. Luna at the Four Seasons is the rooftop to be seen at. Mint Leaf of London combines Indian tapas with creative cocktails. And the smaller wine bars tucked into Gate Village offer a more intimate atmosphere for winding down after a long day of meetings.</p>

<h3>Transport Links</h3>
<p>DIFC is served by the Financial Centre Metro Station (Red Line), putting you 10 minutes from the airport and 5 minutes from Downtown Dubai. Taxis are plentiful, and the RTA bus network connects the district to key business areas. Most hotels offer complimentary luxury car service within a 5 km radius.</p>',
            ],
            [
                'title' => 'Hidden Rooftop Bars in Business Bay',
                'slug' => 'hidden-rooftop-bars-business-bay',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1470337458703-46ad1756a187?w=1200&q=80',
                'excerpt' => 'Business Bay\'s skyline is best enjoyed from above. Discover the rooftop bars that locals love but tourists haven\'t found yet \u2014 with Burj Khalifa as your backdrop.',
                'tags' => ['business bay', 'rooftop bars', 'nightlife', 'dubai', 'cocktails'],
                'is_featured' => false,
                'days_ago' => 30,
                'view_count' => 3650,
                'content' => '<h2>Business Bay After Dark</h2>
<p>While Downtown Dubai draws the tourist crowds and Dubai Marina dominates the nightlife guides, Business Bay has quietly developed one of the most exciting rooftop bar scenes in the city. The advantage? Unobstructed Burj Khalifa views, fewer queues, and a clientele of in-the-know locals and stylish residents. Here are the rooftop bars worth seeking out.</p>

<h3>The Signature Rooftops</h3>

<h4>CE LA VI \u2014 Address Sky View</h4>
<p>Perched on the 54th floor of the Address Sky View hotel, CE LA VI offers what might be the most jaw-dropping Burj Khalifa view in all of Dubai. The tower rises right beside you, close enough that you feel you could almost touch it. The Asian-inspired menu is excellent, the cocktails are creative, and the DJ sets on Thursday and Friday evenings create an electric atmosphere. Book a terrace table for sunset and stay for the evening. Expect to spend 300-500 AED per person.</p>

<h4>Privilege at JW Marriott Marquis</h4>
<p>The JW Marriott Marquis towers are among the tallest hotel buildings in the world, and Privilege bar near the top takes full advantage. The 360-degree views encompass Burj Khalifa, the canal, and the entire Business Bay skyline. The vibe is upscale but relaxed, and the cocktail list features creative twists on classics. The "Burj View" terrace is the spot — arrive before 7 PM on weekends to snag a table without a reservation.</p>

<h3>The Hidden Gems</h3>

<h4>The Oberoi Sky Bar</h4>
<p>The Oberoi hotel is one of Business Bay\'s best-kept secrets, and its intimate rooftop bar is even more under the radar. With just a handful of tables, it feels exclusive without being pretentious. The cocktails are immaculately crafted using house-made syrups and fresh ingredients, and the Burj Khalifa view from the terrace is framed perfectly between the neighbouring towers. It\'s the kind of place where conversations linger and evenings stretch into late nights.</p>

<h4>The Canal-Side Speakeasies</h4>
<p>Several venues along the Dubai Water Canal in Business Bay operate as modern speakeasies — unmarked doors, password entry, and intimate interiors. The cocktail craft at these hidden bars rivals anything in London or New York. Ask your hotel concierge for current recommendations, as these venues change frequently and intentionally keep a low profile. That\'s part of their charm.</p>

<h4>Treehouse at Taj Dubai</h4>
<p>Technically a terrace rather than a rooftop, Treehouse at Taj Dubai is a garden-themed lounge set among actual trees and greenery. It\'s a welcome change from the glass-and-steel aesthetic of most Dubai bars. The Indian-inspired cocktails and tapas are excellent, and the Burj Khalifa peeks through the foliage in a way that feels almost accidental — and all the more beautiful for it.</p>

<h3>Practical Tips for Rooftop Bar Hopping</h3>
<ul>
<li><strong>Dress code:</strong> Smart casual is standard. No shorts, flip-flops, or sportswear at any of these venues</li>
<li><strong>Reservations:</strong> Essential for Thursday and Friday evenings. Walk-ins are possible Sunday to Wednesday</li>
<li><strong>Happy hours:</strong> Many Business Bay bars offer extended happy hours on weekday evenings — check social media for deals</li>
<li><strong>Transport:</strong> Business Bay Metro station serves the area, but taxis and ride-hailing are more practical at night</li>
<li><strong>Best time:</strong> Arrive 30-45 minutes before sunset for the golden hour experience over Burj Khalifa</li>
</ul>',
            ],
            [
                'title' => 'Family-Friendly Hotels Near Dubai Mall',
                'slug' => 'family-friendly-hotels-near-dubai-mall',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1200&q=80',
                'excerpt' => 'Travelling with kids doesn\'t mean compromising on location. These Downtown Dubai hotels combine family-friendly amenities with proximity to Dubai Mall\'s endless entertainment.',
                'tags' => ['family', 'dubai mall', 'hotels', 'kids', 'downtown dubai'],
                'is_featured' => false,
                'days_ago' => 34,
                'view_count' => 2780,
                'content' => '<h2>Luxury Stays That Keep the Whole Family Happy</h2>
<p>Downtown Dubai might seem like an adults-only playground, but several hotels in the area have invested heavily in family-friendly facilities. Combined with the endless entertainment at Dubai Mall — KidZania, the Aquarium, VR Park, and the ice rink — Downtown is actually one of the best districts for a family holiday. Here are the hotels that do it best.</p>

<h3>The Address Boulevard</h3>
<p>The Address Boulevard strikes the perfect balance between family convenience and Downtown style. The spacious rooms and suites are ideal for families who need a bit more space, and many rooms offer interconnecting doors. The rooftop pool has a dedicated children\'s area with shallow sections and splash features, and the pool attendants are particularly attentive to young swimmers. The hotel is connected to Dubai Mall via a short covered walkway, which means you can pop back to the room for naps and outfit changes without the hassle of taxis.</p>

<h3>Rove Downtown</h3>
<p>Rove hotels are designed with young, budget-conscious travellers in mind, but their family rooms are a hidden gem. The rooms are compact but cleverly designed, with bunk beds for kids in the family configurations. The rooftop pool has a fun, social atmosphere, and the 24-hour "Rover" cafe means late-night snack runs are never a problem. At 400-600 AED per night, it\'s the best value family option in Downtown — and the location, steps from Dubai Mall, is unbeatable.</p>

<h3>Vida Downtown</h3>
<p>Part of the Emaar Hospitality Group, Vida Downtown caters to design-conscious families who want a hotel that\'s Instagram-worthy and kid-friendly. The rooms are bright and modern, the rooftop pool and lounge are stylish, and the Boulevard location means restaurants and cafes are on your doorstep. The hotel doesn\'t have a dedicated kids club, but the staff are excellent at arranging family-friendly activities and recommendations.</p>

<h3>The Palace Downtown</h3>
<p>For families who want old-world Arabian luxury, The Palace Downtown is the top choice. Set on the Burj Khalifa Lake with its own private gardens, the Palace feels like an oasis of calm in the bustling district. The kids\' club is well-organised, the pool area is expansive with a dedicated children\'s pool, and the rooms and suites are among the most spacious in Downtown. The direct lake-side access means the fountain shows are your evening entertainment.</p>

<h3>Dubai Mall Family Attractions</h3>
<p>The beauty of staying near Dubai Mall with kids is that you\'ll never run out of things to do:</p>
<ul>
<li><strong>KidZania:</strong> An interactive mini-city where children role-play adult jobs. Expect 3-4 hours of engagement for ages 4-14.</li>
<li><strong>Dubai Aquarium & Underwater Zoo:</strong> Over 33,000 aquatic animals. The glass-bottom boat ride and shark encounters are highlights.</li>
<li><strong>VR Park:</strong> Virtual reality experiences suitable for ages 7+. The Dubai Drone is a particular hit with older kids.</li>
<li><strong>Dubai Ice Rink:</strong> Olympic-sized rink with public sessions and disco nights. Skate hire included in the entry fee.</li>
<li><strong>SEGA Republic:</strong> Arcade gaming and rides for all ages.</li>
</ul>

<h3>Practical Family Tips</h3>
<ul>
<li>Most Downtown hotels offer cribs and extra beds at no charge — request in advance</li>
<li>The Dubai Mall stroller rental service is free and available at multiple entrances</li>
<li>Children under 3 enter most attractions free; ages 3-12 get discounted rates</li>
<li>Pack swimwear for the hotel pool and the Dubai Mall ice rink (surprisingly chilly!)</li>
</ul>',
            ],
            [
                'title' => 'Dubai Creek to Downtown: A Walking Tour',
                'slug' => 'dubai-creek-to-downtown-walking-tour',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1512632578888-169bbbc64f33?w=1200&q=80',
                'excerpt' => 'From the historic Creek to the futuristic Downtown skyline \u2014 this walking tour connects old and new Dubai in one unforgettable journey.',
                'tags' => ['dubai creek', 'downtown dubai', 'walking tour', 'heritage', 'dubai'],
                'is_featured' => false,
                'days_ago' => 38,
                'view_count' => 2450,
                'content' => '<h2>A Walk Through Time</h2>
<p>Dubai is a city of contrasts, and there\'s no better way to experience the full spectrum than a walking tour from the historic Creek to the futuristic Downtown skyline. This route takes you from the traditional souks and wind-tower houses of old Dubai, through the developing cultural corridor, and into the gleaming heart of the modern city. Allow 4-5 hours with stops, or split it across a full day with lunch along the way.</p>

<h3>Stop 1: Al Fahidi Historical Neighbourhood</h3>
<p>Start your walk at Al Fahidi, one of the oldest residential areas in Dubai. The narrow lanes, coral-stone buildings, and wind towers date back to the late 19th century. Today, the neighbourhood houses small museums, art galleries, and courtyard cafes. Visit the Coffee Museum for a fascinating look at the region\'s coffee culture, and browse the XVA Gallery — one of Dubai\'s most respected contemporary art spaces, set inside a traditional house.</p>

<h3>Stop 2: The Creek and Abra Crossing</h3>
<p>Walk north to the Dubai Creek and take an abra (traditional water taxi) across to Deira for just 1 AED. The 5-minute crossing offers beautiful views of the Creek, with wooden dhows moored along the waterfront and the skyline of both Bur Dubai and Deira stretching in either direction. Once across, explore the Gold Souk and Spice Souk — the colours, scents, and energy are intoxicating. Then take the abra back to the Bur Dubai side.</p>

<h3>Stop 3: Dubai Frame and Zabeel Park</h3>
<p>From the Creek, it\'s a 20-minute walk (or short taxi ride) south to the Dubai Frame in Zabeel Park. At 150 metres tall, this landmark structure frames the old city on one side and the new city on the other — the perfect midpoint for your walk between two worlds. The glass-floor observation deck at the top offers panoramic views in both directions. Entry is 50 AED and includes access to the museum in the base.</p>

<h3>Stop 4: Business Bay Canal Walk</h3>
<p>Continue south from Zabeel Park toward the Dubai Water Canal in Business Bay. The canal promenade is one of Dubai\'s newest and most pleasant walking routes. Landscaped gardens, public art installations, and waterside cafes line the path. As you walk east along the canal, Burj Khalifa begins to dominate the skyline ahead of you, growing larger and more imposing with every step.</p>

<h3>Stop 5: Arrival in Downtown Dubai</h3>
<p>The canal walk delivers you into Downtown Dubai via the Business Bay crossing. From here, it\'s a short walk to Mohammed Bin Rashid Boulevard and the Burj Khalifa Lake. You\'ve walked from the 19th century to the 21st century, from wind towers to the world\'s tallest building, from 1 AED abra rides to 1,500 AED hotel suites. The contrast is extraordinary and uniquely Dubai.</p>

<h3>The Route in Numbers</h3>
<ul>
<li><strong>Total walking distance:</strong> Approximately 8-10 km (with some taxi shortcuts available)</li>
<li><strong>Duration:</strong> 4-5 hours with stops, or a full day with lunch and museum visits</li>
<li><strong>Best time:</strong> October to March, starting early morning (8 AM) to avoid midday heat</li>
<li><strong>Cost:</strong> Under 100 AED (abra crossing, Dubai Frame entry, refreshments)</li>
</ul>

<h3>Practical Tips</h3>
<ul>
<li>Wear comfortable walking shoes and light, breathable clothing</li>
<li>Carry a water bottle — hydration is essential, even in cooler months</li>
<li>Start from Al Fahidi in the morning and arrive Downtown for the evening fountain shows</li>
<li>The route can be shortened using the Metro (Al Fahidi Station to Burj Khalifa/Dubai Mall Station)</li>
<li>Dress modestly when visiting the historic Creek area, particularly if entering mosques or the heritage sites</li>
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
                    'meta_title' => $postData['title'] . ' | Burj Khalifa Hotels',
                    'meta_description' => $postData['excerpt'],
                ]
            );

            // Attach to domain
            $post->domains()->syncWithoutDetaching([$domain->id]);
        }

        $this->command->info('Seeded ' . count($posts) . ' blog posts with ' . count($categories) . ' categories for burjkhalifahotelsonline domain.');
    }
}
