@extends('layouts.master')

@section('content')
<div class="container">
    <h1>X Posts</h1>
    
    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $posts, 'createRoute' => route('x_posts.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>X Post ID</th>
                <th>User ID</th>
                <th>Text</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr>
                <td>{{ $post->x_post_id }}</td>
                <td>{{ $post->user_id }}</td>
                <td>{{ Str::limit($post->text, 50) }}</td>
                <td>{{ $post->created_at }}</td>
                <td>
                    <div class="d-flex">
                        <a href="{{ route('x_posts.edit', $post->x_post_id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('x_posts.destroy', $post->x_post_id) }}" method="POST">
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
    @include('components.pagination', ['items' => $posts, 'createRoute' => route('x_posts.create')])
</div>
@endsection
