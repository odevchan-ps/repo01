@extends('layouts.master')

@section('content')
<div class="container">
    <h1>X Generated Posts</h1>

    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $generatedPosts, 'createRoute' => route('x_generated_posts.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Generated Post ID</th>
                <th>Prompt ID</th>
                <th>Generated Text</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($generatedPosts as $generatedPost)
            <tr>
                <td>{{ $generatedPost->generated_post_id }}</td>
                <td>{{ $generatedPost->prompt_id }}</td>
                <td>{{ Str::limit($generatedPost->generated_text, 50) }}</td>
                <td>{{ $generatedPost->created_at }}</td>
                <td>
                    <div class="d-flex">
                        <a href="{{ route('x_generated_posts.edit', $generatedPost->generated_post_id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('x_generated_posts.destroy', $generatedPost->generated_post_id) }}" method="POST">
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
    @include('components.pagination', ['items' => $generatedPosts, 'createRoute' => route('x_generated_posts.create')])
</div>
@endsection
