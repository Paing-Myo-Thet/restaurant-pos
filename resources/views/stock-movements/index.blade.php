<x-app-layout>

    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Stock Movements
                </h1>

                <p class="text-sm text-gray-500">
                    View stock movement history
                </p>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
        <div class="rounded-lg bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
        @endif

        {{-- Stock Movement Table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Product
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Type
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Quantity
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            User
                        </th>

                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Date
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse ($stockMovements as $movement)

                    <tr>

                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $movement->product->name }}
                        </td>

                        <td class="px-6 py-4 text-sm">
                            {{ ucfirst($movement->type) }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $movement->quantity }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $movement->user->name }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $movement->created_at->format('Y-m-d H:i') }}
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

    </div>

</x-app-layout>
