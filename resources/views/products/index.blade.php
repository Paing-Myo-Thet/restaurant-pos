<x-app-layout>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Products
                </h1>

                <p class="text-sm text-gray-500">
                    Manage your restaurant products
                </p>
            </div>

            <a href="{{ route('products.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                + Add Product
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="rounded-lg bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
        @endif

        {{-- Products Table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <table class="w-full text-left text-sm">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4">No.</th>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Cost</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse($products as $product)

                    <tr>

                        <td class="px-6 py-4">
                            {{ $product->id}}
                        </td>

                        <td class="px-6 py-4 font-medium">
                            {{ $product->name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $product->category->name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ number_format($product->cost, 2) }}
                        </td>

                        <td class="px-6 py-4">
                            {{ number_format($product->price, 2) }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $product->stock }}
                        </td>
                        <td class="px-6 py-4">
                            {{$product->description ?? '--'}}
                        </td>
                        <td class="px-6 py-4">
                            @if($product->status)
                            <span class="text-green-600">
                                Active
                            </span>
                            @else
                            <span class="text-red-600">
                                Inactive
                            </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">

                                <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Are you sure you want to delete this product?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        Delete
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
