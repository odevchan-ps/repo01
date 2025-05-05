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

    /**
    * 記事詳細＋前後レコード取得
    */
    public function show(NewsArticle $news_article)
    {
    // ソース／カテゴリ名のマッピング取得
    $sourceMapping   = CodeList::where('main_cd','10')->pluck('sub_name','sub_cd')->toArray();
    $categoryMapping = CodeList::where('main_cd','11')->pluck('sub_name','sub_cd')->toArray();

    // 関連リレーションをロード
    $news_article->load('prompt.generatedPosts');

    // 前の記事 (article_id をキーに)
    $previous = NewsArticle::where('article_id', '<', $news_article->article_id)
        ->orderBy('article_id','desc')
        ->first();
    // 次の記事
    $next = NewsArticle::where('article_id', '>', $news_article->article_id)
        ->orderBy('article_id','asc')
        ->first();

    // 詳細ビューへ
    return view('news-management.show', compact(
        'news_article','previous','next','sourceMapping','categoryMapping'
    ));
    }
}
