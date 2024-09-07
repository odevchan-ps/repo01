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
        Schema::create('x_prompts', function (Blueprint $table) {
            $table->integer('prompt_id', true);
            $table->text('prompt_text');
            $table->dateTime('created_at')->index('idx_created_at_prompts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_prompts');
    }
};
