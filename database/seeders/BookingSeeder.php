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
        if (!$customer) {
            return;
        }

        // Get technicians by name
        $marcus = User::where('name', 'Marcus Vance')->first();
        $darnell = User::where('name', 'Darnell Washington')->first();
        $elena = User::where('name', 'Elena Rostova')->first();
        $kaito = User::where('name', 'Kaito Tanaka')->first();

        $bookings = [
            [
                'id' => 101,
                'user_id' => $customer->id,
                'technician_profile_id' => $marcus ? $marcus->technicianProfile->id : null,
                'service_type' => 'Annual Solar Health Audit',
                'date' => '2026-06-02',
                'time' => '10:00 AM',
                'status' => 'active',
                'cost' => 120.00,
                'location' => '124 Oakwood Ave, San Francisco, CA',
                'notes' => 'Inverter displaying a flashing red light and efficiency seems slightly reduced.'
            ],
            [
                'id' => 102,
                'user_id' => $customer->id,
                'technician_profile_id' => $darnell ? $darnell->technicianProfile->id : null,
                'service_type' => 'Battery Storage Installation',
                'date' => '2026-05-30',
                'time' => '02:00 PM',
                'status' => 'pending',
                'cost' => 450.00,
                'location' => '588 Horizon Blvd, San Francisco, CA',
                'notes' => 'Integrating 10kWh SolarLink Battery Wall with pre-existing solar system.'
            ],
            [
                'id' => 103,
                'user_id' => $customer->id,
                'technician_profile_id' => $elena ? $elena->technicianProfile->id : null,
                'service_type' => 'Solar Panel Panel Cleaning',
                'date' => '2026-05-24',
                'time' => '09:00 AM',
                'status' => 'completed',
                'cost' => 85.00,
                'location' => '72 Pine St, San Francisco, CA',
                'notes' => 'Heavy dust buildup on panels from recent dry winds.'
            ],
            [
                'id' => 104,
                'user_id' => $customer->id,
                'technician_profile_id' => $kaito ? $kaito->technicianProfile->id : null,
                'service_type' => 'Inverter Replacement',
                'date' => '2026-05-15',
                'time' => '11:30 AM',
                'status' => 'cancelled',
                'cost' => 220.00,
                'location' => '900 Sunset Way, San Francisco, CA',
                'notes' => 'Cancel reason: Decided to upgrade entire system later.'
            ]
        ];

        foreach ($bookings as $booking) {
            Booking::updateOrCreate(['id' => $booking['id']], $booking);
        }
    }
}
