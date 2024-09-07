@extends('layouts.master')

@section('content')
<div class="container">
    <h1>X Replies</h1>

    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $replies, 'createRoute' => route('x_replies.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Reply ID</th>
                <th>X Post ID</th>
                <th>Reply Post ID</th>
                <th>User ID</th>
                <th>Text</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($replies as $reply)
            <tr>
                <td>{{ $reply->reply_id }}</td>
                <td>{{ $reply->x_post_id }}</td>
                <td>{{ $reply->reply_post_id }}</td>
                <td>{{ $reply->user_id }}</td>
                <td>{{ Str::limit($reply->text, 50) }}</td>
                <td>{{ $reply->created_at }}</td>
                <td>
                    <div class="d-flex">
                        <a href="{{ route('x_replies.edit', $reply->reply_id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('x_replies.destroy', $reply->reply_id) }}" method="POST">
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
    @include('components.pagination', ['items' => $replies, 'createRoute' => route('x_replies.create')])
</div>
@endsection
