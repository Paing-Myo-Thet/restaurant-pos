<x-app-layout>

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Add Table
            </h1>

            <p class="text-sm text-gray-500">
                Create a new restaurant table
            </p>
        </div>


        {{-- Form --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <form action="{{ route('tables.store') }}" method="POST" class="space-y-6">

                @csrf

                {{-- Table Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Table Name
                    </label>

                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Table 1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>


                {{-- Capacity --}}
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">
                        Capacity
                    </label>

                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', 4) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('capacity')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>


                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                        Status
                    </label>

                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>
                            available
                        </option>

                        <option value="occupied" {{ old('status') === 'occupied' ? 'selected' : '' }}>
                            Occupied
                        </option>

                        <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>
                            Reserved
                        </option>
                    </select>

                    @error('status')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>


                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3">

                    <a href="{{ route('tables.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Save Table
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
