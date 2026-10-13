{{--
    Order builder shared by create and edit.
    Expects: $categories (with products), $customers, $tables, $action, $method,
    $submitLabel, and optionally $sale and $cart (product id => item) when editing.
--}}

@php
    $sale ??= null;
    $cart = collect($cart ?? []);

    // Units the cart may hold: current stock plus what this order already holds.
    $products = $categories->flatMap->products->mapWithKeys(fn ($product) => [
        $product->id => [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'max' => $product->stock + ($cart[$product->id]['quantity'] ?? 0),
        ],
    ]);

    // Items already on the order whose product is no longer on sale can be kept or reduced.
    foreach ($cart as $id => $item) {
        if (! $products->has($id)) {
            $products[$id] = $item + ['max' => $item['quantity']];
        }
    }

    $initial = old('items')
        ? collect(old('items'))->mapWithKeys(fn ($row) => [(int) $row['product_id'] => (int) $row['quantity']])
        : $cart->map(fn ($item) => $item['quantity']);
@endphp

<form action="{{ $action }}" method="POST" id="orderForm">
    @csrf
    @method($method)

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

        {{-- LEFT: Menu --}}
        <div class="lg:col-span-3">
            <div class="rounded-lg bg-white p-5 shadow">

                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <input type="search" id="productSearch" placeholder="Search menu…"
                           class="w-full rounded-lg border-gray-300 sm:w-56">

                    <button type="button" data-category="all"
                            class="category-tab rounded-full bg-gray-900 px-4 py-1.5 text-sm font-medium text-white">
                        All
                    </button>
                    @foreach ($categories as $category)
                        <button type="button" data-category="{{ $category->id }}"
                                class="category-tab rounded-full bg-gray-100 px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                    @forelse ($categories as $category)
                        @foreach ($category->products as $product)
                            @php $max = $products[$product->id]['max']; @endphp
                            <button type="button"
                                    class="product-btn rounded-lg border p-3 text-left transition enabled:hover:border-blue-500 enabled:hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40"
                                    data-id="{{ $product->id }}"
                                    data-category="{{ $category->id }}"
                                    data-name="{{ Str::lower($product->name) }}"
                                    @disabled($max < 1)>
                                <div class="font-semibold text-gray-800">{{ $product->name }}</div>
                                <div class="mt-1 text-sm text-gray-600">{{ number_format($product->price, 0) }} Ks</div>
                                <div class="mt-1 text-xs {{ $product->stock <= \App\Models\Product::LOW_STOCK_THRESHOLD ? 'text-red-500' : 'text-gray-400' }}">
                                    {{ $product->stock > 0 ? $product->stock.' left' : 'Sold out' }}
                                </div>
                            </button>
                        @endforeach
                    @empty
                        <p class="col-span-full py-8 text-center text-gray-500">
                            No products available. Add products and stock first.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Order --}}
        <div class="lg:col-span-2">
            <div class="space-y-4 rounded-lg bg-white p-5 shadow lg:sticky lg:top-6">

                @if ($errors->any())
                    <x-alert type="error" title="Please fix the order" class="!mb-0">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </x-alert>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="table_id" class="block text-sm font-medium text-gray-700">Table</label>
                        <select name="table_id" id="table_id" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="">Takeaway</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}" @selected(old('table_id', $sale?->table_id) == $table->id)>
                                    {{ $table->name }}{{ $table->status !== 'available' ? ' ('.$table->status.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" id="customer_id" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="">Walk-in</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id', $sale?->customer_id) == $customer->id)>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-gray-500">
                                <th class="py-2">Item</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="orderItems"></tbody>
                    </table>
                    <p id="emptyCart" class="py-8 text-center text-sm text-gray-500">Tap a menu item to add it.</p>
                </div>

                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">Kitchen note</label>
                    <input type="text" name="note" id="note" maxlength="255" value="{{ old('note', $sale?->note) }}"
                           placeholder="e.g. no onions, extra spicy" class="mt-1 block w-full rounded-lg border-gray-300">
                </div>

                <div class="flex items-center justify-between border-t pt-4 text-lg">
                    <span class="font-semibold">Total</span>
                    <span class="font-bold text-blue-600"><span id="totalDisplay">0</span> Ks</span>
                </div>

                <div id="hiddenInputs"></div>

                <button type="submit" id="submitBtn"
                        class="w-full rounded-lg bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                    {{ $submitLabel }}
                </button>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
    (() => {
        const products = @json($products);
        const cart = new Map(Object.entries(@json($initial)).map(([id, qty]) => [String(id), qty]));

        const itemsBody = document.getElementById('orderItems');
        const hiddenInputs = document.getElementById('hiddenInputs');
        const fmt = n => n.toLocaleString();

        function setQuantity(id, qty) {
            const max = products[id]?.max ?? 0;
            qty = Math.min(qty, max);

            qty > 0 ? cart.set(id, qty) : cart.delete(id);
            render();
        }

        function button(label, onClick, disabled = false) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = label;
            btn.disabled = disabled;
            btn.className = 'h-7 w-7 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-40';
            btn.addEventListener('click', onClick);
            return btn;
        }

        function render() {
            itemsBody.replaceChildren();
            hiddenInputs.replaceChildren();
            let total = 0;
            let index = 0;

            cart.forEach((qty, id) => {
                const product = products[id];
                if (!product) return;

                const subtotal = product.price * qty;
                total += subtotal;

                const row = itemsBody.insertRow();
                row.className = 'border-b';

                const name = row.insertCell();
                name.className = 'py-2';
                name.textContent = product.name;

                const qtyCell = row.insertCell();
                qtyCell.className = 'py-2';
                const controls = document.createElement('div');
                controls.className = 'flex items-center justify-center gap-2';
                const count = document.createElement('span');
                count.className = 'w-6 text-center font-medium';
                count.textContent = qty;
                controls.append(
                    button('−', () => setQuantity(id, qty - 1)),
                    count,
                    button('+', () => setQuantity(id, qty + 1), qty >= product.max),
                );
                qtyCell.append(controls);

                const sub = row.insertCell();
                sub.className = 'py-2 text-right';
                sub.textContent = fmt(subtotal);

                for (const [field, value] of [['product_id', id], ['quantity', qty]]) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${index}][${field}]`;
                    input.value = value;
                    hiddenInputs.append(input);
                }
                index++;
            });

            document.getElementById('emptyCart').hidden = cart.size > 0;
            document.getElementById('totalDisplay').textContent = fmt(total);
            document.getElementById('submitBtn').disabled = cart.size === 0;
        }

        document.querySelectorAll('.product-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                setQuantity(id, (cart.get(id) ?? 0) + 1);
            });
        });

        // Category tabs and search
        let activeCategory = 'all';
        const search = document.getElementById('productSearch');

        function filterMenu() {
            const term = search.value.trim().toLowerCase();

            document.querySelectorAll('.product-btn').forEach(btn => {
                const inCategory = activeCategory === 'all' || btn.dataset.category === activeCategory;
                btn.hidden = !(inCategory && btn.dataset.name.includes(term));
            });
        }

        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                activeCategory = tab.dataset.category;
                document.querySelectorAll('.category-tab').forEach(t => {
                    const active = t === tab;
                    t.classList.toggle('bg-gray-900', active);
                    t.classList.toggle('text-white', active);
                    t.classList.toggle('bg-gray-100', !active);
                    t.classList.toggle('text-gray-700', !active);
                });
                filterMenu();
            });
        });

        search.addEventListener('input', filterMenu);

        render();
    })();
</script>
@endpush
