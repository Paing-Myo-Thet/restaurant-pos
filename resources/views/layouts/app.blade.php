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

    // [route, active pattern, icon, label, permission]
    $navigation = [
        ['dashboard', 'dashboard', '📊', 'Dashboard', 'dashboard.view'],
        ['sales.index', 'sales.*', '🧾', 'Orders', 'orders.view'],
        ['kitchen.index', 'kitchen.*', '👨‍🍳', 'Kitchen', 'kitchen.manage'],
        ['tables.index', 'tables.*', '🪑', 'Tables', 'tables.manage'],
        ['products.index', 'products.*', '🍔', 'Products', 'menu.manage'],
        ['categories.index', 'categories.*', '📂', 'Categories', 'menu.manage'],
        ['stock-movements.index', 'stock-movements.*', '📦', 'Inventory', 'inventory.manage'],
        ['customers.index', 'customers.*', '🙋', 'Customers', 'customers.manage'],
        ['users.index', 'users.*', '👥', 'Staff', 'users.manage'],
        ['roles.index', 'roles.*', '🔐', 'Roles', 'roles.manage'],
        ['reports.index', 'reports.*', '📈', 'Reports', 'reports.view'],
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
                @foreach ($navigation as [$route, $pattern, $icon, $label, $permission])
                    @can($permission)
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition duration-200
                           {{ request()->routeIs($pattern)
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <span class="text-lg">{{ $icon }}</span>
                            <span>{{ $label }}</span>
                        </a>
                    @endcan
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
                                {{ $user?->role?->name }}
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
                    <x-alert type="success">{{ session('success') }}</x-alert>
                @endif

                @if (session('error'))
                    <x-alert type="error">{{ session('error') }}</x-alert>
                @endif

                {{ $slot }}
            </section>
        </main>

    </div>

    {{--
        Confirmation dialog for any form with data-confirm="message".
        Optional: data-confirm-title, data-confirm-button.
    --}}
    <dialog id="confirmDialog"
            class="w-full max-w-md rounded-2xl p-0 shadow-2xl backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.3 2.9a2 2 0 0 1 3.4 0l6.3 11A2 2 0 0 1 16.3 17H3.7A2 2 0 0 1 2 13.9l6.3-11ZM10 7a1 1 0 0 0-1 1v3a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="pt-1">
                    <h3 class="text-lg font-semibold text-gray-900" data-confirm-title></h3>
                    <p class="mt-1 text-sm text-gray-600" data-confirm-message></p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 rounded-b-2xl bg-gray-50 px-6 py-4">
            <button type="button" data-confirm-cancel
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Keep it
            </button>
            <button type="button" data-confirm-ok
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
            </button>
        </div>
    </dialog>

    <script>
        (() => {
            // Alerts: close button, and auto-dismiss with a countdown bar that pauses on hover.
            document.querySelectorAll('[data-alert]').forEach(alert => {
                const dismiss = () => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-4px)';
                    setTimeout(() => alert.remove(), 300);
                };

                alert.querySelector('[data-alert-close]')?.addEventListener('click', dismiss);

                const delay = Number(alert.dataset.autoDismiss);
                const bar = alert.querySelector('[data-alert-progress]');
                if (!delay) return;

                let timer;
                const start = () => {
                    bar.style.transition = `transform ${delay}ms linear`;
                    bar.style.transform = 'scaleX(0)';
                    timer = setTimeout(dismiss, delay);
                };
                const pause = () => {
                    clearTimeout(timer);
                    bar.style.transition = 'none';
                    bar.style.transform = 'scaleX(1)';
                };

                alert.addEventListener('mouseenter', pause);
                alert.addEventListener('mouseleave', start);
                requestAnimationFrame(() => requestAnimationFrame(start));
            });

            // Confirm dialog for forms with data-confirm.
            const dialog = document.getElementById('confirmDialog');
            const okButton = dialog.querySelector('[data-confirm-ok]');
            let pendingForm = null;

            document.addEventListener('submit', event => {
                const form = event.target;
                if (!form.dataset.confirm) return;

                event.preventDefault();
                pendingForm = form;

                dialog.querySelector('[data-confirm-title]').textContent = form.dataset.confirmTitle || 'Are you sure?';
                dialog.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
                okButton.textContent = form.dataset.confirmButton || 'Yes, continue';
                dialog.showModal();
                okButton.focus();
            });

            okButton.addEventListener('click', () => {
                okButton.disabled = true;
                pendingForm?.submit();
            });

            dialog.querySelector('[data-confirm-cancel]').addEventListener('click', () => dialog.close());

            // Clicking the dark backdrop closes the dialog.
            dialog.addEventListener('click', event => {
                if (event.target === dialog) dialog.close();
            });

            dialog.addEventListener('close', () => {
                pendingForm = null;
                okButton.disabled = false;
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
