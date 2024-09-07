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
        Schema::create('x_posts', function (Blueprint $table) {
            $table->string('x_post_id', 50)->primary();
            $table->string('user_id', 50);
            $table->dateTime('created_at');
            $table->text('text');
            $table->boolean('processed')->nullable()->default(false);
            $table->integer('likes_count')->nullable();
            $table->integer('retweets_count')->nullable();
            $table->integer('replies_count')->nullable();
            $table->integer('impressions_count')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_posts');
    }
};
