<x-app-layout>

    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Tables
                </h1>

                <p class="text-sm text-gray-500">
                    Manage restaurant tables
                </p>
            </div>

            <a href="{{ route('tables.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                + Add Table
            </a>
        </div>


        {{-- Success Message --}}
        @if (session('success'))
        <div class="rounded-lg bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
        @endif


        {{-- Tables --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Table</th>
                            <th class="px-6 py-4">Capacity</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse ($tables as $table)

                        <tr class="hover:bg-gray-50">

                            {{-- ID --}}
                            <td class="px-6 py-4">
                                {{ $table->id }}
                            </td>

                            {{-- Table Name --}}
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $table->name }}
                            </td>

                            {{-- Capacity --}}
                            <td class="px-6 py-4">
                                {{ $table->capacity }} people
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">

                                @if ($table->status === 'available')

                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Available
                                </span>

                                @elseif ($table->status === 'occupied')

                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Occupied
                                </span>

                                @else

                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                    Reserved
                                </span>

                                @endif

                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">

                                <a href="{{ route('tables.edit', $table) }}" class="mr-3 font-medium text-blue-600 hover:underline">
                                    Edit
                                </a>

                                <form action="{{ route('tables.destroy', $table) }}" method="POST" class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this table?')" class="font-medium text-red-600 hover:underline">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">

                                No tables found.

                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>
