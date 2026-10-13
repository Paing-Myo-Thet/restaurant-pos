<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\KitchenController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')->name('dashboard');

    Route::resource('tables', TableController::class)
        ->except('show')
        ->middleware('role:admin,waiter');

    Route::get('/kitchen', [KitchenController::class, 'index'])
        ->middleware('role:admin,kitchen')
        ->name('kitchen.index');

    // Registered before index/show so /sales/create and /sales/today
    // aren't captured by /sales/{sale}. Orders are cancelled, never deleted.
    Route::resource('sales', SaleController::class)
        ->only(['create', 'store', 'edit', 'update'])
        ->middleware('role:admin,cashier,waiter');

    Route::middleware('role:admin,cashier,waiter')->group(function () {
        Route::get('/sales/today', [SaleController::class, 'today'])->name('sales.today');
        Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    });

    Route::resource('sales', SaleController::class)
        ->only(['index', 'show'])
        ->middleware('role:admin,cashier,waiter,kitchen');

    // Which status changes each role may make is checked per transition in Sale::TRANSITIONS.
    Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])
        ->middleware('role:admin,cashier,waiter,kitchen')
        ->name('sales.status');

    Route::post('/sales/{sale}/checkout', [SaleController::class, 'checkout'])
        ->middleware('role:admin,cashier')
        ->name('sales.checkout');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');
        Route::resource('customers', CustomerController::class)->except('show');

        Route::get('/stock-movements', [StockMovementController::class, 'index'])
            ->name('stock-movements.index');
        Route::get('/stock-movements/create', [StockMovementController::class, 'create'])
            ->name('stock-movements.create');
        Route::post('/stock-movements', [StockMovementController::class, 'store'])
            ->name('stock-movements.store');
        Route::get('/stock-movements/out', [StockMovementController::class, 'createOut'])
            ->name('stock-movements.out.create');
        Route::post('/stock-movements/out', [StockMovementController::class, 'out'])
            ->name('stock-movements.out');

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/hello_controller', [HelloController::class, 'index']);
Route::get('/restaurant', [HelloController::class, 'restaurant']);
