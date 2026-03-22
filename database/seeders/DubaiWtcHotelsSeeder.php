<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\DomainHeroSlide;
use App\Models\Location;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DubaiWtcHotelsSeeder extends Seeder
{
    private int $counter = 800;

    public function run(): void
    {
        $this->command->info('Seeding Dubai World Trade Centre Hotels domain...');

        $domain = $this->seedDomain();
        $this->seedLogo($domain);
        $this->seedFavicon($domain);
        $this->linkLocations($domain);
        $this->linkHotels($domain);
        $this->seedHeroSlides($domain);
        $this->seedContentPages($domain);
        $this->linkTestimonials($domain);

        $this->command->info('Dubai World Trade Centre Hotels seeded successfully!');
    }

    // ─── Domain ────────────────────────────────────────────────────────

    private function seedDomain(): Domain
    {
        $domain = Domain::updateOrCreate(
            ['slug' => 'dubai-wtc-hotels'],
            [
                'name' => 'Dubai World Trade Centre Hotels',
                'domain' => 'dubaiworldtradecentrehotels.com',
                'is_active' => true,
                'is_primary' => false,
                'default_currency' => 'AED',
                'default_language' => 'en',
                'meta_title' => 'Dubai World Trade Centre Hotels — Business Hotels Near DWTC & DIFC',
                'meta_description' => 'Book business hotels near Dubai World Trade Centre, DIFC, and Sheikh Zayed Road. Premium hotels for conferences, exhibitions, and corporate stays.',
                'meta_keywords' => 'dubai world trade centre hotels, dwtc hotels, difc hotels, sheikh zayed road hotels, business hotels dubai, conference hotels dubai',
                'phone' => '+971 4 329 4400',
                'email' => 'info@dubaiworldtradecentrehotels.com',
                'address' => 'Trade Centre Area, Sheikh Zayed Road, Dubai, UAE',
            ]
        );

        $this->command->info("  Domain created: {$domain->name} (ID: {$domain->id})");

        return $domain;
    }

    // ─── Logo & Favicon ────────────────────────────────────────────────

    private function seedLogo(Domain $domain): void
    {
        Storage::disk('public')->makeDirectory('domains/logos');
        $storagePath = "domains/logos/{$domain->slug}.png";
        $fullPath = Storage::disk('public')->path($storagePath);

        $this->generateLogoPng($fullPath, $domain->name);
        $domain->update(['logo_path' => $storagePath]);
        $this->command->line('  Logo generated');
    }

    private function seedFavicon(Domain $domain): void
    {
        Storage::disk('public')->makeDirectory('domains/favicons');
        $storagePath = "domains/favicons/{$domain->slug}.png";
        $fullPath = Storage::disk('public')->path($storagePath);

        $this->generateFaviconPng($fullPath, $domain->name);
        $domain->update(['favicon_path' => $storagePath]);
        $this->command->line('  Favicon generated');
    }

    // ─── Link Locations ────────────────────────────────────────────────

    private function linkLocations(Domain $domain): void
    {
        $locationSlugs = [
            'trade-centre',
            'difc',
            'sheikh-zayed-road',
            'al-satwa',
        ];

        $locations = Location::whereIn('slug', $locationSlugs)->get();

        foreach ($locations as $i => $location) {
            $domain->locations()->syncWithoutDetaching([
                $location->id => [
                    'is_active' => true,
                    'sort_order' => $i,
                ],
            ]);
        }

        $this->command->line("  Linked {$locations->count()} locations");
    }

    // ─── Link Hotels ───────────────────────────────────────────────────

    private function linkHotels(Domain $domain): void
    {
        $locationIds = $domain->locations()->pluck('locations.id');

        $hotels = \App\Models\Hotel::whereIn('location_id', $locationIds)->get();

        foreach ($hotels as $i => $hotel) {
            $domain->hotels()->syncWithoutDetaching([
                $hotel->id => [
                    'is_active' => true,
                    'sort_order' => $i,
                ],
            ]);
        }

        $this->command->line("  Linked {$hotels->count()} hotels");
    }

    // ─── Hero Slides ───────────────────────────────────────────────────

    private function seedHeroSlides(Domain $domain): void
    {
        if ($domain->heroSlides()->count() > 0) {
            $this->command->line('  Hero slides already exist, skipping');

            return;
        }

        Storage::disk('public')->makeDirectory("domains/{$domain->id}/hero");

        $slides = [
            [
                'title' => 'Stay At',
                'highlight' => 'Dubai\'s Business Heart',
                'subtitle' => 'Premium hotels steps from Dubai World Trade Centre',
                'description' => 'The perfect base for conferences, exhibitions, and corporate events.',
                'keywords' => 'dubai,skyline,business,tower',
            ],
            [
                'title' => 'Experience',
                'highlight' => 'DIFC Luxury',
                'subtitle' => 'Five-star hotels in Dubai\'s financial capital',
                'description' => 'Where business meets luxury in Dubai\'s most prestigious district.',
                'keywords' => 'luxury,hotel,modern,elegant',
            ],
            [
                'title' => 'Connected To',
                'highlight' => 'Everything',
                'subtitle' => 'Metro access, SZR location, and central Dubai convenience',
                'description' => 'Strategic stays on Sheikh Zayed Road with the city at your feet.',
                'keywords' => 'dubai,road,night,city',
            ],
            [
                'title' => 'Elevate Your',
                'highlight' => 'Business Stay',
                'subtitle' => 'Executive lounges, meeting rooms, and impeccable service',
                'description' => 'Hotels designed for the discerning business traveller.',
                'keywords' => 'hotel,business,conference,meeting',
            ],
        ];

        foreach ($slides as $i => $slide) {
            $filename = 'hero-slide-'.($i + 1).'.jpg';
            $storagePath = "domains/{$domain->id}/hero/{$filename}";
            $fullPath = Storage::disk('public')->path($storagePath);

            $downloaded = $this->downloadImage($fullPath, 1920, 800, $slide['keywords']);

            if ($downloaded) {
                DomainHeroSlide::create([
                    'domain_id' => $domain->id,
                    'image_path' => $storagePath,
                    'title' => $slide['title'],
                    'highlight' => $slide['highlight'],
                    'subtitle' => $slide['subtitle'],
                    'description' => $slide['description'],
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->line('  '.count($slides).' hero slides added');
    }

    // ─── Content Pages ─────────────────────────────────────────────────

    private function seedContentPages(Domain $domain): void
    {
        $brandName = $domain->name;
        $domainUrl = "https://{$domain->domain}";

        $domain->update([
            // About Us
            'about_us' => $this->generateAboutUs($brandName),
            'about_us_meta_title' => "About Us | {$brandName}",
            'about_us_meta_description' => "Learn about {$brandName} — your trusted guide to the best business hotels near Dubai World Trade Centre, DIFC, and Sheikh Zayed Road.",
            'about_us_canonical_url' => "{$domainUrl}/about-us",

            // Privacy Policy
            'privacy_policy' => $this->generatePrivacyPolicy($brandName, $domain->domain),
            'privacy_policy_meta_title' => "Privacy Policy | {$brandName}",
            'privacy_policy_meta_description' => "Read the privacy policy of {$brandName}. We are committed to protecting your personal data and ensuring transparency in how we handle your information.",
            'privacy_policy_canonical_url' => "{$domainUrl}/privacy-policy",

            // Terms & Conditions
            'terms_conditions' => $this->generateTermsConditions($brandName, $domain->domain),
            'terms_conditions_meta_title' => "Terms & Conditions | {$brandName}",
            'terms_conditions_meta_description' => "Review the terms and conditions for using {$brandName}. Understand our booking policies, cancellation rules, and your rights as a guest.",
            'terms_conditions_canonical_url' => "{$domainUrl}/terms-conditions",
        ]);

        $this->command->line('  Content pages + SEO meta added');
    }

    // ─── Link Testimonials ─────────────────────────────────────────────

    private function linkTestimonials(Domain $domain): void
    {
        $reviews = Review::where('is_approved', true)
            ->where('rating', '>=', 4)
            ->inRandomOrder()
            ->limit(12)
            ->get();

        foreach ($reviews as $i => $review) {
            $domain->testimonials()->syncWithoutDetaching([
                $review->id => [
                    'sort_order' => $i,
                ],
            ]);
        }

        $this->command->line("  Linked {$reviews->count()} testimonials");
    }

    // ─── Image Generation Helpers ──────────────────────────────────────

    private function generateLogoPng(string $path, string $brandName): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (! extension_loaded('gd')) {
            $this->createMinimalImage($path);

            return;
        }

        $w = 400;
        $h = 120;
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);

        // Charcoal with gold accent brand color
        $iconColor = imagecolorallocate($img, 60, 60, 70);
        $iconSize = 50;
        imagefilledrectangle($img, 10, 35, 10 + $iconSize, 35 + $iconSize, $iconColor);

        $white = imagecolorallocate($img, 255, 255, 255);
        $initial = 'WTC';
        imagestring($img, 5, 18, 52, $initial, $white);

        $textColor = imagecolorallocate($img, 30, 30, 50);
        imagestring($img, 5, 75, 42, 'Dubai WTC', $textColor);
        $lightColor = imagecolorallocate($img, 120, 120, 140);
        imagestring($img, 4, 75, 60, 'Hotels', $lightColor);

        imagepng($img, $path);
        imagedestroy($img);
    }

    private function generateFaviconPng(string $path, string $brandName): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (! extension_loaded('gd')) {
            $this->createMinimalImage($path);

            return;
        }

        $size = 64;
        $img = imagecreatetruecolor($size, $size);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);

        // Charcoal with gold accent background
        $bgColor = imagecolorallocate($img, 60, 60, 70);
        imagefilledrectangle($img, 4, 4, $size - 5, $size - 5, $bgColor);

        $white = imagecolorallocate($img, 255, 255, 255);
        $tw = imagefontwidth(5);
        $th = imagefontheight(5);
        imagestring($img, 5, ($size - $tw) / 2, ($size - $th) / 2, 'W', $white);

        imagepng($img, $path);
        imagedestroy($img);
    }

    private function createMinimalImage(string $path): void
    {
        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        ));
    }

    private function downloadImage(string $fullPath, int $width, int $height, string $keywords): bool
    {
        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $url = "https://loremflickr.com/{$width}/{$height}/{$keywords}?lock=".$this->counter++;

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 20,
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

        // GD fallback
        if (extension_loaded('gd')) {
            $img = imagecreatetruecolor($width, $height);
            $hash = crc32($keywords.$this->counter);
            $r1 = abs($hash) % 80 + 80;
            $g1 = abs($hash >> 8) % 80 + 80;
            $b1 = abs($hash >> 16) % 80 + 100;
            for ($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;
                $color = imagecolorallocate(
                    $img,
                    (int) ($r1 + (180 - $r1) * $ratio),
                    (int) ($g1 + (200 - $g1) * $ratio),
                    (int) ($b1 + (220 - $b1) * $ratio)
                );
                imageline($img, 0, $y, $width, $y, $color);
            }
            imagejpeg($img, $fullPath, 90);
            imagedestroy($img);

            return true;
        }

        return false;
    }

    // ─── Content Generators ────────────────────────────────────────────

    private function generateAboutUs(string $brandName): string
    {
        return <<<HTML
<h2>Welcome to {$brandName}</h2>
<p>At <strong>{$brandName}</strong>, we specialise in connecting business travellers, conference delegates, and corporate guests with the finest hotels near Dubai World Trade Centre, DIFC, and Sheikh Zayed Road. Whether you're attending an exhibition at DWTC, closing a deal in the financial district, or seeking a strategically located base in central Dubai, we curate the ideal stay.</p>

<h3>Our Mission</h3>
<p>Our mission is to make booking a business hotel in Dubai's Trade Centre district effortless and efficient. We handpick every property to ensure it meets the highest standards of quality, connectivity, and guest experience — so you can focus on what matters most: your business.</p>

<h3>What Sets Us Apart</h3>
<ul>
    <li><strong>Business Travel Specialists</strong> — Every hotel in our collection is selected for its proximity to DWTC, DIFC, and key business hubs. We understand the needs of corporate travellers.</li>
    <li><strong>Best Rate Guarantee</strong> — We work directly with hotels to negotiate exclusive corporate rates you won't find elsewhere.</li>
    <li><strong>Local Expertise</strong> — Our Dubai-based team provides insider knowledge on the best locations, meeting facilities, and transport links.</li>
    <li><strong>24/7 Concierge</strong> — From airport transfers to conference registration assistance, our dedicated team is available around the clock.</li>
    <li><strong>Flexible Booking</strong> — Free cancellation on most properties up to 48 hours before check-in, ideal for changing business schedules.</li>
</ul>

<h3>Our Story</h3>
<p>Born from deep experience in Dubai's corporate hospitality sector, {$brandName} was founded by a team of professionals who recognised that business travellers need more than just a room — they need connectivity, convenience, and seamless service. Located in the heart of Dubai's commercial corridor, the Trade Centre area hosts over 500 exhibitions and events annually, welcoming millions of delegates from around the world.</p>
<p>Today we serve thousands of corporate guests each year, helping them find the perfect base — whether it's a five-star tower overlooking DWTC, a boutique retreat in DIFC's Gate Village, or a smart-value hotel steps from the Metro on Sheikh Zayed Road.</p>

<h3>Our Values</h3>
<ul>
    <li><strong>Efficiency</strong> — We respect your time. Fast booking, instant confirmation, hassle-free stays.</li>
    <li><strong>Quality</strong> — We never compromise on the standard of our properties or service.</li>
    <li><strong>Guest-First</strong> — Every decision starts with: "Is this best for our guests?"</li>
    <li><strong>Sustainability</strong> — We partner with eco-conscious hotels and promote responsible tourism.</li>
</ul>

<p>Thank you for choosing <strong>{$brandName}</strong>. Your ideal business stay awaits.</p>
HTML;
    }

    private function generatePrivacyPolicy(string $brandName, string $domainName): string
    {
        return <<<HTML
<h2>Privacy Policy</h2>
<p><strong>Last Updated:</strong> March 1, 2026</p>
<p>At <strong>{$brandName}</strong> ("{$domainName}"), we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your personal information when you visit our website, make a booking, or interact with our services.</p>

<h3>1. Information We Collect</h3>
<h4>Personal Information</h4>
<p>When you make a booking or create an account, we may collect:</p>
<ul>
    <li>Full name, email address, and phone number</li>
    <li>Nationality and passport/ID details (as required by UAE law for hotel check-in)</li>
    <li>Payment card details (processed securely via our payment gateway)</li>
    <li>Booking preferences and special requests</li>
</ul>

<h4>Automatically Collected Information</h4>
<p>When you browse our website, we automatically collect:</p>
<ul>
    <li>IP address, browser type, and device information</li>
    <li>Pages visited, time spent, and referral sources</li>
    <li>Cookies and similar tracking technologies</li>
</ul>

<h3>2. How We Use Your Information</h3>
<p>We use the information we collect to:</p>
<ul>
    <li>Process and manage your bookings</li>
    <li>Communicate booking confirmations, updates, and support responses</li>
    <li>Improve our website, services, and user experience</li>
    <li>Send promotional offers and newsletters (only with your consent)</li>
    <li>Comply with legal obligations under UAE federal law</li>
    <li>Prevent fraud and ensure the security of our platform</li>
</ul>

<h3>3. Information Sharing</h3>
<p>We do not sell your personal information. We may share your data with:</p>
<ul>
    <li><strong>Hotel Partners</strong> — To fulfil your booking and provide requested services</li>
    <li><strong>Payment Processors</strong> — To securely process transactions (Mashreq Bank gateway)</li>
    <li><strong>Analytics Providers</strong> — To understand website usage (Google Analytics)</li>
    <li><strong>Legal Authorities</strong> — When required by UAE law or court order</li>
</ul>

<h3>4. Data Security</h3>
<p>We implement industry-standard security measures including SSL encryption, PCI-DSS compliant payment processing, and regular security audits to protect your information from unauthorized access, alteration, or destruction.</p>

<h3>5. Cookies</h3>
<p>We use cookies to enhance your browsing experience, remember your preferences, and analyse site traffic. You can control cookie settings through your browser preferences. Disabling cookies may limit some features of our website.</p>

<h3>6. Your Rights</h3>
<p>Under the UAE Personal Data Protection Law (Federal Decree-Law No. 45 of 2021), you have the right to:</p>
<ul>
    <li>Access, correct, or delete your personal data</li>
    <li>Withdraw consent for marketing communications at any time</li>
    <li>Request a copy of your data in a portable format</li>
    <li>Lodge a complaint with the UAE Data Office</li>
</ul>

<h3>7. Data Retention</h3>
<p>We retain your personal data for as long as necessary to fulfil the purposes outlined in this policy, comply with legal obligations, and resolve disputes. Booking records are retained for a minimum of 5 years as required by UAE commercial law.</p>

<h3>8. Contact Us</h3>
<p>If you have questions about this Privacy Policy or wish to exercise your data rights, please contact us at:</p>
<p><strong>Email:</strong> privacy@{$domainName}<br>
<strong>Phone:</strong> +971 4 329 4400<br>
<strong>Address:</strong> Trade Centre Area, Sheikh Zayed Road, Dubai, UAE</p>
HTML;
    }

    private function generateTermsConditions(string $brandName, string $domainName): string
    {
        return <<<HTML
<h2>Terms & Conditions</h2>
<p><strong>Last Updated:</strong> March 1, 2026</p>
<p>Welcome to <strong>{$brandName}</strong>. By accessing or using our website ({$domainName}) and services, you agree to be bound by these Terms and Conditions. Please read them carefully before making a booking.</p>

<h3>1. Definitions</h3>
<ul>
    <li><strong>"We/Us/Our"</strong> refers to {$brandName}, the operator of {$domainName}</li>
    <li><strong>"You/Guest"</strong> refers to the person making the booking or using our services</li>
    <li><strong>"Property"</strong> refers to any hotel or accommodation listed on our platform</li>
    <li><strong>"Booking"</strong> refers to a confirmed reservation made through our platform</li>
</ul>

<h3>2. Booking & Payment</h3>
<ul>
    <li>All bookings are subject to availability and confirmation</li>
    <li>Prices are displayed in the currency selected and include VAT (5%) unless otherwise stated</li>
    <li>A tourism dirham fee (AED 7–20 per room per night, depending on property classification) is applied as per Dubai Tourism regulations</li>
    <li>Full payment is required at the time of booking unless stated otherwise</li>
    <li>We accept Visa, Mastercard, and American Express through our secure Mashreq payment gateway</li>
</ul>

<h3>3. Cancellation & Refund Policy</h3>
<ul>
    <li><strong>Free Cancellation:</strong> Most properties offer free cancellation up to 48 hours before the check-in date</li>
    <li><strong>Late Cancellation:</strong> Cancellations within 48 hours of check-in may incur a charge equivalent to one night's stay</li>
    <li><strong>No-Show:</strong> Failure to check in without cancellation will be charged the full booking amount</li>
    <li><strong>Refunds:</strong> Approved refunds are processed within 7–14 business days to the original payment method</li>
    <li>Non-refundable rate bookings cannot be cancelled or modified once confirmed</li>
</ul>

<h3>4. Check-In & Check-Out</h3>
<ul>
    <li>Standard check-in time is 14:00–15:00 (varies by property)</li>
    <li>Standard check-out time is 11:00–12:00 (varies by property)</li>
    <li>Early check-in and late check-out are subject to availability and may incur additional charges</li>
    <li>Valid photo ID (passport for international guests, Emirates ID for residents) is required at check-in as per UAE law</li>
</ul>

<h3>5. Guest Responsibilities</h3>
<ul>
    <li>Guests must comply with property rules, UAE laws, and local regulations</li>
    <li>Guests are responsible for any damage caused to the property during their stay</li>
    <li>Smoking is prohibited in all indoor areas unless a designated smoking room is booked</li>
    <li>Noise must be kept to a minimum between 22:00 and 08:00</li>
    <li>Illegal activities on property premises will result in immediate eviction and reporting to authorities</li>
</ul>

<h3>6. Limitation of Liability</h3>
<p>{$brandName} acts as an intermediary between guests and hotel operators. While we strive to ensure the accuracy of all listings:</p>
<ul>
    <li>We are not liable for any loss, damage, or injury arising from your stay at a property</li>
    <li>We are not responsible for force majeure events (natural disasters, pandemics, government actions)</li>
    <li>Our total liability is limited to the booking amount paid</li>
</ul>

<h3>7. Intellectual Property</h3>
<p>All content on {$domainName}, including text, images, logos, and design, is owned by or licensed to {$brandName} and is protected by UAE and international copyright laws. You may not reproduce, distribute, or create derivative works without our written permission.</p>

<h3>8. Governing Law</h3>
<p>These Terms and Conditions are governed by the laws of the United Arab Emirates. Any disputes arising shall be subject to the exclusive jurisdiction of the courts of Dubai, UAE.</p>

<h3>9. Changes to Terms</h3>
<p>We reserve the right to update these Terms at any time. Changes will be posted on this page with an updated "Last Updated" date. Continued use of our services after changes constitutes acceptance of the revised Terms.</p>

<h3>10. Contact Us</h3>
<p>For questions about these Terms and Conditions:</p>
<p><strong>Email:</strong> legal@{$domainName}<br>
<strong>Phone:</strong> +971 4 329 4400<br>
<strong>Address:</strong> Trade Centre Area, Sheikh Zayed Road, Dubai, UAE</p>
HTML;
    }
}
