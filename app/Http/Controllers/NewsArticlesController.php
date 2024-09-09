<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsArticle;
use App\Models\CodeList;
use App\Models\TaskTimeline;
use App\Services\NewsImportService;
use Carbon\Carbon;

class NewsArticlesController extends Controller
{
    protected $newsImportService;

    public function __construct(NewsImportService $newsImportService)
    {
        $this->newsImportService = $newsImportService;
    }

    public function index()
    {
        // CodeListから必要なマッピングデータを取得
        $sourceMapping = CodeList::where('main_cd', '10')->pluck('sub_name', 'sub_cd')->toArray();
        $categoryMapping = CodeList::where('main_cd', '11')->pluck('sub_name', 'sub_cd')->toArray();

        // NewsArticlesデータを取得し、ページネーションを適用（20件表示）
        $newsArticles = NewsArticle::orderBy('article_id', 'desc')->paginate(20);

        // タイムラインデータを取得（ページネーション: 10件表示）
        $timelines = TaskTimeline::whereIn('task_name', ['nhk_manual_fetch', 'nhk_auto_fetch'])
                                  ->orderBy('start_time', 'desc')
                                  ->paginate(10);

        // 最後に取込みを行った記事のarticle_idを取得
        $latestTimeline = TaskTimeline::where('task_name', 'nhk_manual_fetch')
                                    ->orWhere('task_name', 'nhk_auto_fetch')
                                    ->orderBy('start_time', 'desc')
                                    ->first();

        $latestImportedArticleIds = $latestTimeline ? explode(',', $latestTimeline->affected_ids) : [];

        return view('news_articles.management', compact('newsArticles', 'sourceMapping', 'categoryMapping', 'timelines', 'latestImportedArticleIds'));
    }

    public function importNews()
    {
        $success = $this->newsImportService->importNews('nhk_manual_fetch');

        if ($success) {
            return redirect()->route('news_articles.management')->with('success', 'News articles fetched and stored successfully.');
        } else {
            return redirect()->route('news_articles.management')->with('error', 'Error during news fetch.');
        }
    }
}