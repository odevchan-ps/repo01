<?php

namespace App\Http\Controllers;

use App\Models\XPost;
use Illuminate\Http\Request;

class XPostController extends Controller
{
    public function index()
    {
        $posts = XPost::orderBy('created_at', 'desc')->paginate(20);
        return view('x_posts.index', compact('posts'));
    }

    public function create()
    {
        return view('x_posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'user_id' => 'required|string|max:50',
            'text' => 'required',
            'created_at' => 'required|date',
        ]);

        XPost::create($request->all());

        return redirect()->route('x_posts.index')
                         ->with('success', 'Post created successfully.');
    }

    public function show(XPost $xPost)
    {
        return view('x_posts.show', compact('xPost'));
    }

    public function edit(XPost $xPost)
    {
        return view('x_posts.edit', compact('xPost'));
    }

    public function update(Request $request, XPost $xPost)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'user_id' => 'required|string|max:50',
            'text' => 'required',
            'created_at' => 'required|date',
        ]);

        $xPost->update($request->all());

        return redirect()->route('x_posts.index')
                         ->with('success', 'Post updated successfully.');
    }

    public function destroy(XPost $xPost)
    {
        $xPost->delete();

        return redirect()->route('x_posts.index')
                         ->with('success', 'Post deleted successfully.');
    }
}
