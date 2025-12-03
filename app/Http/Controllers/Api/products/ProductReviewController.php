<?php

namespace App\Http\Controllers\Api\products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * Get all reviews (Admin only)
     * This endpoint is protected by the 'manage-products' permission in routes
     */
    public function allReviews(Request $request)
    {
        // Permission check is handled by middleware in routes
        // This method will only be reached if user has manage-products permission
        
        $query = ProductReview::with(['product:id,title', 'user:id,name']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        // Search in comment, title, or reviewer name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('reviewer_name', 'like', "%{$search}%")
                  ->orWhere('reviewer_email', 'like', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $reviews = $query->paginate($request->input('per_page', 15));

        // Add reviewer_display_name attribute
        $reviews->getCollection()->transform(function ($review) {
            $review->reviewer_display_name = $review->reviewer_display_name;
            return $review;
        });

        return response()->json($reviews);
    }

    /**
     * Get reviews for a product
     */
    public function index(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $query = $product->reviews();

        // Filter by status (default to approved for public)
        $status = $request->input('status', 'approved');
        if ($status === 'all' && Auth::check() && Auth::user()->role === 'admin') {
            // Admin can see all reviews
        } else {
            $query->where('status', $status);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        // Filter by verified purchase
        if ($request->boolean('verified_only')) {
            $query->where('verified_purchase', true);
        }

        // Sort options
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $reviews = $query->with(['user:id,name', 'approvedBy:id,name'])
            ->paginate($request->input('per_page', 15));

        return response()->json($reviews);
    }

    /**
     * Get rating statistics for a product
     */
    public function stats($productId)
    {
        $product = Product::findOrFail($productId);
        
        return response()->json([
            'rating' => $product->rating,
            'rating_count' => $product->rating_count,
            'stats' => $product->rating_stats,
            'formatted_rating' => $product->formatted_rating,
            'rating_stars' => $product->rating_stars,
        ]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
            'reviewer_name' => 'required_without:user_id|string|max:255',
            'reviewer_email' => 'required_without:user_id|email|max:255',
        ]);

        // Check if user already reviewed this product
        if (Auth::check()) {
            $existingReview = ProductReview::where('product_id', $productId)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingReview) {
                return response()->json([
                    'message' => 'You have already reviewed this product. You can edit your existing review.',
                    'review_id' => $existingReview->id
                ], 422);
            }

            $validated['user_id'] = Auth::id();
        } else {
            // Guest review - check email
            $existingReview = ProductReview::where('product_id', $productId)
                ->where('reviewer_email', $validated['reviewer_email'])
                ->first();

            if ($existingReview) {
                return response()->json([
                    'message' => 'A review from this email already exists for this product.',
                ], 422);
            }
        }

        // Ensure rating is in 0.5 increments
        $validated['rating'] = round($validated['rating'] * 2) / 2;

        $validated['product_id'] = $productId;
        $validated['status'] = 'pending'; // Require approval

        // Check if verified purchase (you can implement this logic based on orders)
        // $validated['verified_purchase'] = $this->checkVerifiedPurchase($productId, Auth::id());

        $review = ProductReview::create($validated);

        return response()->json([
            'message' => 'Review submitted successfully. It will be visible after approval.',
            'review' => $review->load('user:id,name')
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, $productId, $reviewId)
    {
        $review = ProductReview::where('product_id', $productId)
            ->where('id', $reviewId)
            ->firstOrFail();

        // Check permission
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Admin can update any review
        } elseif (!Auth::check() || Auth::id() !== $review->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|required|numeric|min:0|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Ensure rating is in 0.5 increments
        if (isset($validated['rating'])) {
            $validated['rating'] = round($validated['rating'] * 2) / 2;
        }

        // Reset to pending if content changed (except for admin)
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            if ($review->status === 'approved' && 
                (isset($validated['rating']) || isset($validated['comment']))) {
                $validated['status'] = 'pending';
            }
        }

        $review->update($validated);

        return response()->json([
            'message' => 'Review updated successfully.',
            'review' => $review->fresh()->load('user:id,name')
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy($productId, $reviewId)
    {
        $review = ProductReview::where('product_id', $productId)
            ->where('id', $reviewId)
            ->firstOrFail();

        // Check permission
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Admin can delete any review
        } elseif (!Auth::check() || Auth::id() !== $review->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        // Recalculate product rating
        $product = Product::find($productId);
        if ($product) {
            $product->updateRatingFromReviews();
        }

        return response()->json([
            'message' => 'Review deleted successfully.'
        ]);
    }

    /**
     * Approve a review (Admin only)
     */
    public function approve($productId, $reviewId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review = ProductReview::where('product_id', $productId)
            ->where('id', $reviewId)
            ->firstOrFail();

        $review->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Recalculate product rating
        $product = Product::find($productId);
        if ($product) {
            $product->updateRatingFromReviews();
        }

        return response()->json([
            'message' => 'Review approved successfully.',
            'review' => $review->fresh()->load(['user:id,name', 'approvedBy:id,name'])
        ]);
    }

    /**
     * Reject a review (Admin only)
     */
    public function reject($productId, $reviewId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review = ProductReview::where('product_id', $productId)
            ->where('id', $reviewId)
            ->firstOrFail();

        $review->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return response()->json([
            'message' => 'Review rejected successfully.',
            'review' => $review->fresh()->load(['user:id,name', 'approvedBy:id,name'])
        ]);
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(Request $request, $productId, $reviewId)
    {
        $review = ProductReview::where('product_id', $productId)
            ->where('id', $reviewId)
            ->firstOrFail();

        $helpful = $request->boolean('helpful', true);

        if ($helpful) {
            $review->increment('helpful_count');
        } else {
            $review->increment('not_helpful_count');
        }

        return response()->json([
            'message' => 'Thank you for your feedback.',
            'helpful_count' => $review->helpful_count,
            'not_helpful_count' => $review->not_helpful_count,
            'helpfulness_score' => $review->helpfulness_score
        ]);
    }

    /**
     * Check if user has verified purchase (implement based on your order system)
     */
    private function checkVerifiedPurchase($productId, $userId)
    {
        // TODO: Implement based on your order/purchase system
        // Example:
        // return Order::where('user_id', $userId)
        //     ->whereHas('items', function($query) use ($productId) {
        //         $query->where('product_id', $productId);
        //     })
        //     ->where('status', 'completed')
        //     ->exists();
        
        return false;
    }
}

