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
        Schema::create('x_feedbacks', function (Blueprint $table) {
            $table->integer('feedback_id', true);
            $table->string('x_post_id', 50)->nullable()->index('idx_x_post_id_feedbacks');
            $table->enum('feedback_type', ['like', 'retweet', 'reply', 'impression'])->index('idx_feedback_type');
            $table->integer('count');
            $table->dateTime('retrieved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_feedbacks');
    }
};
