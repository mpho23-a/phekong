<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockUpdateRequestController;


Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::middleware('auth')->group(function () {

});

Route::middleware('auth')->group(function () {

    // Dashboard (Breeze default - leave as is)
    Route::get('/dashboard', function () {
        return redirect()->route('products.index');
    })->name('dashboard');

    // Everyone logged in can view products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Product CRUD - stock_admin only (name, description, price, threshold - NOT quantity)
    Route::middleware('role:stock_admin')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // stock admin proposes stock updates
        Route::post('/products/{product}/stock-request', [StockUpdateRequestController::class, 'store'])
            ->name('stock-requests.store');
    });

    // approval admin approves/rejects
    Route::middleware('role:approval_admin')->group(function () {
        Route::get('/stock-requests', [StockUpdateRequestController::class, 'index'])->name('stock-requests.index');
        Route::post('/stock-requests/{stockUpdateRequest}/approve', [StockUpdateRequestController::class, 'approve'])->name('stock-requests.approve');
        Route::post('/stock-requests/{stockUpdateRequest}/reject', [StockUpdateRequestController::class, 'reject'])->name('stock-requests.reject');
    });

    // Profile update
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';