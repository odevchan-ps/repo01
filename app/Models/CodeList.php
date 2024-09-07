<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeList extends Model
{
    use HasFactory;

    // テーブル名の指定
    protected $table = 'code_list';

    // 主キーの指定
    protected $primaryKey = null; // 複合主キーの場合、主キーをnullに設定

    // 主キーのインクリメントを無効にする
    public $incrementing = false;

    // マスアサインメント可能なフィールドの指定
    protected $fillable = [
        'main_cd',
        'main_name',
        'sub_cd',
        'sub_name'
    ];
}
