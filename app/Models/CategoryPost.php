<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryPost extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'category_post';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'blog_post_id',
    ];

    /**
     * Get the category that owns this pivot.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the blog post that owns this pivot.
     */
    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }
}

