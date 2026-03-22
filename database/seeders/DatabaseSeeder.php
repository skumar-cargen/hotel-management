<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Foundation
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            AmenitySeeder::class,

            // Al Barsha Hotels (thealbarshahotels.com)
            AlBarshaHotelsSeeder::class,
            AlBarshaHotelsDataSeeder::class,
            AlBarshaHotelsBlogSeeder::class,
            AlBarshaHotelsDealsSeeder::class,

            // Burj Khalifa Hotels (burjkhalifahotelsonline.com)
            BurjKhalifaHotelsSeeder::class,
            BurjKhalifaHotelsDataSeeder::class,
            BurjKhalifaHotelsBlogSeeder::class,
            BurjKhalifaHotelsDealsSeeder::class,

            // Jumeira Beach Hotels (jumeirabeachhotels.com)
            JumeiraBeachHotelsSeeder::class,
            JumeiraBeachHotelsDataSeeder::class,

            // Dubai Hotel Resorts (dubaihotelresorts.com)
            DubaiHotelResortsSeeder::class,
            DubaiHotelResortsDataSeeder::class,

            // Dubai Apartments (dubaiapartments.com)
            DubaiApartmentsSeeder::class,
            DubaiApartmentsDataSeeder::class,

            // Al Qusais Dubai Hotels (alqusaisdubaihotels.com)
            AlQusaisDubaiHotelsSeeder::class,
            AlQusaisDubaiHotelsDataSeeder::class,

            // Airport Hotels Dubai (airporthotelsdubai.com)
            AirportHotelsDubaiSeeder::class,
            AirportHotelsDubaiDataSeeder::class,

            // Dubai WTC Hotels (dubaiworldtradecentrehotels.com)
            DubaiWtcHotelsSeeder::class,
            DubaiWtcHotelsDataSeeder::class,

            // Bur Dubai Hotels (burdubaihotels.com)
            BurDubaiHotelsSeeder::class,
            BurDubaiHotelsDataSeeder::class,

            // Deira Dubai Hotels (deiradubaihotels.com)
            DeiraDubaiHotelsSeeder::class,
            DeiraDubaiHotelsDataSeeder::class,

            // Ajman Discount Hotels (ajmandiscounthotels.com)
            AjmanDiscountHotelsSeeder::class,
            AjmanDiscountHotelsDataSeeder::class,
        ]);
    }
}
