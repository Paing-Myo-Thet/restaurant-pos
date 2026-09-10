<x-app-layout>

    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Sales / Orders
                </h1>

                <p class="text-sm text-gray-500">
                    Manage restaurant orders
                </p>
            </div>

            <a href="{{ route('sales.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                + New Order
            </a>
        </div>


        {{-- Success Message --}}
        @if (session('success'))
        <div class="rounded-lg bg-green-100 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
        @endif
        @if(session('dl-success'))
        <div class="bg-red-400 rounded px-2 py-2 text-white font-bolder text-lg ">
            {{session('dl-success')}}
        </div>
        @endif


        {{-- Sales Table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <table class="w-full">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Order
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Customer
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Table
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Staff
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

                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">
                            Action
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y">

                    @forelse ($sales as $sale)

                    <tr class="hover:bg-gray-50">

                        {{-- Order --}}
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            #{{ $sale->id }}
                        </td>


                        {{-- Customer --}}
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $sale->customer?->name ?? 'Walk-IN Customer' }}
                        </td>


                        {{-- Table --}}
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $sale->table?->name ?? 'Take-way'}}
                        </td>


                        {{-- Staff --}}
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $sale->user?->name ?? 'Unknown' }}
                        </td>


                        {{-- Total --}}
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                            {{ number_format($sale->total, 2) }}
                        </td>


                        {{-- Status --}}
                        <td class="px-6 py-4">

                            @if ($sale->status === 'pending')

                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">
                                Pending
                            </span>

                            @elseif ($sale->status === 'sent')

                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                Sent
                            </span>

                            @elseif ($sale->status === 'preparing')

                            <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">
                                Preparing
                            </span>

                            @elseif ($sale->status === 'ready')

                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                Ready
                            </span>

                            @elseif ($sale->status === 'served')

                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700">
                                Served
                            </span>

                            @else

                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                Cancelled
                            </span>

                            @endif

                        </td>


                        {{-- Payment --}}
                        <td class="px-6 py-4">

                            @if ($sale->payment_status === 'paid')

                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                Paid
                            </span>

                            @else

                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                Unpaid
                            </span>

                            @endif

                        </td>


                        {{-- Action --}}
                        <td class="px-6 py-4 text-right">

                            <a href="{{ route('sales.show', $sale) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                View
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

    </div>

</x-app-layout>
