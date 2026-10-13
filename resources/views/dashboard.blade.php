<x-app-layout>

    <div class="space-y-8">

        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Welcome back! Here's what's happening in your restaurant today.
                </p>
            </div>

            {{-- New Order --}}
            <div class="items-center flex">
                <a href="{{ route('sales.today') }}" class="block rounded-lg px-4 py-2 text-white hover:bg-blue-800 bg-blue-600 font-md ">
                    Today's Orders
                </a>
                <a href="{{ route('sales.create') }}" class="inline-flex mx-3 items-center justify-center gap-2 rounded-xl bg-gray-900 px-5 py-3
                   text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800">
                    <span>＋</span>
                    New Order
                </a>
            </div>

        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Today's Sales --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Today's Sales
                        </p>

                        <p class="mt-3 text-2xl font-bold text-gray-900">
                            {{ number_format($todaySales, 2) }} Ks
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-xl">
                        💰
                    </div>

                </div>

                <p class="mt-4 text-xs text-gray-400">
                    Today's revenue
                </p>

            </div>


            {{-- Today's Orders --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Today's Orders
                        </p>

                        <p class="mt-3 text-2xl font-bold text-gray-900">
                            {{number_format($todayOrders, 2)}}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-xl">
                        🧾
                    </div>

                </div>

                <p class="mt-4 text-xs text-gray-400">
                    Orders created today
                </p>

            </div>


            {{-- Occupied Tables --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Occupied Tables
                        </p>

                        <p class="mt-3 text-2xl font-bold text-gray-900">
                            {{$occupiedTables}}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-yellow-100 text-xl">
                        🪑
                    </div>

                </div>

                <p class="mt-4 text-xs text-gray-400">
                    Currently occupied
                </p>

            </div>


            {{-- Low Stock --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Lows Stock Items
                        </p>

                        <p class="mt-3 text-2xl font-bold text-gray-900">
                            0
                        </p>
                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-xl">
                        📦
                    </div>

                </div>

                <p class="mt-4 text-xs text-gray-400">
                    Products need attention
                </p>

            </div>

        </div>


        {{-- Quick Actions --}}
        <div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    Quick Actions
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Quickly access common restaurant tasks.
                </p>
            </div>


            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Orders --}}
                <a href="{{ route('sales.index') }}" class="group rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                       transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-4">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-xl">
                            🧾
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Orders
                            </p>

                            <p class="text-sm text-gray-500">
                                Manage orders
                            </p>
                        </div>

                    </div>
                </a>


                {{-- Tables --}}
                <a href="{{ route('tables.index') }}" class="group rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                       transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-4">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-yellow-100 text-xl">
                            🪑
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Tables
                            </p>

                            <p class="text-sm text-gray-500">
                                Manage tables
                            </p>
                        </div>

                    </div>
                </a>


                {{-- Products --}}
                <a href="{{ route('products.index') }}" class="group rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                       transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-4">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-xl">
                            🍔
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Products
                            </p>

                            <p class="text-sm text-gray-500">
                                Manage products
                            </p>
                        </div>

                    </div>
                </a>


                {{-- Reports --}}
                <a href="{{ route('reports.index') }}" class="group rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                       transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center gap-4">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-100 text-xl">
                            📈
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">
                                Reports
                            </p>

                            <p class="text-sm text-gray-500">
                                View sales reports
                            </p>
                        </div>

                    </div>
                </a>

            </div>

        </div>


        {{-- Recent Orders --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">

            <div class="border-b border-gray-200 px-6 py-5">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Recent Orders
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Latest restaurant orders
                        </p>
                    </div>

                    <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-gray-700 transition hover:text-gray-900">
                        View All →
                    </a>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                        <tr class="border-b border-gray-200 text-left text-xs uppercase tracking-wide text-gray-500">

                            <th class="px-6 py-4 font-semibold">
                                Order
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Table
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Waiter
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Total
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>

                            <td colspan="5" class="px-6 py-12 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-2xl">
                                        🧾
                                    </div>

                                    <p class="mt-4 text-sm font-medium text-gray-900">
                                        No orders yet
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Orders will appear here once they are created.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>
