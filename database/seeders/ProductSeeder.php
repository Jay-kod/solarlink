<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'id' => 1,
                'name' => 'AeroVolt 450W Monocrystalline Panel',
                'category' => 'panels',
                'price' => 289.00,
                'original_price' => 349.00,
                'rating' => 4.80,
                'reviews_count' => 124,
                'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&q=80&w=300',
                'description' => 'Next-generation high-efficiency monocrystalline solar panel designed for maximum power output in low-light environments.',
                'specs' => [
                    'Efficiency' => '22.8%',
                    'Max Power' => '450 Watts',
                    'Cell Type' => 'N-Type Monocrystalline',
                    'Warranty' => '25-Year Linear Power Warranty',
                    'Dimensions' => '1722 x 1134 x 30 mm'
                ],
                'stock' => 45,
                'features' => ['Anti-reflective glass', 'Heavy-duty anodized aluminum frame', 'PID resistant'],
                'is_featured' => true
            ],
            [
                'id' => 2,
                'name' => 'SolarLink Max 10kWh Battery Wall',
                'category' => 'batteries',
                'price' => 3499.00,
                'original_price' => null,
                'rating' => 4.95,
                'reviews_count' => 88,
                'image' => 'https://images.unsplash.com/photo-1620714223084-8fcacc6dfd8d?auto=format&fit=crop&q=80&w=300',
                'description' => 'Premium lithium iron phosphate (LiFePO4) home battery system with built-in smart BMS and auto backup capabilities.',
                'specs' => [
                    'Capacity' => '10.2 kWh Usable',
                    'Battery Type' => 'Lithium Iron Phosphate (LFP)',
                    'Nominal Voltage' => '51.2V',
                    'Cycles' => '6000+ Cycles at 80% DOD',
                    'Smart Integration' => 'Wi-Fi / Bluetooth / LTE'
                ],
                'stock' => 12,
                'features' => ['Intelligent self-heating system', 'Modular stacking up to 40kWh', 'Liquid-cooled thermal management'],
                'is_featured' => true
            ],
            [
                'id' => 3,
                'name' => 'NovaGrid Smart 8kW Hybrid Inverter',
                'category' => 'inverters',
                'price' => 1199.00,
                'original_price' => 1399.00,
                'rating' => 4.70,
                'reviews_count' => 43,
                'image' => 'https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?auto=format&fit=crop&q=80&w=300',
                'description' => 'Advanced hybrid inverter that manages solar input, battery power, and utility grid power simultaneously for optimal efficiency.',
                'specs' => [
                    'Max AC Output' => '8000W',
                    'Input Voltage Range' => '125V - 500V',
                    'Peak Efficiency' => '98.2%',
                    'IP Rating' => 'IP65 Water & Dustproof',
                    'Cooling' => 'Smart Fan Cooling'
                ],
                'stock' => 20,
                'features' => ['App-based monitoring & scheduling', 'Under 10ms microsecond transfer time', 'Dual MPPT trackers'],
                'is_featured' => true
            ],
            [
                'id' => 4,
                'name' => 'MPPT Pro 60A Charge Controller',
                'category' => 'accessories',
                'price' => 149.00,
                'original_price' => null,
                'rating' => 4.65,
                'reviews_count' => 52,
                'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&q=80&w=300',
                'description' => 'Ultra-fast Maximum Power Point Tracking (MPPT) charge controller to optimize system current flow and battery lifecycle.',
                'specs' => [
                    'Max Output Current' => '60 Amps',
                    'Nominal System Voltage' => '12/24/36/48V Auto Detect',
                    'Max PV Input Voltage' => '150V DC',
                    'Conversion Efficiency' => '99.0%'
                ],
                'stock' => 60,
                'features' => ['Multiphase synchronous rectification', 'Full short circuit & thermal protection', 'Backlit LCD interface'],
                'is_featured' => false
            ],
            [
                'id' => 5,
                'name' => 'EcoWire Heavy-Duty 10 AWG Solar Cable (100ft)',
                'category' => 'accessories',
                'price' => 59.00,
                'original_price' => null,
                'rating' => 4.50,
                'reviews_count' => 19,
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&q=80&w=300',
                'description' => 'Weatherproof, UV-resistant dual-core extension cables fitted with pre-assembled MC4 connectors for rapid system linking.',
                'specs' => [
                    'Length' => '100 Feet',
                    'Wire Gauge' => '10 AWG',
                    'Insulation' => 'XLPE Weatherproof',
                    'Temperature Range' => '-40°C to +90°C'
                ],
                'stock' => 150,
                'features' => ['UV & ozone resistant', 'Fitted with standard MC4 solar keys', 'Tinned copper conductors'],
                'is_featured' => false
            ]
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['id' => $product['id']], $product);
        }
    }
}
