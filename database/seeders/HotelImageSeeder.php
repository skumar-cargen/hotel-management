<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HotelImageSeeder extends Seeder
{
    /**
     * Color palettes per category — [gradient_start, gradient_end, accent]
     */
    private array $palettes = [
        'exterior' => [
            [[41, 128, 185], [52, 152, 219]],   // Blue
            [[44, 62, 80], [52, 73, 94]],        // Dark slate
            [[22, 160, 133], [26, 188, 156]],    // Teal
        ],
        'lobby' => [
            [[142, 68, 173], [155, 89, 182]],    // Purple
            [[192, 57, 43], [231, 76, 60]],       // Red
            [[211, 84, 0], [243, 156, 18]],       // Orange-gold
        ],
        'rooms' => [
            [[41, 128, 185], [52, 152, 219]],    // Blue
            [[39, 174, 96], [46, 204, 113]],      // Green
            [[44, 62, 80], [52, 73, 94]],         // Slate
        ],
        'bathroom' => [
            [[127, 140, 141], [149, 165, 166]],   // Silver
            [[52, 73, 94], [93, 109, 126]],       // Blue-gray
        ],
        'pool' => [
            [[41, 128, 185], [52, 152, 219]],    // Blue
            [[22, 160, 133], [26, 188, 156]],     // Teal
            [[25, 42, 86], [41, 128, 185]],       // Deep blue
        ],
        'restaurant' => [
            [[192, 57, 43], [231, 76, 60]],       // Red
            [[211, 84, 0], [243, 156, 18]],       // Orange
            [[142, 68, 173], [155, 89, 182]],     // Purple
        ],
        'gym' => [
            [[44, 62, 80], [52, 73, 94]],         // Dark slate
            [[39, 174, 96], [46, 204, 113]],      // Green
        ],
        'spa' => [
            [[142, 68, 173], [155, 89, 182]],     // Purple
            [[22, 160, 133], [26, 188, 156]],     // Teal
        ],
        'general' => [
            [[41, 128, 185], [52, 152, 219]],
            [[44, 62, 80], [52, 73, 94]],
        ],
    ];

    private array $categoryLabels = [
        'exterior' => 'Exterior View',
        'lobby' => 'Lobby & Reception',
        'rooms' => 'Room Interior',
        'bathroom' => 'Bathroom',
        'pool' => 'Pool & Beach',
        'restaurant' => 'Restaurant & Dining',
        'gym' => 'Gym & Fitness',
        'spa' => 'Spa & Wellness',
        'general' => 'General View',
    ];

    public function run(): void
    {
        // Only seed hotels that don't have images
        $hotels = Hotel::doesntHave('images')->get();
        $total = $hotels->count();

        if ($total === 0) {
            $this->command->info('All hotels already have images. Skipping.');
            return;
        }

        $this->command->info("Generating images for {$total} hotels...");

        foreach ($hotels as $i => $hotel) {
            $this->generateImagesForHotel($hotel);

            if (($i + 1) % 20 === 0 || ($i + 1) === $total) {
                $this->command->info("  ... {$i}/{$total} hotels done");
            }
        }

        $this->command->info("Done! Generated images for {$total} hotels.");
    }

    private function generateImagesForHotel(Hotel $hotel): void
    {
        // Determine which categories to create based on hotel features
        $categories = $this->categoriesForHotel($hotel);

        $dir = "hotels/{$hotel->id}";
        Storage::disk('public')->makeDirectory($dir);

        foreach ($categories as $sort => $category) {
            $palette = $this->pickPalette($category);
            $label = $this->categoryLabels[$category] ?? $category;

            $filename = "{$category}-" . ($sort + 1) . '.jpg';
            $path = "{$dir}/{$filename}";
            $fullPath = Storage::disk('public')->path($path);

            $this->generateImage(
                $fullPath,
                $hotel->name,
                $label,
                $hotel->star_rating,
                $palette[0],
                $palette[1],
            );

            HotelImage::create([
                'hotel_id' => $hotel->id,
                'category' => $category,
                'image_path' => $path,
                'alt_text' => "{$hotel->name} — {$label}",
                'caption' => $this->generateCaption($hotel, $category),
                'is_primary' => $sort === 0,
                'sort_order' => $sort,
            ]);
        }
    }

    private function categoriesForHotel(Hotel $hotel): array
    {
        // Every hotel gets exterior + rooms
        $cats = ['exterior', 'rooms'];

        // Add lobby for 3+ stars
        if ($hotel->star_rating >= 3) {
            $cats[] = 'lobby';
        }

        // Add extra room shot for 4+ stars
        if ($hotel->star_rating >= 4) {
            $cats[] = 'rooms';
        }

        // Pool if hotel has pool amenity or beach
        if ($hotel->is_beach_access || $hotel->amenities()->whereIn('amenities.id', [7, 10])->exists()) {
            $cats[] = 'pool';
        }

        // Restaurant for 4+ stars
        if ($hotel->star_rating >= 4 && rand(0, 1)) {
            $cats[] = 'restaurant';
        }

        // Bathroom for 4+ stars
        if ($hotel->star_rating >= 4) {
            $cats[] = 'bathroom';
        }

        // Spa for 5-stars
        if ($hotel->star_rating >= 5 && $hotel->amenities()->where('amenities.id', 9)->exists()) {
            $cats[] = 'spa';
        }

        // Gym randomly
        if (rand(0, 2) === 0 && $hotel->amenities()->where('amenities.id', 8)->exists()) {
            $cats[] = 'gym';
        }

        // General filler if less than 5
        while (count($cats) < 5) {
            $cats[] = 'general';
        }

        // Cap at 8
        return array_slice($cats, 0, 8);
    }

    private function pickPalette(string $category): array
    {
        $options = $this->palettes[$category] ?? $this->palettes['general'];
        return $options[array_rand($options)];
    }

    private function generateImage(string $path, string $hotelName, string $categoryLabel, int $stars, array $colorStart, array $colorEnd): void
    {
        $w = 1200;
        $h = 800;
        $img = imagecreatetruecolor($w, $h);

        // Draw gradient background
        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int) ($colorStart[0] + ($colorEnd[0] - $colorStart[0]) * $ratio);
            $g = (int) ($colorStart[1] + ($colorEnd[1] - $colorStart[1]) * $ratio);
            $b = (int) ($colorStart[2] + ($colorEnd[2] - $colorStart[2]) * $ratio);
            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $color);
        }

        // Add decorative geometric shapes
        $this->drawDecorations($img, $w, $h, $colorStart, $colorEnd, $categoryLabel);

        // Semi-transparent overlay for text area
        $overlay = imagecolorallocatealpha($img, 0, 0, 0, 60);
        imagefilledrectangle($img, 0, (int) ($h * 0.35), $w, (int) ($h * 0.72), $overlay);

        $white = imagecolorallocate($img, 255, 255, 255);
        $lightGray = imagecolorallocate($img, 200, 200, 200);
        $accent = imagecolorallocate($img, 255, 215, 0); // Gold for stars

        // Hotel name — use built-in font (largest = 5)
        $nameLen = strlen($hotelName);
        $font = $nameLen > 30 ? 4 : 5;
        $charW = $font === 5 ? 9 : 8;
        $charH = $font === 5 ? 15 : 14;
        $nameX = (int) (($w - $nameLen * $charW) / 2);
        $nameY = (int) ($h * 0.44);
        imagestring($img, $font, $nameX, $nameY, $hotelName, $white);

        // Stars
        $starStr = str_repeat('*', $stars) . ' ' . $stars . '-Star';
        $starX = (int) (($w - strlen($starStr) * 8) / 2);
        imagestring($img, 4, $starX, $nameY + $charH + 12, $starStr, $accent);

        // Category label
        $catX = (int) (($w - strlen($categoryLabel) * 8) / 2);
        imagestring($img, 4, $catX, $nameY + $charH + 35, $categoryLabel, $lightGray);

        // Bottom branding
        $brand = 'Dubai Apartments';
        $brandX = (int) (($w - strlen($brand) * 7) / 2);
        imagestring($img, 3, $brandX, $h - 35, $brand, $lightGray);

        // Top corner: category badge
        $badgeColor = imagecolorallocatealpha($img, 0, 0, 0, 80);
        $badgeLen = strlen($categoryLabel) * 7 + 16;
        imagefilledrectangle($img, $w - $badgeLen - 15, 15, $w - 15, 38, $badgeColor);
        imagestring($img, 2, $w - $badgeLen - 7, 19, $categoryLabel, $white);

        imagejpeg($img, $path, 85);
        imagedestroy($img);
    }

    private function drawDecorations($img, int $w, int $h, array $cs, array $ce, string $cat): void
    {
        // Draw category-specific decorative elements
        $light = imagecolorallocatealpha($img, 255, 255, 255, 110);
        $lighter = imagecolorallocatealpha($img, 255, 255, 255, 118);

        match ($cat) {
            'Exterior View' => $this->drawBuilding($img, $w, $h, $light, $lighter),
            'Pool & Beach' => $this->drawWaves($img, $w, $h, $light, $lighter),
            'Room Interior' => $this->drawRoom($img, $w, $h, $light, $lighter),
            'Lobby & Reception' => $this->drawLobby($img, $w, $h, $light, $lighter),
            'Restaurant & Dining' => $this->drawDining($img, $w, $h, $light, $lighter),
            'Gym & Fitness' => $this->drawGym($img, $w, $h, $light, $lighter),
            'Spa & Wellness' => $this->drawSpa($img, $w, $h, $light, $lighter),
            default => $this->drawGeneric($img, $w, $h, $light, $lighter),
        };
    }

    private function drawBuilding($img, $w, $h, $light, $lighter): void
    {
        // Skyline silhouette
        $baseY = (int) ($h * 0.75);
        $buildings = [
            [100, $baseY - 250, 80], [200, $baseY - 180, 60], [300, $baseY - 320, 70],
            [420, $baseY - 200, 90], [550, $baseY - 280, 65], [650, $baseY - 150, 80],
            [770, $baseY - 350, 55], [860, $baseY - 220, 75], [970, $baseY - 190, 85],
            [1080, $baseY - 260, 60],
        ];
        foreach ($buildings as [$x, $y, $bw]) {
            imagefilledrectangle($img, $x, $y, $x + $bw, $baseY, $light);
            // Windows
            for ($wy = $y + 15; $wy < $baseY - 10; $wy += 25) {
                for ($wx = $x + 8; $wx < $x + $bw - 8; $wx += 18) {
                    imagefilledrectangle($img, $wx, $wy, $wx + 8, $wy + 12, $lighter);
                }
            }
        }
        imagefilledrectangle($img, 0, $baseY, $w, $h, $lighter);
    }

    private function drawWaves($img, $w, $h, $light, $lighter): void
    {
        // Wave lines
        for ($wave = 0; $wave < 6; $wave++) {
            $baseY = (int) ($h * 0.6) + $wave * 40;
            for ($x = 0; $x < $w; $x += 2) {
                $y = $baseY + (int) (sin(($x + $wave * 50) / 60.0) * 15);
                imagesetpixel($img, $x, $y, $light);
                imagesetpixel($img, $x, $y + 1, $light);
                imagesetpixel($img, $x + 1, $y, $light);
            }
        }
        // Sun circle
        imagefilledellipse($img, (int) ($w * 0.8), (int) ($h * 0.2), 100, 100, $lighter);
    }

    private function drawRoom($img, $w, $h, $light, $lighter): void
    {
        // Bed shape
        $bx = (int) ($w * 0.2);
        $by = (int) ($h * 0.75);
        imagefilledrectangle($img, $bx, $by - 60, $bx + 400, $by, $light); // mattress
        imagefilledrectangle($img, $bx, $by - 80, $bx + 100, $by - 55, $lighter); // pillow 1
        imagefilledrectangle($img, $bx + 110, $by - 80, $bx + 210, $by - 55, $lighter); // pillow 2
        imagefilledrectangle($img, $bx - 10, $by - 100, $bx + 410, $by - 85, $light); // headboard

        // Window
        imagerectangle($img, (int) ($w * 0.7), (int) ($h * 0.12), (int) ($w * 0.9), (int) ($h * 0.45), $light);
        imageline($img, (int) ($w * 0.8), (int) ($h * 0.12), (int) ($w * 0.8), (int) ($h * 0.45), $light);
    }

    private function drawLobby($img, $w, $h, $light, $lighter): void
    {
        // Chandelier
        $cx = (int) ($w / 2);
        imagefilledellipse($img, $cx, (int) ($h * 0.15), 180, 80, $lighter);
        imageline($img, $cx, 0, $cx, (int) ($h * 0.11), $light);
        // Pillars
        for ($i = 0; $i < 4; $i++) {
            $px = 150 + $i * 280;
            imagefilledrectangle($img, $px, (int) ($h * 0.2), $px + 40, (int) ($h * 0.85), $light);
        }
        // Floor
        imagefilledrectangle($img, 0, (int) ($h * 0.85), $w, $h, $lighter);
    }

    private function drawDining($img, $w, $h, $light, $lighter): void
    {
        // Tables
        for ($t = 0; $t < 3; $t++) {
            $tx = 200 + $t * 320;
            $ty = (int) ($h * 0.7);
            imagefilledellipse($img, $tx, $ty, 120, 50, $light);
            // Chairs
            imagefilledellipse($img, $tx - 70, $ty + 10, 35, 35, $lighter);
            imagefilledellipse($img, $tx + 70, $ty + 10, 35, 35, $lighter);
        }
    }

    private function drawGym($img, $w, $h, $light, $lighter): void
    {
        // Dumbbells
        for ($d = 0; $d < 3; $d++) {
            $dx = 250 + $d * 300;
            $dy = (int) ($h * 0.7);
            imagefilledrectangle($img, $dx - 40, $dy - 8, $dx + 40, $dy + 8, $light);
            imagefilledellipse($img, $dx - 45, $dy, 30, 40, $lighter);
            imagefilledellipse($img, $dx + 45, $dy, 30, 40, $lighter);
        }
    }

    private function drawSpa($img, $w, $h, $light, $lighter): void
    {
        // Zen circles
        $cx = (int) ($w / 2);
        $cy = (int) ($h * 0.7);
        for ($r = 40; $r < 200; $r += 35) {
            imageellipse($img, $cx, $cy, $r * 2, $r * 2, $light);
        }
        // Lotus petals
        for ($a = 0; $a < 360; $a += 45) {
            $px = $cx + (int) (cos(deg2rad($a)) * 60);
            $py = (int) ($h * 0.2) + (int) (sin(deg2rad($a)) * 30);
            imagefilledellipse($img, $px, $py, 40, 25, $lighter);
        }
    }

    private function drawGeneric($img, $w, $h, $light, $lighter): void
    {
        // Diamond pattern
        for ($i = 0; $i < 8; $i++) {
            $cx = rand(100, $w - 100);
            $cy = rand(100, $h - 100);
            $size = rand(30, 80);
            $points = [
                $cx, $cy - $size,
                $cx + $size, $cy,
                $cx, $cy + $size,
                $cx - $size, $cy,
            ];
            imagepolygon($img, $points, $light);
        }
    }

    private function generateCaption(Hotel $hotel, string $category): string
    {
        return match ($category) {
            'exterior' => "Welcome to {$hotel->name} — stunning architecture and grand entrance",
            'lobby' => "Elegant lobby and reception area at {$hotel->name}",
            'rooms' => "Beautifully appointed room at {$hotel->name}",
            'bathroom' => "Luxurious bathroom with premium fixtures at {$hotel->name}",
            'pool' => $hotel->is_beach_access
                ? "Beach and pool facilities at {$hotel->name}"
                : "Sparkling pool area at {$hotel->name}",
            'restaurant' => "Fine dining experience at {$hotel->name}",
            'gym' => "State-of-the-art fitness centre at {$hotel->name}",
            'spa' => "Relaxing spa and wellness centre at {$hotel->name}",
            default => "Experience luxury at {$hotel->name}",
        };
    }
}
