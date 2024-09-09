<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('task_timelines', function (Blueprint $table) {
            $table->string('operation_type')->after('error_message');  // 処理タイプ
            $table->text('affected_ids')->nullable()->after('operation_type');  // 処理対象のIDリスト
            $table->integer('record_count')->nullable()->after('affected_ids');  // 処理したレコード数
            $table->text('additional_info')->nullable()->after('record_count');  // 処理に関する追加情報
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('task_timelines', function (Blueprint $table) {
            $table->dropColumn('operation_type');
            $table->dropColumn('affected_ids');
            $table->dropColumn('record_count');
            $table->dropColumn('additional_info');
        });
    }
};
