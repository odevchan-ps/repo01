<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XVector extends Model
{
    use HasFactory;

    // テーブル名の指定
    protected $table = 'x_vectors';

    // 主キーの指定
    protected $primaryKey = 'vector_id';

    // 主キーのインクリメントを有効にする（デフォルトで有効）
    public $incrementing = true;

    // 主キーの型の指定
    protected $keyType = 'int';

    // マスアサインメント可能なフィールドの指定
    protected $fillable = [
        'x_post_id',
        'vector',
        'created_at'
    ];

}
