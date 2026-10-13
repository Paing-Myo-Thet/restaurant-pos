<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Table;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SaleController extends Controller
{
    public const FILTERS = [
        'open' => 'Open',
        'unpaid' => 'Unpaid',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'all' => 'All',
    ];

    public function __construct(private OrderService $orders)
    {
    }

    public function index(Request $request): View
    {
        $filter = array_key_exists($request->query('filter'), self::FILTERS)
            ? $request->query('filter')
            : 'open';

        $sales = Sale::with(['customer', 'table', 'user'])
            ->when($filter === 'open', fn ($q) => $q->open())
            ->when($filter === 'unpaid', fn ($q) => $q->where('payment_status', 'unpaid')->where('status', '!=', 'cancelled'))
            ->when($filter === 'completed', fn ($q) => $q->where('status', 'served')->where('payment_status', 'paid'))
            ->when($filter === 'cancelled', fn ($q) => $q->where('status', 'cancelled'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'filter' => $filter,
            'filters' => self::FILTERS,
            'title' => 'Orders',
        ]);
    }

    public function today(): View
    {
        $sales = Sale::with(['customer', 'table', 'user'])
            ->whereDate('created_at', today())
            ->latest()
            ->paginate(20);

        return view('sales.index', [
            'sales' => $sales,
            'filter' => null,
            'filters' => [],
            'title' => "Today's Orders",
        ]);
    }

    public function create(): View
    {
        return view('sales.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $sale = $this->orders->create($this->validateOrder($request), $request->user());

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', "Order {$sale->order_number} created.");
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'table', 'user', 'items']);

        return view('sales.show', [
            'sale' => $sale,
            'nextStatuses' => $sale->nextStatusesFor(auth()->user()),
        ]);
    }

    public function edit(Sale $sale): View|RedirectResponse
    {
        if (! $sale->isEditable()) {
            return redirect()
                ->route('sales.show', $sale)
                ->with('error', 'Only unpaid orders that the kitchen has not started can be edited.');
        }

        $cart = $sale->items->mapWithKeys(fn ($item) => [
            $item->product_id => [
                'id' => $item->product_id,
                'name' => $item->product_name,
                'price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
            ],
        ]);

        return view('sales.edit', $this->formData() + ['sale' => $sale, 'cart' => $cart]);
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $this->orders->update($sale, $this->validateOrder($request, $sale), $request->user());

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', "Order {$sale->order_number} updated.");
    }

    public function updateStatus(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['sent', 'preparing', 'ready', 'served', 'cancelled'])],
        ]);

        $this->orders->changeStatus($sale, $validated['status'], $request->user());

        return back()->with('success', "Order {$sale->order_number} marked as {$validated['status']}.");
    }

    public function checkout(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(Sale::PAYMENT_METHODS)],
            'descount' => ['nullable', 'numeric', 'min:0'],
            'amount_received' => ['nullable', 'required_if:payment_method,cash', 'numeric', 'min:0'],
        ]);

        $this->orders->checkout($sale, $validated);

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', "Payment recorded for order {$sale->order_number}.")
            ->with('print_receipt', true);
    }

    public function receipt(Sale $sale): View
    {
        $sale->load(['customer', 'table', 'user', 'items']);

        return view('sales.receipt', compact('sale'));
    }

    private function validateOrder(Request $request, ?Sale $sale = null): array
    {
        // Products already on the order may stay even if they were deactivated since.
        $keptProductIds = $sale?->items()->pluck('product_id')->all() ?? [];

        return $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'table_id' => ['nullable', 'exists:tables,id'],
            'note' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(fn ($q) => $q->where(fn ($q) => $q
                    ->where('status', true)
                    ->orWhereIn('id', $keptProductIds))),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ], [
            'items.required' => 'Add at least one product to the order.',
        ]);
    }

    private function formData(): array
    {
        return [
            'categories' => Category::where('status', true)
                ->with(['products' => fn ($q) => $q->where('status', true)->orderBy('name')])
                ->orderBy('name')
                ->get()
                ->filter(fn ($category) => $category->products->isNotEmpty()),
            'customers' => Customer::orderBy('name')->get(),
            'tables' => Table::orderBy('name')->get(),
        ];
    }
}
