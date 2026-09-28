<?php

namespace Database\Factories;

use App\Models\SolarAppliance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolarApplianceFactory extends Factory
{
    protected $model = SolarAppliance::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'type' => $this->faker->randomElement(['panel', 'inverter', 'battery', 'other']),
            'brand' => $this->faker->company(),
            'model' => $this->faker->bothify('Model-###'),
            'capacity' => '5.5 kW',
            'install_date' => now()->subMonths(rand(3, 24))->toDateString(),
            'serial_number' => $this->faker->bothify('SN-########'),
            'status' => 'active',
            'notes' => 'Test appliance',
        ];
    }
}
