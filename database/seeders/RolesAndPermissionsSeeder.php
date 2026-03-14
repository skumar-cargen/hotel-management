<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Domain management
            'manage domains',
            'view domains',

            // Location management
            'manage locations',
            'view locations',

            // Hotel management
            'manage hotels',
            'view hotels',

            // Room management
            'manage rooms',
            'view rooms',

            // Pricing
            'manage pricing',
            'view pricing',

            // Bookings
            'manage bookings',
            'view bookings',
            'cancel bookings',
            'refund bookings',

            // Users
            'manage users',
            'view users',

            // Reviews
            'manage reviews',
            'view reviews',

            // Careers
            'manage careers',
            'view careers',

            // Blogs
            'manage blogs',
            'view blogs',

            // Customers
            'manage customers',
            'view customers',

            // Analytics
            'view analytics',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

        $priceManager = Role::firstOrCreate(['name' => 'Price Manager']);
        $priceManager->givePermissionTo([
            'manage pricing', 'view pricing',
            'view hotels', 'view rooms',
            'view bookings',
        ]);

        $contentEditor = Role::firstOrCreate(['name' => 'Content Editor']);
        $contentEditor->givePermissionTo([
            'manage hotels', 'view hotels',
            'manage locations', 'view locations',
            'manage rooms', 'view rooms',
            'manage reviews', 'view reviews',
            'manage careers', 'view careers',
            'manage blogs', 'view blogs',
        ]);

        $seoManager = Role::firstOrCreate(['name' => 'SEO Manager']);
        $seoManager->givePermissionTo([
            'view analytics',
            'view hotels',
            'view locations',
            'view blogs',
        ]);

        $supportStaff = Role::firstOrCreate(['name' => 'Support Staff']);
        $supportStaff->givePermissionTo([
            'view bookings',
            'manage bookings',
            'view hotels',
            'view reviews',
            'view customers',
        ]);

        // Domain Manager — manages hotels/bookings/reviews only for assigned domains
        $domainManager = Role::firstOrCreate(['name' => 'Domain Manager']);
        $domainManager->givePermissionTo([
            'manage hotels', 'view hotels',
            'manage rooms', 'view rooms',
            'manage bookings', 'view bookings',
            'manage reviews', 'view reviews',
            'manage careers', 'view careers',
            'manage blogs', 'view blogs',
            'view pricing',
            'view analytics',
            'view domains',
            'view customers',
        ]);
    }
}
