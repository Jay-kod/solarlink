<?php

namespace Database\Factories;

use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TechnicianProfileFactory extends Factory
{
    protected $model = TechnicianProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(['role' => 'technician']),
            'cert_name' => 'Solar Master Certification',
            'experience' => '5',
            'hourly_rate' => 65,
            'cert_file_path' => null,
            'approval_status' => 'pending',
            'verification_status' => 'verified',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
            'service_radius' => 25,
            'skills' => ['inverter', 'battery', 'solar_panel'],
            'rating' => 4.8,
            'review_count' => 10,
        ];
    }
}
