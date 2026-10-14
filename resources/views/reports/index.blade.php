<x-app-layout>

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Sales Reports</h1>
            <p class="text-sm text-gray-500">{{ $label }}</p>
        </div>


        {{-- Filter --}}
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4 rounded-lg bg-white p-5 shadow">

            <div>
                <label for="period" class="mb-1 block text-sm font-medium text-gray-700">Period</label>
                <select name="period" id="period" onchange="this.form.submit()" class="rounded-lg border-gray-300">
                    @foreach (\App\Http\Controllers\ReportController::PERIODS as $option)
                        <option value="{{ $option }}" @selected($period === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>

            @if (in_array($period, ['daily', 'weekly']))
                <div>
                    <label for="date" class="mb-1 block text-sm font-medium text-gray-700">
                        {{ $period === 'weekly' ? 'Any day in the week' : 'Date' }}
                    </label>
                    <input type="date" name="date" id="date" value="{{ $date }}" class="rounded-lg border-gray-300">
                </div>
            @elseif ($period === 'monthly')
                <div>
                    <label for="month" class="mb-1 block text-sm font-medium text-gray-700">Month</label>
                    <input type="month" name="month" id="month" value="{{ $month }}" class="rounded-lg border-gray-300">
                </div>
            @else
                <div>
                    <label for="year" class="mb-1 block text-sm font-medium text-gray-700">Year</label>
                    <select name="year" id="year" class="rounded-lg border-gray-300">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" @selected((int) $year === $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            <button type="submit" class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                Apply
            </button>
        </form>


        {{-- Summary --}}
        @php
            $cards = [
                ['Sales', number_format($totalSales, 0).' Ks', "{$totalOrders} orders", 'text-gray-900'],
                ['Gross profit', number_format($profit, 0).' Ks', 'Sales − cost of items ('.number_format($cost, 0).' Ks)', $profit >= 0 ? 'text-green-700' : 'text-red-700'],
                ['Average order', number_format($averageOrder, 0).' Ks', 'Discounts given: '.number_format($discounts, 0).' Ks', 'text-gray-900'],
                ['Collected', number_format($paidSales, 0).' Ks', 'Unpaid: '.number_format($unpaidSales, 0).' Ks', 'text-gray-900'],
                ['Cancelled', $cancelledOrders, 'Food written off: '.number_format($writtenOff, 0).' Ks', $cancelledOrders ? 'text-red-700' : 'text-gray-900'],
            ];
        @endphp

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($cards as [$title, $value, $hint, $color])
                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">{{ $title }}</p>
                    <p class="mt-2 text-2xl font-bold {{ $color }}">{{ $value }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
                </div>
            @endforeach
        </div>


        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Top products --}}
            <div class="overflow-hidden rounded-lg bg-white shadow lg:col-span-2">
                <div class="border-b px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800">Top-selling products</h2>
                </div>

                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">#</th>
                            <th class="px-6 py-3 text-left">Product</th>
                            <th class="px-6 py-3 text-right">Sold</th>
                            <th class="px-6 py-3 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($topProducts as $product)
                            <tr>
                                <td class="px-6 py-3 text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $product['name'] }}</td>
                                <td class="px-6 py-3 text-right">{{ $product['quantity'] }}</td>
                                <td class="px-6 py-3 text-right">{{ number_format($product['revenue'], 0) }} Ks</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No sales in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Payment methods --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Payments received</h2>

                <ul class="space-y-3">
                    @forelse ($paymentMethods as $method => $row)
                        <li>
                            <div class="flex justify-between text-sm">
                                <span class="font-medium capitalize text-gray-700">{{ $method }} <span class="text-gray-400">({{ $row['orders'] }})</span></span>
                                <span class="font-semibold">{{ number_format($row['total'], 0) }} Ks</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-green-500" style="width: {{ $paidSales > 0 ? round($row['total'] / $paidSales * 100) : 0 }}%"></div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No payments in this period.</li>
                    @endforelse
                </ul>
            </div>
        </div>


        {{-- Breakdown --}}
        @if ($period !== 'daily')
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800">Breakdown</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left">{{ $period === 'yearly' ? 'Month' : 'Day' }}</th>
                                <th class="px-6 py-3 text-right">Orders</th>
                                <th class="px-6 py-3 text-right">Sales</th>
                                <th class="px-6 py-3 text-right">Profit</th>
                                <th class="px-6 py-3 text-right">Paid</th>
                                <th class="px-6 py-3 text-right">Unpaid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($breakdown as $row)
                                <tr>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $row['label'] }}</td>
                                    <td class="px-6 py-3 text-right">{{ $row['orders'] }}</td>
                                    <td class="px-6 py-3 text-right font-medium">{{ number_format($row['sales'], 0) }}</td>
                                    <td class="px-6 py-3 text-right text-green-700">{{ number_format($row['profit'], 0) }}</td>
                                    <td class="px-6 py-3 text-right">{{ number_format($row['paid'], 0) }}</td>
                                    <td class="px-6 py-3 text-right text-orange-600">{{ number_format($row['unpaid'], 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No sales in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
