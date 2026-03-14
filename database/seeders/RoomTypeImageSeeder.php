<?php

namespace Database\Seeders;

use App\Models\RoomType;
use App\Models\RoomTypeImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RoomTypeImageSeeder extends Seeder
{
    /**
     * Color palettes per room style — [gradient_start, gradient_end]
     */
    private array $palettes = [
        'bedroom' => [
            [[44, 62, 80], [52, 73, 94]],       // Dark slate
            [[41, 128, 185], [52, 152, 219]],    // Blue
            [[52, 73, 94], [93, 109, 126]],      // Blue-gray
        ],
        'bathroom' => [
            [[127, 140, 141], [149, 165, 166]],  // Silver
            [[52, 73, 94], [93, 109, 126]],      // Blue-gray
            [[41, 128, 185], [52, 152, 219]],    // Blue
        ],
        'living' => [
            [[142, 68, 173], [155, 89, 182]],    // Purple
            [[211, 84, 0], [243, 156, 18]],      // Orange-gold
            [[44, 62, 80], [52, 73, 94]],        // Slate
        ],
        'view' => [
            [[22, 160, 133], [26, 188, 156]],    // Teal
            [[25, 42, 86], [41, 128, 185]],      // Deep blue
            [[39, 174, 96], [46, 204, 113]],     // Green
        ],
        'kitchen' => [
            [[192, 57, 43], [231, 76, 60]],      // Red
            [[211, 84, 0], [243, 156, 18]],      // Orange
            [[127, 140, 141], [149, 165, 166]],  // Silver
        ],
        'balcony' => [
            [[22, 160, 133], [26, 188, 156]],    // Teal
            [[41, 128, 185], [52, 152, 219]],    // Blue
            [[39, 174, 96], [46, 204, 113]],     // Green
        ],
    ];

    private array $categoryLabels = [
        'bedroom' => 'Bedroom',
        'bathroom' => 'Bathroom',
        'living' => 'Living Area',
        'view' => 'Room View',
        'kitchen' => 'Kitchen',
        'balcony' => 'Balcony',
    ];

    public function run(): void
    {
        $roomTypes = RoomType::doesntHave('images')
            ->with('hotel')
            ->get();

        $total = $roomTypes->count();

        if ($total === 0) {
            $this->command->info('All room types already have images. Skipping.');
            return;
        }

        $this->command->info("Generating images for {$total} room types...");

        foreach ($roomTypes as $i => $roomType) {
            $this->generateImagesForRoomType($roomType);

            if (($i + 1) % 50 === 0 || ($i + 1) === $total) {
                $this->command->info("  ... " . ($i + 1) . "/{$total} room types done");
            }
        }

        $this->command->info("Done! Generated images for {$total} room types.");
    }

    private function generateImagesForRoomType(RoomType $roomType): void
    {
        $categories = $this->categoriesForRoom($roomType);
        $hotelId = $roomType->hotel_id;

        $dir = "room-types/{$hotelId}/{$roomType->id}";
        Storage::disk('public')->makeDirectory($dir);

        foreach ($categories as $sort => $category) {
            $palette = $this->pickPalette($category);
            $label = $this->categoryLabels[$category] ?? $category;

            $filename = "{$category}-" . ($sort + 1) . '.jpg';
            $path = "{$dir}/{$filename}";
            $fullPath = Storage::disk('public')->path($path);

            $hotelName = $roomType->hotel->name ?? 'Hotel';
            $roomName = $roomType->name;
            $stars = $roomType->hotel->star_rating ?? 3;
            $price = $roomType->base_price;

            $this->generateImage(
                $fullPath,
                $hotelName,
                $roomName,
                $label,
                $stars,
                $price,
                $palette[0],
                $palette[1],
            );

            RoomTypeImage::create([
                'room_type_id' => $roomType->id,
                'image_path' => $path,
                'alt_text' => "{$roomName} — {$label} at {$hotelName}",
                'is_primary' => $sort === 0,
                'sort_order' => $sort,
            ]);
        }
    }

    private function categoriesForRoom(RoomType $roomType): array
    {
        $name = strtolower($roomType->name);
        $stars = $roomType->hotel->star_rating ?? 3;

        // Every room type gets bedroom
        $cats = ['bedroom'];

        // Second bedroom angle for suites/penthouses
        if (str_contains($name, 'suite') || str_contains($name, 'penthouse') || str_contains($name, 'presidential')) {
            $cats[] = 'living';
        }

        // Bathroom — all rooms
        $cats[] = 'bathroom';

        // View for 4+ stars or rooms with "view" / "sea" / "marina" in name
        if ($stars >= 4 || preg_match('/view|sea|marina|ocean|creek|garden|city/i', $name)) {
            $cats[] = 'view';
        }

        // Kitchen for apartments/studios/family
        if (str_contains($name, 'apartment') || str_contains($name, 'studio') || str_contains($name, 'family') || str_contains($name, 'kitchen')) {
            $cats[] = 'kitchen';
        }

        // Balcony for suites, penthouses, deluxe 4+
        if ($stars >= 4 && (str_contains($name, 'suite') || str_contains($name, 'penthouse') || str_contains($name, 'deluxe'))) {
            $cats[] = 'balcony';
        }

        // Extra bedroom shot if we have < 3 images
        if (count($cats) < 3) {
            $cats[] = 'bedroom';
        }

        // Cap at 5
        return array_slice($cats, 0, 5);
    }

    private function pickPalette(string $category): array
    {
        $options = $this->palettes[$category] ?? $this->palettes['bedroom'];
        return $options[array_rand($options)];
    }

    private function generateImage(string $path, string $hotelName, string $roomName, string $categoryLabel, int $stars, float $price, array $colorStart, array $colorEnd): void
    {
        $w = 1200;
        $h = 800;
        $img = imagecreatetruecolor($w, $h);

        // Gradient background
        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int) ($colorStart[0] + ($colorEnd[0] - $colorStart[0]) * $ratio);
            $g = (int) ($colorStart[1] + ($colorEnd[1] - $colorStart[1]) * $ratio);
            $b = (int) ($colorStart[2] + ($colorEnd[2] - $colorStart[2]) * $ratio);
            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $color);
        }

        // Decorative shapes
        $this->drawDecorations($img, $w, $h, $categoryLabel);

        // Semi-transparent overlay for text area
        $overlay = imagecolorallocatealpha($img, 0, 0, 0, 55);
        imagefilledrectangle($img, 0, (int) ($h * 0.30), $w, (int) ($h * 0.75), $overlay);

        $white = imagecolorallocate($img, 255, 255, 255);
        $lightGray = imagecolorallocate($img, 200, 200, 200);
        $accent = imagecolorallocate($img, 255, 215, 0); // Gold

        // Room name (main title)
        $nameLen = strlen($roomName);
        $font = $nameLen > 30 ? 4 : 5;
        $charW = $font === 5 ? 9 : 8;
        $charH = $font === 5 ? 15 : 14;
        $nameX = (int) (($w - $nameLen * $charW) / 2);
        $nameY = (int) ($h * 0.37);
        imagestring($img, $font, $nameX, $nameY, $roomName, $white);

        // Hotel name (subtitle)
        $hotelLen = strlen($hotelName);
        $hotelX = (int) (($w - $hotelLen * 8) / 2);
        imagestring($img, 4, $hotelX, $nameY + $charH + 10, $hotelName, $lightGray);

        // Stars + Price
        $starStr = str_repeat('*', $stars) . ' ' . $stars . '-Star';
        $priceStr = 'AED ' . number_format($price) . '/night';
        $infoStr = $starStr . '  |  ' . $priceStr;
        $infoX = (int) (($w - strlen($infoStr) * 8) / 2);
        imagestring($img, 4, $infoX, $nameY + $charH + 32, $infoStr, $accent);

        // Category label
        $catX = (int) (($w - strlen($categoryLabel) * 8) / 2);
        imagestring($img, 4, $catX, $nameY + $charH + 55, $categoryLabel, $lightGray);

        // Room details (size + guests)
        $details = '';
        if ($roomType_size = $this->extractSize($roomName)) {
            $details .= $roomType_size;
        }

        // Bottom branding
        $brand = 'Dubai Apartments';
        $brandX = (int) (($w - strlen($brand) * 7) / 2);
        imagestring($img, 3, $brandX, $h - 35, $brand, $lightGray);

        // Top corner: category badge
        $badgeColor = imagecolorallocatealpha($img, 0, 0, 0, 80);
        $badgeLen = strlen($categoryLabel) * 7 + 16;
        imagefilledrectangle($img, $w - $badgeLen - 15, 15, $w - 15, 38, $badgeColor);
        imagestring($img, 2, $w - $badgeLen - 7, 19, $categoryLabel, $white);

        // Top left: room type badge
        $roomBadge = strtoupper(substr($roomName, 0, 20));
        $roomBadgeLen = strlen($roomBadge) * 7 + 16;
        imagefilledrectangle($img, 15, 15, 15 + $roomBadgeLen, 38, $badgeColor);
        imagestring($img, 2, 23, 19, $roomBadge, $white);

        imagejpeg($img, $path, 85);
        imagedestroy($img);
    }

    private function extractSize(string $name): string
    {
        return '';
    }

    private function drawDecorations($img, int $w, int $h, string $cat): void
    {
        $light = imagecolorallocatealpha($img, 255, 255, 255, 110);
        $lighter = imagecolorallocatealpha($img, 255, 255, 255, 118);

        match ($cat) {
            'Bedroom' => $this->drawBedroom($img, $w, $h, $light, $lighter),
            'Bathroom' => $this->drawBathroom($img, $w, $h, $light, $lighter),
            'Living Area' => $this->drawLiving($img, $w, $h, $light, $lighter),
            'Room View' => $this->drawView($img, $w, $h, $light, $lighter),
            'Kitchen' => $this->drawKitchen($img, $w, $h, $light, $lighter),
            'Balcony' => $this->drawBalcony($img, $w, $h, $light, $lighter),
            default => $this->drawGeneric($img, $w, $h, $light, $lighter),
        };
    }

    private function drawBedroom($img, $w, $h, $light, $lighter): void
    {
        // Bed with headboard
        $bx = (int) ($w * 0.25);
        $by = (int) ($h * 0.78);
        imagefilledrectangle($img, $bx, $by - 50, $bx + 500, $by, $light);          // mattress
        imagefilledrectangle($img, $bx + 20, $by - 70, $bx + 140, $by - 45, $lighter); // pillow L
        imagefilledrectangle($img, $bx + 160, $by - 70, $bx + 280, $by - 45, $lighter); // pillow C
        imagefilledrectangle($img, $bx + 300, $by - 70, $bx + 420, $by - 45, $lighter); // pillow R
        imagefilledrectangle($img, $bx - 10, $by - 110, $bx + 510, $by - 85, $light); // headboard

        // Nightstands
        imagefilledrectangle($img, $bx - 60, $by - 50, $bx - 15, $by, $lighter);
        imagefilledrectangle($img, $bx + 515, $by - 50, $bx + 560, $by, $lighter);

        // Lamp circles
        imagefilledellipse($img, $bx - 37, $by - 70, 25, 25, $lighter);
        imagefilledellipse($img, $bx + 537, $by - 70, 25, 25, $lighter);

        // Window
        imagerectangle($img, (int) ($w * 0.75), (int) ($h * 0.08), (int) ($w * 0.92), (int) ($h * 0.28), $light);
        imageline($img, (int) ($w * 0.835), (int) ($h * 0.08), (int) ($w * 0.835), (int) ($h * 0.28), $light);
        imageline($img, (int) ($w * 0.75), (int) ($h * 0.18), (int) ($w * 0.92), (int) ($h * 0.18), $light);
    }

    private function drawBathroom($img, $w, $h, $light, $lighter): void
    {
        // Bathtub
        $bx = (int) ($w * 0.15);
        $by = (int) ($h * 0.72);
        imagefilledrectangle($img, $bx, $by - 40, $bx + 350, $by + 20, $light);
        imagerectangle($img, $bx + 5, $by - 35, $bx + 345, $by + 15, $lighter);

        // Shower head
        imagefilledellipse($img, $bx + 320, $by - 80, 40, 40, $lighter);
        imageline($img, $bx + 320, $by - 60, $bx + 320, $by - 40, $light);

        // Mirror (circle)
        imageellipse($img, (int) ($w * 0.7), (int) ($h * 0.2), 120, 120, $light);
        imageellipse($img, (int) ($w * 0.7), (int) ($h * 0.2), 115, 115, $lighter);

        // Vanity
        imagefilledrectangle($img, (int) ($w * 0.6), (int) ($h * 0.5), (int) ($w * 0.85), (int) ($h * 0.55), $light);
        imagefilledrectangle($img, (int) ($w * 0.62), (int) ($h * 0.55), (int) ($w * 0.68), (int) ($h * 0.78), $lighter);
        imagefilledrectangle($img, (int) ($w * 0.77), (int) ($h * 0.55), (int) ($w * 0.83), (int) ($h * 0.78), $lighter);

        // Tile pattern
        for ($tx = 0; $tx < $w; $tx += 80) {
            imageline($img, $tx, 0, $tx, (int) ($h * 0.28), $lighter);
        }
        for ($ty = 0; $ty < (int) ($h * 0.28); $ty += 50) {
            imageline($img, 0, $ty, $w, $ty, $lighter);
        }
    }

    private function drawLiving($img, $w, $h, $light, $lighter): void
    {
        // Sofa
        $sx = (int) ($w * 0.1);
        $sy = (int) ($h * 0.75);
        imagefilledrectangle($img, $sx, $sy - 50, $sx + 450, $sy, $light);         // seat
        imagefilledrectangle($img, $sx - 10, $sy - 90, $sx + 460, $sy - 45, $light); // back
        imagefilledrectangle($img, $sx - 20, $sy - 55, $sx, $sy, $lighter);         // arm L
        imagefilledrectangle($img, $sx + 450, $sy - 55, $sx + 470, $sy, $lighter);  // arm R

        // Cushions
        imagefilledellipse($img, $sx + 100, $sy - 65, 70, 35, $lighter);
        imagefilledellipse($img, $sx + 350, $sy - 65, 70, 35, $lighter);

        // Coffee table
        imagefilledrectangle($img, $sx + 120, $sy + 25, $sx + 330, $sy + 40, $light);

        // TV on wall
        imagefilledrectangle($img, (int) ($w * 0.62), (int) ($h * 0.12), (int) ($w * 0.92), (int) ($h * 0.38), $light);
        imagerectangle($img, (int) ($w * 0.63), (int) ($h * 0.13), (int) ($w * 0.91), (int) ($h * 0.37), $lighter);

        // Floor lamp
        imageline($img, (int) ($w * 0.58), (int) ($h * 0.15), (int) ($w * 0.58), (int) ($h * 0.78), $lighter);
        imagefilledellipse($img, (int) ($w * 0.58), (int) ($h * 0.14), 50, 30, $light);
    }

    private function drawView($img, $w, $h, $light, $lighter): void
    {
        // Window frame
        $wx1 = (int) ($w * 0.1);
        $wy1 = (int) ($h * 0.05);
        $wx2 = (int) ($w * 0.9);
        $wy2 = (int) ($h * 0.65);
        imagerectangle($img, $wx1, $wy1, $wx2, $wy2, $light);
        imageline($img, (int) ($w * 0.5), $wy1, (int) ($w * 0.5), $wy2, $light);

        // Cityscape through window
        $baseY = (int) ($h * 0.5);
        $buildings = [
            [150, $baseY - 120, 50], [230, $baseY - 180, 40], [310, $baseY - 90, 55],
            [400, $baseY - 220, 35], [470, $baseY - 140, 60], [570, $baseY - 250, 30],
            [640, $baseY - 160, 50], [730, $baseY - 200, 45], [820, $baseY - 100, 55],
            [910, $baseY - 170, 40], [990, $baseY - 130, 50],
        ];
        foreach ($buildings as [$x, $y, $bw]) {
            imagefilledrectangle($img, $x, $y, $x + $bw, $baseY + 20, $lighter);
        }

        // Sun/moon
        imagefilledellipse($img, (int) ($w * 0.75), (int) ($h * 0.15), 60, 60, $lighter);

        // Railing at bottom
        imagefilledrectangle($img, $wx1, $wy2, $wx2, $wy2 + 15, $light);
    }

    private function drawKitchen($img, $w, $h, $light, $lighter): void
    {
        // Counter
        $cy = (int) ($h * 0.6);
        imagefilledrectangle($img, (int) ($w * 0.05), $cy, (int) ($w * 0.95), $cy + 12, $light);

        // Cabinets below
        for ($c = 0; $c < 5; $c++) {
            $cx = (int) ($w * 0.08) + $c * (int) ($w * 0.17);
            imagefilledrectangle($img, $cx, $cy + 15, $cx + (int) ($w * 0.14), (int) ($h * 0.88), $lighter);
            // Handle
            imagefilledrectangle($img, $cx + (int) ($w * 0.06), $cy + 30, $cx + (int) ($w * 0.08), $cy + 50, $light);
        }

        // Upper cabinets
        for ($c = 0; $c < 5; $c++) {
            $cx = (int) ($w * 0.08) + $c * (int) ($w * 0.17);
            imagefilledrectangle($img, $cx, (int) ($h * 0.1), $cx + (int) ($w * 0.14), (int) ($h * 0.38), $lighter);
            imagefilledrectangle($img, $cx + (int) ($w * 0.06), (int) ($h * 0.2), $cx + (int) ($w * 0.08), (int) ($h * 0.28), $light);
        }

        // Stove burners
        $sx = (int) ($w * 0.35);
        imagefilledellipse($img, $sx, $cy - 15, 40, 40, $lighter);
        imagefilledellipse($img, $sx + 70, $cy - 15, 40, 40, $lighter);
        imagefilledellipse($img, $sx + 140, $cy - 15, 40, 40, $lighter);

        // Fridge
        imagefilledrectangle($img, (int) ($w * 0.85), (int) ($h * 0.1), (int) ($w * 0.95), $cy, $light);
        imageline($img, (int) ($w * 0.85), (int) ($h * 0.35), (int) ($w * 0.95), (int) ($h * 0.35), $lighter);
    }

    private function drawBalcony($img, $w, $h, $light, $lighter): void
    {
        // Railing
        $ry = (int) ($h * 0.55);
        imagefilledrectangle($img, 0, $ry, $w, $ry + 8, $light);
        // Railing bars
        for ($bx = 30; $bx < $w; $bx += 50) {
            imageline($img, $bx, $ry, $bx, $ry + (int) ($h * 0.15), $light);
        }
        imagefilledrectangle($img, 0, $ry + (int) ($h * 0.15), $w, $ry + (int) ($h * 0.15) + 8, $light);

        // Chairs
        $cx1 = (int) ($w * 0.2);
        $cx2 = (int) ($w * 0.55);
        $cy = (int) ($h * 0.82);
        // Chair 1
        imagefilledrectangle($img, $cx1, $cy - 30, $cx1 + 80, $cy, $lighter);
        imagefilledrectangle($img, $cx1, $cy - 55, $cx1 + 80, $cy - 25, $lighter);
        // Chair 2
        imagefilledrectangle($img, $cx2, $cy - 30, $cx2 + 80, $cy, $lighter);
        imagefilledrectangle($img, $cx2, $cy - 55, $cx2 + 80, $cy - 25, $lighter);
        // Table between
        imagefilledrectangle($img, $cx1 + 100, $cy - 20, $cx2 - 20, $cy - 5, $light);

        // Sky elements — clouds
        for ($i = 0; $i < 4; $i++) {
            $ccx = rand(100, $w - 100);
            $ccy = rand((int) ($h * 0.05), (int) ($h * 0.3));
            imagefilledellipse($img, $ccx, $ccy, rand(80, 140), rand(30, 50), $lighter);
            imagefilledellipse($img, $ccx + 40, $ccy - 5, rand(60, 100), rand(25, 40), $lighter);
        }
    }

    private function drawGeneric($img, $w, $h, $light, $lighter): void
    {
        // Geometric pattern
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
}
