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
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
        @enderror

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
