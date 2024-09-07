<?php

namespace App\Http\Controllers;

use App\Models\XPrompt;
use Illuminate\Http\Request;

class XPromptController extends Controller
{
    public function index()
    {
        $prompts = XPrompt::orderBy('created_at', 'desc')->paginate(20);
        return view('x_prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('x_prompts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'prompt_text' => 'required',
            'created_at' => 'required|date',
        ]);

        XPrompt::create($request->all());

        return redirect()->route('x_prompts.index')
                         ->with('success', 'Prompt created successfully.');
    }

    public function show(XPrompt $xPrompt)
    {
        return view('x_prompts.show', compact('xPrompt'));
    }

    public function edit(XPrompt $xPrompt)
    {
        return view('x_prompts.edit', compact('xPrompt'));
    }

    public function update(Request $request, XPrompt $xPrompt)
    {
        $request->validate([
            'prompt_text' => 'required',
            'created_at' => 'required|date',
        ]);

        $xPrompt->update($request->all());

        return redirect()->route('x_prompts.index')
                         ->with('success', 'Prompt updated successfully.');
    }

    public function destroy(XPrompt $xPrompt)
    {
        $xPrompt->delete();

        return redirect()->route('x_prompts.index')
                         ->with('success', 'Prompt deleted successfully.');
    }
}
