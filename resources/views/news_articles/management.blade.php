@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="news-management-title">ニュース管理</h1>

    <!-- タイムライン表示 -->
    <div class="mb-3">
        <h3>タイムライン</h3>
        <table class="table table-bordered table-sm text-center align-middle table-timeline">
            <thead>
                <tr>
                    <th class="status">ステータス</th>
                    <th class="task-name">タスク名</th>
                    <th class="start-time">開始時間</th>
                    <th class="duration">処理時間（秒）</th>
                    <th>処理対象ID</th>
                    <th class="record-count">件数</th>
                    <th class="error-message">エラーメッセージ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timelines as $index => $timeline)
                <tr class="{{ $index === 0 ? 'highlight-row' : '' }}"> <!-- highlight-rowクラスを追加 -->
                    <td class="text-center status">{{ $timeline->status }}</td> <!-- ステータス列を中央寄せ -->
                    <td class="text-center task-name">{{ $timeline->task_name === 'nhk_manual_fetch' ? '手動' : '自動' }}</td> <!-- タスク名 -->
                    <td class="text-center start-time">{{ \Carbon\Carbon::parse($timeline->start_time)->format('Y-m-d H:i') }}</td> <!-- 秒を削除 -->
                    <td class="text-center duration">{{ $timeline->duration }}</td> <!-- 処理時間（秒）を中央寄せ -->
            
                    <!-- 処理対象ID列 -->
                    <td class="text-center record-count">
                        @php
                            $affectedIdsArray = explode(',', $timeline->affected_ids);
                            if (count($affectedIdsArray) >= 3) {
                                echo $affectedIdsArray[0] . '～' . end($affectedIdsArray);
                            } else {
                                echo implode(', ', $affectedIdsArray);
                            }
                        @endphp
                    </td>
                    <!-- 件数列 -->
                    <td class="text-center record-count">{{ $timeline->record_count }}</td> <!-- 件数を中央寄せ -->
                    <!-- エラーメッセージ列（非中央寄せ） -->
                    <td class="error-message">{{ $timeline->error_message }}</td> <!-- エラーメッセージ -->
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ページネーション表示 -->
        <div class="d-flex justify-content-center mb-3">
            {{ $timelines->links('vendor.pagination.custom') }}
        </div>
    </div>

    <!-- NHK手動取込ボタン -->
    <div class="mb-3">
        <form action="{{ route('news.import') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">NHK手動取込</button>
        </form>
    </div>

    <!-- ニュース記事の一覧表示 -->
    @include('components.pagination', ['items' => $newsArticles, 'createRoute' => route('news_articles.create')])

    <table class="table table-bordered table-sm text-center align-middle">
        <thead>
            <tr>
                <th>News No</th>
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
            <tr class="{{ in_array($article->article_id, $latestImportedArticleIds) ? 'highlight-row' : '' }}"> <!-- highlight-row クラスを追加 -->
                <td class="text-center">{{ $article->article_id }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($article->created_at)->format('Y-m-d H:i') }}</td>
                <td class="text-center">{{ $sourceMapping[$article->site_cd] ?? '' }}</td>
                <td class="text-center">{{ $categoryMapping[$article->news_category_cd] ?? '' }}</td>
                <td>{{ $article->title }}</td>
                <td class="text-center">{{ $article->prompt_id }}</td>
                <td class="text-center">
                    @php
                        $generatedPost = \App\Models\XGeneratedPost::find($article->x_post_id);
                    @endphp
                    {{ $generatedPost ? $generatedPost->generated_post_id : '' }}
                </td>
                <td class="text-center">{{ $article->x_post_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- パーシャルビューをインクルード（下部） -->
    @include('components.pagination', ['items' => $newsArticles, 'createRoute' => route('news_articles.create')])
</div>
@endsection
