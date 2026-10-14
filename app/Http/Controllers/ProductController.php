<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $products = Product::with('category')->latest()->get();

    return view('products.index', compact('products'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $categories = Category::where('status', true)->get();

    return view('products.create', compact('categories'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'cost' => 'required|numeric|min:0|max:99999999',
            'price' => 'required|numeric|min:0|max:99999999',
            'stock' => 'required|integer|min:0|max:1000000',
            'status' => 'required|boolean',
        ]);
    
        Product::create($validated);
    
        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
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
    public function edit(Product $product)
{
    $categories = Category::where('status', true)->get();

    return view('products.edit', compact('product', 'categories'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
        'cost' => 'required|numeric|min:0|max:99999999',
        'price' => 'required|numeric|min:0|max:99999999',
        'stock' => 'required|integer|min:0|max:1000000',
        'status' => 'required|boolean',
    ]);

    $product->update($validated);

    return redirect()
        ->route('products.index')
        ->with('success', 'Product updated successfully.');
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Sold products are referenced by order history, so hide them instead.
        if ($product->saleItems()->exists()) {
            $product->update(['status' => false]);

            return redirect()
                ->route('products.index')
                ->with('success', "{$product->name} has sales history, so it was deactivated instead of deleted.");
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
