<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('breadcrumb')->nullable();
            $table->text('body_content');
            $table->string('featured_image')->nullable();
            $table->string('slug')->unique();
            $table->string('video_url')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->date('published_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add indexes for better performance
            $table->index('published_date');
            $table->index('status');
            $table->fulltext(['title', 'body_content']); // Enables full-text search
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
