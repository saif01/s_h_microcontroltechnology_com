<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\About;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create if it doesn't exist (singleton pattern)
        if (About::count() === 0) {
            About::create([
                'hero' => [
                    'overline' => 'WHO WE ARE',
                    'title' => 'Empowering Your World',
                    'subtitle' => 'We are dedicated to providing reliable, efficient, and sustainable power solutions for businesses and homes across the globe.'
                ],
                'story' => [
                    'overline' => 'OUR STORY',
                    'title' => 'Decades of Excellence in Power Solutions',
                    'description' => '<p>Founded in 2010, Micro Control Technology began with a simple mission: to ensure that no business or home has to suffer from power interruptions. What started as a small local service has grown into a leading provider of comprehensive power support solutions.</p><p>We specialize in UPS systems, industrial backup power, battery maintenance, and smart energy management. Our team of certified engineers works tirelessly to deliver products and services that you can trust.</p>',
                    'image' => null,
                    'stats' => [
                        ['value' => '15+', 'label' => 'Years Experience'],
                        ['value' => '500+', 'label' => 'Projects Completed'],
                        ['value' => '50+', 'label' => 'Team Members']
                    ]
                ],
                'values_title' => 'Our Core Values',
                'values_subtitle' => 'The principles that guide every project we undertake.',
                'values' => [
                    [
                        'title' => 'Reliability',
                        'description' => 'We deliver systems you can count on. Our backup solutions are tested rigorously to ensure 99.9% uptime.',
                        'icon' => 'mdi-shield-check'
                    ],
                    [
                        'title' => 'Innovation',
                        'description' => 'We stay ahead of the curve by adopting the latest technologies in power management and battery efficiency.',
                        'icon' => 'mdi-lightbulb-on-outline'
                    ],
                    [
                        'title' => 'Customer First',
                        'description' => 'Your satisfaction is our priority. We offer personalized solutions and 24/7 support for all our clients.',
                        'icon' => 'mdi-account-heart-outline'
                    ]
                ],
                'team_overline' => 'OUR TEAM',
                'team_title' => 'Meet the Experts',
                'team' => [
                    [
                        'name' => 'James Wilson',
                        'role' => 'CEO & Founder',
                        'image' => 'https://i.pravatar.cc/300?img=11',
                        'linkedin' => null,
                        'twitter' => null
                    ],
                    [
                        'name' => 'Elena Rodriguez',
                        'role' => 'Chief Engineer',
                        'image' => 'https://i.pravatar.cc/300?img=5',
                        'linkedin' => null,
                        'twitter' => null
                    ],
                    [
                        'name' => 'Michael Chang',
                        'role' => 'Operations Manager',
                        'image' => 'https://i.pravatar.cc/300?img=3',
                        'linkedin' => null,
                        'twitter' => null
                    ],
                    [
                        'name' => 'Sarah Patel',
                        'role' => 'Head of Support',
                        'image' => 'https://i.pravatar.cc/300?img=9',
                        'linkedin' => null,
                        'twitter' => null
                    ]
                ]
            ]);

            $this->command->info('About page default data seeded successfully!');
        } else {
            $this->command->info('About page data already exists. Skipping seed.');
        }
    }
}
