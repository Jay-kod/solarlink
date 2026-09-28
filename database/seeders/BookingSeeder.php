<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('email', 'customer@solarlink.io')->first();
        $marcus = User::where('email', 'technician@solarlink.io')->first()?->technicianProfile;
        $elena = User::where('email', 'elena@solarlink.io')->first()?->technicianProfile;

        if (!$customer || !$marcus || !$elena) {
            return;
        }

        $bookings = [
            [
                'service_type' => '[Demo] Annual Solar Health Check',
                'technician_profile_id' => $marcus->id,
                'date' => now()->addDays(2)->toDateString(),
                'time' => '09:30 AM',
                'status' => 'active',
                'cost' => 145.00,
                'payment_status' => 'unpaid',
                'location' => '124 Oakwood Ave, San Francisco, CA',
                'notes' => 'Demo booking: inspect the inverter and review recent generation output.',
            ],
            [
                'service_type' => '[Demo] Battery Storage Installation',
                'technician_profile_id' => $elena->id,
                'date' => now()->addDays(5)->toDateString(),
                'time' => '02:00 PM',
                'status' => 'pending',
                'cost' => 450.00,
                'payment_status' => 'unpaid',
                'location' => '588 Horizon Blvd, San Francisco, CA',
                'notes' => 'Demo booking: integrate a 10kWh home battery with the existing array.',
            ],
            [
                'service_type' => '[Demo] Solar Panel Cleaning',
                'technician_profile_id' => $marcus->id,
                'date' => now()->subDays(7)->toDateString(),
                'time' => '09:00 AM',
                'status' => 'completed',
                'cost' => 85.00,
                'payment_status' => 'paid',
                'location' => '72 Pine St, San Francisco, CA',
                'notes' => 'Demo booking: remove dust buildup and check the panel surface.',
            ],
            [
                'service_type' => '[Demo] Inverter Replacement',
                'technician_profile_id' => $elena->id,
                'date' => now()->addDays(1)->toDateString(),
                'time' => '11:30 AM',
                'status' => 'cancelled',
                'cost' => 220.00,
                'payment_status' => 'unpaid',
                'location' => '900 Sunset Way, San Francisco, CA',
                'notes' => 'Demo booking: cancelled before technician dispatch.',
            ]
        ];

        foreach ($bookings as $booking) {
            Booking::firstOrCreate(
                ['user_id' => $customer->id, 'service_type' => $booking['service_type']],
                array_merge($booking, ['user_id' => $customer->id])
            );
        }
    }
}
