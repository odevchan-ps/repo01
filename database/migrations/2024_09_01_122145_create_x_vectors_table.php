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
        Schema::create('x_vectors', function (Blueprint $table) {
            $table->integer('vector_id', true);
            $table->string('x_post_id', 50)->nullable()->index('idx_x_post_id');
            $table->binary('vector')->nullable();
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_vectors');
    }
};
