<x-app-layout>

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Edit Category
            </h1>

            <p class="text-sm text-gray-500">
                Update category information
            </p>
        </div>

        {{-- Form --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <form action="{{ route('categories.update', $category) }}" method="POST">

                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="mb-5">
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                        Category Name
                    </label>

                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">

                    @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-5">
                    <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                        Description
                    </label>

                    <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('description', $category->description) }}</textarea>

                    @error('description')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-6">
                    <label class="flex items-center gap-2">

                        <input type="checkbox" name="status" value="1" {{ old('status', $category->status) ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">

                        <span class="text-sm text-gray-700">
                            Active
                        </span>

                    </label>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3">

                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700">
                        Update Category
                    </button>

                    <a href="{{ route('categories.index') }}" class="rounded-lg bg-gray-200 px-5 py-2 font-semibold text-gray-700 hover:bg-gray-300">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
