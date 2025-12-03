<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('email', 'admin@example.com')->first();
        
        // Get sample products
        $products = Product::where('published', true)->take(10)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run product seeders first.');
            return;
        }

        $reviewTemplates = [
            [
                'rating' => 5.0,
                'title' => 'Excellent Product!',
                'comment' => 'This UPS system has been running flawlessly for months. Great build quality and reliable power backup. Highly recommended for home and office use.',
                'status' => 'approved',
            ],
            [
                'rating' => 4.5,
                'title' => 'Very Good Performance',
                'comment' => 'Solid UPS with good battery backup time. The transfer time is fast and it handles power fluctuations well. Value for money.',
                'status' => 'approved',
            ],
            [
                'rating' => 5.0,
                'title' => 'Best Investment',
                'comment' => 'Protected my expensive equipment during multiple power outages. The battery life is impressive and charging is fast. Definitely worth the investment.',
                'status' => 'approved',
            ],
            [
                'rating' => 4.0,
                'title' => 'Good Quality',
                'comment' => 'Works as expected. Good build quality and reliable performance. The only minor issue is the fan noise, but it\'s acceptable.',
                'status' => 'approved',
            ],
            [
                'rating' => 4.5,
                'title' => 'Reliable Backup',
                'comment' => 'Great for protecting sensitive electronics. Fast switchover and stable output voltage. Battery backup is sufficient for my needs.',
                'status' => 'approved',
            ],
            [
                'rating' => 3.5,
                'title' => 'Decent Product',
                'comment' => 'Does the job but could be better. Battery backup time is shorter than expected under heavy load. Overall acceptable for the price.',
                'status' => 'approved',
            ],
            [
                'rating' => 5.0,
                'title' => 'Outstanding!',
                'comment' => 'Exceeded my expectations. Clean power output, long battery life, and excellent build quality. Customer service was also very helpful.',
                'status' => 'approved',
            ],
            [
                'rating' => 4.5,
                'title' => 'Highly Recommended',
                'comment' => 'Using it for my computer setup and it works perfectly. No voltage fluctuations and smooth battery mode transition. Very satisfied.',
                'status' => 'approved',
            ],
            [
                'rating' => 4.0,
                'title' => 'Good Value',
                'comment' => 'Solid performance for the price. Adequate backup time and reliable operation. Would recommend for small office or home use.',
                'status' => 'approved',
            ],
            [
                'rating' => 3.0,
                'title' => 'Average Performance',
                'comment' => 'It works but nothing exceptional. Battery backup could be better. Suitable for basic needs.',
                'status' => 'pending',
            ],
        ];

        $reviewerNames = [
            'Mohammad Rahman',
            'Fatima Begum',
            'Ahmed Khan',
            'Ayesha Siddiqua',
            'Karim Hassan',
            'Nusrat Jahan',
            'Tariq Mahmud',
            'Farzana Ahmed',
            'Imran Ali',
            'Shabnam Akter'
        ];

        foreach ($products as $index => $product) {
            // Add 2-5 reviews per product
            $reviewCount = rand(2, 5);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $template = $reviewTemplates[($index + $i) % count($reviewTemplates)];
                $reviewerName = $reviewerNames[($index + $i) % count($reviewerNames)];
                
                $review = ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => null, // Guest review
                    'reviewer_name' => $reviewerName,
                    'reviewer_email' => strtolower(str_replace(' ', '.', $reviewerName)) . '@example.com',
                    'rating' => $template['rating'],
                    'title' => $template['title'],
                    'comment' => $template['comment'],
                    'status' => $template['status'],
                    'verified_purchase' => rand(0, 1) == 1,
                    'helpful_count' => rand(0, 25),
                    'not_helpful_count' => rand(0, 5),
                    'approved_by' => $template['status'] === 'approved' ? $admin?->id : null,
                    'approved_at' => $template['status'] === 'approved' ? now()->subDays(rand(1, 30)) : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
            }

            // Update product rating from reviews
            $product->updateRatingFromReviews();
            
            $this->command->info("Created {$reviewCount} reviews for: {$product->title}");
        }

        $this->command->info('Product reviews seeded successfully!');
    }
}

