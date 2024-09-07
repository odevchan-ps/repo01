<?php

namespace App\Http\Controllers;

use App\Models\CodeList;
use Illuminate\Http\Request;

class CodeListController extends Controller
{
    // 一覧表示
    public function index()
    {
        $codeLists = CodeList::orderBy('main_cd', 'asc')->paginate(20);
        return view('code_list.index', compact('codeLists'));
    }

    // 新規作成フォームの表示
    public function create()
    {
        return view('code_list.create');
    }

    // 新規データの保存
    public function store(Request $request)
    {
        $request->validate([
            'main_cd' => 'required|string|max:2',
            'main_name' => 'required|string|max:100',
            'sub_cd' => 'required|string|max:2',
            'sub_name' => 'required|string|max:100'
        ]);

        CodeList::create($request->all());

        return redirect()->route('code_list.index')
                         ->with('success', 'Code created successfully.');
    }

    // 詳細表示
    public function show(CodeList $codeList)
    {
        return view('code_list.show', compact('codeList'));
    }

    // 編集フォームの表示
    public function edit(CodeList $codeList)
    {
        return view('code_list.edit', compact('codeList'));
    }

    // 更新
    public function update(Request $request, CodeList $codeList)
    {
        $request->validate([
            'main_cd' => 'required|string|max:2',
            'main_name' => 'required|string|max:100',
            'sub_cd' => 'required|string|max:2',
            'sub_name' => 'required|string|max:100'
        ]);

        $codeList->update($request->all());

        return redirect()->route('code_list.index')
                         ->with('success', 'Code updated successfully.');
    }

    // 削除
    public function destroy(CodeList $codeList)
    {
        $codeList->delete();

        return redirect()->route('code_list.index')
                         ->with('success', 'Code deleted successfully.');
    }
}
