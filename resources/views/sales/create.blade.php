<x-app-layout>

    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">New Order</h1>
                <p class="text-sm text-gray-500">Tap items to add them, then send the order.</p>
            </div>

            <a href="{{ route('sales.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Orders
            </a>
        </div>

        @include('sales._form', [
            'action' => route('sales.store'),
            'method' => 'POST',
            'submitLabel' => 'Create order',
        ])

    </div>

</x-app-layout>
