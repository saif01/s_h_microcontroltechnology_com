<?php

namespace App\Http\Controllers\Public\pages;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Service;
use App\Models\Product;
use App\Models\BlogPost;
use App\Models\Module;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Load only necessary columns for home page
        $homePage = Page::where('page_type', 'home')
            ->where('published', true)
            ->select('id', 'title', 'slug', 'page_type', 'featured_image', 'published', 'order')
            ->first();
        
        // Load home page settings - only select key and value columns
        $homePageSettings = Setting::where('group', 'home_page')
            ->select('key', 'value')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->key => $item->value];
            });
        
        // Ensure section visibility settings exist with defaults (all enabled by default)
        $defaultSectionSettings = [
            'hero_section_enabled' => '1',
            'stats_section_enabled' => '1',
            'trusted_by_section_enabled' => '1',
            'services_section_enabled' => '1',
            'why_choose_us_section_enabled' => '1',
            'testimonials_section_enabled' => '1',
            'featured_products_section_enabled' => '1',
            'cta_section_enabled' => '1',
        ];
        
        // Merge defaults with existing settings (existing settings take precedence)
        foreach ($defaultSectionSettings as $key => $defaultValue) {
            if (!$homePageSettings->has($key)) {
                $homePageSettings[$key] = $defaultValue;
            }
        }
        
        // Merge settings with page data (settings take precedence)
        if ($homePage) {
            if ($homePageSettings->has('home_hero_title') && $homePageSettings->get('home_hero_title')) {
                $homePage->title = $homePageSettings->get('home_hero_title');
            }
            if ($homePageSettings->has('home_hero_subtitle') && $homePageSettings->get('home_hero_subtitle')) {
                $homePage->content = $homePageSettings->get('home_hero_subtitle');
            }
        } else {
            // Create a virtual home page object from settings if no page exists
            $homePage = (object)[
                'title' => $homePageSettings->get('home_hero_title', 'Uninterrupted Power for Your Business & Home'),
                'content' => $homePageSettings->get('home_hero_subtitle', 'Reliable technical power support solutions, including UPS systems, industrial backup, batteries, and professional maintenance services.'),
            ];
        }
        
        $data = [
            'homePage' => $homePage,
            'homePageSettings' => $homePageSettings,
            'modules' => Module::where('enabled', true)->pluck('name')->toArray(),
        ];

        // Load featured services if module is enabled - only select necessary columns
        if (Module::isEnabled('services')) {
            $data['services'] = Service::where('published', true)
                ->select('id', 'title', 'slug', 'short_description', 'icon', 'image', 'price_range', 'order')
                ->orderBy('order')
                ->limit(6)
                ->get();
        }

        // Load featured products if module is enabled - only select necessary columns
        if (Module::isEnabled('products')) {
            $data['products'] = Product::where('published', true)
                ->where('featured', true)
                ->select('id', 'title', 'slug', 'sku', 'short_description', 'thumbnail', 'price', 'price_range', 'show_price', 'order')
                ->orderBy('order')
                ->limit(6)
                ->get();
        }

        // Load latest blog posts if module is enabled - only select necessary columns
        if (Module::isEnabled('blog')) {
            $data['posts'] = BlogPost::where('published', true)
                ->select('id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'views')
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get();
        }

        return response()->json($data);
    }
}
