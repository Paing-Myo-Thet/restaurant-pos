<x-app-layout>

    @php $user = auth()->user(); @endphp

    <div class="space-y-8">

        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Here's what's happening in your restaurant today.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('sales.today') }}" class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">
                    Today's Orders
                </a>
                <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800">
                    <span>＋</span>
                    New Order
                </a>
            </div>

        </div>


        {{-- Statistics --}}
        @php
            $stats = [
                ["Today's Sales", number_format($todaySales, 0).' Ks', 'Paid today', '💰', 'bg-green-100'],
                ["Today's Orders", $todayOrders, 'Excluding cancelled', '🧾', 'bg-blue-100'],
                ['Open Orders', $openOrders, 'Not yet served and paid', '⏳', 'bg-orange-100'],
                ['Tables in Use', "{$occupiedTables} / {$totalTables}", 'Currently occupied', '🪑', 'bg-yellow-100'],
            ];
        @endphp

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as [$label, $value, $hint, $icon, $iconBg])
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                            <p class="mt-3 text-2xl font-bold text-gray-900">{{ $value }}</p>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl text-xl {{ $iconBg }}">
                            {{ $icon }}
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-gray-400">{{ $hint }}</p>
                </div>
            @endforeach
        </div>


        <div class="grid gap-6 xl:grid-cols-3">

            {{-- Recent Orders --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 xl:col-span-2">

                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                        <p class="mt-1 text-sm text-gray-500">Latest restaurant orders</p>
                    </div>

                    <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-gray-700 transition hover:text-gray-900">
                        View All →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200 text-left text-xs uppercase tracking-wide text-gray-500">
                                <th class="px-6 py-3 font-semibold">Order</th>
                                <th class="px-6 py-3 font-semibold">Table</th>
                                <th class="px-6 py-3 font-semibold">Staff</th>
                                <th class="px-6 py-3 text-right font-semibold">Total</th>
                                <th class="px-6 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <a href="{{ route('sales.show', $order) }}" class="font-semibold text-gray-800 hover:text-blue-600">
                                            {{ $order->order_number }}
                                        </a>
                                        <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ $order->table?->name ?? 'Takeaway' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ $order->user?->name }}</td>
                                    <td class="px-6 py-3 text-right text-sm font-semibold">{{ number_format($order->total, 0) }} Ks</td>
                                    <td class="px-6 py-3"><x-order-status :status="$order->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <p class="text-sm font-medium text-gray-900">No orders yet</p>
                                        <p class="mt-1 text-sm text-gray-500">Orders will appear here once they are created.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Side panel --}}
            <div class="space-y-6">

                @if ($user->isAdmin())
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Low Stock</h2>
                            <span class="rounded-full px-2.5 py-0.5 text-sm font-semibold {{ $lowStockCount ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $lowStockCount }}
                            </span>
                        </div>

                        <ul class="mt-4 divide-y">
                            @forelse ($lowStock as $product)
                                <li class="flex justify-between py-2 text-sm">
                                    <span class="text-gray-700">{{ $product->name }}</span>
                                    <span class="font-semibold {{ $product->stock == 0 ? 'text-red-600' : 'text-orange-600' }}">
                                        {{ $product->stock == 0 ? 'Sold out' : $product->stock.' left' }}
                                    </span>
                                </li>
                            @empty
                                <li class="py-2 text-sm text-gray-500">All products are well stocked.</li>
                            @endforelse
                        </ul>

                        @if ($lowStockCount)
                            <a href="{{ route('stock-movements.create') }}" class="mt-4 block rounded-lg bg-gray-900 py-2 text-center text-sm font-semibold text-white hover:bg-gray-800">
                                Restock
                            </a>
                        @endif
                    </div>
                @endif

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Quick Actions</h2>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('sales.index', ['filter' => 'unpaid']) }}" class="rounded-xl bg-gray-50 p-4 text-sm font-medium text-gray-700 hover:bg-gray-100">💳 Unpaid orders</a>
                        @if ($user->hasRole('admin', 'waiter'))
                            <a href="{{ route('tables.index') }}" class="rounded-xl bg-gray-50 p-4 text-sm font-medium text-gray-700 hover:bg-gray-100">🪑 Tables</a>
                        @endif
                        @if ($user->isAdmin())
                            <a href="{{ route('kitchen.index') }}" class="rounded-xl bg-gray-50 p-4 text-sm font-medium text-gray-700 hover:bg-gray-100">👨‍🍳 Kitchen</a>
                            <a href="{{ route('products.index') }}" class="rounded-xl bg-gray-50 p-4 text-sm font-medium text-gray-700 hover:bg-gray-100">🍔 Products</a>
                            <a href="{{ route('reports.index') }}" class="rounded-xl bg-gray-50 p-4 text-sm font-medium text-gray-700 hover:bg-gray-100">📈 Reports</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
