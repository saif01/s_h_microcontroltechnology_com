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
        Schema::create('about', function (Blueprint $table) {
            $table->id();
            $table->json('hero')->nullable(); // {overline, title, subtitle}
            $table->json('story')->nullable(); // {overline, title, description, image, stats: [{value, label}]}
            $table->string('values_title')->nullable();
            $table->text('values_subtitle')->nullable();
            $table->json('values')->nullable(); // [{title, description, icon}]
            $table->string('team_overline')->nullable();
            $table->string('team_title')->nullable();
            $table->json('team')->nullable(); // [{name, role, image, linkedin, twitter}]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about');
    }
};
