<x-app-layout>

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Edit Product
            </h1>

            <p class="text-sm text-gray-500">
                Update product information
            </p>
        </div>

        {{-- Form --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <form method="POST" action="{{ route('products.update', $product) }}">

                @csrf
                @method('PUT')

                <div class="space-y-5">

                    {{-- Product Name --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Product Name
                        </label>

                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-lg border-gray-300">

                        @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Category
                        </label>

                        <select name="category_id" class="w-full rounded-lg border-gray-300">

                            @foreach($categories as $category)

                            <option value="{{ $category->id }}" {{ old('category_id', $product->category->id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>

                            @endforeach

                        </select>

                        @error('category_id')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Description
                        </label>

                        <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300">{{ old('description', $product->description) }}</textarea>

                        @error('description')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Cost --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Cost
                        </label>

                        <input type="number" name="cost" step="0.01" value="{{ old('cost', $product->cost) }}" class="w-full rounded-lg border-gray-300">

                        @error('cost')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Selling Price --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Selling Price
                        </label>

                        <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" class="w-full rounded-lg border-gray-300">

                        @error('price')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Stock --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Stock
                        </label>

                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full rounded-lg border-gray-300">

                        @error('stock')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-3 pt-3">

                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700">
                            Update Product
                        </button>

                        <a href="{{ route('products.index') }}" class="rounded-lg bg-gray-200 px-5 py-2 font-semibold text-gray-700 hover:bg-gray-300">
                            Cancel
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
