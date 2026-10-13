<x-app-layout>

    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Inventory</h1>
                <p class="text-sm text-gray-500">Stock movement history</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('stock-movements.create') }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    + Stock In
                </a>
                <a href="{{ route('stock-movements.out.create') }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    − Stock Out
                </a>
            </div>
        </div>

        @if ($lowStock->isNotEmpty())
            <div class="rounded-lg border border-orange-200 bg-orange-50 p-4">
                <p class="text-sm font-semibold text-orange-900">
                    {{ $lowStock->count() }} {{ Str::plural('product', $lowStock->count()) }} at or below {{ \App\Models\Product::LOW_STOCK_THRESHOLD }} units:
                </p>
                <p class="mt-1 text-sm text-orange-800">
                    {{ $lowStock->map(fn ($p) => "{$p->name} ({$p->stock})")->join(', ') }}
                </p>
            </div>
        @endif

        <div class="flex gap-2">
            @foreach ([null => 'All', 'in' => 'In', 'out' => 'Out'] as $value => $label)
                <a href="{{ route('stock-movements.index', array_filter(['type' => $value])) }}"
                   class="rounded-full px-4 py-1.5 text-sm font-medium
                   {{ $type === ($value ?: null) ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow">

            <table class="min-w-full divide-y divide-gray-200 text-sm">

                <thead class="bg-gray-50 text-left font-semibold text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3 text-right">Quantity</th>
                        <th class="px-6 py-3">Reason</th>
                        <th class="px-6 py-3">By</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse ($stockMovements as $movement)

                        <tr>
                            <td class="whitespace-nowrap px-6 py-3 text-gray-500">
                                {{ $movement->created_at->format('Y-m-d H:i') }}
                            </td>

                            <td class="px-6 py-3 text-gray-800">
                                {{ $movement->product?->name ?? 'Deleted product' }}
                            </td>

                            <td class="px-6 py-3 text-right font-semibold {{ $movement->type === 'in' ? 'text-green-700' : 'text-red-700' }}">
                                {{ $movement->type === 'in' ? '+' : '−' }}{{ $movement->quantity }}
                            </td>

                            <td class="px-6 py-3 text-gray-600">
                                @if ($movement->sale)
                                    <a href="{{ route('sales.show', $movement->sale) }}" class="text-blue-600 hover:underline">
                                        {{ $movement->note ?? 'Order '.$movement->sale->order_number }}
                                    </a>
                                @else
                                    {{ $movement->note ?? '—' }}
                                @endif
                            </td>

                            <td class="px-6 py-3 text-gray-600">
                                {{ $movement->user?->name }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No stock movements found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{ $stockMovements->links() }}

    </div>

</x-app-layout>
