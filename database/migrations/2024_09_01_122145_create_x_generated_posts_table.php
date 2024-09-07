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
        Schema::create('x_generated_posts', function (Blueprint $table) {
            $table->integer('generated_post_id', true);
            $table->integer('prompt_id')->nullable()->index('prompt_id');
            $table->text('generated_text');
            $table->dateTime('created_at')->index('idx_created_at_generated_posts');
            $table->string('x_post_id', 50)->nullable()->index('x_post_id');
            $table->integer('x_error_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_generated_posts');
    }
};
