@extends('layouts.master')

@section('content')
<div class="container">
  <h1>記事詳細</h1>

  <div class="mb-3">
    @if($previous)
      <a href="{{ route('news_articles.show', $previous->article_id) }}"
         class="btn btn-outline-secondary">&laquo; 前へ</a>
    @endif
    <a href="{{ route('news_articles.management') }}" class="btn btn-light">一覧に戻る</a>
    @if($next)
      <a href="{{ route('news_articles.show', $next->article_id) }}"
         class="btn btn-outline-secondary">次へ &raquo;</a>
    @endif
  </div>

  {{-- ニュースデータ --}}
  <h2>ニュースデータ</h2>
  <ul class="list-group mb-4">
    <li class="list-group-item"><strong>作成日時:</strong> {{ $news_article->created_at }}</li>
    <li class="list-group-item"><strong>ソース:</strong> {{ $sourceMapping[$news_article->site_cd] ?? $news_article->site_cd }}</li>
    <li class="list-group-item"><strong>カテゴリ:</strong> {{ $categoryMapping[$news_article->news_category_cd] ?? $news_article->news_category_cd }}</li>
    <li class="list-group-item"><strong>タイトル:</strong> {{ $news_article->title }}</li>
    <li class="list-group-item"><strong>サマリ:</strong> {{ $news_article->summary }}</li>
  </ul>

  {{-- プロンプトデータ --}}
  @if($news_article->prompt)
    <h2>プロンプトデータ</h2>
    <ul class="list-group mb-4">
      <li class="list-group-item"><strong>作成日時:</strong> {{ $news_article->prompt->created_at }}</li>
      <li class="list-group-item"><strong>内容:</strong> {{ $news_article->prompt->prompt_text }}</li>
    </ul>
  @endif

  @if(optional($news_article->prompt)->generatedPosts->isNotEmpty())
    <h2>生成ポストデータ</h2>
    {{-- 生成ポストが複数ある場合はそれぞれ表示 --}}
    @foreach($news_article->prompt->generatedPosts as $post)
      <ul class="list-group mb-4">
        <li class="list-group-item">
          <strong>作成日時:</strong> {{ $post->created_at }}
        </li>
        <li class="list-group-item">
          <strong>内容:</strong> {{ $post->generated_text }}
        </li>
      </ul>
    @endforeach
  @endif
</div>
@endsection
