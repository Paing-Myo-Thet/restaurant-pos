<x-app-layout>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Categories
                </h1>

                <p class="text-sm text-gray-500">
                    Manage your restaurant categories
                </p>
            </div>

            <a href="{{ route('categories.create') }}" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                + Add Category
            </a>
        </div>
        {{-- Success Message --}}
        @if(session('success'))
        <div class="rounded-lg bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
        @endif

        {{-- Categories Table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <table class="w-full text-left">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">
                            No
                        </th>

                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">
                            Name
                        </th>

                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">
                            Description
                        </th>

                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">
                            Status
                        </th>

                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse ($categories as $category)

                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4 text-sm">
                            {{ $loop->iteration }}
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $category->name }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $category->description ?? 'No description' }}
                        </td>

                        <td class="px-6 py-4">

                            @if ($category->status)
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Active
                            </span>
                            @else
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                Inactive
                            </span>
                            @endif

                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">

                                <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>

                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?')">

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
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No categories found.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
