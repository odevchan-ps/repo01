@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Code List</h1>

    <!-- パーシャルビューをインクルード -->
    @include('components.pagination', ['items' => $codeLists, 'createRoute' => route('code_list.create')])

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Main Code</th>
                <th>Main Name</th>
                <th>Sub Code</th>
                <th>Sub Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($codeLists as $code)
            <tr>
                <td>{{ $code->main_cd }}</td>
                <td>{{ $code->main_name }}</td>
                <td>{{ $code->sub_cd }}</td>
                <td>{{ $code->sub_name }}</td>
                <td>
                    <div class="d-flex">
                        <a href="{{ route('code_list.edit', [$code->main_cd, $code->sub_cd]) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <form action="{{ route('code_list.destroy', [$code->main_cd, $code->sub_cd]) }}" method="POST">
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
    @include('components.pagination', ['items' => $codeLists, 'createRoute' => route('code_list.create')])
</div>
@endsection
