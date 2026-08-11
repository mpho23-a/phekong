<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Moringa Leaf Powder 250g', 'description' => 'Pure moringa leaf powder, nutrient booster', 'price' => 159.99, 'quantity' => 35, 'low_stock_threshold' => 10],
            ['name' => 'Rooibos Herbal Tea (Box of 40)', 'description' => 'Caffeine-free antioxidant-rich rooibos tea bags', 'price' => 79.00, 'quantity' => 6, 'low_stock_threshold' => 12],
            ['name' => 'African Wormwood Tincture 100ml', 'description' => 'Traditional immune support tincture', 'price' => 189.50, 'quantity' => 22, 'low_stock_threshold' => 8],
            ['name' => 'Ginger Root Capsules (60ct)', 'description' => 'Digestive support ginger root extract', 'price' => 129.00, 'quantity' => 4, 'low_stock_threshold' => 10],
            ['name' => 'Devil\'s Claw Herbal Balm', 'description' => 'Joint and muscle relief topical balm', 'price' => 149.99, 'quantity' => 18, 'low_stock_threshold' => 6],
            ['name' => 'Buchu Leaf Extract Drops', 'description' => 'Urinary and kidney support herbal drops', 'price' => 169.00, 'quantity' => 3, 'low_stock_threshold' => 5],
            ['name' => 'Hibiscus Flower Tea (Box of 20)', 'description' => 'Vitamin C rich hibiscus infusion', 'price' => 59.99, 'quantity' => 50, 'low_stock_threshold' => 15],
            ['name' => 'Turmeric & Black Pepper Capsules', 'description' => 'Anti-inflammatory turmeric curcumin blend', 'price' => 219.00, 'quantity' => 9, 'low_stock_threshold' => 10],
            ['name' => 'Sutherlandia (Cancer Bush) Capsules', 'description' => 'Traditional adaptogen and immune tonic', 'price' => 199.00, 'quantity' => 2, 'low_stock_threshold' => 6],
            ['name' => 'Chamomile Dried Flowers 100g', 'description' => 'Calming herbal tea and infusion flowers', 'price' => 89.00, 'quantity' => 27, 'low_stock_threshold' => 10],
            ['name' => 'Aloe Ferox Bitters 250ml', 'description' => 'Digestive detox and wellness tonic', 'price' => 119.99, 'quantity' => 15, 'low_stock_threshold' => 8],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['name' => $product['name']], $product);
        }
    }
}
