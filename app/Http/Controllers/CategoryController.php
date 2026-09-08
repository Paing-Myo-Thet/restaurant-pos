<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->get();
        
        return view('categories.index',compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean'
        ]);
        Category::create([
            "name" => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status']
        ]);

        return redirect()
        ->route('categories.index')
        ->with('success', "Adding Category Successful");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view ('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'string|required|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean'
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status']
        ]);

        return redirect()
        ->route('categories.index')
        ->with('success', 'Category Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()
        ->route('categories.index')
        ->with('success', 'Categories deleted Successfully');
    }
}
