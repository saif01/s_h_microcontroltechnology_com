<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('featured', $request->featured);
        }

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $products = $query->orderBy('order')->orderBy('title')->paginate($perPage);
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'sku' => 'nullable|string|max:255|unique:products',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'images' => 'nullable|array',
            'price' => 'nullable|numeric',
            'price_range' => 'nullable|string',
            'show_price' => 'boolean',
            'specifications' => 'nullable|array',
            'downloads' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'published' => 'boolean',
            'featured' => 'boolean',
            'stock' => 'integer',
            'order' => 'integer',
        ]);

        $product = Product::create($validated);
        return response()->json($product->load(['categories', 'tags']), 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load(['categories', 'tags']));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'images' => 'nullable|array',
            'price' => 'nullable|numeric',
            'price_range' => 'nullable|string',
            'show_price' => 'boolean',
            'specifications' => 'nullable|array',
            'downloads' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'published' => 'boolean',
            'featured' => 'boolean',
            'stock' => 'integer',
            'order' => 'integer',
        ]);

        $product->update($validated);
        return response()->json($product->load(['categories', 'tags']));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
