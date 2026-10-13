<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $type = in_array($request->query('type'), ['in', 'out'], true) ? $request->query('type') : null;

        $stockMovements = StockMovement::with(['product', 'user', 'sale'])
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('stock-movements.index', [
            'stockMovements' => $stockMovements,
            'type' => $type,
            'lowStock' => Product::lowStock()->orderBy('stock')->get(),
        ]);
    }

    public function create(): View
    {
        return view('stock-movements.create', ['products' => $this->products()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMovement($request);

        DB::transaction(function () use ($validated, $request) {
            Product::whereKey($validated['product_id'])->increment('stock', $validated['quantity']);

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'user_id' => $request->user()->id,
                'type' => 'in',
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? 'Stock received',
            ]);
        });

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'Stock added successfully.');
    }

    public function createOut(): View
    {
        return view('stock-movements.out', ['products' => $this->products()]);
    }

    public function out(Request $request): RedirectResponse
    {
        $validated = $this->validateMovement($request);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            if ($validated['quantity'] > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => "Not enough stock. Current stock is {$product->stock}.",
                ]);
            }

            $product->decrement('stock', $validated['quantity']);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'type' => 'out',
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? 'Stock removed',
            ]);
        });

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'Stock removed successfully.');
    }

    private function validateMovement(Request $request): array
    {
        return $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function products()
    {
        return Product::where('status', true)->orderBy('name')->get();
    }
}
