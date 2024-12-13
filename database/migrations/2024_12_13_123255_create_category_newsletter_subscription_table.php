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
        Schema::create('category_newsletter_subscription', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade');
                
            $table->foreignId('newsletter_subscription_id')
                ->constrained('newsletter_subscriptions')
                ->onDelete('cascade')
                ->name('cat_news_sub_news_id_foreign'); // Shorter constraint name
                
            $table->primary(['category_id', 'newsletter_subscription_id'], 
                'cat_news_sub_primary'); // Shorter primary key name
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_newsletter_subscription');
    }
};