<?php

namespace App\Http\Controllers;

use App\Models\XReply;
use Illuminate\Http\Request;

class XReplyController extends Controller
{
    public function index()
    {
        $replies = XReply::orderBy('created_at', 'desc')->paginate(20);
        return view('x_replies.index', compact('replies'));
    }

    public function create()
    {
        return view('x_replies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'reply_post_id' => 'required|string|max:50',
            'user_id' => 'required|string|max:50',
            'text' => 'required',
            'created_at' => 'required|date',
        ]);

        XReply::create($request->all());

        return redirect()->route('x_replies.index')
                         ->with('success', 'Reply created successfully.');
    }

    public function show(XReply $xReply)
    {
        return view('x_replies.show', compact('xReply'));
    }

    public function edit(XReply $xReply)
    {
        return view('x_replies.edit', compact('xReply'));
    }

    public function update(Request $request, XReply $xReply)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'reply_post_id' => 'required|string|max:50',
            'user_id' => 'required|string|max:50',
            'text' => 'required',
            'created_at' => 'required|date',
        ]);

        $xReply->update($request->all());

        return redirect()->route('x_replies.index')
                         ->with('success', 'Reply updated successfully.');
    }

    public function destroy(XReply $xReply)
    {
        $xReply->delete();

        return redirect()->route('x_replies.index')
                         ->with('success', 'Reply deleted successfully.');
    }
}
