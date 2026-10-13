<x-app-layout>

    <div class="max-w-xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Stock Out
            </h1>

            <p class="text-gray-600 mt-1">
                Remove stock from a product.
            </p>
        </div>

        <div class="bg-white shadow rounded-lg p-6">

            <form action="{{ route('stock-movements.out') }}" method="POST">
                @csrf

                {{-- Product --}}
                <div class="mb-4">
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Product
                    </label>

                    <select name="product_id" id="product_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select Product</option>

                        @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (Stock: {{ $product->stock }})
                        </option>
                        @endforeach
                    </select>

                    @error('product_id')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Quantity --}}
                <div class="mb-6">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantity
                    </label>

                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" class="w-full border-gray-300 rounded-md shadow-sm" required>

                    @error('quantity')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">

                    <a href="{{ route('stock-movements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>

                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Remove Stock
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
