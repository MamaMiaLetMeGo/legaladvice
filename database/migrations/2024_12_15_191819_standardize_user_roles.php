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
        Schema::table('users', function (Blueprint $table) {
            // Convert existing is_admin values to roles
            DB::statement("UPDATE users SET role = CASE WHEN is_admin = 1 THEN 'admin' ELSE 'user' END");
            
            // Drop the is_admin column
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            // Convert roles back to is_admin
            DB::statement("UPDATE users SET is_admin = CASE WHEN role = 'admin' THEN 1 ELSE 0 END");
        });
    }
};
