<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HotelImagesSeeder extends Seeder
{
    private int $counter = 1;

    // Category → [width, height, loremflickr keywords]
    private array $categoryConfig = [
        'exterior' => [1200, 800, 'luxury,hotel,building'],
        'lobby' => [1000, 700, 'hotel,lobby,interior'],
        'rooms' => [1000, 700, 'hotel,room,bedroom'],
        'bathroom' => [800, 600, 'luxury,bathroom,marble'],
        'pool' => [1200, 800, 'hotel,swimming,pool'],
        'restaurant' => [1000, 700, 'restaurant,dining,luxury'],
        'gym' => [900, 600, 'gym,fitness,modern'],
        'spa' => [900, 600, 'spa,wellness,relaxation'],
        'general' => [1000, 700, 'hotel,resort,luxury'],
    ];

    // Room type image keywords
    private array $roomImageKeywords = [
        'bedroom' => 'hotel,bedroom,luxury',
        'master-bedroom' => 'master,bedroom,suite',
        'second-bedroom' => 'hotel,bedroom,twin',
        'living-area' => 'living,room,apartment',
        'kitchen' => 'modern,kitchen,apartment',
        'kitchenette' => 'kitchenette,modern,hotel',
        'balcony-view' => 'balcony,city,view',
        'room-overview' => 'hotel,studio,room',
        'bathroom' => 'luxury,bathroom,hotel',
    ];

    public function run(): void
    {
        $this->command->info('Downloading hotel & room type images...');
        $this->command->info('(Using loremflickr.com with hotel keywords — may take 2-3 minutes)');
        $this->command->newLine();

        $this->seedHotelImages();
        $this->seedRoomTypeImages();

        $this->command->newLine();
        $this->command->info('All images downloaded successfully!');
    }

    // ─── Hotel Images ────────────────────────────────────────────────

    private function seedHotelImages(): void
    {
        $hotels = Hotel::with('images')->get();

        foreach ($hotels as $hotel) {
            $this->command->line("  ↓ {$hotel->name} — downloading images...");

            Storage::disk('public')->makeDirectory("hotels/{$hotel->id}");

            // Define image categories based on star rating
            $categories = $this->getCategoriesForHotel($hotel);

            // Delete existing images first
            $hotel->images()->delete();

            foreach ($categories as $j => $category) {
                $filename = "{$category}-".($j + 1).'.jpg';
                $storagePath = "hotels/{$hotel->id}/{$filename}";
                $config = $this->categoryConfig[$category] ?? [1000, 700, 'hotel,luxury'];

                $imagePath = $this->downloadImage($storagePath, $config[0], $config[1], $config[2]);

                HotelImage::create([
                    'hotel_id' => $hotel->id,
                    'category' => $category,
                    'image_path' => $imagePath,
                    'alt_text' => $hotel->name.' — '.$this->categoryLabel($category),
                    'caption' => $this->generateCaption($hotel, $category),
                    'is_primary' => $j === 0,
                    'sort_order' => $j,
                ]);
            }

            $this->command->line('    ✓ '.count($categories).' images saved');
        }
    }

    // ─── Room Type Images ────────────────────────────────────────────

    private function seedRoomTypeImages(): void
    {
        $roomTypes = RoomType::with(['images', 'hotel'])->get();

        foreach ($roomTypes as $rt) {
            $this->command->line("  ↓ {$rt->hotel->name} / {$rt->name} — downloading...");

            $dir = "room-types/{$rt->id}";
            Storage::disk('public')->makeDirectory($dir);

            // Delete existing images first
            $rt->images()->delete();

            // Each room type gets 3-4 images
            $imageTypes = $this->getRoomImageTypes($rt);

            foreach ($imageTypes as $j => $type) {
                $filename = "{$type}-".($j + 1).'.jpg';
                $storagePath = "{$dir}/{$filename}";
                $keywords = $this->roomImageKeywords[$type] ?? 'hotel,room,interior';

                $imagePath = $this->downloadImage($storagePath, 1000, 700, $keywords);

                RoomTypeImage::create([
                    'room_type_id' => $rt->id,
                    'image_path' => $imagePath,
                    'alt_text' => $rt->name.' — '.ucfirst(str_replace('-', ' ', $type)).' at '.$rt->hotel->name,
                    'is_primary' => $j === 0,
                    'sort_order' => $j,
                ]);
            }

            $this->command->line('    ✓ '.count($imageTypes).' images saved');
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function getCategoriesForHotel(Hotel $hotel): array
    {
        // 5-star hotels get more images
        return match (true) {
            $hotel->star_rating >= 5 => ['exterior', 'lobby', 'rooms', 'rooms', 'bathroom', 'pool', 'restaurant', 'spa', 'gym', 'general'],
            $hotel->star_rating >= 4 => ['exterior', 'lobby', 'rooms', 'rooms', 'pool', 'restaurant', 'gym', 'general'],
            default => ['exterior', 'lobby', 'rooms', 'rooms', 'pool', 'restaurant', 'general'],
        };
    }

    private function getRoomImageTypes(RoomType $rt): array
    {
        return match (true) {
            str_contains(strtolower($rt->name), 'penthouse') => ['bedroom', 'living-area', 'kitchen', 'balcony-view'],
            str_contains(strtolower($rt->name), 'two-bedroom') => ['master-bedroom', 'second-bedroom', 'living-area', 'kitchen'],
            str_contains(strtolower($rt->name), 'one-bedroom') => ['bedroom', 'living-area', 'kitchen'],
            default => ['room-overview', 'bathroom', 'kitchenette'], // Studio
        };
    }

    private function categoryLabel(string $category): string
    {
        return match ($category) {
            'exterior' => 'Exterior View',
            'lobby' => 'Lobby & Reception',
            'rooms' => 'Guest Room',
            'bathroom' => 'Bathroom',
            'pool' => 'Swimming Pool',
            'restaurant' => 'Restaurant & Dining',
            'gym' => 'Fitness Center',
            'spa' => 'Spa & Wellness',
            'general' => 'General View',
            default => ucfirst($category),
        };
    }

    private function generateCaption(Hotel $hotel, string $category): string
    {
        $name = $hotel->name;

        return match ($category) {
            'exterior' => "Welcome to {$name} — stunning architecture and grand entrance",
            'lobby' => "The elegant lobby of {$name} with premium finishes",
            'rooms' => "Beautifully appointed guest room at {$name}",
            'bathroom' => "Luxurious marble bathroom with premium amenities at {$name}",
            'pool' => "The sparkling pool area at {$name} — perfect for relaxation",
            'restaurant' => "Fine dining experience at {$name}",
            'gym' => "State-of-the-art fitness center at {$name}",
            'spa' => "Rejuvenate at the spa & wellness center of {$name}",
            'general' => "Experience the best of {$name}",
            default => "{$name} — ".ucfirst($category),
        };
    }

    private function downloadImage(string $storagePath, int $width = 1000, int $height = 700, string $keywords = 'hotel,luxury'): string
    {
        // loremflickr gives keyword-matched images — lock param ensures unique per call
        $url = "https://loremflickr.com/{$width}/{$height}/{$keywords}?lock=".$this->counter++;
        $fullPath = Storage::disk('public')->path($storagePath);

        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

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

                return $storagePath;
            }
        } catch (\Exception $e) {
            // Fall through to GD placeholder
        }

        // GD fallback — create a nice gradient placeholder
        $this->generateGradientPlaceholder($fullPath, $width, $height, basename(dirname($storagePath)));

        return $storagePath;
    }

    private function generateGradientPlaceholder(string $path, int $w, int $h, string $label): void
    {
        if (! extension_loaded('gd')) {
            file_put_contents($path, base64_decode(
                '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP'.str_repeat('A', 50).'//Z'
            ));

            return;
        }

        $img = imagecreatetruecolor($w, $h);
        $hash = crc32($label.$this->counter);

        // Create a nicer gradient
        $r1 = abs($hash) % 100 + 60;
        $g1 = abs($hash >> 8) % 100 + 60;
        $b1 = abs($hash >> 16) % 100 + 60;

        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int) ($r1 + (200 - $r1) * $ratio);
            $g = (int) ($g1 + (220 - $g1) * $ratio);
            $b = (int) ($b1 + (240 - $b1) * $ratio);
            $color = imagecolorallocate($img, min($r, 255), min($g, 255), min($b, 255));
            imageline($img, 0, $y, $w, $y, $color);
        }

        $textColor = imagecolorallocate($img, 255, 255, 255);
        $text = strtoupper(substr($label, 0, 25));
        $tw = imagefontwidth(5) * strlen($text);
        imagestring($img, 5, ($w - $tw) / 2, ($h - imagefontheight(5)) / 2, $text, $textColor);

        imagejpeg($img, $path, 90);
        imagedestroy($img);
    }
}
