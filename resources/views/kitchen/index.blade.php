<x-app-layout>

    @push('styles')
        <meta http-equiv="refresh" content="20">
    @endpush

    @php
        $actions = [
            'sent' => ['preparing', 'Start', 'bg-orange-500 hover:bg-orange-600'],
            'preparing' => ['ready', 'Ready', 'bg-green-600 hover:bg-green-700'],
        ];
        $headers = [
            'sent' => 'bg-blue-600',
            'preparing' => 'bg-orange-500',
            'ready' => 'bg-green-600',
        ];
    @endphp

    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kitchen</h1>
                <p class="text-sm text-gray-500">Refreshes every 20 seconds · {{ now()->format('h:i:s A') }}</p>
            </div>
        </div>

        @error('status')
            <x-alert type="error" title="Status not changed" class="!mb-0">{{ $message }}</x-alert>
        @enderror

        @if ($cancelled->isNotEmpty())
            <div class="rounded-xl border-2 border-red-500 bg-red-50 p-4">
                <h2 class="font-bold text-red-700">🛑 Cancelled: stop making these</h2>

                <ul class="mt-3 space-y-2">
                    @foreach ($cancelled as $order)
                        <li class="rounded-lg bg-white p-3 text-sm shadow-sm">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <span class="font-semibold text-gray-900">
                                    {{ $order->order_number }} · {{ $order->table?->name ?? 'Takeaway' }}
                                </span>
                                <span class="text-xs text-gray-500">cancelled {{ $order->cancelled_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-gray-700">
                                @foreach ($order->items as $item)
                                    {{ $item->product_name }} × {{ $item->quantity }}@if (! $loop->last), @endif
                                @endforeach
                            </p>
                            @if ($order->cancel_reason)
                                <p class="mt-1 text-xs text-red-700">{{ $order->cancel_reason }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            @foreach ($columns as $status => $label)
                @php $column = $orders->get($status, collect()); @endphp

                <div class="flex flex-col rounded-xl bg-gray-200/60">
                    <div class="flex items-center justify-between rounded-t-xl px-4 py-3 text-white {{ $headers[$status] }}">
                        <h2 class="font-bold">{{ $label }}</h2>
                        <span class="rounded-full bg-white/25 px-2.5 py-0.5 text-sm font-semibold">{{ $column->count() }}</span>
                    </div>

                    <div class="space-y-3 p-3">
                        @forelse ($column as $order)
                            @php
                                $minutes = (int) ($order->sent_at ?? $order->created_at)->diffInMinutes(now());
                            @endphp

                            <div class="rounded-lg bg-white p-4 shadow-sm {{ $minutes >= 15 && $status !== 'ready' ? 'ring-2 ring-red-500' : '' }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900">{{ $order->table?->name ?? 'Takeaway' }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->order_number }}</p>
                                    </div>
                                    <span class="text-sm font-semibold {{ $minutes >= 15 ? 'text-red-600' : 'text-gray-500' }}">
                                        {{ $minutes }} min
                                    </span>
                                </div>

                                <ul class="mt-3 space-y-1">
                                    @foreach ($order->items as $item)
                                        <li class="flex gap-3 text-base">
                                            <span class="w-8 shrink-0 font-bold">{{ $item->quantity }}×</span>
                                            <span>{{ $item->product_name }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                @if ($order->note)
                                    <p class="mt-3 rounded bg-yellow-50 px-2 py-1 text-sm font-medium text-yellow-900">
                                        ⚠ {{ $order->note }}
                                    </p>
                                @endif

                                @isset($actions[$status])
                                    @php [$next, $buttonLabel, $buttonClass] = $actions[$status]; @endphp
                                    <form action="{{ route('sales.status', $order) }}" method="POST" class="mt-4">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $next }}">
                                        <button type="submit" class="w-full rounded-lg py-2.5 font-semibold text-white {{ $buttonClass }}">
                                            {{ $buttonLabel }}
                                        </button>
                                    </form>
                                @else
                                    <p class="mt-4 text-center text-sm text-gray-500">Waiting for a waiter to serve</p>
                                @endisset
                            </div>
                        @empty
                            <p class="py-10 text-center text-sm text-gray-500">Nothing here</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</x-app-layout>
