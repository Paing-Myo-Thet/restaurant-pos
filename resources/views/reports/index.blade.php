<x-app-layout>

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Sales Reports
            </h1>

            <p class="text-sm text-gray-500">
                View sales by daily, weekly, monthly, or yearly period.
            </p>
        </div>


        {{-- Filter --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <form method="GET" action="{{ route('reports.index') }}">

                <div class="grid gap-4 md:grid-cols-3">

                    {{-- Period --}}
                    <div>
                        <label for="period" class="mb-2 block text-sm font-medium text-gray-700">
                            Report Period
                        </label>

                        <select name="period" id="period" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300">
                            <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>
                                Daily
                            </option>

                            <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>
                                Weekly
                            </option>

                            <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>
                                Monthly
                            </option>

                            <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>
                                Yearly
                            </option>
                        </select>
                    </div>


                    {{-- Daily --}}
                    @if ($period === 'daily')

                    <div>
                        <label for="date" class="mb-2 block text-sm font-medium text-gray-700">
                            Select Date
                        </label>

                        <input type="date" name="date" id="date" value="{{ $date }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    @endif


                    {{-- Monthly --}}
                    @if ($period === 'monthly')

                    <div>
                        <label for="month" class="mb-2 block text-sm font-medium text-gray-700">
                            Select Month
                        </label>

                        <input type="month" name="month" id="month" value="{{ $month }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    @endif


                    {{-- Yearly --}}
                    @if ($period === 'yearly')

                    <div>
                        <label for="year" class="mb-2 block text-sm font-medium text-gray-700">
                            Select Year
                        </label>

                        <select name="year" id="year" class="w-full rounded-lg border-gray-300">

                            @for ($y = now()->year; $y >= now()->year - 5; $y--)

                            <option value="{{ $y }}" {{ (int) $year === $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>

                            @endfor

                        </select>
                    </div>

                    @endif


                    {{-- Filter button --}}
                    <div class="flex items-end">

                        <button type="submit" class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                            Apply Filter
                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- Report Cards --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-5">

            {{-- Total Sales --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">
                    Total Sales
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-800">
                    {{ number_format($totalSales, 2) }}
                </p>
            </div>


            {{-- Total Orders --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">
                    Total Orders
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-800">
                    {{ $totalOrders }}
                </p>
            </div>


            {{-- Paid --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">
                    Paid Sales
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-800">
                    {{ number_format($paidSales, 2) }}
                </p>
            </div>


            {{-- Unpaid --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">
                    Unpaid Sales
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-800">
                    {{ number_format($unpaidSales, 2) }}
                </p>
            </div>


            {{-- Cancelled --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">
                    Cancelled Orders
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-800">
                    {{ $cancelledOrders }}
                </p>
            </div>

        </div>
        {{-- Breakdown --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Sales Breakdown
                </h2>

                <p class="text-sm text-gray-500">
                    @if ($period === 'daily')
                    Sales for the selected date
                    @elseif ($period === 'weekly')
                    Sales for the current week
                    @elseif ($period === 'monthly')
                    Sales for the selected month
                    @else
                    Sales for the selected year
                    @endif
                </p>
            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                                {{ $period === 'yearly' ? 'Month' : 'Date' }}
                            </th>

                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                                Orders
                            </th>

                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                                Sales
                            </th>

                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                                Paid
                            </th>

                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                                Unpaid
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse ($breakdown as $row)

                        <tr>

                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $row['label'] }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $row['orders'] }}
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ number_format($row['sales'], 2) }}
                            </td>

                            <td class="px-6 py-4 text-sm text-green-600">
                                {{ number_format($row['paid'], 2) }}
                            </td>

                            <td class="px-6 py-4 text-sm text-orange-600">
                                {{ number_format($row['unpaid'], 2) }}
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No sales found for this period.
                            </td>

                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>

        </div>
    </div>

</x-app-layout>
