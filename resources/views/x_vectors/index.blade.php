@extends('layouts.master')

@section('content')
<div class="container">
    <h1>X Vectors</h1>

    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $vectors, 'createRoute' => route('x_vectors.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Vector ID</th>
                <th>X Post ID</th>
                <th>Vector</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vectors as $vector)
            <tr>
                <td>{{ $vector->vector_id }}</td>
                <td>{{ $vector->x_post_id }}</td>
                <td>{{ Str::limit($vector->vector, 50) }}</td>
                <td>{{ $vector->created_at }}</td>
                <td>
                    <div class="d-flex">
                        <a href="{{ route('x_vectors.edit', $vector->vector_id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('x_vectors.destroy', $vector->vector_id) }}" method="POST">
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
    @include('components.pagination', ['items' => $vectors, 'createRoute' => route('x_vectors.create')])
</div>
@endsection
