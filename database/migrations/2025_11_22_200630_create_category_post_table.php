<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('blog_post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate category-post relationships
            $table->unique(['category_id', 'blog_post_id']);
            
            // Add indexes for better query performance
            $table->index('category_id');
            $table->index('blog_post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_post');
    }
};
