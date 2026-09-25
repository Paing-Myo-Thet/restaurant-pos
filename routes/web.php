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

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('customers', CustomerController::class);
    });

    Route::resource('tables', TableController::class)
        ->middleware('role:admin,waiter');

    // Registered before index/show so /sales/create isn't captured by /sales/{sale}.
    Route::resource('sales', SaleController::class)
        ->except(['index', 'show'])
        ->middleware('role:admin,cashier,waiter');

    Route::resource('sales', SaleController::class)
        ->only(['index', 'show'])
        ->middleware('role:admin,cashier,waiter,kitchen');

    Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])
        ->middleware('role:admin,cashier,waiter,kitchen')
        ->name('sales.updateStatus');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
