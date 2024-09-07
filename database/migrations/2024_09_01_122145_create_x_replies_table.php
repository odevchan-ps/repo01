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
        Schema::create('x_replies', function (Blueprint $table) {
            $table->integer('reply_id', true);
            $table->string('x_post_id', 50)->nullable()->index('idx_x_post_id_replies');
            $table->string('reply_post_id', 50);
            $table->string('user_id', 50);
            $table->dateTime('created_at');
            $table->text('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_replies');
    }
};
