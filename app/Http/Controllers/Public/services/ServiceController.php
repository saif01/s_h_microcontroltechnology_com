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
            ->orderBy('order')
            ->with(['categories', 'tags'])
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
            ->with(['categories', 'tags'])
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
