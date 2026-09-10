<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Restaurant POS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-900">

    <div class="min-h-screen">

        {{-- Sidebar --}}
        <aside class="fixed left-0 top-0 h-screen w-64 bg-gray-900 text-white shadow-xl">

            {{-- Logo --}}
            <div class="flex h-16 items-center border-b border-gray-700 px-6">
                <h1 class="text-xl font-bold">
                    🍽️ Restaurant POS
                </h1>
            </div>

            {{-- Navigation --}}
            <nav class="mt-4 space-y-1 px-3">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>📊</span>
                    <span>Dashboard</span>

                </a>

                {{-- Orders --}}
                <a href="{{route('sales.index')}}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>🧾</span>
                    <span>Orders</span>

                </a>

                {{-- Tables --}}
                <a href="{{route('tables.index')}}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>🪑</span>
                    <span>Tables</span>

                </a>

                {{-- Products --}}
                <a href="{{route('products.index')}}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>🍔</span>
                    <span>Products</span>

                </a>

                {{-- Categories --}}
                <a href="{{route('categories.index')}}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>📂</span>
                    <span>Categories</span>

                </a>

                {{-- Inventory --}}
                <a href="#" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>📦</span>
                    <span>Inventory</span>

                </a>

                {{-- Users --}}
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>👥</span>
                    <span>Users</span>

                </a>

                {{-- Reports --}}
                <a href="#" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-300
                  transition hover:bg-gray-800 hover:text-white">

                    <span>📈</span>
                    <span>Reports</span>

                </a>

            </nav>

        </aside>

        {{-- Main Content --}}
        <main class="ml-64 min-h-screen pl-[2px]">
            <header class="flex h-16 items-center justify-between border-b bg-white px-6">

                {{-- Page / User --}}
                <div>

                    <span class="ml-2 font-semibold text-gray-900">
                        {{ Auth::user()?->name }}
                    </span>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Logout
                    </button>
                </form>

            </header>
            <section>
                {{ $slot }}
            </section>
        </main>

    </div>

    @stack('scripts')
</body>
</html>
