<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\Menu;
use App\Models\Setting;
use App\Models\Service;
use App\Models\Product;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Portfolio;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Lead;
use App\Models\Branch;
use App\Models\Career;
use App\Models\Event;
use App\Models\User;
use App\Models\Module;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Settings
        $this->seedSettings();

        // Seed Pages
        $this->seedPages();

        // Seed Menu Items
        $this->seedMenus();

        // Seed Categories and Tags
        $categories = $this->seedCategories();
        $tags = $this->seedTags();

        // Seed Services (if module enabled or we'll enable it)
        Module::where('name', 'services')->update(['enabled' => true]);
        $this->seedServices($categories['service'], $tags['service']);

        // Seed Products (if module enabled)
        Module::where('name', 'products')->update(['enabled' => true]);
        $this->seedProducts($categories['product'], $tags['product']);

        // Seed Portfolio
        Module::where('name', 'portfolio')->update(['enabled' => true]);
        $this->seedPortfolio();

        // Seed Blog Posts
        Module::where('name', 'blog')->update(['enabled' => true]);
        $this->seedBlogPosts($categories['post'], $tags['post']);

        // Seed FAQs
        Module::where('name', 'faq')->update(['enabled' => true]);
        $this->seedFaqs();

        // Seed Branches
        Module::where('name', 'branches')->update(['enabled' => true]);
        $this->seedBranches();

        // Seed Careers
        Module::where('name', 'careers')->update(['enabled' => true]);
        $this->seedCareers();

        // Seed Events
        Module::where('name', 'events')->update(['enabled' => true]);
        $this->seedEvents();

        // Seed Sample Leads
        $this->seedLeads();

        $this->command->info('Demo data seeded successfully!');
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'MICRO CONTROL TECHNOLOGY', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'TECHNOLOGY', 'type' => 'text', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'info@microcontrol.com', 'type' => 'email', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+8801712258689', 'type' => 'text', 'group' => 'general'],
            ['key' => 'whatsapp_number', 'value' => '+8801712258689', 'type' => 'text', 'group' => 'general'],
            ['key' => 'address', 'value' => 'House 15, Road 13, Block D, Section 06, Mirpur, Dhaka.', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'owner_name', 'value' => 'Md. Maniruzzaman', 'type' => 'text', 'group' => 'general'],
            ['key' => 'facebook_url', 'value' => '', 'type' => 'url', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => '', 'type' => 'url', 'group' => 'social'],
            ['key' => 'linkedin_url', 'value' => '', 'type' => 'url', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'url', 'group' => 'social'],
            ['key' => 'meta_title', 'value' => 'MICRO CONTROL TECHNOLOGY - UPS, Battery & Power Solutions in Dhaka', 'type' => 'text', 'group' => 'seo'],
            ['key' => 'meta_description', 'value' => 'Leading provider of Offline and Online UPS systems, batteries, UPS motherboards, and power solutions in Mirpur, Dhaka. 15+ years of experience with 500+ systems installed.', 'type' => 'textarea', 'group' => 'seo'],
            ['key' => 'meta_keywords', 'value' => 'UPS, offline UPS, online UPS, battery, UPS motherboard, power solutions, Mirpur Dhaka, uninterruptible power supply', 'type' => 'text', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    private function seedPages(): array
    {
        $pages = [
            [
                'title' => 'Uninterrupted Power for Your Business & Home',
                'slug' => 'home',
                'content' => '<h1>Welcome to MICRO CONTROL TECHNOLOGY</h1><p>We are a leading provider of technical power support solutions in Mirpur, Dhaka. With over 15 years of experience, we ensure your business stays powered with reliable UPS systems, backup generators, and professional maintenance services.</p><h2>Why Choose Us?</h2><p>Our commitment to excellence, quality products, and customer satisfaction sets us apart. We specialize in Offline and Online UPS systems, batteries, UPS motherboards, and comprehensive power solutions for homes and businesses.</p><h2>Our Expertise</h2><ul><li>500+ Systems Installed</li><li>99.9% Power Uptime</li><li>24/7 Support</li><li>15+ Years Experience</li></ul>',
                'page_type' => 'home',
                'published' => true,
                'order' => 0,
                'meta_title' => 'MICRO CONTROL TECHNOLOGY - UPS & Power Solutions in Dhaka',
                'meta_description' => 'Leading provider of UPS systems, batteries, and power solutions in Mirpur, Dhaka. 15+ years experience with 500+ systems installed.',
            ],
            [
                'title' => 'About MICRO CONTROL TECHNOLOGY',
                'slug' => 'about',
                'content' => '<h1>About MICRO CONTROL TECHNOLOGY</h1><p>MICRO CONTROL TECHNOLOGY, owned by Md. Maniruzzaman, has been serving the power solutions market in Dhaka for over 15 years. Located in Mirpur, we specialize in providing reliable UPS systems, batteries, and power management solutions.</p><h2>Our Mission</h2><p>To provide reliable, high-quality power solutions that keep your business and home running smoothly, even during power outages.</p><h2>Our Vision</h2><p>To be the most trusted provider of UPS systems and power solutions in Dhaka, known for quality, reliability, and excellent customer service.</p><h2>Our Values</h2><ul><li>Quality Products</li><li>Reliable Service</li><li>Customer Satisfaction</li><li>Professional Excellence</li></ul><h2>Our Location</h2><p>House 15, Road 13, Block D, Section 06, Mirpur, Dhaka.</p><p><strong>Contact:</strong> +8801712258689</p>',
                'page_type' => 'about',
                'published' => true,
                'order' => 1,
                'meta_title' => 'About Us - MICRO CONTROL TECHNOLOGY',
                'meta_description' => 'Learn about MICRO CONTROL TECHNOLOGY, your trusted partner for UPS systems and power solutions in Mirpur, Dhaka.',
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => '<h1>Get in Touch</h1><p>We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.</p><p><strong>Owner:</strong> Md. Maniruzzaman<br><strong>Phone:</strong> +8801712258689<br><strong>WhatsApp:</strong> +8801712258689<br><strong>Address:</strong> House 15, Road 13, Block D, Section 06, Mirpur, Dhaka.</p>',
                'page_type' => 'page',
                'published' => true,
                'order' => 2,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Last updated: ' . date('F d, Y') . '</p><p>At MICRO CONTROL TECHNOLOGY, we take your privacy seriously. This policy describes how we collect, use, and protect your personal information.</p><h2>Information We Collect</h2><p>We collect information that you provide directly to us, including name, email address, phone number, and other contact details.</p><h2>How We Use Your Information</h2><p>We use your information to provide services, respond to inquiries, and improve our offerings.</p>',
                'page_type' => 'page',
                'published' => true,
                'order' => 10,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => '<h1>Terms & Conditions</h1><p>Last updated: ' . date('F d, Y') . '</p><p>By using our website and services, you agree to these terms and conditions.</p><h2>Use of Services</h2><p>Our services are provided for business purposes. You agree to use them in accordance with applicable laws and regulations.</p><h2>Limitation of Liability</h2><p>We strive to provide accurate information, but we are not liable for any errors or omissions.</p>',
                'page_type' => 'page',
                'published' => true,
                'order' => 11,
            ],
        ];

        $createdPages = [];
        foreach ($pages as $page) {
            $createdPages[$page['slug']] = Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }

        return $createdPages;
    }

    private function seedMenus(): void
    {
        $homePage = Page::where('slug', 'home')->first();
        $aboutPage = Page::where('slug', 'about')->first();
        $contactPage = Page::where('slug', 'contact')->first();

        $menus = [
            // Header Menu
            ['name' => 'header', 'label' => 'Home', 'page_id' => $homePage->id, 'url' => null, 'order' => 1, 'active' => true],
            ['name' => 'header', 'label' => 'About', 'page_id' => $aboutPage->id, 'url' => null, 'order' => 2, 'active' => true],
            ['name' => 'header', 'label' => 'Services', 'page_id' => null, 'url' => '/services', 'order' => 3, 'active' => true],
            ['name' => 'header', 'label' => 'Products', 'page_id' => null, 'url' => '/products', 'order' => 4, 'active' => true],
            ['name' => 'header', 'label' => 'Blog', 'page_id' => null, 'url' => '/blog', 'order' => 5, 'active' => true],
            ['name' => 'header', 'label' => 'Contact', 'page_id' => $contactPage->id, 'url' => null, 'order' => 6, 'active' => true],

            // Footer Menu
            ['name' => 'footer', 'label' => 'Privacy Policy', 'page_id' => Page::where('slug', 'privacy-policy')->first()->id, 'url' => null, 'order' => 1, 'active' => true],
            ['name' => 'footer', 'label' => 'Terms & Conditions', 'page_id' => Page::where('slug', 'terms-conditions')->first()->id, 'url' => null, 'order' => 2, 'active' => true],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name'], 'label' => $menu['label']],
                $menu
            );
        }
    }

    private function seedCategories(): array
    {
        $categories = [];

        // Service Categories
        $categories['service'] = [
            Category::updateOrCreate(
                ['slug' => 'ups-installation', 'type' => 'service'],
                ['name' => 'UPS Installation', 'slug' => 'ups-installation', 'type' => 'service', 'published' => true, 'order' => 1]
            ),
            Category::updateOrCreate(
                ['slug' => 'battery-replacement', 'type' => 'service'],
                ['name' => 'Battery Replacement', 'slug' => 'battery-replacement', 'type' => 'service', 'published' => true, 'order' => 2]
            ),
            Category::updateOrCreate(
                ['slug' => 'maintenance', 'type' => 'service'],
                ['name' => 'Maintenance & Repair', 'slug' => 'maintenance', 'type' => 'service', 'published' => true, 'order' => 3]
            ),
        ];

        // Product Categories
        $categories['product'] = [
            Category::updateOrCreate(
                ['slug' => 'ups-systems', 'type' => 'product'],
                ['name' => 'UPS Systems', 'slug' => 'ups-systems', 'type' => 'product', 'published' => true, 'order' => 1]
            ),
            Category::updateOrCreate(
                ['slug' => 'batteries', 'type' => 'product'],
                ['name' => 'Batteries', 'slug' => 'batteries', 'type' => 'product', 'published' => true, 'order' => 2]
            ),
            Category::updateOrCreate(
                ['slug' => 'ups-components', 'type' => 'product'],
                ['name' => 'UPS Components', 'slug' => 'ups-components', 'type' => 'product', 'published' => true, 'order' => 3]
            ),
        ];

        // Post Categories
        $categories['post'] = [
            Category::updateOrCreate(
                ['slug' => 'news', 'type' => 'post'],
                ['name' => 'News', 'slug' => 'news', 'type' => 'post', 'published' => true, 'order' => 1]
            ),
            Category::updateOrCreate(
                ['slug' => 'tips', 'type' => 'post'],
                ['name' => 'Tips & Tricks', 'slug' => 'tips', 'type' => 'post', 'published' => true, 'order' => 2]
            ),
            Category::updateOrCreate(
                ['slug' => 'case-studies', 'type' => 'post'],
                ['name' => 'Case Studies', 'slug' => 'case-studies', 'type' => 'post', 'published' => true, 'order' => 3]
            ),
        ];

        return $categories;
    }

    private function seedTags(): array
    {
        $tags = [];

        $tagNames = ['business', 'technology', 'innovation', 'strategy', 'growth', 'digital', 'marketing', 'sales'];

        // Service Tags
        $tags['service'] = [];
        foreach ($tagNames as $name) {
            $slug = $name . '-service';
            $tags['service'][] = Tag::updateOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst($name), 'slug' => $slug, 'type' => 'service']
            );
        }

        // Product Tags
        $tags['product'] = [];
        foreach ($tagNames as $name) {
            $slug = $name . '-product';
            $tags['product'][] = Tag::updateOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst($name), 'slug' => $slug, 'type' => 'product']
            );
        }

        // Post Tags
        $tags['post'] = [];
        foreach ($tagNames as $name) {
            $slug = $name . '-post';
            $tags['post'][] = Tag::updateOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst($name), 'slug' => $slug, 'type' => 'post']
            );
        }

        return $tags;
    }

    private function seedServices($categories, $tags): void
    {
        $services = [
            [
                'title' => 'UPS Installation',
                'slug' => 'ups-installation',
                'short_description' => 'Professional installation of offline and online UPS systems for homes and businesses.',
                'description' => '<p>Our expert technicians provide professional UPS installation services for both offline and online systems. We ensure proper setup, configuration, and testing to guarantee optimal performance.</p><h3>What We Offer:</h3><ul><li>Site assessment and capacity planning</li><li>Professional installation</li><li>System configuration and testing</li><li>User training</li><li>Warranty support</li></ul>',
                'icon' => 'mdi-battery-charging-high',
                'price_range' => 'Contact for quote',
                'features' => ['Expert Technicians', 'Quality Installation', 'Testing & Verification', 'Ongoing Support'],
                'benefits' => ['Proper Setup', 'Optimal Performance', 'Extended Lifespan', 'Peace of Mind'],
                'published' => true,
                'order' => 1,
            ],
            [
                'title' => 'Battery Replacement',
                'slug' => 'battery-replacement',
                'short_description' => 'Quick and reliable battery replacement service for all UPS systems.',
                'description' => '<p>Keep your UPS system running at peak performance with our professional battery replacement service. We stock quality batteries compatible with all major UPS brands.</p><h3>Services Include:</h3><ul><li>Battery testing and diagnosis</li><li>Quality replacement batteries</li><li>Professional installation</li><li>Old battery disposal</li><li>System testing after replacement</li></ul>',
                'icon' => 'mdi-battery-sync',
                'price_range' => 'Contact for pricing',
                'features' => ['Quality Batteries', 'Quick Service', 'Proper Disposal', 'Warranty Included'],
                'benefits' => ['Restored Performance', 'Extended UPS Life', 'Reliable Backup', 'Cost Effective'],
                'published' => true,
                'order' => 2,
            ],
            [
                'title' => 'UPS Maintenance & Repair',
                'slug' => 'ups-maintenance-repair',
                'short_description' => 'Comprehensive maintenance and repair services to keep your UPS systems running smoothly.',
                'description' => '<p>Regular maintenance and timely repairs are essential for UPS reliability. Our experienced technicians provide comprehensive maintenance and repair services for all UPS systems.</p><h3>Our Services:</h3><ul><li>Regular maintenance checks</li><li>Component replacement</li><li>Motherboard repair/replacement</li><li>System diagnostics</li><li>Preventive maintenance programs</li></ul>',
                'icon' => 'mdi-tools',
                'price_range' => 'Contact for quote',
                'features' => ['24/7 Support', 'Expert Technicians', 'Quality Parts', 'Fast Response'],
                'benefits' => ['Reduced Downtime', 'Extended Lifespan', 'Cost Savings', 'Reliable Operation'],
                'published' => true,
                'order' => 3,
            ],
            [
                'title' => 'Power Solutions Consultation',
                'slug' => 'power-solutions-consultation',
                'short_description' => 'Expert consultation to help you choose the right power solution for your needs.',
                'description' => '<p>Not sure which UPS system is right for you? Our experts provide comprehensive consultation to help you select the perfect power solution based on your specific requirements.</p><h3>Consultation Services:</h3><ul><li>Power requirement analysis</li><li>System recommendations</li><li>Cost-benefit analysis</li><li>Installation planning</li><li>Ongoing support planning</li></ul>',
                'icon' => 'mdi-lightning-bolt',
                'price_range' => 'Free consultation',
                'features' => ['Expert Advice', 'Custom Solutions', 'Cost Analysis', 'Long-term Planning'],
                'benefits' => ['Right Solution', 'Cost Optimization', 'Future-proofing', 'Informed Decisions'],
                'published' => true,
                'order' => 4,
            ],
        ];

        $createdServices = [];
        foreach ($services as $index => $service) {
            $createdService = Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );

            // Attach categories
            if (isset($categories[$index % count($categories)])) {
                $createdService->categories()->syncWithoutDetaching([$categories[$index % count($categories)]->id]);
            }

            // Attach random tags
            $randomTags = array_slice($tags, 0, rand(2, 4));
            $tagIds = array_map(fn($tag) => $tag->id, $randomTags);
            $createdService->tags()->syncWithoutDetaching($tagIds);
        }
    }

    private function seedProducts($categories, $tags): void
    {
        $products = [
            [
                'title' => 'Offline UPS System',
                'slug' => 'offline-ups-system',
                'sku' => 'UPS-OFF-001',
                'short_description' => 'Reliable offline UPS systems for home and office use. Provides backup power during outages.',
                'description' => '<p>Our offline UPS systems are designed to provide reliable backup power for your essential equipment. Perfect for home offices, small businesses, and personal computers.</p><h3>Key Features:</h3><ul><li>Automatic voltage regulation</li><li>Battery backup during power outages</li><li>Surge protection</li><li>Compact design</li><li>Easy installation</li></ul><h3>Applications:</h3><ul><li>Home computers</li><li>Small office equipment</li><li>Networking devices</li><li>Security systems</li></ul>',
                'price' => null,
                'price_range' => 'Contact for pricing',
                'show_price' => false,
                'specifications' => ['Multiple capacity options', 'LED status indicators', 'Audible alarm', 'Warranty included'],
                'published' => true,
                'featured' => true,
                'order' => 1,
            ],
            [
                'title' => 'Online UPS System',
                'slug' => 'online-ups-system',
                'sku' => 'UPS-ON-001',
                'short_description' => 'Professional online UPS systems for critical applications. Zero transfer time with continuous power protection.',
                'description' => '<p>Enterprise-grade online UPS systems providing continuous power protection for critical equipment. Ideal for servers, data centers, and mission-critical applications.</p><h3>Key Features:</h3><ul><li>Zero transfer time</li><li>Double conversion technology</li><li>High efficiency</li><li>Remote monitoring capability</li><li>Scalable power capacity</li></ul><h3>Applications:</h3><ul><li>Server rooms</li><li>Data centers</li><li>Medical equipment</li><li>Industrial automation</li><li>Telecommunications</li></ul>',
                'price' => null,
                'price_range' => 'Contact for pricing',
                'show_price' => false,
                'specifications' => ['High power factor', 'LCD display', 'Network management card', 'Extended runtime options'],
                'published' => true,
                'featured' => true,
                'order' => 2,
            ],
            [
                'title' => 'UPS Battery',
                'slug' => 'ups-battery',
                'sku' => 'BAT-UPS-001',
                'short_description' => 'High-quality replacement batteries for all UPS systems. Long-lasting and reliable performance.',
                'description' => '<p>Premium quality sealed lead-acid batteries designed specifically for UPS systems. Available in various capacities to match your UPS requirements.</p><h3>Key Features:</h3><ul><li>Sealed maintenance-free design</li><li>Long service life</li><li>High discharge rate</li><li>Wide temperature range</li><li>Compatible with major UPS brands</li></ul><h3>Specifications:</h3><ul><li>Multiple voltage options (12V, 24V, 48V)</li><li>Various capacity ratings</li><li>AGM technology</li><li>UL certified</li></ul>',
                'price' => null,
                'price_range' => 'Contact for pricing',
                'show_price' => false,
                'specifications' => ['Maintenance-free', 'Spill-proof design', 'Fast recharge', 'Deep cycle capable'],
                'published' => true,
                'featured' => true,
                'order' => 3,
            ],
            [
                'title' => 'UPS Motherboard',
                'slug' => 'ups-motherboard',
                'sku' => 'MB-UPS-001',
                'short_description' => 'Replacement motherboards for UPS systems. Compatible with various UPS models and brands.',
                'description' => '<p>Professional-grade replacement motherboards for UPS systems. Restore your UPS to full functionality with our quality replacement boards.</p><h3>Key Features:</h3><ul><li>Compatible with multiple UPS models</li><li>Quality components</li><li>Easy installation</li><li>Tested and verified</li><li>Warranty included</li></ul><h3>Applications:</h3><ul><li>UPS repair and maintenance</li><li>UPS upgrade projects</li><li>Replacement for damaged boards</li></ul>',
                'price' => null,
                'price_range' => 'Contact for pricing',
                'show_price' => false,
                'specifications' => ['Original specifications', 'Quality tested', 'Compatible models listed', 'Installation support'],
                'published' => true,
                'featured' => false,
                'order' => 4,
            ],
        ];

        foreach ($products as $index => $product) {
            $createdProduct = Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );

            // Attach categories - map products to appropriate categories
            $categoryMap = [
                0 => 0, // Offline UPS -> UPS Systems
                1 => 0, // Online UPS -> UPS Systems
                2 => 1, // Battery -> Batteries
                3 => 2, // UPS Motherboard -> UPS Components
            ];
            
            if (isset($categoryMap[$index]) && isset($categories[$categoryMap[$index]])) {
                $createdProduct->categories()->syncWithoutDetaching([$categories[$categoryMap[$index]]->id]);
            }

            // Attach random tags
            $randomTags = array_slice($tags, 0, rand(2, 4));
            $tagIds = array_map(fn($tag) => $tag->id, $randomTags);
            $createdProduct->tags()->syncWithoutDetaching($tagIds);
        }
    }

    private function seedPortfolio(): void
    {
        $portfolio = [
            [
                'title' => 'E-Commerce Platform Redesign',
                'slug' => 'ecommerce-platform-redesign',
                'description' => '<p>Complete redesign and development of a modern e-commerce platform resulting in 300% increase in sales.</p>',
                'client_name' => 'Retail Solutions Inc.',
                'industry' => 'E-Commerce',
                'project_date' => Carbon::now()->subMonths(6),
                'challenge' => 'Outdated platform with poor user experience and low conversion rates.',
                'solution' => 'Modern responsive design, improved checkout process, and mobile optimization.',
                'results' => '300% increase in sales, 50% reduction in bounce rate, improved customer satisfaction.',
                'published' => true,
                'featured' => true,
                'order' => 1,
            ],
            [
                'title' => 'Digital Transformation for Manufacturing',
                'slug' => 'digital-transformation-manufacturing',
                'description' => '<p>Comprehensive digital transformation project that improved efficiency by 40%.</p>',
                'client_name' => 'Manufacturing Corp',
                'industry' => 'Manufacturing',
                'project_date' => Carbon::now()->subMonths(4),
                'challenge' => 'Manual processes causing delays and errors in production.',
                'solution' => 'Implemented automated systems, IoT integration, and real-time monitoring.',
                'results' => '40% efficiency improvement, 30% cost reduction, enhanced quality control.',
                'published' => true,
                'featured' => true,
                'order' => 2,
            ],
            [
                'title' => 'Cloud Migration Project',
                'slug' => 'cloud-migration-project',
                'description' => '<p>Successful migration of enterprise infrastructure to cloud, reducing costs by 35%.</p>',
                'client_name' => 'Tech Enterprises',
                'industry' => 'Technology',
                'project_date' => Carbon::now()->subMonths(8),
                'challenge' => 'High infrastructure costs and limited scalability.',
                'solution' => 'Migrated to cloud infrastructure with automated scaling and backup solutions.',
                'results' => '35% cost reduction, improved scalability, 99.9% uptime.',
                'published' => true,
                'featured' => false,
                'order' => 3,
            ],
        ];

        foreach ($portfolio as $item) {
            Portfolio::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }

    private function seedBlogPosts($categories, $tags): void
    {
        // Get any user with administrator role, or fallback to first user
        $author = User::whereHas('roles', function ($q) {
            $q->where('slug', 'administrator');
        })->first();
        
        // Fallback to first user if no admin found
        if (!$author) {
            $author = User::first();
        }
        
        if (!$author) {
            $this->command->warn('No users found. Please run AdminUserSeeder first.');
            return;
        }

        $posts = [
            [
                'title' => '10 Ways to Improve Your Business Efficiency',
                'slug' => 'improve-business-efficiency',
                'excerpt' => 'Discover practical strategies to streamline operations and boost productivity in your business.',
                'content' => '<p>Efficiency is key to business success. In this article, we explore ten proven strategies to improve your business operations and drive better results.</p><h2>1. Automate Repetitive Tasks</h2><p>Automation can save countless hours and reduce errors. Identify tasks that can be automated and implement solutions.</p><h2>2. Streamline Communication</h2><p>Effective communication is essential. Use collaboration tools to improve team communication.</p><p>Continue reading for more tips...</p>',
                'author_id' => $author->id,
                'published_at' => Carbon::now()->subDays(5),
                'published' => true,
            ],
            [
                'title' => 'The Future of Digital Marketing',
                'slug' => 'future-of-digital-marketing',
                'excerpt' => 'Exploring emerging trends and technologies shaping the future of digital marketing.',
                'content' => '<p>Digital marketing continues to evolve. Let\'s explore the trends that will shape the industry in the coming years.</p><h2>AI and Machine Learning</h2><p>Artificial intelligence is revolutionizing how we approach marketing, enabling personalization at scale.</p><h2>Voice Search Optimization</h2><p>With the rise of smart speakers, optimizing for voice search is becoming increasingly important.</p>',
                'author_id' => $author->id,
                'published_at' => Carbon::now()->subDays(12),
                'published' => true,
            ],
            [
                'title' => 'Case Study: Successful Business Transformation',
                'slug' => 'case-study-business-transformation',
                'excerpt' => 'How one company achieved remarkable results through strategic business transformation.',
                'content' => '<p>This case study examines how a mid-size company achieved significant growth through strategic transformation.</p><h2>The Challenge</h2><p>The company faced declining market share and operational inefficiencies.</p><h2>The Solution</h2><p>Through comprehensive analysis and strategic planning, we helped implement transformative changes.</p><h2>The Results</h2><p>The results exceeded expectations, with 200% growth in revenue.</p>',
                'author_id' => $author->id,
                'published_at' => Carbon::now()->subDays(20),
                'published' => true,
            ],
        ];

        foreach ($posts as $index => $post) {
            $createdPost = BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post
            );

            // Attach categories
            if (isset($categories[$index % count($categories)])) {
                $createdPost->categories()->syncWithoutDetaching([$categories[$index % count($categories)]->id]);
            }

            // Attach random tags
            $randomTags = array_slice($tags, 0, rand(2, 4));
            $tagIds = array_map(fn($tag) => $tag->id, $randomTags);
            $createdPost->tags()->syncWithoutDetaching($tagIds);
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            [
                'question' => 'What services do you offer?',
                'answer' => 'We offer a wide range of business services including consulting, digital marketing, IT solutions, and financial planning. Our services are tailored to meet your specific business needs.',
                'category' => 'General',
                'order' => 1,
                'published' => true,
            ],
            [
                'question' => 'How long does a typical project take?',
                'answer' => 'Project timelines vary depending on the scope and complexity. Simple projects may take 2-4 weeks, while larger implementations can take 3-6 months. We provide detailed timelines during the consultation phase.',
                'category' => 'General',
                'order' => 2,
                'published' => true,
            ],
            [
                'question' => 'Do you offer ongoing support?',
                'answer' => 'Yes, we offer various support packages to ensure your systems continue to run smoothly. Support options include helpdesk, maintenance, and strategic consulting.',
                'category' => 'Support',
                'order' => 1,
                'published' => true,
            ],
            [
                'question' => 'What is your pricing model?',
                'answer' => 'Our pricing varies based on the services required. We offer both one-time project fees and monthly retainer options. Contact us for a customized quote based on your needs.',
                'category' => 'Pricing',
                'order' => 1,
                'published' => true,
            ],
            [
                'question' => 'Can you work with businesses of all sizes?',
                'answer' => 'Absolutely! We work with startups, small businesses, and large enterprises. Our solutions are scalable and adaptable to businesses of any size.',
                'category' => 'General',
                'order' => 3,
                'published' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }

    private function seedBranches(): void
    {
        $branches = [
            [
                'name' => 'Mirpur Office',
                'slug' => 'mirpur-office',
                'address' => 'House 15, Road 13, Block D, Section 06',
                'city' => 'Mirpur',
                'state' => 'Dhaka',
                'postal_code' => '1216',
                'country' => 'Bangladesh',
                'phone' => '+8801712258689',
                'email' => 'info@microcontrol.com',
                'opening_hours' => [
                    'monday' => '9:00 AM - 7:00 PM',
                    'tuesday' => '9:00 AM - 7:00 PM',
                    'wednesday' => '9:00 AM - 7:00 PM',
                    'thursday' => '9:00 AM - 7:00 PM',
                    'friday' => '9:00 AM - 7:00 PM',
                    'saturday' => '9:00 AM - 5:00 PM',
                    'sunday' => 'Closed',
                ],
                'published' => true,
                'order' => 1,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['slug' => $branch['slug']],
                $branch
            );
        }
    }

    private function seedCareers(): void
    {
        $careers = [
            [
                'title' => 'Senior Business Consultant',
                'slug' => 'senior-business-consultant',
                'department' => 'Consulting',
                'location' => 'New York, NY',
                'employment_type' => 'Full-time',
                'description' => '<p>We are seeking an experienced business consultant to join our team.</p>',
                'responsibilities' => '<ul><li>Lead client consulting engagements</li><li>Develop strategic recommendations</li><li>Manage project teams</li><li>Build client relationships</li></ul>',
                'requirements' => '<ul><li>5+ years of consulting experience</li><li>MBA or equivalent</li><li>Strong analytical skills</li><li>Excellent communication</li></ul>',
                'deadline' => Carbon::now()->addMonths(2),
                'published' => true,
                'order' => 1,
            ],
            [
                'title' => 'Digital Marketing Specialist',
                'slug' => 'digital-marketing-specialist',
                'department' => 'Marketing',
                'location' => 'Los Angeles, CA',
                'employment_type' => 'Full-time',
                'description' => '<p>Join our marketing team and help clients grow their digital presence.</p>',
                'responsibilities' => '<ul><li>Develop marketing strategies</li><li>Manage campaigns</li><li>Analyze performance</li><li>Create content</li></ul>',
                'requirements' => '<ul><li>3+ years of digital marketing experience</li><li>Proficiency in SEO/SEM</li><li>Social media expertise</li><li>Analytics skills</li></ul>',
                'deadline' => Carbon::now()->addMonths(1),
                'published' => true,
                'order' => 2,
            ],
        ];

        foreach ($careers as $career) {
            Career::updateOrCreate(
                ['slug' => $career['slug']],
                $career
            );
        }
    }

    private function seedEvents(): void
    {
        $events = [
            [
                'title' => 'Business Growth Summit 2024',
                'slug' => 'business-growth-summit-2024',
                'description' => '<p>Join us for a day of insights, networking, and strategies for business growth.</p>',
                'event_date' => Carbon::now()->addMonths(2),
                'event_time' => Carbon::now()->setTime(9, 0),
                'end_date_time' => Carbon::now()->addMonths(2)->setTime(17, 0),
                'venue' => 'Grand Conference Center',
                'address' => '789 Event Avenue, New York, NY 10001',
                'speakers' => [
                    ['name' => 'Md. Maniruzzaman', 'title' => 'Owner, MICRO CONTROL TECHNOLOGY'],
                    ['name' => 'Jane Doe', 'title' => 'Marketing Expert'],
                ],
                'allow_registration' => true,
                'max_attendees' => 200,
                'published' => true,
            ],
            [
                'title' => 'Digital Transformation Workshop',
                'slug' => 'digital-transformation-workshop',
                'description' => '<p>Learn how to transform your business with digital technologies.</p>',
                'event_date' => Carbon::now()->addMonths(3),
                'event_time' => Carbon::now()->setTime(10, 0),
                'end_date_time' => Carbon::now()->addMonths(3)->setTime(16, 0),
                'venue' => 'Tech Hub Los Angeles',
                'address' => '321 Innovation Drive, Los Angeles, CA 90001',
                'speakers' => [
                    ['name' => 'Mike Johnson', 'title' => 'CTO, Tech Solutions'],
                ],
                'allow_registration' => true,
                'max_attendees' => 50,
                'published' => true,
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['slug' => $event['slug']],
                $event
            );
        }
    }

    private function seedLeads(): void
    {
        $leads = [
            [
                'type' => 'contact',
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1 (555) 111-2222',
                'subject' => 'Inquiry about Services',
                'message' => 'I am interested in learning more about your business consulting services.',
                'status' => 'new',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'type' => 'quote',
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1 (555) 333-4444',
                'subject' => 'Request for Quote - Digital Marketing',
                'message' => 'Please provide a quote for your digital marketing services for our company.',
                'status' => 'in_progress',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'type' => 'contact',
                'name' => 'Robert Johnson',
                'email' => 'robert.j@example.com',
                'phone' => '+1 (555) 555-6666',
                'subject' => 'General Inquiry',
                'message' => 'Would like to schedule a consultation meeting.',
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($leads as $lead) {
            Lead::create($lead);
        }
    }
}
