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
            {{-- Navigation --}}
            <nav class="mt-6 space-y-2 px-3">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
       {{ request()->routeIs('dashboard')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">📊</span>
                    <span>Dashboard</span>

                </a>

                {{-- Orders --}}
                <a href="{{ route('sales.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
       {{ request()->routeIs('sales.*')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">🧾</span>
                    <span>Orders</span>

                </a>

                {{-- Tables --}}
                <a href="{{ route('tables.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
       {{ request()->routeIs('tables.*')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">🪑</span>
                    <span>Tables</span>

                </a>

                {{-- Products --}}
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
       {{ request()->routeIs('products.*')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">🍔</span>
                    <span>Products</span>

                </a>

                {{-- Categories --}}
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
       {{ request()->routeIs('categories.*')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">📂</span>
                    <span>Category</span>

                </a>

                {{-- Inventory --}}
                <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-3 rounded-xl
                 px-4 py-3 text-sm font-medium
       {{ request()->routeIs('stock-movements.*')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">📦</span>
                    <span>Inventory</span>

                </a>

                {{-- Users --}}
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
       {{ request()->routeIs('users.*')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">👥</span>
                    <span>Users</span>

                </a>

                {{-- Reports --}}
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
       {{ request()->routeIs('reports.*')
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}
       transition duration-200">

                    <span class="text-lg">📈</span>
                    <span>Reports</span>

                </a>

            </nav>

        </aside>

        {{-- Main Content --}}
        <main class="ml-64 min-h-screen pl-[2px]">
            <header class="flex h-20 items-center justify-between border-b border-gray-200 bg-white px-8">

                {{-- Left side --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Welcome back
                    </p>

                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ Auth::user()?->name }}
                    </h2>
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-4">

                    {{-- User --}}
                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white">
                            {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}
                        </div>

                        <div class="hidden sm:block">
                            <p class="text-sm font-medium text-gray-900">
                                {{ Auth::user()?->name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ Auth::user()?->role?->value }}
                            </p>
                        </div>

                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium
                       text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
                            Logout
                        </button>
                    </form>

                </div>

            </header>
            <section>
                {{ $slot }}
            </section>
        </main>

    </div>

    @stack('scripts')
</body>
</html>
