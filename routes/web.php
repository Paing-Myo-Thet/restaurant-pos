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
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'active'])->group(function () {
    // Users without dashboard access are redirected to their own start page.
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')->name('dashboard');

    Route::resource('tables', TableController::class)
        ->except('show')
        ->middleware('can:tables.manage');

    Route::get('/kitchen', [KitchenController::class, 'index'])
        ->middleware('can:kitchen.manage')
        ->name('kitchen.index');

    // Registered before index/show so /sales/create and /sales/today
    // aren't captured by /sales/{sale}. Orders are cancelled, never deleted.
    Route::resource('sales', SaleController::class)
        ->only(['create', 'store', 'edit', 'update'])
        ->middleware('can:orders.manage');

    Route::get('/sales/today', [SaleController::class, 'today'])
        ->middleware('can:orders.manage')
        ->name('sales.today');

    // Who may print, and whether it's a draft, is decided in Sale::canBePrintedBy().
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    Route::resource('sales', SaleController::class)
        ->only(['index', 'show'])
        ->middleware('can:orders.view');

    // Which status changes a user may make is checked per transition in Sale::TRANSITIONS.
    Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])
        ->middleware('can:orders.view')
        ->name('sales.status');

    Route::post('/sales/{sale}/checkout', [SaleController::class, 'checkout'])
        ->middleware('can:orders.checkout')
        ->name('sales.checkout');

    Route::resource('users', UserController::class)->except('show')->middleware('can:users.manage');
    Route::resource('roles', RoleController::class)->except('show')->middleware('can:roles.manage');
    Route::resource('categories', CategoryController::class)->except('show')->middleware('can:menu.manage');
    Route::resource('products', ProductController::class)->except('show')->middleware('can:menu.manage');
    Route::resource('customers', CustomerController::class)->except('show')->middleware('can:customers.manage');

    Route::middleware('can:inventory.manage')->group(function () {
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
    });

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('can:reports.view')
        ->name('reports.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
