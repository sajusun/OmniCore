<?php

use App\Modules\Product\Http\Controllers\Backend\AttributeController;
use App\Modules\Product\Http\Controllers\Backend\BrandController;
use App\Modules\Product\Http\Controllers\Backend\CategoryController;
use App\Modules\Product\Http\Controllers\Backend\InventoryController;
use App\Modules\Product\Http\Controllers\Backend\ProductController;
use App\Modules\Product\Http\Controllers\Backend\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product Web Admin Routes (Dashboard Web UI)
|--------------------------------------------------------------------------
| Prefix: /admin/products, /admin/categories, etc.
*/

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/', [BrandController::class, 'index'])->name('index');
    Route::post('/', [BrandController::class, 'store'])->name('store');
    Route::put('/{id}', [BrandController::class, 'update'])->name('update');
    Route::delete('/{id}', [BrandController::class, 'destroy'])->name('destroy');
});

Route::prefix('attributes')->name('attributes.')->group(function () {
    Route::get('/', [AttributeController::class, 'index'])->name('index');
    Route::post('/', [AttributeController::class, 'store'])->name('store');
    Route::put('/{id}', [AttributeController::class, 'update'])->name('update');
    Route::delete('/{id}', [AttributeController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/values', [AttributeController::class, 'storeValue'])->name('values.store');
    Route::delete('/values/{valueId}', [AttributeController::class, 'destroyValue'])->name('values.destroy');
});

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/low-stock', [InventoryController::class, 'index'])->name('index');
    Route::post('/update-stock', [InventoryController::class, 'updateStock'])->name('update-stock');
});

Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::post('/{id}/toggle-status', [ReviewController::class, 'toggleStatus'])->name('toggle-status');
    Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');
});
