<x-app-layout>

    <div class="max-w-xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold  text-gray-800">
                Stock In
            </h1>

            <p class="text-sm text-gray-500">
                Add stock to a product
            </p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
        <div class="rounded-lg bg-red-100 px-4 py-3 text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Stock In Form --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <form action="{{ route('stock-movements.store') }}" method="POST" class="space-y-6">

                @csrf

                {{-- Product --}}
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700">
                        Product
                    </label>

                    <select name="product_id" id="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Select Product</option>

                        @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (Current stock: {{ $product->stock }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">
                        Quantity
                    </label>

                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                {{-- Note --}}
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">
                        Note <span class="text-gray-400">(optional)</span>
                    </label>

                    <input type="text" name="note" id="note" value="{{ old('note') }}" maxlength="255" placeholder="e.g. Delivery from supplier" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">

                    <a href="{{ route('stock-movements.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700">
                        Cancel
                    </a>

                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white">
                        Add Stock
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
