<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'title',
        'comment',
        'status',
        'verified_purchase',
        'helpful_count',
        'not_helpful_count',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the product that owns the review
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who wrote the review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the review
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get only approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get pending reviews
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get verified purchase reviews
     */
    public function scopeVerifiedPurchase($query)
    {
        return $query->where('verified_purchase', true);
    }

    /**
     * Get the reviewer display name
     */
    public function getReviewerDisplayNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->reviewer_name ?? 'Anonymous';
    }

    /**
     * Calculate helpfulness score
     */
    public function getHelpfulnessScoreAttribute()
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        if ($total === 0) {
            return 0;
        }
        return round(($this->helpful_count / $total) * 100, 1);
    }
}

