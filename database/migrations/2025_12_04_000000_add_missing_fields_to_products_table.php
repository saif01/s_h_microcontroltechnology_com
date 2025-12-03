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
        Schema::table('products', function (Blueprint $table) {
            // Add brand field after sku
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('sku');
            }

            // Add discount fields after price
            if (!Schema::hasColumn('products', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'discounted_price')) {
                $table->decimal('discounted_price', 10, 2)->nullable()->after('discount_percent');
            }

            // Add features field after specifications
            if (!Schema::hasColumn('products', 'features')) {
                $table->json('features')->nullable()->after('specifications');
            }
        });

        // Availability field after stock (separate to handle existing data)
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'availability')) {
                $table->enum('availability', ['in_stock', 'out_of_stock', 'pre_order', 'coming_soon'])
                    ->default('in_stock')
                    ->after('stock');
            }

            // Add rating fields after availability
            if (!Schema::hasColumn('products', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0)->after('availability');
            }
            if (!Schema::hasColumn('products', 'rating_count')) {
                $table->integer('rating_count')->default(0)->after('rating');
            }
        });

        // Add indexes for better query performance
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'brand')) {
                $table->index('brand');
            }
            if (Schema::hasColumn('products', 'availability')) {
                $table->index('availability');
            }
            if (Schema::hasColumn('products', 'rating')) {
                $table->index('rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['brand']);
            $table->dropIndex(['availability']);
            $table->dropIndex(['rating']);

            // Then drop columns
            $table->dropColumn([
                'brand',
                'discount_percent',
                'discounted_price',
                'features',
                'availability',
                'rating',
                'rating_count',
            ]);
        });
    }
};

