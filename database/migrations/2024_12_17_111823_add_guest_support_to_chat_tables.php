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
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'session_id')) {
                $table->string('session_id')->nullable();
            }
            if (!Schema::hasColumn('conversations', 'is_guest')) {
                $table->boolean('is_guest')->default(false);
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'session_id')) {
                $table->string('session_id')->nullable();
            }
            if (!Schema::hasColumn('messages', 'is_guest')) {
                $table->boolean('is_guest')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'session_id')) {
                $table->dropColumn('session_id');
            }
            if (Schema::hasColumn('conversations', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'session_id')) {
                $table->dropColumn('session_id');
            }
            if (Schema::hasColumn('messages', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
        });
    }
};
