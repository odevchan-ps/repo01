<?php

namespace App\Http\Controllers;

use App\Models\XFeedback;
use Illuminate\Http\Request;

class XFeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = XFeedback::orderBy('created_at', 'desc')->paginate(20);
        return view('x_feedbacks.index', compact('feedbacks'));
    }

    public function create()
    {
        return view('x_feedbacks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'feedback_type' => 'required|string',
            'count' => 'required|integer',
            'retrieved_at' => 'required|date',
        ]);

        XFeedback::create($request->all());

        return redirect()->route('x_feedbacks.index')
                         ->with('success', 'Feedback created successfully.');
    }

    public function show(XFeedback $xFeedback)
    {
        return view('x_feedbacks.show', compact('xFeedback'));
    }

    public function edit(XFeedback $xFeedback)
    {
        return view('x_feedbacks.edit', compact('xFeedback'));
    }

    public function update(Request $request, XFeedback $xFeedback)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'feedback_type' => 'required|string',
            'count' => 'required|integer',
            'retrieved_at' => 'required|date',
        ]);

        $xFeedback->update($request->all());

        return redirect()->route('x_feedbacks.index')
                         ->with('success', 'Feedback updated successfully.');
    }

    public function destroy(XFeedback $xFeedback)
    {
        $xFeedback->delete();

        return redirect()->route('x_feedbacks.index')
                         ->with('success', 'Feedback deleted successfully.');
    }
}
