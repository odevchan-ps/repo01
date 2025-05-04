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
        // カラムが存在するときだけ DROP
        if (Schema::hasColumn('news_articles', 'processed')) {
            Schema::table('news_articles', function (Blueprint $table) {
                $table->dropColumn('processed');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->boolean('processed')
            ->default(false)
            ->after('collection_method_cd');
        });
    }
};
