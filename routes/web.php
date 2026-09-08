<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TableController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard',[DashboardController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard');

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


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
