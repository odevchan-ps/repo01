<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_timelines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('task_name', 255);
            $table->string('status', 50);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration')->nullable(); // 秒単位で処理時間を記録
            $table->text('error_message')->nullable();
            $table->timestamps(); // created_at, updated_atを自動的に管理
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_timelines');
    }
}
