<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\StockMovement;
use App\Models\Product;

class StockMovementController extends Controller
{
   public function index(): View
   {
    $stockMovements = StockMovement::with([
        'product',
        'user'
    ])->latest()->get();

    return view('stock-movements.index', compact('stockMovements'));
    
   }

   public function create(): View
{
    $products = Product::where('status', true)
        ->orderBy('name')
        ->get();

    return view('stock-movements.create', compact('products'));
}

public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'product_id' => ['required', 'exists:products,id'],
        'quantity' => ['required', 'integer', 'min:1'],
    ]);

    Product::findOrFail($validated['product_id'])
        ->increment('stock', $validated['quantity']);

    StockMovement::create([
        'product_id' => $validated['product_id'],
        'user_id' => auth()->id(),
        'type' => 'in',
        'quantity' => $validated['quantity'],
    ]);

    return redirect()
        ->route('stock-movements.index')
        ->with('success', 'Stock added successfully.');
}
public function out(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'product_id' => ['required', 'exists:products,id'],
        'quantity' => ['required', 'integer', 'min:1'],
    ]);

    $product = Product::findOrFail($validated['product_id']);

    if ($validated['quantity'] > $product->stock) {
        return back()
            ->withErrors([
                'quantity' => "Not enough stock. Current stock is {$product->stock}"
            ])
            ->withInput();
    }

    $product->decrement('stock', $validated['quantity']);

    StockMovement::create([
        'product_id' => $validated['product_id'],
        'user_id' => auth()->id(),
        'type' => 'out',
        'quantity' => $validated['quantity'],
    ]);

    return redirect()
        ->route('stock-movements.index')
        ->with('success', 'Stock removed successfully.');
}

public function createOut(): View
{
    $products = Product::where('status', true)
        ->orderBy('name')
        ->get();

    return view('stock-movements.out', compact('products'));
}

}