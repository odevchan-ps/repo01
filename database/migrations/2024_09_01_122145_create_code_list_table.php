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
        Schema::create('code_list', function (Blueprint $table) {
            $table->char('main_cd', 2);
            $table->string('main_name', 100);
            $table->char('sub_cd', 2);
            $table->string('sub_name', 100);

            $table->primary(['main_cd', 'sub_cd']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_list');
    }
};
