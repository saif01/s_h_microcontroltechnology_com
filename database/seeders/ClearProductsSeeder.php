<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder clears all products from the products table.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the products table
        DB::table('products')->truncate();
        
        // Clear pivot tables if they exist
        DB::table('category_product')->truncate();
        DB::table('tag_product')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Products table has been cleared successfully.');
    }
}

