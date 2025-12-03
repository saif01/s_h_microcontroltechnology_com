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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Review content
            $table->string('reviewer_name')->nullable(); // For guest reviews if enabled
            $table->string('reviewer_email')->nullable();
            $table->decimal('rating', 2, 1); // 0.0 to 5.0 (0.5 increments)
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            
            // Review status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('verified_purchase')->default(false);
            
            // Helpful votes
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            // Moderation
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('product_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('rating');
            $table->index(['product_id', 'status']);
            
            // Prevent duplicate reviews from same user
            $table->unique(['product_id', 'user_id'], 'unique_product_user_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};

