@extends('layouts.master')

@section('content')
<div class="container">
    <h1>ニュース管理</h1>

    <!-- パーシャルビューをインクルード（上部） -->
    @include('components.pagination', ['items' => $newsArticles, 'createRoute' => route('news_articles.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>作成日時</th>
                <th>ソース</th>
                <th>カテゴリ</th>
                <th>タイトル</th>
                <th>プロID</th>
                <th>生成ID</th>
                <th>XID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($newsArticles as $article)
            <tr class="clickable-row"
                data-href="{{ route('news_articles.show', $article->article_id) }}">
                <td>{{ $article->created_at }}</td>
                <td>{{ $sourceMapping[$article->site_cd] ?? '' }}</td>
                <td>{{ $categoryMapping[$article->news_category_cd] ?? '' }}</td>
                <td>{{ $article->title }}</td>
                <td>{{ $article->prompt_id }}</td>
                <td>
                    @php
                        $generatedPost = \App\Models\XGeneratedPost::find($article->x_post_id);
                    @endphp
                    {{ $generatedPost ? $generatedPost->generated_post_id : '' }}
                </td>
                <td>{{ $article->x_post_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.clickable-row').forEach(function(row) {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function() {
        window.location.href = row.dataset.href;
        });
    });
    });
    </script>
    @endpush

    <!-- パーシャルビューをインクルード（下部） -->
    @include('components.pagination', ['items' => $newsArticles, 'createRoute' => route('news_articles.create')])
</div>
@endsection
