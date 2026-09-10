<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Table;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sales = Sale::with([
            'customer',
            'table',
            'user',
            'items'
        ])->latest()->get();

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::where('status',true)
            ->where('stock', '>', 0)
            ->get();

        $customers = Customer::latest()->get();

        $tables = Table::where('status', 'available')->get();
        
        return view('sales.create', compact('products', 'customers', 'tables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'customer_id' => ['nullable', 'exists:customers,id'],
        'table_id' => ['nullable', 'exists:tables,id'],
        'descount' => ['nullable', 'numeric', 'min:0'],
        'payment_status' => ['required', 'in:unpaid,paid'],

        'items' => ['required', 'array', 'min:1'],

        'items.*.product_id' => [
            'required',
            'exists:products,id',
        ],

        'items.*.quantity' => [
            'required',
            'integer',
            'min:1',
        ],
    ]);

    DB::transaction(function () use ($validated) {

        $subtotal = 0;

        $sale = Sale::create([
            'customer_id' => $validated['customer_id'],
            'table_id' => $validated['table_id'],
            'user_id' => auth()->id(),
            'sub_total' => 0,
            'descount' => $validated['descount'] ?? 0,
            'total' => 0,
            'status' => 'pending',
            'payment_status' => $validated['payment_status'],
        ]);

        foreach ($validated['items'] as $item) {

            $product = Product::findOrFail($item['product_id']);

            $quantity = $item['quantity'];

            if ($quantity > $product->stock) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'items' => "Not enough stock for {$product->name}.",
                ]);
            }

            $itemSubtotal = $product->price * $quantity;

            $subtotal += $itemSubtotal;

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $itemSubtotal,
            ]);

            $product->decrement('stock', $quantity);
        }

        $descount = $validated['descount'] ?? 0;

        if ($descount > $subtotal) {
            $descount = $subtotal;
        }

        $total = $subtotal - $descount;

        $sale->update([
            'sub_total' => $subtotal,
            'descount' => $descount,
            'total' => $total,
        ]);

        if ($sale->table_id) {
            Table::where('id', $sale->table_id)
                ->update([
                    'status' => 'occupied',
                ]);
        }
    });

    return redirect()
        ->route('sales.index')
        ->with('success', 'Order created successfully.');
}
    /**
     * Display the specified resource.
     */
    public function show(Sale $sale): View
{
    $sale->load([
        'customer',
        'table',
        'user',
        'items.product',
    ]);

    return view('sales.show', compact('sale'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale): View
{
    $sale->load('items');

    $cart = $sale->items->mapWithKeys(function ($item) {
        return [
            $item->product_id => [
                'id' => $item->product_id,
                'name' => $item->product_name,
                'price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
            ],
        ];
    });

    $products = Product::where('stock', '>', 0)
        ->orderBy('name')
        ->get();

    $customers = Customer::orderBy('name')->get();
    $tables = Table::orderBy('name')->get();

    return view('sales.edit', compact(
        'sale',
        'products',
        'customers',
        'tables',
        'cart'
    ));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'table_id' => ['nullable', 'exists:tables,id'],
            'descount' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', 'in:unpaid,paid'],
    
            'items' => ['required', 'array', 'min:1'],
    
            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],
    
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);
    
        DB::transaction(function () use ($validated, $sale) {
    
            /*
             * 1. Return old stock
             */
            foreach ($sale->items as $oldItem) {
                Product::where('id', $oldItem->product_id)
                    ->increment('stock', $oldItem->quantity);
            }
    
            /*
             * 2. Delete old sale items
             */
            $sale->items()->delete();
    
            /*
             * 3. Calculate new order
             */
            $subtotal = 0;
    
            foreach ($validated['items'] as $item) {
    
                $product = Product::findOrFail($item['product_id']);
    
                $quantity = $item['quantity'];
    
                if ($quantity > $product->stock) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => "Not enough stock for {$product->name}.",
                    ]);
                }
    
                $itemSubtotal = $product->price * $quantity;
    
                $subtotal += $itemSubtotal;
    
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $itemSubtotal,
                ]);
    
                /*
                 * Reduce stock for new quantity
                 */
                $product->decrement('stock', $quantity);
            }
    
            /*
             * 4. Calculate discount
             */
            $descount = $validated['descount'] ?? 0;
    
            if ($descount > $subtotal) {
                $descount = $subtotal;
            }
    
            $total = $subtotal - $descount;
    
            /*
             * 5. Update sale
             */
            $sale->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'sub_total' => $subtotal,
                'descount' => $descount,
                'total' => $total,
                'payment_status' => $validated['payment_status'],
            ]);
        });
    
        return redirect()
            ->route('sales.index')
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        
        return redirect()
            ->route('sales.index')
            ->with('success', 'Deleted Sale Successfully');
    }

    public function updateStatus(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,sent,preparing,ready,served,cancelled',
            ],
        ]);
    
        $newStatus = $validated['status'];
    
        // Define allowed status transitions
        $allowedTransitions = [
            'pending' => ['sent', 'cancelled'],
            'sent' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['served'],
            'served' => [],
            'cancelled' => [],
        ];
    
        if (! in_array($newStatus, $allowedTransitions[$sale->status])) {
            abort(403, 'Invalid order status transition.');
        }
    
        $sale->update([
            'status' => $newStatus,
        ]);
    
        return redirect()
            ->route('sales.index')
            ->with('success', 'Order status updated successfully.');
    }

}
