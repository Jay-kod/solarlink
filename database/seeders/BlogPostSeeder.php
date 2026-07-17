<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogPost;

class BlogPostSeeder extends Seeder
{
    public function run()
    {
        $posts = [
            ['title' => 'How to Prevent Panel Dust Build-Up This Summer', 'author' => 'Dr. Evelyn Carter', 'category' => 'Guide', 'created_at' => '2026-05-24 10:00:00', 'views' => 2400, 'status' => 'published', 'content' => 'Content here...'],
            ['title' => 'Why LFP Battery Stacking is Changing Home Energy Storage', 'author' => 'Siddharth Patel', 'category' => 'News', 'created_at' => '2026-05-20 10:00:00', 'views' => 1800, 'status' => 'published', 'content' => 'Content here...'],
            ['title' => 'Net Metering Policies Update - California NEMA 3.0 Guidelines', 'author' => 'Alex Thompson', 'category' => 'Advisory', 'created_at' => '2026-05-18 10:00:00', 'views' => 3100, 'status' => 'published', 'content' => 'Content here...'],
            ['title' => 'Upcoming App Integration with smart telemetry arrays', 'author' => 'Grace Hopper', 'category' => 'Announcement', 'created_at' => '2026-05-15 10:00:00', 'views' => 720, 'status' => 'draft', 'content' => 'Content here...'],
            ['title' => 'Winterizing your solar power setups: Critical checklist', 'author' => 'Dr. Evelyn Carter', 'category' => 'Guide', 'created_at' => '2026-01-10 10:00:00', 'views' => 430, 'status' => 'draft', 'content' => 'Content here...']
        ];
        
        foreach ($posts as $p) {
            BlogPost::create($p);
        }
    }
}
