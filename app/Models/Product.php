<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'sku',
        'brand',
        'short_description',
        'description',
        'thumbnail',
        'images',
        'price',
        'discount_percent',
        'discounted_price',
        'price_range',
        'show_price',
        'availability',
        'rating',
        'rating_count',
        'features',
        'specifications',
        'downloads',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'published',
        'featured',
        'stock',
        'order',
    ];

    protected $casts = [
        'published' => 'boolean',
        'featured' => 'boolean',
        'show_price' => 'boolean',
        'order' => 'integer',
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'rating_count' => 'integer',
        'stock' => 'integer',
        'images' => 'array',
        'features' => 'array',
        'specifications' => 'array',
        'downloads' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id')->where('type', 'product');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_product', 'product_id', 'tag_id')->where('type', 'product');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('status', 'approved');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Calculate and update product rating from approved reviews
     */
    public function updateRatingFromReviews()
    {
        $reviews = $this->reviews()->where('status', 'approved')->get();
        
        if ($reviews->isEmpty()) {
            $this->rating = 0;
            $this->rating_count = 0;
        } else {
            $this->rating = round($reviews->avg('rating'), 2);
            $this->rating_count = $reviews->count();
        }
        
        $this->save();
        
        return $this;
    }

    /**
     * Get rating statistics
     */
    public function getRatingStatsAttribute()
    {
        $reviews = $this->reviews()->where('status', 'approved')->get();
        
        if ($reviews->isEmpty()) {
            return [
                'average' => 0,
                'count' => 0,
                'distribution' => [
                    5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
                ],
                'percentage_distribution' => [
                    5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
                ]
            ];
        }

        $distribution = [
            5 => $reviews->where('rating', '>=', 4.5)->count(),
            4 => $reviews->whereBetween('rating', [3.5, 4.4])->count(),
            3 => $reviews->whereBetween('rating', [2.5, 3.4])->count(),
            2 => $reviews->whereBetween('rating', [1.5, 2.4])->count(),
            1 => $reviews->where('rating', '<', 1.5)->count(),
        ];

        $totalCount = $reviews->count();
        $percentageDistribution = array_map(
            fn($count) => $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0,
            $distribution
        );

        return [
            'average' => round($reviews->avg('rating'), 2),
            'count' => $totalCount,
            'distribution' => $distribution,
            'percentage_distribution' => $percentageDistribution
        ];
    }

    /**
     * Check if rating is valid (between 0 and 5)
     */
    public function setRatingAttribute($value)
    {
        // Ensure rating is between 0 and 5
        $this->attributes['rating'] = max(0, min(5, floatval($value)));
    }

    /**
     * Check if rating_count is valid (non-negative)
     */
    public function setRatingCountAttribute($value)
    {
        // Ensure rating_count is non-negative
        $this->attributes['rating_count'] = max(0, intval($value));
    }

    /**
     * Get formatted rating display
     */
    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating, 1) . ' / 5.0';
    }

    /**
     * Get rating stars display
     */
    public function getRatingStarsAttribute()
    {
        $fullStars = floor($this->rating);
        $hasHalfStar = ($this->rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);

        return [
            'full' => $fullStars,
            'half' => $hasHalfStar ? 1 : 0,
            'empty' => $emptyStars
        ];
    }
}
