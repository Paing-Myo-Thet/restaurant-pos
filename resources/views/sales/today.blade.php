<x-app-layout>

    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Today's Orders
            </h1>

            <p class="text-sm text-gray-500">
                Orders created today
            </p>
        </div>

        @if (session('success'))
        <div class="rounded-lg bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="rounded-lg bg-red-100 px-4 py-3 text-red-700">
            {{ session('error') }}
        </div>
        @endif

        <div class="overflow-hidden rounded-lg bg-white shadow">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Order #
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Table
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Customer
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Total
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Payment
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Time
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse ($sales as $sale)

                    <tr>

                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                            #{{ $sale->id }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $sale->table?->name ?? 'Takeaway' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                        </td>

                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                            {{ number_format($sale->total, 2) }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            {{ ucfirst($sale->status) }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            {{ ucfirst($sale->payment_status) }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $sale->created_at->format('H:i') }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">

                                <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-800">
                                    View
                                </a>

                                @if ($sale->status === 'pending')
                                <form method="POST" action="{{ route('sales.status', $sale) }}">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="sent">

                                    <button type="submit" class="text-green-600 hover:text-green-800">
                                        Send
                                    </button>
                                </form>

                                @elseif ($sale->status === 'sent')
                                <form method="POST" action="{{ route('sales.status', $sale) }}">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="preparing">

                                    <button type="submit" class="text-orange-600 hover:text-orange-800">
                                        Prepare
                                    </button>
                                </form>

                                @elseif ($sale->status === 'preparing')
                                <form method="POST" action="{{ route('sales.status', $sale) }}">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="ready">

                                    <button type="submit" class="text-purple-600 hover:text-purple-800">
                                        Ready
                                    </button>
                                </form>

                                @elseif ($sale->status === 'ready')
                                <form method="POST" action="{{ route('sales.status', $sale) }}">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="served">

                                    <button type="submit" class="text-blue-600 hover:text-blue-800">
                                        Served
                                    </button>
                                </form>
                                @endif

                                @if (in_array($sale->status, ['pending', 'sent', 'preparing']))
                                <form method="POST" action="{{ route('sales.status', $sale) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="cancelled">

                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        Cancel
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            No orders found for today.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
