<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    use HasFactory;

    // 挿入可能なカラムを指定
    protected $fillable = [
        'title',
        'url',
        'summary',
        'published_at',
        'news_category_cd',
        'site_cd',
        'created_at',
        'collection_method_cd',
    ];
}