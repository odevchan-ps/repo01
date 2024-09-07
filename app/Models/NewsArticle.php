<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    use HasFactory;

    // テーブル名の指定
    protected $table = 'news_articles';

    // 主キーの指定
    protected $primaryKey = 'article_id';

    // 主キーのインクリメントを有効にする（デフォルトで有効）
    public $incrementing = true;

    // 主キーの型の指定
    protected $keyType = 'int';

    // マスアサインメント可能なフィールドの指定
    protected $fillable = [
        'site_cd',
        'title',
        'url',
        'summary',
        'published_at',
        'news_category_cd',
        'created_at',
        'prompt_id',
        'source_id',
        'collection_method_cd'
    ];
}
