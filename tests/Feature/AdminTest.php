<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Table;
use App\Models\User;
use App\Services\OrderService;

beforeEach(function () {
    $this->admin = User::factory()->role('admin')->create();
    $this->waiter = User::factory()->role('waiter')->create();
    $this->orders = app(OrderService::class);
});

test('deactivating a user keeps them and their orders, and blocks login', function () {
    $product = Product::factory()->create();
    $this->orders->create(['items' => [['product_id' => $product->id, 'quantity' => 1]]], $this->waiter);

    $this->actingAs($this->admin)->delete(route('users.destroy', $this->waiter))->assertRedirect();

    expect($this->waiter->fresh()->is_active)->toBeFalse()
        ->and(Sale::count())->toBe(1);

    auth()->logout();
    $this->post('/login', ['email' => $this->waiter->email, 'password' => 'password']);
    $this->assertGuest();
});

test('a deactivated user is signed out on their next request', function () {
    $this->actingAs($this->waiter);
    $this->waiter->update(['is_active' => false]);

    $this->get(route('sales.index'))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('admins cannot deactivate or demote themselves', function () {
    $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin));

    $this->actingAs($this->admin)->put(route('users.update', $this->admin), [
        'name' => 'Boss', 'email' => $this->admin->email, 'role_id' => $this->waiter->role_id, 'is_active' => 0,
    ]);

    $admin = $this->admin->fresh();
    expect($admin->is_active)->toBeTrue()
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->name)->toBe('Boss');
});

test('a sold product is deactivated instead of deleted', function () {
    $sold = Product::factory()->create();
    $unsold = Product::factory()->create();
    $this->orders->create(['items' => [['product_id' => $sold->id, 'quantity' => 1]]], $this->waiter);

    $this->actingAs($this->admin)->delete(route('products.destroy', $sold));
    $this->actingAs($this->admin)->delete(route('products.destroy', $unsold));

    expect($sold->fresh()?->status)->toBeFalse()
        ->and($unsold->fresh())->toBeNull();
});

test('categories with products, customers with orders and tables in use cannot be deleted', function () {
    $product = Product::factory()->create();
    $customer = Customer::factory()->create();
    $table = Table::factory()->create();
    $this->orders->create([
        'customer_id' => $customer->id,
        'table_id' => $table->id,
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ], $this->waiter);

    $this->actingAs($this->admin)->delete(route('categories.destroy', $product->category))->assertSessionHas('error');
    $this->actingAs($this->admin)->delete(route('customers.destroy', $customer))->assertSessionHas('error');
    $this->actingAs($this->admin)->delete(route('tables.destroy', $table))->assertSessionHas('error');

    expect(Category::find($product->category_id))->not->toBeNull()
        ->and($customer->fresh())->not->toBeNull()
        ->and($table->fresh())->not->toBeNull();
});

test('reports show sales, profit, top products and payment methods', function () {
    $cashier = User::factory()->role('cashier')->create();
    $curry = Product::factory()->create(['name' => 'Curry', 'price' => 6000, 'cost' => 2500]);
    $tea = Product::factory()->create(['name' => 'Tea', 'price' => 1500, 'cost' => 400]);

    $paid = $this->orders->create(['items' => [
        ['product_id' => $curry->id, 'quantity' => 2],
        ['product_id' => $tea->id, 'quantity' => 3],
    ]], $this->waiter);
    $this->orders->checkout($paid, ['payment_method' => 'card', 'descount' => 500]);

    $cancelled = $this->orders->create(['items' => [['product_id' => $curry->id, 'quantity' => 5]]], $this->waiter);
    $this->orders->changeStatus($cancelled, 'cancelled', $this->waiter);

    // A later cost change must not rewrite past profit.
    $curry->update(['cost' => 5000]);

    $response = $this->actingAs($this->admin)->get(route('reports.index'))->assertOk();

    // 2×6000 + 3×1500 − 500 discount = 16,000; cost 2×2500 + 3×400 = 6,200.
    expect($response->viewData('totalSales'))->toBe(16000.0)
        ->and($response->viewData('profit'))->toBe(9800.0)
        ->and($response->viewData('totalOrders'))->toBe(1)
        ->and($response->viewData('cancelledOrders'))->toBe(1)
        ->and($response->viewData('topProducts')->first()['name'])->toBe('Tea')
        ->and($response->viewData('paymentMethods')->get('card')['total'])->toBe(16000.0);
});

test('the weekly report covers the week of the chosen date', function () {
    $product = Product::factory()->create(['price' => 1000]);
    $sale = $this->orders->create(['items' => [['product_id' => $product->id, 'quantity' => 1]]], $this->waiter);
    $sale->forceFill(['created_at' => now()->subWeeks(2)])->save();

    $this->actingAs($this->admin)
        ->get(route('reports.index', ['period' => 'weekly']))
        ->assertViewHas('totalOrders', 0);

    $this->actingAs($this->admin)
        ->get(route('reports.index', ['period' => 'weekly', 'date' => now()->subWeeks(2)->toDateString()]))
        ->assertViewHas('totalOrders', 1);
});

test('the dashboard counts only today\'s orders', function () {
    $product = Product::factory()->create();
    $old = $this->orders->create(['items' => [['product_id' => $product->id, 'quantity' => 1]]], $this->waiter);
    $old->forceFill(['created_at' => now()->subDays(3)])->save();
    $this->orders->create(['items' => [['product_id' => $product->id, 'quantity' => 1]]], $this->waiter);

    $this->actingAs($this->admin)->get(route('dashboard'))
        ->assertViewHas('todayOrders', 1)
        ->assertViewHas('openOrders', 2);
});

test('the receipt shows the payment and change', function () {
    $product = Product::factory()->create(['name' => 'Mohinga', 'price' => 2500]);
    $sale = $this->orders->create(['items' => [['product_id' => $product->id, 'quantity' => 2]]], $this->waiter);
    $this->orders->checkout($sale, ['payment_method' => 'cash', 'amount_received' => 10000]);

    $this->actingAs(User::factory()->role('cashier')->create())->get(route('sales.receipt', $sale))
        ->assertOk()
        ->assertSee('RECEIPT')
        ->assertDontSee('DRAFT')
        ->assertSee('Mohinga')
        ->assertSee('5,000.00')
        ->assertSee('Change');
});

test('waiters can print the bill only once the order is served', function () {
    $kitchen = User::factory()->role('kitchen')->create();
    $product = Product::factory()->create(['price' => 2500]);
    $sale = $this->orders->create(['items' => [['product_id' => $product->id, 'quantity' => 1]]], $this->waiter);

    $this->actingAs($this->waiter)->get(route('sales.show', $sale))
        ->assertDontSee('Print draft bill')
        ->assertSee('Bill available once served');
    $this->actingAs($this->waiter)->get(route('sales.receipt', $sale))
        ->assertRedirect(route('sales.show', $sale))
        ->assertSessionHas('error');

    // Cashiers can still print before the food is served, e.g. for takeaway.
    $this->actingAs(User::factory()->role('cashier')->create())->get(route('sales.receipt', $sale))->assertOk();

    $this->orders->changeStatus($sale, 'sent', $this->waiter);
    $this->orders->changeStatus($sale, 'preparing', $kitchen);
    $this->orders->changeStatus($sale, 'ready', $kitchen);
    $this->orders->changeStatus($sale, 'served', $this->waiter);

    $this->actingAs($this->waiter)->get(route('sales.show', $sale))->assertSee('Print draft bill');
    $this->actingAs($this->waiter)->get(route('sales.receipt', $sale))->assertOk();
});

test('a waiter printout is watermarked as a draft, even after payment', function () {
    $kitchen = User::factory()->role('kitchen')->create();
    $product = Product::factory()->create(['price' => 2500]);
    $sale = $this->orders->create(['items' => [['product_id' => $product->id, 'quantity' => 1]]], $this->waiter);
    foreach ([['sent', $this->waiter], ['preparing', $kitchen], ['ready', $kitchen], ['served', $this->waiter]] as [$status, $by]) {
        $this->orders->changeStatus($sale, $status, $by);
    }

    $this->actingAs($this->waiter)->get(route('sales.receipt', $sale))
        ->assertSee('class="watermark"', false)
        ->assertSee('DRAFT — NOT A RECEIPT');

    $this->orders->checkout($sale, ['payment_method' => 'card']);

    $this->actingAs($this->waiter)->get(route('sales.receipt', $sale))
        ->assertSee('DRAFT — NOT A RECEIPT')
        ->assertDontSee('>RECEIPT<', false);

    $this->actingAs($this->waiter)->get(route('sales.show', $sale))
        ->assertSee('Print draft bill');
});
