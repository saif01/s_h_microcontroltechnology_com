<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_id',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'published',
        'views',
    ];

    protected $casts = [
        'published' => 'boolean',
        'views' => 'integer',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post', 'blog_post_id', 'category_id')
            ->using(CategoryPost::class)
            ->where('type', 'post')
            ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_post', 'blog_post_id', 'tag_id')->where('type', 'post');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
