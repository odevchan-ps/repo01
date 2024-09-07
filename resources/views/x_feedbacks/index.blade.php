@extends('layouts.master')

@section('content')
<div class="container">
    <h1>X Feedbacks</h1>

    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $feedbacks, 'createRoute' => route('x_feedbacks.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Feedback ID</th>
                <th>X Post ID</th>
                <th>Feedback Type</th>
                <th>Count</th>
                <th>Retrieved At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feedbacks as $feedback)
            <tr>
                <td>{{ $feedback->feedback_id }}</td>
                <td>{{ $feedback->x_post_id }}</td>
                <td>{{ $feedback->feedback_type }}</td>
                <td>{{ $feedback->count }}</td>
                <td>{{ $feedback->retrieved_at }}</td>
                <td>
                    <div class="d-flex">
                        <a href="{{ route('x_feedbacks.edit', $feedback->feedback_id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('x_feedbacks.destroy', $feedback->feedback_id) }}" method="POST">
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
    @include('components.pagination', ['items' => $feedbacks, 'createRoute' => route('x_feedbacks.create')])
</div>
@endsection
