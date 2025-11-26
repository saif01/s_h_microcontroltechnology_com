<?php

namespace App\Http\Controllers\Public\services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('published', true)
            ->select('id', 'title', 'slug', 'short_description', 'icon', 'image', 'price_range', 'meta_title', 'meta_description', 'og_image', 'published', 'order', 'created_at', 'updated_at')
            ->orderBy('order')
            ->with([
                'categories' => function ($query) {
                    $query->select('categories.id', 'categories.name', 'categories.slug', 'categories.type');
                },
                'tags' => function ($query) {
                    $query->select('tags.id', 'tags.name', 'tags.slug', 'tags.type');
                }
            ])
            ->get();
        
        $services->transform(function ($service) {
            return $this->transformServiceWithImages($service);
        });
        
        return response()->json($services);
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('published', true)
            ->select('id', 'title', 'slug', 'short_description', 'description', 'icon', 'image', 'price_range', 'features', 'benefits', 'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'published', 'order', 'created_at', 'updated_at')
            ->with([
                'categories' => function ($query) {
                    $query->select('categories.id', 'categories.name', 'categories.slug', 'categories.type', 'categories.description', 'categories.image');
                },
                'tags' => function ($query) {
                    $query->select('tags.id', 'tags.name', 'tags.slug', 'tags.type');
                }
            ])
            ->firstOrFail();
        
        return response()->json($this->transformServiceWithImages($service));
    }

    private function transformServiceWithImages(Service $service): Service
    {
        $service->image = MediaPath::url($service->image);

        if (!empty($service->og_image)) {
            $service->og_image = MediaPath::url($service->og_image);
        }

        return $service;
    }
}
