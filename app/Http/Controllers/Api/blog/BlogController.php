<?php

namespace App\Http\Controllers\Api\blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Support\MediaPath;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'categories', 'tags']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Filter by author
        if ($request->has('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'published_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['id', 'title', 'slug', 'published', 'published_at', 'views', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'published_at';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortBy, $sortDirection);
        
        if ($sortBy !== 'title') {
            $query->orderBy('title', 'asc');
        }

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $posts = $query->paginate($perPage);

        $posts->getCollection()->transform(function ($post) {
            return $this->transformPostWithImages($post);
        });
        
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'published' => 'boolean',
            'category_ids' => 'nullable|array',
            'tag_ids' => 'nullable|array',
            'tag_names' => 'nullable|array',
        ]);

        $postData = $validated;
        
        // Set author from authenticated user
        $postData['author_id'] = $request->user()->id;
        
        // Normalize image paths
        if (!empty($postData['featured_image'])) {
            $postData['featured_image'] = MediaPath::normalize($postData['featured_image']);
        }
        if (!empty($postData['og_image'])) {
            $postData['og_image'] = MediaPath::normalize($postData['og_image']);
        }
        
        // Set published_at if published is true and published_at is not set
        if (($postData['published'] ?? false) && empty($postData['published_at'])) {
            $postData['published_at'] = now();
        }

        $post = BlogPost::create($postData);
        
        // Sync categories (filter by type='post')
        if ($request->has('category_ids') && is_array($request->category_ids)) {
            $post->categories()->sync($request->category_ids);
        }
        
        // Sync tags - handle both tag_ids and tag_names
        if ($request->has('tag_ids') && is_array($request->tag_ids)) {
            $post->tags()->sync($request->tag_ids);
        } elseif ($request->has('tag_names') && is_array($request->tag_names)) {
            $tagIds = [];
            foreach ($request->tag_names as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName, 'type' => 'post'],
                        ['slug' => Str::slug($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $post->tags()->sync($tagIds);
        }
        
        return response()->json(
            $this->transformPostWithImages($post->load(['author', 'categories', 'tags'])),
            201
        );
    }

    public function show(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $post = BlogPost::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $post = $post->load(['author', 'categories', 'tags']);
        
        return response()->json($this->transformPostWithImages($post));
    }

    public function update(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $post = BlogPost::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:blog_posts,slug,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|required|string',
            'featured_image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'published' => 'boolean',
            'category_ids' => 'nullable|array',
            'tag_ids' => 'nullable|array',
            'tag_names' => 'nullable|array',
        ]);

        $postData = $validated;
        
        // Normalize image paths
        if (array_key_exists('featured_image', $postData) && !empty($postData['featured_image'])) {
            $postData['featured_image'] = MediaPath::normalize($postData['featured_image']);
        }
        if (array_key_exists('og_image', $postData) && !empty($postData['og_image'])) {
            $postData['og_image'] = MediaPath::normalize($postData['og_image']);
        }
        
        // Set published_at if published is true and published_at is not set
        if (($postData['published'] ?? false) && empty($postData['published_at']) && !$post->published_at) {
            $postData['published_at'] = now();
        }
        
        // Update post
        $post->update($postData);
        
        // Sync categories
        if ($request->has('category_ids')) {
            $post->categories()->sync($request->category_ids ?? []);
        }
        
        // Sync tags - handle both tag_ids and tag_names
        if ($request->has('tag_ids') && is_array($request->tag_ids)) {
            $post->tags()->sync($request->tag_ids);
        } elseif ($request->has('tag_names') && is_array($request->tag_names)) {
            $tagIds = [];
            foreach ($request->tag_names as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName, 'type' => 'post'],
                        ['slug' => Str::slug($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $post->tags()->sync($tagIds);
        }
        
        return response()->json($this->transformPostWithImages($post->load(['author', 'categories', 'tags'])));
    }

    private function transformPostWithImages(BlogPost $post): BlogPost
    {
        if (!empty($post->featured_image)) {
            $post->featured_image = MediaPath::url($post->featured_image);
        }

        if (!empty($post->og_image)) {
            $post->og_image = MediaPath::url($post->og_image);
        }

        return $post;
    }

    public function destroy($id)
    {
        // Support both id and slug for route model binding
        $post = BlogPost::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $post->delete();
        return response()->json(['message' => 'Blog post deleted successfully']);
    }
}

