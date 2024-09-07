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
        Schema::table('x_feedbacks', function (Blueprint $table) {
            $table->foreign(['x_post_id'], 'x_feedbacks_ibfk_1')->references(['x_post_id'])->on('x_posts')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('x_feedbacks', function (Blueprint $table) {
            $table->dropForeign('x_feedbacks_ibfk_1');
        });
    }
};
