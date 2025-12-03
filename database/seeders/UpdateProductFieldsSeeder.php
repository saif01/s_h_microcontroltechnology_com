<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class UpdateProductFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Updates existing products with new fields: brand, availability, features, discount
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found to update.');
            return;
        }

        $brands = [
            'Micro Control Technology',
            'MCT',
            'MCT Power Solutions',
            'MCT Industrial',
        ];

        $featureOptions = [
            ['wireless', 'eco_friendly'],
            ['warranty', 'rechargeable'],
            ['waterproof', 'warranty'],
            ['rechargeable', 'eco_friendly'],
            ['wireless', 'bluetooth', 'rechargeable'],
        ];

        foreach ($products as $index => $product) {
            // Only update if fields are not already set
            $updates = [];

            if (!$product->brand) {
                $updates['brand'] = $brands[$index % count($brands)];
            }

            if (!$product->availability || $product->availability === 'in_stock') {
                $availabilityOptions = ['in_stock', 'in_stock', 'in_stock', 'pre_order', 'coming_soon'];
                $updates['availability'] = $availabilityOptions[$index % count($availabilityOptions)];
            }

            if (!$product->features || empty($product->features)) {
                $updates['features'] = $featureOptions[$index % count($featureOptions)];
            }

            // Add stock information
            if (is_null($product->stock)) {
                $updates['stock'] = $updates['availability'] === 'in_stock' ? rand(10, 100) : 0;
            }

            // Add some products with discounts (20% of products)
            if (is_null($product->discount_percent) && $product->price && rand(1, 5) == 1) {
                $discountPercent = [5, 10, 15, 20][rand(0, 3)];
                $updates['discount_percent'] = $discountPercent;
                $updates['discounted_price'] = $product->price * (1 - $discountPercent / 100);
            }

            if (!empty($updates)) {
                $product->update($updates);
                $this->command->info("Updated: {$product->title}");
            }
        }

        $this->command->info('Product fields updated successfully!');
    }
}

