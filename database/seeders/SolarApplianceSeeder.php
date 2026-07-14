<?php

namespace Database\Seeders;

use App\Models\SolarAppliance;
use App\Models\User;
use Illuminate\Database\Seeder;

class SolarApplianceSeeder extends Seeder
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

        $appliances = [
            [
                'user_id' => $customer->id,
                'name' => 'Main Roof Solar Array',
                'type' => 'panel',
                'brand' => 'SunPower',
                'model' => 'Maxeon 6',
                'capacity' => '7.04 kW (16x 440W)',
                'install_date' => '2025-04-12',
                'serial_number' => 'SP-MAX6-90234',
                'status' => 'active',
                'notes' => 'Primary residential generation array. High-performance monocrystalline panels mounted on south-facing roof.',
            ],
            [
                'user_id' => $customer->id,
                'name' => 'Main Grid-Tie Inverter',
                'type' => 'inverter',
                'brand' => 'SolarEdge',
                'model' => 'SE7600H-US',
                'capacity' => '7.6 kW',
                'install_date' => '2025-04-12',
                'serial_number' => 'SE-IN-492023',
                'status' => 'active',
                'notes' => 'HD-Wave technology grid-tie inverter. Secured in utility closet and connected to telemetry pipeline.',
            ],
            [
                'user_id' => $customer->id,
                'name' => 'LifePO4 Backup Bank',
                'type' => 'battery',
                'brand' => 'Tesla',
                'model' => 'Powerwall 2',
                'capacity' => '13.5 kWh',
                'install_date' => '2025-05-01',
                'serial_number' => 'TS-PW2-102941',
                'status' => 'active',
                'notes' => 'Residential energy storage buffer. Automatic grid-export netting and backup reserve set to 20%.',
            ]
        ];

        foreach ($appliances as $appliance) {
            SolarAppliance::create($appliance);
        }
    }
}
