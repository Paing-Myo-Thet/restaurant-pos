<?php

use App\Models\Product;
use App\Models\Sale;
use App\Models\Table;
use App\Models\User;

function cancel(Sale $sale, User $user, ?string $reason = null)
{
    return test()->actingAs($user)->patch(route('sales.status', $sale), [
        'status' => 'cancelled',
        'cancel_reason' => $reason,
    ]);
}

beforeEach(function () {
    $this->waiter = User::factory()->role('waiter')->create();
    $this->kitchen = User::factory()->role('kitchen')->create();
    $this->cashier = User::factory()->role('cashier')->create();
    $this->admin = User::factory()->role('admin')->create();
    $this->table = Table::factory()->create();
    $this->curry = Product::factory()->create(['name' => 'Chicken Curry', 'price' => 5000, 'stock' => 10]);
});

function orderAt(string $status): Sale
{
    $sale = placeOrder(test()->waiter, [test()->curry->id => 2], test()->table);

    foreach (['sent' => test()->waiter, 'preparing' => test()->kitchen, 'ready' => test()->kitchen] as $next => $user) {
        if ($sale->fresh()->status === $status) {
            break;
        }
        moveTo($sale, $next, $user)->assertSessionHasNoErrors();
    }

    return $sale->fresh();
}

test('a pending order can be cancelled without a reason and goes back to stock', function () {
    $sale = orderAt('pending');

    cancel($sale, $this->waiter)->assertSessionHasNoErrors();

    $sale->refresh();
    expect($sale->status)->toBe('cancelled')
        ->and($sale->cancelled_by)->toBe($this->waiter->id)
        ->and($sale->cancelled_from)->toBe('pending')
        ->and($sale->cancelled_at)->not->toBeNull()
        ->and($sale->wasWrittenOff())->toBeFalse()
        ->and($this->curry->fresh()->stock)->toBe(10)
        ->and($this->table->fresh()->status)->toBe('available');
});

test('cancelling an order the kitchen already has needs a reason', function () {
    $sale = orderAt('sent');

    cancel($sale, $this->cashier)->assertSessionHasErrors('cancel_reason');
    expect($sale->fresh()->status)->toBe('sent');

    cancel($sale, $this->cashier, 'Customer left')->assertSessionHasNoErrors();

    $sale->refresh();
    expect($sale->status)->toBe('cancelled')
        ->and($sale->cancel_reason)->toBe('Customer left')
        ->and($this->curry->fresh()->stock)->toBe(10);
});

test('waiters cannot cancel once the order is in the kitchen', function () {
    cancel(orderAt('sent'), $this->waiter, 'Customer left')->assertSessionHasErrors('status');
});

test('food the kitchen has started is written off, not returned to stock', function () {
    $preparing = orderAt('preparing');

    cancel($preparing, $this->cashier, 'Customer left')->assertSessionHasErrors('status');
    cancel($preparing, $this->admin, 'Customer left')->assertSessionHasNoErrors();

    expect($preparing->fresh()->wasWrittenOff())->toBeTrue()
        ->and($this->curry->fresh()->stock)->toBe(8);
    $this->assertDatabaseMissing('stock_movements', ['sale_id' => $preparing->id, 'type' => 'in']);
});

test('a ready order can be voided by an admin but not a cashier', function () {
    $ready = orderAt('ready');

    cancel($ready, $this->cashier, 'Food quality complaint')->assertSessionHasErrors('status');
    cancel($ready, $this->admin, 'Food quality complaint')->assertSessionHasNoErrors();

    expect($ready->fresh()->status)->toBe('cancelled')
        ->and($ready->fresh()->cancelled_from)->toBe('ready')
        ->and($this->curry->fresh()->stock)->toBe(8);
});

test('served orders cannot be cancelled', function () {
    $sale = orderAt('ready');
    moveTo($sale, 'served', $this->waiter)->assertSessionHasNoErrors();

    cancel($sale, $this->admin, 'Customer complained')->assertSessionHasErrors('status');
});

test('nobody can print a bill for a cancelled order', function () {
    $sale = orderAt('sent');
    cancel($sale, $this->cashier, 'Customer left');

    foreach ([$this->cashier, $this->admin] as $user) {
        $this->actingAs($user)->get(route('sales.receipt', $sale))
            ->assertRedirect(route('sales.show', $sale));

        $this->actingAs($user)->get(route('sales.show', $sale))
            ->assertDontSee(route('sales.receipt', $sale))
            ->assertSee('Cancelled orders have no bill.')
            ->assertSee('Customer left');
    }
});

test('the kitchen is told to stop making a cancelled order', function () {
    $sent = orderAt('sent');
    $neverSent = orderAt('pending');
    cancel($sent, $this->cashier, 'Customer left');
    cancel($neverSent, $this->waiter);

    $this->actingAs($this->kitchen)->get(route('kitchen.index'))
        ->assertSee('stop making these')
        ->assertSee($sent->order_number.' · ', false)
        // Never reached the kitchen, so there's nothing to stop.
        ->assertDontSee($neverSent->order_number.' · ', false);

    $this->travel(31)->minutes();

    $this->actingAs($this->kitchen)->get(route('kitchen.index'))
        ->assertDontSee('stop making these');
});

test('reports show the value of food written off', function () {
    cancel(orderAt('sent'), $this->cashier, 'Customer left');
    cancel(orderAt('preparing'), $this->admin, 'Customer left');

    $this->actingAs($this->admin)->get(route('reports.index'))
        ->assertViewHas('cancelledOrders', 2)
        ->assertViewHas('writtenOff', 10000.0);
});
