<?php

namespace App\Http\Controllers;

use App\Models\XGeneratedPost;
use Illuminate\Http\Request;

class XGeneratedPostController extends Controller
{
    public function index()
    {
        $generatedPosts = XGeneratedPost::orderBy('created_at', 'desc')->paginate(20);
        return view('x_generated_posts.index', compact('generatedPosts'));
    }

    public function create()
    {
        return view('x_generated_posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'prompt_id' => 'required|integer',
            'generated_text' => 'required',
            'created_at' => 'required|date',
        ]);

        XGeneratedPost::create($request->all());

        return redirect()->route('x_generated_posts.index')
                         ->with('success', 'Generated Post created successfully.');
    }

    public function show(XGeneratedPost $xGeneratedPost)
    {
        return view('x_generated_posts.show', compact('xGeneratedPost'));
    }

    public function edit(XGeneratedPost $xGeneratedPost)
    {
        return view('x_generated_posts.edit', compact('xGeneratedPost'));
    }

    public function update(Request $request, XGeneratedPost $xGeneratedPost)
    {
        $request->validate([
            'prompt_id' => 'required|integer',
            'generated_text' => 'required',
            'created_at' => 'required|date',
        ]);

        $xGeneratedPost->update($request->all());

        return redirect()->route('x_generated_posts.index')
                         ->with('success', 'Generated Post updated successfully.');
    }

    public function destroy(XGeneratedPost $xGeneratedPost)
    {
        $xGeneratedPost->delete();

        return redirect()->route('x_generated_posts.index')
                         ->with('success', 'Generated Post deleted successfully.');
    }
}
