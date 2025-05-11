<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('code_list')->insert([
            ['main_cd'=>'10','main_name'=>'news','sub_cd'=>'01','sub_name'=>'nhk'],
            ['main_cd'=>'20','main_name'=>'collection_method','sub_cd'=>'01','sub_name'=>'RSS'],
            ['main_cd'=>'11','main_name'=>'news_category','sub_cd'=>'01','sub_name'=>'政治・経済・社会'],
            ['main_cd'=>'11','main_name'=>'news_category','sub_cd'=>'02','sub_name'=>'エンタメ'],
            ['main_cd'=>'11','main_name'=>'news_category','sub_cd'=>'03','sub_name'=>'スポーツ'],
            ['main_cd'=>'11','main_name'=>'news_category','sub_cd'=>'04','sub_name'=>'IT・科学'],
            ['main_cd'=>'11','main_name'=>'news_category','sub_cd'=>'05','sub_name'=>'その他'],
        ]);
    }

    public function down()
    {
        // down時に挿入データを削除
        DB::table('code_list')->where(function($q){
            $entries = [
                ['10','01'], ['20','01'],
                ['11','01'], ['11','02'], ['11','03'], ['11','04'], ['11','05'],
            ];
            foreach ($entries as [$main, $sub]) {
                $q->orWhere(function($q2) use($main,$sub){
                    $q2->where('main_cd', $main)
                       ->where('sub_cd',  $sub);
                });
            }
        })->delete();
    }
};
