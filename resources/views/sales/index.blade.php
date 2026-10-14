<x-app-layout>

    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $title }}
                </h1>

                <p class="text-sm text-gray-500">
                    {{ $sales->total() }} {{ Str::plural('order', $sales->total()) }}
                </p>
            </div>

            @can('orders.manage')
                <a href="{{ route('sales.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    + New Order
                </a>
            @endcan
        </div>

        {{-- Filters --}}
        @if ($filters)
            <div class="flex flex-wrap gap-2">
                @foreach ($filters as $key => $label)
                    <a href="{{ route('sales.index', ['filter' => $key]) }}"
                       class="rounded-full px-4 py-1.5 text-sm font-medium
                       {{ $filter === $key ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Sales Table --}}
        <div class="overflow-x-auto rounded-lg bg-white shadow">

            <table class="w-full">

                <thead class="bg-gray-50">
                    <tr class="text-left text-sm font-semibold text-gray-700">
                        <th class="px-6 py-3">Order</th>
                        <th class="px-6 py-3">Table</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Staff</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Payment</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse ($sales as $sale)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">
                                <a href="{{ route('sales.show', $sale) }}" class="font-semibold text-gray-800 hover:text-blue-600">
                                    {{ $sale->order_number }}
                                </a>
                                <p class="text-xs text-gray-400">
                                    {{ $sale->created_at->format('M j, g:i A') }}
                                </p>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $sale->table?->name ?? 'Takeaway' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $sale->customer?->name ?? 'Walk-in' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $sale->user?->name ?? 'Unknown' }}
                            </td>

                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">
                                {{ number_format($sale->total, 2) }} Ks
                            </td>

                            <td class="px-6 py-4">
                                <x-order-status :status="$sale->status" />
                            </td>

                            <td class="px-6 py-4">
                                <x-payment-status :sale="$sale" />
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sales.show', $sale) }}" class="rounded bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-700">
                                    Open
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                No orders found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{ $sales->links() }}

    </div>

</x-app-layout>
