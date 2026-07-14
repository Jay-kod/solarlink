<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Customer User
        User::firstOrCreate(
            ['email' => 'customer@solarlink.io'],
            [
                'name' => 'Clara Oswald',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // 2. Seed Technicians
        $technicians = [
            [
                'email' => 'technician@solarlink.io',
                'name' => 'Marcus Vance',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150',
                'cert_name' => 'NABCEP PV Installation Professional',
                'rating' => 4.90,
                'review_count' => 142,
                'distance' => '1.2 miles',
                'skills' => ['Inverter Repair', 'Battery Storage Setup', 'Panel Cleaning'],
                'hourly_rate' => 85.00,
                'status' => 'online',
                'experience' => '6 years exp',
                'lat' => 37.7749000,
                'lng' => -122.4194000,
                'eta' => '12 mins'
            ],
            [
                'email' => 'elena@solarlink.io',
                'name' => 'Elena Rostova',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
                'cert_name' => 'Certified Solar Installer',
                'rating' => 4.80,
                'review_count' => 96,
                'distance' => '2.5 miles',
                'skills' => ['Solar Panel Installation', 'Electrical Wiring', 'System Diagnostics'],
                'hourly_rate' => 95.00,
                'status' => 'online',
                'experience' => '4 years exp',
                'lat' => 37.7833000,
                'lng' => -122.4167000,
                'eta' => '18 mins'
            ],
            [
                'email' => 'darnell@solarlink.io',
                'name' => 'Darnell Washington',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150',
                'cert_name' => 'Tesla Powerwall Certified Specialist',
                'rating' => 4.95,
                'review_count' => 210,
                'distance' => '3.1 miles',
                'skills' => ['Tesla Powerwall Certified', 'Commercial Solar', 'Grid-Tie Systems'],
                'hourly_rate' => 110.00,
                'status' => 'busy',
                'experience' => '8 years exp',
                'lat' => 37.7699000,
                'lng' => -122.4468000,
                'eta' => '25 mins'
            ],
            [
                'email' => 'kaito@solarlink.io',
                'name' => 'Kaito Tanaka',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150',
                'cert_name' => 'Microinverter Operations Certificate',
                'rating' => 4.70,
                'review_count' => 68,
                'distance' => '4.8 miles',
                'skills' => ['Microinverter Setup', 'Roof Leak Proofing', 'Safety Audits'],
                'hourly_rate' => 80.00,
                'status' => 'online',
                'experience' => '3 years exp',
                'lat' => 37.7599000,
                'lng' => -122.4368000,
                'eta' => '32 mins'
            ],
            [
                'email' => 'sophia@solarlink.io',
                'name' => 'Sophia Martinez',
                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150',
                'cert_name' => 'Off-Grid Systems Designer',
                'rating' => 4.85,
                'review_count' => 88,
                'distance' => '5.2 miles',
                'skills' => ['Off-Grid Solar Design', 'Smart Home Integration', 'Maintenance Plans'],
                'hourly_rate' => 90.00,
                'status' => 'offline',
                'experience' => '5 years exp',
                'lat' => 37.7899000,
                'lng' => -122.4068000,
                'eta' => '45 mins'
            ]
        ];

        foreach ($technicians as $techData) {
            $user = User::firstOrCreate(
                ['email' => $techData['email']],
                [
                    'name' => $techData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'technician',
                ]
            );

            if ($user->wasRecentlyCreated || !$user->technicianProfile()->exists()) {
                $user->technicianProfile()->create([
                    'avatar' => $techData['avatar'],
                    'cert_name' => $techData['cert_name'],
                    'rating' => $techData['rating'],
                    'review_count' => $techData['review_count'],
                    'distance' => $techData['distance'],
                    'skills' => $techData['skills'],
                    'status' => $techData['status'],
                    'lat' => $techData['lat'],
                    'lng' => $techData['lng'],
                    'eta' => $techData['eta'],
                    'experience' => $techData['experience'],
                    'hourly_rate' => $techData['hourly_rate'],
                ]);
            }
        }

        // 3. Seed Vendor User + Profile
        $vendor = User::firstOrCreate(
            ['email' => 'vendor@solarlink.io'],
            [
                'name' => 'Sarah Smith',
                'password' => Hash::make('password'),
                'role' => 'vendor',
            ]
        );
        if ($vendor->wasRecentlyCreated || !$vendor->vendorProfile()->exists()) {
            $vendor->vendorProfile()->create([
                'store_name' => 'EcoGrid Wholesale Direct',
                'company_address' => 'Suite 42, Port Industrial, Oakland, CA',
                'vat_number' => 'US-VAT-90249219',
            ]);
        }

        // 4. Seed Admin User
        User::firstOrCreate(
            ['email' => 'admin@solarlink.io'],
            [
                'name' => 'Admin Controller',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 5. Call Other Seeders
        $this->call([
            ProductSeeder::class,
            FaqSeeder::class,
            PricingPlanSeeder::class,
            BookingSeeder::class,
            OrderSeeder::class,
            ChatSeeder::class,
            SolarApplianceSeeder::class,
        ]);
    }
}
