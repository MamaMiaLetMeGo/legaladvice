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
        Schema::table('posts', function (Blueprint $table) {
            // First, add the video column
            $table->string('video')->nullable();
            
            // Then remove the old video_url column if it exists
            if (Schema::hasColumn('posts', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Reverse the changes
            $table->dropColumn('video');
            $table->string('video_url')->nullable();
        });
    }
};
