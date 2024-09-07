<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyProcessedToPromptIdInNewsArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news_articles', function (Blueprint $table) {
            if (Schema::hasColumn('news_articles', 'processed')) {
                // SQL文を使ってカラムの名前を変更し、型を変更する
                DB::statement('ALTER TABLE news_articles CHANGE COLUMN processed prompt_id INT NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_articles', function (Blueprint $table) {
            if (Schema::hasColumn('news_articles', 'prompt_id')) {
                // SQL文を使ってカラムの名前を元に戻し、型を変更する
                DB::statement('ALTER TABLE news_articles CHANGE COLUMN prompt_id processed TINYINT(1) DEFAULT 0');
            }
        });
    }
}
