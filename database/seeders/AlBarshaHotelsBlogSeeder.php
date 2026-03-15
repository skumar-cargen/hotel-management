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

class AlBarshaHotelsBlogSeeder extends Seeder
{
    public function run(): void
    {
        $domain = Domain::where('slug', 'al-barsha-hotels')->first();
        if (! $domain) {
            $this->command->warn('Domain al-barsha-hotels not found. Skipping blog seeder.');
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
                'title' => 'Top 10 Things to Do Near Mall of the Emirates',
                'slug' => 'top-10-things-to-do-near-mall-of-the-emirates',
                'category' => 'things-to-do',
                'featured_image_url' => 'https://images.unsplash.com/photo-1614624532983-4ce03382d63d?w=1200&q=80',
                'excerpt' => 'Mall of the Emirates is more than just shopping. From indoor skiing to world-class dining, discover the top experiences within walking distance of Al Barsha\'s iconic landmark.',
                'tags' => ['mall of the emirates', 'al barsha', 'activities', 'dubai', 'ski dubai'],
                'is_featured' => true,
                'days_ago' => 3,
                'view_count' => 5120,
                'content' => '<h2>Beyond the Shopping Bags</h2>
<p>Mall of the Emirates is Dubai\'s most iconic shopping destination, but the real magic lies in the incredible experiences that surround it. Whether you\'re staying at a nearby Al Barsha hotel or just visiting for the day, here are ten unmissable things to do.</p>

<h3>1. Ski Dubai — Indoor Snow Park</h3>
<p>Where else in the world can you ski, snowboard, and meet penguins inside a shopping mall? Ski Dubai features five ski runs, a freestyle zone, and the Snow Park with toboggan rides and an ice cave. It\'s a surreal experience that defines Dubai\'s anything-is-possible spirit.</p>

<h3>2. VOX Cinemas IMAX</h3>
<p>Catch the latest blockbusters on one of the largest IMAX screens in the region. The THEATRE by VOX offers a luxury cinema experience with reclining seats, gourmet food, and blankets.</p>

<h3>3. Magic Planet</h3>
<p>This sprawling entertainment centre is perfect for families. With bowling alleys, arcade games, trampolines, and toddler zones, kids of all ages will be entertained for hours.</p>

<h3>4. Yas Island by Jalboot</h3>
<p>The rooftop terrace offers stunning views over Al Barsha. Several restaurants here provide outdoor seating where you can enjoy shisha and panoramic city views as the sun sets.</p>

<h3>5. Art & Cultural Exhibitions</h3>
<p>Mall of the Emirates regularly hosts art exhibitions, fashion shows, and cultural events. Check the events calendar before your visit — you might catch something special.</p>

<h2>Just Outside the Mall</h2>

<h3>6. Al Barsha Pond Park</h3>
<p>A 10-minute walk from the mall, this lovely green space features a lake, jogging tracks, playgrounds, and barbecue areas. It\'s the perfect escape from the mall\'s buzz.</p>

<h3>7. Dubai Butterfly Garden</h3>
<p>Home to over 15,000 butterflies across 26 species, this enchanting garden is just a short drive from Al Barsha 1. A magical experience for all ages.</p>

<h3>8. Dubai Miracle Garden</h3>
<p>The world\'s largest natural flower garden, featuring 150 million flowers in stunning designs. Open from November to May, it\'s a must-visit when staying in Al Barsha.</p>

<h3>9. Kite Beach via Free Shuttle</h3>
<p>Many Al Barsha hotels offer complimentary shuttle buses to Kite Beach. Enjoy water sports, beachside cafés, and the iconic view of Burj Al Arab — all just 15 minutes away.</p>

<h3>10. Alserkal Avenue Art District</h3>
<p>A short drive into Al Quoz brings you to Alserkal Avenue, Dubai\'s thriving arts hub. Explore contemporary galleries, indie cinemas, artisan coffee shops, and creative workshops.</p>',
            ],
            [
                'title' => 'Al Barsha Neighbourhood Guide: A Local\'s Perspective',
                'slug' => 'al-barsha-neighbourhood-guide-locals-perspective',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1200&q=80',
                'excerpt' => 'Forget the tourist brochures. Here\'s what it\'s really like to live and stay in Al Barsha — the real Dubai that most visitors never discover.',
                'tags' => ['al barsha', 'neighbourhood', 'local guide', 'dubai', 'residential'],
                'is_featured' => true,
                'days_ago' => 6,
                'view_count' => 4350,
                'content' => '<h2>The Real Al Barsha</h2>
<p>While Downtown Dubai dazzles with its skyscrapers and Palm Jumeirah draws the Instagram crowds, Al Barsha quietly gets on with being one of Dubai\'s most liveable — and most underrated — neighbourhoods. Here\'s what makes it special.</p>

<h3>A Neighbourhood of Neighbourhoods</h3>
<p>Al Barsha isn\'t just one area — it\'s a collection of distinct communities. Al Barsha 1 is the bustling commercial heart, centred around Mall of the Emirates. Al Barsha South is quieter and more residential, with newer developments and family-friendly parks. Barsha Heights (TECOM) is the business district, buzzing with media and tech companies. And Al Quoz is the creative soul, home to art galleries and warehouse cafés.</p>

<h3>The Metro Changes Everything</h3>
<p>Two Metro stations serve Al Barsha — Mall of the Emirates (Red Line) and Sharaf DG. This means you\'re connected to the entire city without needing a car. Downtown Dubai is 15 minutes away, Dubai Marina is 10 minutes, and even Dubai Creek can be reached in under 30 minutes.</p>

<h3>Where Locals Actually Eat</h3>
<p>Forget the hotel restaurants for a night and explore the neighbourhood. Al Barsha\'s back streets are packed with hidden gems: Filipino bakeries, Pakistani grills, Yemeni honey shops, and Levantine shawarma joints. The food is authentic, the portions are generous, and the prices will make you smile.</p>

<h3>The Parks Nobody Talks About</h3>
<p>Al Barsha has some of Dubai\'s loveliest parks, and they\'re never crowded. Al Barsha Pond Park is a local favourite for evening walks. The smaller community parks scattered throughout the neighbourhood have playgrounds, outdoor gyms, and barbecue spots.</p>

<h3>Shopping Beyond the Mall</h3>
<p>While Mall of the Emirates is the headline act, locals know that the real shopping happens at the smaller centres and street-level shops. You\'ll find everything from tailors to electronics at a fraction of mall prices.</p>

<h2>Why Travellers Should Consider Al Barsha</h2>
<p>Hotel rates in Al Barsha are 30-50% lower than Downtown or Marina, yet you\'re just as well connected. You get a taste of real Dubai life, access to authentic food, and the convenience of one of the city\'s best transport links. It\'s the smart traveller\'s secret.</p>',
            ],
            [
                'title' => 'Best Restaurants in Al Barsha & Barsha Heights',
                'slug' => 'best-restaurants-al-barsha-barsha-heights',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80',
                'excerpt' => 'From Michelin-quality hotel dining to hidden street-food gems, Al Barsha\'s food scene punches well above its weight. Here are the restaurants every foodie needs to try.',
                'tags' => ['restaurants', 'al barsha', 'barsha heights', 'food', 'dining', 'dubai'],
                'is_featured' => true,
                'days_ago' => 10,
                'view_count' => 3890,
                'content' => '<h2>A Foodie\'s Paradise You Didn\'t Expect</h2>
<p>Al Barsha might not be the first neighbourhood that comes to mind when you think of Dubai dining, but it should be. From five-star hotel restaurants to hole-in-the-wall gems, this area offers one of the most diverse and affordable food scenes in the city.</p>

<h3>Fine Dining</h3>

<h4>Aspen by Kempinski</h4>
<p>Located in the Kempinski Hotel, Aspen serves alpine-inspired European cuisine in a warm, wood-panelled setting. The fondue is legendary, and the Sunday brunch is one of Al Barsha\'s best-kept secrets. Expect to spend 250-400 AED per person.</p>

<h4>Toshi — Grand Millennium</h4>
<p>This acclaimed Japanese restaurant in Barsha Heights is a favourite among the media city crowd. The sushi is exceptional, and the sake selection is one of the best in Dubai. The terrace seating offers lovely evening views.</p>

<h3>Casual & Mid-Range</h3>

<h4>QD\'s — Media One Hotel</h4>
<p>The rooftop bar and restaurant at Media One is an institution. Great cocktails, solid pub food, and one of the best views in Barsha Heights. Thursday evenings are legendary for their live music and buzz.</p>

<h4>Noodle House — Various Locations</h4>
<p>A Dubai institution serving pan-Asian comfort food. The laksa, pad thai, and Japanese curry are all excellent. Quick, affordable, and consistently good.</p>

<h3>Hidden Gems</h3>

<h4>Ravi Restaurant</h4>
<p>No Al Barsha food guide is complete without mentioning Ravi. This legendary Pakistani restaurant has been serving incredible biryani, butter chicken, and fresh naan since 1978. A full meal costs under 30 AED — unbeatable value.</p>

<h4>Al Ibrahimi</h4>
<p>A neighbourhood favourite for authentic Emirati breakfast. Try the balaleet (sweet vermicelli with egg) and chebab (Emirati pancakes). It\'s a cultural and culinary experience rolled into one.</p>

<h2>Our Top Picks by Occasion</h2>
<ul>
<li><strong>Date night:</strong> Aspen by Kempinski or Toshi</li>
<li><strong>Business lunch:</strong> Nosh at Mövenpick or QD\'s</li>
<li><strong>Family dinner:</strong> Noodle House or any hotel buffet</li>
<li><strong>Budget feast:</strong> Ravi Restaurant or Al Ibrahimi</li>
<li><strong>Late-night bites:</strong> The 24-hour cafeterias along Al Barsha\'s main roads</li>
</ul>',
            ],
            [
                'title' => 'Why Al Barsha Is Dubai\'s Best-Value Hotel District',
                'slug' => 'al-barsha-dubai-best-value-hotel-district',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1200&q=80',
                'excerpt' => 'Five-star hotels at four-star prices, unbeatable transport links, and a real neighbourhood feel — discover why savvy travellers are choosing Al Barsha over Dubai\'s glitzier districts.',
                'tags' => ['hotels', 'value', 'al barsha', 'budget', 'dubai', 'accommodation'],
                'is_featured' => true,
                'days_ago' => 14,
                'view_count' => 6240,
                'content' => '<h2>The Smart Traveller\'s Secret</h2>
<p>Dubai is full of stunning hotel districts — Downtown with its Burj Khalifa views, the Marina with its yacht-lined waterfront, Palm Jumeirah with its over-the-top luxury. But there\'s one area that consistently delivers the best bang for your dirham: Al Barsha.</p>

<h3>The Numbers Don\'t Lie</h3>
<p>A five-star hotel room in Downtown Dubai averages 1,200-1,800 AED per night. The same calibre of room in Al Barsha? 600-900 AED. That\'s a 40-50% saving without sacrificing quality. Hotels like Kempinski, Sheraton, and Grand Millennium offer genuine five-star luxury at prices that Downtown properties can\'t match.</p>

<h3>Location Advantage</h3>
<p>Al Barsha sits right on the Red Metro Line, putting you 15 minutes from Downtown Dubai, 10 minutes from Dubai Marina, and 25 minutes from the airport. Mall of the Emirates — one of Dubai\'s largest shopping centres — is literally on your doorstep. Many hotels offer free shuttle buses to popular beaches.</p>

<h3>The Full Range</h3>
<p>Whether you\'re a luxury traveller, a business professional, or a budget-conscious family, Al Barsha has a hotel for you:</p>
<ul>
<li><strong>5-Star Luxury:</strong> Kempinski, Sheraton, Grand Millennium — world-class facilities, spas, and restaurants</li>
<li><strong>4-Star Comfort:</strong> Novotel, Media One, Studio One — modern, stylish, and excellent value</li>
<li><strong>Extended Stay:</strong> Ghaya Grand, Mövenpick Apartments — full kitchens and living spaces</li>
<li><strong>Budget Friendly:</strong> Holiday Inn, Rose Park — clean, modern, and perfectly positioned</li>
<li><strong>Boutique & Unique:</strong> Alserkal Arts Hotel, The Warehouse Hotel — for the Instagram generation</li>
</ul>

<h3>What You Get That Others Don\'t</h3>
<p>Stay in Al Barsha and you get something the tourist-heavy districts can\'t offer: a real neighbourhood. Walk to local restaurants where a full meal costs 30 AED. Visit parks where Dubai families spend their evenings. Experience the everyday life of a city that\'s so much more than its tourist highlights.</p>

<p>The smartest travellers have already discovered Al Barsha. Isn\'t it time you did too?</p>',
            ],
            [
                'title' => 'A First-Timer\'s Guide to Dubai Metro: Al Barsha Edition',
                'slug' => 'first-timers-guide-dubai-metro-al-barsha',
                'category' => 'travel-tips',
                'featured_image_url' => 'https://images.unsplash.com/photo-1581262177000-8c82635a1832?w=1200&q=80',
                'excerpt' => 'Skip the taxis and save a fortune. Here\'s everything you need to know about using the Dubai Metro from Al Barsha — routes, tips, fares, and insider tricks.',
                'tags' => ['metro', 'transport', 'al barsha', 'dubai', 'tips', 'budget'],
                'is_featured' => false,
                'days_ago' => 18,
                'view_count' => 3210,
                'content' => '<h2>Your Ticket to the Whole City</h2>
<p>One of the biggest advantages of staying in Al Barsha is the excellent Metro connectivity. Two stations serve the area, and from them you can reach virtually every major attraction in Dubai. Here\'s your complete guide.</p>

<h3>Your Stations</h3>
<p><strong>Mall of the Emirates Station (Red Line):</strong> Right at the doorstep of most Al Barsha 1 hotels. Direct access to the mall via a climate-controlled walkway.</p>
<p><strong>Sharaf DG Station (Red Line):</strong> Serves the southern part of Al Barsha 1 and some Al Barsha South hotels.</p>
<p><strong>Dubai Internet City / Media City Stations (Red Line):</strong> For Barsha Heights hotels.</p>

<h3>Where You Can Go</h3>
<ul>
<li><strong>Burj Khalifa/Dubai Mall:</strong> 15 minutes → Burj Khalifa/Dubai Mall Station</li>
<li><strong>Dubai Marina/JBR:</strong> 10 minutes → DMCC Station</li>
<li><strong>Gold Souk/Spice Souk:</strong> 30 minutes → Al Ras Station</li>
<li><strong>Dubai Frame:</strong> 20 minutes → Al Jafiliya Station</li>
<li><strong>DIFC:</strong> 12 minutes → Financial Centre Station</li>
</ul>

<h3>Nol Card Essentials</h3>
<p>You\'ll need a Nol card to ride. Buy a Silver Nol card (25 AED, includes 19 AED credit) from any Metro station ticket machine. Tap in and tap out — fares are zone-based and range from 3-8.50 AED per trip.</p>

<h3>Pro Tips</h3>
<ul>
<li>The Gold Class cabin (front car) costs double but is spacious and comfortable</li>
<li>There\'s a dedicated Women & Children cabin — look for the pink signs</li>
<li>No eating, drinking, or chewing gum on the Metro — fines are steep</li>
<li>Rush hours (7-9 AM, 5-7 PM) can be very crowded on the Red Line</li>
<li>The Metro runs from 5 AM to midnight (Saturday-Wednesday), extended to 1 AM on Thursday, and 10 AM to 1 AM on Friday</li>
</ul>

<h3>Combining Metro with Other Transport</h3>
<p>The Metro connects seamlessly with buses, trams (at DMCC), and the Palm Monorail (at Nakheel). For areas not covered by Metro, taxis from any station are easy to find and use the same Nol card.</p>',
            ],
            [
                'title' => 'From Art Galleries to Ski Slopes: 48 Hours in Al Barsha',
                'slug' => '48-hours-in-al-barsha-art-galleries-ski-slopes',
                'category' => 'things-to-do',
                'featured_image_url' => 'https://images.unsplash.com/photo-1597659840241-37e2b9c2f55f?w=1200&q=80',
                'excerpt' => 'Think Al Barsha is just a place to sleep? Think again. Here\'s how to pack 48 unforgettable hours into Dubai\'s most underrated neighbourhood.',
                'tags' => ['itinerary', 'al barsha', '48 hours', 'activities', 'dubai'],
                'is_featured' => false,
                'days_ago' => 22,
                'view_count' => 2870,
                'content' => '<h2>48 Hours You Won\'t Forget</h2>
<p>Most visitors use Al Barsha as a base and head elsewhere for entertainment. But with 48 hours dedicated to the neighbourhood itself, you\'ll discover a Dubai that most tourists miss entirely.</p>

<h3>Day 1 — Morning: Art & Coffee</h3>
<p>Start at Alserkal Avenue in Al Quoz (a 10-minute drive from most Al Barsha hotels). This converted warehouse district is home to world-class contemporary art galleries, including Leila Heller Gallery and Green Art Gallery. Fuel up with specialty coffee at Nightjar Coffee Roasters.</p>

<h3>Day 1 — Afternoon: Mall of the Emirates</h3>
<p>Head to Mall of the Emirates, but skip the shopping (for now). Instead, hit Ski Dubai for a couple of hours on the slopes — yes, you\'re skiing indoors in the desert. Follow it up with a leisurely lunch at one of the mall\'s 100+ restaurants.</p>

<h3>Day 1 — Evening: Rooftop Vibes</h3>
<p>Clean up at your hotel and head to QD\'s rooftop at Media One Hotel in Barsha Heights. The sunset views, live music, and cocktails make it one of the best Thursday-night spots in Dubai. For dinner, walk to Toshi at the Grand Millennium for exceptional Japanese cuisine.</p>

<h3>Day 2 — Morning: Nature & Gardens</h3>
<p>Start early at Dubai Miracle Garden (open November-May), a short drive from Al Barsha. Over 150 million flowers in jaw-dropping formations — it\'s one of Dubai\'s most photogenic spots. Next door, Dubai Butterfly Garden offers a serene, enclosed tropical experience.</p>

<h3>Day 2 — Afternoon: Local Flavours</h3>
<p>Return to Al Barsha for an authentic local lunch. Try the legendary Ravi Restaurant for Pakistani cuisine, then explore the neighbourhood\'s back streets on foot. Visit the local fruit and vegetable market, browse the textile shops, and soak in the multicultural atmosphere.</p>

<h3>Day 2 — Evening: Sunset & Shopping</h3>
<p>Take the Metro to Kite Beach for sunset (just 15 minutes away), then return to Mall of the Emirates for an evening of shopping. End with dinner at Aspen by Kempinski — their fondue is the perfect way to close your Al Barsha adventure.</p>',
            ],
            [
                'title' => 'Business Traveller\'s Guide to TECOM & Media City Hotels',
                'slug' => 'business-travellers-guide-tecom-media-city-hotels',
                'category' => 'travel-guides',
                'featured_image_url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&q=80',
                'excerpt' => 'Flying into Dubai for business? Barsha Heights (TECOM) puts you right where the action is. Here\'s everything corporate travellers need to know about staying in Dubai\'s tech and media hub.',
                'tags' => ['business', 'tecom', 'media city', 'corporate', 'hotels', 'dubai'],
                'is_featured' => false,
                'days_ago' => 26,
                'view_count' => 2540,
                'content' => '<h2>The Business Traveller\'s Base Camp</h2>
<p>If your Dubai trip involves meetings at Dubai Internet City, Media City, Knowledge Village, or any of the TECOM free zones, Barsha Heights is where you want to be. Here\'s your complete guide to making the most of a business stay.</p>

<h3>Why Barsha Heights?</h3>
<p>Barsha Heights (formerly TECOM) is home to three of Dubai\'s most important free zones: Dubai Internet City (tech companies), Dubai Media City (media and advertising), and Knowledge Village (education). Major corporations including Microsoft, Google, CNN, MBC, and LinkedIn have their regional offices here.</p>

<h3>The Hotels</h3>
<p><strong>Grand Millennium Dubai (5-star):</strong> The premium choice with executive club lounges, multiple meeting rooms, and Toshi restaurant for client dinners. Walking distance to Internet City.</p>
<p><strong>Media One Hotel (4-star):</strong> The social hub of Barsha Heights. Great for networking, with a trendy vibe that appeals to the creative industries. Direct Metro access.</p>
<p><strong>Mövenpick Apartments (4-star):</strong> Ideal for extended business stays. Full kitchen, washer/dryer, and a homely feel that keeps you sane during long projects.</p>

<h3>Getting to Meetings</h3>
<ul>
<li><strong>Internet City Metro Station:</strong> Serves most Barsha Heights hotels</li>
<li><strong>Walking:</strong> Most free zone offices are within 10-15 minutes on foot</li>
<li><strong>Taxi:</strong> Readily available, 15-25 AED within the TECOM area</li>
<li><strong>Dubai Marina:</strong> 5 minutes by car for waterfront client dinners</li>
</ul>

<h3>After-Work Options</h3>
<p>Barsha Heights has a lively after-work scene. QD\'s at Media One is the go-to spot, but there are dozens of restaurants, cafés, and lounges along the main strip. For a change of scene, Dubai Marina is a 5-minute taxi ride away.</p>

<h3>Practical Tips</h3>
<ul>
<li>Hotels offer day-use meeting rooms — book in advance for the best rates</li>
<li>Most properties have business centres with printing and scanning</li>
<li>Airport transfer: 35-45 minutes from DXB, consider Careem/Uber for the best rates</li>
<li>Weekend is Friday-Saturday; many offices are closed but hotels have special rates</li>
</ul>',
            ],
            [
                'title' => 'Hidden Cafés and Street Food in Al Quoz',
                'slug' => 'hidden-cafes-street-food-al-quoz',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1200&q=80',
                'excerpt' => 'Al Quoz\'s industrial warehouses hide some of Dubai\'s coolest cafés and most authentic street food. Here\'s your insider guide to eating in the art district.',
                'tags' => ['al quoz', 'cafes', 'street food', 'alserkal', 'dubai', 'hidden gems'],
                'is_featured' => false,
                'days_ago' => 30,
                'view_count' => 2150,
                'content' => '<h2>Where Creativity Meets Cuisine</h2>
<p>Al Quoz is Dubai\'s most unexpected foodie destination. Behind the industrial facades and between the art galleries, you\'ll find a café culture that\'s completely unlike anything else in the city. No marble lobbies, no dress codes — just great coffee, honest food, and a creative buzz.</p>

<h3>The Alserkal Avenue Scene</h3>

<h4>Nightjar Coffee Roasters</h4>
<p>Dubai\'s specialty coffee pioneers roast their beans right here. The single-origin pour-overs are exceptional, and the minimalist industrial space — all concrete and steel — is the perfect backdrop for a morning brew. Try the cold brew in summer.</p>

<h4>A4 Space Café</h4>
<p>Part of the A4 Space community hub, this café serves healthy, creative dishes in a space that doubles as a co-working area. The avocado toast is elevated, and the fresh juices are made to order. You\'ll sit alongside artists, writers, and entrepreneurs.</p>

<h4>Wild & The Moon</h4>
<p>A plant-based café that proves healthy eating doesn\'t have to be boring. Cold-pressed juices, raw desserts, and nourishing bowls in a beautifully designed space. The almond milk latte is life-changing.</p>

<h3>Beyond Alserkal</h3>

<h4>The Industrial Cafeterias</h4>
<p>Venture into the industrial streets of Al Quoz and you\'ll find dozens of small cafeterias serving the workers who keep Dubai running. The food is incredible — freshly made South Indian dosas for 5 AED, Yemeni bread with honey, Pakistani parathas stuffed with potato. It\'s some of the most authentic food in Dubai, at prices you won\'t believe.</p>

<h4>Times Square Center Food Court</h4>
<p>This unassuming mall on the edge of Al Quoz has a food court that locals love. Filipino, Indian, Arabic, and Chinese cuisines all represented, with most meals under 25 AED.</p>

<h3>Our Recommendation</h3>
<p>Combine a café visit at Alserkal with a street food lunch in the industrial area. You\'ll experience two completely different sides of Al Quoz — and both are unforgettable. Just bring cash for the street food spots.</p>',
            ],
            [
                'title' => 'Family-Friendly Hotels Near Mall of the Emirates',
                'slug' => 'family-friendly-hotels-near-mall-of-the-emirates',
                'category' => 'luxury-stays',
                'featured_image_url' => 'https://images.unsplash.com/photo-1602002418816-5c0aeef426aa?w=1200&q=80',
                'excerpt' => 'Travelling with kids in Dubai? Al Barsha\'s family-friendly hotels near Mall of the Emirates offer the perfect combination of convenience, entertainment, and value.',
                'tags' => ['family', 'kids', 'hotels', 'mall of the emirates', 'al barsha'],
                'is_featured' => false,
                'days_ago' => 34,
                'view_count' => 3450,
                'content' => '<h2>Happy Kids, Happy Holiday</h2>
<p>Choosing the right hotel when travelling with children can make or break a family holiday. Al Barsha\'s proximity to Mall of the Emirates — with its indoor ski slope, entertainment centres, and kid-friendly restaurants — makes it one of the smartest choices for families visiting Dubai.</p>

<h3>Why Mall of the Emirates Is a Family Goldmine</h3>
<p>Having Ski Dubai, Magic Planet, VOX Cinemas, and hundreds of shops within walking distance of your hotel means you\'ll never hear "I\'m bored." On particularly hot days, the mall becomes your climate-controlled playground.</p>

<h3>Top Family Hotels</h3>

<h4>Kempinski Hotel Mall of the Emirates</h4>
<p>Direct mall access means you can pop into Ski Dubai after breakfast. The hotel offers interconnecting rooms, a kids\' menu at all restaurants, and a rooftop pool where children can splash while parents enjoy city views. Babysitting services are available.</p>

<h4>Sheraton Mall of the Emirates</h4>
<p>Another property with direct mall access. The Sheraton\'s family rooms are spacious, and the rooftop pool area is well-designed for families. The breakfast buffet has an excellent children\'s section with pancakes, waffles, and fresh fruit.</p>

<h4>Novotel Al Barsha</h4>
<p>Novotel\'s family-friendly philosophy shines here. Kids under 16 stay free, children\'s meals are complimentary, and the play area keeps little ones entertained. It\'s a short walk to the mall and the Metro station.</p>

<h4>Ghaya Grand Hotel</h4>
<p>For families who need more space, Ghaya Grand\'s apartment-style rooms with full kitchens are a game-changer. Prepare familiar meals for fussy eaters, do laundry on-site, and enjoy the children\'s pool and play area. The nightly rate often works out cheaper than two standard hotel rooms.</p>

<h3>Family Activities Beyond the Mall</h3>
<ul>
<li>Al Barsha Pond Park — free entry, playgrounds, and barbecue areas</li>
<li>Dubai Miracle Garden — 150 million flowers (Nov-May)</li>
<li>Dubai Butterfly Garden — enclosed tropical garden</li>
<li>Kite Beach — free shuttle from most hotels</li>
<li>Desert safari — most hotels can arrange family-friendly trips</li>
</ul>

<p>Pro tip: Book a hotel with a kitchenette. Being able to prepare snacks and simple meals saves money and sanity when travelling with young children.</p>',
            ],
            [
                'title' => 'Weekend Brunch Spots in Barsha Heights',
                'slug' => 'weekend-brunch-spots-barsha-heights',
                'category' => 'food-dining',
                'featured_image_url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80',
                'excerpt' => 'Dubai runs on Friday brunch, and Barsha Heights has some of the best. From lavish hotel buffets to casual rooftop affairs, here are the brunches worth getting out of bed for.',
                'tags' => ['brunch', 'barsha heights', 'weekend', 'food', 'dubai', 'friday'],
                'is_featured' => false,
                'days_ago' => 38,
                'view_count' => 2980,
                'content' => '<h2>Friday Brunch: A Dubai Institution</h2>
<p>In Dubai, Friday brunch isn\'t just a meal — it\'s a lifestyle. And Barsha Heights has quietly become one of the best brunch destinations in the city, with options ranging from extravagant hotel spreads to relaxed rooftop gatherings.</p>

<h3>The Big Hotel Brunches</h3>

<h4>Grand Millennium — Garden Brunch</h4>
<p>The Grand Millennium\'s Friday brunch is a sprawling affair spread across multiple stations. Think sushi bars, live cooking stations, a massive dessert room, and a cheese selection that would make a Parisian weep. Packages start at 299 AED (soft drinks) up to 449 AED (premium beverages). The outdoor garden seating is lovely during winter months.</p>

<h4>Media One Hotel — Pool Deck Brunch</h4>
<p>More relaxed and younger in vibe, Media One\'s poolside brunch combines good food with a party atmosphere. The DJ starts spinning around 2 PM, and by 4 PM it feels more like a pool party than a meal. From 249 AED — excellent value for what you get.</p>

<h3>Casual Brunches</h3>

<h4>Nosh — Mövenpick Apartments</h4>
<p>Mövenpick\'s signature restaurant offers a more intimate brunch experience. Swiss-quality food, a well-curated menu (rather than an overwhelming buffet), and the famous Mövenpick ice cream bar for dessert. Family-friendly and relaxed.</p>

<h4>The Terrace — Sheraton</h4>
<p>If you don\'t mind a short Metro hop, the Sheraton\'s Terrace restaurant offers a solid brunch with international cuisine. The outdoor terrace overlooking Mall of the Emirates adds a nice touch.</p>

<h3>Brunch Tips</h3>
<ul>
<li>Always book in advance — popular brunches sell out, especially in winter</li>
<li>Brunch typically runs 12:30-4 PM on Fridays</li>
<li>Wear comfortable clothes — you\'ll eat more than you planned</li>
<li>Some brunches include pool access — bring swimwear</li>
<li>The "soft drinks" package is usually the best value</li>
<li>Careem or taxi home — don\'t drive if you\'ve had the premium package</li>
</ul>

<h2>Our Pick</h2>
<p>For the full Dubai brunch experience, go Grand Millennium. For a fun, social afternoon, Media One. For a relaxed family brunch, Nosh at Mövenpick. You can\'t go wrong with any of them.</p>',
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
                    'meta_title' => $postData['title'] . ' | Al Barsha Hotels',
                    'meta_description' => $postData['excerpt'],
                ]
            );

            // Attach to domain
            $post->domains()->syncWithoutDetaching([$domain->id]);
        }

        $this->command->info('Seeded ' . count($posts) . ' blog posts with ' . count($categories) . ' categories for al-barsha-hotels domain.');
    }
}
