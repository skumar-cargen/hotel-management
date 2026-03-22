<?php

namespace App\Console\Commands;

use App\Models\DomainHeroSlide;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DownloadDemoImages extends Command
{
    protected $signature = 'demo:download-images
        {--locations : Download location images only}
        {--hotels : Download hotel images only}
        {--slides : Download hero slide images only}';

    protected $description = 'Download unique, themed images for all hotels, locations, and hero slides (demo purpose)';

    private int $counter = 1;

    // Dubai-themed keywords for locations
    private array $locationKeywords = [
        'downtown-dubai' => 'dubai,skyline,burj+khalifa',
        'dubai-marina' => 'dubai,marina,yacht',
        'palm-jumeirah' => 'palm,island,beach+resort',
        'jumeirah-beach-residence' => 'beach,resort,ocean',
        'business-bay' => 'dubai,modern,tower',
        'difc' => 'skyscraper,financial,glass+building',
        'dubai-creek' => 'creek,boat,waterfront',
        'jumeirah-beach-road' => 'beach,coastline,sunset',
        'al-barsha-1' => 'dubai,street,city',
        'al-barsha-south' => 'residential,apartment,garden',
        'barsha-heights-tecom' => 'office,tower,modern+building',
        'al-quoz' => 'art,gallery,warehouse',
        'umm-suqeim' => 'beach,burj+al+arab,sunset',
        'al-sufouh' => 'resort,garden,palm+tree',
        'bluewaters-island' => 'island,ferris+wheel,waterfront',
        'jumeirah-lake-towers' => 'lake,tower,reflection',
        'dubai-sports-city' => 'stadium,sports,modern',
        'dubai-silicon-oasis' => 'technology,modern,office',
        'al-qusais' => 'residential,neighborhood,dubai',
        'al-nahda-dubai' => 'apartment,park,residential',
        'muhaisnah' => 'suburban,market,city',
        'al-twar' => 'residential,city,street',
        'al-garhoud' => 'airport,hotel,city',
        'dubai-festival-city' => 'mall,fountain,shopping',
        'al-rashidiya-dubai' => 'metro,residential,city',
        'deira-creek' => 'dhow,traditional,creek',
        'trade-centre' => 'exhibition,convention,tower',
        'sheikh-zayed-road' => 'highway,skyscraper,dubai+skyline',
        'al-satwa' => 'market,traditional,street+food',
        'al-fahidi' => 'heritage,museum,traditional+dubai',
        'meena-bazaar' => 'market,textiles,bazaar',
        'oud-metha' => 'park,hospital,residential',
        'karama' => 'shopping,market,city+life',
        'gold-souk-al-ras' => 'gold,souk,jewelry',
        'al-rigga' => 'hotel,deira,city+center',
        'naif' => 'souk,traditional,spice',
        'port-saeed' => 'port,ship,waterfront',
        'ajman-corniche' => 'corniche,beach,ajman',
        'al-nuaimia' => 'residential,ajman,apartment',
        'al-rashidiya-ajman' => 'ajman,city,modern',
        'al-jurf' => 'coastal,industrial,port',
    ];

    // Hotel image category keywords
    private array $categoryKeywords = [
        'exterior' => 'hotel,building,facade',
        'lobby' => 'lobby,chandelier,marble+floor',
        'rooms' => 'hotel+room,bedroom,luxury+bed',
        'bathroom' => 'bathroom,marble,shower',
        'pool' => 'swimming+pool,resort,palm+tree',
        'restaurant' => 'restaurant,fine+dining,elegant',
        'gym' => 'gym,fitness,workout',
        'spa' => 'spa,massage,wellness',
        'meeting' => 'conference+room,meeting,business',
        'general' => 'hotel+interior,luxury,decor',
    ];

    // Hero slide keywords per domain
    private array $slideKeywords = [
        'dubai,skyline,night+city',
        'luxury,hotel,swimming+pool',
        'beach,sunset,tropical',
        'dubai,marina,waterfront',
        'desert,safari,arabian',
        'restaurant,dining,rooftop',
        'spa,resort,relaxation',
        'burj+khalifa,dubai,cityscape',
        'yacht,ocean,luxury',
        'palm+jumeirah,aerial,island',
        'souk,traditional,lantern',
        'hotel+lobby,grand,architecture',
    ];

    public function handle(): int
    {
        $doAll = ! $this->option('locations') && ! $this->option('hotels') && ! $this->option('slides');

        $this->info('=== Dubai Apartments Demo Image Downloader ===');
        $this->newLine();

        if ($doAll || $this->option('slides')) {
            $this->downloadHeroSlides();
        }

        if ($doAll || $this->option('locations')) {
            $this->downloadLocationImages();
        }

        if ($doAll || $this->option('hotels')) {
            $this->downloadHotelImages();
        }

        $this->newLine();
        $this->info('All demo images downloaded successfully!');

        return 0;
    }

    // ─── Hero Slides ────────────────────────────────────────────────

    private function downloadHeroSlides(): void
    {
        $slides = DomainHeroSlide::all();
        $this->info("Downloading {$slides->count()} hero slide images...");
        $bar = $this->output->createProgressBar($slides->count());
        $bar->start();

        foreach ($slides as $slide) {
            $keywords = $this->slideKeywords[$this->counter % count($this->slideKeywords)];
            $fullPath = Storage::disk('public')->path($slide->image_path);

            $dir = dirname($fullPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $this->downloadImage($fullPath, 1920, 800, $keywords);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Hero slides done!');
        $this->newLine();
    }

    // ─── Location Images ────────────────────────────────────────────

    private function downloadLocationImages(): void
    {
        $locations = Location::all();
        $this->info("Downloading {$locations->count()} location images...");
        $bar = $this->output->createProgressBar($locations->count());
        $bar->start();

        foreach ($locations as $location) {
            $storagePath = "locations/{$location->slug}.jpg";
            $fullPath = Storage::disk('public')->path($storagePath);

            $dir = dirname($fullPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $keywords = $this->locationKeywords[$location->slug] ?? 'dubai,city,modern';
            $this->downloadImage($fullPath, 1200, 800, $keywords);

            $location->update(['image_path' => $storagePath]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Location images done!');
        $this->newLine();
    }

    // ─── Hotel Images ───────────────────────────────────────────────

    private function downloadHotelImages(): void
    {
        $hotels = Hotel::with(['images', 'location'])->get();
        $totalImages = HotelImage::count();
        $this->info("Downloading images for {$hotels->count()} hotels ({$totalImages} total images)...");
        $bar = $this->output->createProgressBar($totalImages);
        $bar->start();

        foreach ($hotels as $hotel) {
            $hotelDir = "hotels/{$hotel->id}";
            $fullDir = Storage::disk('public')->path($hotelDir);
            if (! is_dir($fullDir)) {
                mkdir($fullDir, 0755, true);
            }

            // Build hotel-specific keywords based on its attributes
            $hotelBase = $this->getHotelKeywords($hotel);

            foreach ($hotel->images as $image) {
                $fullPath = Storage::disk('public')->path($image->image_path);

                $dir = dirname($fullPath);
                if (! is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Use category-specific keywords + hotel base
                $catKeywords = $this->categoryKeywords[$image->category] ?? $this->categoryKeywords['general'];

                // Mix hotel keywords with category keywords for variety
                $keywords = $image->is_primary ? $hotelBase : $catKeywords;

                $this->downloadImage($fullPath, 800, 600, $keywords);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Hotel images done!');
        $this->newLine();
    }

    // ─── Download Helper ────────────────────────────────────────────

    private function downloadImage(string $fullPath, int $width, int $height, string $keywords): bool
    {
        $lock = $this->counter++;

        // Primary: loremflickr with themed keywords
        $url = "https://loremflickr.com/{$width}/{$height}/{$keywords}?lock={$lock}";

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 8,
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

            if ($imageData && strlen($imageData) > 2000) {
                file_put_contents($fullPath, $imageData);

                return true;
            }
        } catch (\Exception $e) {
            // Fall through to picsum
        }

        // Fallback: picsum with unique seed
        return $this->downloadFromPicsum($fullPath, $width, $height, $lock);
    }

    private function downloadFromPicsum(string $fullPath, int $width, int $height, int $seed): bool
    {
        $url = "https://picsum.photos/seed/dubai-demo-{$seed}/{$width}/{$height}";

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 8,
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

            if ($imageData && strlen($imageData) > 2000) {
                file_put_contents($fullPath, $imageData);

                return true;
            }
        } catch (\Exception $e) {
            // Fall through to GD
        }

        // Last resort: GD placeholder with gradient
        return $this->generateGdPlaceholder($fullPath, $width, $height);
    }

    private function generateGdPlaceholder(string $fullPath, int $w, int $h): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $img = imagecreatetruecolor($w, $h);
        $hash = crc32('demo-'.$this->counter);
        $r1 = abs($hash) % 60 + 40;
        $g1 = abs($hash >> 8) % 60 + 60;
        $b1 = abs($hash >> 16) % 80 + 100;

        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $color = imagecolorallocate(
                $img,
                (int) ($r1 + (200 - $r1) * $ratio),
                (int) ($g1 + (180 - $g1) * $ratio),
                (int) ($b1 + (240 - $b1) * $ratio)
            );
            imageline($img, 0, $y, $w, $y, $color);
        }

        imagejpeg($img, $fullPath, 90);
        imagedestroy($img);

        return true;
    }

    // ─── Hotel Keywords ─────────────────────────────────────────────

    private function getHotelKeywords(Hotel $hotel): string
    {
        $starRating = $hotel->star_rating ?? 4;
        $hasBeach = $hotel->is_beach_access ?? false;
        $locationSlug = $hotel->location->slug ?? '';

        // Base keywords by location type
        if ($hasBeach) {
            $base = 'beach+hotel,resort,ocean';
        } elseif (str_contains($locationSlug, 'marina')) {
            $base = 'marina+hotel,waterfront,yacht';
        } elseif (str_contains($locationSlug, 'downtown') || str_contains($locationSlug, 'business')) {
            $base = 'city+hotel,skyscraper,modern';
        } elseif (str_contains($locationSlug, 'palm')) {
            $base = 'island+resort,beach,luxury';
        } elseif (str_contains($locationSlug, 'creek') || str_contains($locationSlug, 'deira') || str_contains($locationSlug, 'fahidi') || str_contains($locationSlug, 'souk') || str_contains($locationSlug, 'rigga') || str_contains($locationSlug, 'naif')) {
            $base = 'heritage+hotel,traditional,arabic';
        } elseif (str_contains($locationSlug, 'jumeirah')) {
            $base = 'beach+resort,luxury+hotel,tropical';
        } elseif ($starRating >= 5) {
            $base = 'luxury+hotel,five+star,resort';
        } elseif ($starRating >= 4) {
            $base = 'hotel,modern+building,city';
        } else {
            $base = 'apartment+building,hotel,urban';
        }

        return $base;
    }
}
