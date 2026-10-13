<x-app-layout>

    @php
        $user = auth()->user();
        $statusButtons = [
            'sent' => ['Send to kitchen', 'bg-blue-600 hover:bg-blue-700'],
            'preparing' => ['Start preparing', 'bg-orange-500 hover:bg-orange-600'],
            'ready' => ['Mark ready', 'bg-green-600 hover:bg-green-700'],
            'served' => ['Mark served', 'bg-gray-900 hover:bg-gray-700'],
        ];
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Order {{ $sale->order_number }}
                    </h1>
                    <x-order-status :status="$sale->status" />
                    <x-payment-status :sale="$sale" />
                </div>

                <p class="mt-1 text-sm text-gray-500">
                    Created {{ $sale->created_at->format('d M Y, h:i A') }} by {{ $sale->user?->name ?? 'Unknown' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('sales.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    ← Orders
                </a>

                @if ($user->hasRole('admin', 'cashier', 'waiter'))
                    @if ($sale->isEditable())
                        <a href="{{ route('sales.edit', $sale) }}" class="rounded-lg bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600">
                            Edit items
                        </a>
                    @endif

                    <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                       class="rounded-lg px-4 py-2 text-sm font-medium text-white {{ session('print_receipt') ? 'bg-green-600 hover:bg-green-700 ring-4 ring-green-200' : 'bg-gray-700 hover:bg-gray-800' }}">
                        🖨 {{ $user->hasRole('waiter') ? 'Print draft bill' : ($sale->isPaid() ? 'Print receipt' : 'Print bill') }}
                    </a>
                @endif
            </div>
        </div>

        @error('status')
            <x-alert type="error" title="Status not changed" class="!mb-0">{{ $message }}</x-alert>
        @enderror

        {{-- Status actions --}}
        @if ($nextStatuses)
            <div class="flex flex-wrap items-center gap-3 rounded-lg bg-white p-4 shadow">
                <span class="text-sm font-medium text-gray-500">Next step:</span>

                @foreach ($nextStatuses as $next)
                    <form action="{{ route('sales.status', $sale) }}" method="POST"
                          @if ($next === 'cancelled') data-confirm-title="Cancel order {{ $sale->order_number }}?" data-confirm="All items go back into stock and the table is freed. This can't be undone." data-confirm-button="Cancel order" @endif>
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next }}">

                        @if ($next === 'cancelled')
                            <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                                Cancel order
                            </button>
                        @else
                            <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white {{ $statusButtons[$next][1] }}">
                                {{ $statusButtons[$next][0] }}
                            </button>
                        @endif
                    </form>
                @endforeach
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Left: details + items --}}
            <div class="space-y-6 lg:col-span-2">

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="text-xs font-semibold uppercase text-gray-500">Table</p>
                        <p class="mt-1 font-semibold text-gray-800">{{ $sale->table?->name ?? 'Takeaway' }}</p>
                    </div>

                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="text-xs font-semibold uppercase text-gray-500">Customer</p>
                        <p class="mt-1 font-semibold text-gray-800">{{ $sale->customer?->name ?? 'Walk-in' }}</p>
                    </div>

                    <div class="col-span-2 rounded-lg bg-white p-4 shadow md:col-span-1">
                        <p class="text-xs font-semibold uppercase text-gray-500">Sent to kitchen</p>
                        <p class="mt-1 font-semibold text-gray-800">{{ $sale->sent_at?->format('h:i A') ?? '—' }}</p>
                    </div>
                </div>

                @if ($sale->note)
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-900">
                        <span class="font-semibold">Note:</span> {{ $sale->note }}
                    </div>
                @endif

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b px-6 py-4">
                        <h2 class="text-lg font-bold text-gray-800">Items</h2>
                    </div>

                    <table class="w-full text-sm">
                        <thead class="border-b bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left">Product</th>
                                <th class="px-6 py-3 text-right">Price</th>
                                <th class="px-6 py-3 text-center">Qty</th>
                                <th class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $item->product_name }}</td>
                                    <td class="px-6 py-3 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-3 text-center">{{ $item->quantity }}</td>
                                    <td class="px-6 py-3 text-right font-medium">{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right: summary + checkout --}}
            <div class="space-y-6">

                <div class="rounded-lg bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-gray-800">Summary</h2>

                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Subtotal</dt>
                            <dd>{{ number_format($sale->sub_total, 2) }} Ks</dd>
                        </div>

                        @if ($sale->descount > 0)
                            <div class="flex justify-between text-red-600">
                                <dt>Discount</dt>
                                <dd>− {{ number_format($sale->descount, 2) }} Ks</dd>
                            </div>
                        @endif

                        <div class="flex justify-between border-t pt-3 text-lg font-bold">
                            <dt>Total</dt>
                            <dd class="text-green-700">{{ number_format($sale->total, 2) }} Ks</dd>
                        </div>

                        @if ($sale->isPaid())
                            <div class="flex justify-between pt-2">
                                <dt class="text-gray-600">Paid by</dt>
                                <dd class="capitalize">{{ $sale->payment_method ?? '—' }}</dd>
                            </div>
                            @if ($sale->payment_method === 'cash')
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Received</dt>
                                    <dd>{{ number_format($sale->amount_received, 2) }} Ks</dd>
                                </div>
                                <div class="flex justify-between font-semibold">
                                    <dt class="text-gray-600">Change</dt>
                                    <dd>{{ number_format($sale->change_due, 2) }} Ks</dd>
                                </div>
                            @endif
                            <div class="flex justify-between text-gray-500">
                                <dt>Paid at</dt>
                                <dd>{{ $sale->paid_at?->format('d M, h:i A') ?? '—' }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Checkout --}}
                @if ($sale->canCheckout() && $user->hasRole('admin', 'cashier'))
                    <form action="{{ route('sales.checkout', $sale) }}" method="POST" id="checkoutForm"
                          class="space-y-4 rounded-lg bg-white p-6 shadow ring-2 ring-green-500">
                        @csrf

                        <h2 class="text-lg font-bold text-gray-800">Take payment</h2>

                        @if ($errors->hasAny(['payment', 'payment_method', 'descount', 'amount_received']))
                            <div class="rounded bg-red-50 px-3 py-2 text-sm text-red-700">
                                {{ $errors->first('payment') ?: $errors->first('amount_received') ?: $errors->first('payment_method') ?: $errors->first('descount') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-3 gap-2">
                            @foreach (\App\Models\Sale::PAYMENT_METHODS as $method)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $method }}" class="peer sr-only"
                                           @checked(old('payment_method', 'cash') === $method)>
                                    <span class="block rounded-lg border px-3 py-2 text-center text-sm font-medium capitalize
                                                 peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-800">
                                        {{ $method }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div>
                            <label for="descount" class="block text-sm font-medium text-gray-700">Discount (Ks)</label>
                            <input type="number" name="descount" id="descount" min="0" max="{{ $sale->sub_total }}" step="0.01"
                                   value="{{ old('descount', 0) }}" class="mt-1 block w-full rounded-lg border-gray-300">
                        </div>

                        <div id="cashFields">
                            <label for="amount_received" class="block text-sm font-medium text-gray-700">Amount received (Ks)</label>
                            <input type="number" name="amount_received" id="amount_received" min="0" step="0.01"
                                   value="{{ old('amount_received') }}" class="mt-1 block w-full rounded-lg border-gray-300 text-lg">
                        </div>

                        <div class="space-y-1 rounded-lg bg-gray-50 p-3 text-sm">
                            <div class="flex justify-between font-bold">
                                <span>To pay</span>
                                <span><span id="payTotal">{{ number_format($sale->sub_total, 2) }}</span> Ks</span>
                            </div>
                            <div class="flex justify-between" id="changeRow">
                                <span>Change</span>
                                <span><span id="payChange">0.00</span> Ks</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-3 font-semibold text-white hover:bg-green-700">
                            Confirm payment
                        </button>
                    </form>

                    <script>
                        (() => {
                            const form = document.getElementById('checkoutForm');
                            const subtotal = {{ (float) $sale->sub_total }};
                            const discount = document.getElementById('descount');
                            const received = document.getElementById('amount_received');
                            const cashFields = document.getElementById('cashFields');
                            const changeRow = document.getElementById('changeRow');
                            const fmt = n => n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                            function update() {
                                const isCash = form.payment_method.value === 'cash';
                                const total = Math.max(subtotal - (parseFloat(discount.value) || 0), 0);
                                const change = (parseFloat(received.value) || 0) - total;

                                cashFields.hidden = !isCash;
                                changeRow.hidden = !isCash;
                                document.getElementById('payTotal').textContent = fmt(total);
                                document.getElementById('payChange').textContent = change >= 0 ? fmt(change) : 'not enough';
                            }

                            form.addEventListener('input', update);
                            update();
                        })();
                    </script>
                @endif
            </div>
        </div>

    </div>

</x-app-layout>
