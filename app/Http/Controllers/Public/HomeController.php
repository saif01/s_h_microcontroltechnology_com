<?php

namespace App\Http\Controllers\Public;

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
        $homePage = Page::where('page_type', 'home')->where('published', true)->first();
        
        // Load home page settings
        $homePageSettings = Setting::where('group', 'home_page')->get()->mapWithKeys(function ($item) {
            return [$item->key => $item->value];
        });
        
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

        // Load featured services if module is enabled
        if (Module::isEnabled('services')) {
            $data['services'] = Service::where('published', true)->orderBy('order')->limit(6)->get();
        }

        // Load featured products if module is enabled
        if (Module::isEnabled('products')) {
            $data['products'] = Product::where('published', true)->where('featured', true)->orderBy('order')->limit(6)->get();
        }

        // Load latest blog posts if module is enabled
        if (Module::isEnabled('blog')) {
            $data['posts'] = BlogPost::where('published', true)->orderBy('published_at', 'desc')->limit(3)->get();
        }

        return response()->json($data);
    }
}
