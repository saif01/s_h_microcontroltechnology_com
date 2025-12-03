<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ModuleSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            DemoDataSeeder::class,
            AboutSeeder::class,
            AnnouncementSeeder::class,
            UpsOfflineProductSeeder::class,
            OnlineUpsCatalog1ProductSeeder::class,
            OnlineUpsCatalog2ProductSeeder::class,
            IpsCatalogProductSeeder::class,
            Stabilizer1ProductSeeder::class,
            Stabilizer2ProductSeeder::class,
            UpdateProductFieldsSeeder::class,  // Update existing products with new fields
            ProductReviewSeeder::class,        // Add sample reviews
        ]);
    }
}
