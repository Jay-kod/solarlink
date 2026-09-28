<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'vendor_id' => User::factory(['role' => 'vendor']),
            'name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['panels', 'batteries', 'inverters', 'accessories']),
            'price' => 250,
            'original_price' => 290,
            'rating' => 4.7,
            'reviews_count' => 10,
            'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276',
            'description' => 'Sample solar product',
            'specs' => ['Warranty' => '12 years'],
            'stock' => 25,
            'features' => ['High efficiency', 'Certified'],
            'is_featured' => true,
            'is_verified' => true,
            'approval_status' => 'approved',
        ];
    }
}
