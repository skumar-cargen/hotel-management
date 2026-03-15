<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            AmenitySeeder::class,
            AlBarshaHotelsSeeder::class,
            AlBarshaHotelsDataSeeder::class,
            AlBarshaHotelsBlogSeeder::class,
            AlBarshaHotelsDealsSeeder::class,
            BurjKhalifaHotelsSeeder::class,
            BurjKhalifaHotelsDataSeeder::class,
            BurjKhalifaHotelsBlogSeeder::class,
            BurjKhalifaHotelsDealsSeeder::class,
        ]);
    }
}
