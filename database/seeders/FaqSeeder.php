<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Technical & Telemetry',
                'question' => 'How does the predictive AI diagnostic tool track outages?',
                'answer' => 'By pulling streaming metrics (V, I, Temp) from connected solar panels, our machine-learning health models compare generation curves against localized weather projections to spot microinverter degradations immediately.',
                'order' => 1
            ],
            [
                'category' => 'Technical & Telemetry',
                'question' => 'What types of inverters are supported by SolarLink?',
                'answer' => 'We support all major hybrid and microinverter models, including Enphase, Tesla, SMA, and Sol-Ark, through our open telemetry API structures.',
                'order' => 2
            ],
            [
                'category' => 'Marketplace & Orders',
                'question' => 'How does direct factory parts procurement work?',
                'answer' => 'We bypass regional logistics hubs by listing replacement units direct from certified manufacturing inventories, routing parts straight to your address with zero middleman markup.',
                'order' => 3
            ],
            [
                'category' => 'Marketplace & Orders',
                'question' => 'Are parts sold through the marketplace covered by warranties?',
                'answer' => 'Yes, all products (panels, batteries, smart breaker arrays) carry full linear manufacturer performance warranties, easily managed inside your client dashboard.',
                'order' => 4
            ],
            [
                'category' => 'Billing & Subscriptions',
                'question' => 'Is there a contract required for the Pro subscription plan?',
                'answer' => 'No contracts. All premium monitoring and priority dispatch tiers operate on a monthly recurring schedule, cancellable at any time with a single click in your settings.',
                'order' => 5
            ],
            [
                'category' => 'Billing & Subscriptions',
                'question' => 'How do you structure dispatcher payment rates?',
                'answer' => 'Technicians set their own local hourly service rates. Booking fees are pre-calculated and securely collected via the dashboard during technician selection.',
                'order' => 6
            ]
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate([
                'category' => $faq['category'],
                'question' => $faq['question']
            ], $faq);
        }
    }
}
