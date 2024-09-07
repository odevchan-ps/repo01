<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XPrompt extends Model
{
    use HasFactory;

    // テーブル名の指定
    protected $table = 'x_prompts';

    // 主キーの指定
    protected $primaryKey = 'prompt_id';

    // 主キーのインクリメントを有効にする（デフォルトで有効）
    public $incrementing = true;

    // 主キーの型の指定
    protected $keyType = 'int';

    // マスアサインメント可能なフィールドの指定
    protected $fillable = [
        'prompt_text',
        'created_at'
    ];

}
