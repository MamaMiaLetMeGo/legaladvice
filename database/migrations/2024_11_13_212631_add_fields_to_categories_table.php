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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('description');
            $table->string('color', 7)->nullable()->after('is_featured');
            $table->string('icon', 50)->nullable()->after('color');
            $table->string('meta_title', 60)->nullable()->after('icon');
            $table->string('meta_description', 160)->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'color',
                'icon',
                'meta_title',
                'meta_description',
            ]);
        });
    }
};
