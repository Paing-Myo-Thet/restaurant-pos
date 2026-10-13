<x-app-layout>

    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Order {{ $sale->order_number }}</h1>
                <p class="text-sm text-gray-500">Stock is adjusted only for the items you change.</p>
            </div>

            <a href="{{ route('sales.show', $sale) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Back to order
            </a>
        </div>

        @include('sales._form', [
            'action' => route('sales.update', $sale),
            'method' => 'PUT',
            'submitLabel' => 'Save changes',
        ])

    </div>

</x-app-layout>
