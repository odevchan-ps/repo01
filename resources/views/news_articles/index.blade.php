@extends('layouts.master')

@section('content')
<div class="container">
    <h1>News Articles</h1>

    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $newsArticles, 'createRoute' => route('news_articles.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Article ID</th>
                <th>Site Code</th>
                <th>Title</th>
                <th>URL</th>
                <th>Summary</th>
                <th>Published At</th>
                <th>Prompt ID</th> <!-- processedからprompt_idに変更 -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($newsArticles as $article)
            <tr>
                <td>{{ $article->article_id }}</td>
                <td>{{ $article->site_cd }}</td>
                <td>{{ Str::limit($article->title, 50) }}</td>
                <td><a href="{{ $article->url }}" target="_blank">{{ Str::limit($article->url, 30) }}</a></td>
                <td>{{ Str::limit($article->summary, 50) }}</td>
                <td>{{ $article->published_at }}</td>
                <td>{{ $article->prompt_id }}</td> <!-- processedからprompt_idに変更 -->
                <td>
                    <div class="d-flex">
                        <a href="{{ route('news_articles.edit', $article->article_id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('news_articles.destroy', $article->article_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- パーシャルビューを再度インクルード（下部ページネーション） -->
    @include('components.pagination', ['items' => $newsArticles, 'createRoute' => route('news_articles.create')])
</div>
@endsection
