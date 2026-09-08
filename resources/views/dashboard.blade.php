<x-app-layout>

    <div class="space-y-6">

        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Dashboard
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Welcome back! Here's what's happening in your restaurant today.
            </p>
        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Today's Sales --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Today's Sales
                        </p>

                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            0 Ks
                        </p>
                    </div>

                    <div class="rounded-lg bg-green-100 p-3 text-xl">
                        💰
                    </div>

                </div>
            </div>


            {{-- Today's Orders --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Today's Orders
                        </p>

                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            0
                        </p>
                    </div>

                    <div class="rounded-lg bg-blue-100 p-3 text-xl">
                        🧾
                    </div>

                </div>
            </div>


            {{-- Occupied Tables --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Occupied Tables
                        </p>

                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            0
                        </p>
                    </div>

                    <div class="rounded-lg bg-yellow-100 p-3 text-xl">
                        🪑
                    </div>

                </div>
            </div>


            {{-- Low Stock --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Low Stock Items
                        </p>

                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            0
                        </p>
                    </div>

                    <div class="rounded-lg bg-red-100 p-3 text-xl">
                        📦
                    </div>

                </div>
            </div>

        </div>


        {{-- Recent Orders --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">

            <div class="border-b px-6 py-4">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Recent Orders
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Latest restaurant orders
                        </p>
                    </div>

                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        View All
                    </a>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                        <tr class="border-b text-left text-sm text-gray-500">

                            <th class="px-6 py-3 font-medium">
                                Order
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Table
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Waiter
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Total
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>

                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">

                                🧾

                                <p class="mt-2">
                                    No orders yet.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>
