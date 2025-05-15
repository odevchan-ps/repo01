<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(){
        Schema::table('news_articles', function(Blueprint $t){
            $t->unique('url', 'uq_url');
        });
    }
    public function down(){
        Schema::table('news_articles', function(Blueprint $t){
            $t->dropUnique('uq_url');
        });
    }
};
