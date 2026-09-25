<x-app-layout>


    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Edit Order #{{ $sale->id }}
                </h1>

                <p class="text-sm text-gray-500">
                    Update order details and products
                </p>
            </div>

            <a href="{{ route('sales.show', $sale) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
                Back
            </a>

        </div>


        {{-- Validation Errors --}}
        @if ($errors->any())

        <div class="rounded-lg bg-red-100 px-4 py-3 text-sm text-red-700">

            <ul class="list-disc pl-5">

                @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

        @endif


        <form action="{{ route('sales.update', $sale) }}" method="POST">

            @csrf
            @method('PUT')


            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


                {{-- LEFT: Products --}}
                <div class="lg:col-span-2">

                    <div class="rounded-lg bg-white p-6 shadow">

                        <h2 class="mb-4 text-lg font-semibold text-gray-800">
                            Products
                        </h2>


                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3">

                            @forelse ($products as $product)

                            <button type="button" class="product-btn rounded-lg border p-4 text-left transition hover:border-blue-500 hover:bg-blue-50" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">

                                <div class="font-semibold text-gray-800">
                                    {{ $product->name }}
                                </div>

                                <div class="mt-1 text-sm text-gray-500">
                                    {{ number_format($product->price, 2) }}
                                </div>

                                <div class="mt-2 text-xs text-gray-400">
                                    Stock: {{ $product->stock }}
                                </div>

                            </button>

                            @empty

                            <p class="col-span-full py-8 text-center text-gray-500">
                                No products available.
                            </p>

                            @endforelse

                        </div>

                    </div>

                </div>


                {{-- RIGHT: Order --}}
                <div>

                    <div class="rounded-lg bg-white p-6 shadow">

                        <h2 class="mb-4 text-lg font-semibold text-gray-800">
                            Order Details
                        </h2>


                        {{-- Customer --}}
                        <div class="mb-4">

                            <label for="customer_id" class="block text-sm font-medium text-gray-700">
                                Customer
                            </label>

                            <select name="customer_id" id="customer_id" class="mt-1 block w-full rounded-lg border-gray-300">

                                <option value="">
                                    Walk-in Customer
                                </option>

                                @foreach ($customers as $customer)

                                <option value="{{ $customer->id }}" @selected($sale->customer_id == $customer->id)
                                    >
                                    {{ $customer->name }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Table --}}
                        <div class="mb-4">

                            <label for="table_id" class="block text-sm font-medium text-gray-700">
                                Table
                            </label>

                            <select name="table_id" id="table_id" class="mt-1 block w-full rounded-lg border-gray-300">

                                <option value="">
                                    Takeaway
                                </option>

                                @foreach ($tables as $table)

                                <option value="{{ $table->id }}" @selected($sale->table_id == $table->id)
                                    >
                                    {{ $table->name }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Items --}}
                        <div class="mt-6">

                            <h3 class="mb-3 font-semibold text-gray-800">
                                Order Items
                            </h3>

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm">

                                    <thead>

                                        <tr class="border-b text-left">

                                            <th class="py-2">
                                                Product
                                            </th>

                                            <th class="py-2">
                                                Qty
                                            </th>

                                            <th class="py-2">
                                                Subtotal
                                            </th>

                                            <th></th>

                                        </tr>

                                    </thead>


                                    <tbody id="orderItems">

                                        {{-- JavaScript will render items here --}}

                                    </tbody>

                                </table>

                            </div>

                        </div>


                        {{-- Discount --}}
                        <div class="mt-6">

                            <label for="descount" class="block text-sm font-medium text-gray-700">
                                Discount
                            </label>

                            <input type="number" name="descount" id="descount" value="{{ old('descount', $sale->descount) }}" min="0" step="0.01" class="mt-1 block w-full rounded-lg border-gray-300">

                        </div>


                        {{-- Payment --}}
                        <div class="mt-4">

                            <label for="payment_status" class="block text-sm font-medium text-gray-700">
                                Payment Status
                            </label>

                            <select name="payment_status" id="payment_status" class="mt-1 block w-full rounded-lg border-gray-300">

                                <option value="unpaid" @selected($sale->payment_status === 'unpaid')
                                    >
                                    Unpaid
                                </option>

                                <option value="paid" @selected($sale->payment_status === 'paid')
                                    >
                                    Paid
                                </option>

                            </select>

                        </div>


                        {{-- Totals --}}
                        <div class="mt-6 space-y-2 border-t pt-4">

                            <div class="flex justify-between">

                                <span class="text-gray-600">
                                    Subtotal
                                </span>

                                <span id="subtotalDisplay" class="font-semibold">
                                    0.00
                                </span>

                            </div>


                            <div class="flex justify-between text-lg">

                                <span class="font-semibold">
                                    Total
                                </span>

                                <span id="totalDisplay" class="font-bold text-blue-600">
                                    0.00
                                </span>

                            </div>

                        </div>


                        {{-- Submit --}}
                        <button type="submit" class="mt-6 w-full rounded-lg bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700">
                            Update Order
                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>
    <script>
        const cart = @json($cart);

        const orderItems = document.getElementById('orderItems');
        const discountInput = document.getElementById('descount');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const totalDisplay = document.getElementById('totalDisplay');

        function renderCart() {

            orderItems.innerHTML = '';

            let subtotal = 0;

            Object.values(cart).forEach(item => {

                const itemSubtotal = item.price * item.quantity;

                subtotal += itemSubtotal;

                const row = document.createElement('tr');

                row.className = 'border-b';

                row.innerHTML = `
                <td class="py-2">
                    <span class="item-name"></span>

                    <input
                        type="hidden"
                        name="items[${item.id}][product_id]"
                        value="${item.id}"
                    >
                </td>

                <td class="py-2">

                    <div class="flex items-center gap-2">

                        <button
                            type="button"
                            onclick="decreaseQuantity(${item.id})"
                            class="rounded bg-gray-200 px-2 py-1"
                        >
                            −
                        </button>

                        <span>
                            ${item.quantity}
                        </span>

                        <button
                            type="button"
                            onclick="increaseQuantity(${item.id})"
                            class="rounded bg-gray-200 px-2 py-1"
                        >
                            +
                        </button>

                    </div>

                    <input
                        type="hidden"
                        name="items[${item.id}][quantity]"
                        value="${item.quantity}"
                    >

                </td>

                <td class="py-2">
                    ${itemSubtotal.toFixed(2)}
                </td>

                <td class="py-2 text-right">

                    <button
                        type="button"
                        onclick="removeItem(${item.id})"
                        class="text-red-600 hover:text-red-800"
                    >
                        Remove
                    </button>

                </td>
            `;

                row.querySelector('.item-name').textContent = item.name;

                orderItems.appendChild(row);
            });

            const discount = parseFloat(discountInput.value) || 0;

            const total = Math.max(subtotal - discount, 0);

            subtotalDisplay.textContent = subtotal.toFixed(2);

            totalDisplay.textContent = total.toFixed(2);
        }


        function increaseQuantity(id) {

            if (cart[id]) {
                cart[id].quantity++;
            }

            renderCart();
        }


        function decreaseQuantity(id) {

            if (cart[id]) {

                cart[id].quantity--;

                if (cart[id].quantity <= 0) {
                    delete cart[id];
                }
            }

            renderCart();
        }


        function removeItem(id) {

            delete cart[id];

            renderCart();
        }


        document.querySelectorAll('.product-btn').forEach(button => {

            button.addEventListener('click', function() {

                const id = this.dataset.id;

                const name = this.dataset.name;

                const price = parseFloat(this.dataset.price);

                if (cart[id]) {

                    cart[id].quantity++;

                } else {

                    cart[id] = {
                        id: id
                        , name: name
                        , price: price
                        , quantity: 1
                    };

                }

                renderCart();
            });

        });


        discountInput.addEventListener('input', renderCart);


        renderCart();

    </script>


</x-app-layout>
