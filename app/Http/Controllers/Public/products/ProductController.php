<?php

namespace App\Http\Controllers\Public\products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('published', true)
            ->select('id', 'title', 'slug', 'sku', 'short_description', 'thumbnail', 'price', 'price_range', 'show_price', 'meta_title', 'meta_description', 'og_image', 'published', 'featured', 'stock', 'order', 'created_at', 'updated_at');

        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('featured')) {
            $query->where('featured', true);
        }

        $products = $query->orderBy('order')
            ->with([
                'categories' => function ($query) {
                    $query->select('categories.id', 'categories.name', 'categories.slug', 'categories.type');
                },
                'tags' => function ($query) {
                    $query->select('tags.id', 'tags.name', 'tags.slug', 'tags.type');
                }
            ])
            ->get();
        
        $products->transform(function ($product) {
            return $this->transformProductWithImages($product);
        });
        
        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('published', true)
            ->select('id', 'title', 'slug', 'sku', 'short_description', 'description', 'thumbnail', 'images', 'price', 'price_range', 'show_price', 'specifications', 'downloads', 'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'published', 'featured', 'stock', 'order', 'created_at', 'updated_at')
            ->with([
                'categories' => function ($query) {
                    $query->select('categories.id', 'categories.name', 'categories.slug', 'categories.type', 'categories.description', 'categories.image');
                },
                'tags' => function ($query) {
                    $query->select('tags.id', 'tags.name', 'tags.slug', 'tags.type');
                }
            ])
            ->firstOrFail();
        
        return response()->json($this->transformProductWithImages($product));
    }

    private function transformProductWithImages(Product $product): Product
    {
        $product->thumbnail = MediaPath::url($product->thumbnail);

        if (is_array($product->images)) {
            $product->images = array_map(function ($image) {
                return MediaPath::url($image);
            }, array_filter($product->images));
        }

        if (!empty($product->og_image)) {
            $product->og_image = MediaPath::url($product->og_image);
        }

        return $product;
    }
}
