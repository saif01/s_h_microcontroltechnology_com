<?php

namespace App\Http\Controllers\Public\pages;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('published', true)
            ->select('id', 'title', 'slug', 'content', 'page_type', 'featured_image', 'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'published', 'order', 'created_at', 'updated_at')
            ->firstOrFail();
        return response()->json($page);
    }
}
