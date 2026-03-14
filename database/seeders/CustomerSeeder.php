<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Al Maktoum',
                'email' => 'ahmed@example.com',
                'password' => Hash::make('password'),
                'phone' => '+971501234567',
                'nationality' => 'UAE',
                'google_id' => null,
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subDays(1),
                'last_login_ip' => '192.168.1.100',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'password' => Hash::make('password'),
                'phone' => '+14155551234',
                'nationality' => 'US',
                'google_id' => 'google_112233445566778899',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subHours(3),
                'last_login_ip' => '10.0.0.55',
            ],
            [
                'first_name' => 'Mohammed',
                'last_name' => 'Al Saud',
                'email' => 'mohammed.saud@example.com',
                'password' => null,
                'phone' => '+966501234567',
                'nationality' => 'SAR',
                'google_id' => 'google_998877665544332211',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subDays(5),
                'last_login_ip' => '172.16.0.10',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Smith',
                'email' => 'emily.smith@example.com',
                'password' => Hash::make('password'),
                'phone' => '+447911123456',
                'nationality' => 'GB',
                'google_id' => null,
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subDays(2),
                'last_login_ip' => '192.168.1.200',
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Hassan',
                'email' => 'fatima.hassan@example.com',
                'password' => Hash::make('password'),
                'phone' => '+971559876543',
                'nationality' => 'UAE',
                'google_id' => 'google_aabbccddeeff00112233',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subHours(12),
                'last_login_ip' => '192.168.2.50',
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Williams',
                'email' => 'john.williams@example.com',
                'password' => null,
                'phone' => null,
                'nationality' => 'US',
                'google_id' => 'google_112233aabbcc445566',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subDays(10),
                'last_login_ip' => '10.0.1.100',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'maria.garcia@example.com',
                'password' => Hash::make('password'),
                'phone' => '+34612345678',
                'nationality' => 'ES',
                'google_id' => null,
                'email_verified_at' => now(),
                'is_active' => false,
                'last_login_at' => now()->subDays(30),
                'last_login_ip' => '10.0.2.200',
            ],
            [
                'first_name' => 'Ali',
                'last_name' => 'Reza',
                'email' => 'ali.reza@example.com',
                'password' => Hash::make('password'),
                'phone' => '+971504567890',
                'nationality' => 'IR',
                'google_id' => null,
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => null,
                'last_login_ip' => null,
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Mueller',
                'email' => 'lisa.mueller@example.com',
                'password' => null,
                'phone' => '+4917612345678',
                'nationality' => 'DE',
                'google_id' => 'google_ffaabb1122334455cc',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subDays(3),
                'last_login_ip' => '172.16.1.50',
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Khalil',
                'email' => 'omar.khalil@example.com',
                'password' => Hash::make('password'),
                'phone' => '+20101234567',
                'nationality' => 'EG',
                'google_id' => 'google_00aabb11cc22dd33ee',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now()->subHours(6),
                'last_login_ip' => '192.168.3.75',
            ],
        ];

        foreach ($customers as $data) {
            Customer::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        // Link existing bookings to customers by matching guest_email
        $createdCustomers = Customer::all();
        foreach ($createdCustomers as $customer) {
            Booking::where('guest_email', $customer->email)
                ->whereNull('customer_id')
                ->update(['customer_id' => $customer->id]);
        }
    }
}
