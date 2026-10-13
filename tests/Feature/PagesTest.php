<?php

use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;

beforeEach(function () {
    $this->seed(DemoDataSeeder::class);
    $this->sale = Sale::where('status', 'sent')->firstOrFail();
});

function pagesFor(Sale $sale): array
{
    return [
        'dashboard' => route('dashboard'),
        'orders' => route('sales.index'),
        'orders filtered' => route('sales.index', ['filter' => 'all']),
        'today' => route('sales.today'),
        'new order' => route('sales.create'),
        'order' => route('sales.show', $sale),
        'edit order' => route('sales.edit', $sale),
        'receipt' => route('sales.receipt', $sale),
        'kitchen' => route('kitchen.index'),
        'tables' => route('tables.index'),
        'products' => route('products.index'),
        'categories' => route('categories.index'),
        'customers' => route('customers.index'),
        'staff' => route('users.index'),
        'staff edit' => route('users.edit', User::first()),
        'inventory' => route('stock-movements.index'),
        'stock in' => route('stock-movements.create'),
        'stock out' => route('stock-movements.out.create'),
        'reports daily' => route('reports.index'),
        'reports weekly' => route('reports.index', ['period' => 'weekly']),
        'reports monthly' => route('reports.index', ['period' => 'monthly']),
        'reports yearly' => route('reports.index', ['period' => 'yearly']),
    ];
}

$access = [
    'admin' => ['*'],
    'cashier' => ['dashboard', 'orders', 'orders filtered', 'today', 'new order', 'order', 'edit order', 'receipt'],
    'waiter' => ['dashboard', 'orders', 'orders filtered', 'today', 'new order', 'order', 'edit order', 'receipt', 'tables'],
    'kitchen' => ['orders', 'orders filtered', 'order', 'kitchen'],
];

foreach ($access as $role => $allowed) {
    test("{$role} can open exactly the pages for their role", function () use ($role, $allowed) {
        $user = User::factory()->role($role)->create();

        foreach (pagesFor($this->sale) as $page => $url) {
            $response = $this->actingAs($user)->get($url);

            if ($allowed === ['*'] || in_array($page, $allowed, true)) {
                expect($response->status())->toBe(200, "{$role} should open {$page}");
            } else {
                expect($response->status())->toBeIn([302, 403], "{$role} should not open {$page}");
            }
        }
    });
}

test('kitchen staff land on the kitchen screen', function () {
    $this->actingAs(User::factory()->role('kitchen')->create())
        ->get(route('dashboard'))
        ->assertRedirect(route('kitchen.index'));
});

test('the sidebar only links to pages the role can open', function () {
    $this->actingAs(User::factory()->role('waiter')->create())
        ->get(route('sales.index'))
        ->assertSee(route('tables.index'))
        ->assertDontSee(route('reports.index'))
        ->assertDontSee(route('users.index'))
        ->assertDontSee(route('kitchen.index'));
});

test('the kitchen screen lists orders sent to the kitchen', function () {
    $this->actingAs(User::factory()->role('kitchen')->create())
        ->get(route('kitchen.index'))
        ->assertSee('Table 3')
        ->assertSee('Chicken Curry')
        ->assertSee('No chilli in the curry')
        ->assertDontSee('Mohinga');
});

test('actions show a styled message and destructive buttons ask in a dialog', function () {
    $waiter = User::factory()->role('waiter')->create();

    $pending = Sale::where('status', 'pending')->first();

    $this->actingAs($waiter)
        ->from(route('sales.show', $pending))
        ->followingRedirects()
        ->patch(route('sales.status', $pending), ['status' => 'sent'])
        ->assertSee('data-alert', false)
        ->assertSee('marked as sent');

    // Orders already in the kitchen can be cancelled by cashiers, not waiters.
    $this->actingAs(User::factory()->role('cashier')->create())
        ->get(route('sales.show', $this->sale))
        ->assertSee('data-confirm-title="Cancel order '.$this->sale->order_number.'?"', false)
        ->assertSee('id="confirmDialog"', false);
});

test('the order page offers checkout to cashiers but not waiters', function () {
    $this->actingAs(User::factory()->role('cashier')->create())
        ->get(route('sales.show', $this->sale))
        ->assertSee('Take payment');

    $this->actingAs(User::factory()->role('waiter')->create())
        ->get(route('sales.show', $this->sale))
        ->assertDontSee('Take payment');
});
