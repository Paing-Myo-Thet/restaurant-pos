<x-app-layout>

    <div class="max-w-6xl mx-auto px-4 py-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Order #{{ $sale->id }}
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Created {{ $sale->created_at->format('d M Y, h:i A') }}
                </p>
            </div>

            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                ← Back to Sales
            </a>
        </div>


        {{-- Order Information --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

            {{-- Customer --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase">
                    Customer
                </h2>

                <p class="text-lg font-semibold text-gray-800 mt-2">
                    {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                </p>
            </div>


            {{-- Table --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase">
                    Table
                </h2>

                <p class="text-lg font-semibold text-gray-800 mt-2">
                    {{ $sale->table?->name ?? 'No Table' }}
                </p>
            </div>


            {{-- Staff --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase">
                    Created By
                </h2>

                <p class="text-lg font-semibold text-gray-800 mt-2">
                    {{ $sale->user?->name ?? 'Unknown' }}
                </p>
            </div>

        </div>


        {{-- Status --}}
        <div class="bg-white rounded-lg shadow p-5 mb-6">

            <div class="flex  flex-wrap gap-6">

                <div>
                    <p class="text-sm text-gray-500">
                        Order Status
                    </p>

                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold
                        @if($sale->status === 'pending')
                            bg-yellow-100 text-yellow-800
                        @elseif($sale->status === 'sent')
                            bg-blue-100 text-blue-800
                        @elseif($sale->status === 'preparing')
                            bg-orange-100 text-orange-800
                        @elseif($sale->status === 'ready')
                            bg-green-100 text-green-800
                        @elseif($sale->status === 'served')
                            bg-gray-100 text-gray-800
                        @elseif($sale->status === 'cancelled')
                            bg-red-100 text-red-800
                        @endif
                    ">
                        {{ ucfirst($sale->status) }}
                    </span>
                </div>


                <div>
                    <p class="text-sm mb-2 text-gray-500">
                        Payment Status
                    </p>

                    @if ($sale->payment_status === 'unpaid')
                    <form action="{{ route('sales.markAsPaid', $sale) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white">
                            Mark as Paid
                        </button>
                    </form>
                    @else
                    <span class="text-sm pt-2 bg-green-600 py-2 px-6 text-white rounded-md font-medium text-green-600">
                        Paid
                    </span>
                    @endif
                </div>

            </div>

        </div>


        {{-- Order Items --}}
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">

            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">
                    Order Items
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                #
                            </th>

                            <th class="px-6 py-3 text-left">
                                Product
                            </th>

                            <th class="px-6 py-3 text-right">
                                Unit Price
                            </th>

                            <th class="px-6 py-3 text-center">
                                Quantity
                            </th>

                            <th class="px-6 py-3 text-right">
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($sale->items as $index => $item)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $item->product_name }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                {{ $item->quantity }}
                            </td>

                            <td class="px-6 py-4 text-right font-medium">
                                {{ number_format($item->subtotal, 2) }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No items found for this order.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Order Summary --}}
        <div class="flex justify-end">

            <div class="w-full md:w-96 bg-white rounded-lg shadow p-6">

                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    Order Summary
                </h2>

                <div class="flex justify-between py-2">
                    <span class="text-gray-600">
                        Subtotal
                    </span>

                    <span class="font-medium">
                        {{ number_format($sale->sub_total, 2) }}
                    </span>
                </div>


                <div class="flex justify-between py-2">
                    <span class="text-gray-600">
                        Discount
                    </span>

                    <span class="font-medium text-red-600">
                        - {{ number_format($sale->descount, 2) }}
                    </span>
                </div>


                <div class="border-t mt-3 pt-4 flex justify-between">

                    <span class="text-lg font-bold">
                        Total
                    </span>

                    <span class="text-xl font-bold text-green-600">
                        {{ number_format($sale->total, 2) }}
                    </span>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
