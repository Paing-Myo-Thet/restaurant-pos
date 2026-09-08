<x-app-layout>

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Add Product
            </h1>

            <p class="text-sm text-gray-500">
                Create a new restaurant product
            </p>
        </div>

        {{-- Form --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <form method="POST" action="{{ route('products.store') }}">

                @csrf

                <div class="space-y-5">

                    {{-- Product Name --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Product Name
                        </label>

                        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border-gray-300" placeholder="Enter product name">

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
                            <option value="">
                                Select Category
                            </option>

                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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

                        <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300" placeholder="Enter product description">{{ old('description') }}</textarea>
                    </div>

                    {{-- Cost --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Cost
                        </label>

                        <input type="number" name="cost" step="0.01" value="{{ old('cost') }}" class="w-full rounded-lg border-gray-300" placeholder="3000">

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

                        <input type="number" name="price" step="0.01" value="{{ old('price') }}" class="w-full rounded-lg border-gray-300" placeholder="5000">

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

                        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full rounded-lg border-gray-300" placeholder="10">

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
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-3 pt-3">

                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700">
                            Save Product
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
