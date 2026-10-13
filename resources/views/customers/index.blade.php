<x-app-layout>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Customers
                </h1>

                <p class="text-sm text-gray-500">
                    Manage your restaurant customers
                </p>
            </div>

            <a href="{{ route('customers.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">
                + Add Customer
            </a>

        </div>

        {{-- Success --}}

        {{-- Table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <table class="w-full text-left text-sm">

                <thead class="bg-gray-100">

                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Address</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>

                </thead>

                <tbody class="divide-y">

                    @forelse($customers as $customer)

                    <tr>

                        <td class="px-6 py-4">
                            {{ $customer->id }}
                        </td>

                        <td class="px-6 py-4 font-medium">
                            {{ $customer->name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $customer->phone }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $customer->email }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $customer->address }}
                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm-title="Delete customer?" data-confirm="{{ $customer->name }} will be removed permanently. Customers with past orders can't be deleted." data-confirm-button="Delete customer">

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

                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">

                            No customers found.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
