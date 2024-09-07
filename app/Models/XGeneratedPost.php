<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XGeneratedPost extends Model
{
    use HasFactory;

    // テーブル名の指定
    protected $table = 'x_generated_posts';

    // 主キーの指定
    protected $primaryKey = 'generated_post_id';

    // 主キーのインクリメントを有効にする（デフォルトで有効）
    public $incrementing = true;

    // 主キーの型の指定
    protected $keyType = 'int';

    // マスアサインメント可能なフィールドの指定
    protected $fillable = [
        'prompt_id',
        'generated_text',
        'created_at',
        'x_post_id'
    ];
}
