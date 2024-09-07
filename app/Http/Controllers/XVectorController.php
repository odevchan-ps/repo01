<?php

namespace App\Http\Controllers;

use App\Models\XVector;
use Illuminate\Http\Request;

class XVectorController extends Controller
{
    public function index()
    {
        $vectors = XVector::orderBy('created_at', 'desc')->paginate(20);
        return view('x_vectors.index', compact('vectors'));
    }

    public function create()
    {
        return view('x_vectors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'vector' => 'required',
            'created_at' => 'required|date',
        ]);

        XVector::create($request->all());

        return redirect()->route('x_vectors.index')
                         ->with('success', 'Vector created successfully.');
    }

    public function show(XVector $xVector)
    {
        return view('x_vectors.show', compact('xVector'));
    }

    public function edit(XVector $xVector)
    {
        return view('x_vectors.edit', compact('xVector'));
    }

    public function update(Request $request, XVector $xVector)
    {
        $request->validate([
            'x_post_id' => 'required|string|max:50',
            'vector' => 'required',
            'created_at' => 'required|date',
        ]);

        $xVector->update($request->all());

        return redirect()->route('x_vectors.index')
                         ->with('success', 'Vector updated successfully.');
    }

    public function destroy(XVector $xVector)
    {
        $xVector->delete();

        return redirect()->route('x_vectors.index')
                         ->with('success', 'Vector deleted successfully.');
    }
}
