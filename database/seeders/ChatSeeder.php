<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('email', 'customer@solarlink.io')->first();
        $vendor = User::where('email', 'vendor@solarlink.io')->first();
        $techMarcus = User::where('email', 'technician@solarlink.io')->first();
        $techElena = User::where('email', 'elena@solarlink.io')->first();

        if (!$customer || !$vendor || !$techMarcus || !$techElena) {
            return;
        }

        // 1. Tripartite Active Group Chat (Alice, Marcus, Sarah)
        $groupActive = Conversation::create([
            'title' => 'Inverter Swap & Parts Claims',
            'status' => 'active',
        ]);

        // Attach participants (Customer, Vendor, Technician)
        $groupActive->users()->attach([$customer->id, $vendor->id, $techMarcus->id]);

        // Create messages with correct timestamps
        $now = now();
        
        Message::create([
            'conversation_id' => $groupActive->id,
            'sender_id' => $techMarcus->id,
            'text' => "Hi Alice, I've reviewed the diagnostic report on your microinverter array. Panels 3 and 7 show early-stage voltage degradation.",
            'created_at' => $now->copy()->subMinutes(60),
        ]);

        Message::create([
            'conversation_id' => $groupActive->id,
            'sender_id' => $customer->id,
            'text' => "That makes sense — I noticed the generation dip on the telemetry dashboard last week. What do you recommend?",
            'created_at' => $now->copy()->subMinutes(50),
        ]);

        Message::create([
            'conversation_id' => $groupActive->id,
            'sender_id' => $techMarcus->id,
            'text' => "I recommend a full swap of the Enphase IQ8+ units on those two positions. I can bring the replacement boards during the scheduled visit.",
            'created_at' => $now->copy()->subMinutes(40),
        ]);

        Message::create([
            'conversation_id' => $groupActive->id,
            'sender_id' => $vendor->id,
            'text' => "Hi guys, Sarah here from EcoGrid. I've processed the parts warranty claim for those two Enphase IQ8+ units. Marcus, they will be ready for pickup at our Oakland warehouse tomorrow morning.",
            'created_at' => $now->copy()->subMinutes(30),
        ]);

        Message::create([
            'conversation_id' => $groupActive->id,
            'sender_id' => $customer->id,
            'text' => "Perfect — can you also inspect the battery charge controller while you're here? The cycling seems slower than expected.",
            'created_at' => $now->copy()->subMinutes(20),
        ]);

        Message::create([
            'conversation_id' => $groupActive->id,
            'sender_id' => $techMarcus->id,
            'text' => "Absolutely. I will pick up the panels from Sarah first, then be on-site at 2:00 PM for the inverter swap. Please ensure the main breaker is accessible.",
            'created_at' => $now->copy()->subMinutes(10),
        ]);


        // 2. Tripartite Concluded Group Chat (Alice, Elena, Sarah)
        $groupConcluded = Conversation::create([
            'title' => 'Initial Smart Meter Upgrade',
            'status' => 'concluded',
        ]);

        $groupConcluded->users()->attach([$customer->id, $vendor->id, $techElena->id]);

        Message::create([
            'conversation_id' => $groupConcluded->id,
            'sender_id' => $techElena->id,
            'text' => "Upgrade complete. Smart meter is now broadcasting telemetry data successfully.",
            'created_at' => $now->copy()->subDays(5),
        ]);

        Message::create([
            'conversation_id' => $groupConcluded->id,
            'sender_id' => $customer->id,
            'text' => "Thanks Elena! The dashboard shows live metrics now.",
            'created_at' => $now->copy()->subDays(5)->addMinutes(10),
        ]);
    }
}
