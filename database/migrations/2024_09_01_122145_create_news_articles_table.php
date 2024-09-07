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
        Schema::create('news_articles', function (Blueprint $table) {
            $table->integer('article_id', true);
            $table->char('site_cd', 2)->index('idx_site_cd');
            $table->string('title');
            $table->string('url');
            $table->text('summary')->nullable();
            $table->dateTime('published_at')->index('idx_published_at');
            $table->char('news_category_cd', 2)->index('idx_news_category_cd');
            $table->dateTime('created_at');
            $table->integer('prompt_id')->nullable();
            $table->string('source_id', 50)->nullable()->index('idx_source_id');
            $table->char('collection_method_cd', 2)->index('idx_collection_method_cd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
