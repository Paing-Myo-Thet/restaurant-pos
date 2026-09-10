<x-app-layout>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Order #{{ $sale->id }}
                </h1>

                <p class="text-sm text-gray-500">
                    {{ $sale->created_at->format('d M Y, h:i A') }}
                </p>
            </div>

            <div class="flex items-center gap-2">

                <a href="{{ route('sales.edit', $sale) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Edit
                </a>
                <form action="{{route('sales.destroy', $sale)}}" method="POST" onsubmit="return confirm('You want to delte this Sale')">
                    @csrf
                    @method('DELETE')

                    <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>

                </form>
                <a href="{{ route('sales.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
                    Back
                </a>

            </div>
        </div>




        {{-- Order Information --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

            <div class="rounded-lg bg-white p-5 shadow">
                <p class="text-sm text-gray-500">Customer</p>
                <p class="mt-1 font-semibold">
                    {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                </p>
            </div>

            <div class="rounded-lg bg-white p-5 shadow">
                <p class="text-sm text-gray-500">Table</p>
                <p class="mt-1 font-semibold">
                    {{ $sale->table?->name ?? 'Takeaway' }}
                </p>
            </div>

            <div class="rounded-lg bg-white p-5 shadow">
                <p class="text-sm text-gray-500">Created By</p>
                <p class="mt-1 font-semibold">
                    {{ $sale->user?->name ?? 'Unknown' }}
                </p>
            </div>

        </div>

        {{-- Items --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <div class="border-b px-6 py-4">
                <h2 class="text-lg font-semibold">
                    Order Items
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                Product
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-medium uppercase text-gray-500">
                                Qty
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                Unit Price
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @foreach ($sale->items as $item)
                        <tr>

                            <td class="px-6 py-4 font-medium">
                                {{ $item->product_name }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                {{ $item->quantity }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                {{ number_format($item->subtotal, 2) }}
                            </td>

                        </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

            {{-- Totals --}}
            <div class="border-t px-6 py-5">

                <div class="ml-auto max-w-sm space-y-3">

                    <div class="flex justify-between">
                        <span class="text-gray-600">
                            Subtotal
                        </span>

                        <span class="font-medium">
                            {{ number_format($sale->sub_total, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">
                            Discount
                        </span>

                        <span class="font-medium">
                            {{ number_format($sale->descount, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between border-t pt-3 text-lg font-bold">
                        <span>Total</span>

                        <span>
                            {{ number_format($sale->total, 2) }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

        {{-- Status --}}
        {{-- Order Status --}}
        {{-- Order Status --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="mb-4 text-lg font-semibold text-gray-800">
                Order Status
            </h2>

            <div class="mb-4">
                <span class="text-sm text-gray-500">
                    Current Status:
                </span>

                <span class="font-semibold">
                    {{ ucfirst($sale->status) }}
                </span>
            </div>


            {{-- Waiter: Pending → Sent --}}
            @if (auth()->user()->role->value === 'waiter' && $sale->status === 'pending')

            <form action="{{ route('sales.updateStatus', $sale) }}" method="POST">
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" value="sent">

                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">
                    Send to Kitchen
                </button>
            </form>


            {{-- Kitchen: Sent → Preparing --}}
            @elseif (auth()->user()->role->value === 'kitchen' && $sale->status === 'sent')

            <form action="{{ route('sales.updateStatus', $sale) }}" method="POST">
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" value="preparing">

                <button type="submit" class="rounded-lg bg-yellow-500 px-4 py-2 font-semibold text-white hover:bg-yellow-600">
                    Start Preparing
                </button>
            </form>


            {{-- Kitchen: Preparing → Ready --}}
            @elseif (auth()->user()->role->value === 'kitchen' && $sale->status === 'preparing')

            <form action="{{ route('sales.updateStatus', $sale) }}" method="POST">
                @csrf
                @method('PATCH')

                <input type="hide" name="status" value="ready">

                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 font-semibold text-white hover:bg-green-700">
                    Mark Ready
                </button>
            </form>


            {{-- Waiter: Ready → Served --}}
            @elseif (auth()->user()->role->value === 'waiter' && $sale->status === 'ready')

            <form action="{{ route('sales.updateStatus', $sale) }}" method="POST">
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" value="served">

                <button type="submit" class="rounded-lg bg-purple-600 px-4 py-2 font-semibold text-white hover:bg-purple-700">
                    Mark Served
                </button>
            </form>


            {{-- Served --}}
            @elseif ($sale->status === 'served')

            <p class="text-sm text-green-600">
                Order has been served.
            </p>


            {{-- Cancelled --}}
            @elseif ($sale->status === 'cancelled')

            <p class="text-sm text-red-600">
                Order has been cancelled.
            </p>


            {{-- No action for this role --}}
            @else

            <p class="text-sm text-gray-500">
                No action available for your role.
            </p>

            @endif

        </div>

    </div>

</x-app-layout>
