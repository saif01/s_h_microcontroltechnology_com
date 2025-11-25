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
        $query = Product::where('published', true);

        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('featured')) {
            $query->where('featured', true);
        }

        $products = $query->orderBy('order')
            ->with(['categories', 'tags'])
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
            ->with(['categories', 'tags'])
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
