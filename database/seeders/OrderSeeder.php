<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
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

        $panel = Product::where('name', 'like', '%AeroVolt%')->first();
        $battery = Product::where('name', 'like', '%Battery Wall%')->first();
        $controller = Product::where('name', 'like', '%Charge Controller%')->first();

        $orders = [
            [
                'id' => 8849,
                'user_id' => $customer->id,
                'product_id' => $panel ? $panel->id : 1,
                'quantity' => 6,
                'total_price' => 1734.00,
                'status' => 'shipped',
                'order_date' => '2026-05-26',
                'shipping_address' => '430 Mission District, San Francisco, CA',
                'tracking_number' => 'TRK-SL-90924901'
            ],
            [
                'id' => 7429,
                'user_id' => $customer->id,
                'product_id' => $battery ? $battery->id : 2,
                'quantity' => 1,
                'total_price' => 3499.00,
                'status' => 'processing',
                'order_date' => '2026-05-28',
                'shipping_address' => '12 Emerald Bay Dr, San Francisco, CA',
                'tracking_number' => null
            ],
            [
                'id' => 6512,
                'user_id' => $customer->id,
                'product_id' => $controller ? $controller->id : 4,
                'quantity' => 2,
                'total_price' => 298.00,
                'status' => 'delivered',
                'order_date' => '2026-05-20',
                'shipping_address' => '1776 Lightning Ridge, Philadelphia, PA',
                'tracking_number' => 'TRK-SL-3382942'
            ]
        ];

        foreach ($orders as $order) {
            Order::updateOrCreate(['id' => $order['id']], $order);
        }
    }
}
