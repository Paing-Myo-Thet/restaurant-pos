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

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard',[DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('users', UserController::class)
    ->middleware('role:admin');

Route::resource('categories', CategoryController::class)
    ->middleware('role:admin');

Route::resource('products', ProductController::class)
    ->middleware('role:admin');

Route::resource('customers', CustomerController::class)
    ->middleware('role:admin');

Route::resource('tables', TableController::class)
    ->middleware(['auth', 'role:admin,waiter']);
    

Route::get('/sales/today', [SaleController::class, 'today'])
    ->middleware(['auth', 'role:admin,cashier,waiter'])
    ->name('sales.today');

Route::resource('sales', SaleController::class)
    ->middleware('role:admin,waiter');


Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])
    ->middleware(['auth', 'role:admin,cashier,waiter,kitchen'])
    ->name('sales.updateStatus');

Route::get('/stock-movements', [StockMovementController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('stock-movements.index');

Route::get('/stock-movements/create', [StockMovementController::class, 'create'])
    ->middleware(['auth', 'role:admin'])
    ->name('stock-movements.create');

Route::post('/stock-movements', [StockMovementController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('stock-movements.store');

Route::post('/stock-movements/out', [StockMovementController::class, 'out'])
    ->name('stock-movements.out');

Route::get('/stock-movements/out', [StockMovementController::class, 'createOut'])
    ->name('stock-movements.out.create');

Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])
    ->name('sales.status');

Route::patch('/sales/{sale}/payment', [SaleController::class, 'markAsPaid'])
    ->middleware(['auth', 'role:admin,cashier'])
    ->name('sales.markAsPaid');

Route::get('/reports', [ReportController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('reports.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
