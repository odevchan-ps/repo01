@extends('layouts.master')

@section('content')
<div class="container">
    <h1>X Prompts</h1>

    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $prompts, 'createRoute' => route('x_prompts.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Prompt ID</th>
                <th>Prompt Text</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prompts as $prompt)
            <tr>
                <td>{{ $prompt->prompt_id }}</td>
                <td>{{ Str::limit($prompt->prompt_text, 50) }}</td>
                <td>{{ $prompt->created_at }}</td>
                <td>
                    <div class="d-flex">
                        <a href="{{ route('x_prompts.edit', $prompt->prompt_id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('x_prompts.destroy', $prompt->prompt_id) }}" method="POST">
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
    @include('components.pagination', ['items' => $prompts, 'createRoute' => route('x_prompts.create')])
</div>
@endsection
