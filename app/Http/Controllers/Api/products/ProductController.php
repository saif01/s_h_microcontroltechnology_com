<?php

namespace App\Http\Controllers\Api\products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Limit payload to the fields the grid needs to reduce response size
        $baseSelect = [
            'id',
            'title',
            'slug',
            'sku',
            'price',
            'price_range',
            'published',
            'featured',
            'stock',
            'order',
            'thumbnail',
            'images',
            'created_at',
        ];

        $query = Product::query()->select($baseSelect);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by published status (accepts truthy/falsey strings)
        if ($request->has('published')) {
            $published = filter_var($request->published, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_null($published)) {
                $query->where('published', $published);
            }
        }

        // Filter by featured
        if ($request->has('featured')) {
            $featured = filter_var($request->featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_null($featured)) {
                $query->where('featured', $featured);
            }
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

        // Paginate results with sane limit to avoid massive payloads
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $products = $query
            ->with([
                'categories:id,name',
                'tags:id,name',
            ])
            ->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            return $this->transformProductWithImages($product);
        });

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
        
        // Normalize image paths
        if (!empty($productData['thumbnail'])) {
            $productData['thumbnail'] = MediaPath::normalize($productData['thumbnail']);
        }
        if (!empty($productData['images']) && is_array($productData['images'])) {
            $productData['images'] = array_map(function ($image) {
                return MediaPath::normalize($image);
            }, array_filter($productData['images']));
        }
        if (!empty($productData['og_image'])) {
            $productData['og_image'] = MediaPath::normalize($productData['og_image']);
        }
        
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
        
        return response()->json(
            $this->transformProductWithImages($product->load(['categories:id,name', 'tags:id,name'])),
            201
        );
    }

    public function show(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $product = Product::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $product = $product->load(['categories:id,name', 'tags:id,name']);
        
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
        
        return response()->json($this->transformProductWithImages($product));
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
        
        // Normalize image paths
        if (array_key_exists('thumbnail', $productData) && !empty($productData['thumbnail'])) {
            $productData['thumbnail'] = MediaPath::normalize($productData['thumbnail']);
        }
        if (array_key_exists('images', $productData) && is_array($productData['images'])) {
            $productData['images'] = array_map(function ($image) {
                return MediaPath::normalize($image);
            }, array_filter($productData['images']));
        }
        if (array_key_exists('og_image', $productData) && !empty($productData['og_image'])) {
            $productData['og_image'] = MediaPath::normalize($productData['og_image']);
        }
        
        // Get existing specifications
        $existingSpecs = $product->specifications ?? [];
        
        // Start with empty merged specs
        $mergedSpecs = [];
        
        // Handle regular specifications (non-special fields)
        if ($request->has('specifications') && is_array($request->specifications)) {
            // Only include non-special fields (those that don't start with '_')
            foreach ($request->specifications as $key => $value) {
                if (!str_starts_with($key, '_')) {
                    $mergedSpecs[$key] = $value;
                }
            }
        } else {
            // If no new specifications provided, preserve existing non-special fields
            if (is_array($existingSpecs)) {
                foreach ($existingSpecs as $key => $value) {
                    if (!str_starts_with($key, '_')) {
                        $mergedSpecs[$key] = $value;
                    }
                }
            }
        }
        
        // Handle special fields (_key_features, _faqs, _warranty_info)
        // These are stored in specifications but also can be sent separately
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
        
        // Only set specifications if there's actual data
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
        
        return response()->json($this->transformProductWithImages($product->load(['categories:id,name', 'tags:id,name'])));
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

    public function destroy($id)
    {
        // Support both id and slug for route model binding
        $product = Product::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
