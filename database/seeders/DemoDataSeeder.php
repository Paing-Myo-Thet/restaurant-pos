<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

/**
 * Sample menu, tables, staff and orders for trying the POS locally.
 * Run with: php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    public function run(OrderService $orders): void
    {
        $menu = [
            'Rice & Noodles' => [
                ['Mohinga', 2500, 900],
                ['Shan Noodles', 3000, 1100],
                ['Fried Rice with Chicken', 4500, 1800],
                ['Nan Gyi Thoke', 3000, 1000],
            ],
            'Curries' => [
                ['Chicken Curry', 6000, 2500],
                ['Pork Curry', 6500, 2800],
                ['Fish Curry', 7000, 3200],
            ],
            'Salads' => [
                ['Tea Leaf Salad', 3500, 1200],
                ['Ginger Salad', 3000, 1000],
            ],
            'Drinks' => [
                ['Burmese Milk Tea', 1500, 400],
                ['Lime Juice', 2000, 500],
                ['Bottled Water', 500, 200],
            ],
        ];

        foreach ($menu as $categoryName => $products) {
            $category = Category::firstOrCreate(['name' => $categoryName], ['status' => true]);

            foreach ($products as [$name, $price, $cost]) {
                Product::firstOrCreate(['name' => $name], [
                    'category_id' => $category->id,
                    'price' => $price,
                    'cost' => $cost,
                    'stock' => 40,
                    'status' => true,
                ]);
            }
        }

        // A couple of items low on stock, so the alerts have something to show.
        Product::where('name', 'Fish Curry')->update(['stock' => 4]);
        Product::where('name', 'Lime Juice')->update(['stock' => 7]);

        foreach (range(1, 8) as $number) {
            Table::firstOrCreate(['name' => "Table {$number}"], ['capacity' => $number <= 4 ? 2 : 4]);
        }

        foreach (['waiter', 'cashier', 'kitchen'] as $role) {
            User::firstOrCreate(['email' => "{$role}@restaurant.com"], [
                'name' => 'Demo '.ucfirst($role),
                'password' => 'password',
                'role' => $role,
            ]);
        }

        Customer::firstOrCreate(['phone' => '09123456789'], ['name' => 'Ko Aung', 'email' => null]);

        $waiter = User::where('email', 'waiter@restaurant.com')->first();
        $item = fn (string $name, int $quantity) => [
            'product_id' => Product::where('name', $name)->value('id'),
            'quantity' => $quantity,
        ];

        $orders->create([
            'table_id' => Table::where('name', 'Table 1')->value('id'),
            'items' => [$item('Mohinga', 2), $item('Burmese Milk Tea', 2)],
        ], $waiter);

        $sent = $orders->create([
            'table_id' => Table::where('name', 'Table 3')->value('id'),
            'note' => 'No chilli in the curry',
            'items' => [$item('Chicken Curry', 1), $item('Tea Leaf Salad', 1), $item('Lime Juice', 2)],
        ], $waiter);
        $orders->changeStatus($sent, 'sent', $waiter);
    }
}
