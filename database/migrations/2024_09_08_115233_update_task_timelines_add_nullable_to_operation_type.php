<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_timelines', function (Blueprint $table) {
            $table->string('operation_type')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('task_timelines', function (Blueprint $table) {
            $table->string('operation_type')->nullable(false)->change();
        });
    }
};
