<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        return response()->json(Blog::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

       if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('blogs', 'public');
        $validated['photo'] = $path;
    }

        $blog = Blog::create($validated);

        return response()->json($blog, 201);
    }

    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);
    }

    public function update(Request $request , $id)
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($blog->photo) {
                Storage::disk('public')->delete($blog->photo);
            }
            $validated['photo'] = $request->file('photo')->store('blogs', 'public');
        }

        $blog->update($validated);

        return response()->json($blog);
    }

    public function delete($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->photo) {
            Storage::disk('public')->delete($blog->photo);
        }

        $blog->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
