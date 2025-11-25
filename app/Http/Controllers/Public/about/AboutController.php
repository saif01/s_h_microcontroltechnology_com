<?php

namespace App\Http\Controllers\Public\about;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Support\MediaPath;

class AboutController extends Controller
{
    /**
     * Get the public about content
     * If no content exists, seed default data and return it
     */
    public function index()
    {
        $about = About::first();
        
        if (!$about) {
            // Seed default data if it doesn't exist
            $about = $this->seedDefaultData();
        }

        return response()->json($this->transformAboutWithImages($about));
    }

    /**
     * Seed default about data
     */
    private function seedDefaultData(): About
    {
        return About::create([
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
    }

    /**
     * Transform about data with image URLs and camelCase keys
     */
    private function transformAboutWithImages(About $about): array
    {
        $data = $about->toArray();

        // Transform story image
        if (!empty($data['story']['image'])) {
            $data['story']['image'] = MediaPath::url($data['story']['image']);
        }

        // Transform team member images
        if (!empty($data['team'])) {
            foreach ($data['team'] as &$member) {
                if (!empty($member['image'])) {
                    $member['image'] = MediaPath::url($member['image']);
                }
            }
        }

        // Transform snake_case to camelCase for frontend
        $transformed = [
            'hero' => $data['hero'] ?? null,
            'story' => $data['story'] ?? null,
            'valuesTitle' => $data['values_title'] ?? null,
            'valuesSubtitle' => $data['values_subtitle'] ?? null,
            'values' => $data['values'] ?? null,
            'teamOverline' => $data['team_overline'] ?? null,
            'teamTitle' => $data['team_title'] ?? null,
            'team' => $data['team'] ?? null,
        ];

        return $transformed;
    }
}
