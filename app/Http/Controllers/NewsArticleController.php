<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsArticleController extends Controller
{
    // 一覧表示
    public function index()
    {
        $newsArticles = NewsArticle::orderBy('created_at', 'desc')->paginate(20);
        return view('news_articles.index', compact('newsArticles'));
    }

    // 新規作成フォームの表示
    public function create()
    {
        return view('news_articles.create');
    }

    // // 新規データの保存
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'site_cd' => 'required|string|max:2',
    //         'title' => 'required|string|max:255',
    //         'url' => 'required|string|max:255',
    //         'summary' => 'nullable|string',
    //         'published_at' => 'required|date',
    //         'news_category_cd' => 'required|string|max:2',
    //         'created_at' => 'required|date',
    //         'processed' => 'required|boolean',
    //         'source_id' => 'nullable|string|max:50',
    //         'collection_method_cd' => 'required|string|max:2'
    //     ]);

    //     NewsArticle::create($request->all());

    //     return redirect()->route('news_articles.index')
    //                      ->with('success', 'News article created successfully.');
    // }

    // 詳細表示
    public function show(NewsArticle $newsArticle)
    {
        return view('news_articles.show', compact('newsArticle'));
    }

    // 編集フォームの表示
    public function edit(NewsArticle $newsArticle)
    {
        return view('news_articles.edit', compact('newsArticle'));
    }

    // // 更新
    // public function update(Request $request, NewsArticle $newsArticle)
    // {
    //     $request->validate([
    //         'site_cd' => 'required|string|max:2',
    //         'title' => 'required|string|max:255',
    //         'url' => 'required|string|max:255',
    //         'summary' => 'nullable|string',
    //         'published_at' => 'required|date',
    //         'news_category_cd' => 'required|string|max:2',
    //         'created_at' => 'required|date',
    //         'processed' => 'required|boolean',
    //         'source_id' => 'nullable|string|max:50',
    //         'collection_method_cd' => 'required|string|max:2'
    //     ]);

    //     $newsArticle->update($request->all());

    //     return redirect()->route('news_articles.index')
    //                      ->with('success', 'News article updated successfully.');
    // }

    // 削除
    public function destroy(NewsArticle $newsArticle)
    {
        $newsArticle->delete();

        return redirect()->route('news_articles.index')
                         ->with('success', 'News article deleted successfully.');
    }
}
