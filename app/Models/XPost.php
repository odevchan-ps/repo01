<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XPost extends Model
{
    use HasFactory;

    protected $table = 'x_posts'; // 既存のテーブル名を指定

    protected $primaryKey = 'x_post_id'; // 主キーのカラム名を指定

    public $incrementing = false; // 主キーが非数値（VARCHARなど）の場合

    protected $keyType = 'string'; // 主キーの型を指定

    protected $fillable = [
        'x_post_id',
        'user_id',
        'created_at',
        'text',
        'prompt_id',
        'likes_count',
        'retweets_count',
        'replies_count',
        'impressions_count',
        'deleted_at'
    ];
}
