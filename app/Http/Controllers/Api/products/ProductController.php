<?php

namespace App\Http\Controllers\Api\products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
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

        // Sorting
        $sortBy = $request->get('sort_by', 'order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['id', 'title', 'slug', 'sku', 'price', 'published', 'featured', 'stock', 'order', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'order';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortBy, $sortDirection);
        
        if ($sortBy !== 'title' && $sortBy !== 'order') {
            $query->orderBy('title', 'asc');
        }

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $products = $query->with(['categories', 'tags'])->paginate($perPage);
        
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
            'stock' => 'nullable|integer',
            'order' => 'nullable|integer',
        ]);

        // Handle key_features, faqs, warranty_info - store in specifications or as separate data
        $productData = $validated;
        
        // Store key_features, faqs, warranty_info in specifications JSON if provided
        if ($request->has('key_features') || $request->has('faqs') || $request->has('warranty_info')) {
            $specs = $productData['specifications'] ?? [];
            if ($request->has('key_features')) {
                $specs['_key_features'] = $request->key_features;
            }
            if ($request->has('faqs')) {
                $specs['_faqs'] = $request->faqs;
            }
            if ($request->has('warranty_info')) {
                $specs['_warranty_info'] = $request->warranty_info;
            }
            $productData['specifications'] = $specs;
        }
        
        $product = Product::create($productData);
        
        // Sync categories
        if ($request->has('category_ids') && is_array($request->category_ids)) {
            $product->categories()->sync($request->category_ids);
        }
        
        // Sync tags - handle both tag_ids and tag_names
        if ($request->has('tag_ids') && is_array($request->tag_ids)) {
            $product->tags()->sync($request->tag_ids);
        } elseif ($request->has('tag_names') && is_array($request->tag_names)) {
            $tagIds = [];
            foreach ($request->tag_names as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName, 'type' => 'product'],
                        ['slug' => \Illuminate\Support\Str::slug($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $product->tags()->sync($tagIds);
        }
        
        return response()->json($product->load(['categories', 'tags']), 201);
    }

    public function show(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $product = Product::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $product = $product->load(['categories', 'tags']);
        
        // Extract key_features, faqs, warranty_info from specifications
        $specs = $product->specifications ?? [];
        if (isset($specs['_key_features'])) {
            $product->key_features = $specs['_key_features'];
            unset($specs['_key_features']);
        }
        if (isset($specs['_faqs'])) {
            $product->faqs = $specs['_faqs'];
            unset($specs['_faqs']);
        }
        if (isset($specs['_warranty_info'])) {
            $product->warranty_info = $specs['_warranty_info'];
            unset($specs['_warranty_info']);
        }
        $product->specifications = $specs;
        
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $product = Product::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        
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
            'key_features' => 'nullable|array',
            'faqs' => 'nullable|array',
            'warranty_info' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'published' => 'boolean',
            'featured' => 'boolean',
            'stock' => 'nullable|integer',
            'order' => 'nullable|integer',
            'category_ids' => 'nullable|array',
            'tag_ids' => 'nullable|array',
            'tag_names' => 'nullable|array',
        ]);

        // Handle key_features, faqs, warranty_info - merge with existing specifications
        $productData = $validated;
        
        // Get existing specifications
        $existingSpecs = $product->specifications ?? [];
        
        // Merge new specifications with existing ones (preserve special fields)
        if ($request->has('specifications') && is_array($request->specifications)) {
            // Remove special fields from new specs before merging
            $newSpecs = array_filter($request->specifications, function($key) {
                return !str_starts_with($key, '_');
            }, ARRAY_FILTER_USE_KEY);
            
            // Merge new specs with existing (excluding special fields)
            $mergedSpecs = array_merge($existingSpecs, $newSpecs);
            // Remove special fields from merged specs
            $mergedSpecs = array_filter($mergedSpecs, function($key) {
                return !str_starts_with($key, '_');
            }, ARRAY_FILTER_USE_KEY);
            // Add back the new specs
            $mergedSpecs = array_merge($mergedSpecs, $newSpecs);
        } else {
            $mergedSpecs = array_filter($existingSpecs, function($key) {
                return !str_starts_with($key, '_');
            }, ARRAY_FILTER_USE_KEY);
        }
        
        // Add/update special fields
        if ($request->has('key_features')) {
            $mergedSpecs['_key_features'] = $request->key_features;
        } elseif (isset($existingSpecs['_key_features'])) {
            $mergedSpecs['_key_features'] = $existingSpecs['_key_features'];
        }
        
        if ($request->has('faqs')) {
            $mergedSpecs['_faqs'] = $request->faqs;
        } elseif (isset($existingSpecs['_faqs'])) {
            $mergedSpecs['_faqs'] = $existingSpecs['_faqs'];
        }
        
        if ($request->has('warranty_info')) {
            $mergedSpecs['_warranty_info'] = $request->warranty_info;
        } elseif (isset($existingSpecs['_warranty_info'])) {
            $mergedSpecs['_warranty_info'] = $existingSpecs['_warranty_info'];
        }
        
        $productData['specifications'] = !empty($mergedSpecs) ? $mergedSpecs : null;
        
        // Update product
        $product->update($productData);
        
        // Sync categories
        if ($request->has('category_ids')) {
            $product->categories()->sync($request->category_ids ?? []);
        }
        
        // Sync tags - handle both tag_ids and tag_names
        if ($request->has('tag_ids') && is_array($request->tag_ids)) {
            $product->tags()->sync($request->tag_ids);
        } elseif ($request->has('tag_names') && is_array($request->tag_names)) {
            $tagIds = [];
            foreach ($request->tag_names as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName, 'type' => 'product'],
                        ['slug' => \Illuminate\Support\Str::slug($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $product->tags()->sync($tagIds);
        }
        
        return response()->json($product->load(['categories', 'tags']));
    }

    public function destroy($id)
    {
        // Support both id and slug for route model binding
        $product = Product::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
