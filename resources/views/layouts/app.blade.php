<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Restaurant POS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

@php
    $user = Auth::user();

    // [route, active pattern, icon, label, roles]
    $navigation = [
        ['dashboard', 'dashboard', '📊', 'Dashboard', ['admin', 'cashier', 'waiter']],
        ['sales.index', 'sales.*', '🧾', 'Orders', ['admin', 'cashier', 'waiter', 'kitchen']],
        ['kitchen.index', 'kitchen.*', '👨‍🍳', 'Kitchen', ['admin', 'kitchen']],
        ['tables.index', 'tables.*', '🪑', 'Tables', ['admin', 'waiter']],
        ['products.index', 'products.*', '🍔', 'Products', ['admin']],
        ['categories.index', 'categories.*', '📂', 'Categories', ['admin']],
        ['stock-movements.index', 'stock-movements.*', '📦', 'Inventory', ['admin']],
        ['customers.index', 'customers.*', '🙋', 'Customers', ['admin']],
        ['users.index', 'users.*', '👥', 'Staff', ['admin']],
        ['reports.index', 'reports.*', '📈', 'Reports', ['admin']],
    ];
@endphp

<body class="bg-gray-100 text-gray-900">

    <div class="min-h-screen">

        {{-- Sidebar --}}
        <aside class="fixed left-0 top-0 h-screen w-64 overflow-y-auto bg-gray-900 text-white shadow-xl">

            {{-- Logo --}}
            <div class="flex h-16 items-center border-b border-gray-700 px-6">
                <h1 class="text-xl font-bold">
                    🍽️ Restaurant POS
                </h1>
            </div>

            {{-- Navigation --}}
            <nav class="mt-6 space-y-1 px-3">
                @foreach ($navigation as [$route, $pattern, $icon, $label, $roles])
                    @if ($user?->hasRole(...$roles))
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200
                           {{ request()->routeIs($pattern)
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <span class="text-lg">{{ $icon }}</span>
                            <span>{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

        </aside>

        {{-- Main Content --}}
        <main class="ml-64 min-h-screen">
            <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-8">

                <div>
                    <p class="text-xs text-gray-500">
                        {{ now()->format('l, F j, Y') }}
                    </p>

                    <h2 class="text-base font-semibold text-gray-900">
                        {{ $user?->name }}
                    </h2>
                </div>

                <div class="flex items-center gap-4">

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white">
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        </div>

                        <div class="hidden sm:block">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $user?->name }}
                            </p>

                            <p class="text-xs capitalize text-gray-500">
                                {{ $user?->role?->value }}
                            </p>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium
                       text-gray-700 transition hover:bg-gray-100 hover:text-gray-900">
                            Logout
                        </button>
                    </form>

                </div>

            </header>

            <section class="p-6 lg:p-8">

                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </section>
        </main>

    </div>

    @stack('scripts')
</body>
</html>
