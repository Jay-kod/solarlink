<?php

namespace Database\Factories;

use App\Models\MaintenanceRequest;
use App\Models\SolarAppliance;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceRequestFactory extends Factory
{
    protected $model = MaintenanceRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(['role' => 'customer']),
            'solar_appliance_id' => SolarAppliance::factory(),
            'technician_profile_id' => TechnicianProfile::factory(),
            'fault_type' => 'inverter',
            'priority' => 'high',
            'description' => 'Sample fault report',
            'location' => 'Lagos Island, Lagos',
            'latitude' => 6.4541,
            'longitude' => 3.3947,
            'status' => 'reported',
            'payment_status' => 'unpaid',
            'cost' => 1500,
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:00',
            'technician_notes' => null,
            'customer_notes' => null,
        ];
    }
}
