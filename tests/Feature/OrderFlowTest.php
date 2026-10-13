<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\Table;
use App\Models\User;

function placeOrder(User $user, array $items, ?Table $table = null): Sale
{
    test()->actingAs($user)->post(route('sales.store'), [
        'table_id' => $table?->id,
        'items' => collect($items)->map(fn ($qty, $id) => ['product_id' => $id, 'quantity' => $qty])->values()->all(),
    ])->assertSessionHasNoErrors();

    return Sale::latest('id')->firstOrFail();
}

function moveTo(Sale $sale, string $status, User $user)
{
    return test()->actingAs($user)->patch(route('sales.status', $sale), ['status' => $status]);
}

beforeEach(function () {
    $this->waiter = User::factory()->role('waiter')->create();
    $this->kitchen = User::factory()->role('kitchen')->create();
    $this->cashier = User::factory()->role('cashier')->create();
    $this->table = Table::factory()->create();
    $this->burger = Product::factory()->create(['price' => 3000, 'cost' => 1200, 'stock' => 10]);
    $this->tea = Product::factory()->create(['price' => 1000, 'cost' => 300, 'stock' => 10]);
});

test('placing an order prices it from the database, takes stock and occupies the table', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 2, $this->tea->id => 3], $this->table);

    expect((float) $sale->total)->toBe(9000.0)
        ->and($sale->status)->toBe('pending')
        ->and($sale->payment_status)->toBe('unpaid')
        ->and($this->burger->fresh()->stock)->toBe(8)
        ->and($this->tea->fresh()->stock)->toBe(7)
        ->and($this->table->fresh()->status)->toBe('occupied')
        ->and((float) $sale->items->firstWhere('product_id', $this->burger->id)->unit_cost)->toBe(1200.0);

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $this->burger->id, 'sale_id' => $sale->id, 'type' => 'out', 'quantity' => 2,
    ]);
});

test('an order cannot take more than the available stock', function () {
    $this->actingAs($this->waiter)->post(route('sales.store'), [
        'items' => [['product_id' => $this->burger->id, 'quantity' => 11]],
    ])->assertSessionHasErrors('items');

    expect(Sale::count())->toBe(0)
        ->and($this->burger->fresh()->stock)->toBe(10);
});

test('inactive products cannot be ordered', function () {
    $this->burger->update(['status' => false]);

    $this->actingAs($this->waiter)->post(route('sales.store'), [
        'items' => [['product_id' => $this->burger->id, 'quantity' => 1]],
    ])->assertSessionHasErrors('items.0.product_id');
});

test('editing an order only moves the stock difference', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 2, $this->tea->id => 1]);

    $this->actingAs($this->waiter)->put(route('sales.update', $sale), [
        'items' => [['product_id' => $this->burger->id, 'quantity' => 3]],
    ])->assertSessionHasNoErrors();

    expect($this->burger->fresh()->stock)->toBe(7)
        ->and($this->tea->fresh()->stock)->toBe(10)
        ->and((float) $sale->fresh()->total)->toBe(9000.0)
        ->and($sale->items()->count())->toBe(1);

    // 2 out + 1 out at creation, then +1 burger out and 1 tea back in.
    $this->assertDatabaseCount('stock_movements', 4);
});

test('orders cannot be edited once the kitchen starts them', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 1]);
    moveTo($sale, 'sent', $this->waiter);
    moveTo($sale, 'preparing', $this->kitchen);

    $this->actingAs($this->waiter)->put(route('sales.update', $sale), [
        'items' => [['product_id' => $this->burger->id, 'quantity' => 5]],
    ])->assertSessionHasErrors('items');

    expect($this->burger->fresh()->stock)->toBe(9);
});

test('each role can only make its own status changes', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 1]);

    moveTo($sale, 'preparing', $this->kitchen)->assertSessionHasErrors('status');
    moveTo($sale, 'sent', $this->kitchen)->assertSessionHasErrors('status');
    moveTo($sale, 'sent', $this->waiter)->assertSessionHasNoErrors();
    expect($sale->fresh()->sent_at)->not->toBeNull();

    moveTo($sale, 'preparing', $this->waiter)->assertSessionHasErrors('status');
    moveTo($sale, 'preparing', $this->kitchen)->assertSessionHasNoErrors();
    moveTo($sale, 'ready', $this->kitchen)->assertSessionHasNoErrors();
    moveTo($sale, 'served', $this->kitchen)->assertSessionHasErrors('status');
    moveTo($sale, 'served', $this->waiter)->assertSessionHasNoErrors();

    expect($sale->fresh()->status)->toBe('served');
});

test('cancelling returns stock, keeps the items and frees the table', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 4], $this->table);

    moveTo($sale, 'cancelled', $this->waiter)->assertSessionHasNoErrors();

    expect($sale->fresh()->status)->toBe('cancelled')
        ->and($sale->items()->count())->toBe(1)
        ->and($this->burger->fresh()->stock)->toBe(10)
        ->and($this->table->fresh()->status)->toBe('available');
});

test('cash checkout applies the discount and calculates change', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 2]);

    $this->actingAs($this->cashier)->post(route('sales.checkout', $sale), [
        'payment_method' => 'cash',
        'descount' => 500,
        'amount_received' => 10000,
    ])->assertSessionHasNoErrors();

    $sale->refresh();

    expect($sale->payment_status)->toBe('paid')
        ->and((float) $sale->total)->toBe(5500.0)
        ->and((float) $sale->change_due)->toBe(4500.0)
        ->and($sale->paid_at)->not->toBeNull();
});

test('cash checkout rejects too little money', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 2]);

    $this->actingAs($this->cashier)->post(route('sales.checkout', $sale), [
        'payment_method' => 'cash',
        'amount_received' => 5000,
    ])->assertSessionHasErrors('amount_received');

    expect($sale->fresh()->payment_status)->toBe('unpaid');
});

test('waiters cannot take payment', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 1]);

    $this->actingAs($this->waiter)->post(route('sales.checkout', $sale), [
        'payment_method' => 'card',
    ])->assertForbidden();
});

test('the table frees up once the order is both paid and served', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 1], $this->table);

    $this->actingAs($this->cashier)->post(route('sales.checkout', $sale), ['payment_method' => 'card']);
    expect($this->table->fresh()->status)->toBe('occupied');

    moveTo($sale, 'sent', $this->waiter);
    moveTo($sale, 'preparing', $this->kitchen);
    moveTo($sale, 'ready', $this->kitchen);
    moveTo($sale, 'served', $this->waiter);

    expect($this->table->fresh()->status)->toBe('available');
});

test('a table stays occupied while it has another open order', function () {
    $first = placeOrder($this->waiter, [$this->burger->id => 1], $this->table);
    placeOrder($this->waiter, [$this->tea->id => 1], $this->table);

    moveTo($first, 'cancelled', $this->waiter);

    expect($this->table->fresh()->status)->toBe('occupied');
});

test('paid orders cannot be cancelled', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 1]);
    $this->actingAs($this->cashier)->post(route('sales.checkout', $sale), ['payment_method' => 'card']);

    moveTo($sale, 'cancelled', $this->cashier)->assertSessionHasErrors('status');

    expect($this->burger->fresh()->stock)->toBe(9);
});

test('orders cannot be deleted', function () {
    $sale = placeOrder($this->waiter, [$this->burger->id => 1]);

    $this->actingAs(User::factory()->role('admin')->create())
        ->delete("/sales/{$sale->id}")
        ->assertStatus(405);
});
