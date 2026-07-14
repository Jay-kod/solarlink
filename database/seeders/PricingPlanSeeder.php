<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'id' => 1,
                'name' => 'Starter Plan',
                'price' => 0.00,
                'desc' => 'Best for standard residential arrays requiring direct replacement components and simple bookings.',
                'features' => [
                    'Access to OEM Parts Marketplace',
                    'Emergency Tech Dispatch Booking',
                    'Standard Email Support',
                    'Single Solar System Telemetry'
                ],
                'cta' => 'Get Started Now',
                'href' => '/user',
                'popular' => false
            ],
            [
                'id' => 2,
                'name' => 'Pro Premium',
                'price' => 29.00,
                'desc' => 'Advanced predictive monitoring designed to completely eliminate array breakdown periods.',
                'features' => [
                    'All OEM Marketplace access',
                    'Priority 2-Hour Technician Booking',
                    'Predictive microinverter diagnostics',
                    'Custom alerts (SMS / push notes)',
                    'Stackable modular battery dashboards',
                    'Covers up to 3 individual arrays'
                ],
                'cta' => 'Get Started Now',
                'href' => '/user',
                'popular' => true
            ],
            [
                'id' => 3,
                'name' => 'Enterprise Grid',
                'price' => 149.00,
                'desc' => 'Designed for commercial farms, solar installations, and regional asset managers.',
                'features' => [
                    'API Telemetry feeds & webhooks',
                    'Guaranteed SLA arrival commitments',
                    'Uncapped system profiles',
                    'Automated vendor wholesale matching',
                    'Technician team dispatch logs',
                    'Dedicated regional manager dashboard'
                ],
                'cta' => 'Contact Sales',
                'href' => '/admin',
                'popular' => false
            ]
        ];

        foreach ($plans as $plan) {
            PricingPlan::updateOrCreate(['id' => $plan['id']], $plan);
        }
    }
}
