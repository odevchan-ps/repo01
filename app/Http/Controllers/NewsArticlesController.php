<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsArticle;
use App\Models\CodeList;
use App\Models\XGeneratedPost;

class NewsArticlesController extends Controller
{
    public function index()
    {
        // CodeListから必要なマッピングデータを取得
        $sourceMapping = CodeList::where('main_cd', '10')->pluck('sub_name', 'sub_cd')->toArray();
        $categoryMapping = CodeList::where('main_cd', '11')->pluck('sub_name', 'sub_cd')->toArray();

        // NewsArticlesデータを取得し、ページネーションを適用（20件表示）
        $newsArticles = NewsArticle::orderBy('created_at', 'desc')->paginate(20);

        return view('news_articles.management', compact('newsArticles', 'sourceMapping', 'categoryMapping'));
    }
}
