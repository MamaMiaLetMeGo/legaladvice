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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('breadcrumb')->nullable();
            $table->text('body_content');
            $table->string('featured_image')->nullable();
            $table->string('categories')->nullable();
            $table->string('slug')->unique();
            $table->string('video_url')->nullable();
            $table->date('published_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
