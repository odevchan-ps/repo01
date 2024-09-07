<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XReply extends Model
{
    use HasFactory;

    // テーブル名の指定
    protected $table = 'x_replies';

    // 主キーの指定
    protected $primaryKey = 'reply_id';

    // 主キーのインクリメントを有効にする（デフォルトで有効）
    public $incrementing = true;

    // 主キーの型の指定
    protected $keyType = 'int';

    // マスアサインメント可能なフィールドの指定
    protected $fillable = [
        'x_post_id',
        'reply_post_id',
        'user_id',
        'created_at',
        'text'
    ];
}
