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
        Schema::table('x_generated_posts', function (Blueprint $table) {
            $table->foreign(['prompt_id'], 'x_generated_posts_ibfk_1')->references(['prompt_id'])->on('x_prompts')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['x_post_id'], 'x_generated_posts_ibfk_2')->references(['x_post_id'])->on('x_posts')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('x_generated_posts', function (Blueprint $table) {
            $table->dropForeign('x_generated_posts_ibfk_1');
            $table->dropForeign('x_generated_posts_ibfk_2');
        });
    }
};
